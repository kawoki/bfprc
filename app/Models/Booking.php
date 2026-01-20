<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
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
        'user_id',
        'firstname',
        'lastname',
        'address',
        'phone_number',
        'proof_of_payment',
        'total_amount',
        'confirmed_at',
        'cancelled_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
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
        return ! is_null($this->confirmed_at) && is_null($this->cancelled_at);
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
        $this->update([
            'confirmed_at' => now(),
            'cancelled_at' => null,
        ]);
    }

    /**
     * Cancel the booking.
     */
    public function cancel(): void
    {
        $this->update([
            'confirmed_at' => null,
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Scope a query to only include confirmed bookings.
     */
    public function scopeConfirmed($query)
    {
        return $query->whereNotNull('confirmed_at')->whereNull('cancelled_at');
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
     * Returns bookings with both capacity counts and specific table IDs
     */
    public static function getBookedTimesForDate(string $date): array
    {
        $bookings = self::whereHas('occupiedTable', function ($query) use ($date) {
            $query->where('date', $date);
        })->whereNull('cancelled_at')
            ->with(['occupiedTable.table'])
            ->get();

        $filteredBookings = $bookings->filter(function ($booking) {
            // Filter out bookings without occupied table or table data
            return $booking->occupiedTable
                && $booking->occupiedTable->relationLoaded('table')
                && $booking->occupiedTable->table !== null;
        });

        $result = [];

        foreach ($filteredBookings->groupBy(function ($booking) {
            // Format time as H:i (remove seconds)
            return substr($booking->occupiedTable->time, 0, 5);
        }) as $time => $timeBookings) {
            $result[$time] = [
                'capacities' => [],
                'tableIds' => []
            ];

            // Group by capacity for counts
            foreach ($timeBookings->groupBy(function ($booking) {
                $tableModel = $booking->occupiedTable->getRelation('table');
                return (string) $tableModel->capacity;
            }) as $capacity => $capacityBookings) {
                $result[$time]['capacities'][$capacity] = $capacityBookings->count();
            }

            // Collect actual booked table IDs
            foreach ($timeBookings as $booking) {
                $result[$time]['tableIds'][] = $booking->occupiedTable->table_id;
            }
        }

        return $result;
    }

    /**
     * Get all of the booking's items (formerly menus).
     */
    public function items(): MorphMany
    {
        return $this->morphMany(Item::class, 'itemable');
    }

    /**
     * Get the occupied table record associated with the booking.
     */
    public function occupiedTable(): MorphOne
    {
        return $this->morphOne(OccupiedTable::class, 'occupiable');
    }

    /**
     * Get the user that owns the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
