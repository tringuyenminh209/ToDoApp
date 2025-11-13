<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * StudySchedule Model
 * スケジュール学習モデル
 *
 * Purpose:
 * - Enforce study discipline with scheduled learning times
 * - Track completion and missed sessions
 * - Support reminders for upcoming study sessions
 */
class StudySchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'learning_path_id',
        'study_time',
        'day_of_week',
        'duration_minutes',
        'is_active',
        'reminder_before_minutes',
        'reminder_enabled',
        'completed_sessions',
        'missed_sessions',
        'last_studied_at',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'duration_minutes' => 'integer',
        'is_active' => 'boolean',
        'reminder_before_minutes' => 'integer',
        'reminder_enabled' => 'boolean',
        'completed_sessions' => 'integer',
        'missed_sessions' => 'integer',
        'last_studied_at' => 'date',
    ];

    // Day of week constants
    public const SUNDAY = 0;
    public const MONDAY = 1;
    public const TUESDAY = 2;
    public const WEDNESDAY = 3;
    public const THURSDAY = 4;
    public const FRIDAY = 5;
    public const SATURDAY = 6;

    // Relationships
    public function learningPath(): BelongsTo
    {
        return $this->belongsTo(LearningPath::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDay($query, int $dayOfWeek)
    {
        return $query->where('day_of_week', $dayOfWeek);
    }

    public function scopeForToday($query)
    {
        return $query->where('day_of_week', Carbon::now()->dayOfWeek);
    }

    public function scopeUpcoming($query)
    {
        $now = Carbon::now();
        $currentTime = $now->format('H:i:s');
        $currentDay = $now->dayOfWeek;

        return $query->where('is_active', true)
            ->where(function ($q) use ($currentDay, $currentTime) {
                $q->where('day_of_week', '>', $currentDay)
                    ->orWhere(function ($q2) use ($currentDay, $currentTime) {
                        $q2->where('day_of_week', $currentDay)
                            ->where('study_time', '>', $currentTime);
                    });
            });
    }

    // Accessors
    public function getDayNameAttribute(): string
    {
        return $this->getDayName($this->day_of_week);
    }

    public function getDayNameJapaneseAttribute(): string
    {
        return match($this->day_of_week) {
            0 => '日曜日',
            1 => '月曜日',
            2 => '火曜日',
            3 => '水曜日',
            4 => '木曜日',
            5 => '金曜日',
            6 => '土曜日',
            default => '不明',
        };
    }

    public function getStudyTimeFormattedAttribute(): string
    {
        return Carbon::parse($this->study_time)->format('H:i');
    }

    public function getReminderTimeAttribute(): Carbon
    {
        $studyDateTime = $this->getNextStudyDateTime();
        return $studyDateTime->subMinutes($this->reminder_before_minutes);
    }

    public function getCompletionRateAttribute(): float
    {
        $total = $this->completed_sessions + $this->missed_sessions;
        if ($total === 0) return 0;

        return round(($this->completed_sessions / $total) * 100, 1);
    }

    public function getConsistencyScoreAttribute(): int
    {
        // Score out of 100 based on completion rate and missed sessions
        $completionRate = $this->completion_rate;
        $penalty = min($this->missed_sessions * 2, 20); // Max 20 point penalty

        return max(0, min(100, (int)($completionRate - $penalty)));
    }

    // Helper methods
    public static function getDayName(int $dayOfWeek): string
    {
        return match($dayOfWeek) {
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            default => 'Unknown',
        };
    }

    public static function getDayNameVietnamese(int $dayOfWeek): string
    {
        return match($dayOfWeek) {
            0 => 'Chủ Nhật',
            1 => 'Thứ Hai',
            2 => 'Thứ Ba',
            3 => 'Thứ Tư',
            4 => 'Thứ Năm',
            5 => 'Thứ Sáu',
            6 => 'Thứ Bảy',
            default => 'Không xác định',
        };
    }

    /**
     * Get the next study date/time for this schedule
     */
    public function getNextStudyDateTime(): Carbon
    {
        $now = Carbon::now();
        $targetDay = $this->day_of_week;
        $targetTime = Carbon::parse($this->study_time);

        // Find the next occurrence of this day
        $nextDate = $now->copy();

        // If today is the target day
        if ($now->dayOfWeek === $targetDay) {
            // If the time hasn't passed yet, use today
            if ($now->format('H:i:s') < $this->study_time) {
                $nextDate = $now->copy();
            } else {
                // Otherwise, use next week
                $nextDate = $now->copy()->addWeek()->startOfWeek()->addDays($targetDay);
            }
        } else {
            // Calculate days until target day
            $daysUntil = ($targetDay - $now->dayOfWeek + 7) % 7;
            if ($daysUntil === 0) {
                $daysUntil = 7; // Next week
            }
            $nextDate = $now->copy()->addDays($daysUntil);
        }

        return $nextDate->setTimeFromTimeString($this->study_time);
    }

    /**
     * Check if this schedule is due today
     */
    public function isDueToday(): bool
    {
        return $this->is_active && $this->day_of_week === Carbon::now()->dayOfWeek;
    }

    /**
     * Check if this schedule is upcoming (within next hour)
     */
    public function isUpcoming(): bool
    {
        if (!$this->isDueToday()) {
            return false;
        }

        $now = Carbon::now();
        $studyTime = Carbon::parse($this->study_time);
        $timeDiff = $now->diffInMinutes($studyTime, false);

        // Upcoming if within next 0-60 minutes
        return $timeDiff >= 0 && $timeDiff <= 60;
    }

    /**
     * Mark session as completed
     */
    public function markCompleted(): void
    {
        $this->increment('completed_sessions');
        $this->update(['last_studied_at' => Carbon::today()]);
    }

    /**
     * Mark session as missed
     */
    public function markMissed(): void
    {
        $this->increment('missed_sessions');
    }

    /**
     * Activate schedule
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Deactivate schedule
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Get schedules for the entire week
     */
    public static function getWeekSchedule(int $learningPathId): array
    {
        $schedules = self::where('learning_path_id', $learningPathId)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('study_time')
            ->get();

        // Group by day
        $weekSchedule = [];
        for ($day = 0; $day <= 6; $day++) {
            $weekSchedule[$day] = $schedules->where('day_of_week', $day)->values();
        }

        return $weekSchedule;
    }

    /**
     * Validate schedule doesn't conflict with user's timetable
     * (To be implemented in Phase 2 with AI)
     */
    public function conflictsWithTimetable(): bool
    {
        // TODO: Phase 2 - Check against user's timetable
        return false;
    }
}
