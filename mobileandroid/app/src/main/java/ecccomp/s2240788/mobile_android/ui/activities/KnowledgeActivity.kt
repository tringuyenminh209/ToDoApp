package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.text.Editable
import android.text.TextWatcher
import android.view.View
import android.widget.PopupMenu
import android.widget.Toast
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityKnowledgeBinding
import ecccomp.s2240788.mobile_android.data.models.KnowledgeItem
import ecccomp.s2240788.mobile_android.ui.adapters.KnowledgeAdapter
import ecccomp.s2240788.mobile_android.ui.adapters.KnowledgeCategoryChipAdapter
import ecccomp.s2240788.mobile_android.ui.dialogs.CategorySelectorBottomSheet
import ecccomp.s2240788.mobile_android.ui.viewmodels.KnowledgeViewModel

/**
 * KnowledgeActivity
 * 知識管理画面 - Phase 1 Knowledge Base System
 * Features:
 * - Knowledge items list with filters
 * - Search functionality
 * - Category-based navigation
 * - Quick Capture FAB
 * - Integration with CheatCode
 */
class KnowledgeActivity : BaseActivity() {

    private lateinit var binding: ActivityKnowledgeBinding
    private lateinit var viewModel: KnowledgeViewModel
    private lateinit var adapter: KnowledgeAdapter
    private lateinit var categoryAdapter: KnowledgeCategoryChipAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityKnowledgeBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupWindowInsets()

        viewModel = ViewModelProvider(this)[KnowledgeViewModel::class.java]

        setupUI()
        setupClickListeners()
        setupObservers()
        setupBottomNavigation()
        setupFilters()

