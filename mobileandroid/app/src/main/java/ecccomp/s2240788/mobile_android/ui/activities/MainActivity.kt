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

class MainActivity : AppCompatActivity() {

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

    private fun setupClickListeners() {
        // Header actions
        binding.btnLanguage.setOnClickListener {
            // Long press to show logout option
            showLogoutDialog()
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
            Toast.makeText(this, "Start 5-min task", Toast.LENGTH_SHORT).show()
        }

        binding.btnReschedule.setOnClickListener {
            Toast.makeText(this, "Reschedule tasks", Toast.LENGTH_SHORT).show()
        }

        // Progress links
        binding.btnViewStats.setOnClickListener {
            Toast.makeText(this, "View statistics", Toast.LENGTH_SHORT).show()
        }

        binding.btnViewTimetable.setOnClickListener {
            Toast.makeText(this, "View timetable", Toast.LENGTH_SHORT).show()
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
                val intent = Intent(this@MainActivity, TaskListActivity::class.java)
                startActivity(intent)
            }

            // Complete option
            optionComplete.setOnClickListener {
                bottomSheet.dismiss()
                Toast.makeText(this@MainActivity, "タスクを完了しました", Toast.LENGTH_SHORT).show()
                // TODO: Call viewModel to mark as complete
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
            .setMessage("「${task.title}」を削除しますか？")
            .setPositiveButton("削除") { _, _ ->
                Toast.makeText(this, "タスクを削除しました", Toast.LENGTH_SHORT).show()
                // TODO: Call viewModel to delete task
                viewModel.getTasks() // Refresh
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
                Toast.makeText(this, "タスクを開始しました！", Toast.LENGTH_SHORT).show()
                // TODO: Call API to mark task as in_progress
                // For now, just navigate to task list
                val intent = Intent(this, TaskListActivity::class.java)
                startActivity(intent)
            }
            .setNegativeButton("キャンセル", null)
            .show()
    }
}