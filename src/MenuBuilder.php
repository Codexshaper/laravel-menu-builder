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
        require __DIR__ . '/../routes/menu.php';
    }

    /**
     *
     * @param  int $menu_id
     *
     * @return array
     */
    public function getSettings($menu_id)
    {
        $settings        = MenuSetting::where('menu_id', $menu_id)->first();
        $defaultSettings = MenuSetting::whereNull('menu_id')->first();

        $depth = (!empty($settings) && $settings->depth)
        ? $settings->depth
        : ((!empty($defaultSettings) && $defaultSettings->depth)
            ? $defaultSettings->depth : config('menu.depth'));

        $applyChildAsParent = (!empty($settings) && $settings->applyChildAsParent)
        ? $settings->applyChildAsParent
        : ((!empty($defaultSettings) && $defaultSettings->applyChildAsParent)
            ? $defaultSettings->applyChildAsParent : config('menu.apply_child_as_parent'));

        $levels = (!empty($settings) && $settings->levels)
        ? $settings->levels
        : ((!empty($defaultSettings) && $defaultSettings->levels)
            ? $defaultSettings->levels : config('menu.levels'));

        return [
            'depth'              => $depth,
            'applyChildAsParent' => $applyChildAsParent,
            'levels'             => $levels,
        ];
    }

    /**
     * Generate Menu for display
     *
     * @param  string $request
     *
     * @return \Illuminate\View\View
     */
    public function generateMenu($name)
    {
        if ($menu = Menu::where('slug', Str::slug($name))->first()) {
            $menuItems = MenuItem::with('childrens')
                ->where('menu_id', $menu->id)
                ->whereNull('parent_id')
                ->orderBy('order', 'asc')
                ->get();
            $settings = self::getSettings($menu->id);

            return view('menu::menus.generate-menu', compact('menuItems', 'settings'))->render();
        }

        return "";

    }

    /**
     * Load assests
     *
     * @param  string $path
     *
     * @return \Illuminate\Http\Response
     */
    public function assets($path)
    {
        $file = base_path(trim(config('menu.resources_path'), '/') . "/" . urldecode($path));

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
