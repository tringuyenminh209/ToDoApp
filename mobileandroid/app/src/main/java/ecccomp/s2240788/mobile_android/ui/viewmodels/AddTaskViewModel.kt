package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.CheckContextSwitchRequest
import ecccomp.s2240788.mobile_android.data.models.ContextSwitchResponse
import ecccomp.s2240788.mobile_android.data.models.CreateTaskRequest
import ecccomp.s2240788.mobile_android.data.models.CreateSubtaskRequest
import ecccomp.s2240788.mobile_android.data.models.SaveEnvironmentCheckRequest
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

    private val _createdTaskId = MutableLiveData<Int?>()
    val createdTaskId: LiveData<Int?> = _createdTaskId

    private val _environmentCheckSaved = MutableLiveData<Boolean>()
    val environmentCheckSaved: LiveData<Boolean> = _environmentCheckSaved

    private val _contextSwitchResponse = MutableLiveData<ContextSwitchResponse?>()
    val contextSwitchResponse: LiveData<ContextSwitchResponse?> = _contextSwitchResponse

    /**
     * タスクを作成
     * Priority is now directly 1-5 integer
     * Focus Enhancement fields added
     */
    fun createTask(
        title: String,
        description: String?,
        priority: Int, // 1-5
        dueDate: String?,
        energyLevel: String, // "low" | "medium" | "high"
        estimatedMinutes: Int?,
        category: String?, // "study" | "work" | "personal" | "other"
        subtasks: List<SubtaskInput> = emptyList(),
        requiresDeepFocus: Boolean = false,
        allowInterruptions: Boolean = true,
        focusDifficulty: Int = 3,
        warmupMinutes: Int? = null,
        cooldownMinutes: Int? = null,
        startImmediately: Boolean = false
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
                    deadline = dueDate,
                    requires_deep_focus = requiresDeepFocus,
                    allow_interruptions = allowInterruptions,
                    focus_difficulty = focusDifficulty,
                    warmup_minutes = warmupMinutes,
                    cooldown_minutes = cooldownMinutes
                )

                val response = apiService.createTask(request)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse != null && apiResponse.success) {
                        val createdTask = apiResponse.data

                        // Store created task ID
                        if (createdTask != null) {
                            _createdTaskId.value = createdTask.id
                        }

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

    /**
     * Save environment checklist
     */
    fun saveEnvironmentCheck(request: SaveEnvironmentCheckRequest) {
        viewModelScope.launch {
            try {
                val response = apiService.saveEnvironmentCheck(request)
                if (response.isSuccessful && response.body()?.success == true) {
                    _environmentCheckSaved.value = true
                } else {
                    _error.value = "環境チェックの保存に失敗しました"
                    _environmentCheckSaved.value = false
                }
            } catch (e: Exception) {
                _error.value = "ネットワークエラー: ${e.message}"
                _environmentCheckSaved.value = false
            }
        }
    }

    /**
     * Check for context switch
     */
    fun checkContextSwitch(toTaskId: Int, fromTaskId: Int? = null) {
        viewModelScope.launch {
            try {
                val request = CheckContextSwitchRequest(fromTaskId, toTaskId)
                val response = apiService.checkContextSwitch(request)
                if (response.isSuccessful && response.body()?.success == true) {
                    _contextSwitchResponse.value = response.body()?.data
                } else {
                    _contextSwitchResponse.value = null
                }
            } catch (e: Exception) {
                _error.value = "ネットワークエラー: ${e.message}"
                _contextSwitchResponse.value = null
            }
        }
    }

    /**
     * Confirm context switch
     */
    fun confirmContextSwitch(contextSwitchId: Int, note: String? = null) {
        viewModelScope.launch {
            try {
                val noteMap = if (note != null) {
                    mapOf("note" to note)
                } else {
                    mapOf<String, String>()
                }
                apiService.confirmContextSwitch(contextSwitchId, noteMap)
            } catch (e: Exception) {
                // Silent fail for confirmation
                android.util.Log.e("AddTaskViewModel", "Error confirming context switch", e)
            }
        }
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

