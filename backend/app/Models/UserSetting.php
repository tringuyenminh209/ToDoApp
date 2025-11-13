<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        // Appearance
        'theme',
        // Pomodoro Settings
        'default_focus_minutes',
        'pomodoro_duration',
        'break_minutes',
        'long_break_minutes',
        'auto_start_break',
        'block_notifications',
        'background_sound',
        // Daily Goals
        'daily_target_tasks',
        // Notifications
        'notification_enabled',
        'push_notifications',
        'daily_reminders',
        'goal_reminders',
        'reminder_times',
        // Localization
        'language',
        'timezone',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'auto_start_break' => 'boolean',
        'notification_enabled' => 'boolean',
        'push_notifications' => 'boolean',
        'daily_reminders' => 'boolean',
        'goal_reminders' => 'boolean',
        'block_notifications' => 'boolean',
        'background_sound' => 'boolean',
        'reminder_times' => 'array',
        'default_focus_minutes' => 'integer',
        'pomodoro_duration' => 'integer',
        'break_minutes' => 'integer',
        'long_break_minutes' => 'integer',
        'daily_target_tasks' => 'integer',
    ];

    /**
     * Get the user that owns the settings.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get default settings for a new user
     *
     * @return array
     */
    public static function getDefaultSettings(): array
    {
        return [
            'theme' => 'auto',
            'default_focus_minutes' => 25,
            'pomodoro_duration' => 25,
            'break_minutes' => 5,
            'long_break_minutes' => 15,
            'auto_start_break' => false,
            'block_notifications' => true,
            'background_sound' => false,
            'daily_target_tasks' => 3,
            'notification_enabled' => true,
            'push_notifications' => true,
            'daily_reminders' => true,
            'goal_reminders' => false,
            'reminder_times' => ['09:00', '18:00'],
            'language' => 'vi',
            'timezone' => 'Asia/Ho_Chi_Minh',
        ];
    }
}
