<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\MenuCategory;
use App\Models\OccupiedTable;
use App\Models\PendingOrder;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $now = Carbon::now('Asia/Manila');

        // Fetch tables with their occupiedTable entries, focusing on those linked to Bookings.
        // We will determine the live status in the loop.
        $tables = Table::with([
            'occupiedTables' => function ($query) use ($now) {
                $query->where('date', $now->format('Y-m-d'))
                    ->whereHasMorph('occupiable', [Booking::class], function ($bookingQuery) {
                        // Eager load menus for the Booking model when it's the occupiable type
                        $bookingQuery->whereNotNull('confirmed_at')
                            ->whereNull('cancelled_at')
                            ->with('menus'); // menus with pivot data (quantity)
                    })
                    ->with('occupiable'); // This loads the Booking model itself (or Sale model)
            },
        ])->get();

        $tables->each(function ($table) use ($now) {
            $currentActiveBooking = null;

            // Find the first OccupiedTable entry for this table that represents an active booking
            // within the 1-hour window.
            $activeOccupation = $table->occupiedTables
                ->first(function ($ot) use ($now) {
                    // Ensure $ot is linked to a Booking model and it's loaded.
                    // The eager load conditions should already ensure this.
                    if (! ($ot->occupiable instanceof Booking)) {
                        return false;
                    }

                    // $ot->date is today's date (e.g., "2024-03-25")
                    // $ot->time is the booking start time (e.g., "19:00" or "19:00:00")
                    $bookingDateStr = $ot->date;
                    $bookingTimeStr = $ot->time;

                    try {
                        // Carbon::parse can handle "Y-m-d H:i" or "Y-m-d H:i:s"
                        $bookingStartDateTime = Carbon::parse($bookingDateStr.' '.$bookingTimeStr, 'Asia/Manila');
                    } catch (\Exception $e) {
                        // Log or handle parsing errors if necessary
                        // error_log("Failed to parse booking datetime for OT id {$ot->id}: {$bookingDateStr} {$bookingTimeStr}. Error: {$e->getMessage()}");
                        return false; // Cannot process this record if time is invalid
                    }

                    $bookingEndDateTime = $bookingStartDateTime->copy()->addHour();

                    // Check if current time $now is >= bookingStartDateTime and < bookingEndDateTime
                    return $now->gte($bookingStartDateTime) && $now->lt($bookingEndDateTime);
                });

            if ($activeOccupation) {
                $table->status = 'occupied'; // Dynamically set status for the view
                $table->booking = $activeOccupation->occupiable; // If occupiable is Booking, it should have ->menus now
            } else {
                // If no active booking makes it occupied by the 1-hour rule,
                // set status to 'available' for the view.
                // This overrides any status from DB for the purpose of this view's logic for bookings.
                // Table status due to sales is handled by direct updates to the table's status column.
                $table->status = 'available';
                $table->booking = null;
            }
        });

        // Fetch confirmed bookings for today
        $allConfirmedToday = Booking::confirmed()
            ->whereNull('cancelled_at')
            ->whereHas('occupiedTable', function ($query) use ($now) {
                $query->where('date', $now->format('Y-m-d'));
            })
            ->with(['occupiedTable.table'])
            ->get();

        // Filter these bookings: only include those whose 1-hour slot hasn't fully passed
        $confirmedBookings = $allConfirmedToday->filter(function ($booking) use ($now) {
            if (! $booking->occupiedTable) {
                return false;
            }

            $bookingDateStr = $booking->occupiedTable->date;
            $bookingTimeStr = $booking->occupiedTable->time;

            try {
                $bookingStartDateTime = Carbon::parse($bookingDateStr.' '.$bookingTimeStr, 'Asia/Manila');
                $bookingEffectiveEndDateTime = $bookingStartDateTime->copy()->addHour();

                // Keep the booking if the current time is before its effective end time.
                return $now->lt($bookingEffectiveEndDateTime);
            } catch (\Exception $e) {
                // Log error or handle if time parsing fails (e.g., malformed time string)
                // error_log("Error parsing booking time for booking ID {$booking->id}: " . $e->getMessage());
                return false;
            }
        })->values(); // ->values() to re-index the collection if it becomes sparse

        // Fetch active pending orders with their items and table information
        $activePendingOrders = PendingOrder::where('status', 'active')
            ->with(['items.menu', 'table'])
            ->orderBy('created_at', 'desc')
            ->get();

        $tables->each(function ($table) use ($activePendingOrders) {
            $table->has_active_pending_order = $activePendingOrders->contains(function ($po) use ($table) {
                return $po->table_id === $table->id && $po->status === 'active';
            });
        });

        return Inertia::render('Order', [
            'categories' => MenuCategory::with('menus')->get(),
            'tables' => $tables,
            'confirmedBookings' => $confirmedBookings,
            'activePendingOrders' => $activePendingOrders,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'items' => 'required|array',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $sale = Sale::create([
            'user_id' => auth()->user()->id,
            'total_amount' => collect($request->items)->sum(fn ($item) => $item['price'] * $item['quantity']),
            'status' => 'pending',
        ]);

        foreach ($request->items as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'menu_id' => $item['menu_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['price'] * $item['quantity'],
            ]);
        }

        OccupiedTable::create([
            'occupiable_id' => $sale->id,
            'occupiable_type' => Sale::class,
            'table_id' => $request->table_id,
            'date' => now()->format('Y-m-d'),
            'time' => now()->format('H:i'),
        ]);

        // Update table status
        Table::where('id', $request->table_id)->update(['status' => 'occupied']);

        return redirect()->back()->with('success', 'Order created successfully');
    }

    public function complete(Sale $sale)
    {
        $sale->update(['status' => 'completed']);
        $sale->table()->update(['status' => 'available']);

        return redirect()->back()->with('success', 'Order completed successfully');
    }

    public function cancel(Sale $sale)
    {
        $sale->update(['status' => 'cancelled']);
        $sale->table()->update(['status' => 'available']);

        return redirect()->back()->with('success', 'Order cancelled successfully');
    }
}
