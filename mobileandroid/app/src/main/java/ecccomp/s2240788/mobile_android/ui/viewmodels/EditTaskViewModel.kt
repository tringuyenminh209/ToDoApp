package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.CreateTaskRequest
import ecccomp.s2240788.mobile_android.data.models.Task
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

/**
 * EditTaskViewModel
 * タスク編集のビジネスロジック
 */
class EditTaskViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    private val _task = MutableLiveData<Task?>()
    val task: LiveData<Task?> = _task

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    private val _taskUpdated = MutableLiveData<Boolean>()
    val taskUpdated: LiveData<Boolean> = _taskUpdated

    /**
     * タスクを読み込む
     */
    fun loadTask(taskId: Int) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.getTasks()

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse != null && apiResponse.success) {
                        // Extract and parse tasks from paginated response
                        val tasks = extractTasksFromResponse(apiResponse.data)
                        _task.value = tasks.find { it.id == taskId }

                        if (_task.value == null) {
                            _error.value = "タスクが見つかりません"
                        }
                    } else {
                        _error.value = "タスクの読み込みに失敗しました"
                    }
                } else {
                    _error.value = "タスクの読み込みに失敗しました: ${response.message()}"
                }

            } catch (e: Exception) {
                _error.value = "ネットワークエラー: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Extract tasks from paginated response
     */
    private fun extractTasksFromResponse(data: Any?): List<Task> {
        return try {
            if (data is Map<*, *>) {
                val tasksData = data["data"] as? List<*>
                tasksData?.mapNotNull { taskMap ->
                    if (taskMap is Map<*, *>) {
                        convertMapToTask(taskMap)
                    } else {
                        null
                    }
                } ?: emptyList()
            } else if (data is List<*>) {
                data.mapNotNull { taskMap ->
                    if (taskMap is Map<*, *>) {
                        convertMapToTask(taskMap)
                    } else {
                        null
                    }
                }
            } else {
                emptyList()
            }
        } catch (e: Exception) {
            emptyList()
        }
    }

    /**
     * Convert Map to Task object
     */
    private fun convertMapToTask(map: Map<*, *>): Task? {
        return try {
            Task(
                id = (map["id"] as? Number)?.toInt() ?: 0,
                title = map["title"] as? String ?: "",
                description = map["description"] as? String,
                status = map["status"] as? String ?: "pending",
                priority = (map["priority"] as? Number)?.toInt() ?: 3,
                energy_level = map["energy_level"] as? String ?: "medium",
                estimated_minutes = (map["estimated_minutes"] as? Number)?.toInt(),
                deadline = map["deadline"] as? String,
                created_at = map["created_at"] as? String ?: "",
                updated_at = map["updated_at"] as? String ?: "",
                user_id = (map["user_id"] as? Number)?.toInt() ?: 0,
                project_id = (map["project_id"] as? Number)?.toInt(),
                learning_milestone_id = (map["learning_milestone_id"] as? Number)?.toInt(),
                ai_breakdown_enabled = map["ai_breakdown_enabled"] as? Boolean ?: false
            )
        } catch (e: Exception) {
            null
        }
    }

    /**
     * タスクを更新
     */
    fun updateTask(
        taskId: Int,
        title: String,
        description: String?,
        priority: String, // "low", "medium", "high"
        dueDate: String?,
        energyLevel: String?,
        estimatedMinutes: Int?
    ) {
        viewModelScope.launch {
            try {
                // Validation
                if (title.trim().isEmpty()) {
                    _error.value = "タイトルは必須です"
                    return@launch
                }

                _isLoading.value = true
                _error.value = null

                // Map priority string to Int
                val priorityInt = when (priority) {
                    "low" -> 2
                    "high" -> 5
                    else -> 3
                }

                val request = CreateTaskRequest(
                    title = title.trim(),
                    description = if (description.isNullOrBlank()) null else description.trim(),
                    priority = priorityInt,
                    energy_level = energyLevel ?: _task.value?.energy_level ?: "medium",
                    estimated_minutes = estimatedMinutes ?: _task.value?.estimated_minutes,
                    deadline = dueDate ?: _task.value?.deadline
                )

                val response = apiService.updateTask(taskId, request)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse != null && apiResponse.success) {
                        _taskUpdated.value = true
                    } else {
                        _error.value = "タスクの更新に失敗しました"
                    }
                } else {
                    _error.value = when (response.code()) {
                        422 -> "入力データが無効です"
                        404 -> "タスクが見つかりません"
                        500 -> "サーバーエラーが発生しました"
                        else -> "タスクの更新に失敗しました: ${response.message()}"
                    }
                }

            } catch (e: Exception) {
                _error.value = "ネットワークエラー: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    fun clearError() {
        _error.value = null
    }
}

