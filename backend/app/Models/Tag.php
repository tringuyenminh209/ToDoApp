<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'icon',
    ];

    protected $casts = [
        // No special casts needed for current fields
    ];

    // Relationships
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_tags');
    }

    // Scopes
    public function scopeByColor($query, $color)
    {
        return $query->where('color', $color);
    }

    public function scopeWithIcon($query)
    {
        return $query->whereNotNull('icon');
    }

    public function scopeWithoutIcon($query)
    {
        return $query->whereNull('icon');
    }

    public function scopePopular($query)
    {
        return $query->withCount('tasks')
                    ->orderBy('tasks_count', 'desc');
    }

    public function scopeRecentlyUsed($query, $days = 30)
    {
        return $query->whereHas('tasks', function($q) use ($days) {
            $q->where('created_at', '>=', now()->subDays($days));
        });
    }

    // Accessors
    public function getUsageCountAttribute()
    {
        return $this->tasks()->count();
    }

    public function getIsPopularAttribute()
    {
        return $this->usage_count >= 5; // Consider popular if used in 5+ tasks
    }

    public function getDisplayNameAttribute()
    {
        return $this->name;
    }

    // Helper methods
    public function getTasksCount()
    {
        return $this->tasks()->count();
    }

    public function getRecentTasks($limit = 5)
    {
        return $this->tasks()
                   ->orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public function isUsedInTask($taskId)
    {
        return $this->tasks()->where('task_id', $taskId)->exists();
    }

    public function attachToTask($taskId)
    {
        if (!$this->isUsedInTask($taskId)) {
            $this->tasks()->attach($taskId);
        }
    }

    public function detachFromTask($taskId)
    {
        $this->tasks()->detach($taskId);
    }

    public function syncWithTasks($taskIds)
    {
        $this->tasks()->sync($taskIds);
    }

    public function getColorVariants()
    {
        return [
            'primary' => $this->color,
            'light' => $this->lightenColor($this->color, 0.3),
            'dark' => $this->darkenColor($this->color, 0.3),
        ];
    }

    private function lightenColor($hex, $amount)
    {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $r = min(255, $r + ($amount * 255));
        $g = min(255, $g + ($amount * 255));
        $b = min(255, $b + ($amount * 255));

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    private function darkenColor($hex, $amount)
    {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $r = max(0, $r - ($amount * 255));
        $g = max(0, $g - ($amount * 255));
        $b = max(0, $b - ($amount * 255));

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
}
