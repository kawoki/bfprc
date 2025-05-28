<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'booking_time' => 'required|date_format:H:i', // This will be combined with date
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'address' => 'required|string',
            'phone_number' => 'required|string|max:20',
            'selected_menus' => 'sometimes|array', // 'sometimes' if menu selection is optional
            'selected_menus.*.menu_id' => 'required_with:selected_menus|exists:menus,id',
            'selected_menus.*.quantity' => 'required_with:selected_menus|integer|min:1',
        ]);

        // Combine date and time for a full booking_time timestamp
        $bookingDateTime = Carbon::parse($validated['booking_date'].' '.$validated['booking_time']);

        DB::beginTransaction();
        try {
            $booking = Booking::create([
                // Consider consolidating to customer_name, customer_email, customer_phone
                // For now, using existing fields + adding what's needed for items
                'firstname' => $validated['firstname'],
                'lastname' => $validated['lastname'],
                'address' => $validated['address'],
                'phone_number' => $validated['phone_number'],
                // 'booking_time' => $bookingDateTime, // If Booking model has this field
                // 'table_id' => $validated['table_id'], // If Booking model has this directly
                // 'user_id' => auth()->id(), // If tracking user who made booking
                // 'status' => 'pending', // Default status
            ]);

            // Create occupied table record
            // This implies booking_time and table_id are primarily managed via OccupiedTable
            $booking->occupiedTable()->create([
                'table_id' => $validated['table_id'],
                'date' => $bookingDateTime->format('Y-m-d'),
                'time' => $bookingDateTime->format('H:i'),
            ]);

            $totalBookingAmount = 0;

            if (isset($validated['selected_menus'])) {
                foreach ($validated['selected_menus'] as $selectedMenuData) {
                    $menu = Menu::find($selectedMenuData['menu_id']);
                    if ($menu) {
                        $subtotal = $menu->price * $selectedMenuData['quantity'];
                        $booking->items()->create([
                            'menu_id' => $menu->id,
                            'quantity' => $selectedMenuData['quantity'],
                            'price_at_time_of_order' => $menu->price,
                            'subtotal_at_time_of_order' => $subtotal,
                        ]);
                        $totalBookingAmount += $subtotal;
                    }
                }
            }

            // Save the calculated total_amount to the booking
            $booking->save(); // Save changes to the booking

            DB::commit();

            return redirect()->back()->with('success', 'Booking created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            // Log::error('Booking creation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create booking. '.$e->getMessage());
        }
    }

    public function getAvailableTimes(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        // This static method on Booking might need adjustment if it relied on the 'menus' pivot directly
        // For now, assuming it primarily checks OccupiedTable which is still in use
        $bookedTimes = Booking::getBookedTimesForDate($request->date);

        return response()->json([
            'bookedTimes' => $bookedTimes,
        ]);
    }

    public function index()
    {
        $today = Carbon::now()->startOfDay();
        // Ensure items and their menus are loaded, and also the table via occupiedTable
        $bookings = Booking::with(['items.menu', 'occupiedTable.table'])
            ->leftJoin('occupied_tables', function ($join) use ($today) {
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
        $bookings = Booking::with(['items.menu', 'occupiedTable.table'])
            ->leftJoin('occupied_tables', function ($join) use ($today) {
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
        $bookings = Booking::with(['items.menu', 'occupiedTable.table'])
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
        $booking->confirm();

        return back()->with('success', 'Booking confirmed successfully.');
    }

    public function cancel(Booking $booking)
    {

        $booking->cancel();

        // Potentially, if items have implications (e.g. pre-ordered stock), handle here.
        // For now, items remain associated for record-keeping but booking is cancelled.

        return back()->with('success', 'Booking cancelled successfully.');
    }
}
