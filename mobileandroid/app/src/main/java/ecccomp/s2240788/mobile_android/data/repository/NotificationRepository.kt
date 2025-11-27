package ecccomp.s2240788.mobile_android.data.repository

import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.*
import ecccomp.s2240788.mobile_android.data.result.NotificationResult

/**
 * Notification Repository
 * Handles API calls for notification management
 * Layer between ViewModel and ApiService
 */
class NotificationRepository(
    private val apiService: ApiService
) {
    /**
     * Get all notifications (paginated)
     */
    suspend fun getNotifications(
        type: String? = null,
        read: Boolean? = null,  // Map to "unread" query param
        perPage: Int = 20
    ): NotificationResult<NotificationListResponse> {
        return try {
            android.util.Log.d("NotificationRepository", "Getting notifications: type=$type, read=$read, perPage=$perPage")
            // Backend dùng "unread" query param (string: "true"/"1" hoặc null)
            val unreadParam = if (read == false) "true" else null
            val response = apiService.getNotifications(type, unreadParam, perPage)
            android.util.Log.d("NotificationRepository", "Response received: isSuccessful=${response.isSuccessful}, code=${response.code()}")

            if (response.isSuccessful) {
                try {
                    val body = response.body()
                    android.util.Log.d("NotificationRepository", "Response body: ${if (body == null) "null" else "not null"}")
                    
                    if (body?.success == true && body.data != null) {
                        android.util.Log.d("NotificationRepository", "Success: ${body.data.size} notifications")
                        // Convert từ NotificationListApiResponse sang NotificationListResponse
                        val listResponse = NotificationListResponse.fromApiResponse(body)
                        NotificationResult.Success(listResponse)
                    } else {
                        val errorMsg = "Failed to get notifications"
                        android.util.Log.e("NotificationRepository", "Error in response: $errorMsg")
                        NotificationResult.Error(errorMsg)
                    }
                } catch (e: IllegalStateException) {
                    android.util.Log.e("NotificationRepository", "IllegalStateException when reading body: ${e.message}", e)
                    NotificationResult.Error("Network error: Response body already consumed. Please try again.")
                }
            } else {
                val errorMsg = try {
                    val errorBody = response.errorBody()
                    if (errorBody != null) {
                        errorBody.string()
                    } else {
                        response.message() ?: "Failed to get notifications"
                    }
                } catch (e: Exception) {
                    android.util.Log.e("NotificationRepository", "Error reading error body: ${e.message}", e)
                    response.message() ?: "Failed to get notifications"
                }
                android.util.Log.e("NotificationRepository", "HTTP error: ${response.code()}, message: $errorMsg")
                NotificationResult.Error("Failed to get notifications: $errorMsg")
            }
        } catch (e: IllegalStateException) {
            android.util.Log.e("NotificationRepository", "IllegalStateException: ${e.message}", e)
            android.util.Log.e("NotificationRepository", "Stack trace", e)
            NotificationResult.Error("Network error: Response body already consumed. Please try again.")
        } catch (e: Exception) {
            android.util.Log.e("NotificationRepository", "Exception: ${e.message}", e)
            android.util.Log.e("NotificationRepository", "Exception type: ${e.javaClass.name}", e)
            NotificationResult.Error("Network error: ${e.message ?: e.javaClass.simpleName}")
        }
    }

    /**
     * Get unread notification count
     */
    suspend fun getUnreadCount(): NotificationResult<Int> {
        return try {
            val response = apiService.getUnreadCount()

            if (response.isSuccessful) {
                val body = response.body()
                if (body?.success == true && body.data != null) {
                    NotificationResult.Success(body.data.unread_count)
                } else {
                    NotificationResult.Error("Failed to get unread count")
                }
            } else {
                val errorMsg = try {
                    response.errorBody()?.string() ?: response.message() ?: "Failed to get unread count"
                } catch (e: Exception) {
                    response.message() ?: "Failed to get unread count"
                }
                NotificationResult.Error("Failed to get unread count: $errorMsg")
            }
        } catch (e: IllegalStateException) {
            android.util.Log.e("NotificationRepository", "IllegalStateException in getUnreadCount: ${e.message}", e)
            NotificationResult.Error("Network error: Response body already consumed. Please try again.")
        } catch (e: Exception) {
            android.util.Log.e("NotificationRepository", "Exception in getUnreadCount: ${e.message}", e)
            NotificationResult.Error("Network error: ${e.message ?: e.javaClass.simpleName}")
        }
    }

    /**
     * Get recent notifications
     */
    suspend fun getRecentNotifications(): NotificationResult<List<Notification>> {
        return try {
            val response = apiService.getRecentNotifications()

            if (response.isSuccessful) {
                val apiResponse = response.body()
                if (apiResponse?.success == true && apiResponse.data != null) {
                    NotificationResult.Success(apiResponse.data)
                } else {
                    NotificationResult.Error("Failed to get recent notifications")
                }
            } else {
                NotificationResult.Error("Failed to get recent notifications")
            }
        } catch (e: Exception) {
            NotificationResult.Error("Network error: ${e.message}")
        }
    }

    /**
     * Mark notification as read
     */
    suspend fun markAsRead(notificationId: Int): NotificationResult<Notification> {
        return try {
            val response = apiService.markNotificationAsRead(notificationId)

            if (response.isSuccessful) {
                val apiResponse = response.body()
                if (apiResponse?.success == true && apiResponse.data != null) {
                    NotificationResult.Success(apiResponse.data)
                } else {
                    NotificationResult.Error("Failed to mark as read")
                }
            } else {
                NotificationResult.Error("Failed to mark as read")
            }
        } catch (e: Exception) {
            NotificationResult.Error("Network error: ${e.message}")
        }
    }

    /**
     * Mark all notifications as read
     */
    suspend fun markAllAsRead(): NotificationResult<Boolean> {
        return try {
            val response = apiService.markAllNotificationsAsRead()

            if (response.isSuccessful) {
                val apiResponse = response.body()
                if (apiResponse?.success == true) {
                    NotificationResult.Success(true)
                } else {
                    NotificationResult.Error("Failed to mark all as read")
                }
            } else {
                NotificationResult.Error("Failed to mark all as read")
            }
        } catch (e: Exception) {
            NotificationResult.Error("Network error: ${e.message}")
        }
    }

    /**
     * Delete notification
     */
    suspend fun deleteNotification(notificationId: Int): NotificationResult<Boolean> {
        return try {
            val response = apiService.deleteNotification(notificationId)

            if (response.isSuccessful) {
                val apiResponse = response.body()
                if (apiResponse?.success == true) {
                    NotificationResult.Success(true)
                } else {
                    NotificationResult.Error("Failed to delete notification")
                }
            } else {
                NotificationResult.Error("Failed to delete notification")
            }
        } catch (e: Exception) {
            NotificationResult.Error("Network error: ${e.message}")
        }
    }

    /**
     * Clear all read notifications
     */
    suspend fun clearReadNotifications(): NotificationResult<Boolean> {
        return try {
            val response = apiService.clearReadNotifications()

            if (response.isSuccessful) {
                val apiResponse = response.body()
                if (apiResponse?.success == true) {
                    NotificationResult.Success(true)
                } else {
                    NotificationResult.Error("Failed to clear read notifications")
                }
            } else {
                NotificationResult.Error("Failed to clear read notifications")
            }
        } catch (e: Exception) {
            NotificationResult.Error("Network error: ${e.message}")
        }
    }
}
