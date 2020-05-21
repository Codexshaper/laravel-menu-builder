<?php

namespace CodexShaper\Menu;

use CodexShaper\Menu\Models\Menu;
use CodexShaper\Menu\Models\MenuItem;
use CodexShaper\Menu\Models\MenuSetting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class MenuBuilder
{
    public function routes()
    {
        require __DIR__.'/../routes/menu.php';
    }

    /**
     * @param int $menu_id
     *
     * @return array
     */
    public function getSettings($menu_id)
    {
        $settings = MenuSetting::where('menu_id', $menu_id)->first();
        $defaultSettings = MenuSetting::whereNull('menu_id')->first();

        $depth = (!empty($settings) && $settings->depth)
        ? $settings->depth
        : ((!empty($defaultSettings) && $defaultSettings->depth)
            ? $defaultSettings->depth : config('menu.depth'));

        $apply_child_as_parent = (!empty($settings) && $settings->apply_child_as_parent)
        ? $settings->apply_child_as_parent
        : ((!empty($defaultSettings) && $defaultSettings->apply_child_as_parent)
            ? $defaultSettings->apply_child_as_parent : config('menu.apply_child_as_parent'));

        $levels = (!empty($settings) && $settings->levels)
        ? $settings->levels
        : ((!empty($defaultSettings) && $defaultSettings->levels)
            ? $defaultSettings->levels : config('menu.levels'));

        return [
            'depth'                 => $depth,
            'apply_child_as_parent' => $apply_child_as_parent,
            'levels'                => $levels,
        ];
    }

    /**
     * Generate Menu for display.
     *
     * @param string $name
     *
     * @return \Illuminate\View\View|string
     */
    public function generateMenu($name)
    {
        if (is_numeric($name)) {
            $menuHtml = $this->getMenu($name);
        } elseif (is_string($name)) {
            if ($menu = Menu::where('slug', Str::slug($name))->first()) {
                $menuHtml = $this->getMenu($menu->id);
            }
        }

        return isset($menuHtml) ? $menuHtml : '';
    }

    /**
     * Generate Menu for display.
     *
     * @param int $menu_id
     *
     * @return \Illuminate\View\View
     */
    protected function getMenu($menu_id)
    {
        $menuItems = MenuItem::with('childrens')
            ->where('menu_id', $menu_id)
            ->whereNull('parent_id')
            ->orderBy('order', 'asc')
            ->get();
        $settings = self::getSettings($menu_id);

        return view('menu::menus.generate-menu', compact('menuItems', 'settings'))->render();
    }

    /**
     * Load assests.
     *
     * @param string $path
     *
     * @return \Illuminate\Http\Response
     */
    public function assets($path)
    {
        $file = base_path(trim(config('menu.resources_path'), '/').'/'.urldecode($path));

        if (File::exists($file)) {
            switch ($extension = pathinfo($file, PATHINFO_EXTENSION)) {
                case 'js':
                    $mimeType = 'text/javascript';
                    break;
                case 'css':
                    $mimeType = 'text/css';
                    break;
                default:
                    $mimeType = File::mimeType($file);
                    break;
            }

            $response = Response::make(File::get($file), 200);
            $response->header('Content-Type', $mimeType);
            $response->setSharedMaxAge(31536000);
            $response->setMaxAge(31536000);
            $response->setExpires(new \DateTime('+1 year'));

            return $response;
        }

        return response('', 404);
    }
}
