<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FocusEnvironment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'focus_session_id',
        'quiet_space',
        'phone_silent',
        'materials_ready',
        'water_coffee_ready',
        'comfortable_position',
        'notifications_off',
        'apps_closed',
        'all_checks_passed',
        'notes',
    ];

    protected $casts = [
        'quiet_space' => 'boolean',
        'phone_silent' => 'boolean',
        'materials_ready' => 'boolean',
        'water_coffee_ready' => 'boolean',
        'comfortable_position' => 'boolean',
        'notifications_off' => 'boolean',
        'all_checks_passed' => 'boolean',
        'apps_closed' => 'array',
    ];

    // Relationships
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function focusSession(): BelongsTo
    {
        return $this->belongsTo(FocusSession::class);
    }

    // Helper methods
    public function checkIfAllPassed(): bool
    {
        return $this->quiet_space &&
               $this->phone_silent &&
               $this->materials_ready &&
               $this->water_coffee_ready &&
               $this->comfortable_position &&
               $this->notifications_off;
    }

    public function updateAllChecksStatus(): void
    {
        $this->all_checks_passed = $this->checkIfAllPassed();
        $this->save();
    }

    public function getCompletionPercentage(): int
    {
        $checks = [
            $this->quiet_space,
            $this->phone_silent,
            $this->materials_ready,
            $this->water_coffee_ready,
            $this->comfortable_position,
            $this->notifications_off,
        ];

        $passedChecks = count(array_filter($checks));
        return round(($passedChecks / count($checks)) * 100);
    }

    public function getPendingChecks(): array
    {
        $pending = [];

        if (!$this->quiet_space) $pending[] = 'quiet_space';
        if (!$this->phone_silent) $pending[] = 'phone_silent';
        if (!$this->materials_ready) $pending[] = 'materials_ready';
        if (!$this->water_coffee_ready) $pending[] = 'water_coffee_ready';
        if (!$this->comfortable_position) $pending[] = 'comfortable_position';
        if (!$this->notifications_off) $pending[] = 'notifications_off';

        return $pending;
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByTask($query, $taskId)
    {
        return $query->where('task_id', $taskId);
    }

    public function scopeAllPassed($query)
    {
        return $query->where('all_checks_passed', true);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
