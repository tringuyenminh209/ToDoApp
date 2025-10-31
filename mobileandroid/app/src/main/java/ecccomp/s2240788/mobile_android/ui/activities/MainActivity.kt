package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import com.google.android.material.bottomsheet.BottomSheetDialog
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.Task
import ecccomp.s2240788.mobile_android.databinding.ActivityMainBinding
import ecccomp.s2240788.mobile_android.databinding.BottomSheetTaskOptionsBinding
import ecccomp.s2240788.mobile_android.ui.adapters.MainTaskAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.LogoutViewModel
import ecccomp.s2240788.mobile_android.ui.viewmodels.MainViewModel
import ecccomp.s2240788.mobile_android.utils.LocaleHelper
import java.text.SimpleDateFormat
import java.util.Calendar
import java.util.Locale

class MainActivity : BaseActivity() {

    private lateinit var binding: ActivityMainBinding
    private lateinit var viewModel: MainViewModel
    private lateinit var logoutViewModel: LogoutViewModel
    private lateinit var taskAdapter: MainTaskAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityMainBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupViewModel()
        setupUI()
        setupObservers()  // Setup observers BEFORE loading data
        setupClickListeners()
        setupBottomNavigation()

        // Load tasks
        viewModel.getTasks()
    }

    override fun onResume() {
        super.onResume()
        // Refresh tasks when returning to this activity
        viewModel.getTasks()
    }

    private fun setupViewModel() {
        viewModel = ViewModelProvider(this)[MainViewModel::class.java]
        logoutViewModel = ViewModelProvider(this)[LogoutViewModel::class.java]
        observeLogoutViewModel()
    }

    private fun setupUI() {
        // Set current date
        updateCurrentDate()
        
        // Update language button text
        updateLanguageButton()
        
        // Set initial progress (45%)
        binding.progressRing.progress = 45

        // Setup RecyclerView
        taskAdapter = MainTaskAdapter(
            onTaskClick = { task ->
                // Click to view task details
                val intent = Intent(this, TaskDetailActivity::class.java)
                intent.putExtra("task_id", task.id)
                startActivity(intent)
            },
            onStartClick = { task ->
                // Start task - mark as in_progress
                showStartTaskDialog(task)
            },
            onMoreClick = { task ->
                showTaskOptionsBottomSheet(task)
            }
        )

        binding.rvTasks.apply {
            adapter = taskAdapter
            layoutManager = LinearLayoutManager(this@MainActivity)
            setHasFixedSize(true)
        }
    }
    
    /**
     * Update current date display based on locale
     */
    private fun updateCurrentDate() {
        val calendar = Calendar.getInstance()
        val currentLanguage = LocaleHelper.getLanguage(this)
        
        val dateFormat = when (currentLanguage) {
            LocaleHelper.LANGUAGE_JAPANESE -> {
                // 木曜日, 10月31日
                SimpleDateFormat("EEEE, M月d日", Locale.JAPANESE)
            }
            LocaleHelper.LANGUAGE_VIETNAMESE -> {
                // Thứ 5, 31/10/2024
                SimpleDateFormat("EEEE, dd/MM/yyyy", Locale("vi"))
            }
            LocaleHelper.LANGUAGE_ENGLISH -> {
                // Thursday, Oct 31, 2024
                SimpleDateFormat("EEEE, MMM d, yyyy", Locale.ENGLISH)
            }
            else -> SimpleDateFormat("EEEE, M月d日", Locale.JAPANESE)
        }
        
        binding.tvHeaderDate.text = dateFormat.format(calendar.time)
    }
    
    /**
     * Update language button text
     */
    private fun updateLanguageButton() {
        val currentLanguage = LocaleHelper.getLanguage(this)
        binding.btnLanguage.text = LocaleHelper.getLanguageShortName(currentLanguage)
    }

    private fun setupClickListeners() {
        // Header actions - Language switcher
        binding.btnLanguage.setOnClickListener {
            showLanguageDialog()
        }
        
        // Long press language button to show logout
        binding.btnLanguage.setOnLongClickListener {
            showLogoutDialog()
            true
        }

        binding.btnNotification.setOnClickListener {
            Toast.makeText(this, "Notifications", Toast.LENGTH_SHORT).show()
        }

        binding.btnAddTask.setOnClickListener {
            Toast.makeText(this, "Add task", Toast.LENGTH_SHORT).show()
        }

        // Daily actions
        binding.btnCheckin.setOnClickListener {
            Toast.makeText(this, "Daily check-in", Toast.LENGTH_SHORT).show()
        }

        binding.btnReview.setOnClickListener {
            Toast.makeText(this, "Daily review", Toast.LENGTH_SHORT).show()
        }

        binding.btnAiCoach.setOnClickListener {
            Toast.makeText(this, "AI Coach", Toast.LENGTH_SHORT).show()
        }

        // Quick actions
        binding.btnAiArrange.setOnClickListener {
            Toast.makeText(this, "AI Auto-arrange", Toast.LENGTH_SHORT).show()
        }

        binding.btnStart5min.setOnClickListener {
            showSelectTaskToStartDialog()
        }

        binding.btnReschedule.setOnClickListener {
            Toast.makeText(this, "Reschedule tasks", Toast.LENGTH_SHORT).show()
        }

        // Progress links
        binding.btnViewStats.setOnClickListener {
            val intent = Intent(this, StatsActivity::class.java)
            startActivity(intent)
        }

        binding.btnViewTimetable.setOnClickListener {
            val intent = Intent(this, TimetableActivity::class.java)
            startActivity(intent)
        }

        // View All Tasks button
        binding.btnViewAllTasks.setOnClickListener {
            val intent = Intent(this, TaskListActivity::class.java)
            startActivity(intent)
        }

        binding.btnAddTask.setOnClickListener {
            val intent = Intent(this, AddTaskActivity::class.java)
            startActivity(intent)
        }

        // FAB
//        binding.fabFocus.setOnClickListener {
//            Toast.makeText(this, "Start focus session", Toast.LENGTH_SHORT).show()
//        }
    }

    private fun setupObservers() {
        viewModel.error.observe(this) { error ->
            if (error != null) {
                Toast.makeText(this, error, Toast.LENGTH_LONG).show()
            }
        }

        viewModel.successMessage.observe(this) { message ->
            if (message != null) {
                Toast.makeText(this, message, Toast.LENGTH_SHORT).show()
            }
        }

        // Navigate to Focus Session when task started
        viewModel.startedTaskId.observe(this) { taskId ->
            if (taskId != null) {
                // Get the started task details
                val startedTask = viewModel.tasks.value?.find { it.id == taskId }
                
                val intent = Intent(this, FocusSessionActivity::class.java)
                intent.putExtra("task_id", taskId)
                if (startedTask != null) {
                    intent.putExtra("task_title", startedTask.title)
                }
                startActivity(intent)
                
                // Reset the startedTaskId to prevent re-navigation on orientation change
                viewModel.clearStartedTaskId()
            }
        }

        viewModel.tasks.observe(this) { tasks ->
            // Always submit list to adapter (even if empty/null)
            val taskList = tasks ?: emptyList()
            val topTasks = taskList.take(3)

            // Ensure list is visible before submit to avoid flicker
            if (topTasks.isNotEmpty()) {
                binding.rvTasks.visibility = View.VISIBLE
                binding.emptyState.visibility = View.GONE
            } else {
                binding.rvTasks.visibility = View.GONE
                binding.emptyState.visibility = View.VISIBLE
            }

            // Submit as new list to trigger update
            taskAdapter.submitList(topTasks.toList())
            taskAdapter.notifyDataSetChanged()
            Toast.makeText(this, "Tasks: ${topTasks.size}", Toast.LENGTH_SHORT).show()
        }
    }

    /**
     * ログアウト処理のObserverを設定
     */
    private fun observeLogoutViewModel() {
        logoutViewModel.logoutSuccess.observe(this) { success ->
            if (success) {
                // LoginActivityに遷移（バックスタックをクリア）
                val intent = Intent(this, LoginActivity::class.java).apply {
                    flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
                }
                startActivity(intent)
                finish()
            }
        }

        logoutViewModel.isLoading.observe(this) { isLoading ->
            // ローディング表示は必要に応じて実装
        }

        logoutViewModel.error.observe(this) { error ->
            if (error != null) {
                Toast.makeText(this, error, Toast.LENGTH_SHORT).show()
            }
        }
    }

    /**
     * 言語選択ダイアログを表示
     */
    private fun showLanguageDialog() {
        val currentLanguage = LocaleHelper.getLanguage(this)
        val languages = arrayOf(
            LocaleHelper.getLanguageDisplayName(LocaleHelper.LANGUAGE_JAPANESE),
            LocaleHelper.getLanguageDisplayName(LocaleHelper.LANGUAGE_VIETNAMESE),
            LocaleHelper.getLanguageDisplayName(LocaleHelper.LANGUAGE_ENGLISH)
        )
        
        val languageCodes = arrayOf(
            LocaleHelper.LANGUAGE_JAPANESE,
            LocaleHelper.LANGUAGE_VIETNAMESE,
            LocaleHelper.LANGUAGE_ENGLISH
        )
        
        // Find current selection
        val checkedItem = languageCodes.indexOf(currentLanguage)
        
        AlertDialog.Builder(this)
            .setTitle(getString(R.string.language))
            .setSingleChoiceItems(languages, checkedItem) { dialog, which ->
                val selectedLanguage = languageCodes[which]
                if (selectedLanguage != currentLanguage) {
                    // Save new language
                    LocaleHelper.setLanguage(this, selectedLanguage)
                    
                    // Recreate activity to apply new locale
                    recreate()
                }
                dialog.dismiss()
            }
            .setNegativeButton(getString(R.string.cancel), null)
            .show()
    }
    
    /**
     * ログアウト確認ダイアログを表示
     */
    private fun showLogoutDialog() {
        AlertDialog.Builder(this)
            .setTitle("ログアウト")
            .setMessage("ログアウトしますか？")
            .setPositiveButton("はい") { _, _ ->
                logoutViewModel.logout()
            }
            .setNegativeButton("いいえ", null)
            .show()
    }

    /**
     * タスクオプションのBottomSheetを表示
     */
    private fun showTaskOptionsBottomSheet(task: Task) {
        val bottomSheet = BottomSheetDialog(this)
        val sheetBinding = BottomSheetTaskOptionsBinding.inflate(layoutInflater)
        bottomSheet.setContentView(sheetBinding.root)

        sheetBinding.apply {
            // Set task title
            tvTaskTitle.text = task.title

            // Edit option
            optionEdit.setOnClickListener {
                bottomSheet.dismiss()
                val intent = Intent(this@MainActivity, EditTaskActivity::class.java)
                intent.putExtra("task_id", task.id)
                startActivity(intent)
            }

            // Complete option
            optionComplete.setOnClickListener {
                bottomSheet.dismiss()
                viewModel.completeTask(task.id)
            }

            // Delete option
            optionDelete.setOnClickListener {
                bottomSheet.dismiss()
                showDeleteConfirmDialog(task)
            }

            // Cancel
            btnCancel.setOnClickListener {
                bottomSheet.dismiss()
            }
        }

        bottomSheet.show()
    }

    /**
     * タスク削除確認ダイアログ
     */
    private fun showDeleteConfirmDialog(task: Task) {
        AlertDialog.Builder(this)
            .setTitle("タスクを削除")
            .setMessage("「${task.title}」を削除しますか？\nこの操作は取り消せません。")
            .setPositiveButton("削除") { _, _ ->
                viewModel.deleteTask(task.id)
            }
            .setNegativeButton("キャンセル", null)
            .show()
    }

    /**
     * タスク開始確認ダイアログ
     */
    private fun showStartTaskDialog(task: Task) {
        if (task.status == "in_progress") {
            Toast.makeText(this, "このタスクは既に進行中です", Toast.LENGTH_SHORT).show()
            return
        }

        if (task.status == "completed") {
            Toast.makeText(this, "このタスクは既に完了しています", Toast.LENGTH_SHORT).show()
            return
        }

        AlertDialog.Builder(this)
            .setTitle("タスクを開始")
            .setMessage("「${task.title}」を開始しますか？")
            .setPositiveButton("開始") { _, _ ->
                // Call API to mark task as in_progress
                viewModel.startTask(task.id)
            }
            .setNegativeButton("キャンセル", null)
            .show()
    }

    /**
     * タスク選択ダイアログ（開始ボタン用）
     */
    private fun showSelectTaskToStartDialog() {
        // Get current tasks from ViewModel
        val allTasks = viewModel.tasks.value ?: emptyList()

        // Filter out completed and in-progress tasks
        val availableTasks = allTasks.filter {
            it.status != "completed" && it.status != "in_progress"
        }

        if (availableTasks.isEmpty()) {
            Toast.makeText(this, "開始できるタスクがありません", Toast.LENGTH_SHORT).show()
            return
        }

        // Create task titles array
        val taskTitles = availableTasks.map { task ->
            val prioritySymbol = when (task.priority) {
                5 -> "🔴"
                4 -> "🟠"
                3 -> "🟡"
                2 -> "🟢"
                else -> "⚪"
            }
            "$prioritySymbol ${task.title}"
        }.toTypedArray()

        AlertDialog.Builder(this)
            .setTitle("タスクを選択")
            .setItems(taskTitles) { _, which ->
                val selectedTask = availableTasks[which]
                showStartTaskDialog(selectedTask)
            }
            .setNegativeButton("キャンセル", null)
            .show()
    }

    /**
     * ボトムナビゲーションのセットアップ
     */
    private fun setupBottomNavigation() {
        binding.bottomNavigation.selectedItemId = R.id.nav_home

        binding.bottomNavigation.setOnItemSelectedListener { item ->
            when (item.itemId) {
                R.id.nav_home -> {
                    // Current screen
                    true
                }
                R.id.nav_calendar -> {
                    startActivity(Intent(this, CalendarActivity::class.java))
                    finish()
                    true
                }
                R.id.nav_paths -> {
                    startActivity(Intent(this, PathsActivity::class.java))
                    finish()
                    true
                }
                R.id.nav_knowledge -> {
                    startActivity(Intent(this, KnowledgeActivity::class.java))
                    finish()
                    true
                }
                R.id.nav_settings -> {
                    startActivity(Intent(this, SettingsActivity::class.java))
                    finish()
                    true
                }
                else -> false
            }
        }
    }
}