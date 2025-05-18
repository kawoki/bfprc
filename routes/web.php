<?php

use App\Models\MenuCategory;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $menuCategories = MenuCategory::with('menus')->get();

    return Inertia::render('Welcome', [
        'menuCategories' => $menuCategories,
    ]);
})->name('home');

Route::get('/booking', function () {
    $menuCategories = MenuCategory::with('menus')->get();

    return Inertia::render('Booking', [
        'menuCategories' => $menuCategories,
    ]);
})->name('booking');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
