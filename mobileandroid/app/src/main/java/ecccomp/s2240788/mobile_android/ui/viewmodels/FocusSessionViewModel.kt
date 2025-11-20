package ecccomp.s2240788.mobile_android.ui.viewmodels

import android.os.CountDownTimer
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.Task
import ecccomp.s2240788.mobile_android.data.models.Subtask
import ecccomp.s2240788.mobile_android.data.models.KnowledgeItem
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

    // Track current subtask ID if focusing on specific subtask
    private var currentSubtaskId: Int? = null
    private var parentTaskId: Int? = null

    // Deep Work Mode
    private val _isDeepWorkMode = MutableLiveData<Boolean>(false)
    val isDeepWorkMode: LiveData<Boolean> = _isDeepWorkMode

    private val _focusSessionCompleted = MutableLiveData<Boolean>()
    val focusSessionCompleted: LiveData<Boolean> = _focusSessionCompleted

    // Notification & messages
    private val _toast = MutableLiveData<String?>()
    val toast: LiveData<String?> = _toast

    // Knowledge items
    private val _knowledgeItems = MutableLiveData<List<KnowledgeItem>>()
    val knowledgeItems: LiveData<List<KnowledgeItem>> = _knowledgeItems

    private val _isLoadingKnowledge = MutableLiveData<Boolean>(false)
    val isLoadingKnowledge: LiveData<Boolean> = _isLoadingKnowledge

    // Subtasks tracking
    private val _subtasks = MutableLiveData<List<Subtask>>()
    val subtasks: LiveData<List<Subtask>> = _subtasks

    // Track elapsed time for each subtask (subtask_id -> elapsed seconds)
    private val subtaskElapsedSeconds = mutableMapOf<Int, Int>()
    private val _subtaskElapsedMinutes = MutableLiveData<Map<Int, Int>>()
    val subtaskElapsedMinutes: LiveData<Map<Int, Int>> = _subtaskElapsedMinutes

    // Track which subtasks have been auto-completed in this session
    private val autoCompletedSubtaskIds = mutableSetOf<Int>()

    // Session stats
    private var sessionStartTimeMillis: Long = 0
    private var pomodoroCount: Int = 0
    private var lastTickTimeMillis: Long = 0

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
                val response = apiService.getTask(taskId)
                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true && apiResponse.data != null) {
                        val task = apiResponse.data
                        _currentTask.value = task

                        // Clear subtask tracking (focusing on whole task, not specific subtask)
                        currentSubtaskId = null
                        parentTaskId = null
                        android.util.Log.d("FocusSessionViewModel",
                            "Focusing on whole task (no specific subtask)")

                        // Update subtasks
                        val subtasks = task.subtasks ?: emptyList()
                        _subtasks.value = subtasks

                        // Update deep work mode status - Log để debug
                        android.util.Log.d("FocusSessionViewModel", "Task loaded - requires_deep_focus: ${task.requires_deep_focus}")
                        android.util.Log.d("FocusSessionViewModel", "Task loaded - allow_interruptions: ${task.allow_interruptions}")
                        android.util.Log.d("FocusSessionViewModel", "Task loaded - focus_difficulty: ${task.focus_difficulty}")
                        _isDeepWorkMode.value = task.requires_deep_focus

                        // Set timer duration - use remaining_minutes if available (smart time calculation)
                        val timerMinutes = when {
                            // Use remaining_minutes if > 0 (accounts for completed subtasks)
                            task.remaining_minutes != null && task.remaining_minutes > 0 -> {
                                android.util.Log.d("FocusSessionViewModel",
                                    "Using remaining_minutes: ${task.remaining_minutes} " +
                                    "(estimated: ${task.estimated_minutes}, completed subtasks deducted)")
                                task.remaining_minutes
                            }
                            // Fallback to estimated_minutes if remaining_minutes not available or = 0
                            task.estimated_minutes != null && task.estimated_minutes > 0 -> {
                                android.util.Log.d("FocusSessionViewModel",
                                    "Using estimated_minutes: ${task.estimated_minutes}")
                                task.estimated_minutes
                            }
                            // Default to 25 minutes if no time estimate
                            else -> {
                                android.util.Log.d("FocusSessionViewModel",
                                    "No time estimate, using default: 25 minutes")
                                25
                            }
                        }
                        setTimerDuration(timerMinutes)

                        // Load knowledge items for task and all subtasks
                        // Pass subtasks directly to avoid race condition with LiveData
                        loadKnowledgeItemsInternal(taskId, subtasks)
                    } else {
                        _toast.value = "タスクが見つかりません"
                    }
                } else {
                    _toast.value = "タスクの取得に失敗しました"
                }
            } catch (e: Exception) {
                _toast.value = "エラーが発生しました: ${e.message}"
            }
        }
    }

    /**
     * サブタスクを含むタスクを取得してフォーカス
     * @param taskId Task ID
     * @param subtaskId Subtask ID (database ID, not array index)
     */
    fun loadTaskWithSubtask(taskId: Int, subtaskId: Int) {
        viewModelScope.launch {
            try {
                val response = apiService.getTask(taskId)
                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true && apiResponse.data != null) {
                        val task = apiResponse.data
                        val subtasks = task.subtasks

                        // Find subtask by ID (not by index!)
                        val subtask = subtasks?.find { it.id == subtaskId }

                        if (subtask != null) {
                            android.util.Log.d("FocusSessionViewModel",
                                "Subtask found: id=${subtask.id}, title=${subtask.title}, " +
                                "estimated_minutes=${subtask.estimated_minutes}")

                            // Track current subtask and parent task for auto-completion
                            currentSubtaskId = subtask.id
                            parentTaskId = task.id
                            android.util.Log.d("FocusSessionViewModel",
                                "Tracking subtask: subtaskId=$currentSubtaskId, parentTaskId=$parentTaskId")

                            // Calculate default time if subtask has no estimate
                            // Use parent task's estimated time divided by number of subtasks, or 25 min default
                            val defaultTime = if (task.estimated_minutes != null && subtasks != null && subtasks.isNotEmpty()) {
                                (task.estimated_minutes / subtasks.size).coerceAtLeast(15)
                            } else {
                                25  // Standard Pomodoro time
                            }

                            val subtaskTime = subtask.estimated_minutes ?: defaultTime

                            android.util.Log.d("FocusSessionViewModel",
                                "Subtask time calculation: subtask.estimated_minutes=${subtask.estimated_minutes}, " +
                                "defaultTime=$defaultTime, final=$subtaskTime")

                            // Create a modified task object with subtask info for display
                            val modifiedTask = task.copy(
                                title = subtask.title,
                                estimated_minutes = subtaskTime
                            )
                            _currentTask.value = modifiedTask

                            // Update subtasks (show all subtasks, highlight current one)
                            val subtasks = task.subtasks ?: emptyList()
                            _subtasks.value = subtasks

                            // Update deep work mode status (inherit from parent task)
                            android.util.Log.d("FocusSessionViewModel", "Subtask loaded - parent requires_deep_focus: ${task.requires_deep_focus}")
                            _isDeepWorkMode.value = task.requires_deep_focus

                            // Set timer duration based on subtask estimated minutes
                            setTimerDuration(subtaskTime)

                            // Load knowledge items for task and all subtasks
                            // Pass subtasks directly to avoid race condition with LiveData
                            loadKnowledgeItemsInternal(taskId, subtasks)
                        } else {
                            android.util.Log.e("FocusSessionViewModel",
                                "Subtask not found: subtaskId=$subtaskId in task $taskId with ${subtasks?.size ?: 0} subtasks")
                            _toast.value = "サブタスクが見つかりません (ID: $subtaskId)"
                        }
                    } else {
                        _toast.value = "タスクが見つかりません"
                    }
                } else {
                    _toast.value = "タスクの取得に失敗しました"
                }
            } catch (e: Exception) {
                android.util.Log.e("FocusSessionViewModel", "Error loading subtask", e)
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
        lastTickTimeMillis = sessionStartTimeMillis
        _isTimerRunning.value = true

        countDownTimer = object : CountDownTimer(timeRemainingMillis, 1000) {
            override fun onTick(millisUntilFinished: Long) {
                val currentTime = System.currentTimeMillis()
                val elapsedSeconds = ((currentTime - lastTickTimeMillis) / 1000).toInt()
                lastTickTimeMillis = currentTime

                timeRemainingMillis = millisUntilFinished
                updateTimerDisplay(millisUntilFinished)
                updateProgress()

                // Update subtask elapsed time and auto-complete if needed
                if (elapsedSeconds > 0) {
                    updateSubtaskProgress(elapsedSeconds)
                }
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
        lastTickTimeMillis = 0
        // Note: Don't reset elapsed time on pause - keep progress
    }

    /**
     * タイマーをリセット
     */
    fun resetTimer() {
        countDownTimer?.cancel()
        _isTimerRunning.value = false
        timeRemainingMillis = totalTimeMillis
        lastTickTimeMillis = 0
        // Reset subtask elapsed time
        subtaskElapsedSeconds.clear()
        autoCompletedSubtaskIds.clear()
        _subtaskElapsedMinutes.value = emptyMap()
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

                // Auto-complete subtask if focusing on specific subtask
                completeCurrentSubtask()

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
     * タイマー表示を更新
     * - 60分未満: MM:SS形式 (例: 25:00)
     * - 60分以上: MM:SS形式 (例: 119:57) - hiển thị tổng số phút
     */
    private fun updateTimerDisplay(millisUntilFinished: Long) {
        val totalSeconds = (millisUntilFinished / 1000).toInt()
        val totalMinutes = totalSeconds / 60
        val seconds = totalSeconds % 60
        
        // Hiển thị dạng MM:SS (tổng số phút:giây)
        // Ví dụ: 119:57 (119 phút 57 giây)
        _timerDisplay.value = String.format("%d:%02d", totalMinutes, seconds)
    }

    /**
     * プログレスバーを更新 (0-100)
     */
    private fun updateProgress() {
        val progressPercent = ((totalTimeMillis - timeRemainingMillis) * 100 / totalTimeMillis).toInt()
        _progress.value = progressPercent
    }

    /**
     * Update subtask progress and auto-complete if time elapsed >= estimated_minutes
     */
    private fun updateSubtaskProgress(elapsedSeconds: Int) {
        val currentSubtasks = _subtasks.value ?: return
        var updated = false

        currentSubtasks.forEach { subtask ->
            // Skip if already completed or no estimated time
            if (subtask.is_completed || subtask.estimated_minutes == null || subtask.estimated_minutes <= 0) {
                return@forEach
            }

            // Skip if already auto-completed in this session
            if (subtask.id in autoCompletedSubtaskIds) {
                return@forEach
            }

            // Add elapsed seconds
            val currentElapsedSeconds = subtaskElapsedSeconds[subtask.id] ?: 0
            val newElapsedSeconds = currentElapsedSeconds + elapsedSeconds
            subtaskElapsedSeconds[subtask.id] = newElapsedSeconds

            // Convert to minutes for display
            val elapsedMinutes = newElapsedSeconds / 60
            updated = true

            // Auto-complete if elapsed time >= estimated time (in minutes)
            if (elapsedMinutes >= subtask.estimated_minutes) {
                autoCompleteSubtask(subtask.id)
            }
        }

        if (updated) {
            // Convert seconds to minutes for LiveData
            val elapsedMinutesMap = subtaskElapsedSeconds.mapValues { it.value / 60 }
            _subtaskElapsedMinutes.value = elapsedMinutesMap
        }
    }

    /**
     * Auto-complete a subtask via API
     */
    private fun autoCompleteSubtask(subtaskId: Int) {
        // Mark as auto-completed to prevent duplicate calls
        autoCompletedSubtaskIds.add(subtaskId)

        viewModelScope.launch {
            try {
                val response = apiService.toggleSubtask(subtaskId)
                if (response.isSuccessful && response.body()?.success == true) {
                    // Reload task to get updated subtasks
                    val taskId = _currentTask.value?.id ?: return@launch
                    loadTask(taskId)
                    _toast.value = "サブタスクが自動完了しました"
                }
            } catch (e: Exception) {
                // Silent fail - don't show error for auto-complete
            }
        }
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
                scheduled_time = map["scheduled_time"] as? String,
                created_at = map["created_at"] as? String ?: "",
                updated_at = map["updated_at"] as? String ?: "",
                user_id = (map["user_id"] as? Number)?.toInt() ?: 0,
                project_id = (map["project_id"] as? Number)?.toInt(),
                learning_milestone_id = (map["learning_milestone_id"] as? Number)?.toInt(),
                ai_breakdown_enabled = map["ai_breakdown_enabled"] as? Boolean ?: false,
                // Focus enhancement features
                requires_deep_focus = map["requires_deep_focus"] as? Boolean ?: false,
                allow_interruptions = map["allow_interruptions"] as? Boolean ?: true,
                focus_difficulty = (map["focus_difficulty"] as? Number)?.toInt() ?: 3,
                warmup_minutes = (map["warmup_minutes"] as? Number)?.toInt(),
                cooldown_minutes = (map["cooldown_minutes"] as? Number)?.toInt(),
                recovery_minutes = (map["recovery_minutes"] as? Number)?.toInt(),
                last_focus_at = map["last_focus_at"] as? String,
                total_focus_minutes = (map["total_focus_minutes"] as? Number)?.toInt() ?: 0,
                distraction_count = (map["distraction_count"] as? Number)?.toInt() ?: 0
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

    /**
     * Load knowledge items for the current task and all its subtasks
     * Public method that can be called from Activity
     * This will fetch task data first to get all subtasks, then load knowledge
     */
    fun loadKnowledgeItems(taskId: Int) {
        viewModelScope.launch {
            try {
                // Fetch task to get all subtasks first
                val response = apiService.getTask(taskId)
                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true && apiResponse.data != null) {
                        val task = apiResponse.data
                        val subtasks = task.subtasks ?: emptyList()

                        android.util.Log.d("FocusSessionViewModel",
                            "Public loadKnowledgeItems: taskId=$taskId, subtasks=${subtasks.size}")

                        // Load knowledge with all subtasks
                        loadKnowledgeItemsInternal(taskId, subtasks)
                    } else {
                        // Fallback: try without subtasks
                        loadKnowledgeItemsInternal(taskId, null)
                    }
                } else {
                    // Fallback: try without subtasks
                    loadKnowledgeItemsInternal(taskId, null)
                }
            } catch (e: Exception) {
                android.util.Log.e("FocusSessionViewModel",
                    "Error in public loadKnowledgeItems", e)
                // Fallback: try without subtasks
                loadKnowledgeItemsInternal(taskId, null)
            }
        }
    }

    /**
     * Internal method to load knowledge items (can be called within other suspend functions)
     * @param taskId The task ID
     * @param subtasks List of subtasks (optional, will use _subtasks.value if null)
     */
    private suspend fun loadKnowledgeItemsInternal(taskId: Int, subtasks: List<Subtask>? = null) {
        try {
            _isLoadingKnowledge.value = true

            // Load all knowledge items and filter by source_task_id
            val response = apiService.getKnowledgeItems(null)

            if (response.isSuccessful) {
                val allItems = response.body()?.data ?: emptyList()

                // Get IDs to filter: task ID + all subtask IDs
                // Use provided subtasks or fallback to LiveData value
                val currentSubtasks = subtasks ?: _subtasks.value ?: emptyList()
                val subtaskIds = currentSubtasks.map { it.id }
                val filterIds = listOf(taskId) + subtaskIds

                android.util.Log.d("FocusSessionViewModel", "Loading knowledge items for taskId=$taskId, subtaskIds=$subtaskIds (${currentSubtasks.size} subtasks)")

                // Filter knowledge items for task and all subtasks
                val taskItems = allItems.filter { item ->
                    item.source_task_id in filterIds
                }

                android.util.Log.d("FocusSessionViewModel", "Found ${taskItems.size} knowledge items (total: ${allItems.size})")

                _knowledgeItems.postValue(taskItems)
            } else {
                _knowledgeItems.postValue(emptyList())
                _toast.value = "学習内容の読み込みに失敗しました"
            }
        } catch (e: Exception) {
            _knowledgeItems.postValue(emptyList())
            _toast.value = "ネットワークエラー: ${e.message}"
        } finally {
            _isLoadingKnowledge.value = false
        }
    }

    /**
     * Complete current subtask when focus session finishes
     * Only called if user was focusing on a specific subtask
     */
    private fun completeCurrentSubtask() {
        val subtaskId = currentSubtaskId
        val taskId = parentTaskId

        if (subtaskId == null || taskId == null) {
            android.util.Log.d("FocusSessionViewModel",
                "No subtask to complete (focusing on whole task)")
            return
        }

        android.util.Log.d("FocusSessionViewModel",
            "Auto-completing subtask: subtaskId=$subtaskId, parentTaskId=$taskId")

        viewModelScope.launch {
            try {
                val response = apiService.completeSubtask(subtaskId)
                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true && apiResponse.data != null) {
                        val result = apiResponse.data

                        android.util.Log.d("FocusSessionViewModel",
                            "Subtask completed successfully: ${result.subtask.title}")

                        // Reload parent task to refresh subtasks list and progress
                        loadTask(taskId)

                        // Show success message
                        _toast.value = apiResponse.message ?: "サブタスクを完了しました！"
                    } else {
                        _toast.value = "サブタスクの完了に失敗しました"
                    }
                } else {
                    _toast.value = "サブタスクの完了に失敗しました"
                }
            } catch (e: Exception) {
                android.util.Log.e("FocusSessionViewModel",
                    "Error completing subtask", e)
                _toast.value = "エラーが発生しました: ${e.message}"
            }
        }
    }

    override fun onCleared() {
        super.onCleared()
        countDownTimer?.cancel()
    }
}

