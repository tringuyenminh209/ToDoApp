package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.activity.viewModels
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.GridLayoutManager
import androidx.recyclerview.widget.LinearLayoutManager
import ecccomp.s2240788.mobile_android.databinding.ActivityTemplateBrowserBinding
import ecccomp.s2240788.mobile_android.data.models.LearningPathTemplate
import ecccomp.s2240788.mobile_android.data.models.TemplateCategory
import ecccomp.s2240788.mobile_android.ui.adapters.TemplateCategoryAdapter
import ecccomp.s2240788.mobile_android.ui.adapters.TemplateListAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.TemplateViewModel

/**
 * Template Browser Activity
 * テンプレートライブラリのブラウザ画面
 */
class TemplateBrowserActivity : AppCompatActivity() {

    private lateinit var binding: ActivityTemplateBrowserBinding
    private val viewModel: TemplateViewModel by viewModels()

    private lateinit var featuredAdapter: TemplateListAdapter
    private lateinit var popularAdapter: TemplateListAdapter
    private lateinit var categoryAdapter: TemplateCategoryAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityTemplateBrowserBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupToolbar()
        setupRecyclerViews()
        setupObservers()
        setupClickListeners()

        // Load initial data
        viewModel.getFeaturedTemplates()
        viewModel.getPopularTemplates()
        viewModel.getCategories()
    }

    private fun setupToolbar() {
        setSupportActionBar(binding.toolbar)
        supportActionBar?.apply {
            setDisplayHomeAsUpEnabled(true)
            setDisplayShowHomeEnabled(true)
            title = "ロードマップテンプレート"
        }
    }

    private fun setupRecyclerViews() {
        // Featured Templates
        featuredAdapter = TemplateListAdapter { template ->
            navigateToTemplateDetail(template)
        }
        binding.rvFeatured.apply {
            layoutManager = LinearLayoutManager(
                this@TemplateBrowserActivity,
                LinearLayoutManager.HORIZONTAL,
                false
            )
            adapter = featuredAdapter
        }

        // Popular Templates
        popularAdapter = TemplateListAdapter { template ->
            navigateToTemplateDetail(template)
        }
        binding.rvPopular.apply {
            layoutManager = LinearLayoutManager(this@TemplateBrowserActivity)
            adapter = popularAdapter
        }

        // Categories
        categoryAdapter = TemplateCategoryAdapter { category ->
            navigateToCategory(category)
        }
        binding.rvCategories.apply {
            layoutManager = GridLayoutManager(this@TemplateBrowserActivity, 2)
            adapter = categoryAdapter
        }
    }

    private fun setupObservers() {
        // Featured Templates
        viewModel.featuredTemplates.observe(this) { templates ->
            featuredAdapter.submitList(templates)
            binding.sectionFeatured.visibility = if (templates.isNotEmpty()) View.VISIBLE else View.GONE
        }

        // Popular Templates
        viewModel.popularTemplates.observe(this) { templates ->
            popularAdapter.submitList(templates)
            binding.sectionPopular.visibility = if (templates.isNotEmpty()) View.VISIBLE else View.GONE
        }

        // Categories
        viewModel.categories.observe(this) { categories ->
            categoryAdapter.submitList(categories)
            binding.sectionCategories.visibility = if (categories.isNotEmpty()) View.VISIBLE else View.GONE
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

    private fun setupClickListeners() {
        binding.btnViewAllFeatured.setOnClickListener {
            // Navigate to all featured templates
            navigateToAllTemplates("featured")
        }

        binding.btnViewAllPopular.setOnClickListener {
            // Navigate to all popular templates
            navigateToAllTemplates("popular")
        }

        binding.btnCreateManual.setOnClickListener {
            // Navigate to manual creation
            // TODO: Implement manual creation flow
            Toast.makeText(this, "手動作成機能は開発中です", Toast.LENGTH_SHORT).show()
        }
    }

    private fun navigateToTemplateDetail(template: LearningPathTemplate) {
        val intent = Intent(this, TemplateDetailActivity::class.java)
        intent.putExtra("TEMPLATE_ID", template.id)
        startActivity(intent)
    }

    private fun navigateToCategory(category: TemplateCategory) {
        val intent = Intent(this, TemplateListActivity::class.java)
        intent.putExtra("CATEGORY", category.value)
        intent.putExtra("TITLE", category.displayName)
        startActivity(intent)
    }

    private fun navigateToAllTemplates(type: String) {
        val intent = Intent(this, TemplateListActivity::class.java)
        intent.putExtra("TYPE", type)
        startActivity(intent)
    }

    override fun onSupportNavigateUp(): Boolean {
        onBackPressed()
        return true
    }
}

