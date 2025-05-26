<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OccupiedTable extends Model
{
    protected $fillable = [
        'table_id',
        'occupiable_id',
        'occupiable_type',
        'date',
        'time',
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function occupiable(): MorphTo
    {
        return $this->morphTo();
    }
}
