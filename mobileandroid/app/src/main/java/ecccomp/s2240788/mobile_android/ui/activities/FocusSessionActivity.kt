package ecccomp.s2240788.mobile_android.ui.activities

import android.os.Bundle
import android.text.Editable
import android.text.TextWatcher
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.repository.TaskTrackingRepository
import ecccomp.s2240788.mobile_android.databinding.ActivityFocusSessionBinding
import ecccomp.s2240788.mobile_android.ui.adapters.FocusKnowledgeAdapter
import ecccomp.s2240788.mobile_android.ui.adapters.FocusSubtaskAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.FocusSessionViewModel
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.*

/**
 * FocusSessionActivity
 * Focus Timer 画面 - Pomodoro Technique
 * - タイマー機能 (5分、25分、45分)
 * - 作業/休憩モード自動切り替え
 * - Focus Session の記録
 */
class FocusSessionActivity : BaseActivity() {

    private lateinit var binding: ActivityFocusSessionBinding
    private lateinit var viewModel: FocusSessionViewModel
    private lateinit var knowledgeAdapter: FocusKnowledgeAdapter
    private lateinit var subtaskAdapter: FocusSubtaskAdapter
    private var taskId: Int = -1
    private var subtaskId: Int = -1
    
    // Heartbeat tracking
    private var heartbeatJob: Job? = null
    private val heartbeatScope = CoroutineScope(Dispatchers.IO + SupervisorJob())

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityFocusSessionBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupWindowInsets()

        viewModel = ViewModelProvider(this)[FocusSessionViewModel::class.java]

        // Get task ID and subtask ID from intent
        taskId = intent.getIntExtra("task_id", -1)
        subtaskId = intent.getIntExtra("subtask_id", -1)

        if (taskId != -1) {
            if (subtaskId != -1) {
                // Focus on specific subtask
                viewModel.loadTaskWithSubtask(taskId, subtaskId)
            } else {
                // Focus on main task
                viewModel.loadTask(taskId)
            }

            // Knowledge items are automatically loaded in loadTask/loadTaskWithSubtask
        }

