<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    protected $fillable = [
        'name',
        'status',
        'capacity',
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function occupiedTables(): HasMany
    {
        return $this->hasMany(OccupiedTable::class);
    }

    public function booking()
    {
        $now = Carbon::now('Asia/Manila');
        // Find an occupied table for today and current or future time, not cancelled
        $occupied = $this->occupiedTables()
            ->where('date', $now->format('Y-m-d'))
            ->where('time', '>=', $now->format('H:i'))
            ->whereHasMorph('occupiable', [\App\Models\Booking::class], function ($query) {
                $query->whereNull('cancelled_at');
            })
            ->orderBy('time')
            ->first();

        if ($occupied && $occupied->occupiable) {
            $booking = $occupied->occupiable;

            return [
                'date' => $occupied->date,
                'time' => $occupied->time,
                'customer_name' => $booking->firstname.' '.$booking->lastname,
            ];
        }

        return null;
    }
}
