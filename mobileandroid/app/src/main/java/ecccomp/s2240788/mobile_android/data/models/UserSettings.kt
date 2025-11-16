package ecccomp.s2240788.mobile_android.data.models

import com.google.gson.annotations.SerializedName

data class UserSettings(
    val id: Int? = null,

    @SerializedName("user_id")
    val userId: Int? = null,

    // Appearance
    val theme: String = "auto", // light, dark, auto

    // Pomodoro Settings
    @SerializedName("default_focus_minutes")
    val defaultFocusMinutes: Int = 25,

    @SerializedName("pomodoro_duration")
    val pomodoroDuration: Int = 25,

    @SerializedName("break_minutes")
    val breakMinutes: Int = 5,

    @SerializedName("long_break_minutes")
    val longBreakMinutes: Int = 15,

    @SerializedName("auto_start_break")
    val autoStartBreak: Boolean = false,

    @SerializedName("block_notifications")
    val blockNotifications: Boolean = true,

    @SerializedName("background_sound")
    val backgroundSound: Boolean = false,

    // Daily Goals
    @SerializedName("daily_target_tasks")
    val dailyTargetTasks: Int = 3,

    // Notifications
    @SerializedName("notification_enabled")
    val notificationEnabled: Boolean = true,

    @SerializedName("push_notifications")
    val pushNotifications: Boolean = true,

    @SerializedName("daily_reminders")
    val dailyReminders: Boolean = true,

    @SerializedName("goal_reminders")
    val goalReminders: Boolean = false,

    @SerializedName("reminder_times")
    val reminderTimes: List<String> = listOf("09:00", "18:00"),

    // Localization
    val language: String = "vi", // vi, en, ja
    val timezone: String = "Asia/Ho_Chi_Minh",

    @SerializedName("created_at")
    val createdAt: String? = null,

    @SerializedName("updated_at")
    val updatedAt: String? = null
)
