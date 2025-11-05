package ecccomp.s2240788.mobile_android.ui.activities

import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import androidx.lifecycle.ViewModelProvider
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityFocusSessionBinding
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
    private var taskId: Int = -1
    private var subtaskIndex: Int = -1

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityFocusSessionBinding.inflate(layoutInflater)
        setContentView(binding.root)

        viewModel = ViewModelProvider(this)[FocusSessionViewModel::class.java]

        // Get task ID and subtask index from intent
        taskId = intent.getIntExtra("task_id", -1)
        subtaskIndex = intent.getIntExtra("subtask_index", -1)

        if (taskId != -1) {
            if (subtaskIndex != -1) {
                // Focus on specific subtask
                viewModel.loadTaskWithSubtask(taskId, subtaskIndex)
            } else {
                // Focus on main task
                viewModel.loadTask(taskId)
            }
        }

        setupClickListeners()
        observeViewModel()
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

                // Set timer to task's estimated time
                val estimatedMinutes = it.estimated_minutes ?: 25
                binding.tvEstimatedTime.text = "⏱ ${estimatedMinutes} phút"

                // Set timer duration to task's estimated time
                viewModel.setTimerDuration(estimatedMinutes)

                binding.tvFocusCount.text = "🎯 ${viewModel.getPomodoroCount()}/5 Pomodoro"
            }
        }

        // Session completed
        viewModel.focusSessionCompleted.observe(this) { completed ->
            if (completed) {
                // Play sound, show notification, etc.
                Toast.makeText(this, "Session hoàn thành!", Toast.LENGTH_LONG).show()
                
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
     * タイマーモードのUI更新
     */
    private fun updateTimerModeUI(mode: FocusSessionViewModel.TimerMode) {
        when (mode) {
            FocusSessionViewModel.TimerMode.WORK -> {
                binding.tvTimerMode.text = "Làm việc"
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
                binding.tvTimerMode.text = "Nghỉ ngắn"
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
                binding.tvTimerMode.text = "Nghỉ dài"
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

