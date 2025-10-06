<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStats extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stat_date',
        'tasks_completed_today',
        'focus_minutes_today',
        'streak_days',
        'productivity_score',
        'mood_average',
        'energy_avg',
    ];

    protected $casts = [
        'stat_date' => 'date',
        'tasks_completed_today' => 'integer',
        'focus_minutes_today' => 'integer',
        'streak_days' => 'integer',
        'productivity_score' => 'decimal:2',
        'mood_average' => 'decimal:2',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('stat_date', $date);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('stat_date', [$startDate, $endDate]);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('stat_date', '>=', now()->subDays($days));
    }

    public function scopeHighProductivity($query)
    {
        return $query->where('productivity_score', '>=', 4.0);
    }

    public function scopeLowProductivity($query)
    {
        return $query->where('productivity_score', '<', 3.0);
    }

    public function scopeHighMood($query)
    {
        return $query->where('mood_average', '>=', 4.0);
    }

    public function scopeLowMood($query)
    {
        return $query->where('mood_average', '<', 3.0);
    }

    public function scopeByEnergyLevel($query, $energyLevel)
    {
        return $query->where('energy_avg', $energyLevel);
    }

    public function scopeWithStreak($query)
    {
        return $query->where('streak_days', '>', 0);
    }

    // Accessors
    public function getFocusHoursAttribute()
    {
        return round($this->focus_minutes_today / 60, 1);
    }

    public function getProductivityLevelAttribute()
    {
        if (!$this->productivity_score) return 'unknown';

        return match(true) {
            $this->productivity_score >= 4.5 => 'excellent',
            $this->productivity_score >= 3.5 => 'good',
            $this->productivity_score >= 2.5 => 'fair',
            $this->productivity_score >= 1.5 => 'poor',
            default => 'very_poor',
        };
    }

    public function getMoodLevelAttribute()
    {
        if (!$this->mood_average) return 'unknown';

        return match(true) {
            $this->mood_average >= 4.5 => 'excellent',
            $this->mood_average >= 3.5 => 'good',
            $this->mood_average >= 2.5 => 'fair',
            $this->mood_average >= 1.5 => 'poor',
            default => 'very_poor',
        };
    }

    public function getEnergyLevelDisplayAttribute()
    {
        return match($this->energy_avg) {
            'low' => '低',
            'medium' => '中',
            'high' => '高',
            default => '不明',
        };
    }

    public function getIsActiveTodayAttribute()
    {
        return $this->stat_date->isToday();
    }

    public function getIsRecentAttribute()
    {
        return $this->stat_date->isAfter(now()->subDays(7));
    }

    // Helper methods
    public function updateTasksCompleted($count)
    {
        $this->update(['tasks_completed_today' => $count]);
    }

    public function updateFocusMinutes($minutes)
    {
        $this->update(['focus_minutes_today' => $minutes]);
    }

    public function updateStreakDays($days)
    {
        $this->update(['streak_days' => $days]);
    }

    public function updateProductivityScore($score)
    {
        $this->update(['productivity_score' => $score]);
    }

    public function updateMoodAverage($mood)
    {
        $this->update(['mood_average' => $mood]);
    }

    public function updateEnergyAverage($energy)
    {
        $this->update(['energy_avg' => $energy]);
    }

    public function incrementTasksCompleted($count = 1)
    {
        $this->update(['tasks_completed_today' => $this->tasks_completed_today + $count]);
    }

    public function incrementFocusMinutes($minutes)
    {
        $this->update(['focus_minutes_today' => $this->focus_minutes_today + $minutes]);
    }

    public function incrementStreakDays($days = 1)
    {
        $this->update(['streak_days' => $this->streak_days + $days]);
    }

    public function resetStreak()
    {
        $this->update(['streak_days' => 0]);
    }

    public function isHighProductivity()
    {
        return $this->productivity_score && $this->productivity_score >= 4.0;
    }

    public function isLowProductivity()
    {
        return $this->productivity_score && $this->productivity_score < 3.0;
    }

    public function isHighMood()
    {
        return $this->mood_average && $this->mood_average >= 4.0;
    }

    public function isLowMood()
    {
        return $this->mood_average && $this->mood_average < 3.0;
    }

    public function hasHighEnergy()
    {
        return $this->energy_avg === 'high';
    }

    public function hasLowEnergy()
    {
        return $this->energy_avg === 'low';
    }

    public function hasStreak()
    {
        return $this->streak_days > 0;
    }

    public function isActiveToday()
    {
        return $this->stat_date->isToday();
    }

    public function getDailySummary()
    {
        return [
            'date' => $this->stat_date,
            'tasks_completed' => $this->tasks_completed_today,
            'focus_minutes' => $this->focus_minutes_today,
            'focus_hours' => $this->focus_hours,
            'streak_days' => $this->streak_days,
            'productivity_score' => $this->productivity_score,
            'productivity_level' => $this->productivity_level,
            'mood_average' => $this->mood_average,
            'mood_level' => $this->mood_level,
            'energy_avg' => $this->energy_avg,
            'energy_level_display' => $this->energy_level_display,
            'is_active_today' => $this->is_active_today,
            'is_recent' => $this->is_recent,
        ];
    }

    public function getPerformanceTrend($previousStats)
    {
        if (!$previousStats) return 'no_data';

        $currentScore = $this->productivity_score ?: 0;
        $previousScore = $previousStats->productivity_score ?: 0;

        $diff = $currentScore - $previousScore;

        if ($diff > 0.5) return 'improving';
        if ($diff < -0.5) return 'declining';
        return 'stable';
    }
}
