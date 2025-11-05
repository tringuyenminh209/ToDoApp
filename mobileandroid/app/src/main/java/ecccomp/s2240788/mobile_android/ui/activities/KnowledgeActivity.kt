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
    private lateinit var adapter: ecccomp.s2240788.mobile_android.ui.adapters.KnowledgeAdapter
    private var taskId: Int? = null
    private var taskTitle: String? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityKnowledgeBinding.inflate(layoutInflater)
        setContentView(binding.root)

        viewModel = ViewModelProvider(this)[KnowledgeViewModel::class.java]

        // Get task ID from intent (if coming from task detail)
        taskId = intent.getIntExtra("TASK_ID", -1).takeIf { it != -1 }
        taskTitle = intent.getStringExtra("TASK_TITLE")

        setupUI()
        setupClickListeners()
        setupObservers()

        // Only setup bottom navigation if not filtered by task
        if (taskId == null) {
            setupBottomNavigation()
        } else {
            binding.bottomNavigation.visibility = View.GONE
            // Update title to show task name
            taskTitle?.let {
                binding.tvTitle.text = it
                binding.tvSubtitle.text = "学習内容"
            }
        }

        // Load knowledge items
        taskId?.let {
            viewModel.loadKnowledgeItemsByTask(it)
        } ?: run {
            viewModel.loadKnowledgeItems()
        }
    }

    private fun setupUI() {
        // Setup Knowledge adapter
        adapter = ecccomp.s2240788.mobile_android.ui.adapters.KnowledgeAdapter(
            onItemClick = { item ->
                // TODO: Open knowledge detail activity
                Toast.makeText(this, "View: ${item.title}", Toast.LENGTH_SHORT).show()
            },
            onFavoriteClick = { item ->
                viewModel.toggleFavorite(item.id) {
                    Toast.makeText(this, "お気に入り更新", Toast.LENGTH_SHORT).show()
                }
            },
            onMenuClick = { item ->
                // TODO: Show menu options (edit, delete, archive, etc.)
                Toast.makeText(this, "Menu: ${item.title}", Toast.LENGTH_SHORT).show()
            }
        )

        // RecyclerView setup
        binding.rvKnowledge.layoutManager = LinearLayoutManager(this)
        binding.rvKnowledge.adapter = adapter

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
        // Observe filtered items instead of knowledgeItems
        viewModel.filteredItems.observe(this) { items ->
            if (items.isEmpty()) {
                binding.emptyState.visibility = View.VISIBLE
                binding.rvKnowledge.visibility = View.GONE
            } else {
                binding.emptyState.visibility = View.GONE
                binding.rvKnowledge.visibility = View.VISIBLE
                adapter.submitList(items)
            }
        }

        viewModel.error.observe(this) { error ->
            error?.let {
                Toast.makeText(this, it, Toast.LENGTH_LONG).show()
                viewModel.clearError()
            }
        }

        viewModel.isLoading.observe(this) { isLoading ->
            // Show/hide loading indicator (if needed)
            // binding.progressBarLoading?.visibility = if (isLoading) View.VISIBLE else View.GONE
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
        taskId?.let {
            viewModel.loadKnowledgeItemsByTask(it)
        } ?: run {
            viewModel.loadKnowledgeItems()
        }
    }
}

