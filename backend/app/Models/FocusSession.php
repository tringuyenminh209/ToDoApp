<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FocusSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'task_id',
        'session_type',
        'duration_minutes',
        'actual_minutes',
        'started_at',
        'ended_at',
        'status',
        'notes',
        'quality_score',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'actual_minutes' => 'integer',
        'quality_score' => 'integer',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('session_type', $type);
    }

    public function scopeWork($query)
    {
        return $query->where('session_type', 'work');
    }

    public function scopeBreak($query)
    {
        return $query->whereIn('session_type', ['break', 'long_break']);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('started_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('started_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    // Accessors
    public function getDurationFormattedAttribute()
    {
        $minutes = $this->actual_minutes ?: $this->duration_minutes;
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return "{$hours}h {$mins}m";
        }
        return "{$mins}m";
    }

    public function getEfficiencyAttribute()
    {
        if (!$this->actual_minutes || !$this->duration_minutes) {
            return null;
        }

        return round(($this->actual_minutes / $this->duration_minutes) * 100);
    }

    public function isValidSessionAttribute()
    {
        return $this->started_at &&
               ($this->ended_at ?: now()) > $this->started_at &&
               $this->duration_minutes > 0;
    }

    // Helper methods
    public function start()
    {
        $this->update([
            'started_at' => now(),
            'status' => 'completed'
        ]);
    }

    public function stop()
    {
        $this->update([
            'ended_at' => now(),
            'actual_minutes' => $this->started_at ? $this->started_at->diffInMinutes(now()) : 0
        ]);
    }

    public function pause()
    {
        $this->update(['status' => 'paused']);
    }

    public function resume()
    {
        $this->update(['status' => 'completed']);
    }

    public function getProductivityScore()
    {
        $base = $this->quality_score > 0 ? $this->quality_score * 20 : 50;
        $efficiency = $this->efficiency ?: 100;
        $duration = min($this->duration_minutes / 25, 1) * 10; // Punishment for shorter sessions

        return min(100, $base + ($efficiency / 10) + $duration);
    }
}
