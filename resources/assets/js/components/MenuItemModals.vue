<template>
    <div class="menu-modals">
        <!-- Modal -->
        <div class="modal fade" id="addMenuItemModal" tabindex="-1" role="dialog" aria-labelledby="addMenuItemModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addMenuItemModalLabel">Add Menu Item</h5>
                    </div>
                    <form method="post" action="" v-on:submit.prevent="addMenuItem(item)">
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="text" name="title" class="form-control input-field mb-2" v-bind:class="{error:errors.title}" id="add_menu_item_title" v-model="item.title">
                                <label for="add_menu_item_title" class="cs-label">Title <span style="color: red">*</span></label>
                            </div>
                            <div class="form-group">
                                <input type="text" name="url" class="form-control input-field" id="add_menu_item_url" v-model="item.url">
                                <label for="add_menu_item_url" class="cs-label">URL</label>
                            </div>
                            <div class="form-group">
                                <input type="text" name="route" class="form-control input-field" id="add_menu_item_route" v-model="item.route">
                                <label for="add_menu_item_route" class="cs-label">Route</label>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-info edit-info btn-block d-flex" type="button">
                                    <span class="text-left mr-auto">Params</span> 
                                    <span 
                                        class="text-right ml-auto" 
                                        data-target="#addParams" 
                                        data-toggle="collapse" 
                                        aria-expanded="false" 
                                        aria-controls="addParams" 
                                        @click="showCollapse = !showCollapse ">
                                            {{ showCollapse ? 'hide' : 'open' }}
                                    </span>
                                  </button>
                                <div class="collapse" id="addParams">
                                  <div class="card card-body" style="padding-left: 0px; padding-right: 0px">
                                    <div class="param-field">
                                        <div v-for="(paramItem, index) in item.paramItems" :key="index" class="form-group row">
                                            <div class="col-md-5">
                                                <input type="text" class="form-control input-field" id="param_key" v-model="paramItem.key">
                                                <label for="param_key" class="cs-label">Key</label>
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" class="form-control input-field" id="param_value" v-model="paramItem.value">
                                                <label for="param_value" class="cs-label">Value</label>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger" @click="removeParam(index)"><i class="fa fa-trash"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pl-3">
                                        <button type="button" class="btn btn-success" value="Add Param" @click="addParam">Add Param</button>
                                    </div>
                                  </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="text" name="controller" class="form-control input-field" id="add_menu_item_controller" v-model="item.controller">
                                <label for="add_menu_item_controller" class="cs-label">Controller</label>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control input-field" id="add_menu_item_controller" v-model="item.middleware">
                                <label for="add_menu_item_controller" class="cs-label">Permission (Middleware)</label>
                            </div>
                            <div class="form-group">
                                <select name="target" id="add_menu_item_target" v-model="item.target" class="form-control input-field mb-2 ">
                                    <option value="_self">Same Tab</option>
                                    <option value="_blank">New Tab</option>
                                </select>
                                <label for="add_menu_item_target" class="cs-label">Open In</label>
                            </div>
                            <div class="form-group">
                                <select name="parent_id" id="add_parent_id" v-model="item.parent_id" class="form-control input-field parent">
                                    <option value="">Select parent</option>
                                    <option v-for="parent in items" :key="parent.id" :value="parent.id">{{ parent.title }}</option>
                                </select>
                                <label for="add_parent_id" class="cs-label">Parent</label>
                            </div>
                            <div class="form-group">
                                <input type="text" name="icon" class="form-control input-field mb-2" id="add_menu_item_icon" v-model="item.icon">
                                <label for="add_menu_item_custom_class" class="cs-label">Icon</label>
                            </div>
                            <div class="form-group">
                                <input type="text" name="custom_class" class="form-control input-field mb-2" id="add_menu_item_custom_class" v-model="item.custom_class">
                                <label for="add_menu_item_custom_class" class="cs-label">Custom Class</label>
                            </div>
                            <div class="m-footer pull-right">
                                <button type="button" class="btn btn-danger cs-danger" data-dismiss="modal">Close</button>
                                <button type="submit" id="add_menu_item_btn" class="btn btn-info edit-info">Add Item</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Edit Modal -->
        <div class="modal fade" id="editMenuItemModal" tabindex="-1" role="dialog" aria-labelledby="editMenuItemModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editMenuItemModalLabel">Edit Menu Item</h5>
                    </div>
                    <form method="post" action="" v-on:submit.prevent="updateMenuItem(item)">
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="text" name="title" class="form-control input-field mb-2" v-bind:class="{error:errors.title}" v-model="item.title" >
                                <label for="add_menu_item_title" class="cs-label">Title <span style="color: red">*</span></label>
                            </div>
                            <div class="form-group">
                                <input type="text" name="url" class="form-control input-field " v-model="item.url" />
                                <label for="url" class="cs-label">URL</label>
                            </div>
                            <div class="form-group">
                                <input type="text" name="route" class="form-control input-field" id="add_menu_item_route" v-model="item.route">
                                <label for="add_menu_item_route" class="cs-label">Route</label>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-info edit-info btn-block d-flex" type="button">
                                    <span class="text-left mr-auto">Params</span> 
                                    <span 
                                        class="text-right ml-auto" 
                                        data-target="#updateParams" 
                                        data-toggle="collapse" 
                                        aria-expanded="false" 
                                        aria-controls="updateParams" 
                                        @click="showCollapse = !showCollapse ">
                                            {{ showCollapse ? 'hide' : 'open' }}
                                    </span>
                                  </button>
                                <div class="collapse" id="updateParams">
                                  <div class="card card-body my-0 px-0">
                                    <div class="param-field">
                                        <div v-for="(paramItem, index) in item.paramItems" :key="index" class="form-group row">
                                            <div class="col-md-5">
                                                <input type="text" class="form-control input-field" id="param_key" v-model="paramItem.key">
                                                <label for="param_key" class="cs-label">Key</label>
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" class="form-control input-field" id="param_value" v-model="paramItem.value">
                                                <label for="param_value" class="cs-label">Value</label>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger" @click="removeParam(index)"><i class="fa fa-trash"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pl-3">
                                        <button type="button" class="btn btn-success" value="Add Param" @click="addParam">Add Param</button>
                                    </div>
                                  </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="text" name="controller" class="form-control input-field" id="add_menu_item_controller" v-model="item.controller">
                                <label for="add_menu_item_controller" class="cs-label">Controller</label>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control input-field" id="add_menu_item_controller" v-model="item.middleware">
                                <label for="add_menu_item_controller" class="cs-label">Permission (Middleware)</label>
                            </div>
                            <div class="form-group">
                                <select name="target" id="edit_menu_item_target" class="form-control input-field " v-model="item.target">
                                    <option  class="red" value="_self">Same Tab</option>
                                    <option class="red "value="_blank">New Tab</option>
                                </select>
                                <label for="edit_menu_item_target" class="cs-label">Open In</label>
                            </div>
                            <div class="form-group">
                                <div v-if="item.applyChildAsParent">
                                    <select name="parent_id" class="form-control input-field parent" v-model="item.parent_id">
                                        <option value="" selected>Select parent</option>
                                        <option v-if="( parent.id != item.id)" v-for="parent in items" :key="parent.id" :value="parent.id">{{ parent.title }}</option>
                                        
                                    </select>
                                </div>
                                <div v-else>
                                    <select name="parent_id" class="form-control input-field parent" v-model="item.parent_id">
                                        <option value="" selected>Select parent</option>
                                        <option v-if="( parent.id != item.id)" v-for="parent in parents" :key="parent.id" :value="parent.id">{{ parent.title }}</option>
                                        
                                    </select>
                                </div>
                                <label for="edit_parent_id" class="cs-label">Parent</label>
                            </div>
                            <div class="form-group">
                                <input type="text" name="icon" class="form-control input-field mb-2" id="add_menu_item_icon" v-model="item.icon">
                                <label for="add_menu_item_custom_class" class="cs-label">Icon</label>
                            </div>
                            <div class="form-group">
                                <input type="text" name="custom_class" class="form-control input-field mb-2" v-model="item.custom_class">
                                <label for="add_menu_item_custom_class" class="cs-label">Custom Class</label>
                            </div>
                            <div class="mo-footer pull-right">
                                <button type="button" class="btn btn-danger cs-danger" data-dismiss="modal">Close</button>
                             <button type="submit" id="edit_menu_item_btn" class="btn btn-info edit-info">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Settings Modal -->
        <div class="modal fade" id="settingsModal" tabindex="-1" role="dialog" aria-labelledby="settingModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="settingModalLabel">Settings</h5>
                       
                    </div>
                    <form method="post" action="" v-on:submit.prevent="addMenuSetting(settings)">
                        <input type="hidden" name="menu_id" class="form-control input-field mb-2" v-model="settings.menu_id" >
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="text" name="depth" class="form-control input-field mb-2" v-model="settings.depth" />
                                <label for="depth" class="cs-label">Depth</label>
                            </div>
                            <div class="form-group">
                                <textarea name="levels" class="form-control " style="resize: vertical;height: 100%; min-height: 120px" v-model="settings.levels"></textarea>
                                <label for="levels" class="cs-label">Levels</label>
                            </div>
                            <div class="form-group">
                                <input type="checkbox" name="apply_child_as_parent" id="apply_child_as_parent" v-model="settings.apply_child_as_parent">
                                <label for="apply_child_as_parent"> Apply Child As Parent</label>
                            </div>
                            <div class="mo-footer pull-right">
                                <button type="button" class="btn btn-danger cs-danger" data-dismiss="modal">Close</button>
                             <button type="submit" id="add_menu_setting" class="btn btn-info edit-info">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Show Menu -->
        <div class="modal fade" id="showMenuModel" tabindex="-1" role="dialog" aria-labelledby="editMenuModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editMenuItemModalLabel">Display {{ menu.name }} Menu</h5>
                    </div>
                    <div class="modal-body">
                        <div class="menu_items"></div>
                        <div class="m-footer">
                            <button type="button" class="btn btn-danger cs-danger float-right" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        props: {
            items: Array,
            item: Object,
            menu: Object,
            settings: Object,
            defaultSettings: Object,
            parents: Array,
            errors: Object,
            menuHTML: String,
            updateMenuItem: Function,
            addMenuItem: Function,
            addMenuSetting: Function,
        },
        name: 'menu-item-modals',
        data(){
            return {
                showCollapse : false
            }
        },
        methods: {
            addParam: function(){
                let paramItems = [...this.item.paramItems];
                Vue.delete(this.item, 'paramItems');
                paramItems.push({key:'',value: ''});
                this.item.paramItems = paramItems;
            },
            removeParam(index) {
                console.log(index);
                let paramItems = [...this.item.paramItems];
                paramItems.splice(index, 1);
                Vue.delete(this.item, 'paramItems');
                this.item.paramItems = paramItems;
            },
        }
    }
</script>

<style scoped="scoped">
    .modal-dialog {
        max-width: 700px;
    }
    .btn.btn-info {
        box-shadow: none;
    }
</style>
