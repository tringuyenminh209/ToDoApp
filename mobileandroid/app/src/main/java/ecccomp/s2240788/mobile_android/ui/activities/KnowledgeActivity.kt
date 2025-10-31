package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityKnowledgeBinding
import ecccomp.s2240788.mobile_android.ui.viewmodels.KnowledgeViewModel

/**
 * KnowledgeActivity
 * 知識管理画面 - 学習した内容を保存・整理
 * - Notes, Code snippets, Exercises, Resource links
 * - Categories and Tags
 * - Search and Filter
 * - Review tracking
 */
class KnowledgeActivity : BaseActivity() {

    private lateinit var binding: ActivityKnowledgeBinding
    private lateinit var viewModel: KnowledgeViewModel

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityKnowledgeBinding.inflate(layoutInflater)
        setContentView(binding.root)

        viewModel = ViewModelProvider(this)[KnowledgeViewModel::class.java]

        setupUI()
        setupClickListeners()
        setupObservers()
        setupBottomNavigation()

        // Load knowledge items
        viewModel.loadKnowledgeItems()
    }

    private fun setupUI() {
        // RecyclerView setup (will be implemented with adapter)
        binding.rvKnowledge.layoutManager = LinearLayoutManager(this)
        
        // Categories RecyclerView
        binding.rvCategories.layoutManager = LinearLayoutManager(
            this, 
            LinearLayoutManager.HORIZONTAL, 
            false
        )
    }

    private fun setupClickListeners() {
        binding.btnBack.setOnClickListener {
            finish()
        }

        binding.btnAddKnowledge.setOnClickListener {
            Toast.makeText(this, "Add Knowledge (開発中)", Toast.LENGTH_SHORT).show()
        }

        // Filter chips
        binding.chipAll.setOnCheckedChangeListener { _, isChecked ->
            if (isChecked) viewModel.setFilter(KnowledgeViewModel.FilterType.ALL)
        }

        binding.chipNotes.setOnCheckedChangeListener { _, isChecked ->
            if (isChecked) viewModel.setFilter(KnowledgeViewModel.FilterType.NOTES)
        }

        binding.chipCode.setOnCheckedChangeListener { _, isChecked ->
            if (isChecked) viewModel.setFilter(KnowledgeViewModel.FilterType.CODE)
        }

        binding.chipExercises.setOnCheckedChangeListener { _, isChecked ->
            if (isChecked) viewModel.setFilter(KnowledgeViewModel.FilterType.EXERCISES)
        }

        binding.chipLinks.setOnCheckedChangeListener { _, isChecked ->
            if (isChecked) viewModel.setFilter(KnowledgeViewModel.FilterType.LINKS)
        }

        binding.chipAttachments.setOnCheckedChangeListener { _, isChecked ->
            if (isChecked) viewModel.setFilter(KnowledgeViewModel.FilterType.ATTACHMENTS)
        }

        binding.chipFavorites.setOnCheckedChangeListener { _, isChecked ->
            if (isChecked) viewModel.setFilter(KnowledgeViewModel.FilterType.FAVORITES)
        }

        binding.chipArchived.setOnCheckedChangeListener { _, isChecked ->
            if (isChecked) viewModel.setFilter(KnowledgeViewModel.FilterType.ARCHIVED)
        }

        binding.chipDueReview.setOnCheckedChangeListener { _, isChecked ->
            if (isChecked) viewModel.setFilter(KnowledgeViewModel.FilterType.DUE_REVIEW)
        }

        // Search
        binding.etSearch.addTextChangedListener(object : android.text.TextWatcher {
            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {}
            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {
                viewModel.setQuery(s?.toString() ?: "")
            }
            override fun afterTextChanged(s: android.text.Editable?) {}
        })
    }

    private fun setupObservers() {
        viewModel.knowledgeItems.observe(this) { items ->
            if (items.isEmpty()) {
                binding.emptyState.visibility = View.VISIBLE
                binding.rvKnowledge.visibility = View.GONE
            } else {
                binding.emptyState.visibility = View.GONE
                binding.rvKnowledge.visibility = View.VISIBLE
                // TODO: Submit list to adapter when created
            }
        }

        viewModel.error.observe(this) { error ->
            error?.let {
                Toast.makeText(this, it, Toast.LENGTH_LONG).show()
            }
        }

        viewModel.isLoading.observe(this) { isLoading ->
            // TODO: Show/hide loading indicator
        }
    }

    /**
     * ボトムナビゲーションのセットアップ
     */
    private fun setupBottomNavigation() {
        binding.bottomNavigation.selectedItemId = R.id.nav_knowledge

        binding.bottomNavigation.setOnItemSelectedListener { item ->
            when (item.itemId) {
                R.id.nav_home -> {
                    startActivity(Intent(this, MainActivity::class.java))
                    finish()
                    true
                }
                R.id.nav_calendar -> {
                    startActivity(Intent(this, CalendarActivity::class.java))
                    finish()
                    true
                }
                R.id.nav_paths -> {
                    startActivity(Intent(this, PathsActivity::class.java))
                    finish()
                    true
                }
                R.id.nav_knowledge -> {
                    // Current screen
                    true
                }
                R.id.nav_settings -> {
                    startActivity(Intent(this, SettingsActivity::class.java))
                    finish()
                    true
                }
                else -> false
            }
        }
    }

    override fun onResume() {
        super.onResume()
        viewModel.loadKnowledgeItems()
    }
}

