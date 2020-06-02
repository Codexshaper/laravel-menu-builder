<?php

use CodexShaper\Menu\Models\MenuItem;

Route::group([
    'prefix'    => config('menu.prefix'),
    'namespace' => config('menu.controller_namespace'),
], function () {
    Route::get('menus', 'MenuController@index');
    Route::get('menu/builder/{id}', 'MenuItemController@showMenuItems')->name('menu.builder');

    /*
     * Helpers Route
     */
    Route::get('assets', 'MenuController@assets')->name('menu.asset');

    /*
     * Vue Routes
     */
    // Menus
    Route::get('getMenus', 'MenuController@getMenus');
    Route::get('menu/{id}', 'MenuController@getMenu');
    Route::get('menu/html/{id}', 'MenuController@getMenuHtml');
    Route::post('menu', 'MenuController@store');
    Route::post('menu/sort', 'MenuController@sort');
    Route::put('menu', 'MenuController@update');
    Route::delete('menu/{id}', 'MenuController@destroy');
    // Menu Items
    Route::get('menu/items/{menu_id}', 'MenuItemController@getMenuItems');
    Route::get('menu/{menu_id}/item/{id}', 'MenuItemController@getMenuItem');
    Route::post('menu/item/sort', 'MenuItemController@sort');
    Route::post('menu/item', 'MenuItemController@store');
    Route::put('menu/item', 'MenuItemController@update');
    Route::delete('/menu/item/{id}', 'MenuItemController@destroy');
    // Menu Settings
    Route::post('menu/item/settings', 'MenuItemController@storeSettings');
    Route::get('menu/item/settings/{menu_id}', 'MenuItemController@getSettings');
});

$menuItems = MenuItem::all();

foreach ($menuItems as $menuItem) {
    if ($menuItem->url != null) {
        $controller = $menuItem->controller ?? '\CodexShaper\Menu\Http\Controllers\MenuItemController@setRoute';
        $partials = explode('@', $menuItem->controller);

        if (!class_exists($partials[0])) {
            $controller = '\CodexShaper\Menu\Http\Controllers\MenuItemController@setRoute';
        }

        if ($menuItem->route && !$menuItem->middleware) {
            Route::get($menuItem->url, $controller)->name($menuItem->route);
        } elseif ($menuItem->middleware && !$menuItem->route) {
            Route::get($menuItem->url, $controller)->middleware($menuItem->middleware);
        } elseif ($menuItem->route && $menuItem->middleware) {
            Route::get($menuItem->url, $controller)->name($menuItem->route)->middleware(explode(',', $menuItem->middleware));
        } elseif (!$menuItem->route && !$menuItem->middleware) {
            Route::get($menuItem->url, $controller);
        }
    }
}
