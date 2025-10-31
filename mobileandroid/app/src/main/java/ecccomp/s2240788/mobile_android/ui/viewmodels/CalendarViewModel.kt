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

                val response = apiService.getTasks()

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        val tasks = parseTasksFromResponse(apiResponse.data)
                        _allTasks.value = tasks
                        applyFilter()
                    } else {
                        _error.value = apiResponse?.message ?: "タスクの取得に失敗しました"
                    }
                } else {
                    _error.value = "ネットワークエラー: ${response.code()}"
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
                false
            } else {
                try {
                    val taskDate = task.deadline.substring(0, 10) // YYYY-MM-DD部分を抽出
                    taskDate == selectedDateString
                } catch (e: Exception) {
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

        _filteredTasks.value = filtered
    }

    /**
     * API レスポンスからタスクリストをパース
     */
    private fun parseTasksFromResponse(data: Any?): List<Task> {
        return try {
            when (data) {
                is List<*> -> data.mapNotNull { item ->
                    when (item) {
                        is Map<*, *> -> convertMapToTask(item)
                        is Task -> item
                        else -> null
                    }
                }
                else -> emptyList()
            }
        } catch (e: Exception) {
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
}

