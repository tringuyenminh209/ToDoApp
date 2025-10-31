package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import com.google.android.material.textfield.TextInputEditText
import com.google.android.material.textfield.TextInputLayout
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityTaskDetailBinding
import ecccomp.s2240788.mobile_android.ui.adapters.SubtaskDisplayAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.TaskDetailViewModel
import java.text.SimpleDateFormat
import java.util.Locale

class TaskDetailActivity : BaseActivity() {

    private lateinit var binding: ActivityTaskDetailBinding
    private lateinit var viewModel: TaskDetailViewModel
    private var taskId: Int = -1
    private lateinit var subtaskAdapter: SubtaskDisplayAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityTaskDetailBinding.inflate(layoutInflater)
        setContentView(binding.root)

        taskId = intent.getIntExtra("task_id", -1)
        if (taskId == -1) {
            Toast.makeText(this, "タスクIDが無効です", Toast.LENGTH_LONG).show()
            finish(); return
        }

        viewModel = ViewModelProvider(this)[TaskDetailViewModel::class.java]

        setupSubtaskRecyclerView()
        setupClicks()
        observeViewModel()

        viewModel.loadTask(taskId)
    }

    private fun setupSubtaskRecyclerView() {
        subtaskAdapter = SubtaskDisplayAdapter { subtask ->
            viewModel.toggleSubtask(subtask.id)
        }
        binding.rvSubtasks.layoutManager = LinearLayoutManager(this)
        binding.rvSubtasks.adapter = subtaskAdapter
    }

    private fun setupClicks() {
        binding.btnBack.setOnClickListener { finish() }

        binding.btnEdit.setOnClickListener {
            val i = Intent(this, EditTaskActivity::class.java)
            i.putExtra("task_id", taskId)
            startActivity(i)
        }

        binding.btnAddSubtask.setOnClickListener {
            showAddSubtaskDialog()
        }

        binding.btnDelete.setOnClickListener {
            AlertDialog.Builder(this)
                .setTitle(getString(R.string.delete))
                .setMessage(getString(R.string.confirm_delete_message))
                .setPositiveButton(getString(R.string.delete)) { _, _ ->
                    viewModel.deleteTask(taskId)
                }
                .setNegativeButton(getString(R.string.cancel), null)
                .show()
        }

        binding.btnComplete.setOnClickListener {
            viewModel.completeTask(taskId)
        }

        binding.btnStartFocus.setOnClickListener {
            // Check task status first
            val currentTask = viewModel.task.value
            if (currentTask?.status == "completed") {
                Toast.makeText(this, "このタスクは既に完了しています", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }
            if (currentTask?.status == "in_progress") {
                // Already in progress, navigate directly to focus session
                val intent = Intent(this, FocusSessionActivity::class.java)
                intent.putExtra("task_id", taskId)
                startActivity(intent)
            } else {
                // Start the task first
                viewModel.startTask(taskId)
            }
        }
    }

    private fun observeViewModel() {
        viewModel.task.observe(this) { task ->
            task ?: return@observe

            binding.tvTaskTitle.text = task.title

            // Deadline (yyyy-MM-dd -> MM/dd)
            val deadlineStr = task.deadline
            if (!deadlineStr.isNullOrEmpty()) {
                try {
                    val inFmt = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
                    val outFmt = SimpleDateFormat("MM/dd", Locale.getDefault())
                    val d = inFmt.parse(deadlineStr)
                    binding.tvDueDate.text = getString(R.string.due_date) + ": " + (if (d!=null) outFmt.format(d) else deadlineStr)
                } catch (_: Exception) {
                    binding.tvDueDate.text = getString(R.string.due_date) + ": " + deadlineStr
                }
            }

            // Priority label
            val priText = when (task.priority) {
                5,4 -> getString(R.string.priority_high)
                1,2 -> getString(R.string.priority_low)
                else -> getString(R.string.priority_medium)
            }
            binding.tvPriority.text = getString(R.string.task_priority) + ": " + priText

            // Description
            binding.tvDescription.text = task.description ?: ""

            // Subtasks
            val subtasks = task.subtasks ?: emptyList()
            if (subtasks.isEmpty()) {
                binding.rvSubtasks.visibility = View.GONE
                binding.emptySubtasks.visibility = View.VISIBLE
            } else {
                binding.rvSubtasks.visibility = View.VISIBLE
                binding.emptySubtasks.visibility = View.GONE
                subtaskAdapter.submitList(subtasks)
            }
        }

        viewModel.toast.observe(this) { msg ->
            msg?.let { Toast.makeText(this, it, Toast.LENGTH_SHORT).show(); viewModel.clearToast() }
        }

        viewModel.finishEvent.observe(this) { done ->
            if (done == true) finish()
        }

        // Navigate to Focus Session when task started
        viewModel.startedTaskId.observe(this) { startedTaskId ->
            if (startedTaskId != null) {
                val intent = Intent(this, FocusSessionActivity::class.java)
                intent.putExtra("task_id", startedTaskId)
                viewModel.task.value?.let { task ->
                    intent.putExtra("task_title", task.title)
                }
                startActivity(intent)
                
                // Reset the startedTaskId to prevent re-navigation on orientation change
                viewModel.clearStartedTaskId()
            }
        }
    }

    private fun showAddSubtaskDialog() {
        // Create a custom view for the dialog
        val dialogView = layoutInflater.inflate(R.layout.dialog_add_subtask, null)
        val etSubtaskTitle = dialogView.findViewById<TextInputEditText>(R.id.et_subtask_title)
        val tilSubtaskTitle = dialogView.findViewById<TextInputLayout>(R.id.til_subtask_title)

        val dialog = AlertDialog.Builder(this)
            .setTitle(getString(R.string.add_subtask))
            .setView(dialogView)
            .setPositiveButton(getString(R.string.add)) { _, _ ->
                val title = etSubtaskTitle.text.toString().trim()
                if (title.isNotEmpty()) {
                    viewModel.addSubtask(taskId, title)
                } else {
                    Toast.makeText(this, "サブタスク名を入力してください", Toast.LENGTH_SHORT).show()
                }
            }
            .setNegativeButton(getString(R.string.cancel), null)
            .create()

        dialog.show()
    }
}


