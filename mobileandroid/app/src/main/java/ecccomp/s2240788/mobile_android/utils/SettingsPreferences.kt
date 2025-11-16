package ecccomp.s2240788.mobile_android.utils

import android.content.Context
import android.content.SharedPreferences
import com.google.gson.Gson
import ecccomp.s2240788.mobile_android.data.models.UserSettings

/**
 * Helper class to manage settings locally using SharedPreferences
 */
object SettingsPreferences {

    private const val PREF_NAME = "settings_prefs"
    private const val KEY_SETTINGS = "user_settings"
    private const val KEY_LAST_SYNC = "last_sync_time"

    private val gson = Gson()

    private fun getPreferences(context: Context): SharedPreferences {
        return context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE)
    }

    /**
     * Save settings locally
     */
    fun saveSettings(context: Context, settings: UserSettings) {
        val json = gson.toJson(settings)
        getPreferences(context).edit()
            .putString(KEY_SETTINGS, json)
            .putLong(KEY_LAST_SYNC, System.currentTimeMillis())
            .apply()
    }

    /**
     * Get settings from local storage
     */
    fun getSettings(context: Context): UserSettings? {
        val json = getPreferences(context).getString(KEY_SETTINGS, null)
        return if (json != null) {
            try {
                gson.fromJson(json, UserSettings::class.java)
            } catch (e: Exception) {
                null
            }
        } else {
            null
        }
    }

    /**
     * Get default settings
     */
    fun getDefaultSettings(): UserSettings {
        return UserSettings(
            theme = "auto",
            defaultFocusMinutes = 25,
            pomodoroDuration = 25,
            breakMinutes = 5,
            longBreakMinutes = 15,
            autoStartBreak = false,
            blockNotifications = true,
            backgroundSound = false,
            dailyTargetTasks = 3,
            notificationEnabled = true,
            pushNotifications = true,
            dailyReminders = true,
            goalReminders = false,
            reminderTimes = listOf("09:00", "18:00"),
            language = "vi",
            timezone = "Asia/Ho_Chi_Minh"
        )
    }

    /**
     * Get settings or default if not available
     */
    fun getSettingsOrDefault(context: Context): UserSettings {
        return getSettings(context) ?: getDefaultSettings()
    }

    /**
     * Clear all settings
     */
    fun clearSettings(context: Context) {
        getPreferences(context).edit()
            .remove(KEY_SETTINGS)
            .remove(KEY_LAST_SYNC)
            .apply()
    }

    /**
     * Get last sync time
     */
    fun getLastSyncTime(context: Context): Long {
        return getPreferences(context).getLong(KEY_LAST_SYNC, 0L)
    }

    /**
     * Check if settings need sync (older than 1 hour)
     */
    fun needsSync(context: Context): Boolean {
        val lastSync = getLastSyncTime(context)
        val oneHour = 60 * 60 * 1000L
        return (System.currentTimeMillis() - lastSync) > oneHour
    }

    // Individual setting getters for quick access
    fun getTheme(context: Context): String {
        return getSettings(context)?.theme ?: "auto"
    }

    fun getLanguage(context: Context): String {
        return getSettings(context)?.language ?: "vi"
    }

    fun getPomodoroDuration(context: Context): Int {
        return getSettings(context)?.pomodoroDuration ?: 25
    }

    fun getBreakMinutes(context: Context): Int {
        return getSettings(context)?.breakMinutes ?: 5
    }

    fun getDailyTargetTasks(context: Context): Int {
        return getSettings(context)?.dailyTargetTasks ?: 3
    }

    fun isPushNotificationsEnabled(context: Context): Boolean {
        return getSettings(context)?.pushNotifications ?: true
    }

    fun isDailyRemindersEnabled(context: Context): Boolean {
        return getSettings(context)?.dailyReminders ?: true
    }

    fun isGoalRemindersEnabled(context: Context): Boolean {
        return getSettings(context)?.goalReminders ?: false
    }

    fun isBlockNotificationsEnabled(context: Context): Boolean {
        return getSettings(context)?.blockNotifications ?: true
    }

    fun isBackgroundSoundEnabled(context: Context): Boolean {
        return getSettings(context)?.backgroundSound ?: false
    }
}