        // Load knowledge items and categories
        viewModel.loadKnowledgeItems()
        viewModel.loadCategories()
        viewModel.loadKnowledgeStats()
    }

    private fun setupUI() {
        // Update title
        binding.tvTitle.text = getString(R.string.knowledge_title)
        binding.tvSubtitle.text = getString(R.string.knowledge_subtitle)

        // Hide path selector (not needed for direct knowledge browsing)
        binding.pathSelectorCard.visibility = View.GONE

        // Setup Knowledge adapter
        adapter = KnowledgeAdapter(
            onItemClick = { item ->
                // Navigate to knowledge detail
                val intent = Intent(this, KnowledgeDetailActivity::class.java)
                intent.putExtra("KNOWLEDGE_ITEM_ID", item.id)
                startActivity(intent)
            },
            onFavoriteClick = { item ->
                viewModel.toggleFavorite(item.id)
            },
            onMenuClick = { item ->
                showItemMenu(item)
            }
        )

        // RecyclerView setup
        binding.rvKnowledge.layoutManager = LinearLayoutManager(this)
        binding.rvKnowledge.adapter = adapter

        // Setup Categories RecyclerView
        categoryAdapter = KnowledgeCategoryChipAdapter { category ->
            // カテゴリ選択ボトムシートを表示（現在選択中のカテゴリを渡す）
            showCategorySelectorBottomSheet(viewModel.getCurrentCategoryId())
        }
        binding.rvCategories.layoutManager = LinearLayoutManager(this, LinearLayoutManager.HORIZONTAL, false)
        binding.rvCategories.adapter = categoryAdapter
        
        // 「すべて」チップの設定
        binding.chipAllCategories.setOnClickListener {
            viewModel.clearCategoryFilter()
            updateSelectedCategory()
        }
        binding.chipAllCategories.isChecked = viewModel.getCurrentCategoryId() == null
        
        // カテゴリセクション全体をクリック可能にする
        binding.categoriesCard.setOnClickListener {
            showCategorySelectorBottomSheet(viewModel.getCurrentCategoryId())
        }
        
        // カテゴリ選択ボタン
        binding.btnSelectCategory.setOnClickListener {
            showCategorySelectorBottomSheet(viewModel.getCurrentCategoryId())
        }
    }

    private fun setupClickListeners() {
        // Quick Capture FAB
        binding.fabAddKnowledge.setOnClickListener {
            val intent = Intent(this, KnowledgeEditorActivity::class.java)
            startActivity(intent)
        }

        // Quick Capture button (header)
        binding.btnQuickCapture.setOnClickListener {
            val intent = Intent(this, QuickCaptureActivity::class.java)
            startActivity(intent)
        }

        // Header add button
        binding.btnAddKnowledge.setOnClickListener {
            val intent = Intent(this, KnowledgeEditorActivity::class.java)
            startActivity(intent)
        }

        // Empty state add button
        binding.btnAddKnowledgeEmpty.setOnClickListener {
            val intent = Intent(this, KnowledgeEditorActivity::class.java)
            startActivity(intent)
        }

        // CheatCode navigation
        binding.cheatCodeCard.setOnClickListener {
            val intent = Intent(this, CheatCodeActivity::class.java)
            startActivity(intent)
        }

        // Search
        binding.etSearch.addTextChangedListener(object : TextWatcher {
            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {}
            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {}
            override fun afterTextChanged(s: Editable?) {
                viewModel.setQuery(s?.toString() ?: "")
            }
        })
    }

    private fun setupObservers() {
        // Observe filtered knowledge items
        viewModel.filteredItems.observe(this) { items ->
            android.util.Log.d("KnowledgeActivity", "filteredItems observer triggered with ${items.size} items")
            if (items.isEmpty()) {
                android.util.Log.d("KnowledgeActivity", "Showing empty state")
                binding.emptyState.visibility = View.VISIBLE
                binding.rvKnowledge.visibility = View.GONE
            } else {
                android.util.Log.d("KnowledgeActivity", "Showing ${items.size} items in RecyclerView")
                binding.emptyState.visibility = View.GONE
                binding.rvKnowledge.visibility = View.VISIBLE
                adapter.submitList(items)
            }
        }

        viewModel.error.observe(this) { error ->
            error?.let {
                android.util.Log.e("KnowledgeActivity", "Error: $it")
                Toast.makeText(this, it, Toast.LENGTH_LONG).show()
                viewModel.clearError()
            }
        }

        viewModel.successMessage.observe(this) { message ->
            message?.let {
                android.util.Log.d("KnowledgeActivity", "Success: $it")
                Toast.makeText(this, it, Toast.LENGTH_SHORT).show()
                viewModel.clearSuccessMessage()
            }
        }

        viewModel.isLoading.observe(this) { isLoading ->
            android.util.Log.d("KnowledgeActivity", "Loading state: $isLoading")
            // Show/hide loading indicator if needed
        }

        // Observe stats for potential future use
        viewModel.knowledgeStats.observe(this) { stats ->
            // Could display stats in UI
        }

        // Observe categories
        viewModel.categories.observe(this) { categories ->
            android.util.Log.d("KnowledgeActivity", "Categories loaded: ${categories.size}")
            categoryAdapter.submitList(categories)
            // 選択中のカテゴリを更新
            updateSelectedCategory()
        }
        
        // カテゴリフィルターの変更を監視
        viewModel.filteredItems.observe(this) {
            updateSelectedCategory()
        }
    }

    private fun setupFilters() {
        // Filter chips
        binding.chipAll.setOnClickListener {
            viewModel.setFilter(KnowledgeViewModel.FilterType.ALL)
        }
        binding.chipNotes.setOnClickListener {
            viewModel.setFilter(KnowledgeViewModel.FilterType.NOTES)
        }
        binding.chipCode.setOnClickListener {
            viewModel.setFilter(KnowledgeViewModel.FilterType.CODE)
        }
        binding.chipExercises.setOnClickListener {
            viewModel.setFilter(KnowledgeViewModel.FilterType.EXERCISES)
        }
        binding.chipLinks.setOnClickListener {
            viewModel.setFilter(KnowledgeViewModel.FilterType.LINKS)
        }
        binding.chipAttachments.setOnClickListener {
            viewModel.setFilter(KnowledgeViewModel.FilterType.ATTACHMENTS)
        }
        binding.chipFavorites.setOnClickListener {
            viewModel.setFilter(KnowledgeViewModel.FilterType.FAVORITES)
        }
        binding.chipArchived.setOnClickListener {
            viewModel.setFilter(KnowledgeViewModel.FilterType.ARCHIVED)
        }
        binding.chipDueReview.setOnClickListener {
            viewModel.setFilter(KnowledgeViewModel.FilterType.DUE_REVIEW)
        }
    }

    private fun showItemMenu(item: KnowledgeItem) {
        val popup = PopupMenu(this, binding.rvKnowledge)
        popup.menuInflater.inflate(R.menu.menu_knowledge_item_actions, popup.menu)

        // Hide archive option if already archived
        if (item.is_archived) {
            popup.menu.findItem(R.id.action_archive)?.title = "復元"
        }

        popup.setOnMenuItemClickListener { menuItem ->
            when (menuItem.itemId) {
                R.id.action_edit -> {
                    // TODO: Open edit dialog
                    Toast.makeText(this, "Edit - Coming soon", Toast.LENGTH_SHORT).show()
                    true
                }
                R.id.action_clone -> {
                    viewModel.cloneKnowledgeItem(item.id)
                    true
                }
                R.id.action_archive -> {
                    viewModel.toggleArchive(item.id)
                    true
                }
                R.id.action_delete -> {
                    viewModel.deleteItem(item.id) {
                        Toast.makeText(this, "削除しました", Toast.LENGTH_SHORT).show()
                    }
                    true
                }
                else -> false
            }
        }
        popup.show()
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

    /**
     * 選択中のカテゴリを更新
     */
    private fun updateSelectedCategory() {
        val currentCategoryId = viewModel.getCurrentCategoryId()
        categoryAdapter.setSelectedCategory(currentCategoryId)
        // 「すべて」チップの選択状態を更新
        binding.chipAllCategories.isChecked = currentCategoryId == null
    }

    /**
     * カテゴリ選択ボトムシートを表示
     */
    private fun showCategorySelectorBottomSheet(currentCategoryId: Int? = null) {
        val bottomSheet = CategorySelectorBottomSheet.newInstance(
            currentCategoryId = currentCategoryId ?: viewModel.getCurrentCategoryId(),
            onCategorySelected = { category ->
                if (category != null) {
                    viewModel.filterByCategory(category.id)
                    Toast.makeText(this, "カテゴリ: ${category.name}", Toast.LENGTH_SHORT).show()
                } else {
                    viewModel.clearCategoryFilter()
                }
                // 選択状態を更新
                updateSelectedCategory()
            }
        )
        bottomSheet.show(supportFragmentManager, CategorySelectorBottomSheet.TAG)
    }


    override fun onResume() {
        super.onResume()
        // Reload knowledge items when returning
        viewModel.refreshKnowledgeItems()
    }
}
