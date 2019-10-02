<?php

namespace CodexShaper\Menu\Http\Controllers;

use CodexShaper\Menu\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::all();
        return view('menu::menus.index', compact('menus'));
    }

    /**
     * Return all menu list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getMenus()
    {
        if ($menus = Menu::orderBy('order', 'asc')->get()) {
            return response()->json([
                'success' => true,
                'menus'   => $menus,
            ]);
        }
    }

    /**
     * Return single menu.
     *
     * @return \Illuminate\Http\Response
     */
    public function getMenu(Request $request)
    {
        if ($request->id) {
            $menu = Menu::find($request->id);
            return response()->json([
                'success' => true,
                'menu'    => $menu,
            ]);
        }
        return response()->json(['success' => false]);
    }

    public function getMenuHtml(Request $request)
    {
        if ($request->ajax()) {
            $html = \MenuBuilder::generateMenu($request->id);
            return response()->json([
                'success' => true,
                'html'    => $html,
            ]);
        }
    }

    /**
     * Create new menu.
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

            $order              = Menu::max('order');
            $menu               = new Menu;
            $menu->name         = $request->name;
            $menu->slug         = Str::slug($request->name);
            $menu->url          = $request->url;
            $menu->order        = $order + 1;
            $menu->custom_class = $request->custom_class;

            if ($menu->save()) {
                $menus = Menu::all();
                return response()->json([
                    'success' => true,
                ]);
            }
        }
        return response()->json([
            'success' => false,
            'errors'  => ["There is no ajax call"],
        ]);
    }

    /**
     * Sort menu list.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function sort(Request $request)
    {
        if (isset($request->menus)) {
            $menus = $request->menus;
            $order = 1;

            foreach ($menus as $item) {
                $menu        = Menu::find($item['id']);
                $menu->order = $order;

                if ($menu->update()) {
                    $order++;
                }
            }

            return response()->json([
                'success' => true,
            ]);
        }
        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Update the specified menu.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if (($errors = $this->validation($request->all())) !== true) {
            return $errors;
        }

        if ($request->id && $menu = Menu::find($request->id)) {
            $menu->name         = $request->name;
            $menu->slug         = Str::slug($request->name);
            $menu->url          = $request->url;
            $menu->custom_class = $request->custom_class;

            if ($menu->update()) {
                return response()->json([
                    'success' => true,
                    'menu'    => $menu,
                    'request' => $request->all(),
                ]);
            }
        }
        return response()->json(['success' => false]);
    }

    /**
     * Delete the specified menu
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($request->id) {
            $menu = Menu::find($request->id);
            $menu->items()->delete();

            if ($menu->delete()) {
                return response()->json([
                    'success' => true,
                ]);
            }
        }
        return response()->json(['success' => true]);
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
            'name' => 'required|max:191',
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
     * Load menu builder assests
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function assets(Request $request)
    {
        return \MenuBuilder::assets($request->path);
    }
}
