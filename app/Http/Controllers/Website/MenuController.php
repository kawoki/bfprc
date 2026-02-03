<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MenuController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $menuCategories = MenuCategory::with('menus')->get();

        return Inertia::render('Menus', [
            'menuCategories' => $menuCategories,
        ]);
    }
}
