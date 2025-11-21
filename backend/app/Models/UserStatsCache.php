<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStatsCache extends Model
{
    protected $table = 'user_stats_cache';

    protected $fillable = [
        'user_id',
        'total_tasks',
        'completed_tasks',
        'pending_tasks',
        'in_progress_tasks',
        'completion_rate',
        'total_focus_time',
        'total_focus_sessions',
        'average_session_duration',
        'current_streak',
        'longest_streak',
        'last_calculated_at',
    ];

    protected $casts = [
        'total_tasks' => 'integer',
        'completed_tasks' => 'integer',
        'pending_tasks' => 'integer',
        'in_progress_tasks' => 'integer',
        'completion_rate' => 'decimal:2',
        'total_focus_time' => 'integer',
        'total_focus_sessions' => 'integer',
        'average_session_duration' => 'integer',
        'current_streak' => 'integer',
        'longest_streak' => 'integer',
        'last_calculated_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helper Methods
    public function isStale(int $minutesThreshold = 5): bool
    {
        if (!$this->last_calculated_at) {
            return true;
        }

        return $this->last_calculated_at->diffInMinutes(now()) > $minutesThreshold;
    }

    public function markAsCalculated(): void
    {
        $this->update(['last_calculated_at' => now()]);
    }
}
