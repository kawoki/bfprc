<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();

        return Inertia::render('Customer/Dashboard', [
            'stats' => [
                'totalReservations' => $user->bookings()->count(),
                'upcomingReservations' => $user->bookings()
                    ->whereHas('occupiedTable', function ($query) {
                        $query->where('date', '>=', now()->toDateString());
                    })
                    ->whereNull('cancelled_at')
                    ->count(),
                'confirmedReservations' => $user->bookings()
                    ->whereNotNull('confirmed_at')
                    ->whereNull('cancelled_at')
                    ->count(),
            ],
            'recentReservations' => $user->bookings()
                ->with(['items.menu', 'occupiedTable.table'])
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}
