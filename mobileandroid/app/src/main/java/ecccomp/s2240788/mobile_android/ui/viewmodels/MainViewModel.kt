package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.Task
import ecccomp.s2240788.mobile_android.data.models.StatsDashboard
import ecccomp.s2240788.mobile_android.data.models.UserStats
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch
import kotlinx.coroutines.async

/**
 * Today's statistics for main screen
 */
data class TodayStats(
    val progressPercentage: Int,
    val tasksCompleted: Int,
    val focusTimeMinutes: Int,
    val streakDays: Int
)

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

    private val _todayProgress = MutableLiveData<Int>()
    val todayProgress: LiveData<Int> = _todayProgress

    private val _todayStats = MutableLiveData<TodayStats?>()
    val todayStats: LiveData<TodayStats?> = _todayStats

    private val _studySessions = MutableLiveData<List<ecccomp.s2240788.mobile_android.data.models.StudyScheduleWithPath>>()
    val studySessions: LiveData<List<ecccomp.s2240788.mobile_android.data.models.StudyScheduleWithPath>> = _studySessions

    fun getTasks() {
        viewModelScope.launch {
            try {
                _isLoading.value = true

                // Fetch both tasks and study sessions in parallel
                val tasksDeferred = async { apiService.getTasks() }
                val studySessionsDeferred = async { apiService.getTodaySessions() }

                val tasksResponse = tasksDeferred.await()
                val studySessionsResponse = studySessionsDeferred.await()

                // Process tasks
                val tasks = if (tasksResponse.isSuccessful) {
                    val apiResponse = tasksResponse.body()
                    if (apiResponse?.success == true) {
                        extractTasksFromResponse(apiResponse.data)
                    } else {
                        emptyList()
                    }
                } else {
                    emptyList()
                }

                // Process study sessions
                val studySessions = if (studySessionsResponse.isSuccessful) {
                    val apiResponse = studySessionsResponse.body()
                    if (apiResponse?.success == true) {
                        apiResponse.data?.upcoming_sessions ?: emptyList()
                    } else {
                        emptyList()
                    }
                } else {
                    emptyList()
                }

                _studySessions.postValue(studySessions)

                // Convert study sessions to Task objects and merge
                val studySessionTasks = studySessions.map { convertStudySessionToTask(it) }
                val allTasks = tasks + studySessionTasks

                // Sort and display
                val sortedTasks = sortTasksForMainDisplay(allTasks)
                _tasks.postValue(sortedTasks)

                android.util.Log.d("MainViewModel", "Tasks: ${tasks.size}, Study sessions: ${studySessions.size}, Total: ${allTasks.size}, Sorted: ${sortedTasks.size}")

            } catch (e: Exception) {
                _error.value = "Network error: ${e.message}"
                _tasks.postValue(emptyList())
                android.util.Log.e("MainViewModel", "Error loading tasks", e)
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Convert StudyScheduleWithPath to Task for unified display
     */
    private fun convertStudySessionToTask(session: ecccomp.s2240788.mobile_android.data.models.StudyScheduleWithPath): Task {
        val pathTitle = session.learning_path?.title ?: "学習"
        val dayName = session.getDayNameJapanese()
        val time = session.getFormattedTime()

        return Task(
            id = -session.id, // Negative ID to distinguish from regular tasks
            title = "📚 学習スケジュール: $pathTitle",
            category = "study",
            description = "${dayName}曜日 $time - ${session.duration_minutes}分",
            status = "pending",
            priority = 5, // High priority for study schedules
            energy_level = "high",
            estimated_minutes = session.duration_minutes,
            deadline = null, // No specific deadline, it's today
            scheduled_time = session.study_time,
            created_at = "",
            updated_at = "",
            user_id = 0,
            project_id = null,
            learning_milestone_id = session.learning_path_id, // Mark as roadmap task
            ai_breakdown_enabled = false,
            subtasks = null,
            knowledge_items = null
        )
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

    /**
     * Smart task sorting for main display:
     * 1. Prioritize user-created tasks (learning_milestone_id == null)
     * 2. Then roadmap tasks (learning_milestone_id != null)
     * 3. Sort by scheduled_time (earliest first)
     * 4. Sort by priority (highest first)
     * 5. Filter out completed tasks
     * 6. Take top 3
     */
    private fun sortTasksForMainDisplay(tasks: List<Task>): List<Task> {
        return tasks
            .filter { it.status != "completed" } // Exclude completed tasks
            .sortedWith(compareBy<Task> { it.learning_milestone_id != null } // User-created tasks first (null milestone)
                .thenBy { task ->
                    // Parse scheduled_time for sorting (HH:mm:ss format)
                    task.scheduled_time?.let { time ->
                        try {
                            val parts = time.split(":")
                            val hour = parts.getOrNull(0)?.toIntOrNull() ?: 24
                            val minute = parts.getOrNull(1)?.toIntOrNull() ?: 0
                            hour * 60 + minute // Convert to minutes for comparison
                        } catch (e: Exception) {
                            Int.MAX_VALUE // Put at end if parsing fails
                        }
                    } ?: Int.MAX_VALUE // Tasks without scheduled_time go to end
                }
                .thenByDescending { it.priority } // Higher priority first
            )
            .take(3) // Take top 3
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

                        // Refresh task list and progress
                        getTasks()
                        getTodayProgress()
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

                        // Refresh task list and progress
                        getTasks()
                        getTodayProgress()
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

                        // Refresh task list and progress
                        getTasks()
                        getTodayProgress()
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

    /**
     * Get today's progress and statistics
     * 今日の進捗と統計を取得
     */
    fun getTodayProgress() {
        viewModelScope.launch {
            try {
                // Fetch dashboard stats and user stats in parallel
                val dashboardDeferred = async { apiService.getStatsDashboard() }
                val userStatsDeferred = async { apiService.getUserStats() }

                val dashboardResponse = dashboardDeferred.await()
                val userStatsResponse = userStatsDeferred.await()

                if (dashboardResponse.isSuccessful) {
                    val apiResponse = dashboardResponse.body()
                    if (apiResponse?.success == true) {
                        val dashboard = apiResponse.data as? StatsDashboard
                        if (dashboard != null) {
                            // Calculate today's progress percentage
                            val todayTasks = dashboard.tasks.today
                            val totalTasks = todayTasks["total"] ?: 0
                            val completedTasks = todayTasks["completed"] ?: 0
                            
                            // Progress = (completed / total) * 100
                            // Handle edge cases:
                            // - If total > 0: normal calculation
                            // - If total == 0 but completed > 0: 100% (all tasks done)
                            // - If total == 0 and completed == 0: 0% (no tasks)
                            val progress = when {
                                totalTasks > 0 -> {
                                    ((completedTasks.toFloat() / totalTasks.toFloat()) * 100).toInt().coerceIn(0, 100)
                                }
                                completedTasks > 0 -> {
                                    // Tasks completed today but not created today (e.g., from previous days)
                                    100
                                }
                                else -> {
                                    0
                                }
                            }

                            // Get focus time from sessions
                            val todaySessions = dashboard.sessions.today
                            val focusTimeMinutes = todaySessions["minutes"] ?: 0

                            // Get streak from user stats
                            val streakDays = if (userStatsResponse.isSuccessful) {
                                val userStatsApiResponse = userStatsResponse.body()
                                if (userStatsApiResponse?.success == true) {
                                    val userStats = userStatsApiResponse.data as? UserStats
                                    userStats?.current_streak ?: 0
                                } else {
                                    0
                                }
                            } else {
                                0
                            }

                            val stats = TodayStats(
                                progressPercentage = progress,
                                tasksCompleted = completedTasks,
                                focusTimeMinutes = focusTimeMinutes,
                                streakDays = streakDays
                            )

                            _todayProgress.postValue(progress)
                            _todayStats.postValue(stats)
                            
                            android.util.Log.d("MainViewModel", "Today progress: $progress%, Tasks: $completedTasks/$totalTasks, Streak: $streakDays")
                        } else {
                            _todayProgress.postValue(0)
                            _todayStats.postValue(null)
                        }
                    } else {
                        _error.postValue(apiResponse?.message ?: "進捗データの取得に失敗しました")
                        _todayProgress.postValue(0)
                        _todayStats.postValue(null)
                    }
                } else {
                    _error.postValue("API Error: ${dashboardResponse.message()}")
                    _todayProgress.postValue(0)
                    _todayStats.postValue(null)
                }
            } catch (e: Exception) {
                _error.postValue("ネットワークエラー: ${e.message}")
                _todayProgress.postValue(0)
                _todayStats.postValue(null)
                android.util.Log.e("MainViewModel", "Error loading today progress", e)
            }
        }
    }
}