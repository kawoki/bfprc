<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
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

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
