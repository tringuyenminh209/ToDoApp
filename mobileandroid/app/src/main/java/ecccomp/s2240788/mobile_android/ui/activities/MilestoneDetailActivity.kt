package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.activity.viewModels
import androidx.appcompat.app.AlertDialog
import androidx.recyclerview.widget.LinearLayoutManager
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityMilestoneDetailBinding
import ecccomp.s2240788.mobile_android.ui.adapters.MilestoneTaskAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.PathsViewModel

/**
 * Milestone Detail Activity
 * マイルストーン詳細画面
 * Displays detailed information about a milestone including:
 * - Tasks list with estimated time
 * - Progress statistics
 * - Resources for learning
 */
class MilestoneDetailActivity : BaseActivity() {

    private lateinit var binding: ActivityMilestoneDetailBinding
    private val viewModel: PathsViewModel by viewModels()

    private lateinit var taskAdapter: MilestoneTaskAdapter
    private var milestoneId: Int = 0
    private var pathId: Int = 0

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityMilestoneDetailBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupWindowInsets()

        // Get milestone ID and path ID from intent
        milestoneId = intent.getIntExtra("MILESTONE_ID", 0)
        pathId = intent.getIntExtra("LEARNING_PATH_ID", 0)

        if (milestoneId == 0 || pathId == 0) {
            Toast.makeText(this, "エラー: マイルストーンIDが無効です", Toast.LENGTH_SHORT).show()
            finish()
            return
        }

        setupToolbar()
        setupRecyclerView()
        setupObservers()

        // Load learning path detail to get milestone data
        viewModel.getLearningPathDetail(pathId)
    }

    private fun setupToolbar() {
        binding.btnBack.setOnClickListener {
            finish()
        }

        binding.btnViewKnowledge.setOnClickListener {
            // Navigate to milestone knowledge activity
            val intent = Intent(this, MilestoneKnowledgeActivity::class.java)
            intent.putExtra("MILESTONE_ID", milestoneId)
            intent.putExtra("MILESTONE_TITLE", binding.tvTitle.text.toString())
            startActivity(intent)
        }
    }

    private fun setupRecyclerView() {
        taskAdapter = MilestoneTaskAdapter(
            onStartTask = { task ->
                // Navigate to focus session with this task
                val intent = Intent(this, FocusSessionActivity::class.java)
                intent.putExtra("task_id", task.id)
                startActivity(intent)
            },
            onStartSubtask = { task, subtaskIndex ->
                // Navigate to focus session with main task (subtask will be handled in focus session)
                val intent = Intent(this, FocusSessionActivity::class.java)
                intent.putExtra("task_id", task.id)
                intent.putExtra("subtask_index", subtaskIndex)
                startActivity(intent)
            }
        )
        binding.rvTasks.apply {
            layoutManager = LinearLayoutManager(this@MilestoneDetailActivity)
            adapter = taskAdapter
        }
    }

    private fun setupObservers() {
        // Learning Path Detail (to get milestone data)
        viewModel.pathDetail.observe(this) { path ->
            path?.let {
                // Find the milestone by ID
                val milestone = it.milestones?.find { m -> m.id == milestoneId }

                milestone?.let { m ->
                    binding.apply {
                        // Title and description
                        tvTitle.text = m.title
                        tvDescription.text = m.description ?: "説明なし"

                        // Status badge
                        val statusText = when (m.status.lowercase()) {
                            "not_started", "pending" -> getString(R.string.status_pending)
                            "in_progress" -> getString(R.string.status_in_progress)
                            "completed" -> getString(R.string.status_completed)
                            else -> getString(R.string.status_pending)
                        }
                        tvStatus.text = statusText

                        // Status color
                        val statusColor = when (m.status.lowercase()) {
                            "completed" -> getColor(R.color.success)
                            "in_progress" -> getColor(R.color.primary)
                            else -> getColor(R.color.text_muted)
                        }
                        tvStatus.setBackgroundColor(statusColor)

                        // Tasks list
                        val tasks = m.tasks
                        if (!tasks.isNullOrEmpty()) {
                            taskAdapter.submitList(tasks)
                            rvTasks.visibility = View.VISIBLE
                            emptyState.visibility = View.GONE
                        } else {
                            rvTasks.visibility = View.GONE
                            emptyState.visibility = View.VISIBLE
                        }

                    }
                } ?: run {
                    Toast.makeText(this, "マイルストーンが見つかりません", Toast.LENGTH_SHORT).show()
                    finish()
                }
            }
        }

        // Loading State
        viewModel.isLoading.observe(this) { isLoading ->
            binding.progressBarLoading.visibility = if (isLoading) View.VISIBLE else View.GONE
            binding.scrollView.visibility = if (isLoading) View.GONE else View.VISIBLE
        }

        // Error
        viewModel.error.observe(this) { error ->
            error?.let {
                Toast.makeText(this, it, Toast.LENGTH_SHORT).show()
            }
        }
    }


    override fun onDestroy() {
        super.onDestroy()
        viewModel.clearPathDetail()
    }
}
