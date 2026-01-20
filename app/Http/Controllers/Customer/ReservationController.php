<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Item;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\OccupiedTable;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReservationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $reservations = $user->bookings()
            ->with(['items.menu', 'occupiedTable.table'])
            ->latest()
            ->get();

        return Inertia::render('Customer/Reservations/Index', [
            'reservations' => $reservations,
        ]);
    }

    public function create()
    {
        $categories = MenuCategory::with('menus')->get();
        $tables = Table::where('status', 'available')->get();

        return Inertia::render('Customer/Reservations/Create', [
            'categories' => $categories,
            'tables' => $tables,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|date_format:H:i',
            'table_id' => 'required|exists:tables,id',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'address' => 'required|string',
            'phone_number' => 'required|string|max:20',
            'selected_menus' => 'nullable|array',
            'selected_menus.*.id' => 'required|exists:menus,id',
            'selected_menus.*.quantity' => 'required|integer|min:1',
            'proof_of_payment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $totalAmount = 0;

            if (!empty($validated['selected_menus'])) {
                foreach ($validated['selected_menus'] as $menuItem) {
                    $menu = Menu::find($menuItem['id']);
                    $totalAmount += $menu->price * $menuItem['quantity'];
                }
            }

            $proofOfPaymentPath = null;
            if ($request->hasFile('proof_of_payment')) {
                $proofOfPaymentPath = $request->file('proof_of_payment')->store('proofs', 'public');
            }

            $booking = Booking::create([
                'user_id' => auth()->id(),
                'firstname' => $validated['firstname'],
                'lastname' => $validated['lastname'],
                'address' => $validated['address'],
                'phone_number' => $validated['phone_number'],
                'proof_of_payment' => $proofOfPaymentPath,
                'total_amount' => $totalAmount,
            ]);

            OccupiedTable::create([
                'table_id' => $validated['table_id'],
                'occupiable_id' => $booking->id,
                'occupiable_type' => Booking::class,
                'date' => $validated['booking_date'],
                'time' => $validated['booking_time'],
            ]);

            if (!empty($validated['selected_menus'])) {
                foreach ($validated['selected_menus'] as $menuItem) {
                    $menu = Menu::find($menuItem['id']);
                    Item::create([
                        'itemable_id' => $booking->id,
                        'itemable_type' => Booking::class,
                        'menu_id' => $menu->id,
                        'quantity' => $menuItem['quantity'],
                        'price_at_time_of_order' => $menu->price,
                        'subtotal_at_time_of_order' => $menu->price * $menuItem['quantity'],
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('customer.reservations.index')
                ->with('success', 'Reservation created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Reservation creation failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to create reservation: ' . $e->getMessage()]);
        }
    }

    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $booking->cancel();

        return back()->with('success', 'Reservation cancelled successfully!');
    }

    public function getAvailableTimes(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'table_id' => 'required|exists:tables,id',
        ]);

        $bookedTimes = Booking::getBookedTimesForDate($request->date);
        $table = Table::find($request->table_id);

        $allTimes = [];
        for ($hour = 9; $hour < 21; $hour++) {
            for ($minute = 0; $minute < 60; $minute += 30) {
                $time = sprintf('%02d:%02d', $hour, $minute);
                $allTimes[] = $time;
            }
        }

        $availableTimes = array_filter($allTimes, function ($time) use ($bookedTimes, $request) {
            if (!isset($bookedTimes[$time])) {
                return true;
            }
            return !in_array($request->table_id, $bookedTimes[$time]['tableIds']);
        });

        return response()->json([
            'availableTimes' => array_values($availableTimes),
        ]);
    }
}
