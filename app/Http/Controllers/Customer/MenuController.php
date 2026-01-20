<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use Inertia\Inertia;

class MenuController extends Controller
{
    public function index()
    {
        $categories = MenuCategory::with('menus')->get();

        return Inertia::render('Customer/Menu', [
            'categories' => $categories,
        ]);
    }
}