        setupKnowledgeRecyclerView()
        setupSubtaskRecyclerView()
        setupClickListeners()
        observeViewModel()
    }

    /**
     * Knowledge RecyclerView setup
     */
    private fun setupKnowledgeRecyclerView() {
        knowledgeAdapter = FocusKnowledgeAdapter { item ->
            // Handle item click if needed
            Toast.makeText(this, item.title, Toast.LENGTH_SHORT).show()
        }

        binding.rvKnowledgeItems.apply {
            layoutManager = LinearLayoutManager(this@FocusSessionActivity)
            adapter = knowledgeAdapter
        }
    }

    /**
     * Subtask RecyclerView setup
     */
    private fun setupSubtaskRecyclerView() {
        subtaskAdapter = FocusSubtaskAdapter()

        binding.rvSubtasks.apply {
            layoutManager = LinearLayoutManager(this@FocusSessionActivity)
            adapter = subtaskAdapter
        }
    }

    /**
     * クリックリスナーのセットアップ
     */
    private fun setupClickListeners() {
        // Back button - Show confirmation if timer is running
        binding.btnBack.setOnClickListener {
            val isRunning = viewModel.isTimerRunning.value ?: false
            if (isRunning) {
                showGiveUpConfirmDialog()
            } else {
                finish()
            }
        }

        // Settings button
        binding.btnSettings.setOnClickListener {
            Toast.makeText(this, "設定 (開発中)", Toast.LENGTH_SHORT).show()
        }

        // Hide timer duration buttons (use task's estimated time)
        binding.btn5min.visibility = View.GONE
        binding.btn25min.visibility = View.GONE
        binding.btn45min.visibility = View.GONE

        // Start button only (no pause)
        binding.btnStart.setOnClickListener {
            val isRunning = viewModel.isTimerRunning.value ?: false
            if (!isRunning) {
                viewModel.startTimer()
            }
        }

        // Hide Pause button
        binding.btnPause.visibility = View.GONE

        // Change Skip to Give Up
        binding.btnSkip.text = "諦める"
        binding.btnSkip.setOnClickListener {
            showGiveUpConfirmDialog()
        }

        // メモのEditTextにTextWatcherを追加して、変更があるたびにViewModelに保存
        binding.etNotes.addTextChangedListener(object : TextWatcher {
            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {}
            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {}
            override fun afterTextChanged(s: Editable?) {
                val notes = s?.toString()
                if (!notes.isNullOrBlank()) {
                    viewModel.setSessionNotes(notes)
                } else {
                    viewModel.setSessionNotes(null)
                }
            }
        })
    }

    /**
     * ViewModelの監視
     */
    private fun observeViewModel() {
        // Timer display
        viewModel.timerDisplay.observe(this) { time ->
            binding.tvTimerDisplay.text = time
        }

        // Progress
        viewModel.progress.observe(this) { progress ->
            binding.timerProgress.setProgressCompat(progress, true)
            binding.progressBar.setProgressCompat(progress, true)
        }

        // Timer running state
        viewModel.isTimerRunning.observe(this) { isRunning ->
            updateControlButtonsUI(isRunning)

            // Start/stop heartbeat worker based on timer state
            if (isRunning && taskId != -1) {
                startHeartbeatWorker(taskId)
            } else {
                stopHeartbeatWorker(taskId)
            }
        }

        // Timer mode
        viewModel.timerMode.observe(this) { mode ->
            updateTimerModeUI(mode)
            // 休憩モードに切り替わった時、タスク情報の表示を更新
            updateTaskInfoForMode(mode)
            
            // 休憩モードに切り替わった時、メモ欄をクリア
            if (mode == FocusSessionViewModel.TimerMode.SHORT_BREAK || 
                mode == FocusSessionViewModel.TimerMode.LONG_BREAK) {
                binding.etNotes.setText("")
            }
        }

        // Current task
        viewModel.currentTask.observe(this) { task ->
            // 休憩モードの場合はタスク情報を非表示または休憩メッセージを表示
            val timerMode = viewModel.timerMode.value
            if (timerMode == FocusSessionViewModel.TimerMode.SHORT_BREAK || 
                timerMode == FocusSessionViewModel.TimerMode.LONG_BREAK) {
                // 休憩モード：タスク情報を非表示または休憩メッセージを表示
                binding.tvCurrentTask.text = when (timerMode) {
                    FocusSessionViewModel.TimerMode.SHORT_BREAK -> getString(R.string.timer_mode_short_break)
                    FocusSessionViewModel.TimerMode.LONG_BREAK -> getString(R.string.timer_mode_long_break)
                    else -> ""
                }
                binding.tvEstimatedTime.text = ""
                binding.tvFocusCount.text = ""
            } else {
                // 作業モード：タスク情報を表示
                task?.let {
                    binding.tvCurrentTask.text = it.title

                    // Display estimated time (ViewModel already set the timer duration correctly)
                    val estimatedMinutes = it.estimated_minutes ?: 25
                    binding.tvEstimatedTime.text = "⏱ $estimatedMinutes ${getString(R.string.minutes_unit)}"

                    // DO NOT call setTimerDuration() here - ViewModel already set it correctly in loadTask/loadTaskWithSubtask
                    // Calling it here would override the correct subtask time with the modified task's time

                    // Calculate target pomodoros (1 pomodoro = 25 min)
                    val targetPomodoros = (estimatedMinutes + 24) / 25  // Round up
                    binding.tvFocusCount.text = getString(R.string.pomodoro_count_format, viewModel.getPomodoroCount(), targetPomodoros)

                    // Update timer mode text based on category
                    val categoryText = when (it.category?.lowercase()) {
                        "study" -> getString(R.string.category_study)
                        "work" -> getString(R.string.category_work)
                        "personal" -> getString(R.string.category_personal)
                        "other" -> getString(R.string.category_other)
                        else -> getString(R.string.timer_mode_work) // Default to "Working"
                    }
                    binding.tvTimerMode.text = categoryText

                    // CRITICAL: Update deep work mode UI immediately when task loads
                    // This ensures UI is updated even if isDeepWorkMode observer hasn't fired yet
                    val isDeepWork = it.requires_deep_focus
                    android.util.Log.d("FocusSessionActivity", "Task loaded - requires_deep_focus: $isDeepWork, allow_interruptions: ${it.allow_interruptions}, focus_difficulty: ${it.focus_difficulty}")

                    // Always update UI based on task's requires_deep_focus value
                    updateDeepWorkModeUI(isDeepWork)
                }
            }
        }

        // Deep Work Mode observer (backup - should also trigger)
        viewModel.isDeepWorkMode.observe(this) { isDeepWork ->
            android.util.Log.d("FocusSessionActivity", "isDeepWorkMode LiveData changed: $isDeepWork")
            updateDeepWorkModeUI(isDeepWork)
        }

        // Session completed
        viewModel.focusSessionCompleted.observe(this) { completed ->
            if (completed) {
                // Stop heartbeat worker when session completes
                stopHeartbeatWorker(taskId)

                // Play sound, show notification, etc.
                Toast.makeText(this, getString(R.string.session_completed), Toast.LENGTH_LONG).show()

                // タスクのステータスを確認して、完了していない場合はダイアログを表示
                viewModel.currentTask.value?.let { task ->
                    if (task.status != "completed") {
                        showTaskCompleteDialog()
                    }
                }

                // メモは既にTextWatcherでViewModelに設定されているため、ここでは何もしない
                // stopFocusSession()で既にメモが保存されている

                viewModel.clearSessionCompleted()
            }
        }

        // Toast messages
        viewModel.toast.observe(this) { message ->
            message?.let {
                Toast.makeText(this, it, Toast.LENGTH_SHORT).show()
                viewModel.clearToast()
            }
        }

        // Knowledge items
        viewModel.knowledgeItems.observe(this) { items ->
            android.util.Log.d("FocusSessionActivity", "Knowledge items observer: Received ${items?.size ?: 0} items")
            
            // ローディング状態を確認
            val isLoading = viewModel.isLoadingKnowledge.value ?: false
            
            // ローディング中でも、itemsが空でない場合は更新する（初回ロード時）
            // ローディング完了後は必ず更新する
            if (isLoading && items.isNullOrEmpty()) {
                android.util.Log.d("FocusSessionActivity", "Knowledge items observer: Loading in progress and items empty, skipping update")
                return@observe
            }
            
            android.util.Log.d("FocusSessionActivity", "Knowledge items observer: Updating UI with ${items?.size ?: 0} items")
            
            // 表示を更新
            if (items.isNullOrEmpty()) {
                binding.learningContentCard.visibility = View.GONE
                binding.emptyKnowledgeState.visibility = View.VISIBLE
                // 空のリストをadapterに設定（以前のデータをクリア）
                knowledgeAdapter.submitList(emptyList())
            } else {
                binding.learningContentCard.visibility = View.VISIBLE
                binding.emptyKnowledgeState.visibility = View.GONE
                knowledgeAdapter.submitList(items)
                binding.tvKnowledgeCount.text = getString(R.string.items_count_format, items.size)
            }
        }

        // Knowledge loading state
        viewModel.isLoadingKnowledge.observe(this) { isLoading ->
            android.util.Log.d("FocusSessionActivity", "Knowledge loading state changed: isLoading=$isLoading")
            if (isLoading) {
                // ローディング中は空の状態を非表示（ローディングインジケーターを表示する場合はここで）
                binding.emptyKnowledgeState.visibility = View.GONE
                // ローディング中はカードを非表示にしない（以前のデータを保持）
            } else {
                // ローディングが完了したら、knowledgeItemsの現在の値を確認して更新
                val currentItems = viewModel.knowledgeItems.value
                android.util.Log.d("FocusSessionActivity", "Knowledge loading completed, current items count: ${currentItems?.size ?: 0}")
                
                // 現在のitemsでUIを更新（observerが既に処理している可能性があるが、念のため）
                if (currentItems.isNullOrEmpty()) {
                    binding.learningContentCard.visibility = View.GONE
                    binding.emptyKnowledgeState.visibility = View.VISIBLE
                    knowledgeAdapter.submitList(emptyList())
                } else {
                    binding.learningContentCard.visibility = View.VISIBLE
                    binding.emptyKnowledgeState.visibility = View.GONE
                    knowledgeAdapter.submitList(currentItems)
                    binding.tvKnowledgeCount.text = getString(R.string.items_count_format, currentItems.size)
                }
            }
        }

        // Subtasks
        viewModel.subtasks.observe(this) { subtasks ->
            if (subtasks.isNullOrEmpty()) {
                binding.subtasksCard.visibility = View.GONE
            } else {
                binding.subtasksCard.visibility = View.VISIBLE
                subtaskAdapter.submitList(subtasks)

                // Update progress text
                val completedCount = subtasks.count { it.is_completed }
                binding.tvSubtasksProgress.text = "$completedCount/${subtasks.size}"
            }
        }

        // Subtask elapsed minutes
        viewModel.subtaskElapsedMinutes.observe(this) { elapsedMap ->
            subtaskAdapter.updateElapsedMinutes(elapsedMap)
        }
    }

    /**
     * コントロールボタンのUI更新
     */
    private fun updateControlButtonsUI(isRunning: Boolean) {
        if (isRunning) {
            // Timer running - disable start button and show give up
            binding.btnStart.isEnabled = false
            binding.btnStart.alpha = 0.5f
            binding.btnSkip.visibility = View.VISIBLE
        } else {
            // Timer not running - enable start button and hide give up
            binding.btnStart.isEnabled = true
            binding.btnStart.alpha = 1.0f
            binding.btnStart.text = "開始"
            binding.btnStart.icon = ContextCompat.getDrawable(this, R.drawable.ic_play)
            binding.btnSkip.visibility = View.GONE
        }
    }

    /**
     * Give up confirmation dialog
     */
    private fun showGiveUpConfirmDialog() {
        androidx.appcompat.app.AlertDialog.Builder(this)
            .setTitle("タスクを諦めますか？")
            .setMessage("タイマーを途中でやめると、このタスクは未完了として記録されます。")
            .setPositiveButton("諦める") { _, _ ->
                // セッションを停止してから終了
                viewModel.pauseTimer()
                // セッションを停止（メモなし）
                viewModel.stopFocusSessionForAbandon()
                Toast.makeText(this, "タスクを中断しました", Toast.LENGTH_SHORT).show()
                finish()
            }
            .setNegativeButton("続ける", null)
            .show()
    }

    /**
     * Task completion confirmation dialog
     */
    private fun showTaskCompleteDialog() {
        androidx.appcompat.app.AlertDialog.Builder(this)
            .setTitle("タスクを完了しますか？")
            .setMessage("セッションは終了しましたが、タスクはまだ完了していません。タスクを完了としてマークしますか？")
            .setPositiveButton("完了") { _, _ ->
                // タスクを完了にマーク
                viewModel.completeTaskManually()
                Toast.makeText(this, "タスクを完了しました！", Toast.LENGTH_SHORT).show()
            }
            .setNegativeButton("続ける", null)
            .show()
    }

    /**
     * Deep Work Mode UI更新
     */
    private fun updateDeepWorkModeUI(isDeepWork: Boolean) {
        if (isDeepWork) {
            // Change title to "Deep Work Mode" with primary color
            binding.tvTitle.text = getString(R.string.deep_work_mode_title)
            binding.tvTitle.setTextColor(ContextCompat.getColor(this, R.color.primary))
            
            // Change top bar background to primary_light for Deep Work
            binding.topBar.setCardBackgroundColor(
                ContextCompat.getColor(this, R.color.primary_light)
            )
            
            // Show deep work badge
            binding.deepWorkBadge.visibility = View.VISIBLE
            
            // Change timer card background to primary_light
            binding.timerCard.setCardBackgroundColor(
                ContextCompat.getColor(this, R.color.primary_light)
            )
            binding.timerCard.strokeColor = ContextCompat.getColor(this, R.color.primary)
            binding.timerCard.strokeWidth = 2
            
            // Change timer mode badge to show deep work
            binding.timerModeBadge.setCardBackgroundColor(
                ContextCompat.getColor(this, R.color.primary)
            )
            binding.tvTimerMode.text = getString(R.string.deep_work_mode_title)
            binding.tvTimerMode.setTextColor(
                ContextCompat.getColor(this, R.color.white)
            )
            
            // Change progress indicator color to primary
            binding.timerProgress.setIndicatorColor(
                ContextCompat.getColor(this, R.color.primary)
            )
            binding.progressBar.setIndicatorColor(
                ContextCompat.getColor(this, R.color.primary)
            )
            
            // Change task info card background
            binding.taskInfoCard.setCardBackgroundColor(
                ContextCompat.getColor(this, R.color.primary_light)
            )
            binding.taskInfoCard.strokeColor = ContextCompat.getColor(this, R.color.primary)
            binding.taskInfoCard.strokeWidth = 1
            
        } else {
            // Normal focus mode
            binding.tvTitle.text = getString(R.string.focus_mode_title)
            binding.tvTitle.setTextColor(ContextCompat.getColor(this, R.color.text_primary))
            
            // Reset top bar background
            binding.topBar.setCardBackgroundColor(
                ContextCompat.getColor(this, R.color.white)
            )
            
            // Hide deep work badge
            binding.deepWorkBadge.visibility = View.GONE
            
            // Reset timer card background
            binding.timerCard.setCardBackgroundColor(
                ContextCompat.getColor(this, R.color.white)
            )
            binding.timerCard.strokeColor = ContextCompat.getColor(this, R.color.line_variant)
            binding.timerCard.strokeWidth = 1
            
            // Reset task info card background
            binding.taskInfoCard.setCardBackgroundColor(
                ContextCompat.getColor(this, R.color.white)
            )
            binding.taskInfoCard.strokeColor = ContextCompat.getColor(this, R.color.line_variant)
            binding.taskInfoCard.strokeWidth = 1
            
            // Reset timer mode badge (will be updated by updateTimerModeUI)
        }
    }

    /**
     * モードに応じてタスク情報の表示を更新
     */
    private fun updateTaskInfoForMode(mode: FocusSessionViewModel.TimerMode) {
        val task = viewModel.currentTask.value
        if (mode == FocusSessionViewModel.TimerMode.SHORT_BREAK || 
            mode == FocusSessionViewModel.TimerMode.LONG_BREAK) {
            // 休憩モード：タスク情報を非表示または休憩メッセージを表示
            binding.tvCurrentTask.text = when (mode) {
                FocusSessionViewModel.TimerMode.SHORT_BREAK -> getString(R.string.timer_mode_short_break)
                FocusSessionViewModel.TimerMode.LONG_BREAK -> getString(R.string.timer_mode_long_break)
                else -> ""
            }
            binding.tvEstimatedTime.text = ""
            binding.tvFocusCount.text = ""
        } else {
            // 作業モード：タスク情報を表示
            task?.let {
                binding.tvCurrentTask.text = it.title
                val estimatedMinutes = it.estimated_minutes ?: 25
                binding.tvEstimatedTime.text = "⏱ $estimatedMinutes ${getString(R.string.minutes_unit)}"
                val targetPomodoros = (estimatedMinutes + 24) / 25
                binding.tvFocusCount.text = getString(R.string.pomodoro_count_format, viewModel.getPomodoroCount(), targetPomodoros)
            }
        }
    }

    /**
     * タイマーモードのUI更新
     */
    private fun updateTimerModeUI(mode: FocusSessionViewModel.TimerMode) {
        // Check if deep work mode is active - if yes, don't override timer badge
        val isDeepWork = viewModel.isDeepWorkMode.value ?: false
        if (isDeepWork && mode == FocusSessionViewModel.TimerMode.WORK) {
            // Deep work mode already set the badge, just update progress color
            binding.timerProgress.setIndicatorColor(
                ContextCompat.getColor(this, R.color.primary)
            )
            return
        }

        when (mode) {
            FocusSessionViewModel.TimerMode.WORK -> {
                binding.tvTimerMode.text = getString(R.string.timer_mode_work)
                binding.timerModeBadge.setCardBackgroundColor(
                    ContextCompat.getColor(this, R.color.primary_light)
                )
                binding.tvTimerMode.setTextColor(
                    ContextCompat.getColor(this, R.color.primary)
                )
                binding.timerProgress.setIndicatorColor(
                    ContextCompat.getColor(this, R.color.primary)
                )
            }
            FocusSessionViewModel.TimerMode.SHORT_BREAK -> {
                binding.tvTimerMode.text = getString(R.string.timer_mode_short_break)
                binding.timerModeBadge.setCardBackgroundColor(
                    ContextCompat.getColor(this, R.color.success_light)
                )
                binding.tvTimerMode.setTextColor(
                    ContextCompat.getColor(this, R.color.success)
                )
                binding.timerProgress.setIndicatorColor(
                    ContextCompat.getColor(this, R.color.success)
                )
            }
            FocusSessionViewModel.TimerMode.LONG_BREAK -> {
                binding.tvTimerMode.text = getString(R.string.timer_mode_long_break)
                binding.timerModeBadge.setCardBackgroundColor(
                    ContextCompat.getColor(this, R.color.accent_light)
                )
                binding.tvTimerMode.setTextColor(
                    ContextCompat.getColor(this, R.color.accent)
                )
                binding.timerProgress.setIndicatorColor(
                    ContextCompat.getColor(this, R.color.accent)
                )
            }
        }
    }

    /**
     * 時間選択ボタンのUI更新
     */
    private fun updateDurationButtonsUI(selectedMinutes: Int) {
        // Reset all buttons
        binding.btn5min.apply {
            backgroundTintList = ContextCompat.getColorStateList(this@FocusSessionActivity, R.color.surface)
            setTextColor(ContextCompat.getColor(this@FocusSessionActivity, R.color.text_primary))
        }
        binding.btn25min.apply {
            backgroundTintList = ContextCompat.getColorStateList(this@FocusSessionActivity, R.color.surface)
            setTextColor(ContextCompat.getColor(this@FocusSessionActivity, R.color.text_primary))
        }
        binding.btn45min.apply {
            backgroundTintList = ContextCompat.getColorStateList(this@FocusSessionActivity, R.color.surface)
            setTextColor(ContextCompat.getColor(this@FocusSessionActivity, R.color.text_primary))
        }

        // Highlight selected button
        val selectedButton = when (selectedMinutes) {
            5 -> binding.btn5min
            25 -> binding.btn25min
            45 -> binding.btn45min
            else -> binding.btn25min
        }

        selectedButton.apply {
            backgroundTintList = ContextCompat.getColorStateList(this@FocusSessionActivity, R.color.primary)
            setTextColor(ContextCompat.getColor(this@FocusSessionActivity, R.color.white))
        }
    }

    override fun onBackPressed() {
        val isRunning = viewModel.isTimerRunning.value ?: false
        if (isRunning) {
            showGiveUpConfirmDialog()
        } else {
            super.onBackPressed()
        }
    }

    override fun onPause() {
        super.onPause()
        // Keep timer running when activity goes to background
        // User must explicitly give up to stop
    }

    override fun onDestroy() {
        super.onDestroy()

        // Stop heartbeat when activity is destroyed
        stopHeartbeatWorker(taskId)
        
        // Cancel heartbeat scope
        heartbeatScope.cancel()

        // Only clean up if timer is not running
        if (viewModel.isTimerRunning.value != true) {
            viewModel.pauseTimer()
        }
    }

    /**
     * Start periodic heartbeat for task tracking
     * Sends heartbeat every 1-2 minutes as per backend requirements
     * Uses Coroutine instead of WorkManager to achieve 1-2 minute interval
     */
    private fun startHeartbeatWorker(taskId: Int) {
        if (taskId == -1) return

        // Cancel existing heartbeat if any
        stopHeartbeatWorker(taskId)

        // Start heartbeat coroutine (every 2 minutes)
        heartbeatJob = heartbeatScope.launch {
            while (isActive) {
                try {
                    val apiService = NetworkModule.provideApiService(
                        NetworkModule.provideRetrofit(
                            NetworkModule.provideOkHttpClient()
                        )
                    )
                    val repository = TaskTrackingRepository(apiService)
                    
                    val result = repository.sendHeartbeat(taskId)
                    result.fold(
                        onSuccess = {
                            android.util.Log.d("FocusSessionActivity", "Heartbeat sent successfully for task #$taskId")
                        },
                        onError = { error ->
                            android.util.Log.e("FocusSessionActivity", "Failed to send heartbeat: $error")
                        }
                    )
                } catch (e: Exception) {
                    android.util.Log.e("FocusSessionActivity", "Error sending heartbeat: ${e.message}", e)
                }
                
                // Wait 2 minutes before next heartbeat
                delay(2 * 60 * 1000) // 2 minutes in milliseconds
            }
        }
        
        android.util.Log.d("FocusSessionActivity", "Started heartbeat for task #$taskId (every 2 minutes)")
    }

    /**
     * Stop heartbeat worker
     */
    private fun stopHeartbeatWorker(taskId: Int) {
        heartbeatJob?.cancel()
        heartbeatJob = null
        android.util.Log.d("FocusSessionActivity", "Stopped heartbeat for task #$taskId")
    }
}

