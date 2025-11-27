<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskAbandonment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'task_id',
        'focus_session_id',
        'started_at',
        'last_active_at',
        'abandoned_at',
        'duration_minutes',
        'abandonment_type',
        'inactivity_minutes',
        'auto_detected',
        'reason',
        'resumed',
        'resumed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'last_active_at' => 'datetime',
        'abandoned_at' => 'datetime',
        'resumed_at' => 'datetime',
        'duration_minutes' => 'integer',
        'inactivity_minutes' => 'integer',
        'auto_detected' => 'boolean',
        'resumed' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
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

    public function scopeAutoDetected($query)
    {
        return $query->where('auto_detected', true);
    }

    public function scopeManual($query)
    {
        return $query->where('auto_detected', false);
    }

    public function scopeResumed($query)
    {
        return $query->where('resumed', true);
    }

    public function scopeNotResumed($query)
    {
        return $query->where('resumed', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('abandonment_type', $type);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('abandoned_at', '>=', now()->subDays($days));
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('abandoned_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('abandoned_at', now()->month)
                    ->whereYear('abandoned_at', now()->year);
    }

    // Accessors
    public function getFormattedDurationAttribute()
    {
        $minutes = $this->duration_minutes;
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return "{$hours}時間{$mins}分";
        }
        return "{$mins}分";
    }

    public function getFormattedInactivityAttribute()
    {
        if (!$this->inactivity_minutes) {
            return '-';
        }

        $minutes = $this->inactivity_minutes;
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return "{$hours}時間{$mins}分";
        }
        return "{$mins}分";
    }

    public function getTypeDisplayAttribute()
    {
        return match($this->abandonment_type) {
            'app_switched' => 'アプリ切り替え',
            'long_inactivity' => '長期間非アクティブ',
            'manual' => '手動放棄',
            'deadline_passed' => '締め切り超過',
            default => '不明',
        };
    }

    public function getResumeTimeAttribute()
    {
        if (!$this->resumed || !$this->resumed_at || !$this->abandoned_at) {
            return null;
        }

        return $this->abandoned_at->diffInMinutes($this->resumed_at);
    }

    // Helper methods
    public function markAsResumed()
    {
        $this->update([
            'resumed' => true,
            'resumed_at' => now(),
        ]);
    }

    public function calculateProductivityLoss(): float
    {
        // Calculate productivity loss based on duration worked and abandonment
        // Assuming completed task = 100% productivity
        // Abandoned task productivity = (duration worked / estimated duration) * penalty factor

        $penaltyFactor = 0.5; // 50% of work is considered lost when abandoned

        if ($this->task && $this->task->estimated_minutes) {
            $completionRatio = min(1.0, $this->duration_minutes / $this->task->estimated_minutes);
            return $this->duration_minutes * $completionRatio * $penaltyFactor;
        }

        return $this->duration_minutes * $penaltyFactor;
    }

    // Static methods for analytics
    public static function getTotalAbandonmentsForUser($userId, $days = 7)
    {
        return self::where('user_id', $userId)
            ->where('abandoned_at', '>=', now()->subDays($days))
            ->count();
    }

    public static function getAbandonmentRateForUser($userId, $days = 7)
    {
        $totalSessions = FocusSession::where('user_id', $userId)
            ->where('started_at', '>=', now()->subDays($days))
            ->count();

        if ($totalSessions == 0) {
            return 0;
        }

        $abandonments = self::getTotalAbandonmentsForUser($userId, $days);

        return round(($abandonments / $totalSessions) * 100, 1);
    }

    public static function getAverageWorkTimeBeforeAbandonment($userId, $days = 7)
    {
        return self::where('user_id', $userId)
            ->where('abandoned_at', '>=', now()->subDays($days))
            ->avg('duration_minutes');
    }

    public static function getMostCommonAbandonmentType($userId)
    {
        return self::where('user_id', $userId)
            ->selectRaw('abandonment_type, COUNT(*) as count')
            ->groupBy('abandonment_type')
            ->orderByDesc('count')
            ->first();
    }

    public static function getResumeRate($userId, $days = 7)
    {
        $total = self::where('user_id', $userId)
            ->where('abandoned_at', '>=', now()->subDays($days))
            ->count();

        if ($total == 0) {
            return 0;
        }

        $resumed = self::where('user_id', $userId)
            ->where('abandoned_at', '>=', now()->subDays($days))
            ->where('resumed', true)
            ->count();

        return round(($resumed / $total) * 100, 1);
    }
}
