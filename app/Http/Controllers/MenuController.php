<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MenuController extends Controller
{
    public function index()
    {
        $categories = MenuCategory::with('menus')->get();

        return Inertia::render('Menu', [
            'categories' => $categories,
        ]);
    }

    public function updateCategory(Request $request, MenuCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $category->update($validated);

        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    public function updateMenuItem(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
        ]);

        $menu->update($validated);

        return redirect()->back()->with('success', 'Menu item updated successfully.');
    }
}
