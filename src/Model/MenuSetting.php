<?php

namespace CodexShaper\Menu\Models;

use Illuminate\Database\Eloquent\Model;

class MenuSetting extends Model
{
    protected $table = 'menu_settings';
    protected $fillable = [
        'depth', 'levels',
    ];
    protected $casts = [
        'levels' => 'array',
    ];
}
