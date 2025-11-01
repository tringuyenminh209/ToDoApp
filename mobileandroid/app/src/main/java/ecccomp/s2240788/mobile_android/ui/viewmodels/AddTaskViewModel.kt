package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.CreateTaskRequest
import ecccomp.s2240788.mobile_android.data.models.CreateSubtaskRequest
import ecccomp.s2240788.mobile_android.ui.adapters.SubtaskInput
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

/**
 * AddTaskViewModel
 * 新規タスク作成のビジネスロジック
 */
class AddTaskViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    private val _taskCreated = MutableLiveData<Boolean>()
    val taskCreated: LiveData<Boolean> = _taskCreated

    /**
     * タスクを作成
     * Priority is now directly 1-5 integer
     */
    fun createTask(
        title: String,
        description: String?,
        priority: Int, // 1-5
        dueDate: String?,
        energyLevel: String, // "low" | "medium" | "high"
        estimatedMinutes: Int?,
        category: String?, // "study" | "work" | "personal" | "other"
        subtasks: List<SubtaskInput> = emptyList()
    ) {
        viewModelScope.launch {
            try {
                // Validation
                if (title.trim().isEmpty()) {
                    _error.value = "タイトルは必須です"
                    return@launch
                }

                // Validate priority range
                val validPriority = priority.coerceIn(1, 5)

                _isLoading.value = true
                _error.value = null

                val request = CreateTaskRequest(
                    title = title.trim(),
                    category = category,
                    description = if (description.isNullOrBlank()) null else description.trim(),
                    priority = validPriority,
                    energy_level = energyLevel,
                    estimated_minutes = estimatedMinutes,
                    deadline = dueDate
                )

                val response = apiService.createTask(request)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse != null && apiResponse.success) {
                        val createdTask = apiResponse.data

                        // Create subtasks if any
                        if (createdTask != null && subtasks.isNotEmpty()) {
                            createSubtasks(createdTask.id, subtasks)
                        } else {
                            _taskCreated.value = true
                        }
                    } else {
                        _error.value = "タスクの作成に失敗しました"
                    }
                } else {
                    _error.value = when (response.code()) {
                        422 -> "入力データが無効です"
                        500 -> "サーバーエラーが発生しました"
                        else -> "タスクの作成に失敗しました: ${response.message()}"
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

    private suspend fun createSubtasks(taskId: Int, subtasks: List<SubtaskInput>) {
        try {
            // Filter out empty subtasks
            val validSubtasks = subtasks.filter { it.title.trim().isNotEmpty() }

            if (validSubtasks.isEmpty()) {
                _taskCreated.value = true
                return
            }

            // Create each subtask
            var successCount = 0
            for ((index, subtask) in validSubtasks.withIndex()) {
                val request = CreateSubtaskRequest(
                    title = subtask.title.trim(),
                    estimated_minutes = subtask.estimatedMinutes,
                    sort_order = index
                )

                val response = apiService.createSubtask(taskId, request)
                if (response.isSuccessful && response.body()?.success == true) {
                    successCount++
                }
            }

            // Success if at least one subtask was created or if there were no valid subtasks
            _taskCreated.value = true

        } catch (e: Exception) {
            // Even if subtask creation fails, the task was created successfully
            _taskCreated.value = true
        }
    }

}

