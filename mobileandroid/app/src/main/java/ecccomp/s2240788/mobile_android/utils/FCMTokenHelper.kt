package ecccomp.s2240788.mobile_android.utils

import android.content.Context
import android.util.Log
import com.google.firebase.messaging.FirebaseMessaging
import ecccomp.s2240788.mobile_android.data.repository.FCMTokenRepository
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch

/**
 * FCM Token Helper
 * Utility class to manage FCM token sending to backend
 */
object FCMTokenHelper {
    private const val TAG = "FCMTokenHelper"

    /**
     * Get FCM token and send to backend
     * Should be called after user login
     */
    fun sendTokenToServer(context: Context) {
        // Check if user is logged in
        if (!TokenManager.isTokenValid()) {
            Log.d(TAG, "User not logged in, skipping FCM token send")
            return
        }

        FirebaseMessaging.getInstance().token.addOnCompleteListener { task ->
            if (!task.isSuccessful) {
                Log.w(TAG, "Failed to get FCM token", task.exception)
                return@addOnCompleteListener
            }

            val token = task.result
            Log.d(TAG, "FCM token retrieved: $token")

            // Send token to backend
            CoroutineScope(Dispatchers.IO).launch {
                try {
                    val apiService = NetworkModule.provideApiService(
                        NetworkModule.provideRetrofit(
                            NetworkModule.provideOkHttpClient()
                        )
                    )

                    val repository = FCMTokenRepository(apiService)
                    val result = repository.updateFCMToken(token)

                    result.fold(
                        onSuccess = {
                            Log.d(TAG, "FCM token sent to server successfully")
                        },
                        onError = { error ->
                            Log.e(TAG, "Failed to send FCM token to server: $error")
                        }
                    )
                } catch (e: Exception) {
                    Log.e(TAG, "Error sending FCM token: ${e.message}", e)
                }
            }
        }
    }

    /**
     * Get current FCM token (for debugging)
     */
    fun getCurrentToken(callback: (String?) -> Unit) {
        FirebaseMessaging.getInstance().token.addOnCompleteListener { task ->
            if (task.isSuccessful) {
                callback(task.result)
            } else {
                Log.w(TAG, "Failed to get FCM token", task.exception)
                callback(null)
            }
        }
    }
}

