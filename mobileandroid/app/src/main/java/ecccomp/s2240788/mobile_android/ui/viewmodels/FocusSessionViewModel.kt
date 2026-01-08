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
import ecccomp.s2240788.mobile_android.data.models.StopFocusSessionRequest
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
    private var currentSessionId: Int? = null // 現在のセッションIDを保存
    private var sessionNotes: String? = null // セッション後のメモを保存

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
                        android.util.Log.d("FocusSessionViewModel", "Task loaded - learning_milestone_id: ${task.learning_milestone_id}")
                        android.util.Log.d("FocusSessionViewModel", "Task loaded - learning_path_id: ${task.learning_path_id}")
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
                        // タスク固有の知識のみを表示するため、learning_path_idは使用しない
                        // 確実にロードされるように、awaitで待つ
                        loadKnowledgeItemsInternal(taskId, subtasks, null)
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
                            // タスク固有の知識のみを表示するため、learning_path_idは使用しない
                            // 確実にロードされるように、awaitで待つ
                            loadKnowledgeItemsInternal(taskId, subtasks, null)
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

        // セッションを開始（workモードの場合のみ）
        if (_timerMode.value == TimerMode.WORK) {
            startFocusSession()
        }

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
     * タスクを諦めた時にセッションを停止（公開メソッド）
     */
    fun stopFocusSessionForAbandon() {
        stopFocusSession(null)
    }

    /**
     * タスクを完了してセッションを停止（公開メソッド）
     */
    fun stopFocusSessionAndCompleteTask(notes: String? = null) {
        stopFocusSession(notes, forceCompleteTask = true)
    }

    /**
     * タスクを手動で完了にマーク（公開メソッド）
     */
    fun completeTaskManually() {
        viewModelScope.launch {
            try {
                val taskId = _currentTask.value?.id ?: return@launch
                android.util.Log.d("FocusSessionViewModel", "completeTaskManually called: taskId=$taskId")
                
                val response = apiService.completeTask(taskId)
                if (response.isSuccessful && response.body()?.success == true) {
                    val completedTask = response.body()?.data
                    if (completedTask != null) {
                        _currentTask.value = completedTask
                        android.util.Log.d("FocusSessionViewModel", "タスクを完了しました: taskId=$taskId")
                        _toast.value = "タスクが完了しました！"
                    }
                } else {
                    android.util.Log.e("FocusSessionViewModel", "タスク完了エラー: ${response.body()?.message}")
                    _toast.value = "タスク完了に失敗しました"
                }
            } catch (e: Exception) {
                android.util.Log.e("FocusSessionViewModel", "タスク完了エラー", e)
                _toast.value = "タスク完了に失敗しました"
            }
        }
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
        // セッションIDもクリア
        currentSessionId = null
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
     * セッション後のメモを設定（タイマー完了前に呼び出す）
     */
    fun setSessionNotes(notes: String?) {
        val oldNotes = sessionNotes
        sessionNotes = notes?.takeIf { it.isNotBlank() }
        android.util.Log.d("FocusSessionViewModel", "setSessionNotes called: oldNotes=${oldNotes?.take(20)}, newNotes=${sessionNotes?.take(20)}, isTimerRunning=${_isTimerRunning.value}")
    }

    /**
     * タイマー完了時の処理
     */
    private fun onTimerComplete() {
        _isTimerRunning.value = false
        
        android.util.Log.d("FocusSessionViewModel", "onTimerComplete called, timerMode=${_timerMode.value}")
        
        when (_timerMode.value) {
            TimerMode.WORK -> {
                pomodoroCount++
                
                android.util.Log.d("FocusSessionViewModel", "WORK mode completed - sessionNotes: isNull=${sessionNotes == null}, content=${sessionNotes?.take(50)}")
                
                // セッションを終了（タスク完了チェックが実行される、メモも保存）
                stopFocusSession(sessionNotes)
                
                // メモをクリア
                sessionNotes = null

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
        
        // 休憩モードに切り替わる時、メモをクリア
        sessionNotes = null
        
        // 休憩モードではタスク情報をクリアしない（前のタスク情報を保持）
        // ただし、UIで休憩モードであることを明確に表示する
        // タスク情報は保持するが、休憩中であることを示す
    }

    /**
     * Focus Session を開始
     */
    private fun startFocusSession() {
        val task = _currentTask.value ?: return
        val durationMinutes = (totalTimeMillis / 1000 / 60).toInt()

        android.util.Log.d("FocusSessionViewModel", "startFocusSession - totalTimeMillis: $totalTimeMillis, durationMinutes: $durationMinutes, taskId: ${task.id}")

        // duration_minutesは最低1分必要
        if (durationMinutes < 1) {
            android.util.Log.e("FocusSessionViewModel", "セッション開始失敗: duration_minutes ($durationMinutes) は1分以上必要です")
            return
        }

        viewModelScope.launch {
            try {
                val request = ecccomp.s2240788.mobile_android.data.models.StartFocusSessionRequest(
                    task_id = task.id,
                    duration_minutes = durationMinutes,
                    session_type = "work"
                )
                android.util.Log.d("FocusSessionViewModel", "Sending startFocusSession request: taskId=${request.task_id}, duration=${request.duration_minutes}, type=${request.session_type}")
                val response = apiService.startFocusSession(request)

                if (response.isSuccessful && response.body()?.success == true) {
                    val session = response.body()?.data
                    currentSessionId = session?.id
                    android.util.Log.d("FocusSessionViewModel", "セッション開始: sessionId=$currentSessionId")
                } else {
                    // セッション開始失敗時の詳細ログ
                    val errorMessage = response.body()?.message ?: "不明なエラー"
                    val errorCode = response.code()
                    android.util.Log.e("FocusSessionViewModel", "セッション開始失敗: code=$errorCode, message=$errorMessage")
                    android.util.Log.e("FocusSessionViewModel", "Response body: ${response.errorBody()?.string()}")
                    
                    // ユーザーに通知
                    _toast.value = "セッション開始に失敗しました: $errorMessage"
                }
            } catch (e: Exception) {
                android.util.Log.e("FocusSessionViewModel", "セッション開始エラー", e)
                _toast.value = "セッション開始エラー: ${e.message}"
            }
        }
    }

    /**
     * Focus Session を終了（タスク完了チェックが実行される）
     * @param notes セッション後のメモ（オプション）
     * @param forceCompleteTask タスクを強制的に完了にする（オプション）
     */
    private fun stopFocusSession(notes: String? = null, forceCompleteTask: Boolean = false) {
        viewModelScope.launch {
            try {
                // currentSessionIdがnullの場合、アクティブなセッションを検索
                var sessionId = currentSessionId
                if (sessionId == null) {
                    android.util.Log.d("FocusSessionViewModel", "currentSessionId is null, searching for active session")
                    val currentSessionResponse = apiService.getCurrentSession()
                    if (currentSessionResponse.isSuccessful && currentSessionResponse.body()?.success == true) {
                        sessionId = currentSessionResponse.body()?.data?.id
                        currentSessionId = sessionId
                        android.util.Log.d("FocusSessionViewModel", "Found active session: sessionId=$sessionId")
                    } else {
                        android.util.Log.w("FocusSessionViewModel", "No active session found, cannot stop")
                        return@launch
                    }
                }

                android.util.Log.d("FocusSessionViewModel", "stopFocusSession called: sessionId=$sessionId, notes=${notes?.take(50)}..., forceCompleteTask=$forceCompleteTask")
                android.util.Log.d("FocusSessionViewModel", "notes details: isNull=${notes == null}, isBlank=${notes?.isBlank()}, length=${notes?.length}")
                
                val request = StopFocusSessionRequest(
                    notes = notes?.takeIf { it.isNotBlank() },
                    force_complete_task = if (forceCompleteTask) true else null
                )
                android.util.Log.d("FocusSessionViewModel", "Request created - notes: ${request.notes}, notes_length: ${request.notes?.length}, force_complete_task: ${request.force_complete_task}")
                
                val response = apiService.stopFocusSession(sessionId!!, request)
                android.util.Log.d("FocusSessionViewModel", "API response received: success=${response.isSuccessful}, code=${response.code()}")

                if (response.isSuccessful && response.body()?.success == true) {
                    android.util.Log.d("FocusSessionViewModel", "セッション終了: sessionId=$sessionId")
                    
                    // セッション終了レスポンスからタスク完了情報を取得
                    // バックエンドはApiResponseの直下にtask_completedを返すため、レスポンス全体をMapとして解析
                    val taskCompleted = try {
                        // Retrofitのレスポンスから直接Mapとして取得
                        val responseBody = response.body()
                        val gson = com.google.gson.Gson()
                        val json = gson.toJson(responseBody)
                        val map: Map<*, *> = gson.fromJson(json, Map::class.java)
                        (map["task_completed"] as? Boolean) ?: false
                    } catch (e: Exception) {
                        android.util.Log.w("FocusSessionViewModel", "task_completed取得失敗: ${e.message}")
                        false
                    }
                    
                    // タスクをリロードして最新の状態を取得（ステータス更新を確認）
                    val taskId = _currentTask.value?.id
                    if (taskId != null) {
                        // 少し待ってからタスクをリロード（バックエンドの処理完了を待つ）
                        kotlinx.coroutines.delay(500)
                        
                        val taskResponse = apiService.getTask(taskId)
                        if (taskResponse.isSuccessful && taskResponse.body()?.success == true) {
                            val updatedTask = taskResponse.body()?.data
                            if (updatedTask != null) {
                                // ステータスに関係なく、常に最新のタスク情報を更新
                                val oldStatus = _currentTask.value?.status
                                val newStatus = updatedTask.status
                                
                                android.util.Log.d("FocusSessionViewModel", 
                                    "タスクステータス更新: taskId=$taskId, oldStatus=$oldStatus, newStatus=$newStatus, taskCompleted=$taskCompleted")
                                
                                _currentTask.value = updatedTask
                                
                                // タスクが完了した場合、トーストを表示
                                if (newStatus == "completed" && oldStatus != "completed") {
                                    _toast.value = "タスクが完了しました！"
                                    android.util.Log.d("FocusSessionViewModel", "タスク完了: taskId=$taskId")
                                } else if (taskCompleted && newStatus != "completed") {
                                    // バックエンドで完了とマークされたが、まだステータスが更新されていない場合
                                    android.util.Log.w("FocusSessionViewModel", 
                                        "タスク完了とマークされたが、ステータスが更新されていません: taskId=$taskId, status=$newStatus")
                                    // 再度リロードを試みる
                                    kotlinx.coroutines.delay(1000)
                                    val retryResponse = apiService.getTask(taskId)
                                    if (retryResponse.isSuccessful && retryResponse.body()?.success == true) {
                                        val retryTask = retryResponse.body()?.data
                                        if (retryTask != null && retryTask.status == "completed") {
                                            _currentTask.value = retryTask
                                            _toast.value = "タスクが完了しました！"
                                        }
                                    }
                                } else if (newStatus == "in_progress" && oldStatus != "in_progress") {
                                    android.util.Log.d("FocusSessionViewModel", "タスクが進行中に戻りました: taskId=$taskId")
                                } else {
                                    android.util.Log.d("FocusSessionViewModel", 
                                        "タスクステータス変更なしまたは予期しない変更: oldStatus=$oldStatus, newStatus=$newStatus")
                                }
                            }
                        } else {
                            android.util.Log.e("FocusSessionViewModel", 
                                "タスク取得失敗: ${taskResponse.body()?.message}")
                        }
                    } else {
                        android.util.Log.w("FocusSessionViewModel", "taskId is null, cannot reload task")
                    }
                    
                    currentSessionId = null
                } else {
                    // セッション終了失敗時の詳細ログ
                    val errorMessage = response.body()?.message ?: "不明なエラー"
                    val errorCode = response.code()
                    android.util.Log.e("FocusSessionViewModel", "セッション終了失敗: code=$errorCode, message=$errorMessage")
                    android.util.Log.e("FocusSessionViewModel", "Response body: ${response.errorBody()?.string()}")
                    
                    // ユーザーに通知
                    _toast.value = "セッション終了に失敗しました: $errorMessage"
                }
            } catch (e: Exception) {
                android.util.Log.e("FocusSessionViewModel", "セッション終了エラー: ${e.message}", e)
                _toast.value = "セッション終了エラー: ${e.message}"
            }
        }
    }
    
    /**
     * メモ付きでFocus Session を保存（非推奨：setSessionNotesを使用してください）
     * このメソッドは後方互換性のために残していますが、メモはstopFocusSessionで保存されます
     */
    fun saveFocusSessionWithNotes(notes: String?) {
        // メモを設定（タイマー完了時にstopFocusSessionで保存される）
        setSessionNotes(notes)
        _toast.value = "メモはセッション終了時に保存されます"
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
                        // タスク固有の知識のみを表示するため、learning_path_idは使用しない
                        loadKnowledgeItemsInternal(taskId, subtasks, null)
                    } else {
                        // Fallback: try without subtasks
                        loadKnowledgeItemsInternal(taskId, null, null)
                    }
                } else {
                    // Fallback: try without subtasks
                    loadKnowledgeItemsInternal(taskId, null, null)
                }
            } catch (e: Exception) {
                android.util.Log.e("FocusSessionViewModel",
                    "Error in public loadKnowledgeItems", e)
                // Fallback: try without subtasks
                loadKnowledgeItemsInternal(taskId, null, null)
            }
        }
    }

    /**
     * Internal method to load knowledge items (can be called within other suspend functions)
     * タスク固有の知識アイテムのみを取得（学習パス全体の知識は含めない）
     * @param taskId The task ID
     * @param subtasks List of subtasks (optional, will use _subtasks.value if null)
     * @param learningPathId Learning path ID (deprecated - タスク固有の知識のみを表示するため使用しない)
     */
    private suspend fun loadKnowledgeItemsInternal(taskId: Int, subtasks: List<Subtask>? = null, learningPathId: Int? = null) {
        // 変数を関数スコープで定義（try/catch/finallyで使用するため）
        var taskItems = emptyList<KnowledgeItem>()
        
        try {
            // ローディング状態を設定（UIがローディング中であることを示す）
            _isLoadingKnowledge.value = true
            
            // 初期状態として空のリストを設定（以前のデータをクリア）
            _knowledgeItems.postValue(emptyList())

            // Get IDs to filter: task ID + all subtask IDs
            // Use provided subtasks or fallback to LiveData value
            val currentSubtasks = subtasks ?: _subtasks.value ?: emptyList()
            val subtaskIds = currentSubtasks.map { it.id }
            val filterTaskIds = listOf(taskId) + subtaskIds

            android.util.Log.d("FocusSessionViewModel", "Loading knowledge items for taskId=$taskId, subtaskIds=$subtaskIds (${currentSubtasks.size} subtasks)")

            // Load knowledge items with filters applied on server side
            // タスク固有の知識のみを表示するため、sourceTaskIdのみを使用（learningPathIdは送信しない）
            // リトライメカニズムを追加して、高速なデバイスでも確実にロードされるようにする
            var retryCount = 0
            val maxRetries = 3
            
            while (retryCount < maxRetries) {
                try {
                    val response = apiService.getKnowledgeItems(
                        categoryId = null,
                        itemType = null,
                        isFavorite = null,
                        isArchived = false,  // Only non-archived items
                        search = null,
                        tags = null,
                        sourceTaskId = filterTaskIds,  // Filter by task and subtask IDs only
                        learningPathId = null,  // タスク固有の知識のみを表示するため、learningPathIdは送信しない
                        perPage = 1000  // Load all items
                    )

                    if (response.isSuccessful) {
                        val apiResponse = response.body()
                        
                        // デバッグ用ログ：レスポンス構造を確認
                        android.util.Log.d("FocusSessionViewModel", "API response received: success=${apiResponse?.success}, data type=${apiResponse?.data?.javaClass?.simpleName}")
                        
                        if (apiResponse?.success == true) {
                            // データの構造をログに出力
                            val responseData = apiResponse.data
                            when (responseData) {
                                is List<*> -> {
                                    android.util.Log.d("FocusSessionViewModel", "Data is List with ${responseData.size} items")
                                    if (responseData.isNotEmpty()) {
                                        android.util.Log.d("FocusSessionViewModel", "First item type: ${responseData[0]?.javaClass?.simpleName}")
                                    }
                                }
                                is Map<*, *> -> {
                                    android.util.Log.d("FocusSessionViewModel", "Data is Map with keys: ${responseData.keys}")
                                }
                                else -> {
                                    android.util.Log.w("FocusSessionViewModel", "Data is unexpected type: ${responseData?.javaClass?.simpleName}")
                                }
                            }
                            
                            taskItems = parseItemsFromResponse(responseData)
                            
                            android.util.Log.d("FocusSessionViewModel", "Parsed ${taskItems.size} knowledge items (attempt ${retryCount + 1})")
                            
                            // Log sample items for debugging
                            if (taskItems.isNotEmpty()) {
                                android.util.Log.d("FocusSessionViewModel", "Sample item: source_task_id=${taskItems[0].source_task_id}, learning_path_id=${taskItems[0].learning_path_id}, title=${taskItems[0].title}")
                            } else {
                                android.util.Log.w("FocusSessionViewModel", "No items parsed from response. Data type: ${responseData?.javaClass?.simpleName}, Data: $responseData")
                            }
                        } else {
                            android.util.Log.w("FocusSessionViewModel", "API response success=false, message=${apiResponse?.message}")
                            taskItems = emptyList()
                        }

                        // 成功したらループを抜ける（空のリストでも成功とみなす）
                        break
                    } else {
                        android.util.Log.w("FocusSessionViewModel", "API response not successful: ${response.code()}, attempt ${retryCount + 1}")
                        if (retryCount < maxRetries - 1) {
                            // リトライ前に少し待つ
                            kotlinx.coroutines.delay(200 * (retryCount + 1).toLong())
                        }
                    }
                } catch (e: Exception) {
                    android.util.Log.e("FocusSessionViewModel", "Error loading knowledge items (attempt ${retryCount + 1}): ${e.message}", e)
                    if (retryCount < maxRetries - 1) {
                        // リトライ前に少し待つ
                        kotlinx.coroutines.delay(200 * (retryCount + 1).toLong())
                    }
                }
                
                retryCount++
            }

            // 最終結果を設定（成功または失敗に関わらず）
            // 空のリストでも設定する（UIが正しく更新されるように）
            
            // 空のリストの場合、ログに記録（デバッグ用）
            if (taskItems.isEmpty()) {
                android.util.Log.d("FocusSessionViewModel", "No knowledge items found for taskId=$taskId, subtaskIds=$subtaskIds")
                // エラーメッセージは表示しない（空のリストは正常な状態の可能性がある）
            }
        } catch (e: Exception) {
            android.util.Log.e("FocusSessionViewModel", "Error in loadKnowledgeItemsInternal", e)
            taskItems = emptyList()
            _toast.value = "ネットワークエラー: ${e.message}"
        } finally {
            // ローディング状態を解除してから、knowledge itemsを設定する
            // これにより、observerが正しく更新される
            _isLoadingKnowledge.value = false
            
            // 少し遅延してからpostValueすることで、isLoadingKnowledgeのobserverが先に処理される
            kotlinx.coroutines.delay(50)
            _knowledgeItems.postValue(taskItems)
            
            android.util.Log.d("FocusSessionViewModel", "Posted ${taskItems.size} knowledge items after loading completed")
        }
    }

    /**
     * Parse Items from API Response
     */
    private fun parseItemsFromResponse(data: Any?): List<KnowledgeItem> {
        return try {
            android.util.Log.d("FocusSessionViewModel", "parseItemsFromResponse: data type=${data?.javaClass?.simpleName}, data=$data")
            
            when (data) {
                is List<*> -> {
                    android.util.Log.d("FocusSessionViewModel", "Parsing List with ${data.size} items")
                    // Retrofit/Gson returns List<LinkedHashMap>, not List<KnowledgeItem>
                    // Need to use Gson to convert each map to KnowledgeItem
                    val gson = com.google.gson.Gson()
                    val parsedItems = data.mapIndexedNotNull { index, item ->
                        try {
                            when (item) {
                                is KnowledgeItem -> {
                                    android.util.Log.d("FocusSessionViewModel", "Item $index is already KnowledgeItem")
                                    item
                                }
                                is Map<*, *> -> {
                                    android.util.Log.d("FocusSessionViewModel", "Item $index is Map, converting...")
                                    // Convert Map to JSON string then parse to KnowledgeItem
                                    val json = gson.toJson(item)
                                    val parsed = gson.fromJson(json, KnowledgeItem::class.java)
                                    android.util.Log.d("FocusSessionViewModel", "Item $index parsed: id=${parsed.id}, title=${parsed.title}")
                                    parsed
                                }
                                else -> {
                                    android.util.Log.w("FocusSessionViewModel", "Item $index is unexpected type: ${item?.javaClass?.simpleName}")
                                    null
                                }
                            }
                        } catch (e: Exception) {
                            android.util.Log.e("FocusSessionViewModel", "Error parsing knowledge item at index $index", e)
                            null
                        }
                    }
                    android.util.Log.d("FocusSessionViewModel", "Successfully parsed ${parsedItems.size} items from List")
                    parsedItems
                }
                is Map<*, *> -> {
                    android.util.Log.d("FocusSessionViewModel", "Parsing Map with keys: ${data.keys}")
                    // Handle paginated response or nested structure
                    val dataList = data["data"] as? List<*>
                    if (dataList != null) {
                        android.util.Log.d("FocusSessionViewModel", "Found 'data' key in Map with ${dataList.size} items")
                        parseItemsFromResponse(dataList)
                    } else {
                        android.util.Log.w("FocusSessionViewModel", "Map does not contain 'data' key")
                        emptyList()
                    }
                }
                null -> {
                    android.util.Log.w("FocusSessionViewModel", "Data is null")
                    emptyList()
                }
                else -> {
                    android.util.Log.w("FocusSessionViewModel", "Unexpected data type: ${data?.javaClass?.simpleName}, value: $data")
                    emptyList()
                }
            }
        } catch (e: Exception) {
            android.util.Log.e("FocusSessionViewModel", "Error in parseItemsFromResponse", e)
            e.printStackTrace()
            emptyList()
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

