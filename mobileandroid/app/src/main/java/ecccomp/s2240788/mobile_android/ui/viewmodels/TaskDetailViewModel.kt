package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.CreateSubtaskRequest
import ecccomp.s2240788.mobile_android.data.models.ScheduleSuggestion
import ecccomp.s2240788.mobile_android.data.models.Subtask
import ecccomp.s2240788.mobile_android.data.models.Task
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

class TaskDetailViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    private val _task = MutableLiveData<Task?>()
    val task: LiveData<Task?> = _task

    private val _toast = MutableLiveData<String?>()
    val toast: LiveData<String?> = _toast

    private val _finishEvent = MutableLiveData<Boolean>()
    val finishEvent: LiveData<Boolean> = _finishEvent

    private val _startedTaskId = MutableLiveData<Int?>()
    val startedTaskId: LiveData<Int?> = _startedTaskId

    private val _scheduleSuggestions = MutableLiveData<List<ScheduleSuggestion>>()
    val scheduleSuggestions: LiveData<List<ScheduleSuggestion>> = _scheduleSuggestions

    private val _loadingSuggestions = MutableLiveData<Boolean>()
    val loadingSuggestions: LiveData<Boolean> = _loadingSuggestions

    fun loadTask(taskId: Int) {
        viewModelScope.launch {
            try {
                val res = apiService.getTask(taskId)
                if (res.isSuccessful) {
                    val body = res.body()
                    if (body?.success == true) {
                        _task.value = body.data
                    } else {
                        _toast.value = body?.message ?: "取得に失敗しました"
                    }
                } else {
                    _toast.value = "API Error: ${res.message()}"
                }
            } catch (e: Exception) {
                _toast.value = "ネットワークエラー: ${e.message}"
            }
        }
    }

    fun completeTask(taskId: Int) {
        viewModelScope.launch {
            try {
                val res = apiService.completeTask(taskId)
                if (res.isSuccessful && res.body()?.success == true) {
                    _toast.value = "完了しました"
                    _finishEvent.value = true
                } else _toast.value = "完了に失敗しました"
            } catch (e: Exception) { _toast.value = "ネットワークエラー: ${e.message}" }
        }
    }

    fun deleteTask(taskId: Int) {
        viewModelScope.launch {
            try {
                val res = apiService.deleteTask(taskId)
                if (res.isSuccessful && res.body()?.success == true) {
                    _toast.value = "削除しました"
                    _finishEvent.value = true
                } else _toast.value = "削除に失敗しました"
            } catch (e: Exception) { _toast.value = "ネットワークエラー: ${e.message}" }
        }
    }

    fun startTask(taskId: Int) {
        viewModelScope.launch {
            try {
                val res = apiService.startTask(taskId)
                if (res.isSuccessful && res.body()?.success == true) {
                    _startedTaskId.postValue(taskId)
                    _toast.value = "タスクを開始しました！"
                } else {
                    _toast.value = "タスクの開始に失敗しました"
                    _startedTaskId.postValue(null)
                }
            } catch (e: Exception) {
                _toast.value = "ネットワークエラー: ${e.message}"
                _startedTaskId.postValue(null)
            }
        }
    }

    fun clearStartedTaskId() {
        _startedTaskId.value = null
    }

    fun toggleSubtask(subtaskId: Int) {
        viewModelScope.launch {
            try {
                val res = apiService.toggleSubtask(subtaskId)
                if (res.isSuccessful && res.body()?.success == true) {
                    // Reload task to update subtask list
                    _task.value?.id?.let { loadTask(it) }
                } else {
                    _toast.value = "サブタスクの更新に失敗しました"
                }
            } catch (e: Exception) {
                _toast.value = "ネットワークエラー: ${e.message}"
            }
        }
    }

    fun addSubtask(taskId: Int, title: String, estimatedMinutes: Int? = null) {
        viewModelScope.launch {
            try {
                val request = CreateSubtaskRequest(
                    title = title.trim(),
                    estimated_minutes = estimatedMinutes,
                    sort_order = (_task.value?.subtasks?.size ?: 0)
                )
                val res = apiService.createSubtask(taskId, request)
                if (res.isSuccessful && res.body()?.success == true) {
                    _toast.value = "サブタスクを追加しました"
                    // Reload task to update subtask list
                    loadTask(taskId)
                } else {
                    _toast.value = "サブタスクの追加に失敗しました"
                }
            } catch (e: Exception) {
                _toast.value = "ネットワークエラー: ${e.message}"
            }
        }
    }

    fun loadScheduleSuggestions(taskId: Int, daysAhead: Int = 7) {
        viewModelScope.launch {
            _loadingSuggestions.value = true
            try {
                val res = apiService.suggestTaskSchedule(taskId, daysAhead)
                if (res.isSuccessful) {
                    val body = res.body()
                    if (body?.success == true) {
                        _scheduleSuggestions.value = body.data?.suggestions ?: emptyList()
                    } else {
                        _toast.value = body?.message ?: "スケジュール提案の取得に失敗しました"
                        _scheduleSuggestions.value = emptyList()
                    }
                } else {
                    _toast.value = "API Error: ${res.message()}"
                    _scheduleSuggestions.value = emptyList()
                }
            } catch (e: Exception) {
                _toast.value = "ネットワークエラー: ${e.message}"
                _scheduleSuggestions.value = emptyList()
            } finally {
                _loadingSuggestions.value = false
            }
        }
    }

    /**
     * Apply selected schedule suggestion to task
     */
    fun applyScheduleSuggestion(taskId: Int, suggestion: ScheduleSuggestion) {
        viewModelScope.launch {
            try {
                // Get current task data
                val currentTask = _task.value ?: return@launch

                // Create update request with new scheduled_time
                val scheduledTime = "${suggestion.date} ${suggestion.startTime}:00"
                val updateRequest = ecccomp.s2240788.mobile_android.data.models.CreateTaskRequest(
                    title = currentTask.title,
                    category = currentTask.category,
                    description = currentTask.description,
                    priority = currentTask.priority,
                    energy_level = currentTask.energy_level ?: "medium",
                    estimated_minutes = currentTask.estimated_minutes,
                    deadline = currentTask.deadline,
                    scheduled_time = scheduledTime,
                    requires_deep_focus = currentTask.requires_deep_focus ?: false,
                    allow_interruptions = currentTask.allow_interruptions ?: true,
                    focus_difficulty = currentTask.focus_difficulty ?: 3,
                    warmup_minutes = currentTask.warmup_minutes,
                    cooldown_minutes = currentTask.cooldown_minutes,
                    recovery_minutes = currentTask.recovery_minutes
                )

                // Call API to update task
                val response = apiService.updateTask(taskId, updateRequest)
                if (response.isSuccessful) {
                    val body = response.body()
                    if (body?.success == true) {
                        _toast.value = "スケジュールを設定しました"
                        // Reload task to get updated data
                        loadTask(taskId)
                    } else {
                        _toast.value = body?.message ?: "更新に失敗しました"
                    }
                } else {
                    _toast.value = "更新に失敗しました: ${response.code()}"
                }
            } catch (e: Exception) {
                _toast.value = "ネットワークエラー: ${e.message}"
            }
        }
    }

    private fun extractTasksFromResponse(data: Any?): List<Task> {
        return try {
            if (data is Map<*, *>) {
                val tasksData = data["data"] as? List<*>
                tasksData?.mapNotNull { m -> if (m is Map<*, *>) convertMapToTask(m) else null } ?: emptyList()
            } else if (data is List<*>) {
                data.mapNotNull { m -> if (m is Map<*, *>) convertMapToTask(m) else null }
            } else emptyList()
        } catch (e: Exception) {
            emptyList()
        }
    }

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
                category = map["category"] as? String,
                description = map["description"] as? String,
                status = map["status"] as? String ?: "pending",
                priority = (map["priority"] as? Number)?.toInt() ?: 3,
                energy_level = map["energy_level"] as? String ?: "medium",
                estimated_minutes = (map["estimated_minutes"] as? Number)?.toInt(),
                deadline = map["deadline"] as? String,
                scheduled_time = map["scheduled_time"] as? String,
                created_at = map["created_at"] as? String ?: "",
                updated_at = map["updated_at"] as? String ?: "",
                user_id = (map["user_id"] as? Number)?.toInt() ?: 0,
                project_id = (map["project_id"] as? Number)?.toInt(),
                learning_milestone_id = (map["learning_milestone_id"] as? Number)?.toInt(),
                ai_breakdown_enabled = map["ai_breakdown_enabled"] as? Boolean ?: false,
                subtasks = subtasksList
            )
        } catch (e: Exception) { null }
    }

    fun clearToast() { _toast.value = null }
}


