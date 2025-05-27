<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PendingOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_id',
        'user_id',
        'status',
        'total_amount',
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the pending order's items.
     */
    public function items(): MorphMany
    {
        return $this->morphMany(Item::class, 'itemable');
    }
}
