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

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        MenuCategory::create($validated);

        return redirect()->back()->with('success', 'Category created successfully.');
    }

    public function destroyCategory(MenuCategory $category)
    {
        // Prevent deletion if category has menu items
        if ($category->menus()->count() > 0) {
            return redirect()->back()->withErrors([
                'category' => 'Cannot delete category with existing menu items. Please delete all menu items first.'
            ]);
        }

        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully.');
    }

    public function storeMenuItem(Request $request)
    {
        $validated = $request->validate([
            'menu_category_id' => 'required|exists:menu_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
        ]);

        Menu::create($validated);

        return redirect()->back()->with('success', 'Menu item created successfully.');
    }

    public function destroyMenuItem(Menu $menu)
    {
        $menu->delete();

        return redirect()->back()->with('success', 'Menu item deleted successfully.');
    }
}
