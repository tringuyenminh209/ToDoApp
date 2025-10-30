package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.ViewModelProvider
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityTaskDetailBinding
import ecccomp.s2240788.mobile_android.ui.viewmodels.TaskDetailViewModel
import java.text.SimpleDateFormat
import java.util.Locale

class TaskDetailActivity : AppCompatActivity() {

    private lateinit var binding: ActivityTaskDetailBinding
    private lateinit var viewModel: TaskDetailViewModel
    private var taskId: Int = -1

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

        setupClicks()
        observeViewModel()

        viewModel.loadTask(taskId)
    }

    private fun setupClicks() {
        binding.btnBack.setOnClickListener { finish() }

        binding.btnEdit.setOnClickListener {
            val i = Intent(this, EditTaskActivity::class.java)
            i.putExtra("task_id", taskId)
            startActivity(i)
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
        }

        viewModel.toast.observe(this) { msg ->
            msg?.let { Toast.makeText(this, it, Toast.LENGTH_SHORT).show(); viewModel.clearToast() }
        }

        viewModel.finishEvent.observe(this) { done ->
            if (done == true) finish()
        }
    }
}


