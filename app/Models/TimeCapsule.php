<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeCapsule extends Model
{
    protected $fillable = [
        'sender',
        'content',
        'unlock_at',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'unlock_at' => 'datetime',
        ];
    }

    public function isUnlocked(): bool
    {
        return $this->unlock_at->isPast();
    }
}
