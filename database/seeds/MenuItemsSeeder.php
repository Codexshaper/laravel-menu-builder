<?php

use CodexShaper\Menu\Models\Menu;
use CodexShaper\Menu\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MenuItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!$menu = Menu::where('slug', 'admin')->first()) {
            $menu = new Menu();
            $menu->name = 'Admin';
            $menu->slug = Str::slug('Admin');
            $menu->url = '/admin';
            $menu->order = 1;
            $menu->save();
        }
        if (!MenuItem::where('slug', Str::slug('Menu Builder'))->first()) {
            $menuItem = new MenuItem();
            $menuItem->menu_id = $menu->id;
            $menuItem->title = 'Menu Builder';
            $menuItem->slug = Str::slug('Menu Builder');
            $menuItem->url = '/admin/menus';
            $menuItem->parent_id = null;
            $menuItem->order = 1;
            $menuItem->route = null;
            $menuItem->params = null;
            $menuItem->middleware = null;
            $menuItem->controller = '\CodexShaper\Menu\Http\Controllers\MenuController@index';
            $menuItem->target = '_self';
            $menuItem->icon = null;
            $menuItem->custom_class = null;
            $menuItem->save();
        }
    }
}
