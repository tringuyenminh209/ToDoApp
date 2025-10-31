package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.Task
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

class MainViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String>()
    val error: LiveData<String> = _error

    private val _tasks = MutableLiveData<List<Task>?>()
    val tasks: LiveData<List<Task>?> = _tasks

    private val _taskStarted = MutableLiveData<Boolean>()
    val taskStarted: LiveData<Boolean> = _taskStarted

    private val _startedTaskId = MutableLiveData<Int?>()
    val startedTaskId: LiveData<Int?> = _startedTaskId

    private val _successMessage = MutableLiveData<String>()
    val successMessage: LiveData<String> = _successMessage

    fun getTasks() {
        viewModelScope.launch {
            try {
                _isLoading.value = true

                val response = apiService.getTasks()

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        val tasks = extractTasksFromResponse(apiResponse.data)
                        // Always post value to trigger observer
                        _tasks.postValue(tasks)
                        android.util.Log.d("MainViewModel", "Tasks loaded: ${tasks.size}")
                    } else {
                        _error.value = apiResponse?.message ?: "Failed to get tasks"
                        _tasks.postValue(emptyList()) // Post empty list on error
                    }
                } else {
                    _error.value = "API Error: ${response.message()}"
                    _tasks.postValue(emptyList())
                }

            } catch (e: Exception) {
                _error.value = "Network error: ${e.message}"
                _tasks.postValue(emptyList())
                android.util.Log.e("MainViewModel", "Error loading tasks", e)
            } finally {
                _isLoading.value = false
            }
        }
    }

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

    private fun convertMapToTask(map: Map<*, *>): Task? {
        return try {
            // Parse subtasks
            val subtasksList = (map["subtasks"] as? List<*>)?.mapNotNull { subtaskMap ->
                if (subtaskMap is Map<*, *>) {
                    convertMapToSubtask(subtaskMap)
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

    private fun convertMapToSubtask(map: Map<*, *>): ecccomp.s2240788.mobile_android.data.models.Subtask? {
        return try {
            ecccomp.s2240788.mobile_android.data.models.Subtask(
                id = (map["id"] as? Number)?.toInt() ?: 0,
                task_id = (map["task_id"] as? Number)?.toInt() ?: 0,
                title = map["title"] as? String ?: "",
                is_completed = map["is_completed"] as? Boolean ?: false,
                estimated_minutes = (map["estimated_minutes"] as? Number)?.toInt(),
                sort_order = (map["sort_order"] as? Number)?.toInt() ?: 0,
                created_at = map["created_at"] as? String ?: "",
                updated_at = map["updated_at"] as? String ?: ""
            )
        } catch (e: Exception) {
            null
        }
    }

    /**
     * Start a task (change status to in_progress)
     */
    fun startTask(taskId: Int) {
        viewModelScope.launch {
            try {
                _isLoading.value = true

                val response = apiService.startTask(taskId)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        _startedTaskId.postValue(taskId)
                        _taskStarted.postValue(true)
                        _successMessage.postValue("タスクを開始しました！")
                        android.util.Log.d("MainViewModel", "Task started: $taskId")

                        // Refresh task list
                        getTasks()
                    } else {
                        _error.postValue(apiResponse?.message ?: "タスクの開始に失敗しました")
                        _taskStarted.postValue(false)
                        _startedTaskId.postValue(null)
                    }
                } else {
                    val errorMessage = when (response.code()) {
                        404 -> "タスクが見つかりません"
                        422 -> "このタスクは開始できません"
                        else -> "タスクの開始に失敗しました: ${response.message()}"
                    }
                    _error.postValue(errorMessage)
                    _taskStarted.postValue(false)
                    _startedTaskId.postValue(null)
                }
            } catch (e: Exception) {
                _error.postValue("ネットワークエラー: ${e.message}")
                _taskStarted.postValue(false)
                _startedTaskId.postValue(null)
                android.util.Log.e("MainViewModel", "Error starting task", e)
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Delete a task
     */
    fun deleteTask(taskId: Int) {
        viewModelScope.launch {
            try {
                _isLoading.value = true

                val response = apiService.deleteTask(taskId)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        _successMessage.postValue("タスクを削除しました")
                        android.util.Log.d("MainViewModel", "Task deleted: $taskId")

                        // Refresh task list
                        getTasks()
                    } else {
                        _error.postValue(apiResponse?.message ?: "タスクの削除に失敗しました")
                    }
                } else {
                    val errorMessage = when (response.code()) {
                        404 -> "タスクが見つかりません"
                        403 -> "このタスクを削除する権限がありません"
                        else -> "タスクの削除に失敗しました: ${response.message()}"
                    }
                    _error.postValue(errorMessage)
                }
            } catch (e: Exception) {
                _error.postValue("ネットワークエラー: ${e.message}")
                android.util.Log.e("MainViewModel", "Error deleting task", e)
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Clear started task ID to prevent re-navigation
     */
    fun clearStartedTaskId() {
        _startedTaskId.value = null
    }

    /**
     * Complete a task
     */
    fun completeTask(taskId: Int) {
        viewModelScope.launch {
            try {
                _isLoading.value = true

                val response = apiService.completeTask(taskId)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        _successMessage.postValue("タスクを完了しました！")
                        android.util.Log.d("MainViewModel", "Task completed: $taskId")

                        // Refresh task list
                        getTasks()
                    } else {
                        _error.postValue(apiResponse?.message ?: "タスクの完了に失敗しました")
                    }
                } else {
                    val errorMessage = when (response.code()) {
                        404 -> "タスクが見つかりません"
                        422 -> "このタスクは完了できません"
                        else -> "タスクの完了に失敗しました: ${response.message()}"
                    }
                    _error.postValue(errorMessage)
                }
            } catch (e: Exception) {
                _error.postValue("ネットワークエラー: ${e.message}")
                android.util.Log.e("MainViewModel", "Error completing task", e)
            } finally {
                _isLoading.value = false
            }
        }
    }
}