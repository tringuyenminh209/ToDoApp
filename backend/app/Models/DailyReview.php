<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'tasks_completed',
        'focus_time_minutes',
        'productivity_score',
        'gratitude_note',
        'challenges_faced',
        'tomorrow_goals',
    ];

    protected $casts = [
        'tasks_completed' => 'integer',
        'focus_time_minutes' => 'integer',
        'productivity_score' => 'integer',
        'date' => 'date',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }
}
