<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subtask extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'title',
        'is_completed',
        'estimated_minutes',
        'sort_order',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'estimated_minutes' => 'integer',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_completed', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeByTask($query, $taskId)
    {
        return $query->where('task_id', $taskId);
    }

    public function scopeWithEstimatedTime($query)
    {
        return $query->whereNotNull('estimated_minutes');
    }

    public function scopeWithoutEstimatedTime($query)
    {
        return $query->whereNull('estimated_minutes');
    }

    // Helper methods
    public function markAsCompleted()
    {
        $this->update(['is_completed' => true]);
    }

    public function markAsPending()
    {
        $this->update(['is_completed' => false]);
    }

    public function toggleCompletion()
    {
        $this->update(['is_completed' => !$this->is_completed]);
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

    public function isCompleted()
    {
        return $this->is_completed;
    }

    public function isPending()
    {
        return !$this->is_completed;
    }
}
