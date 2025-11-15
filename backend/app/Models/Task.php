<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'learning_milestone_id',
        'title',
        'category',
        'description',
        'priority',
        'energy_level',
        'estimated_minutes',
        'deadline',
        'scheduled_time',
        'status',
        'ai_breakdown_enabled',
        // Focus enhancement features
        'requires_deep_focus',
        'allow_interruptions',
        'focus_difficulty',
        'warmup_minutes',
        'cooldown_minutes',
        'recovery_minutes',
        'last_focus_at',
        'total_focus_minutes',
        'distraction_count',
    ];

    protected $casts = [
        // deadline is cast as 'datetime' but will be formatted as date-only in serialization
        'deadline' => 'datetime',
        // scheduled_time is TIME type (HH:MM:SS) - no casting needed, returns as string
        'priority' => 'integer',
        'estimated_minutes' => 'integer',
        'ai_breakdown_enabled' => 'boolean',
        // Focus enhancement features
        'requires_deep_focus' => 'boolean',
        'allow_interruptions' => 'boolean',
        'focus_difficulty' => 'integer',
        'warmup_minutes' => 'integer',
        'cooldown_minutes' => 'integer',
        'recovery_minutes' => 'integer',
        'last_focus_at' => 'datetime',
        'total_focus_minutes' => 'integer',
        'distraction_count' => 'integer',
    ];

    /**
     * Serialize deadline as date-only (Y-m-d) to avoid timezone issues
     */
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d');
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function learningMilestone(): BelongsTo
    {
        return $this->belongsTo(LearningMilestone::class);
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Subtask::class)->orderBy('sort_order');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'task_tags');
    }

    public function focusSessions(): HasMany
    {
        return $this->hasMany(FocusSession::class);
    }

    public function knowledgeItems(): HasMany
    {
        return $this->hasMany(KnowledgeItem::class, 'source_task_id');
    }

    public function focusEnvironments(): HasMany
    {
        return $this->hasMany(FocusEnvironment::class);
    }

    public function distractionLogs(): HasMany
    {
        return $this->hasMany(DistractionLog::class);
    }

    public function contextSwitchesFrom(): HasMany
    {
        return $this->hasMany(ContextSwitch::class, 'from_task_id');
    }

    public function contextSwitchesTo(): HasMany
    {
        return $this->hasMany(ContextSwitch::class, 'to_task_id');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByEnergyLevel($query, $energyLevel)
    {
        return $query->where('energy_level', $energyLevel);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
                    ->where('status', '!=', 'completed');
    }

    public function scopeWithDeadline($query)
    {
        return $query->whereNotNull('deadline');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByMilestone($query, $milestoneId)
    {
        return $query->where('learning_milestone_id', $milestoneId);
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', '>=', 4);
    }

    public function scopeLowPriority($query)
    {
        return $query->where('priority', '<=', 2);
    }

    public function scopeHighEnergy($query)
    {
        return $query->where('energy_level', 'high');
    }

    public function scopeLowEnergy($query)
    {
        return $query->where('energy_level', 'low');
    }

    public function scopeWithEstimatedTime($query)
    {
        return $query->whereNotNull('estimated_minutes');
    }

    public function scopeWithoutEstimatedTime($query)
    {
        return $query->whereNull('estimated_minutes');
    }

    public function scopeDueSoon($query, $days = 3)
    {
        return $query->where('deadline', '<=', now()->addDays($days))
                    ->where('status', '!=', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeAiBreakdownEnabled($query)
    {
        return $query->where('ai_breakdown_enabled', true);
    }

    // Accessors
    public function getIsOverdueAttribute()
    {
        return $this->deadline && $this->deadline->isPast() && $this->status !== 'completed';
    }

    public function getCompletionPercentageAttribute()
    {
        if ($this->subtasks->isEmpty()) {
            return $this->status === 'completed' ? 100 : 0;
        }

        $completedSubtasks = $this->subtasks->where('is_completed', true)->count();
        return $this->subtasks->count() > 0
            ? round(($completedSubtasks / $this->subtasks->count()) * 100)
            : 0;
    }

    public function getStatusDisplayAttribute()
    {
        return match($this->status) {
            'pending' => '待機中',
            'in_progress' => '進行中',
            'completed' => '完了',
            'cancelled' => 'キャンセル',
            default => '不明',
        };
    }

    public function getPriorityDisplayAttribute()
    {
        return match($this->priority) {
            1 => '低',
            2 => 'やや低',
            3 => '中',
            4 => 'やや高',
            5 => '高',
            default => '不明',
        };
    }

    public function getEnergyLevelDisplayAttribute()
    {
        return match($this->energy_level) {
            'low' => '低',
            'medium' => '中',
            'high' => '高',
            default => '不明',
        };
    }

    public function getEstimatedHoursAttribute()
    {
        return $this->estimated_minutes ? round($this->estimated_minutes / 60, 1) : null;
    }

    public function getEstimatedTimeFormattedAttribute()
    {
        if (!$this->estimated_minutes) {
            return 'No estimate';
        }

        $hours = floor($this->estimated_minutes / 60);
        $minutes = $this->estimated_minutes % 60;

        if ($hours > 0) {
            return $minutes > 0 ? "{$hours}h {$minutes}m" : "{$hours}h";
        }

        return "{$minutes}m";
    }

    public function getDaysUntilDeadlineAttribute()
    {
        if (!$this->deadline) return null;

        return now()->diffInDays($this->deadline, false);
    }

    public function getIsDueSoonAttribute()
    {
        return $this->days_until_deadline !== null &&
               $this->days_until_deadline <= 3 &&
               $this->days_until_deadline >= 0;
    }

    // Helper methods
    public function markAsCompleted()
    {
        $this->update(['status' => 'completed']);

        // Auto-complete all subtasks
        $this->subtasks()->update(['is_completed' => true]);
    }

    public function markAsInProgress()
    {
        $this->update(['status' => 'in_progress']);
    }

    public function markAsPending()
    {
        $this->update(['status' => 'pending']);
    }

    public function markAsCancelled()
    {
        $this->update(['status' => 'cancelled']);
    }

    public function getTotalEstimatedTime()
    {
        $subtaskTime = $this->subtasks->sum('estimated_minutes');
        return $this->estimated_minutes ? max($this->estimated_minutes, $subtaskTime) : $subtaskTime;
    }

    public function getNextSubtask()
    {
        return $this->subtasks()
                   ->where('is_completed', false)
                   ->orderBy('sort_order')
                   ->first();
    }

    public function getCompletedSubtasksCount()
    {
        return $this->subtasks()->where('is_completed', true)->count();
    }

    public function getPendingSubtasksCount()
    {
        return $this->subtasks()->where('is_completed', false)->count();
    }

    public function getTotalSubtasksCount()
    {
        return $this->subtasks()->count();
    }

    public function hasSubtasks()
    {
        return $this->subtasks()->exists();
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function isHighPriority()
    {
        return $this->priority >= 4;
    }

    public function isLowPriority()
    {
        return $this->priority <= 2;
    }

    public function requiresHighEnergy()
    {
        return $this->energy_level === 'high';
    }

    public function requiresLowEnergy()
    {
        return $this->energy_level === 'low';
    }

    public function canBeStarted()
    {
        return $this->status === 'pending' && !$this->is_overdue;
    }

    public function needsAttention()
    {
        return $this->is_overdue || $this->is_due_soon;
    }

    public function getProgressSummary()
    {
        $totalSubtasks = $this->getTotalSubtasksCount();
        $completedSubtasks = $this->getCompletedSubtasksCount();

        return [
            'total' => $totalSubtasks,
            'completed' => $completedSubtasks,
            'pending' => $totalSubtasks - $completedSubtasks,
            'percentage' => $totalSubtasks > 0 ? round(($completedSubtasks / $totalSubtasks) * 100) : 0,
        ];
    }

    public function attachTag($tagId)
    {
        if (!$this->tags()->where('tag_id', $tagId)->exists()) {
            $this->tags()->attach($tagId);
        }
    }

    public function detachTag($tagId)
    {
        $this->tags()->detach($tagId);
    }

    public function syncTags($tagIds)
    {
        $this->tags()->sync($tagIds);
    }

    public function getTagNames()
    {
        return $this->tags()->pluck('name')->toArray();
    }
}
