<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningMilestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'learning_path_id',
        'title',
        'description',
        'sort_order',
        'target_start_date',
        'target_end_date',
        'completed_at',
        'status',
        'progress_percentage',
        'estimated_hours',
        'actual_hours',
        'deliverables',
        'self_assessment',
        'notes',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'target_start_date' => 'date',
        'target_end_date' => 'date',
        'completed_at' => 'datetime',
        'progress_percentage' => 'decimal:2',
        'estimated_hours' => 'integer',
        'actual_hours' => 'integer',
        'deliverables' => 'array',
        'self_assessment' => 'integer',
    ];

    // Relationships
    public function learningPath(): BelongsTo
    {
        return $this->belongsTo(LearningPath::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeUpcoming($query, $days = 7)
    {
        return $query->where('status', 'pending')
                   ->whereBetween('target_start_date', [
                       now(),
                       now()->addDays($days)
                   ]);
    }

    public function scopeOverdue($query)
    {
        return $query->where('target_end_date', '<', now())
                    ->where('status', '!=', 'completed');
    }

    public function scopeHighPriority($query)
    {
        return $query->whereHas('tasks', function($q) {
            $q->where('priority', '>=', 4);
        });
    }

    // Accessors
    public function getCompletionRateAttribute()
    {
        $tasks = $this->tasks;
        if ($tasks->isEmpty()) {
            return $this->progress_percentage;
        }

        $completedTasks = $tasks->where('status', 'completed')->count();
        return $tasks->count() > 0
            ? round(($completedTasks / $tasks->count()) * 100, 2)
            : 0;
    }

    public function getEstimatedDurationAttribute()
    {
        if ($this->actual_hours > 0) {
            return $this->actual_hours;
        }

        $taskHours = $this->tasks->sum('estimated_minutes') / 60;
        return max($this->estimated_hours ?: 0, $taskHours);
    }

    public function getStatusDisplayAttribute()
    {
        return match($this->status) {
            'pending' => '待機中',
            'in_progress' => '進行中',
            'completed' => '完了',
            'skipped' => 'スキップ',
        };
    }

    public function getTimeUtilizationAttribute()
    {
        if (!$this->estimated_hours || $this->estimated_hours == 0) {
            return null;
        }

        return round(($this->actual_hours / $this->estimated_hours) * 100, 1);
    }

    public function getDaysForCompletionAttribute()
    {
        if ($this->target_end_date && !$this->completed_at) {
            return now()->diffInDays($this->target_end_date, false);
        }
        return null;
    }

    public function getIsOverdueAttribute()
    {
        return $this->target_end_date &&
               $this->target_end_date->isPast() &&
               $this->status !== 'completed';
    }

    public function getIsDueSoonAttribute()
    {
        return $this->target_end_date &&
               $this->target_end_date->isBefore(now()->addDays(3)) &&
               $this->status === 'in_progress';
    }

    // Helper methods
    public function markAsStarted()
    {
      $this->update(['status' => 'in_progress']);
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'progress_percentage' => 100,
        ]);

        // Auto-complete all associated tasks
        $this->tasks()->where('status', '!=', 'completed')->update([
            'status' => 'completed',
            'updated_at' => now(),
        ]);
    }

    public function calculateProgress()
    {
        $tasks = $this->tasks;
        if ($tasks->isEmpty()) {
            return $this->progress_percentage;
        }

        $totalWeight = 0;
        $weightedProgress = 0;

        foreach ($tasks as $task) {
            $weight = $task->priority * ($task->estimated_minutes ?: 30);
            $totalWeight += $weight;

            $taskProgress = match($task->status) {
                'completed' => 100,
                'in_progress' => 50,
                default => 0,
            };

            $weightedProgress += $taskProgress * $weight;
        }

        $progress = $totalWeight > 0 ? ($weightedProgress / $totalWeight) : 0;

        // Update progress_percentage
        $updateData = ['progress_percentage' => $progress];

        // Auto-update status based on progress
        if ($progress >= 100 && $this->status !== 'completed') {
            $updateData['status'] = 'completed';
            $updateData['completed_at'] = now();
        } elseif ($progress > 0 && $progress < 100 && $this->status === 'pending') {
            $updateData['status'] = 'in_progress';
        } elseif ($progress == 0 && $this->status !== 'pending' && $this->status !== 'completed') {
            $updateData['status'] = 'pending';
        }

        $this->update($updateData);

        return $progress;
    }

    public function generateTasks()
    {
        // AI logic sẽ được implement để tự động tạo tasks cho milestone
        $suggestions = [
            'research_basics' => '基本概念のリサーチ',
            'practice_exercises' => '練習問題を解く',
            'create_project' => 'ミニプロジェクトを作成',
            'review_material' => '教材の復習',
            'assess_progress' => '進捗の評価',
        ];

        return [
            'tasks' => $suggestions,
            'estimated_hours' => $this->estimated_hours,
            'recommendations' => "このマイルストーンを完了するために推奨されるタスクです。"
        ];
    }

    public function getRelatedKnowledgeItems()
    {
        return KnowledgeItem::where('category', 'like', "%{$this->title}%")
                          ->orWhere('title', 'like', "%{$this->title}%")
                          ->get();
    }

    public function canBeStarted()
    {
        return $this->status === 'pending' &&
               (!$this->target_start_date || $this->target_start_date->isPast());
    }

    public function needsAttention()
    {
        return $this->is_overdue || $this->is_due_soon || $this->time_utilization > 150;
    }
}
