package ecccomp.s2240788.mobile_android.data.models

import com.google.gson.annotations.SerializedName

data class SettingsRequest(
    // Appearance
    val theme: String? = null,

    // Pomodoro Settings
    @SerializedName("default_focus_minutes")
    val defaultFocusMinutes: Int? = null,

    @SerializedName("pomodoro_duration")
    val pomodoroDuration: Int? = null,

    @SerializedName("break_minutes")
    val breakMinutes: Int? = null,

    @SerializedName("long_break_minutes")
    val longBreakMinutes: Int? = null,

    @SerializedName("auto_start_break")
    val autoStartBreak: Boolean? = null,

    @SerializedName("block_notifications")
    val blockNotifications: Boolean? = null,

    @SerializedName("background_sound")
    val backgroundSound: Boolean? = null,

    // Daily Goals
    @SerializedName("daily_target_tasks")
    val dailyTargetTasks: Int? = null,

    // Notifications
    @SerializedName("notification_enabled")
    val notificationEnabled: Boolean? = null,

    @SerializedName("push_notifications")
    val pushNotifications: Boolean? = null,

    @SerializedName("daily_reminders")
    val dailyReminders: Boolean? = null,

    @SerializedName("goal_reminders")
    val goalReminders: Boolean? = null,

    @SerializedName("reminder_times")
    val reminderTimes: List<String>? = null,

    // Localization
    val language: String? = null,
    val timezone: String? = null
)
