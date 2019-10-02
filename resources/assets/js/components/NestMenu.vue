<template>
    <div class="nest-menu">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="create-btn col-md-12">
                       <button  
                           v-on:click="showAddMenuForm"
                           class="btn btn-success mat-raised-button" 
                           data-toggle="modal" 
                           data-target="#addMenuModal"><i class="material-icons">adds</i> Add Item</button>
                     </div>
                     <div class="col-md-12">
                        <div class="use-menu">
                             <p>To use a menu on your site just call <span class="menu-code">menu('name')</span> Or <span class="menu-code"> @menu('name')</span></p>
                        </div>
                     </div>
                    <div class="col-md-12 col-sm-12">
                        <div class="table-responsive">
                            <table class="table" id="menuTable" >
                                <thead>
                                    <tr>
                                        <th width="120">Id</th>
                                        <th> Name </th>
                                        <th class="action-head" >Action </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="menu in menus" :key="menu.id">
                                        <td>{{ menu.id }}</td>
                                        <td>{{ menu.name }}</td>
                                        <td class="action-buttons">
                                            <a :href="prefix+'/menu/builder/'+menu.id" class="btn  btn-build-menu" title="menu build"><i class="material-icons">menus</i>Builder</a>
                                            <button 
                                                class="btn btn-info edit-info" 
                                                title="edit menu"
                                                data-toggle="modal" 
                                                data-target="#editMenuModal" 
                                                v-on:click="showEditMenuForm(menu.id)" 
                                                :data-id="menu.id"> 
                                                <i class="material-icons">edit</i>
                                            </button>
                                            <button 
                                                class="btn btn-danger cs-danger" 
                                                title="delete menu"
                                                v-on:click="deleteMenu(menu.id)" 
                                                :data-id="menu.id"> 
                                                <i class="material-icons">delete</i>
                                            </button>
                                        </td>
                                    </tr> 
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modals -->
        <menu-modals :menu="menu" :errors="errors" :update-menu="updateMenu" :add-menu="addMenu" />
    </div>
</template>

<script>
    import menuModals from './MenuModals'
    export default {
        props: ['prefix'],
        components: {
            'menu-modals': menuModals
        },
        data(){
            return {
                menus: [],
                menu: {},
                errors: {
                    name: ""
                },
                successMsg: '',
                settings: {
                    depth: 1
                }
            };
        },
        methods: {
            fetchMenus: function(){
                let self = this;
                let url = this.prefix+'/getMenus';

                axios({
                    url: url,
                    method: 'GET',
                    responseType: 'json'
                })
                .then(res => {
                    self.destroyDataTable('#menuTable');
                    self.menus = res.data.menus;
                    self.initDataTable('#menuTable');
                })
                .catch(err => console.log(err));
            },
            showAddMenuForm: function(){
                this.errors.name = "";
                this.resetForm();
            },
            addMenu: function(menu) {
                console.log( menu );
                let self = this;
                let url = this.prefix+'/menu';
                axios({
                    url: url,
                    method: 'POST',
                    data: menu,
                    responseType: 'json'
                })
                .then(res => {
                    if( res.data.success == true ) {
                        self.errors.name = "";
                        self.fetchMenus();
                        self.resetForm();
                        self.closeModal();
                        toastr.success('Created Successfully.', menu.name);
                    }else {
                        self.errors.name = res.data.errors.name[0];
                    }
                    
                })
                .catch(err => console.log(err));
            },
            showEditMenuForm: function(id){
                this.errors.name = "";
                let self = this;
                let url = this.prefix+'/menu/'+id;
                axios({
                    url: url,
                    method: 'GET',
                    responseType: 'json'
                })
                .then(res => {
                    if(res.data.success == true) {
                        self.menu = res.data.menu;
                    }
                })
                .catch(err => console.log(err));
            },
            updateMenu: function(menu){
                let self = this;
                let url = this.prefix+'/menu';
                axios({
                    url: url,
                    method: 'PUT',
                    data: menu,
                    responseType: 'json'
                })
                .then(res => {
                    if(res.data.success == true) {
                        self.errors.name = "";
                        self.fetchMenus();
                        self.resetForm();
                        self.closeModal();
                        toastr.success('Updated Successfully.', menu.name);
                    }else if(res.data.success == false) {
                        self.errors.name = res.data.errors.name[0];
                    }
                })
                .catch(err => console.log(err));
            },
            deleteMenu: function(id){
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
                        let url = this.prefix+'/menu/'+id;
                        axios({
                            url: url,
                            method: 'DELETE',
                            responseType: 'json'
                        })
                        .then(res => {
                            self.fetchMenus();
                            toastr.success('Menu Deleted Successfully.');

                        })
                        .catch(err => console.log(err));

                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire(
                            'Cancelled',
                            'Your imaginary file is safe :)',
                            'error'
                        )
                    }
                });
            },
            resetForm: function(){
                this.menu = {};
            },
            closeModal: function(){
                $('.modal').modal('hide');
                $('.modal-backdrop').remove();
            },
            initDataTable(selector, options={}){
                setTimeout(function(){
                    $(selector).DataTable().draw();
                },300);
            },
            destroyDataTable(selector){
                $(selector).DataTable().destroy();
            },
        },
        mounted() {
            toastr.options.closeButton = true;
            this.fetchMenus();
        },
    }
</script>
