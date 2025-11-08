package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.Task
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch
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
     */
    fun fetchTasks() {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                // Request all tasks (request up to 100 tasks for calendar view)
                val response = apiService.getTasks(perPage = 100)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    android.util.Log.d("CalendarViewModel", "API Response: success=${apiResponse?.success}, data type=${apiResponse?.data?.javaClass?.simpleName}")
                    
                    if (apiResponse?.success == true) {
                        val tasks = parseTasksFromResponse(apiResponse.data)
                        android.util.Log.d("CalendarViewModel", "Loaded ${tasks.size} tasks from API")
                        
                        // Debug: Log tasks with deadlines
                        tasks.forEach { task ->
                            if (!task.deadline.isNullOrEmpty()) {
                                android.util.Log.d("CalendarViewModel", 
                                    "Task '${task.title}' has deadline: ${task.deadline}")
                            }
                        }
                        
                        _allTasks.value = tasks
                        applyFilter()
                    } else {
                        val errorMsg = apiResponse?.message ?: "タスクの取得に失敗しました"
                        android.util.Log.e("CalendarViewModel", "API Error: $errorMsg")
                        _error.value = errorMsg
                    }
                } else {
                    val errorMsg = "ネットワークエラー: ${response.code()} - ${response.message()}"
                    android.util.Log.e("CalendarViewModel", errorMsg)
                    _error.value = errorMsg
                }
            } catch (e: Exception) {
                _error.value = "エラーが発生しました: ${e.message}"
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
     */
    private fun applyFilter() {
        val tasks = _allTasks.value ?: emptyList()
        val selectedDateValue = _selectedDate.value ?: Calendar.getInstance().time

        // 日付フォーマッター
        val dateFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
        val selectedDateString = dateFormat.format(selectedDateValue)

        // 日付でフィルタリング
        var filtered = tasks.filter { task ->
            if (task.deadline.isNullOrEmpty()) {
                // Tasks without deadline: show for today only if not completed
                val isToday = dateFormat.format(Calendar.getInstance().time) == selectedDateString
                if (isToday && task.status != "completed") {
                    true // Show pending tasks without deadline for today
                } else {
                    false
                }
            } else {
                try {
                    // Handle different deadline formats: "yyyy-MM-dd" or "yyyy-MM-dd HH:mm:ss"
                    val deadlineStr = task.deadline.trim()
                    val taskDate = if (deadlineStr.length >= 10) {
                        deadlineStr.substring(0, 10) // Extract YYYY-MM-DD part
                    } else {
                        deadlineStr
                    }
                    
                    // Match exact date
                    val matches = taskDate == selectedDateString
                    
                    // Debug logging (can be removed later)
                    if (!matches && taskDate.isNotEmpty()) {
                        android.util.Log.d("CalendarViewModel", 
                            "Task '${task.title}' deadline '$taskDate' != selected '$selectedDateString'")
                    }
                    
                    matches
                } catch (e: Exception) {
                    android.util.Log.e("CalendarViewModel", "Error parsing deadline: ${task.deadline}", e)
                    false
                }
            }
        }

        // ステータスでフィルタリング
        filtered = when (currentFilter) {
            FilterType.ALL -> filtered
            FilterType.ACTIVE -> filtered.filter { it.status != "completed" }
            FilterType.COMPLETED -> filtered.filter { it.status == "completed" }
        }

        android.util.Log.d("CalendarViewModel", 
            "Filtered ${filtered.size} tasks for date $selectedDateString (from ${tasks.size} total tasks)")

        _filteredTasks.value = filtered
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

