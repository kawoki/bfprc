<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
        // Add other sale-specific fields if any, e.g., payment_type, reference_number
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the sale's items.
     */
    public function items(): MorphMany
    {
        return $this->morphMany(Item::class, 'itemable');
    }

    // If you had a relationship to Table via OccupiedTable, that would be different.
    // For direct table relationship if a sale makes a table occupied:
    // This is an indirect relationship if a Sale is an 'occupiable' on OccupiedTable.
    // public function occupiedTable()
    // {
    //    return $this->morphOne(OccupiedTable::class, 'occupiable');
    // }

    // public function table()
    // {
    //     return $this->hasOneThrough(Table::class, OccupiedTable::class, 'occupiable_id', 'id', 'id', 'table_id')
    //         ->where('occupiable_type', self::class);
    // }
}
