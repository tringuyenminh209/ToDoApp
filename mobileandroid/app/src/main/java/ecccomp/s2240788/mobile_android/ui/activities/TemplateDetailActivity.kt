package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.graphics.Color
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.activity.viewModels
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.LinearLayoutManager
import ecccomp.s2240788.mobile_android.databinding.ActivityTemplateDetailBinding
import ecccomp.s2240788.mobile_android.data.models.getFormattedDuration
import ecccomp.s2240788.mobile_android.data.models.getTotalMilestones
import ecccomp.s2240788.mobile_android.data.models.getTotalTasks
import ecccomp.s2240788.mobile_android.ui.adapters.MilestoneTemplateAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.TemplateViewModel

/**
 * Template Detail Activity
 * テンプレート詳細画面
 */
class TemplateDetailActivity : AppCompatActivity() {

    private lateinit var binding: ActivityTemplateDetailBinding
    private val viewModel: TemplateViewModel by viewModels()
    
    private lateinit var milestoneAdapter: MilestoneTemplateAdapter
    private var templateId: Long = 0

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityTemplateDetailBinding.inflate(layoutInflater)
        setContentView(binding.root)

        // Get template ID from intent
        templateId = intent.getLongExtra("TEMPLATE_ID", 0)
        if (templateId == 0L) {
            Toast.makeText(this, "エラー: テンプレートIDが無効です", Toast.LENGTH_SHORT).show()
            finish()
            return
        }

        setupToolbar()
        setupRecyclerView()
        setupObservers()
        setupClickListeners()

        // Load template detail
        viewModel.getTemplateDetail(templateId)
    }

    private fun setupToolbar() {
        setSupportActionBar(binding.toolbar)
        supportActionBar?.apply {
            setDisplayHomeAsUpEnabled(true)
            setDisplayShowHomeEnabled(true)
            title = "テンプレート詳細"
        }
    }

    private fun setupRecyclerView() {
        milestoneAdapter = MilestoneTemplateAdapter()
        binding.rvMilestones.apply {
            layoutManager = LinearLayoutManager(this@TemplateDetailActivity)
            adapter = milestoneAdapter
        }
    }

    private fun setupObservers() {
        // Template Detail
        viewModel.templateDetail.observe(this) { template ->
            binding.apply {
                // Header
                tvIcon.text = template.icon ?: "📚"
                tvTitle.text = template.title
                tvDescription.text = template.description ?: ""
                
                // Badges
                tvCategory.text = template.category.displayName
                tvDifficulty.text = template.difficulty.displayName
                tvDifficulty.setTextColor(Color.parseColor(template.difficulty.color))
                
                // Featured badge
                if (template.isFeatured) {
                    chipFeatured.visibility = View.VISIBLE
                } else {
                    chipFeatured.visibility = View.GONE
                }
                
                // Stats
                tvDuration.text = template.getFormattedDuration()
                tvMilestonesCount.text = "${template.getTotalMilestones()}"
                tvTasksCount.text = "${template.getTotalTasks()}"
                tvUsageCount.text = "${template.usageCount}人"
                
                // Tags
                if (!template.tags.isNullOrEmpty()) {
                    tvTags.text = template.tags.joinToString(" • ")
                    tvTags.visibility = View.VISIBLE
                } else {
                    tvTags.visibility = View.GONE
                }
                
                // Milestones
                template.milestones?.let { milestones ->
                    milestoneAdapter.submitList(milestones)
                    sectionMilestones.visibility = View.VISIBLE
                } ?: run {
                    sectionMilestones.visibility = View.GONE
                }
                
                // Color accent
                try {
                    val color = Color.parseColor(template.color)
                    appBarLayout.setBackgroundColor(color)
                    fabClone.backgroundTintList = android.content.res.ColorStateList.valueOf(color)
                } catch (e: Exception) {
                    // Use default color
                }
            }
        }

        // Loading State
        viewModel.isLoading.observe(this) { isLoading ->
            binding.progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
            binding.fabClone.isEnabled = !isLoading
        }

        // Error
        viewModel.error.observe(this) { error ->
            error?.let {
                Toast.makeText(this, it, Toast.LENGTH_SHORT).show()
                viewModel.clearError()
            }
        }

        // Clone Success
        viewModel.clonedLearningPathId.observe(this) { learningPathId ->
            learningPathId?.let {
                Toast.makeText(this, "学習パスを作成しました！", Toast.LENGTH_LONG).show()
                
                // Navigate to Roadmap detail (if activity exists)
                // TODO: Implement navigation to RoadmapDetailActivity
                // val intent = Intent(this, RoadmapDetailActivity::class.java)
                // intent.putExtra("LEARNING_PATH_ID", it)
                // startActivity(intent)
                
                finish()
                viewModel.clearClonedLearningPathId()
            }
        }

        // Success Message
        viewModel.message.observe(this) { message ->
            message?.let {
                Toast.makeText(this, it, Toast.LENGTH_SHORT).show()
                viewModel.clearMessage()
            }
        }
    }

    private fun setupClickListeners() {
        binding.fabClone.setOnClickListener {
            // Show confirmation dialog
            androidx.appcompat.app.AlertDialog.Builder(this)
                .setTitle("テンプレートを使用")
                .setMessage("このテンプレートから学習パスを作成しますか？\n\n作成後、自由にカスタマイズできます。")
                .setPositiveButton("作成") { _, _ ->
                    viewModel.cloneTemplate(templateId)
                }
                .setNegativeButton("キャンセル", null)
                .show()
        }
    }

    override fun onSupportNavigateUp(): Boolean {
        onBackPressed()
        return true
    }
}

