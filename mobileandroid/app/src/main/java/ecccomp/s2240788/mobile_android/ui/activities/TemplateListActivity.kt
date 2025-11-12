package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.activity.viewModels
import androidx.recyclerview.widget.LinearLayoutManager
import ecccomp.s2240788.mobile_android.databinding.ActivityTemplateListBinding
import ecccomp.s2240788.mobile_android.data.models.LearningPathTemplate
import ecccomp.s2240788.mobile_android.data.models.TemplateCategory
import ecccomp.s2240788.mobile_android.ui.adapters.TemplateListAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.TemplateViewModel

/**
 * Template List Activity
 * テンプレート一覧画面
 */
class TemplateListActivity : BaseActivity() {

    private lateinit var binding: ActivityTemplateListBinding
    private val viewModel: TemplateViewModel by viewModels()
    
    private lateinit var templateAdapter: TemplateListAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityTemplateListBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupWindowInsets()

        val type = intent.getStringExtra("TYPE")
        val category = intent.getStringExtra("CATEGORY")
        val title = intent.getStringExtra("TITLE") ?: "テンプレート一覧"

        setupToolbar(title)
        setupRecyclerView()
        setupObservers()

        // Load templates based on type or category
        when {
            type == "featured" -> viewModel.getFeaturedTemplates()
            type == "popular" -> viewModel.getPopularTemplates()
            category != null -> {
                val templateCategory = TemplateCategory.fromValue(category)
                viewModel.getTemplatesByCategory(templateCategory)
            }
            else -> viewModel.getTemplates()
        }
    }

    private fun setupToolbar(title: String) {
        // Header card với back button thay vì toolbar
        binding.btnBack.setOnClickListener {
            finish()
        }
    }

    private fun setupRecyclerView() {
        templateAdapter = TemplateListAdapter { template ->
            navigateToTemplateDetail(template)
        }
        binding.rvTemplates.apply {
            layoutManager = LinearLayoutManager(this@TemplateListActivity)
            adapter = templateAdapter
        }
    }

    private fun setupObservers() {
        // Templates (from any source)
        viewModel.templates.observe(this) { templates ->
            templateAdapter.submitList(templates)
            updateEmptyState(templates.isEmpty())
        }

        // Featured Templates
        viewModel.featuredTemplates.observe(this) { templates ->
            templateAdapter.submitList(templates)
            updateEmptyState(templates.isEmpty())
        }

        // Popular Templates
        viewModel.popularTemplates.observe(this) { templates ->
            templateAdapter.submitList(templates)
            updateEmptyState(templates.isEmpty())
        }

        // Loading State
        viewModel.isLoading.observe(this) { isLoading ->
            binding.progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
        }

        // Error
        viewModel.error.observe(this) { error ->
            error?.let {
                Toast.makeText(this, it, Toast.LENGTH_SHORT).show()
                viewModel.clearError()
            }
        }
    }

    private fun updateEmptyState(isEmpty: Boolean) {
        binding.tvEmpty.visibility = if (isEmpty) View.VISIBLE else View.GONE
        binding.rvTemplates.visibility = if (isEmpty) View.GONE else View.VISIBLE
    }

    private fun navigateToTemplateDetail(template: LearningPathTemplate) {
        val intent = Intent(this, TemplateDetailActivity::class.java)
        intent.putExtra("TEMPLATE_ID", template.id)
        startActivity(intent)
    }

    override fun onSupportNavigateUp(): Boolean {
        onBackPressed()
        return true
    }
}

