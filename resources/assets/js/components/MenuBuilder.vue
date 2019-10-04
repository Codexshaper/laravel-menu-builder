<template>
     <div class="cx-menu-builder"> 
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <div class="create-btn">
                            <button  
                                v-on:click="showAddMenuItemForm"
                                class="btn btn-success" 
                                data-toggle="modal" 
                                data-target="#addMenuItemModal"><i class="material-icons">adds</i> Add Item</button>
                            <button  
                                v-on:click="showSettingsForm"
                                class="btn btn-info edit-info" 
                                data-toggle="modal" 
                                data-target="#settingsModal"><i class="material-icons">settings_appl</i> Settings</button>
                            <button
                                id="show_menu_design"  
                                class="btn btn-info edit-info"
                                :data-id="menu.id" 
                                :data-prefix="prefix" 
                                data-toggle="modal" 
                                data-target="#showMenuModel"><i class="material-icons">visibility</i> View Design</button>
                        </div>

                    </div>
                    <div class="col-md-12">
                        <div class="use-menu">
                             <p>To use a menu on your site just call <span class="menu-code"> menu('{{ menu.slug }}')</span> Or <span class="menu-code"> @menu('{{ menu.slug }}')</span></p>
                        </div>
                    </div>
                </div>
                <div class="dd" id="nestmenu">
                    <draggable-menu
                        v-if="renderComponent"
                        :prefix="prefix" 
                        :lists="lists"
                        :settings="settings"
                        :defaultSettings="defaultSettings" 
                        :isDestroyAble="isDestroyAble" 
                        :editMenuItem="editMenuItem" 
                        :deleteMenuItem="deleteMenuItem">
                    </draggable-menu>
                </div>
                <menu-item-modals 
                    :items="items"
                    :item="item" 
                    :menu="menu" 
                    :parents="parents"
                    :settings="settings"
                    :defaultSettings="defaultSettings"
                    :errors="errors" 
                    :update-menu-item="updateMenuItem" 
                    :add-menu-item="addMenuItem"
                    :add-menu-setting="addMenuSetting" />
            </div>
        </div>
    </div>
</template>

<script>
     import draggableMenu from './DraggableMenu'
     import menuItemModals from './MenuItemModals'
    export default {
        name: "menu-builder",
        components: {
            'draggable-menu':draggableMenu,
            'menu-item-modals':menuItemModals,
        },
        props: ['menu','prefix'],
        data() {
            return {
                lists: [],
                items: [],
                item: {
                    menu_id: this.menu.id,
                    id: '',
                    title: '',
                    url: '',
                    target: '_self',
                    parent_id: '',
                    custom_class: '',
                    apply_child_as_parent: 0
                },
                childrens: [],
                parents: [],
                settings: {},
                defaultSettings: {},
                successMsg: '',
                renderComponent: true,
                errors:{
                    title: ""
                },
                isDestroyAble: false
            };
        },
        created(){
            toastr.options.closeButton = true;
            this.fetchMenuItems();
        },
        methods: {
            fetchMenuItems: function(){
                this.renderComponent = false;
                let self = this;
                let url = this.prefix+'/menu/items/'+this.menu.id;

                axios({
                    method: 'get',
                    url: url,
                    responseType: 'json',
                }).then(res => {
                    if (res.data.success == true) {
                        self.lists = res.data.lists;
                        self.items = res.data.items;
                        self.settings = res.data.settings;
                        self.settings.menu_id = self.menu.id;
                        self.settings.apply_child_as_parent = parseInt(self.settings.apply_child_as_parent);
                        self.settings.levels = JSON.stringify(self.settings.levels, null, 4);
                        self.defaultSettings = res.data.default;
                        self.renderComponent = true;
                    }
                })
                .catch(err => console.log(err));
            },
            showSettingsForm: function(){},
            showAddMenuItemForm: function(){
                this.errors.title = "";
                this.resetForm();
            },
            addMenuItem: function(item){
                event.preventDefault();
                let url = this.prefix+'/menu/item';
                let self = this;

                axios({
                  method: 'post',
                  url: url,
                  data: item,
                  responseType: 'json',
                })
                .then(res => {
                    if( res.data.success == true ){
                        toastr.success('Created Successfully.', item.title);
                        self.errors.title = "";
                        self.resetForm();
                        self.fetchMenuItems();
                        self.isDestroyAble = true;
                        self.closeModal();
                    }else {
                        self.errors.title = res.data.errors.title[0];
                    }
                    
                })
                .catch(err => console.log(err));
            },
            editMenuItem(id) {
                this.errors.title = "";
                var self = this;
                let url = this.prefix+'/menu/'+this.menu.id+'/item/'+id;

                axios({
                    method: 'get',
                    url: url,
                    responseType: 'json',
                }).then(res => {
                    if( res.data.success == true ) {
                        self.item = res.data.item;
                        self.item.parent_id = (res.data.item.parent_id) ? res.data.item.parent_id : '';
                        self.item.apply_child_as_parent = parseInt(self.settings.apply_child_as_parent);
                        self.childrens = res.data.childrens;
                        self.parents = res.data.parents;
                    }
                })
                .catch(err => console.log(err));
            },
            updateMenuItem: function(item){
                event.preventDefault();
                var self = this;
                var url = this.prefix+'/menu/item';

                axios({
                    method: 'put',
                    url: url,
                    data: item,
                })
                .then(res => {
                    if( res.data.success == true ) {
                        self.errors.title = "";
                        self.fetchMenuItems();
                        self.isDestroyAble = true;
                        self.closeModal();
                        toastr.success('Updated Successfully.', item.title);
                    }else {
                        self.errors.title = res.data.errors.title[0];
                    }
                    
                })
                .catch(err => console.log(err));
            },
            deleteMenuItem: function(id){
                event.preventDefault();
                let self = this;

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this menu item',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, keep it'
                }).then((result) => {
                    if (result.value) {
                        let url = this.prefix+'/menu/item/'+id;
                        
                        axios({
                          method: 'delete',
                          url: url,
                        })
                        .then(res => {
                            if (res.data.success == true) {
                                self.fetchMenuItems();
                                self.isDestroyAble = true;
                                toastr.success('Menu Deleted Successfully.');
                            }
                        })
                        .catch(err => console.log(err));

                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire(
                            'Cancelled',
                            'Your imaginary file is safe :)',
                            'error'
                        )
                    }
                })
            },
            addMenuSetting: function(settings){
                settings.levels = JSON.parse(settings.levels);
                let url = this.prefix+'/menu/item/settings';
                let self = this;

                axios({
                  method: 'post',
                  url: url,
                  data: settings,
                  responseType: 'json',
                })
                .then(res => {
                    if(res.data.success == true) {
                        self.resetForm();
                        self.fetchMenuItems();
                        self.isDestroyAble = true;
                        toastr.success('Updated Successfully.', 'Settings');
                        self.closeModal();
                    }
                })
                .catch(err => console.log(err));
            },
            resetForm: function(){
                this.item.menu_id = this.menu.id;
                this.item.id = '';
                this.item.title = '';
                this.item.url = '';
                this.item.target = '_self';
                this.item.parent_id = '';
                this.item.icon = '';
                this.item.custom_class = '';
            },
            closeModal: function(){
                $('.modal').modal('hide');
                $('.modal-backdrop').remove();
            },
        }
    };
</script>
<style scoped>
    
</style>
