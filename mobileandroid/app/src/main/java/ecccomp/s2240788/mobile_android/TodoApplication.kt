package ecccomp.s2240788.mobile_android

import android.app.Application
import androidx.work.ExistingPeriodicWorkPolicy
import androidx.work.PeriodicWorkRequestBuilder
import androidx.work.WorkManager
// Firebaseは使用しないためコメントアウト
// import ecccomp.s2240788.mobile_android.utils.FCMTokenHelper
import ecccomp.s2240788.mobile_android.utils.LocaleHelper
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import ecccomp.s2240788.mobile_android.utils.NotificationHelper
import ecccomp.s2240788.mobile_android.utils.TokenManager
import ecccomp.s2240788.mobile_android.workers.NotificationSyncWorker
import java.util.concurrent.TimeUnit

/**
 * Application class for TodoApp
 * Initializes TokenManager, Notification Channels, and Background Workers
 */
class TodoApplication : Application() {

    override fun onCreate() {
        super.onCreate()

        // Apply saved locale
        LocaleHelper.applyLocale(this)

        // TokenManagerを初期化（EncryptedSharedPreferencesを使用するため）
        TokenManager.init(this)

        // NetworkModuleにContextを設定（ResponseInterceptorで使用）
        NetworkModule.setContext(this)

        // Initialize notification channels (Android 8.0+)
        NotificationHelper.createNotificationChannels(this)

        // Firebaseは使用しないためコメントアウト
        // Initialize FCM and get token
        // initializeFCM()

        // Schedule periodic notification sync worker (every 30 minutes)
        scheduleNotificationSyncWorker()
    }

    /**
     * Initialize Firebase Cloud Messaging
     * Get FCM token and send to backend if user is logged in
     * Firebaseは使用しないためコメントアウト
     */
    /*
    private fun initializeFCM() {
        // Send token to backend if user is already logged in
        // (e.g., app restarted with valid token)
        if (TokenManager.isTokenValid()) {
            FCMTokenHelper.sendTokenToServer(this)
        }
    }
    */

    private fun scheduleNotificationSyncWorker() {
        val syncWorkRequest = PeriodicWorkRequestBuilder<NotificationSyncWorker>(
            30, TimeUnit.MINUTES
        ).build()

        WorkManager.getInstance(this).enqueueUniquePeriodicWork(
            "notification_sync",
            ExistingPeriodicWorkPolicy.KEEP,
            syncWorkRequest
        )
    }
}

