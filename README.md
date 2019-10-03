# Laravel Menu Builder
Laravel Menu Builder with VueJs and jQuery. Build your multi level menu within 5 mins.

![thumbnail.jpg](https://imgbbb.com/images/2019/10/03/thumbnail.jpg)

#Demo http://demo.codexshaper.com/admin/menus

#Installation: https://youtu.be/1-IHy1Xur-I

#Configuration: https://youtu.be/0Nvoc3hzCug

#Build Menu: https://youtu.be/iA8JVR9QV_0

#How to Use: https://youtu.be/_7rxHe_a1mI

#Install the Package

```
composer require codexshaper/laravel-menu-builder
```

#Optional:

    - If your mysql version is old then follow next steps
       * Goto `app\Providers\AppServiceProvider.php` and open it in your text editor
       * Add `use Illuminate\Support\Facades\Schema;` on top under namespace
       * Add `Schema::defaultStringLength(191);` in your boot method
       
#Publish Resource, Configs, Migration and Seeding Database in a single command

```
php artisan menu:install
```
#run `php artisan serve`

#for check menus go to `http://127.0.0.1:8000/admin/menus` . You can change `admin` prefix from `config/menu.php`

#How to use Menu in your site? You can choose any one from two uses

Use 1:
```
@extends('menu::layouts.app')
@section('content')
    {{ menu('name') }}
@endsection
```
Use 2:
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

# Credits
#Designed by @mahabubul1 Alam and concept inspired from voyager `https://github.com/the-control-group/voyager`
