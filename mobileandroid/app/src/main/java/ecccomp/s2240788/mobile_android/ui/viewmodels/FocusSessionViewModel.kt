package ecccomp.s2240788.mobile_android.ui.viewmodels

import android.os.CountDownTimer
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.Task
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

/**
 * FocusSessionViewModel
 * タイマー管理とFocus Session のビジネスロジック
 * - Pomodoro Timer (25分作業 + 5分休憩)
 * - 複数の時間設定 (5分、25分、45分)
 * - Session データの保存
 */
class FocusSessionViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    // Timer state
    private var countDownTimer: CountDownTimer? = null
    private var timeRemainingMillis: Long = 25 * 60 * 1000 // Default: 25 minutes
    private var totalTimeMillis: Long = 25 * 60 * 1000
    
    private val _timerDisplay = MutableLiveData<String>()
    val timerDisplay: LiveData<String> = _timerDisplay

    private val _progress = MutableLiveData<Int>()
    val progress: LiveData<Int> = _progress

    private val _isTimerRunning = MutableLiveData<Boolean>(false)
    val isTimerRunning: LiveData<Boolean> = _isTimerRunning

    private val _timerMode = MutableLiveData<TimerMode>(TimerMode.WORK)
    val timerMode: LiveData<TimerMode> = _timerMode

    // Task info
    private val _currentTask = MutableLiveData<Task?>()
    val currentTask: LiveData<Task?> = _currentTask

    private val _focusSessionCompleted = MutableLiveData<Boolean>()
    val focusSessionCompleted: LiveData<Boolean> = _focusSessionCompleted

    // Notification & messages
    private val _toast = MutableLiveData<String?>()
    val toast: LiveData<String?> = _toast

    // Session stats
    private var sessionStartTimeMillis: Long = 0
    private var pomodoroCount: Int = 0

    enum class TimerMode {
        WORK, SHORT_BREAK, LONG_BREAK
    }

    init {
        updateTimerDisplay(timeRemainingMillis)
        updateProgress()
    }

    /**
     * タスク情報を設定
     */
    fun setTask(task: Task) {
        _currentTask.value = task
    }

    /**
     * タスクIDからタスク情報を取得
     */
    fun loadTask(taskId: Int) {
        viewModelScope.launch {
            try {
                val response = apiService.getTasks()
                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        val tasks = parseTasksFromResponse(apiResponse.data)
                        val task = tasks.find { it.id == taskId }
                        if (task != null) {
                            _currentTask.value = task
                        } else {
                            _toast.value = "タスクが見つかりません"
                        }
                    }
                }
            } catch (e: Exception) {
                _toast.value = "エラーが発生しました: ${e.message}"
            }
        }
    }

    /**
     * タイマーを開始
     */
    fun startTimer() {
        if (_isTimerRunning.value == true) return

        sessionStartTimeMillis = System.currentTimeMillis()
        _isTimerRunning.value = true

        countDownTimer = object : CountDownTimer(timeRemainingMillis, 1000) {
            override fun onTick(millisUntilFinished: Long) {
                timeRemainingMillis = millisUntilFinished
                updateTimerDisplay(millisUntilFinished)
                updateProgress()
            }

            override fun onFinish() {
                onTimerComplete()
            }
        }.start()
    }

    /**
     * タイマーを一時停止
     */
    fun pauseTimer() {
        countDownTimer?.cancel()
        _isTimerRunning.value = false
    }

    /**
     * タイマーをリセット
     */
    fun resetTimer() {
        countDownTimer?.cancel()
        _isTimerRunning.value = false
        timeRemainingMillis = totalTimeMillis
        updateTimerDisplay(timeRemainingMillis)
        updateProgress()
    }

    /**
     * タイマー時間を設定 (分単位)
     */
    fun setTimerDuration(minutes: Int) {
        if (_isTimerRunning.value == true) {
            pauseTimer()
        }
        totalTimeMillis = minutes * 60 * 1000L
        timeRemainingMillis = totalTimeMillis
        updateTimerDisplay(timeRemainingMillis)
        updateProgress()
    }

    /**
     * セッションをスキップ
     */
    fun skipSession() {
        countDownTimer?.cancel()
        _isTimerRunning.value = false
        onTimerComplete()
    }

    /**
     * タイマー完了時の処理
     */
    private fun onTimerComplete() {
        _isTimerRunning.value = false
        
        when (_timerMode.value) {
            TimerMode.WORK -> {
                pomodoroCount++
                saveFocusSession()
                
                // 4ポモドーロごとに長い休憩
                if (pomodoroCount % 4 == 0) {
                    switchToBreakMode(TimerMode.LONG_BREAK, 15)
                    _toast.value = "お疲れ様でした！長めの休憩を取りましょう (15分)"
                } else {
                    switchToBreakMode(TimerMode.SHORT_BREAK, 5)
                    _toast.value = "お疲れ様でした！短い休憩を取りましょう (5分)"
                }
            }
            TimerMode.SHORT_BREAK, TimerMode.LONG_BREAK -> {
                switchToWorkMode()
                _toast.value = "休憩終了！次のポモドーロを始めましょう"
            }
            else -> {}
        }
        
        _focusSessionCompleted.value = true
    }

    /**
     * 作業モードに切り替え
     */
    private fun switchToWorkMode() {
        _timerMode.value = TimerMode.WORK
        setTimerDuration(25) // Default 25 minutes
    }

    /**
     * 休憩モードに切り替え
     */
    private fun switchToBreakMode(mode: TimerMode, minutes: Int) {
        _timerMode.value = mode
        setTimerDuration(minutes)
    }

    /**
     * Focus Session をAPIに保存
     */
    private fun saveFocusSession() {
        val task = _currentTask.value ?: return
        val durationMinutes = ((totalTimeMillis - timeRemainingMillis) / 1000 / 60).toInt()

        viewModelScope.launch {
            try {
                val sessionType = when (_timerMode.value) {
                    TimerMode.WORK -> "work"
                    TimerMode.SHORT_BREAK -> "break"
                    TimerMode.LONG_BREAK -> "long_break"
                    else -> "work"
                }

                val request = ecccomp.s2240788.mobile_android.data.models.StartFocusSessionRequest(
                    task_id = task.id,
                    duration_minutes = durationMinutes,
                    session_type = sessionType
                )
                val response = apiService.startFocusSession(request)

                if (response.isSuccessful && response.body()?.success == true) {
                    val totalMinutes = pomodoroCount * 25 // Each pomodoro is 25 minutes
                    _toast.value = "Focus Session を保存しました (${totalMinutes}分)"
                } else {
                    _toast.value = "保存に失敗しました: ${response.body()?.message}"
                }
            } catch (e: Exception) {
                _toast.value = "保存に失敗しました: ${e.message}"
            }
        }
    }
    
    /**
     * メモ付きでFocus Session を保存
     */
    fun saveFocusSessionWithNotes(notes: String?) {
        val task = _currentTask.value ?: return
        val durationMinutes = ((totalTimeMillis - timeRemainingMillis) / 1000 / 60).toInt()

        viewModelScope.launch {
            try {
                val sessionType = when (_timerMode.value) {
                    TimerMode.WORK -> "work"
                    TimerMode.SHORT_BREAK -> "break"
                    TimerMode.LONG_BREAK -> "long_break"
                    else -> "work"
                }

                val request = ecccomp.s2240788.mobile_android.data.models.StartFocusSessionRequest(
                    task_id = task.id,
                    duration_minutes = durationMinutes,
                    session_type = sessionType
                )
                val response = apiService.startFocusSession(request)

                if (response.isSuccessful && response.body()?.success == true) {
                    _toast.value = "Focus Session を保存しました"
                } else {
                    _toast.value = "保存に失敗しました: ${response.body()?.message}"
                }
            } catch (e: Exception) {
                _toast.value = "保存に失敗しました: ${e.message}"
            }
        }
    }

    /**
     * タイマー表示を更新 (MM:SS形式)
     */
    private fun updateTimerDisplay(millisUntilFinished: Long) {
        val minutes = (millisUntilFinished / 1000) / 60
        val seconds = (millisUntilFinished / 1000) % 60
        _timerDisplay.value = String.format("%02d:%02d", minutes, seconds)
    }

    /**
     * プログレスバーを更新 (0-100)
     */
    private fun updateProgress() {
        val progressPercent = ((totalTimeMillis - timeRemainingMillis) * 100 / totalTimeMillis).toInt()
        _progress.value = progressPercent
    }

    /**
     * API レスポンスからタスクリストをパース
     */
    private fun parseTasksFromResponse(data: Any?): List<Task> {
        return try {
            when (data) {
                is Map<*, *> -> {
                    // Handle nested structure: { data: [...] }
                    val tasksData = data["data"] as? List<*>
                    tasksData?.mapNotNull { item ->
                        if (item is Map<*, *>) convertMapToTask(item) else null
                    } ?: emptyList()
                }
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
     * Pomodoro カウントを取得
     */
    fun getPomodoroCount(): Int = pomodoroCount

    /**
     * Toast メッセージをクリア
     */
    fun clearToast() {
        _toast.value = null
    }

    /**
     * Session completed イベントをクリア
     */
    fun clearSessionCompleted() {
        _focusSessionCompleted.value = false
    }

    override fun onCleared() {
        super.onCleared()
        countDownTimer?.cancel()
    }
}

