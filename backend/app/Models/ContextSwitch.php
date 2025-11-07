<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContextSwitch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'from_task_id',
        'from_category',
        'from_focus_difficulty',
        'to_task_id',
        'to_category',
        'to_focus_difficulty',
        'is_significant_switch',
        'estimated_cost_minutes',
        'user_proceeded',
        'user_note',
    ];

    protected $casts = [
        'from_focus_difficulty' => 'integer',
        'to_focus_difficulty' => 'integer',
        'is_significant_switch' => 'boolean',
        'estimated_cost_minutes' => 'integer',
        'user_proceeded' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fromTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'from_task_id');
    }

    public function toTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'to_task_id');
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeSignificant($query)
    {
        return $query->where('is_significant_switch', true);
    }

    public function scopeUserProceeded($query)
    {
        return $query->where('user_proceeded', true);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    // Helper methods
    public function calculateCost(): int
    {
        // Base cost: 15 minutes
        $baseCost = 15;

        // Different category: +10 minutes
        if ($this->from_category !== $this->to_category) {
            $baseCost += 10;
        }

        // Focus difficulty jump: +5 minutes per level
        if ($this->from_focus_difficulty && $this->to_focus_difficulty) {
            $difficultyJump = abs($this->to_focus_difficulty - $this->from_focus_difficulty);
            if ($difficultyJump >= 2) {
                $baseCost += ($difficultyJump * 5);
            }
        }

        return $baseCost;
    }

    public function isSignificantSwitch(): bool
    {
        // Different category
        if ($this->from_category && $this->to_category && $this->from_category !== $this->to_category) {
            return true;
        }

        // Focus difficulty jump of 2 or more
        if ($this->from_focus_difficulty && $this->to_focus_difficulty) {
            $difficultyJump = abs($this->to_focus_difficulty - $this->from_focus_difficulty);
            if ($difficultyJump >= 2) {
                return true;
            }
        }

        return false;
    }

    public function getSwitchDescription(): string
    {
        $from = $this->from_category ?? 'Unknown';
        $to = $this->to_category ?? 'Unknown';

        if ($this->from_focus_difficulty && $this->to_focus_difficulty) {
            $from .= " (Focus: {$this->from_focus_difficulty})";
            $to .= " (Focus: {$this->to_focus_difficulty})";
        }

        return "{$from} → {$to}";
    }

    public function getCostDisplay(): string
    {
        return "~{$this->estimated_cost_minutes} minutes";
    }

    // Static methods for analytics
    public static function getTotalSwitchesForUser($userId, $days = 7)
    {
        return self::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays($days))
            ->count();
    }

    public static function getSignificantSwitchesForUser($userId, $days = 7)
    {
        return self::where('user_id', $userId)
            ->where('is_significant_switch', true)
            ->where('created_at', '>=', now()->subDays($days))
            ->count();
    }

    public static function getAverageSwitchCost($userId, $days = 7)
    {
        return self::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays($days))
            ->avg('estimated_cost_minutes');
    }

    public static function getTotalCostInMinutes($userId, $days = 7)
    {
        return self::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays($days))
            ->sum('estimated_cost_minutes');
    }

    public static function getMostCommonSwitchPattern($userId)
    {
        return self::where('user_id', $userId)
            ->selectRaw('from_category, to_category, COUNT(*) as count')
            ->groupBy('from_category', 'to_category')
            ->orderByDesc('count')
            ->limit(5)
            ->get();
    }
}
