package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.activity.viewModels
import androidx.appcompat.app.AlertDialog
import androidx.recyclerview.widget.LinearLayoutManager
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityLearningPathDetailBinding
import ecccomp.s2240788.mobile_android.ui.adapters.MilestoneAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.PathsViewModel

/**
 * Learning Path Detail Activity
 * 学習パス詳細画面
 * Displays detailed information about a user's learning path including:
 * - Progress statistics
 * - Milestones list with tasks
 * - Completion status
 */
class LearningPathDetailActivity : BaseActivity() {

    private lateinit var binding: ActivityLearningPathDetailBinding
    private val viewModel: PathsViewModel by viewModels()

    private lateinit var milestoneAdapter: MilestoneAdapter
    private var pathId: Int = 0

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityLearningPathDetailBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupWindowInsets()

        // Get learning path ID from intent
        pathId = intent.getIntExtra("LEARNING_PATH_ID", 0)
        if (pathId == 0) {
            Toast.makeText(this, "エラー: 学習パスIDが無効です", Toast.LENGTH_SHORT).show()
            finish()
            return
        }

        setupToolbar()
        setupRecyclerView()
        setupObservers()
        setupClickListeners()

        // Load learning path detail
        viewModel.getLearningPathDetail(pathId)
    }

    private fun setupToolbar() {
        binding.btnBack.setOnClickListener {
            finish()
        }
    }

    private fun setupRecyclerView() {
        milestoneAdapter = MilestoneAdapter(
            onMilestoneClick = { milestone ->
                // Navigate directly to milestone knowledge/content (code + explanation)
                val intent = Intent(this, MilestoneKnowledgeActivity::class.java)
                intent.putExtra("MILESTONE_ID", milestone.id)
                intent.putExtra("MILESTONE_TITLE", milestone.title)
                intent.putExtra("LEARNING_PATH_ID", pathId)
                startActivity(intent)
            }
        )
        binding.rvMilestones.apply {
            layoutManager = LinearLayoutManager(this@LearningPathDetailActivity)
            adapter = milestoneAdapter
        }
    }

    private fun setupObservers() {
        // Learning Path Detail
        viewModel.pathDetail.observe(this) { path ->
            path?.let {
                binding.apply {
                    // Title and description
                    tvTitle.text = it.title
                    tvDescription.text = it.description ?: "説明なし"

                    // Status badge
                    tvStatus.text = when (it.status) {
                        "active" -> "進行中"
                        "in_progress" -> "進行中"
                        "completed" -> "完了"
                        "paused" -> "一時停止"
                        else -> it.status
                    }

                    // Set status color
                    val statusColor = when (it.status) {
                        "completed" -> getColor(R.color.success)
                        "paused" -> getColor(R.color.warning)
                        else -> getColor(R.color.primary)
                    }
                    tvStatus.setBackgroundColor(statusColor)

                    // Progress
                    progressBar.progress = it.progress_percentage
                    tvProgress.text = "${it.progress_percentage}%"

                    // Milestones statistics
                    tvMilestonesCount.text = "${it.completed_milestones}/${it.total_milestones}"

                    // Target date
                    if (!it.target_date.isNullOrEmpty()) {
                        tvTargetDate.text = it.target_date
                        tvTargetDate.visibility = View.VISIBLE
                        tvTargetDateLabel.visibility = View.VISIBLE
                    } else {
                        tvTargetDate.visibility = View.GONE
                        tvTargetDateLabel.visibility = View.GONE
                    }

                    // Milestones list
                    it.milestones?.let { milestones ->
                        milestoneAdapter.submitList(milestones)
                        sectionMilestones.visibility = View.VISIBLE
                    } ?: run {
                        sectionMilestones.visibility = View.GONE
                    }

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

    private fun setupClickListeners() {
        // Edit learning path
        binding.btnEdit.setOnClickListener {
            // TODO: Navigate to edit learning path activity
            Toast.makeText(this, "編集機能は開発中です", Toast.LENGTH_SHORT).show()
        }

        // Delete learning path
        binding.btnDelete.setOnClickListener {
            AlertDialog.Builder(this)
                .setTitle("学習パスを削除")
                .setMessage("この学習パスを削除しますか？この操作は取り消せません。")
                .setPositiveButton("削除") { _, _ ->
                    viewModel.deletePath(pathId)
                    finish()
                }
                .setNegativeButton("キャンセル", null)
                .show()
        }
    }

    override fun onDestroy() {
        super.onDestroy()
        viewModel.clearPathDetail()
    }
}
