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
        'mood',
        'tasks_completed',
        'focus_time_minutes',
        'productivity_score',
        'focus_time_score',
        'task_completion_score',
        'goal_achievement_score',
        'work_life_balance_score',
        'achievements',
        'challenges',
        'lessons_learned',
        'gratitude',
        'gratitude_note',
        'challenges_faced',
        'tomorrow_goals',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'tasks_completed' => 'integer',
        'focus_time_minutes' => 'integer',
        'productivity_score' => 'integer',
        'focus_time_score' => 'integer',
        'task_completion_score' => 'integer',
        'goal_achievement_score' => 'integer',
        'work_life_balance_score' => 'integer',
        'achievements' => 'array',
        'challenges' => 'array',
        'lessons_learned' => 'array',
        'gratitude' => 'array',
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
