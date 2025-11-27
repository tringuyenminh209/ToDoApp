package ecccomp.s2240788.mobile_android.utils

import android.app.NotificationChannel
import android.app.NotificationManager
import android.app.PendingIntent
import android.content.Context
import android.content.Intent
import android.os.Build
import androidx.core.app.NotificationCompat
import androidx.core.app.NotificationManagerCompat
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.ui.activities.MainActivity

/**
 * NotificationHelper
 * Utility class for showing system notifications
 * Handles notification channels and display logic
 */
object NotificationHelper {

    private const val CHANNEL_ID_DEFAULT = "kizamu_notifications"
    private const val CHANNEL_ID_REMINDERS = "kizamu_reminders"
    private const val CHANNEL_ID_ACHIEVEMENTS = "kizamu_achievements"
    private const val CHANNEL_NAME_DEFAULT = "Kizamu Notifications"
    private const val CHANNEL_NAME_REMINDERS = "Task Reminders"
    private const val CHANNEL_NAME_ACHIEVEMENTS = "Achievements"

    /**
     * Create notification channels (Android 8.0+)
     */
    fun createNotificationChannels(context: Context) {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val notificationManager = context.getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager

            // Default channel
            val defaultChannel = NotificationChannel(
                CHANNEL_ID_DEFAULT,
                CHANNEL_NAME_DEFAULT,
                NotificationManager.IMPORTANCE_DEFAULT
            ).apply {
                description = "General notifications from Kizamu"
            }

            // Reminders channel
            val remindersChannel = NotificationChannel(
                CHANNEL_ID_REMINDERS,
                CHANNEL_NAME_REMINDERS,
                NotificationManager.IMPORTANCE_HIGH
            ).apply {
                description = "Task and deadline reminders"
            }

            // Achievements channel
            val achievementsChannel = NotificationChannel(
                CHANNEL_ID_ACHIEVEMENTS,
                CHANNEL_NAME_ACHIEVEMENTS,
                NotificationManager.IMPORTANCE_DEFAULT
            ).apply {
                description = "Achievement and motivational notifications"
            }

            notificationManager.createNotificationChannel(defaultChannel)
            notificationManager.createNotificationChannel(remindersChannel)
            notificationManager.createNotificationChannel(achievementsChannel)
        }
    }

    /**
     * Show notification
     */
    fun showNotification(
        context: Context,
        id: Int,
        title: String,
        message: String,
        taskId: Int? = null,
        type: String = "system"
    ) {
        // Create intent to open app
        val intent = Intent(context, MainActivity::class.java).apply {
            flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
            taskId?.let {
                putExtra("task_id", it)
                putExtra("open_task_detail", true)
            }
        }

        val pendingIntent = PendingIntent.getActivity(
            context,
            id,
            intent,
            PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE
        )

        // Select channel based on type
        val channelId = when (type) {
            "reminder" -> CHANNEL_ID_REMINDERS
            "achievement", "motivational" -> CHANNEL_ID_ACHIEVEMENTS
            else -> CHANNEL_ID_DEFAULT
        }

        // Build notification
        val notification = NotificationCompat.Builder(context, channelId)
            .setSmallIcon(R.drawable.ic_notifications)
            .setContentTitle(title)
            .setContentText(message)
            .setStyle(NotificationCompat.BigTextStyle().bigText(message))
            .setPriority(NotificationCompat.PRIORITY_DEFAULT)
            .setContentIntent(pendingIntent)
            .setAutoCancel(true)
            .build()

        // Show notification
        with(NotificationManagerCompat.from(context)) {
            notify(id, notification)
        }
    }

    /**
     * Show abandoned task warning notification
     */
    fun showAbandonedTaskNotification(
        context: Context,
        taskId: Int,
        taskTitle: String
    ) {
        showNotification(
            context = context,
            id = 9000 + taskId, // Unique ID for abandoned tasks
            title = "Task Abandoned",
            message = "You've been away from \"$taskTitle\" for 15 minutes. The task has been marked as abandoned.",
            taskId = taskId,
            type = "reminder"
        )
    }

    /**
     * Cancel notification by ID
     */
    fun cancelNotification(context: Context, id: Int) {
        with(NotificationManagerCompat.from(context)) {
            cancel(id)
        }
    }

    /**
     * Cancel all notifications
     */
    fun cancelAllNotifications(context: Context) {
        with(NotificationManagerCompat.from(context)) {
            cancelAll()
        }
    }
}
