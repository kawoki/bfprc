<?php

namespace App\Models;

use Carbon\Carbon;
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
        'seats',
        'booking_date',
        'booking_time',
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
        'booking_date' => 'datetime:Y-m-d',
        'booking_time' => 'datetime:H:i',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Validation rules for the booking.
     *
     * @return array<string, string>
     */
    public static function rules(): array
    {
        return [
            'seats' => 'required|in:2,4,6,8',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|date_format:H:i',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'address' => 'required|string',
            'phone_number' => 'required|string|max:20',
        ];
    }

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
     * Get all booked times for a specific date with seat information
     */
    public static function getBookedTimesForDate(string $date): array
    {
        $bookings = self::where('booking_date', $date)
            ->whereNull('cancelled_at')
            ->get()
            ->groupBy(function ($booking) {
                return Carbon::parse($booking->booking_time)->format('H:i');
            })
            ->map(function ($timeBookings) {
                return $timeBookings->groupBy('seats')
                    ->map(function ($seatBookings) {
                        return $seatBookings->count();
                    })
                    ->toArray();
            })
            ->toArray();

        return $bookings;
    }
}
