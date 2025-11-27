package ecccomp.s2240788.mobile_android.workers

import android.content.Context
import android.util.Log
import androidx.work.CoroutineWorker
import androidx.work.WorkerParameters
import ecccomp.s2240788.mobile_android.data.repository.TaskTrackingRepository
import ecccomp.s2240788.mobile_android.utils.NetworkModule

/**
 * TaskHeartbeatWorker
 * Sends periodic heartbeat to backend for active task tracking
 * Runs every 15 minutes when there's an active task
 */
class TaskHeartbeatWorker(
    context: Context,
    params: WorkerParameters
) : CoroutineWorker(context, params) {

    companion object {
        private const val TAG = "TaskHeartbeatWorker"
        const val KEY_TASK_ID = "task_id"
    }

    override suspend fun doWork(): Result {
        val taskId = inputData.getInt(KEY_TASK_ID, -1)

        if (taskId == -1) {
            Log.e(TAG, "No task ID provided")
            return Result.failure()
        }

        return try {
            // Initialize repository
            val apiService = NetworkModule.provideApiService(
                NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
            )
            val repository = TaskTrackingRepository(apiService)

            // Send heartbeat
            Log.d(TAG, "Sending heartbeat for task #$taskId")
            val result = repository.sendHeartbeat(taskId)

            if (result.isSuccess) {
                Log.d(TAG, "Heartbeat sent successfully for task #$taskId")
                Result.success()
            } else {
                Log.e(TAG, "Failed to send heartbeat: ${result.fold({ "" }, { it })}")
                Result.retry()
            }
        } catch (e: Exception) {
            Log.e(TAG, "Exception in heartbeat worker", e)
            Result.retry()
        }
    }
}
