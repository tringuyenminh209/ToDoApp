<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimetableStudy extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'timetable_class_id',
        'title',
        'description',
        'type',
        'subject',
        'due_date',
        'priority',
        'status',
        'completed_at',
        'task_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'priority' => 'integer',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function timetableClass(): BelongsTo
    {
        return $this->belongsTo(TimetableClass::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    // Scopes
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
        return $query->where('status', '!=', 'completed')
                     ->where('due_date', '<', now()->toDateString());
    }

    public function scopeDueSoon($query, $days = 3)
    {
        return $query->where('status', '!=', 'completed')
                     ->whereBetween('due_date', [
                         now()->toDateString(),
                         now()->addDays($days)->toDateString()
                     ]);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessors
    public function getTypeDisplayAttribute()
    {
        return match($this->type) {
            'homework' => '宿題',
            'review' => '復習',
            'exam' => '試験',
            'project' => 'プロジェクト',
            default => '不明',
        };
    }

    public function getStatusDisplayAttribute()
    {
        return match($this->status) {
            'pending' => '未着手',
            'in_progress' => '進行中',
            'completed' => '完了',
            default => '不明',
        };
    }

    public function getIsOverdueAttribute()
    {
        return $this->status !== 'completed' &&
               $this->due_date &&
               $this->due_date->isPast();
    }

    public function getDaysUntilDueAttribute()
    {
        if (!$this->due_date) return null;
        return now()->diffInDays($this->due_date, false);
    }

    // Helper methods
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function toggleStatus()
    {
        if ($this->status === 'completed') {
            $this->update([
                'status' => 'pending',
                'completed_at' => null,
            ]);
        } else {
            $this->markAsCompleted();
        }
    }
}

