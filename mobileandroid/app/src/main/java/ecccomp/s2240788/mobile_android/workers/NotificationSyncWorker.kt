package ecccomp.s2240788.mobile_android.workers

import android.content.Context
import android.util.Log
import androidx.work.CoroutineWorker
import androidx.work.WorkerParameters
import ecccomp.s2240788.mobile_android.data.repository.NotificationRepository
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import ecccomp.s2240788.mobile_android.utils.NotificationHelper

/**
 * NotificationSyncWorker
 * Syncs notifications from backend and displays system notifications
 * Runs every 30 minutes in background
 */
class NotificationSyncWorker(
    context: Context,
    params: WorkerParameters
) : CoroutineWorker(context, params) {

    companion object {
        private const val TAG = "NotificationSyncWorker"
    }

    override suspend fun doWork(): Result {
        return try {
            // Initialize repository
            val apiService = NetworkModule.provideApiService(
                NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
            )
            val repository = NotificationRepository(apiService)

            // Fetch recent notifications
            Log.d(TAG, "Fetching recent notifications")
            val result = repository.getRecentNotifications()

            if (result.isSuccess) {
                result.fold(
                    onSuccess = { notifications ->
                        Log.d(TAG, "Fetched ${notifications.size} notifications")

                        // Show system notifications for unread items
                        notifications.filter { !it.read }.take(3).forEach { notification ->
                            NotificationHelper.showNotification(
                                context = applicationContext,
                                id = notification.id,
                                title = notification.title,
                                message = notification.message,
                                taskId = notification.task_id
                            )
                        }

                        Log.d(TAG, "Notification sync completed successfully")
                    },
                    onError = { error ->
                        Log.e(TAG, "Failed to fetch notifications: $error")
                    }
                )
                Result.success()
            } else {
                Log.e(TAG, "Failed to sync notifications")
                Result.retry()
            }
        } catch (e: Exception) {
            Log.e(TAG, "Exception in notification sync worker", e)
            Result.retry()
        }
    }
}
