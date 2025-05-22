<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\MenuCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function create()
    {
        $menuCategories = MenuCategory::with('menus')->get();

        return Inertia::render('Booking', [
            'menuCategories' => $menuCategories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'seats' => 'required|in:2,4,6,8',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|date_format:H:i',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'address' => 'required|string',
            'phone_number' => 'required|string|max:20',
            'selected_menus' => 'array',
            'selected_menus.*.menu_id' => 'required|exists:menus,id',
            'selected_menus.*.quantity' => 'required|integer|min:1',
        ]);

        $validated['booking_date'] = Carbon::parse($validated['booking_date'])->format('Y-m-d');
        $validated['booking_time'] = Carbon::parse($validated['booking_time'])->format('H:i');

        $booking = Booking::create($validated);

        // Attach selected menus with quantities
        if (isset($validated['selected_menus'])) {
            foreach ($validated['selected_menus'] as $menu) {
                $booking->menus()->attach($menu['menu_id'], ['quantity' => $menu['quantity']]);
            }
        }

        return redirect()->back()->with('success', 'Booking created successfully!');
    }

    public function getAvailableTimes(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        $bookedTimes = Booking::getBookedTimesForDate($request->date);

        return response()->json([
            'bookedTimes' => $bookedTimes,
        ]);
    }
}
