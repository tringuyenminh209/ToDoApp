package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.*
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.*

/**
 * ViewModel for Timetable Screen
 * 時間割画面のビジネスロジック
 */
class TimetableViewModel : ViewModel() {
    
    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )
    
    // Classes list
    private val _classes = MutableLiveData<List<TimetableClass>>()
    val classes: LiveData<List<TimetableClass>> = _classes
    
    // Studies list
    private val _studies = MutableLiveData<List<TimetableStudy>>()
    val studies: LiveData<List<TimetableStudy>> = _studies
    
    // Current class
    private val _currentClass = MutableLiveData<TimetableClass?>()
    val currentClass: LiveData<TimetableClass?> = _currentClass
    
    // Next class
    private val _nextClass = MutableLiveData<TimetableClass?>()
    val nextClass: LiveData<TimetableClass?> = _nextClass
    
    // Current status
    private val _currentStatus = MutableLiveData<TimetableStatus>()
    val currentStatus: LiveData<TimetableStatus> = _currentStatus
    
    // Loading state
    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading
    
    // Error message
    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error
    
    // Current week offset (0 = current week, -1 = previous week, +1 = next week)
    private var currentWeekOffset = 0
    
    // Week info
    private val _weekInfo = MutableLiveData<String>()
    val weekInfo: LiveData<String> = _weekInfo
    
    init {
        loadTimetable()
        updateWeekInfo()
    }
    
    /**
     * Load timetable data from API
     * 時間割データをAPIから取得
     */
    fun loadTimetable() {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null
                
                // Calculate year and week based on offset
                val calendar = Calendar.getInstance()
                calendar.add(Calendar.WEEK_OF_YEAR, currentWeekOffset)
                val year = calendar.get(Calendar.YEAR)
                val weekNumber = calendar.get(Calendar.WEEK_OF_YEAR)
                
                val response = apiService.getTimetable(year, weekNumber)
                
                if (response.isSuccessful) {
                    val timetableData = response.body()?.data
                    if (timetableData != null) {
                        _classes.value = timetableData.classes
                        _studies.value = timetableData.studies
                        _currentClass.value = timetableData.currentClass
                        _nextClass.value = timetableData.nextClass
                        
                        updateCurrentStatus()
                    }
                } else {
                    _error.value = "Failed to load timetable: ${response.message()}"
                }
            } catch (e: Exception) {
                _error.value = "Error loading timetable: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }
    
    /**
     * Load only classes
     * クラス一覧のみ取得
     */
    fun loadClasses() {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null
                
                val response = apiService.getTimetableClasses()
                
                if (response.isSuccessful) {
                    _classes.value = response.body()?.data ?: emptyList()
                } else {
                    _error.value = "Failed to load classes: ${response.message()}"
                }
            } catch (e: Exception) {
                _error.value = "Error loading classes: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }
    
    /**
     * Load only studies
     * 宿題・課題一覧のみ取得
     */
    fun loadStudies(status: String? = null) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null
                
                val response = apiService.getTimetableStudies(status)
                
                if (response.isSuccessful) {
                    _studies.value = response.body()?.data ?: emptyList()
                } else {
                    _error.value = "Failed to load studies: ${response.message()}"
                }
            } catch (e: Exception) {
                _error.value = "Error loading studies: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }
    
    /**
     * Update current class status
     * 現在の授業ステータスを更新
     */
    private fun updateCurrentStatus() {
        _currentStatus.value = when {
            _currentClass.value != null -> TimetableStatus.ACTIVE
            _nextClass.value != null -> TimetableStatus.NEXT
            else -> TimetableStatus.BREAK
        }
    }
    
    /**
     * Get class by day and period
     * 曜日と時限から授業を取得
     */
    fun getClassByDayAndPeriod(day: String, period: Int): TimetableClass? {
        return _classes.value?.find { it.day == day && it.period == period }
    }
    
    /**
     * Add new class
     * 新しい授業を追加
     */
    fun addClass(request: CreateTimetableClassRequest, onSuccess: () -> Unit = {}) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null
                
                val response = apiService.createTimetableClass(request)
                
                if (response.isSuccessful) {
                    loadClasses() // Reload to get updated list
                    onSuccess()
                } else {
                    _error.value = "Failed to add class: ${response.message()}"
                }
            } catch (e: Exception) {
                _error.value = "Error adding class: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }
    
    /**
     * Create class with individual parameters (convenience method)
     * パラメータ指定で授業を作成（便利メソッド）
     */
    fun createClass(
        name: String,
        description: String? = null,
        room: String? = null,
        instructor: String? = null,
        day: String,
        period: Int,
        startTime: String,
        endTime: String,
        color: String? = null,
        icon: String? = null,
        notes: String? = null,
        learningPathId: Int? = null,
        onSuccess: () -> Unit = {}
    ) {
        val request = CreateTimetableClassRequest(
            name = name,
            description = description,
            room = room,
            instructor = instructor,
            day = day,
            period = period,
            startTime = startTime,
            endTime = endTime,
            color = color,
            icon = icon,
            notes = notes,
            learningPathId = learningPathId
        )
        addClass(request, onSuccess)
    }
    
    /**
     * Update class
     * 授業を更新
     */
    fun updateClass(id: Int, request: CreateTimetableClassRequest, onSuccess: () -> Unit = {}) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null
                
                val response = apiService.updateTimetableClass(id, request)
                
                if (response.isSuccessful) {
                    loadClasses() // Reload to get updated list
                    onSuccess()
                } else {
                    _error.value = "Failed to update class: ${response.message()}"
                }
            } catch (e: Exception) {
                _error.value = "Error updating class: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }
    
    /**
     * Delete class
     * 授業を削除
     */
    fun deleteClass(id: Int, onSuccess: () -> Unit = {}) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null
                
                val response = apiService.deleteTimetableClass(id)
                
                if (response.isSuccessful) {
                    loadClasses() // Reload to get updated list
                    onSuccess()
                } else {
                    _error.value = "Failed to delete class: ${response.message()}"
                }
            } catch (e: Exception) {
                _error.value = "Error deleting class: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }
    
    /**
     * Add study
     * 宿題・課題を追加
     */
    fun addStudy(request: CreateTimetableStudyRequest, onSuccess: () -> Unit = {}) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null
                
                val response = apiService.createTimetableStudy(request)
                
                if (response.isSuccessful) {
                    loadStudies() // Reload to get updated list
                    onSuccess()
                } else {
                    _error.value = "Failed to add study: ${response.message()}"
                }
            } catch (e: Exception) {
                _error.value = "Error adding study: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }
    
    /**
     * Toggle study completion
     * 宿題・課題の完了状態をトグル
     */
    fun toggleStudyCompletion(id: Int, onSuccess: () -> Unit = {}) {
        viewModelScope.launch {
            try {
                _error.value = null
                
                val response = apiService.toggleTimetableStudy(id)
                
                if (response.isSuccessful) {
                    loadStudies() // Reload to get updated list
                    onSuccess()
                } else {
                    _error.value = "Failed to toggle study: ${response.message()}"
                }
            } catch (e: Exception) {
                _error.value = "Error toggling study: ${e.message}"
            }
        }
    }
    
    /**
     * Delete study
     * 宿題・課題を削除
     */
    fun deleteStudy(id: Int, onSuccess: () -> Unit = {}) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null
                
                val response = apiService.deleteTimetableStudy(id)
                
                if (response.isSuccessful) {
                    loadStudies() // Reload to get updated list
                    onSuccess()
                } else {
                    _error.value = "Failed to delete study: ${response.message()}"
                }
            } catch (e: Exception) {
                _error.value = "Error deleting study: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }
    
    /**
     * Get weekly content for a class
     * 授業の週別内容を取得
     */
    fun getWeeklyContent(classId: Int, onSuccess: (TimetableClassWeeklyContent?) -> Unit = {}) {
        viewModelScope.launch {
            try {
                _error.value = null
                
                // Calculate year and week based on offset
                val calendar = Calendar.getInstance()
                calendar.add(Calendar.WEEK_OF_YEAR, currentWeekOffset)
                val year = calendar.get(Calendar.YEAR)
                val weekNumber = calendar.get(Calendar.WEEK_OF_YEAR)
                
                val response = apiService.getWeeklyContent(classId, year, weekNumber)
                
                if (response.isSuccessful) {
                    onSuccess(response.body()?.data)
                } else {
                    _error.value = "Failed to get weekly content: ${response.message()}"
                    onSuccess(null)
                }
            } catch (e: Exception) {
                _error.value = "Error getting weekly content: ${e.message}"
                onSuccess(null)
            }
        }
    }
    
    /**
     * Update weekly content for a class
     * 授業の週別内容を更新
     */
    fun updateWeeklyContent(
        classId: Int,
        title: String? = null,
        content: String? = null,
        homework: String? = null,
        notes: String? = null,
        status: String? = null,
        onSuccess: () -> Unit = {}
    ) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null
                
                // Calculate year and week based on offset
                val calendar = Calendar.getInstance()
                calendar.add(Calendar.WEEK_OF_YEAR, currentWeekOffset)
                val year = calendar.get(Calendar.YEAR)
                val weekNumber = calendar.get(Calendar.WEEK_OF_YEAR)
                
                // Calculate week start date (Monday)
                calendar.set(Calendar.DAY_OF_WEEK, Calendar.MONDAY)
                val dateFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
                val weekStartDate = dateFormat.format(calendar.time)
                
                val request = UpdateWeeklyContentRequest(
                    year = year,
                    weekNumber = weekNumber,
                    weekStartDate = weekStartDate,
                    title = title,
                    content = content,
                    homework = homework,
                    notes = notes,
                    status = status
                )
                
                val response = apiService.updateWeeklyContent(classId, request)
                
                if (response.isSuccessful) {
                    loadTimetable() // Reload to get updated data
                    onSuccess()
                } else {
                    _error.value = "Failed to update weekly content: ${response.message()}"
                }
            } catch (e: Exception) {
                _error.value = "Error updating weekly content: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }
    
    /**
     * Delete weekly content
     * 週別内容を削除
     */
    fun deleteWeeklyContent(weeklyContentId: Int, onSuccess: () -> Unit = {}) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null
                
                val response = apiService.deleteWeeklyContent(weeklyContentId)
                
                if (response.isSuccessful) {
                    loadTimetable() // Reload to get updated data
                    onSuccess()
                } else {
                    _error.value = "Failed to delete weekly content: ${response.message()}"
                }
            } catch (e: Exception) {
                _error.value = "Error deleting weekly content: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }
    
    /**
     * Get remaining time for current class (in minutes)
     * 現在の授業の残り時間（分）
     */
    fun getRemainingTime(): Int {
        val current = _currentClass.value ?: return 0
        
        return try {
            val calendar = Calendar.getInstance()
            val currentHour = calendar.get(Calendar.HOUR_OF_DAY)
            val currentMinute = calendar.get(Calendar.MINUTE)
            val currentTime = currentHour * 60 + currentMinute
            
            // Parse end time from "HH:mm" format
            val timeFormat = SimpleDateFormat("HH:mm", Locale.getDefault())
            val endDate = timeFormat.parse(current.endTime)
            val endCalendar = Calendar.getInstance().apply {
                time = endDate ?: return 0
            }
            val endTime = endCalendar.get(Calendar.HOUR_OF_DAY) * 60 + endCalendar.get(Calendar.MINUTE)
            
            maxOf(0, endTime - currentTime)
        } catch (e: Exception) {
            0
        }
    }
    
    /**
     * Get time until next class (in minutes)
     * 次の授業までの時間（分）
     */
    fun getTimeUntilNextClass(): Int {
        val next = _nextClass.value ?: return 0
        
        return try {
            val calendar = Calendar.getInstance()
            val currentHour = calendar.get(Calendar.HOUR_OF_DAY)
            val currentMinute = calendar.get(Calendar.MINUTE)
            val currentTime = currentHour * 60 + currentMinute
            
            // Parse start time from "HH:mm" format
            val timeFormat = SimpleDateFormat("HH:mm", Locale.getDefault())
            val startDate = timeFormat.parse(next.startTime)
            val startCalendar = Calendar.getInstance().apply {
                time = startDate ?: return 0
            }
            val startTime = startCalendar.get(Calendar.HOUR_OF_DAY) * 60 + startCalendar.get(Calendar.MINUTE)
            
            maxOf(0, startTime - currentTime)
        } catch (e: Exception) {
            0
        }
    }
    
    /**
     * Navigate to previous week
     * 前の週に移動
     */
    fun navigateToPreviousWeek() {
        currentWeekOffset--
        updateWeekInfo()
        // Filter classes for the selected week
        filterClassesByWeek()
    }
    
    /**
     * Navigate to next week
     * 次の週に移動
     */
    fun navigateToNextWeek() {
        currentWeekOffset++
        updateWeekInfo()
        // Filter classes for the selected week
        filterClassesByWeek()
    }
    
    /**
     * Update week info display
     * 週情報を更新
     */
    private fun updateWeekInfo() {
        val calendar = Calendar.getInstance()
        
        // Add week offset
        calendar.add(Calendar.WEEK_OF_YEAR, currentWeekOffset)
        
        // Get week number and month
        val weekNumber = calendar.get(Calendar.WEEK_OF_YEAR)
        val month = calendar.get(Calendar.MONTH) + 1 // 0-based
        
        // Get start of week (Monday)
        calendar.set(Calendar.DAY_OF_WEEK, Calendar.MONDAY)
        val weekStartDay = calendar.get(Calendar.DAY_OF_MONTH)
        val weekStartMonth = calendar.get(Calendar.MONTH) + 1
        
        // Get end of week (Sunday)
        calendar.add(Calendar.DAY_OF_WEEK, 6)
        val weekEndDay = calendar.get(Calendar.DAY_OF_MONTH)
        val weekEndMonth = calendar.get(Calendar.MONTH) + 1
        val weekEndYear = calendar.get(Calendar.YEAR)
        
        // Format: "第X週 - Y月" and "DD/MM - DD/MM/YYYY"
        val weekTitle = "第${weekNumber}週 - ${month}月"
        val dateRange = "${weekStartDay}/${weekStartMonth} - ${weekEndDay}/${weekEndMonth}/${weekEndYear}"
        
        _weekInfo.value = "$weekTitle\n$dateRange"
    }
    
    /**
     * Get current week info string
     * 現在の週情報を取得
     */
    fun getCurrentWeekInfo(): String {
        return _weekInfo.value ?: ""
    }
    
    /**
     * Filter classes by current week offset
     * Note: This is a client-side filter. Ideally backend should support week parameter
     */
    private fun filterClassesByWeek() {
        // For now, show all classes since backend doesn't support week filtering
        // In future, add week parameter to API call
        loadTimetable()
    }
}

/**
 * Enum for timetable status
 * 時間割ステータス
 */
enum class TimetableStatus {
    ACTIVE,  // Currently in a class / 授業中
    NEXT,    // Next class coming up / 次の授業がある
    BREAK    // Break time / No class / 休憩時間
}
