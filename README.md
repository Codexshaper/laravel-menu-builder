[![License](http://img.shields.io/:license-mit-blue.svg?style=flat-square)](http://badges.mit-license.org)
[![Downloads](https://poser.pugx.org/Codexshaper/laravel-menu-builder/d/total.svg)](https://packagist.org/packages/Codexshaper/laravel-menu-builder)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/Codexshaper/laravel-menu-builder.svg?style=flat-square)](https://packagist.org/packages/Codexshaper/laravel-menu-builder)

# Laravel Menu Builder
Laravel Menu Builder with VueJs and jQuery. Build your multi level menu within 5 minutes.

[![Laravel Menu Builder Demo](https://img.youtube.com/vi/5hr8b9DR_HU/0.jpg)](https://www.youtube.com/watch?v=5hr8b9DR_HU)

### Demo http://demo.codexshaper.com/admin/menus

### Install the Package

```
composer require codexshaper/laravel-menu-builder
```

### Optional:

    - If your mysql version is old then follow next steps
       * Goto `app\Providers\AppServiceProvider.php` and open it in your text editor
       * Add `use Illuminate\Support\Facades\Schema;` on top under namespace
       * Add `Schema::defaultStringLength(191);` in your boot method
       
#### Publish Resource, Configs, Migration and Seeding Database in a single command

```
php artisan menu:install
```
#### run `php artisan serve`

#### To check menus go to `http://127.0.0.1:8000/admin/menus` . You can change `admin` prefix from `config/menu.php`

#### How to use Menu in your site? You can choose any one from two options

Option 1:
```
@extends('menu::layouts.app')
@section('content')
    {{ menu('name') }}
@endsection
```
Option 2:
1. Call Menu anywhere on your site by calling `{{ menu('name') }}` or `@menu('name')`
2. Link CSS and JS if you want to use our default design 
```
CSS: <link rel="stylesheet" type="text/css" href="{{ menu_asset('css/menu.css') }}">
JS: <script src="{{ menu_asset('js/menu.js') }}"></script> 
```
3. Optional: If you don't have jQuery and bootstrap in your page then add `app.css` before `menu.css` and add `app.js` before `menu.js`
```
CSS: <link rel="stylesheet" type="text/css" href="{{ menu_asset('css/app.css') }}">
JS: <script src="{{ menu_asset('js/app.js') }}"></script>
```

## Authors

* **Md Abu Ahsan Basir** - *Main Developer* - [github](https://github.com/maab16)
* **Mahabubul Alam** - *Main designer* - [github](https://github.com/mahabubul1)

See also the list of [contributors](https://github.com/laravel-menu-builder/contributors) who participated in this project.

## License

[![License](http://img.shields.io/:license-mit-blue.svg?style=flat-square)](http://badges.mit-license.org)

- **[MIT license](http://opensource.org/licenses/mit-license.php)**
- Copyright 2019 © <a href="https://github.com/Codexshaper/laravel-menu-builder/blob/master/LICENSE" target="_blank">CodexShaper</a>.

## Thanks
* *Special Thanks to* <a href="https://github.com/the-control-group/voyager"> **Voyager** </a> *for awesome design concept*
