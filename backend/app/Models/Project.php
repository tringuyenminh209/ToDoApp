<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name_en',
        'name_ja',
        'description_en',
        'description_ja',
        'status',
        'start_date',
        'end_date',
        'progress_percentage',
        'color',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'progress_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
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

    public function scopeCompleted($query)
    {
        return $query->where('progress_percentage', 100);
    }

    public function scopeOverdue($query)
    {
        return $query->where('end_date', '<', now())
                    ->where('progress_percentage', '<', 100);
    }

    // Accessors
    public function getStatusDisplayAttribute()
    {
        return match($this->status) {
            'active' => 'アクティブ',
            'completed' => '完了',
            'paused' => '一時停止',
            'cancelled' => 'キャンセル',
            default => '不明',
        };
    }

    public function getDaysRemainingAttribute()
    {
        if (!$this->end_date) return null;

        return now()->diffInDays($this->end_date, false);
    }

    public function getIsOverdueAttribute()
    {
        return $this->end_date &&
               $this->end_date->isPast() &&
               $this->progress_percentage < 100;
    }

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

    // Helper methods
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
        $this->update(['progress_percentage' => $progress]);

        return $progress;
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

    public function activate()
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    public function getDisplayName($locale = 'en')
    {
        return $locale === 'ja' ? $this->name_ja : $this->name_en;
    }

    public function getDisplayDescription($locale = 'en')
    {
        return $locale === 'ja' ? $this->description_ja : $this->description_en;
    }
}
