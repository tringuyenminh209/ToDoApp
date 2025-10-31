package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout
import com.google.android.material.bottomsheet.BottomSheetDialog
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.Task
import ecccomp.s2240788.mobile_android.databinding.ActivityTaskListBinding
import ecccomp.s2240788.mobile_android.databinding.BottomSheetTaskOptionsBinding
import ecccomp.s2240788.mobile_android.ui.adapters.MainTaskAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.TaskViewModel
import com.google.android.material.tabs.TabLayout

/**
 * TaskListActivity
 * 全タスク一覧を表示する画面
 */
class TaskListActivity : BaseActivity() {

    private lateinit var binding: ActivityTaskListBinding
    private lateinit var taskViewModel: TaskViewModel
    private lateinit var taskAdapter: MainTaskAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityTaskListBinding.inflate(layoutInflater)
        setContentView(binding.root)

        taskViewModel = ViewModelProvider(this)[TaskViewModel::class.java]

        setupRecyclerView()
        setupClickListeners()
        setupObservers()
        setupTabLayout()

        // 初回タスク取得
        taskViewModel.fetchTasks()
    }

    private fun setupRecyclerView() {
        taskAdapter = MainTaskAdapter(
            onTaskClick = { task ->
                val intent = Intent(this, TaskDetailActivity::class.java)
                intent.putExtra("task_id", task.id)
                startActivity(intent)
            },
            onStartClick = { task ->
                if (task.status != "completed" && task.status != "in_progress") {
                    showStartTaskDialog(task)
                }
            },
            onMoreClick = { task ->
                showTaskOptionsBottomSheet(task)
            }
        )

        binding.rvTasks.apply {
            layoutManager = LinearLayoutManager(this@TaskListActivity)
            adapter = taskAdapter
            setHasFixedSize(true)
        }

        // Pull to refresh
        binding.swipeRefresh.setOnRefreshListener {
            taskViewModel.fetchTasks()
        }
    }

    private fun setupClickListeners() {
        // Back button
        binding.btnBack.setOnClickListener {
            finish()
        }

        // FAB: 新規タスク作成
        binding.fabAddTask.setOnClickListener {
            val intent = Intent(this, AddTaskActivity::class.java)
            startActivity(intent)
        }

        // Empty state: Add first task button
        binding.btnAddFirstTask.setOnClickListener {
            val intent = Intent(this, AddTaskActivity::class.java)
            startActivity(intent)
        }

        // Search
        binding.etSearch.addTextChangedListener(object : android.text.TextWatcher {
            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {}
            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {
                taskViewModel.setQuery(s?.toString() ?: "")
            }
            override fun afterTextChanged(s: android.text.Editable?) {}
        })
    }

    private fun setupTabLayout() {
        binding.tabLayout.addOnTabSelectedListener(object : TabLayout.OnTabSelectedListener {
            override fun onTabSelected(tab: TabLayout.Tab?) {
                when (tab?.position) {
                    0 -> taskViewModel.applyFilter(TaskViewModel.TaskFilter.ALL)
                    1 -> taskViewModel.applyFilter(TaskViewModel.TaskFilter.PENDING)
                    2 -> taskViewModel.applyFilter(TaskViewModel.TaskFilter.COMPLETED)
                }
            }

            override fun onTabUnselected(tab: TabLayout.Tab?) {}
            override fun onTabReselected(tab: TabLayout.Tab?) {}
        })
    }

    private fun setupObservers() {
        // タスクリスト
        taskViewModel.filteredTasks.observe(this) { tasks ->
            taskAdapter.submitList(tasks)

            // Empty state
            if (tasks.isEmpty()) {
                binding.emptyStateView.visibility = View.VISIBLE
                binding.rvTasks.visibility = View.GONE
            } else {
                binding.emptyStateView.visibility = View.GONE
                binding.rvTasks.visibility = View.VISIBLE
            }
        }

        // Loading state
        taskViewModel.isLoading.observe(this) { isLoading ->
            binding.swipeRefresh.isRefreshing = isLoading
        }

        // Error handling
        taskViewModel.error.observe(this) { error ->
            error?.let {
                showError(it)
                taskViewModel.clearError()
            }
        }

        // Task completed
        taskViewModel.taskCompleted.observe(this) { task ->
            task?.let {
                Toast.makeText(this, "タスクを完了しました！", Toast.LENGTH_SHORT).show()
                taskViewModel.clearTaskCompleted()
            }
        }

        // Task deleted
        taskViewModel.taskDeleted.observe(this) { deleted ->
            if (deleted) {
                Toast.makeText(this, "タスクを削除しました", Toast.LENGTH_SHORT).show()
                taskViewModel.clearTaskDeleted()
            }
        }

        // Navigate to Focus Session when task started
        taskViewModel.startedTaskId.observe(this) { taskId ->
            if (taskId != null) {
                // Get the started task details
                val startedTask = taskViewModel.tasks.value?.find { it.id == taskId }
                
                val intent = Intent(this, FocusSessionActivity::class.java)
                intent.putExtra("task_id", taskId)
                if (startedTask != null) {
                    intent.putExtra("task_title", startedTask.title)
                }
                startActivity(intent)
                
                // Reset the startedTaskId to prevent re-navigation on orientation change
                taskViewModel.clearStartedTaskId()
            }
        }
    }

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
                taskViewModel.startTask(task.id)
            }
            .setNegativeButton("キャンセル", null)
            .show()
    }

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
                val intent = Intent(this@TaskListActivity, EditTaskActivity::class.java)
                intent.putExtra("task_id", task.id)
                startActivity(intent)
            }

            // Complete option
            optionComplete.setOnClickListener {
                bottomSheet.dismiss()
                taskViewModel.completeTask(task.id)
            }

            // Delete option
            optionDelete.setOnClickListener {
                bottomSheet.dismiss()
                showDeleteConfirmationDialog(task)
            }

            // Cancel
            btnCancel.setOnClickListener {
                bottomSheet.dismiss()
            }
        }

        bottomSheet.show()
    }

    private fun showDeleteConfirmationDialog(task: Task) {
        AlertDialog.Builder(this)
            .setTitle("タスクを削除")
            .setMessage("「${task.title}」を削除しますか？\nこの操作は取り消せません。")
            .setPositiveButton("削除") { _, _ ->
                taskViewModel.deleteTask(task.id)
            }
            .setNegativeButton("キャンセル", null)
            .show()
    }

    private fun showError(message: String) {
        Toast.makeText(this, message, Toast.LENGTH_LONG).show()
    }

    override fun onResume() {
        super.onResume()
        // 画面に戻った時にタスクを再取得
        taskViewModel.fetchTasks()
    }
}
