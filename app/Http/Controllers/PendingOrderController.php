<?php

namespace App\Http\Controllers;

use App\Models\OccupiedTable;
use App\Models\PendingOrder;
use App\Models\Sale;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PendingOrderController extends Controller
{
    /**
     * Store a newly created pending order in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $pendingOrder = PendingOrder::where('table_id', $request->table_id)
                ->where('status', 'active')
                ->first();

            $totalAmount = collect($request->items)->sum(function ($item) {
                return $item['price'] * $item['quantity'];
            });

            if ($pendingOrder) {
                $pendingOrder->update(['total_amount' => $totalAmount]);
                $pendingOrder->items()->delete();
            } else {
                $pendingOrder = PendingOrder::create([
                    'table_id' => $request->table_id,
                    'user_id' => Auth::id(),
                    'total_amount' => $totalAmount,
                    'status' => 'active',
                ]);
            }

            foreach ($request->items as $itemData) {
                $pendingOrder->items()->create([
                    'menu_id' => $itemData['menu_id'],
                    'quantity' => $itemData['quantity'],
                    'price_at_time_of_order' => $itemData['price'],
                    'subtotal_at_time_of_order' => $itemData['price'] * $itemData['quantity'],
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Order saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error: error_log($e->getMessage());
            return redirect()->back()->with('error', 'Failed to save order. '.$e->getMessage());
        }
    }

    /**
     * Finalize a pending order and convert it to a sale.
     */
    public function finalize(PendingOrder $pendingOrder)
    {
        if ($pendingOrder->status !== 'active') {
            return redirect()->back()->with('error', 'This order is not active and cannot be finalized.');
        }

        DB::beginTransaction();
        try {
            $sale = Sale::create([
                'user_id' => $pendingOrder->user_id,
                'total_amount' => $pendingOrder->total_amount,
                'status' => 'completed',
            ]);

            foreach ($pendingOrder->items as $pendingItem) {
                $sale->items()->create([
                    'menu_id' => $pendingItem->menu_id,
                    'quantity' => $pendingItem->quantity,
                    'price_at_time_of_order' => $pendingItem->price_at_time_of_order,
                    'subtotal_at_time_of_order' => $pendingItem->subtotal_at_time_of_order,
                ]);
            }

            // Mark table as occupied by this sale
            OccupiedTable::create([
                'occupiable_id' => $sale->id,
                'occupiable_type' => Sale::class,
                'table_id' => $pendingOrder->table_id,
                'date' => now()->format('Y-m-d'),
                'time' => now()->format('H:i'),
            ]);

            Table::where('id', $pendingOrder->table_id)->update(['status' => 'occupied']);

            $pendingOrder->update(['status' => 'completed']);

            DB::commit();

            return redirect()->back()->with('success', 'Order finalized successfully and sale created!');
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error: error_log($e->getMessage());
            return redirect()->back()->with('error', 'Failed to finalize order. '.$e->getMessage());
        }
    }

    /**
     * Remove the specified pending order from storage.
     */
    public function destroy(PendingOrder $pendingOrder)
    {
        if ($pendingOrder->status !== 'active') {
            // Or, allow deletion regardless of status, depending on desired business logic
            // For now, only active pending orders that are emptied out should be auto-deleted.
            // If called directly for other reasons, this check might change.
            // However, the frontend logic will only call this for active ones that become empty.
        }

        DB::beginTransaction();
        try {
            // Items are deleted via the MorphMany relationship's cascading delete
            // or model events if configured. Explicit delete here is safer.
            $pendingOrder->items()->delete();
            $pendingOrder->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Pending order cleared successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error: error_log($e->getMessage());
            return redirect()->back()->with('error', 'Failed to clear pending order. '.$e->getMessage());
        }
    }
}
