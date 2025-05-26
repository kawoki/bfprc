<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'address',
        'phone_number',
        'confirmed_at',
        'cancelled_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Get the full name of the customer.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->firstname} {$this->lastname}";
    }

    /**
     * Check if the booking is confirmed.
     */
    public function isConfirmed(): bool
    {
        return ! is_null($this->confirmed_at);
    }

    /**
     * Check if the booking is cancelled.
     */
    public function isCancelled(): bool
    {
        return ! is_null($this->cancelled_at);
    }

    /**
     * Confirm the booking.
     */
    public function confirm(): void
    {
        $this->update(['confirmed_at' => now()]);
    }

    /**
     * Cancel the booking.
     */
    public function cancel(): void
    {
        $this->update(['cancelled_at' => now()]);
    }

    /**
     * Scope a query to only include confirmed bookings.
     */
    public function scopeConfirmed($query)
    {
        return $query->whereNotNull('confirmed_at');
    }

    /**
     * Scope a query to only include cancelled bookings.
     */
    public function scopeCancelled($query)
    {
        return $query->whereNotNull('cancelled_at');
    }

    /**
     * Scope a query to only include active bookings.
     */
    public function scopeActive($query)
    {
        return $query->whereNull('cancelled_at');
    }

    /**
     * Get all booked times for a specific date with table information
     */
    public static function getBookedTimesForDate(string $date): array
    {
        $bookings = self::whereHas('occupiedTable', function ($query) use ($date) {
            $query->where('date', $date)
                ->whereNull('cancelled_at');
        })->get();

        return $bookings->groupBy(function ($booking) {
            return $booking->occupiedTable->time;
        })->map(function ($timeBookings) {
            return $timeBookings->groupBy(function ($booking) {
                return $booking->occupiedTable->table_id;
            })->map(function ($tableBookings) {
                return $tableBookings->count();
            })->toArray();
        })->toArray();
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'booking_menus')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function occupiedTable()
    {
        return $this->morphOne(OccupiedTable::class, 'occupiable');
    }
}
