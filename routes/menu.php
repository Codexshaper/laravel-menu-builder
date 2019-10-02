<?php

Route::group(['namespace' => config('menu.controller_namespace')], function () {
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
