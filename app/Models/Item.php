<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'itemable_id',
        'itemable_type',
        'menu_id',
        'quantity',
        'price_at_time_of_order',
        'subtotal_at_time_of_order',
    ];

    /**
     * Get the parent itemable model (Sale, Booking, or PendingOrder).
     */
    public function itemable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the menu item associated with this item.
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}
