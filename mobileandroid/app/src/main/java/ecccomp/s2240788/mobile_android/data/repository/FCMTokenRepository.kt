package ecccomp.s2240788.mobile_android.data.repository

import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.ApiResponse
import ecccomp.s2240788.mobile_android.data.models.UpdateFCMTokenRequest
import ecccomp.s2240788.mobile_android.data.result.NotificationResult

/**
 * FCM Token Repository
 * Handles FCM token management with backend
 */
class FCMTokenRepository(
    private val apiService: ApiService
) {
    /**
     * Update FCM token on server
     */
    suspend fun updateFCMToken(token: String): NotificationResult<Map<String, Any>> {
        return try {
            val request = UpdateFCMTokenRequest(fcm_token = token)
            val response = apiService.updateFCMToken(request)

            if (response.isSuccessful) {
                val apiResponse = response.body()
                if (apiResponse?.success == true) {
                    NotificationResult.Success(apiResponse.data ?: emptyMap())
                } else {
                    NotificationResult.Error(apiResponse?.message ?: "Failed to update FCM token")
                }
            } else {
                NotificationResult.Error("Failed to update FCM token: ${response.message()}")
            }
        } catch (e: Exception) {
            NotificationResult.Error("Network error: ${e.message}")
        }
    }
}

