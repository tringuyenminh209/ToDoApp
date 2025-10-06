<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'metric_date',
        'metric_type',
        'metric_value',
        'trend_direction',
        'notes',
    ];

    protected $casts = [
        'metric_date' => 'date',
        'metric_value' => 'decimal:4',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('metric_type', $type);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('metric_date', $date);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('metric_date', [$startDate, $endDate]);
    }

    public function scopePositiveTrend($query)
    {
        return $query->where('trend_direction', 'up');
    }

    public function scopeNegativeTrend($query)
    {
        return $query->where('trend_direction', 'down');
    }

    public function scopeStableTrend($query)
    {
        return $query->where('trend_direction', 'stable');
    }

    public function scopeDailyCompletion($query)
    {
        return $query->where('metric_type', 'daily_completion');
    }

    public function scopeFocusTime($query)
    {
        return $query->where('metric_type', 'focus_time');
    }

    public function scopeMoodTrend($query)
    {
        return $query->where('metric_type', 'mood_trend');
    }

    public function scopeStreakMaintenance($query)
    {
        return $query->where('metric_type', 'streak_maintenance');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('metric_date', '>=', now()->subDays($days));
    }

    // Accessors
    public function getMetricTypeDisplayAttribute()
    {
        return match($this->metric_type) {
            'daily_completion' => '日次完了率',
            'focus_time' => '集中時間',
            'mood_trend' => '気分トレンド',
            'streak_maintenance' => 'ストリーク維持',
            default => '不明',
        };
    }

    public function getTrendDirectionDisplayAttribute()
    {
        return match($this->trend_direction) {
            'up' => '上昇',
            'down' => '下降',
            'stable' => '安定',
            default => '不明',
        };
    }

    public function getFormattedValueAttribute()
    {
        return match($this->metric_type) {
            'daily_completion' => $this->metric_value . '%',
            'focus_time' => $this->metric_value . '分',
            'mood_trend' => $this->metric_value . '/5',
            'streak_maintenance' => $this->metric_value . '日',
            default => $this->metric_value,
        };
    }

    // Helper methods
    public function calculateTrend($previousValue)
    {
        if ($previousValue === null) {
            return 'stable';
        }

        $diff = $this->metric_value - $previousValue;
        $threshold = $this->metric_value * 0.05; // 5% threshold

        if ($diff > $threshold) {
            return 'up';
        } elseif ($diff < -$threshold) {
            return 'down';
        } else {
            return 'stable';
        }
    }

    public function isImproving()
    {
        return $this->trend_direction === 'up';
    }

    public function isDeclining()
    {
        return $this->trend_direction === 'down';
    }

    public function isStable()
    {
        return $this->trend_direction === 'stable';
    }

    public function getPerformanceLevel()
    {
        return match($this->metric_type) {
            'daily_completion' => match(true) {
                $this->metric_value >= 80 => 'excellent',
                $this->metric_value >= 60 => 'good',
                $this->metric_value >= 40 => 'fair',
                default => 'poor',
            },
            'focus_time' => match(true) {
                $this->metric_value >= 120 => 'excellent', // 2+ hours
                $this->metric_value >= 60 => 'good',      // 1+ hour
                $this->metric_value >= 30 => 'fair',      // 30+ minutes
                default => 'poor',
            },
            'mood_trend' => match(true) {
                $this->metric_value >= 4 => 'excellent',
                $this->metric_value >= 3 => 'good',
                $this->metric_value >= 2 => 'fair',
                default => 'poor',
            },
            'streak_maintenance' => match(true) {
                $this->metric_value >= 7 => 'excellent',  // 1+ week
                $this->metric_value >= 3 => 'good',        // 3+ days
                $this->metric_value >= 1 => 'fair',        // 1+ day
                default => 'poor',
            },
            default => 'unknown',
        };
    }
}
