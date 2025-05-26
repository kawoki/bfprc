<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\MenuCategory;
use App\Models\OccupiedTable;
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
        $tables = Table::with(['occupiedTables' => function ($query) use ($now) {
            $query->where('date', $now->format('Y-m-d'))
                ->where('time', '<=', $now->format('H:i'))
                ->whereHasMorph('occupiable', [Booking::class], function ($query) {
                    $query->whereNull('cancelled_at');
                });
        }])->get();

        // Mark tables as occupied if they have a confirmed booking and attach booking info
        $tables->each(function ($table) {
            $hasConfirmed = $table->occupiedTables
                ->filter(function ($ot) {
                    return $ot->occupiable_type === Booking::class
                        && $ot->occupiable
                        && $ot->occupiable->confirmed_at
                        && ! $ot->occupiable->cancelled_at;
                })
                ->isNotEmpty();

            $table->status = $hasConfirmed ? 'occupied' : 'available';
            $table->booking = $table->booking(); // Attach booking info as a property
        });

        // Fetch confirmed bookings for today
        $confirmedBookings = Booking::confirmed()
            ->whereNull('cancelled_at')
            ->whereHas('occupiedTable', function ($query) use ($now) {
                $query->where('date', $now->format('Y-m-d'));
            })
            ->with(['occupiedTable.table'])
            ->get();

        return Inertia::render('Order', [
            'categories' => MenuCategory::with('menus')->get(),
            'tables' => $tables,
            'confirmedBookings' => $confirmedBookings,
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
            'user_id' => auth()->id(),
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
