<?php

namespace CodexShaper\Menu\Http\Controllers;

use CodexShaper\Menu\Facades\Menu as MenuBuilder;
use CodexShaper\Menu\Models\Menu;
use CodexShaper\Menu\Models\MenuItem;
use CodexShaper\Menu\Models\MenuSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MenuItemController extends Controller
{
    protected $order      = [];
    protected $childrens  = [];
    protected $parents    = [];
    protected $depth      = 0;
    protected $root       = null;
    protected $root_depth = 0;

    public function showMenuItems(Request $request)
    {
        $menu = Menu::find($request->id);
        return view('menu::menus.builder', compact('menu'));
    }

    /**
     * Retrive all items for the specified menu
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function getMenuItems(Request $request)
    {
        if ($request->ajax()) {

            if ($request->menu_id) {
                $itemsWithChildrens = $this->getChildrens($request->menu_id);
                $items              = MenuItem::where('menu_id', $request->menu_id)->orderBy('order', 'asc')->get();
                $parents            = $this->checkParents($request->menu_id, $items);
                $settings           = MenuSetting::where('menu_id', $request->menu_id)->first();
                $defaultSettings    = MenuSetting::whereNull('menu_id')->first();
                $menuHtml           = MenuBuilder::generateMenu($request->menu_id);

                if (empty($settings)) {
                    $settings = $defaultSettings;
                }

                return response()->json([
                    'success'  => true,
                    'lists'    => $itemsWithChildrens,
                    'items'    => $parents,
                    'settings' => $settings,
                    'default'  => $defaultSettings,
                    'menuHtml' => $menuHtml,
                ]);
            }
        }

        return response()->json(['success' => false]);
    }

    /**
     * Retrieve single menu item
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function getMenuItem(Request $request)
    {
        if ($request->ajax() && isset($request->id)) {
            $menu_id = $request->menu_id;

            if ($menuItem = MenuItem::find($request->id)) {
                $itemsWithChildrens = $this->getChildrens($menu_id, $request->id);
                $items              = MenuItem::where('menu_id', $menu_id)->get();
                $childrens          = $this->getSingleDimentionChildrens($itemsWithChildrens);
                $child_ids          = $this->getIds($childrens);
                $depth              = $this->getDepth($itemsWithChildrens);
                $parents            = $this->checkParentsWithChildrens($menu_id, $items, $menuItem, $depth, $child_ids);

                return response()->json([
                    'success'   => true,
                    'item'      => $menuItem,
                    'childrens' => $childrens,
                    'parents'   => $parents,
                ]);
            }
        }

        return response()->json([
            'success' => false,
        ]);
    }

    /**
     * Sort menu items
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function sort(Request $request)
    {
        if ($request->ajax()) {
            $items = $request->menus;

            foreach ($items as $item) {
                $menuItem  = MenuItem::find($item['id']);
                $parent_id = isset($item['parent_id']) ? $item['parent_id'] : null;

                if ($parent_id) {
                    $this->order[$parent_id] = isset($this->order[$parent_id]) ? $this->order[$parent_id] + 1 : 1;
                    $newOrder                = $this->order[$parent_id];
                }

                if (!$parent_id) {
                    $this->order['root'] = isset($this->order['root']) ? $this->order['root'] + 1 : 1;
                    $newOrder            = $this->order['root'];
                }

                if ($parent_id != $menuItem->id) {
                    $menuItem->parent_id = $parent_id;
                    $menuItem->order     = $newOrder;
                    $menuItem->update();
                }
            }

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }

    /**
     * Create new menu item
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->ajax()) {

            if (($errors = $this->validation($request->all())) !== true) {
                return $errors;
            }

            $parent_id = isset($request->parent_id) ? $request->parent_id : null;
            $parent_id = $this->checkParentDepth($request->menu_id, $parent_id);

            $order = ($parent_id)
            ? MenuItem::where('parent_id', $parent_id)->max('order')
            : MenuItem::whereNull('parent_id')->max('order');

            $menuItem               = new MenuItem;
            $menuItem->menu_id      = $request->menu_id;
            $menuItem->title        = $request->title;
            $menuItem->slug         = Str::slug($request->title);
            $menuItem->url          = $request->url;
            $menuItem->target       = $request->target;
            $menuItem->parent_id    = $parent_id;
            $menuItem->order        = $order + 1;
            $menuItem->icon         = $request->icon;
            $menuItem->custom_class = $request->custom_class;

            if ($menuItem->save()) {
                return response()->json(['success' => true]);
            }
        }

        return response()->json(['success' => false]);
    }

    /**
     * Update the specified menu item
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if ($request->ajax()) {

            if (($errors = $this->validation($request->all())) !== true) {
                return $errors;
            }

            if ($menuItem = MenuItem::find($request->id)) {
                $childrens = $this->getChildrens($request->menu_id, $request->id);

                // If Allow Child as a Parent
                if ($request->apply_child_as_parent) {

                    foreach ($childrens as $children) {
                        $children->parent_id = $menuItem->parent_id;
                        $children->update();
                    }

                    $parent_id = $request->parent_id;
                }

                if (!$request->apply_child_as_parent) {
                    $depth = $this->getDepth($childrens);

                    if (!$request->parent_id) {
                        $parent_id = null;
                    }

                    if ($request->parent_id) {
                        $parent_id = $this->checkParentDepth($request->menu_id, $request->parent_id, $depth);

                        if ($parent_id == null) {
                            $parent_id = $menuItem->parent_id;
                        }
                    }
                }

                $menuItem->title        = $request->title;
                $menuItem->slug         = Str::slug($request->title);
                $menuItem->url          = $request->url;
                $menuItem->target       = $request->target;
                $menuItem->parent_id    = $parent_id;
                $menuItem->icon         = $request->icon;
                $menuItem->custom_class = $request->custom_class;

                if ($menuItem->update()) {
                    return response()->json(['success' => true]);
                }
            }
        }

        return response()->json(['success' => false]);
    }

    /**
     * Delete the specified menu item
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($request->ajax()) {

            if ($menuItem = MenuItem::find($request->id)) {

                if ($childrens = $menuItem->childrens) {

                    foreach ($childrens as $children) {
                        $child            = MenuItem::find($children->id);
                        $child->parent_id = $menuItem->parent_id;
                        $child->save();
                    }
                }

                if ($menuItem->delete()) {
                    return response()->json(['success' => true]);
                }
            }
        }

        return response()->json(['success' => false]);
    }

    /**
     * Validation
     *
     * @param  object $data
     *
     * @return \Illuminate\Http\Response|true
     */
    public function validation($data)
    {
        $validator = Validator::make($data, [
            'menu_id' => 'required',
            'title'   => 'required|max:191',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ]);
        }

        return true;
    }

    /**
     * Check Root Depths alongs parent and childrens
     *
     * @param  int $menu_id
     * @param  array $items
     * @param  object $menuItem
     * @param  int $child_depth
     * @param  array $ids
     *
     * @return array
     */
    public function checkParentsWithChildrens($menu_id, $items, $menuItem, $child_depth = 0, $ids = [])
    {
        foreach ($items as $item) {

            if ($item->id != $menuItem->id) {
                $this->root_depth = 0;
                $depth            = $this->getRootDepth($item->id) + $child_depth;
                $settings         = MenuSetting::where('menu_id', $menu_id)->first();
                $defaultSettings  = MenuSetting::whereNull('menu_id')->first();

                if (empty($settings)) {
                    $settings = $defaultSettings;
                }

                if ($depth < $settings->depth) {

                    if ($settings->apply_child_as_parent) {
                        $this->parents[] = $item;
                    } elseif (!in_array($item->id, $ids)) {
                        $this->parents[] = $item;
                    }
                }
            }

        }

        return $this->parents;
    }

    /**
     * Check Root Parent
     *
     * @param  int $menu_id
     * @param  array $items
     *
     * @return array
     */
    public function checkParents($menu_id, $items)
    {
        foreach ($items as $item) {
            $this->root_depth = 0;
            $depth            = $this->getRootDepth($item->id);
            $settings         = MenuSetting::where('menu_id', $menu_id)->first();
            $defaultSettings  = MenuSetting::whereNull('menu_id')->first();

            if (empty($settings)) {
                $settings = $defaultSettings;
            }

            if ($depth < $settings->depth) {
                $this->parents[] = $item;
            }
        }

        return $this->parents;
    }

    /**
     * Check Parent Depth
     *
     * @param  int $menu_id
     * @param  int $parent_id
     * @param  int $child_depth
     *
     * @return int $parent_id|null
     */
    public function checkParentDepth($menu_id, $parent_id, $child_depth = 0)
    {
        if ($parent_id == null) {
            return null;
        }

        // Check root parent depth limit
        $depth           = $this->getRootDepth($parent_id) + $child_depth;
        $settings        = MenuSetting::where('menu_id', $menu_id)->first();
        $defaultSettings = MenuSetting::whereNull('menu_id')->first();

        if (empty($settings)) {
            $settings = $defaultSettings;
        }

        return ($depth < $settings->depth) ? $parent_id : null;
    }

    /**
     * Get Root Parent Id
     *
     * @param  int $id
     *
     * @return int
     */
    public function getRootParent($id)
    {
        if ($menu = MenuItem::find($id)) {
            $this->root = $menu->id;

            if ($menu->parent_id) {
                $this->getRootParent($menu->parent_id);
            }
        }

        return $this->root;
    }

    /**
     * Get root depth
     *
     * @param  int $id
     *
     * @return int
     */
    public function getRootDepth($id)
    {
        if ($menu = MenuItem::find($id)) {
            $this->root_depth++;

            if ($menu->parent_id) {
                $this->getRootDepth($menu->parent_id);
            }
        }

        return $this->root_depth;
    }

    /**
     * Get Children depth
     *
     * @param  array $childrens
     *
     * @return int
     */
    public function getDepth($childrens)
    {
        if (count($childrens) > 0) {
            $this->depth++;

            foreach ($childrens as $children) {

                if (count($children->childrens) > 0) {
                    $this->getDepth($children->childrens);
                }
            }
        }

        return $this->depth;
    }

    /**
     * Convert childrens multidimensional to single dimension
     *
     * @param  array $childrens
     *
     * @return array|false
     */
    public function getSingleDimentionChildrens($childrens)
    {
        if (empty($childrens)) {
            return false;
        }

        foreach ($childrens as $children) {
            $this->childrens[] = $children;

            if (!empty($children['childrens'])) {
                $this->getSingleDimentionChildrens($children['childrens']);
            }
        }

        return $this->childrens;
    }

    /**
     * Get all Childrens
     *
     * @param  int $menu_id
     * @param  int $parent_id
     * @param  string $orderBy
     *
     * @return array
     */
    public function getChildrens($menu_id, $parent_id = null, $orderBy = 'asc')
    {
        return MenuItem::with('childrens')
            ->where(['menu_id' => $menu_id, 'parent_id' => $parent_id])
            ->orderBy('order', $orderBy)
            ->get();
    }

    /**
     * Get Items id
     *
     * @param  array $items
     *
     * @return array
     */
    public function getIds($items)
    {
        $ids = [];

        foreach ($items as $item) {
            $ids[] = $item->id;
        }

        return $ids;
    }

    /**
     * Create or Update Settings
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function storeSettings(Request $request)
    {
        if ($request->ajax()) {

            if ($request->depth && $request->menu_id) {

                if ($menuSetting = MenuSetting::where('menu_id', $request->menu_id)->first()) {
                    $menuSetting->depth                 = $request->depth;
                    $menuSetting->levels                = $request->levels;
                    $menuSetting->apply_child_as_parent = $request->apply_child_as_parent;

                    if ($menuSetting->update()) {
                        return response()->json([
                            'success'  => true,
                            'settings' => $menuSetting,
                        ]);
                    }
                } else {
                    $menuSetting                        = new MenuSetting;
                    $menuSetting->menu_id               = $request->menu_id;
                    $menuSetting->depth                 = $request->depth;
                    $menuSetting->apply_child_as_parent = $request->apply_child_as_parent;
                    $menuSetting->levels                = $request->levels;

                    if ($menuSetting->save()) {

                        return response()->json([
                            'success'  => true,
                            'settings' => $menuSetting,
                        ]);
                    }
                }
            }
        }

        return response()->json(['success' => false]);
    }

    /**
     * Get Single Setting
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function getSettings(Request $request)
    {
        if ($request->ajax()) {

            if ($request->menu_id) {

                if ($menuSetting = MenuSetting::where('menu_id', $request->menu_id)->first()) {

                    return response()->json([
                        'success'  => true,
                        'settings' => $menuSetting,
                    ]);
                }
            }
        }

        return response()->json(['success' => false]);
    }
}
