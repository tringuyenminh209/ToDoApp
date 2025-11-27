package ecccomp.s2240788.mobile_android.services

import android.app.NotificationManager
import android.content.Context
import android.content.Intent
import android.util.Log
import androidx.core.app.NotificationCompat
import com.google.firebase.messaging.FirebaseMessagingService
import com.google.firebase.messaging.RemoteMessage
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.ui.activities.MainActivity
import ecccomp.s2240788.mobile_android.ui.activities.NotificationActivity
import ecccomp.s2240788.mobile_android.ui.activities.TaskDetailActivity
import ecccomp.s2240788.mobile_android.utils.FCMTokenHelper
import ecccomp.s2240788.mobile_android.utils.NotificationHelper

/**
 * FirebaseMessagingService
 * Handles incoming FCM push notifications
 */
class MyFirebaseMessagingService : FirebaseMessagingService() {

    companion object {
        private const val TAG = "FCMService"
    }

    /**
     * Called when a new FCM token is generated
     * Send this token to backend server
     */
    override fun onNewToken(token: String) {
        super.onNewToken(token)
        Log.d(TAG, "New FCM token: $token")
        
        // Send token to backend using helper
        // Helper will check if user is logged in before sending
        FCMTokenHelper.sendTokenToServer(this)
    }

    /**
     * Called when a message is received
     * Handle both data and notification payloads
     */
    override fun onMessageReceived(remoteMessage: RemoteMessage) {
        super.onMessageReceived(remoteMessage)
        Log.d(TAG, "Message received from: ${remoteMessage.from}")

        // Check if message contains data payload
        if (remoteMessage.data.isNotEmpty()) {
            Log.d(TAG, "Message data payload: ${remoteMessage.data}")
            handleDataMessage(remoteMessage.data)
        }

        // Check if message contains notification payload
        remoteMessage.notification?.let { notification ->
            Log.d(TAG, "Message notification body: ${notification.body}")
            showNotification(
                title = notification.title ?: "通知",
                message = notification.body ?: "",
                data = remoteMessage.data
            )
        }
    }

    /**
     * Handle data-only messages (when app is in foreground)
     */
    private fun handleDataMessage(data: Map<String, String>) {
        val title = data["title"] ?: "通知"
        val message = data["message"] ?: ""
        
        showNotification(title, message, data)
    }

    /**
     * Show notification to user
     */
    private fun showNotification(
        title: String,
        message: String,
        data: Map<String, String>
    ) {
        val taskId = data["task_id"]?.toIntOrNull()
        val notificationId = data["notification_id"]?.toIntOrNull() ?: System.currentTimeMillis().toInt()
        val type = data["type"] ?: "system"
        val actionType = data["action_type"]

        // Create intent based on action type
        val intent = when (actionType) {
            "task_reminder", "deadline_reminder", "overdue_task", "task_abandoned" -> {
                taskId?.let {
                    Intent(this, TaskDetailActivity::class.java).apply {
                        putExtra("task_id", it)
                        flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TOP
                    }
                } ?: Intent(this, MainActivity::class.java).apply {
                    flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TOP
                }
            }
            else -> {
                Intent(this, NotificationActivity::class.java).apply {
                    flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TOP
                }
            }
        }

        // Use NotificationHelper to show notification
        NotificationHelper.showNotification(
            context = this,
            id = notificationId,
            title = title,
            message = message,
            taskId = taskId,
            type = type
        )
    }

}

