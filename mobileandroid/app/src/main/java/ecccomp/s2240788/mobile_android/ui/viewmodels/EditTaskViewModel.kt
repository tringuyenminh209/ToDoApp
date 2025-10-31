package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.CreateSubtaskRequest
import ecccomp.s2240788.mobile_android.data.models.CreateTaskRequest
import ecccomp.s2240788.mobile_android.data.models.Subtask
import ecccomp.s2240788.mobile_android.data.models.Task
import ecccomp.s2240788.mobile_android.ui.adapters.SubtaskInput
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
            // Extract subtasks
            val subtasksList = (map["subtasks"] as? List<*>)?.mapNotNull { subtaskMap ->
                if (subtaskMap is Map<*, *>) {
                    try {
                        Subtask(
                            id = (subtaskMap["id"] as? Number)?.toInt() ?: 0,
                            task_id = (subtaskMap["task_id"] as? Number)?.toInt() ?: 0,
                            title = subtaskMap["title"] as? String ?: "",
                            is_completed = subtaskMap["is_completed"] as? Boolean ?: false,
                            estimated_minutes = (subtaskMap["estimated_minutes"] as? Number)?.toInt(),
                            sort_order = (subtaskMap["sort_order"] as? Number)?.toInt() ?: 0,
                            created_at = subtaskMap["created_at"] as? String ?: "",
                            updated_at = subtaskMap["updated_at"] as? String ?: ""
                        )
                    } catch (e: Exception) {
                        null
                    }
                } else null
            }

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
                ai_breakdown_enabled = map["ai_breakdown_enabled"] as? Boolean ?: false,
                subtasks = subtasksList
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
        priority: Int, // 1-5
        dueDate: String?,
        energyLevel: String?,
        estimatedMinutes: Int?,
        subtasks: List<SubtaskInput> = emptyList()
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

                // Validate priority range
                val validPriority = priority.coerceIn(1, 5)

                val request = CreateTaskRequest(
                    title = title.trim(),
                    description = if (description.isNullOrBlank()) null else description.trim(),
                    priority = validPriority,
                    energy_level = energyLevel ?: _task.value?.energy_level ?: "medium",
                    estimated_minutes = estimatedMinutes ?: _task.value?.estimated_minutes,
                    deadline = dueDate ?: _task.value?.deadline
                )

                val response = apiService.updateTask(taskId, request)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse != null && apiResponse.success) {
                        // Update subtasks
                        updateSubtasks(taskId, subtasks)
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

    private suspend fun updateSubtasks(taskId: Int, newSubtasks: List<SubtaskInput>) {
        try {
            val oldSubtasks = _task.value?.subtasks ?: emptyList()

            // Filter out empty subtasks
            val validSubtasks = newSubtasks.filter { it.title.trim().isNotEmpty() }

            // Find subtasks to delete (old subtasks not in new list)
            val subtasksToDelete = oldSubtasks.filter { oldSub ->
                !validSubtasks.any { it.id == oldSub.id.toString() }
            }

            // Delete removed subtasks
            for (subtask in subtasksToDelete) {
                try {
                    apiService.deleteSubtask(subtask.id)
                } catch (e: Exception) {
                    // Continue even if delete fails
                }
            }

            // Update existing or create new subtasks
            for ((index, subtask) in validSubtasks.withIndex()) {
                val request = CreateSubtaskRequest(
                    title = subtask.title.trim(),
                    estimated_minutes = subtask.estimatedMinutes,
                    sort_order = index
                )

                // Check if it's an existing subtask (has numeric ID from backend)
                val isExisting = subtask.id.toIntOrNull() != null &&
                                oldSubtasks.any { it.id.toString() == subtask.id }

                if (isExisting) {
                    // Update existing subtask
                    apiService.updateSubtask(subtask.id.toInt(), request)
                } else {
                    // Create new subtask
                    apiService.createSubtask(taskId, request)
                }
            }

            _taskUpdated.value = true

        } catch (e: Exception) {
            // Even if subtask update fails, the task was updated successfully
            _taskUpdated.value = true
        }
    }
}

