package ecccomp.s2240788.mobile_android.ui.activities

import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityFocusSessionBinding
import ecccomp.s2240788.mobile_android.ui.adapters.FocusKnowledgeAdapter
import ecccomp.s2240788.mobile_android.ui.adapters.FocusSubtaskAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.FocusSessionViewModel

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
        }

        // Timer mode
        viewModel.timerMode.observe(this) { mode ->
            updateTimerModeUI(mode)
        }

        // Current task
        viewModel.currentTask.observe(this) { task ->
            task?.let {
                binding.tvCurrentTask.text = it.title

                // Display estimated time (ViewModel already set the timer duration correctly)
                val estimatedMinutes = it.estimated_minutes ?: 25
                binding.tvEstimatedTime.text = "⏱ $estimatedMinutes ${getString(R.string.minutes_unit)}"

                // DO NOT call setTimerDuration() here - ViewModel already set it correctly in loadTask/loadTaskWithSubtask
                // Calling it here would override the correct subtask time with the modified task's time

                binding.tvFocusCount.text = getString(R.string.pomodoro_count_format, viewModel.getPomodoroCount())

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

        // Deep Work Mode observer (backup - should also trigger)
        viewModel.isDeepWorkMode.observe(this) { isDeepWork ->
            android.util.Log.d("FocusSessionActivity", "isDeepWorkMode LiveData changed: $isDeepWork")
            updateDeepWorkModeUI(isDeepWork)
        }

        // Session completed
        viewModel.focusSessionCompleted.observe(this) { completed ->
            if (completed) {
                // Play sound, show notification, etc.
                Toast.makeText(this, getString(R.string.session_completed), Toast.LENGTH_LONG).show()
                
                // Save notes if user entered any
                val notes = binding.etNotes.text?.toString()
                if (!notes.isNullOrBlank()) {
                    viewModel.saveFocusSessionWithNotes(notes)
                }
                
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
            if (items.isNullOrEmpty()) {
                binding.learningContentCard.visibility = View.GONE
                binding.emptyKnowledgeState.visibility = View.VISIBLE
            } else {
                binding.learningContentCard.visibility = View.VISIBLE
                binding.emptyKnowledgeState.visibility = View.GONE
                knowledgeAdapter.submitList(items)
                binding.tvKnowledgeCount.text = getString(R.string.items_count_format, items.size)
            }
        }

        // Knowledge loading state
        viewModel.isLoadingKnowledge.observe(this) { isLoading ->
            // You can show a loading indicator if needed
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
                viewModel.pauseTimer()
                Toast.makeText(this, "タスクを中断しました", Toast.LENGTH_SHORT).show()
                finish()
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
        // Only clean up if timer is not running
        if (viewModel.isTimerRunning.value != true) {
            viewModel.pauseTimer()
        }
    }
}

