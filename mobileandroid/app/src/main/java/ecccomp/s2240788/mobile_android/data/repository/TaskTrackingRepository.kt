package ecccomp.s2240788.mobile_android.data.repository

import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.*
import ecccomp.s2240788.mobile_android.data.result.NotificationResult

/**
 * Task Tracking Repository
 * Handles API calls for task abandonment and heartbeat tracking
 * Layer between ViewModel and ApiService
 */
class TaskTrackingRepository(
    private val apiService: ApiService
) {
    /**
     * Send heartbeat for active task
     */
    suspend fun sendHeartbeat(taskId: Int): NotificationResult<Task> {
        return try {
            val response = apiService.sendTaskHeartbeat(taskId)

            if (response.isSuccessful) {
                val apiResponse = response.body()
                if (apiResponse?.success == true && apiResponse.data != null) {
                    NotificationResult.Success(apiResponse.data)
                } else {
                    NotificationResult.Error("Failed to send heartbeat")
                }
            } else {
                NotificationResult.Error("Failed to send heartbeat: ${response.message()}")
            }
        } catch (e: Exception) {
            NotificationResult.Error("Network error: ${e.message}")
        }
    }

    /**
     * Manually abandon task
     */
    suspend fun abandonTask(
        taskId: Int,
        reason: String? = null,
        type: String = "manual"
    ): NotificationResult<TaskAbandonment> {
        return try {
            val request = AbandonTaskRequest(reason, type)
            val response = apiService.abandonTask(taskId, request)

            if (response.isSuccessful) {
                val apiResponse = response.body()
                if (apiResponse?.success == true && apiResponse.data != null) {
                    NotificationResult.Success(apiResponse.data)
                } else {
                    NotificationResult.Error("Failed to abandon task")
                }
            } else {
                NotificationResult.Error("Failed to abandon task: ${response.message()}")
            }
        } catch (e: Exception) {
            NotificationResult.Error("Network error: ${e.message}")
        }
    }

    /**
     * Resume abandoned task
     */
    suspend fun resumeTask(taskId: Int): NotificationResult<Task> {
        return try {
            val response = apiService.resumeAbandonedTask(taskId)

            if (response.isSuccessful) {
                val apiResponse = response.body()
                if (apiResponse?.success == true && apiResponse.data != null) {
                    NotificationResult.Success(apiResponse.data)
                } else {
                    NotificationResult.Error("Failed to resume task")
                }
            } else {
                NotificationResult.Error("Failed to resume task: ${response.message()}")
            }
        } catch (e: Exception) {
            NotificationResult.Error("Network error: ${e.message}")
        }
    }

    /**
     * Get abandonment history for a task
     */
    suspend fun getTaskAbandonments(taskId: Int): NotificationResult<List<TaskAbandonment>> {
        return try {
            val response = apiService.getTaskAbandonments(taskId)

            if (response.isSuccessful) {
                val apiResponse = response.body()
                if (apiResponse?.success == true && apiResponse.data != null) {
                    NotificationResult.Success(apiResponse.data)
                } else {
                    NotificationResult.Error("Failed to get abandonments")
                }
            } else {
                NotificationResult.Error("Failed to get abandonments: ${response.message()}")
            }
        } catch (e: Exception) {
            NotificationResult.Error("Network error: ${e.message}")
        }
    }

    /**
     * Get all abandoned tasks
     */
    suspend fun getAbandonedTasks(): NotificationResult<List<TaskWithAbandonmentInfo>> {
        return try {
            val response = apiService.getAbandonedTasks()

            if (response.isSuccessful) {
                val apiResponse = response.body()
                if (apiResponse?.success == true && apiResponse.data != null) {
                    NotificationResult.Success(apiResponse.data)
                } else {
                    NotificationResult.Error("Failed to get abandoned tasks")
                }
            } else {
                NotificationResult.Error("Failed to get abandoned tasks: ${response.message()}")
            }
        } catch (e: Exception) {
            NotificationResult.Error("Network error: ${e.message}")
        }
    }

    /**
     * Get abandonment statistics
     */
    suspend fun getAbandonmentStats(days: Int = 7): NotificationResult<AbandonmentStatsResponse> {
        return try {
            val response = apiService.getAbandonmentStats(days)

            if (response.isSuccessful) {
                val apiResponse = response.body()
                if (apiResponse?.success == true && apiResponse.data != null) {
                    NotificationResult.Success(apiResponse.data)
                } else {
                    NotificationResult.Error("Failed to get statistics")
                }
            } else {
                NotificationResult.Error("Failed to get statistics: ${response.message()}")
            }
        } catch (e: Exception) {
            NotificationResult.Error("Network error: ${e.message}")
        }
    }
}
