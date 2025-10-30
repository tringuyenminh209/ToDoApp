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
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityTaskListBinding
import ecccomp.s2240788.mobile_android.ui.adapters.TaskAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.TaskViewModel
import com.google.android.material.tabs.TabLayout

/**
 * TaskListActivity
 * 全タスク一覧を表示する画面
 */
class TaskListActivity : AppCompatActivity() {

    private lateinit var binding: ActivityTaskListBinding
    private lateinit var taskViewModel: TaskViewModel
    private lateinit var taskAdapter: TaskAdapter

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
        taskAdapter = TaskAdapter(
            onTaskClick = { task ->
                // タスク編集画面へ遷移
                val intent = Intent(this, EditTaskActivity::class.java)
                intent.putExtra("task_id", task.id)
                startActivity(intent)
            },
            onTaskComplete = { task ->
                if (task.status != "completed") {
                    taskViewModel.completeTask(task.id)
                }
            },
            onTaskDelete = { task ->
                showDeleteConfirmationDialog(task.id)
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
    }

    private fun showDeleteConfirmationDialog(taskId: Int) {
        AlertDialog.Builder(this)
            .setTitle("タスクの削除")
            .setMessage("このタスクを削除しますか？")
            .setPositiveButton("削除") { _, _ ->
                taskViewModel.deleteTask(taskId)
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
