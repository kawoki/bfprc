<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $status
 * @property Carbon $read_at
 * @property Carbon $replied_at
 */
class Inquiry extends Model
{

    protected $guarded = [];

    public function markAs(string $status): void
    {
        match ($status) {
            'read' => $this->markAsRead(),
            'replied' => $this->markAsReplied()
        };

        $this->fresh();
    }

    public function markAsRead(): void
    {
        $this->status = 'read';
        $this->read_at = now();
        $this->save();
    }

    public function markAsReplied(): void
    {
        $this->status = 'replied';
        $this->read_at = now();
        $this->replied_at = now();
        $this->save();
    }

}
