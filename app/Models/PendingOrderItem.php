<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendingOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pending_order_id',
        'menu_id',
        'quantity',
        'price',
        'subtotal',
    ];

    public function pendingOrder(): BelongsTo
    {
        return $this->belongsTo(PendingOrder::class);
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}
