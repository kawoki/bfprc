<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Models\MenuCategory;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $menuCategories = MenuCategory::with('menus')->get();

    return Inertia::render('Welcome', [
        'menuCategories' => $menuCategories,
    ]);
})->name('home');

Route::get('/booking', [BookingController::class, 'create'])->name('booking.create');
Route::post('/booking', [BookingController::class, 'store'])->name('bookings.store');

Route::get('/bookings/available-times', [BookingController::class, 'getAvailableTimes'])->name('bookings.available-times');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, '__invoke'])->name('dashboard');

    // Booking routes
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/upcoming', [BookingController::class, 'upcoming'])->name('bookings.upcoming');
    Route::get('/bookings/past', [BookingController::class, 'past'])->name('bookings.past');
    Route::put('/bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::put('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    // Menu Management
    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    Route::put('/menu/categories/{category}', [MenuController::class, 'updateCategory'])->name('menu.categories.update');
    Route::put('/menu/items/{menu}', [MenuController::class, 'updateMenuItem'])->name('menu.items.update');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
