package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.*
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

/**
 * StudyScheduleViewModel
 * スケジュール学習管理ViewModel
 *
 * Purpose: Manage study schedule setup and tracking
 */
class StudyScheduleViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    // Schedule setup data
    private val _selectedDays = MutableLiveData<MutableSet<Int>>(mutableSetOf())
    val selectedDays: LiveData<MutableSet<Int>> = _selectedDays

    private val _selectedTime = MutableLiveData<String>("19:30")
    val selectedTime: LiveData<String> = _selectedTime

    private val _durationMinutes = MutableLiveData<Int>(60)
    val durationMinutes: LiveData<Int> = _durationMinutes

    private val _reminderEnabled = MutableLiveData<Boolean>(true)
    val reminderEnabled: LiveData<Boolean> = _reminderEnabled

    private val _reminderBeforeMinutes = MutableLiveData<Int>(30)
    val reminderBeforeMinutes: LiveData<Int> = _reminderBeforeMinutes

    // Schedules list
    private val _schedules = MutableLiveData<List<StudySchedule>>()
    val schedules: LiveData<List<StudySchedule>> = _schedules

    private val _todaySessions = MutableLiveData<TodaySessionsResponse?>()
    val todaySessions: LiveData<TodaySessionsResponse?> = _todaySessions

    private val _stats = MutableLiveData<StudyScheduleStats?>()
    val stats: LiveData<StudyScheduleStats?> = _stats

    // UI State
    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    private val _successMessage = MutableLiveData<String?>()
    val successMessage: LiveData<String?> = _successMessage

    /**
     * Toggle day selection
     */
    fun toggleDay(dayOfWeek: Int) {
        val current = _selectedDays.value ?: mutableSetOf()
        if (current.contains(dayOfWeek)) {
            current.remove(dayOfWeek)
        } else {
            current.add(dayOfWeek)
        }
        _selectedDays.value = current
    }

    /**
     * Set study time
     */
    fun setStudyTime(time: String) {
        _selectedTime.value = time
    }

    /**
     * Set duration in minutes
     */
    fun setDuration(minutes: Int) {
        _durationMinutes.value = minutes
    }

    /**
     * Set reminder settings
     */
    fun setReminderEnabled(enabled: Boolean) {
        _reminderEnabled.value = enabled
    }

    fun setReminderBeforeMinutes(minutes: Int) {
        _reminderBeforeMinutes.value = minutes
    }

    /**
     * Get schedule inputs for API request
     */
    fun getScheduleInputs(): List<StudyScheduleInput> {
        val days = _selectedDays.value ?: emptySet()
        val time = _selectedTime.value ?: "19:30"
        val duration = _durationMinutes.value ?: 60
        val reminderEnabled = _reminderEnabled.value ?: true
        val reminderBefore = _reminderBeforeMinutes.value ?: 30

        return days.map { day ->
            StudyScheduleInput(
                day_of_week = day,
                study_time = time,
                duration_minutes = duration,
                reminder_enabled = reminderEnabled,
                reminder_before_minutes = reminderBefore
            )
        }
    }

    /**
     * Validate schedule inputs
     */
    fun validateSchedules(): String? {
        val days = _selectedDays.value ?: emptySet()
        if (days.isEmpty()) {
            return "Vui lòng chọn ít nhất 1 ngày học"
        }

        val time = _selectedTime.value
        if (time.isNullOrBlank()) {
            return "Vui lòng chọn giờ học"
        }

        val duration = _durationMinutes.value ?: 0
        if (duration < 15) {
            return "Thời lượng tối thiểu là 15 phút"
        }

        return null // Valid
    }

    /**
     * Calculate total study hours per week
     */
    fun getWeeklyStudyHours(): Float {
        val days = _selectedDays.value?.size ?: 0
        val duration = _durationMinutes.value ?: 60
        val totalMinutes = days * duration
        return totalMinutes / 60f
    }

    /**
     * Get summary text for preview
     */
    fun getSummaryText(): String {
        val days = _selectedDays.value?.size ?: 0
        val hours = getWeeklyStudyHours()
        return "$days buổi/tuần • ${String.format("%.1f", hours)} giờ/tuần"
    }

    /**
     * Reset to default values
     */
    fun reset() {
        _selectedDays.value = mutableSetOf()
        _selectedTime.value = "19:30"
        _durationMinutes.value = 60
        _reminderEnabled.value = true
        _reminderBeforeMinutes.value = 30
    }

    /**
     * Fetch schedules for a learning path
     */
    fun fetchSchedules(learningPathId: Int) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.getStudySchedules(learningPathId)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        // Parse schedules from data
                        val data = apiResponse.data
                        if (data is Map<*, *>) {
                            @Suppress("UNCHECKED_CAST")
                            val schedulesList = (data["schedules"] as? List<*>)?.mapNotNull { item ->
                                // Simple parsing - in production use proper deserialization
                                null
                            } ?: emptyList()
                            _schedules.value = schedulesList
                        }
                    } else {
                        _error.value = apiResponse?.message ?: "スケジュールの取得に失敗しました"
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
     * Create study schedule
     */
    fun createSchedule(learningPathId: Int, request: CreateStudyScheduleRequest) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.createStudySchedule(learningPathId, request)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        _successMessage.value = "スケジュールを追加しました"
                        fetchSchedules(learningPathId) // Refresh list
                    } else {
                        _error.value = apiResponse?.message ?: "スケジュールの作成に失敗しました"
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
     * Delete study schedule
     */
    fun deleteSchedule(scheduleId: Int, learningPathId: Int) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.deleteStudySchedule(scheduleId)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        _successMessage.value = "スケジュールを削除しました"
                        fetchSchedules(learningPathId) // Refresh list
                    } else {
                        _error.value = apiResponse?.message ?: "スケジュールの削除に失敗しました"
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
     * Mark session as completed
     */
    fun markCompleted(scheduleId: Int) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.markScheduleCompleted(scheduleId)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        _successMessage.value = "学習セッションを完了しました！"
                        fetchTodaySessions() // Refresh today's sessions
                    } else {
                        _error.value = apiResponse?.message ?: "セッションの完了に失敗しました"
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
     * Fetch today's sessions
     */
    fun fetchTodaySessions() {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.getTodaySessions()

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        _todaySessions.value = apiResponse.data
                    } else {
                        _error.value = apiResponse?.message
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
     * Fetch statistics
     */
    fun fetchStats() {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.getStudyScheduleStats()

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        _stats.value = apiResponse.data
                    } else {
                        _error.value = apiResponse?.message
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
     * Clear error message
     */
    fun clearError() {
        _error.value = null
    }

    /**
     * Clear success message
     */
    fun clearSuccessMessage() {
        _successMessage.value = null
    }
}
