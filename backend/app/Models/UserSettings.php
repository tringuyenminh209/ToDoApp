<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'theme',
        'default_focus_minutes',
        'break_minutes',
        'long_break_minutes',
        'auto_start_break',
        'daily_target_tasks',
        'notification_enabled',
        'reminder_times',
        'language',
        'timezone',
    ];

    protected $casts = [
        'default_focus_minutes' => 'integer',
        'break_minutes' => 'integer',
        'long_break_minutes' => 'integer',
        'auto_start_break' => 'boolean',
        'daily_target_tasks' => 'integer',
        'notification_enabled' => 'boolean',
        'reminder_times' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByTheme($query, $theme)
    {
        return $query->where('theme', $theme);
    }

    public function scopeWithNotifications($query)
    {
        return $query->where('notification_enabled', true);
    }

    public function scopeByLanguage($query, $language)
    {
        return $query->where('language', $language);
    }

    public function scopeByTimezone($query, $timezone)
    {
        return $query->where('timezone', $timezone);
    }

    public function scopeWithReminders($query)
    {
        return $query->whereNotNull('reminder_times');
    }

    public function scopeWithoutReminders($query)
    {
        return $query->whereNull('reminder_times');
    }

    // Accessors
    public function getThemeDisplayAttribute()
    {
        return match($this->theme) {
            'light' => 'ライト',
            'dark' => 'ダーク',
            'auto' => '自動',
            default => '不明',
        };
    }

    public function getLanguageDisplayAttribute()
    {
        return match($this->language) {
            'vi' => 'Tiếng Việt',
            'en' => 'English',
            'ja' => '日本語',
            default => 'Unknown',
        };
    }

    public function getEffectiveThemeAttribute()
    {
        if ($this->theme === 'auto') {
            return now()->hour >= 18 ? 'dark' : 'light';
        }
        return $this->theme;
    }

    public function getFormattedReminderTimesAttribute()
    {
        if (!$this->reminder_times) {
            return [];
        }

        return array_map(function($time) {
            return date('H:i', strtotime($time));
        }, $this->reminder_times);
    }

    public function getPomodoroCycleAttribute()
    {
        return [
            'focus' => $this->default_focus_minutes,
            'short_break' => $this->break_minutes,
            'long_break' => $this->long_break_minutes,
            'auto_break' => $this->auto_start_break,
        ];
    }

    // Helper methods
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

    public function updateTheme($theme)
    {
        $this->update(['theme' => $theme]);
    }

    public function updateLanguage($language)
    {
        $this->update(['language' => $language]);
    }

    public function updateTimezone($timezone)
    {
        $this->update(['timezone' => $timezone]);
    }

    public function updatePomodoroSettings($focusMinutes, $breakMinutes, $longBreakMinutes, $autoStartBreak = false)
    {
        $this->update([
            'default_focus_minutes' => $focusMinutes,
            'break_minutes' => $breakMinutes,
            'long_break_minutes' => $longBreakMinutes,
            'auto_start_break' => $autoStartBreak,
        ]);
    }

    public function updateDailyTarget($targetTasks)
    {
        $this->update(['daily_target_tasks' => $targetTasks]);
    }

    public function setReminderTimes($times)
    {
        $this->update(['reminder_times' => $times]);
    }

    public function addReminderTime($time)
    {
        $reminderTimes = $this->reminder_times ?: [];
        if (!in_array($time, $reminderTimes)) {
            $reminderTimes[] = $time;
            $this->update(['reminder_times' => $reminderTimes]);
        }
    }

    public function removeReminderTime($time)
    {
        $reminderTimes = $this->reminder_times ?: [];
        $reminderTimes = array_filter($reminderTimes, function($t) use ($time) {
            return $t !== $time;
        });
        $this->update(['reminder_times' => array_values($reminderTimes)]);
    }

    public function hasNotificationsEnabled()
    {
        return $this->notification_enabled;
    }

    public function hasReminders()
    {
        return !is_null($this->reminder_times) && !empty($this->reminder_times);
    }

    public function isDarkTheme()
    {
        return $this->effective_theme === 'dark';
    }

    public function isLightTheme()
    {
        return $this->effective_theme === 'light';
    }

    public function isAutoTheme()
    {
        return $this->theme === 'auto';
    }

    public function getSettingsSummary()
    {
        return [
            'theme' => $this->theme,
            'theme_display' => $this->theme_display,
            'effective_theme' => $this->effective_theme,
            'language' => $this->language,
            'language_display' => $this->language_display,
            'timezone' => $this->timezone,
            'pomodoro_cycle' => $this->pomodoro_cycle,
            'daily_target_tasks' => $this->daily_target_tasks,
            'notification_enabled' => $this->notification_enabled,
            'reminder_times' => $this->reminder_times,
            'formatted_reminder_times' => $this->formatted_reminder_times,
        ];
    }
}
