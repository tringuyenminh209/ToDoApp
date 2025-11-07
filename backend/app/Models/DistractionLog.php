<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DistractionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'focus_session_id',
        'distraction_type',
        'duration_seconds',
        'notes',
        'occurred_at',
        'time_of_day',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'duration_seconds' => 'integer',
    ];

    // Relationships
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function focusSession(): BelongsTo
    {
        return $this->belongsTo(FocusSession::class);
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByTask($query, $taskId)
    {
        return $query->where('task_id', $taskId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('distraction_type', $type);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('occurred_at', '>=', now()->subDays($days));
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('occurred_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('occurred_at', now()->month)
                    ->whereYear('occurred_at', now()->year);
    }

    public function scopeByTimeOfDay($query, $hour)
    {
        return $query->whereRaw('HOUR(time_of_day) = ?', [$hour]);
    }

    // Helper methods
    public function getDurationFormatted(): string
    {
        if (!$this->duration_seconds) {
            return 'Unknown';
        }

        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;

        if ($minutes > 0) {
            return $seconds > 0 ? "{$minutes}m {$seconds}s" : "{$minutes}m";
        }

        return "{$seconds}s";
    }

    public function getTypeDisplay(): string
    {
        return match($this->distraction_type) {
            'phone' => '📱 Phone',
            'social_media' => '💬 Social Media',
            'noise' => '🔊 Noise',
            'person' => '👥 Person',
            'thoughts' => '💭 Thoughts',
            'hunger_thirst' => '🍽️ Hunger/Thirst',
            'fatigue' => '😴 Fatigue',
            'other' => '❓ Other',
            default => 'Unknown',
        };
    }

    // Static methods for analytics
    public static function getTopDistractionsForUser($userId, $limit = 5)
    {
        return self::where('user_id', $userId)
            ->selectRaw('distraction_type, COUNT(*) as count, SUM(duration_seconds) as total_duration')
            ->groupBy('distraction_type')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }

    public static function getDistractionsByTimeOfDay($userId)
    {
        return self::where('user_id', $userId)
            ->whereNotNull('time_of_day')
            ->selectRaw('HOUR(time_of_day) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
    }

    public static function getAverageDistractionDuration($userId)
    {
        return self::where('user_id', $userId)
            ->whereNotNull('duration_seconds')
            ->avg('duration_seconds');
    }
}
