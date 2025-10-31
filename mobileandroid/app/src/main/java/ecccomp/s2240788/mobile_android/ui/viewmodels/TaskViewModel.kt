package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.Task
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

/**
 * TaskViewModel
 * タスクリストの管理とCRUD操作
 */
class TaskViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    private val _tasks = MutableLiveData<List<Task>>()
    val tasks: LiveData<List<Task>> = _tasks

    private val _filteredTasks = MutableLiveData<List<Task>>()
    val filteredTasks: LiveData<List<Task>> = _filteredTasks

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    private val _taskDeleted = MutableLiveData<Boolean>()
    val taskDeleted: LiveData<Boolean> = _taskDeleted

    private val _taskCompleted = MutableLiveData<Task?>()
    val taskCompleted: LiveData<Task?> = _taskCompleted

    private val _startedTaskId = MutableLiveData<Int?>()
    val startedTaskId: LiveData<Int?> = _startedTaskId

    private var currentFilter: TaskFilter = TaskFilter.ALL
    private var currentQuery: String = ""

    enum class TaskFilter {
        ALL, PENDING, COMPLETED
    }

    /**
     * タスク一覧を取得
     */
    fun fetchTasks() {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.getTasks()

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse != null && apiResponse.success) {
                        // Extract tasks from paginated response
                        val tasks = extractTasksFromResponse(apiResponse.data)
                        _tasks.value = tasks
                        applyFilter(currentFilter)
                    } else {
                        _error.value = "タスクの取得に失敗しました"
                    }
                } else {
                    _error.value = when (response.code()) {
                        401 -> "認証エラー。再度ログインしてください"
                        500 -> "サーバーエラーが発生しました"
                        else -> "タスクの取得に失敗しました: ${response.message()}"
                    }
                }

            } catch (e: Exception) {
                _error.value = "ネットワークエラー: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * タスクを完了にする
     */
    fun completeTask(taskId: Int) {
        viewModelScope.launch {
            try {
                val response = apiService.completeTask(taskId)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse != null && apiResponse.success) {
                        _taskCompleted.value = apiResponse.data
                        // リストを更新
                        fetchTasks()
                    } else {
                        _error.value = "タスクの完了に失敗しました"
                    }
                } else {
                    _error.value = "タスクの完了に失敗しました: ${response.message()}"
                }

            } catch (e: Exception) {
                _error.value = "ネットワークエラー: ${e.message}"
            }
        }
    }

    /**
     * タスクを開始する
     */
    fun startTask(taskId: Int) {
        viewModelScope.launch {
            try {
                val response = apiService.startTask(taskId)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse != null && apiResponse.success) {
                        _startedTaskId.postValue(taskId)
                        // リストを更新
                        fetchTasks()
                    } else {
                        _error.value = "タスクの開始に失敗しました"
                        _startedTaskId.postValue(null)
                    }
                } else {
                    _error.value = "タスクの開始に失敗しました: ${response.message()}"
                    _startedTaskId.postValue(null)
                }

            } catch (e: Exception) {
                _error.value = "ネットワークエラー: ${e.message}"
                _startedTaskId.postValue(null)
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
     * タスクを削除
     */
    fun deleteTask(taskId: Int) {
        viewModelScope.launch {
            try {
                val response = apiService.deleteTask(taskId)

                if (response.isSuccessful) {
                    _taskDeleted.value = true
                    // リストを更新
                    fetchTasks()
                } else {
                    _error.value = "タスクの削除に失敗しました: ${response.message()}"
                }

            } catch (e: Exception) {
                _error.value = "ネットワークエラー: ${e.message}"
            }
        }
    }

    /**
     * フィルターを適用
     */
    fun applyFilter(filter: TaskFilter) {
        currentFilter = filter
        val allTasks = _tasks.value ?: emptyList()
        val base = when (filter) {
            TaskFilter.ALL -> allTasks
            TaskFilter.PENDING -> allTasks.filter { it.status == "pending" || it.status == "in_progress" }
            TaskFilter.COMPLETED -> allTasks.filter { it.status == "completed" }
        }
        applyQueryOn(base)
    }

    fun setQuery(query: String) {
        currentQuery = query
        val base = when (currentFilter) {
            TaskFilter.ALL -> _tasks.value ?: emptyList()
            TaskFilter.PENDING -> (_tasks.value ?: emptyList()).filter { it.status == "pending" || it.status == "in_progress" }
            TaskFilter.COMPLETED -> (_tasks.value ?: emptyList()).filter { it.status == "completed" }
        }
        applyQueryOn(base)
    }

    private fun applyQueryOn(list: List<Task>) {
        val q = currentQuery.trim().lowercase()
        _filteredTasks.value = if (q.isEmpty()) list else list.filter {
            it.title.lowercase().contains(q) || (it.description ?: "").lowercase().contains(q)
        }
    }

    fun clearError() {
        _error.value = null
    }

    fun clearTaskCompleted() {
        _taskCompleted.value = null
    }

    fun clearTaskDeleted() {
        _taskDeleted.value = false
    }

    /**
     * Extract tasks from paginated response
     * Laravel returns tasks in a nested structure: { data: [tasks], current_page, ... }
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
}

