<?php

namespace CodexShaper\Menu\Models;

use CodexShaper\Menu\Models\MenuItem;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    //
    protected $table = 'menus';

    public function items()
    {
        return $this->hasMany(MenuItem::class, 'menu_id');
    }
}
