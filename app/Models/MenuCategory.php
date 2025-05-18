<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuCategory extends Model
{
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }
}
