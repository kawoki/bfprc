<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\MenuCategory;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function create()
    {
        $menuCategories = MenuCategory::with('menus')->get();
        $tables = Table::all();

        return Inertia::render('Booking', [
            'menuCategories' => $menuCategories,
            'tables' => $tables,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_id' => 'required|exists:tables,id',
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

        $validated['date'] = Carbon::parse($validated['booking_date'])->format('Y-m-d');
        $validated['time'] = Carbon::parse($validated['booking_time'])->format('H:i');

        $booking = Booking::create([
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'address' => $validated['address'],
            'phone_number' => $validated['phone_number'],
        ]);

        // Create occupied table record
        $booking->occupiedTable()->create([
            'table_id' => $validated['table_id'],
            'date' => $validated['date'],
            'time' => $validated['time'],
        ]);

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

    public function index()
    {
        $today = Carbon::now()->startOfDay();
        $bookings = Booking::with(['menus', 'occupiedTable.table'])
            ->join('occupied_tables', function ($join) use ($today) {
                $join->on('bookings.id', '=', 'occupied_tables.occupiable_id')
                    ->where('occupied_tables.occupiable_type', Booking::class)
                    ->where('occupied_tables.date', $today->format('Y-m-d'));
            })
            ->orderBy('occupied_tables.time')
            ->select('bookings.*')
            ->get();

        return Inertia::render('Bookings/Index', [
            'bookings' => $bookings,
            'title' => "Today's Bookings",
        ]);
    }

    public function upcoming()
    {
        $today = Carbon::now()->startOfDay();
        $bookings = Booking::with(['menus', 'occupiedTable.table'])
            ->join('occupied_tables', function ($join) use ($today) {
                $join->on('bookings.id', '=', 'occupied_tables.occupiable_id')
                    ->where('occupied_tables.occupiable_type', Booking::class)
                    ->where('occupied_tables.date', '>', $today->format('Y-m-d'));
            })
            ->orderBy('occupied_tables.date')
            ->orderBy('occupied_tables.time')
            ->select('bookings.*')
            ->get();

        return Inertia::render('Bookings/Index', [
            'bookings' => $bookings,
            'title' => 'Upcoming Bookings',
        ]);
    }

    public function past()
    {
        $today = Carbon::now()->startOfDay();
        $bookings = Booking::with(['menus', 'occupiedTable.table'])
            ->join('occupied_tables', function ($join) use ($today) {
                $join->on('bookings.id', '=', 'occupied_tables.occupiable_id')
                    ->where('occupied_tables.occupiable_type', Booking::class)
                    ->where('occupied_tables.date', '<', $today->format('Y-m-d'));
            })
            ->orderByDesc('occupied_tables.date')
            ->orderByDesc('occupied_tables.time')
            ->select('bookings.*')
            ->get();

        return Inertia::render('Bookings/Index', [
            'bookings' => $bookings,
            'title' => 'Past Bookings',
        ]);
    }

    public function confirm(Booking $booking)
    {
        if ($booking->cancelled_at) {
            return back()->with('error', 'Cannot confirm a cancelled booking.');
        }

        if ($booking->confirmed_at) {
            return back()->with('error', 'Booking is already confirmed.');
        }

        $booking->update([
            'confirmed_at' => now(),
        ]);

        return back()->with('success', 'Booking confirmed successfully.');
    }

    public function cancel(Booking $booking)
    {
        if ($booking->cancelled_at) {
            return back()->with('error', 'Booking is already cancelled.');
        }

        $booking->update([
            'cancelled_at' => now(),
        ]);

        // Remove occupied table record
        $booking->occupiedTable()->delete();

        return back()->with('success', 'Booking cancelled successfully.');
    }
}
