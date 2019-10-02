<ul class="level_root {{ $settings['levels']['root']['style'] }}">

    @foreach ($menuItems as $menu)
        @include(
        	'menu::menus.recursive', 
        	[
        		'menu'=>$menu,
        		'settings'=>$settings,
        		'i' => 0
        	])
    @endforeach
</ul>