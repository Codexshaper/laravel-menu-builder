<?php
return [
    'prefix'                => '/admin',
    'namespace'             => '\CodexShaper\Menu',
    'controller_namespace'  => '\CodexShaper\Menu\Http\Controllers',
    'resources_path'        => 'vendor/codexshaper/laravel-menu-builder/publishable/assets/',
    'views'                 => 'vendor/codexshaper/laravel-menu-builder/publishable/views',
    // Menu Settings
    'depth'                 => 5,
    'apply_child_as_parent' => false,
    'levels'                => [
        'root'  => [
            'style' => 'vertical', // horizontal | vertical
        ],
        'child' => [
            'show'    => 'onClick', // onclick | onHover
            'level_1' => [
                'show'     => 'onClick',
                'position' => 'bottom',
            ],
            'level_2' => [
                'show'     => 'onHover',
                'position' => 'right',
            ],
        ],
    ],
];
