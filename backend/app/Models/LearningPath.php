<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningPath extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'goal_type',
        'target_start_date',
        'target_end_date',
        'status',
        'progress_percentage',
        'is_ai_generated',
        'ai_prompt',
        'estimated_hours_total',
        'actual_hours_total',
        'tags',
        'color',
        'icon',
    ];

    protected $casts = [
        'progress_percentage' => 'decimal:2',
        'target_start_date' => 'date',
        'target_end_date' => 'date',
        'is_ai_generated' => 'boolean',
        'estimated_hours_total' => 'integer',
        'actual_hours_total' => 'integer',
        'tags' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(LearningMilestone::class)->orderBy('sort_order');
    }

    public function knowledgeItems(): HasMany
    {
        return $this->hasMany(KnowledgeItem::class);
    }

    public function studySchedules(): HasMany
    {
        return $this->hasMany(StudySchedule::class)->orderBy('day_of_week')->orderBy('study_time');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePaused($query)
    {
        return $query->where('status', 'paused');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeAbandoned($query)
    {
        return $query->where('status', 'abandoned');
    }

    public function scopeByGoalType($query, $goalType)
    {
        return $query->where('goal_type', $goalType);
    }

    public function scopeAiGenerated($query)
    {
        return $query->where('is_ai_generated', true);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'active')
                    ->where('progress_percentage', '>', 0)
                    ->where('progress_percentage', '<', 100);
    }

    // Accessors
    public function getStatusDisplayAttribute()
    {
        return match($this->status) {
            'active' => 'アクティブ',
            'paused' => '一時停止',
            'completed' => '完了',
            'abandoned' => '放棄',
            default => '不明',
        };
    }

    public function getGoalTypeDisplayAttribute()
    {
        return match($this->goal_type) {
            'career' => 'キャリア',
            'skill' => 'スキル',
            'certification' => '資格',
            'hobby' => '趣味',
            default => '不明',
        };
    }

    public function getEstimatedDurationDaysAttribute()
    {
        if (!$this->estimated_hours_total) return null;

        return round($this->estimated_hours_total / 8); // Assuming 8 hours per day
    }

    public function getTimeUtilizationAttribute()
    {
        if (!$this->estimated_hours_total || $this->estimated_hours_total == 0) {
            return null;
        }

        return round(($this->actual_hours_total / $this->estimated_hours_total) * 100, 1);
    }

    public function getDaysRemainingAttribute()
    {
        if (!$this->target_end_date) return null;

        return now()->diffInDays($this->target_end_date, false);
    }

    public function getIsOverdueAttribute()
    {
        return $this->target_end_date &&
               $this->target_end_date->isPast() &&
               $this->status !== 'completed';
    }

    // Helper methods
    public function calculateProgress()
    {
        $milestones = $this->milestones;
        if ($milestones->isEmpty()) {
            return $this->progress_percentage;
        }

        $totalProgress = $milestones->avg('progress_percentage');
        $this->update(['progress_percentage' => $totalProgress]);

        return $totalProgress;
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'progress_percentage' => 100,
        ]);
    }

    public function pause()
    {
        $this->update(['status' => 'paused']);
    }

    public function resume()
    {
        $this->update(['status' => 'active']);
    }

    public function abandon()
    {
        $this->update(['status' => 'abandoned']);
    }

    public function canBeStarted()
    {
        return $this->status === 'active' &&
               (!$this->target_start_date || $this->target_start_date->isPast());
    }

    public function needsAttention()
    {
        return $this->is_overdue ||
               ($this->target_end_date && $this->target_end_date->isBefore(now()->addDays(7)));
    }

    /**
     * Check if learning path has study schedules set up
     */
    public function hasStudySchedule(): bool
    {
        return $this->studySchedules()->where('is_active', true)->exists();
    }

    /**
     * Get active study schedules
     */
    public function getActiveSchedules()
    {
        return $this->studySchedules()->where('is_active', true)->get();
    }

    /**
     * Get weekly schedule summary
     */
    public function getWeeklyScheduleSummary(): array
    {
        $schedules = $this->getActiveSchedules();
        $summary = [];

        foreach ($schedules as $schedule) {
            $day = $schedule->day_of_week;
            if (!isset($summary[$day])) {
                $summary[$day] = [];
            }
            $summary[$day][] = [
                'time' => $schedule->study_time_formatted,
                'duration' => $schedule->duration_minutes,
                'day_name' => StudySchedule::getDayNameVietnamese($day),
            ];
        }

        return $summary;
    }

    /**
     * Calculate total study hours per week
     */
    public function getWeeklyStudyHours(): float
    {
        $totalMinutes = $this->getActiveSchedules()->sum('duration_minutes');
        return round($totalMinutes / 60, 1);
    }
}
