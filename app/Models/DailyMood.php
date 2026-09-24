<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyMood extends Model
{
    use HasFactory;

    protected $table = 'daily_moods';

    protected $fillable = [
        'mood_emoji',
        'mood_label',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}