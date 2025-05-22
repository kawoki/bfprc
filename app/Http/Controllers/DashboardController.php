<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Menu;
use App\Models\MenuCategory;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                'totalBookings' => Booking::count(),
                'activeBookings' => Booking::active()->count(),
                'totalMenuItems' => Menu::count(),
                'totalCategories' => MenuCategory::count(),
            ],
            'recentBookings' => Booking::latest()->take(5)->get(),
            'categories' => MenuCategory::withCount('menus')->get(),
        ]);
    }
}
