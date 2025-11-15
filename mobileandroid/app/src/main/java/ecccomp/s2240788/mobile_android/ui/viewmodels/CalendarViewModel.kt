package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.Task
import ecccomp.s2240788.mobile_android.data.models.StudyScheduleWithPath
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch
import kotlinx.coroutines.async
import java.text.SimpleDateFormat
import java.util.*

/**
 * CalendarViewModel
 * カレンダー画面のビジネスロジック管理
 * - タスクを日付でフィルタリング
 * - カレンダー選択状態の管理
 */
class CalendarViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    private val _allTasks = MutableLiveData<List<Task>>()
    val allTasks: LiveData<List<Task>> = _allTasks

    private val _allStudySchedules = MutableLiveData<List<StudyScheduleWithPath>>()
    val allStudySchedules: LiveData<List<StudyScheduleWithPath>> = _allStudySchedules

    private val _filteredTasks = MutableLiveData<List<Task>>()
    val filteredTasks: LiveData<List<Task>> = _filteredTasks

    private val _selectedDate = MutableLiveData<Date>()
    val selectedDate: LiveData<Date> = _selectedDate

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    private var currentFilter: FilterType = FilterType.ALL

    enum class FilterType {
        ALL, ACTIVE, COMPLETED
    }

    init {
        // デフォルトで今日の日付を選択
        _selectedDate.value = Calendar.getInstance().time
        fetchTasks()
    }

    /**
     * タスク一覧を取得
     * Fetch both regular tasks and study schedules
     */
    fun fetchTasks() {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                // Fetch tasks and study schedules in parallel
                val tasksDeferred = async { apiService.getTasks(perPage = 100) }
                val studySchedulesDeferred = async { apiService.getAllStudySchedules() }

                val tasksResponse = tasksDeferred.await()
                val studySchedulesResponse = studySchedulesDeferred.await()

                // Process regular tasks
                if (tasksResponse.isSuccessful) {
                    val apiResponse = tasksResponse.body()
                    android.util.Log.d("CalendarViewModel", "API Response: success=${apiResponse?.success}, data type=${apiResponse?.data?.javaClass?.simpleName}")

                    if (apiResponse?.success == true) {
                        val tasks = parseTasksFromResponse(apiResponse.data)
                        android.util.Log.d("CalendarViewModel", "Loaded ${tasks.size} tasks from API")

                        _allTasks.value = tasks
                    } else {
                        val errorMsg = apiResponse?.message ?: "タスクの取得に失敗しました"
                        android.util.Log.e("CalendarViewModel", "API Error: $errorMsg")
                        _error.value = errorMsg
                    }
                } else {
                    val errorMsg = "ネットワークエラー: ${tasksResponse.code()} - ${tasksResponse.message()}"
                    android.util.Log.e("CalendarViewModel", errorMsg)
                    _error.value = errorMsg
                }

                // Process study schedules
                if (studySchedulesResponse.isSuccessful) {
                    val apiResponse = studySchedulesResponse.body()
                    if (apiResponse?.success == true) {
                        val data = apiResponse.data
                        val schedules = when (data) {
                            is List<*> -> data.mapNotNull { it as? StudyScheduleWithPath }
                            else -> emptyList()
                        }
                        android.util.Log.d("CalendarViewModel", "Loaded ${schedules.size} study schedules from API")
                        _allStudySchedules.value = schedules
                    }
                }

                applyFilter()
            } catch (e: Exception) {
                _error.value = "エラーが発生しました: ${e.message}"
                android.util.Log.e("CalendarViewModel", "Error fetching tasks", e)
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * 日付を選択してフィルタリング
     */
    fun selectDate(date: Date) {
        _selectedDate.value = date
        applyFilter()
    }

    /**
     * 今日に戻る
     */
    fun selectToday() {
        selectDate(Calendar.getInstance().time)
    }

    /**
     * フィルタータイプを設定
     */
    fun setFilter(filterType: FilterType) {
        currentFilter = filterType
        applyFilter()
    }

    /**
     * タスクを再読み込み
     */
    fun refreshTasks() {
        fetchTasks()
    }

    /**
     * フィルターを適用してタスクをフィルタリング
     * Apply filter including study schedules for the selected day of week
     */
    private fun applyFilter() {
        val tasks = _allTasks.value ?: emptyList()
        val studySchedules = _allStudySchedules.value ?: emptyList()
        val selectedDateValue = _selectedDate.value ?: Calendar.getInstance().time
        val today = Calendar.getInstance().time

        // 日付フォーマッター
        val dateFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
        val selectedDateString = dateFormat.format(selectedDateValue)

        // Calculate day of week for selected date (0=Sunday, 1=Monday, ...)
        val calendar = Calendar.getInstance()
        calendar.time = selectedDateValue
        val dayOfWeek = calendar.get(Calendar.DAY_OF_WEEK) - 1 // 0=Sunday, 1=Monday, ..., 6=Saturday

        // Timeline only shows study schedules (not regular tasks)
        // Filter study schedules by day of week
        var filtered = studySchedules
            .filter { schedule -> schedule.day_of_week == dayOfWeek && schedule.is_active }
            .map { schedule -> convertStudyScheduleToTask(schedule, selectedDateValue) }

        android.util.Log.d("CalendarViewModel",
            "Timeline for date: $selectedDateString, Day of week: $dayOfWeek, Study schedules: ${filtered.size}")

        // ステータスでフィルタリング
        filtered = when (currentFilter) {
            FilterType.ALL -> filtered
            FilterType.ACTIVE -> filtered.filter { it.status != "completed" }
            FilterType.COMPLETED -> filtered.filter { it.status == "completed" }
        }

        // Sort by scheduled_time (all study schedules have scheduled_time)
        filtered = filtered.sortedBy { task ->
            task.scheduled_time ?: "99:99:99"
        }

        android.util.Log.d("CalendarViewModel",
            "Timeline showing ${filtered.size} study schedules for $selectedDateString")

        _filteredTasks.value = filtered
    }

    /**
     * Check if two dates are the same day
     */
    private fun isSameDay(date1: Date, date2: Date): Boolean {
        val dateFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
        return dateFormat.format(date1) == dateFormat.format(date2)
    }

    /**
     * Convert StudyScheduleWithPath to Task for unified display
     */
    private fun convertStudyScheduleToTask(schedule: StudyScheduleWithPath, selectedDate: Date): Task {
        val pathTitle = schedule.learning_path?.title ?: "学習"
        val dayName = schedule.getDayNameJapanese()
        val time = schedule.getFormattedTime() // Returns "HH:mm"

        // Format deadline as the selected date
        val dateFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
        val deadline = dateFormat.format(selectedDate)

        // scheduled_time is now TIME type (HH:mm:ss only, no date)
        // Convert HH:mm to HH:mm:ss format
        val scheduledTime = if (time.count { it == ':' } == 1) {
            "$time:00" // Add seconds
        } else {
            time // Already HH:mm:ss format
        }

        return Task(
            id = -schedule.id, // Negative ID to distinguish from regular tasks
            title = "📚 学習スケジュール: $pathTitle",
            category = "study",
            description = "${dayName}曜日 $time - ${schedule.duration_minutes}分",
            status = "pending",
            priority = 5, // High priority for study schedules
            energy_level = "high",
            estimated_minutes = schedule.duration_minutes,
            deadline = deadline,
            scheduled_time = scheduledTime, // Now TIME type (HH:mm:ss)
            created_at = "",
            updated_at = "",
            user_id = 0,
            project_id = null,
            learning_milestone_id = schedule.learning_path_id, // Mark as roadmap task
            ai_breakdown_enabled = false,
            subtasks = null,
            knowledge_items = null
        )
    }

    /**
     * API レスポンスからタスクリストをパース
     * Handles both paginated response and direct list response
     */
    private fun parseTasksFromResponse(data: Any?): List<Task> {
        return try {
            when (data) {
                is Map<*, *> -> {
                    // Paginated response: { data: [...], current_page: 1, ... }
                    val tasksData = data["data"] as? List<*>
                    tasksData?.mapNotNull { item ->
                        when (item) {
                            is Map<*, *> -> convertMapToTask(item)
                            is Task -> item
                            else -> null
                        }
                    } ?: emptyList()
                }
                is List<*> -> {
                    // Direct list response
                    data.mapNotNull { item ->
                        when (item) {
                            is Map<*, *> -> convertMapToTask(item)
                            is Task -> item
                            else -> null
                        }
                    }
                }
                else -> {
                    android.util.Log.w("CalendarViewModel", "Unknown response format: ${data?.javaClass?.simpleName}")
                    emptyList()
                }
            }
        } catch (e: Exception) {
            android.util.Log.e("CalendarViewModel", "Error parsing tasks response", e)
            emptyList()
        }
    }

    /**
     * Map を Task オブジェクトに変換
     */
    private fun convertMapToTask(map: Map<*, *>): Task? {
        return try {
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
                ai_breakdown_enabled = map["ai_breakdown_enabled"] as? Boolean ?: false
            )
        } catch (e: Exception) {
            null
        }
    }

    /**
     * 選択された日付の文字列表現を取得
     */
    fun getSelectedDateString(): String {
        val date = _selectedDate.value ?: Calendar.getInstance().time
        val format = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
        return format.format(date)
    }

    /**
     * 月年の文字列表現を取得
     */
    fun getMonthYearString(): String {
        val date = _selectedDate.value ?: Calendar.getInstance().time
        val format = SimpleDateFormat("MMMM, yyyy", Locale.getDefault())
        return format.format(date)
    }
    
    /**
     * Get dates that have tasks (for calendar decoration)
     * タスクがある日付のリストを取得（カレンダー装飾用）
     */
    fun getDatesWithTasks(): Set<String> {
        val tasks = _allTasks.value ?: emptyList()
        val dateFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
        
        return tasks.mapNotNull { task ->
            if (!task.deadline.isNullOrEmpty()) {
                try {
                    task.deadline.substring(0, 10) // Extract YYYY-MM-DD
                } catch (e: Exception) {
                    null
                }
            } else {
                null
            }
        }.toSet()
    }
    
    /**
     * Get task count for a specific date
     * 特定の日付のタスク数を取得
     */
    fun getTaskCountForDate(date: Date): Int {
        val tasks = _allTasks.value ?: emptyList()
        val dateFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
        val dateString = dateFormat.format(date)
        
        return tasks.count { task ->
            if (!task.deadline.isNullOrEmpty()) {
                try {
                    val taskDate = task.deadline.substring(0, 10)
                    taskDate == dateString
                } catch (e: Exception) {
                    false
                }
            } else {
                false
            }
        }
    }
    
    /**
     * Get tasks for date range (for monthly view)
     * 期間内のタスクを取得（月表示用）
     */
    fun getTasksForDateRange(startDate: Date, endDate: Date): Map<String, List<Task>> {
        val tasks = _allTasks.value ?: emptyList()
        val dateFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
        val result = mutableMapOf<String, MutableList<Task>>()
        
        tasks.forEach { task ->
            if (!task.deadline.isNullOrEmpty()) {
                try {
                    val taskDateString = task.deadline.substring(0, 10)
                    val taskDate = dateFormat.parse(taskDateString)
                    
                    if (taskDate != null && !taskDate.before(startDate) && !taskDate.after(endDate)) {
                        if (!result.containsKey(taskDateString)) {
                            result[taskDateString] = mutableListOf()
                        }
                        result[taskDateString]?.add(task)
                    }
                } catch (e: Exception) {
                    // Skip invalid dates
                }
            }
        }
        
        return result
    }
    
    /**
     * Check if selected date is today
     * 選択された日付が今日かどうか
     */
    fun isSelectedDateToday(): Boolean {
        val selectedDate = _selectedDate.value ?: return false
        val today = Calendar.getInstance().time
        val dateFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
        
        return dateFormat.format(selectedDate) == dateFormat.format(today)
    }
}

