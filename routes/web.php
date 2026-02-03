<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\MenuController as CustomerMenuController;
use App\Http\Controllers\Customer\ReservationController as CustomerReservationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\Website\MenuController as WebsiteMenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PendingOrderController;
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
Route::get('/menus', WebsiteMenuController::class)->name('website.menus');

Route::get('/bookings/available-times', [BookingController::class, 'getAvailableTimes'])->name('bookings.available-times');

// Admin Routes
Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, '__invoke'])->name('dashboard');

    // Booking routes
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/upcoming', [BookingController::class, 'upcoming'])->name('bookings.upcoming');
    Route::get('/bookings/past', [BookingController::class, 'past'])->name('bookings.past');
    Route::put('/bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::put('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/payments', [BookingController::class, 'recordPayment'])->name('bookings.payments.store');

    // Menu Management
    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    Route::put('/menu/categories/{category}', [MenuController::class, 'updateCategory'])->name('menu.categories.update');
    Route::post('/menu/categories', [MenuController::class, 'storeCategory'])->name('menu.categories.store');
    Route::delete('/menu/categories/{category}', [MenuController::class, 'destroyCategory'])->name('menu.categories.destroy');
    Route::put('/menu/items/{menu}', [MenuController::class, 'updateMenuItem'])->name('menu.items.update');
    Route::post('/menu/items', [MenuController::class, 'storeMenuItem'])->name('menu.items.store');
    Route::delete('/menu/items/{menu}', [MenuController::class, 'destroyMenuItem'])->name('menu.items.destroy');

    // Order routes
    Route::get('/order', [OrderController::class, 'index'])->name('order.index');
    Route::post('/order', [OrderController::class, 'store'])->name('order.store');
    Route::put('/order/{sale}/complete', [OrderController::class, 'complete'])->name('order.complete');
    Route::put('/order/{sale}/cancel', [OrderController::class, 'cancel'])->name('order.cancel');

    // Pending Orders Routes
    Route::post('/pending-orders', [PendingOrderController::class, 'store'])->name('pending_orders.store');
    Route::put('/pending-orders/{pendingOrder}/finalize', [PendingOrderController::class, 'finalize'])->name('pending_orders.finalize');
    Route::delete('/pending-orders/{pendingOrder}', [PendingOrderController::class, 'destroy'])->name('pending_orders.destroy');
});

// Customer Routes
Route::middleware(['auth', 'verified', 'customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, '__invoke'])->name('dashboard');

    // Customer Menu viewing
    Route::get('/menu', [CustomerMenuController::class, 'index'])->name('menu');

    // Customer Reservations
    Route::get('/reservations', [CustomerReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/create', [CustomerReservationController::class, 'create'])->name('reservations.create');
    Route::post('/reservations', [CustomerReservationController::class, 'store'])->name('reservations.store');
    Route::put('/reservations/{booking}/cancel', [CustomerReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::get('/reservations/available-times', [CustomerReservationController::class, 'getAvailableTimes'])->name('reservations.available-times');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
