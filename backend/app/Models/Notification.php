<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'scheduled_at',
        'sent_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeReminders($query)
    {
        return $query->where('type', 'reminder');
    }

    public function scopeAchievements($query)
    {
        return $query->where('type', 'achievement');
    }

    public function scopeMotivational($query)
    {
        return $query->where('type', 'motivational');
    }

    public function scopeSystem($query)
    {
        return $query->where('type', 'system');
    }

    public function scopeScheduled($query)
    {
        return $query->where('scheduled_at', '<=', now())
                    ->whereNull('sent_at');
    }

    public function scopeSent($query)
    {
        return $query->whereNotNull('sent_at');
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessors
    public function getStatusAttribute()
    {
        if ($this->sent_at) {
            return 'sent';
        }

        if ($this->scheduled_at && $this->scheduled_at->isFuture()) {
            return 'scheduled';
        }

        if ($this->scheduled_at && $this->scheduled_at->isPast()) {
            return 'overdue';
        }

        return 'pending';
    }

    public function getFormattedTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getActionDataAttribute()
    {
        $data = $this->data ?: [];

        // Extract actionable data for frontend
        return [
            'action_type' => $data['action_type'] ?? 'none',
            'task_id' => $data['task_id'] ?? null,
            'project_id' => $data['project_id'] ?? null,
            'url' => $data['url'] ?? null,
            'button_text' => $data['button_text'] ?? null,
        ];
    }

    // Helper methods
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    public function markAsSent()
    {
        $this->update(['sent_at' => now()]);
    }

    public function schedule($datetime)
    {
        $this->update(['scheduled_at' => $datetime]);
    }

    public function isOverdue()
    {
        return $this->scheduled_at &&
               $this->scheduled_at->isPast() &&
               !$this->sent_at;
    }

    public function canBeSent()
    {
        return $this->scheduled_at &&
               $this->scheduled_at->isPast() &&
               !$this->sent_at;
    }

    public function getDisplayMessage()
    {
        // Logic để lấy message theo ngôn ngữ nếu cần
        $locale = app()->getLocale();
        $data = $this->data ?: [];

        if (isset($data["message_{$locale}"])) {
            return $data["message_{$locale}"];
        }

        return $this->message;
    }

    public function getDisplayTitle()
    {
        $locale = app()->getLocale();
        $data = $this->data ?: [];

        if (isset($data["title_{$locale}"])) {
            return $data["title_{$locale}"];
        }

        return $this->title;
    }

    public function getTypeDisplayAttribute()
    {
        return match($this->type) {
            'reminder' => 'リマインダー',
            'achievement' => '達成',
            'motivational' => 'モチベーション',
            'system' => 'システム',
            default => '不明',
        };
    }
}
