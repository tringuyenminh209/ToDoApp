<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'goal_type',
        'preferred_time',
        'notification_enabled',
        'onboarding_completed',
    ];

    protected $casts = [
        'notification_enabled' => 'boolean',
        'onboarding_completed' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeCompletedOnboarding($query)
    {
        return $query->where('onboarding_completed', true);
    }

    public function scopePendingOnboarding($query)
    {
        return $query->where('onboarding_completed', false);
    }

    public function scopeByGoalType($query, $goalType)
    {
        return $query->where('goal_type', $goalType);
    }

    public function scopeWithNotifications($query)
    {
        return $query->where('notification_enabled', true);
    }

    // Accessors
    public function getGoalTypeDisplayAttribute()
    {
        return match($this->goal_type) {
            'learning' => '学習',
            'work' => '仕事',
            'health' => '健康',
            default => '不明',
        };
    }

    public function getPreferredTimeDisplayAttribute()
    {
        return match($this->preferred_time) {
            'morning' => '朝',
            'morning_late' => '午前遅め',
            'afternoon' => '午後',
            'evening' => '夜',
            default => '不明',
        };
    }

    public function getOnboardingStatusAttribute()
    {
        return $this->onboarding_completed ? '完了' : '未完了';
    }

    public function getNotificationStatusAttribute()
    {
        return $this->notification_enabled ? '有効' : '無効';
    }

    // Helper methods
    public function markOnboardingCompleted()
    {
        $this->update(['onboarding_completed' => true]);
    }

    public function markOnboardingPending()
    {
        $this->update(['onboarding_completed' => false]);
    }

    public function toggleNotifications()
    {
        $this->update(['notification_enabled' => !$this->notification_enabled]);
    }

    public function enableNotifications()
    {
        $this->update(['notification_enabled' => true]);
    }

    public function disableNotifications()
    {
        $this->update(['notification_enabled' => false]);
    }

    public function updateGoalType($goalType)
    {
        $this->update(['goal_type' => $goalType]);
    }

    public function updatePreferredTime($preferredTime)
    {
        $this->update(['preferred_time' => $preferredTime]);
    }

    public function isOnboardingCompleted()
    {
        return $this->onboarding_completed;
    }

    public function isOnboardingPending()
    {
        return !$this->onboarding_completed;
    }

    public function hasNotificationsEnabled()
    {
        return $this->notification_enabled;
    }

    public function hasNotificationsDisabled()
    {
        return !$this->notification_enabled;
    }

    public function hasGoalType()
    {
        return !is_null($this->goal_type);
    }

    public function hasPreferredTime()
    {
        return !is_null($this->preferred_time);
    }

    public function getProfileCompletionPercentage()
    {
        $totalFields = 4; // goal_type, preferred_time, notification_enabled, onboarding_completed
        $completedFields = 0;

        if ($this->hasGoalType()) $completedFields++;
        if ($this->hasPreferredTime()) $completedFields++;
        if ($this->hasNotificationsEnabled()) $completedFields++;
        if ($this->isOnboardingCompleted()) $completedFields++;

        return round(($completedFields / $totalFields) * 100);
    }

    public function getProfileSummary()
    {
        return [
            'goal_type' => $this->goal_type,
            'goal_type_display' => $this->goal_type_display,
            'preferred_time' => $this->preferred_time,
            'preferred_time_display' => $this->preferred_time_display,
            'notification_enabled' => $this->notification_enabled,
            'notification_status' => $this->notification_status,
            'onboarding_completed' => $this->onboarding_completed,
            'onboarding_status' => $this->onboarding_status,
            'completion_percentage' => $this->getProfileCompletionPercentage(),
        ];
    }
}
