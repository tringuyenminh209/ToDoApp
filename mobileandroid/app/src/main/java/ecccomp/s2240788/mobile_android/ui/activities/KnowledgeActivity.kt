package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.text.Editable
import android.text.TextWatcher
import android.view.LayoutInflater
import android.view.View
import android.widget.ImageView
import android.widget.PopupMenu
import android.widget.TextView
import android.widget.Toast
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityKnowledgeBinding
import ecccomp.s2240788.mobile_android.data.models.KnowledgeCategory
import ecccomp.s2240788.mobile_android.data.models.KnowledgeItem
import ecccomp.s2240788.mobile_android.ui.adapters.FolderAdapter
import ecccomp.s2240788.mobile_android.ui.adapters.KnowledgeAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.KnowledgeViewModel

/**
 * KnowledgeActivity (Redesigned)
 * Folder-Based Hierarchical Navigation
 * Features:
 * - Browse categories as folders
 * - Breadcrumb navigation
 * - Mixed folder + file view
 * - Navigate into sub-categories
 * - Context-aware UI
 */
class KnowledgeActivity : BaseActivity() {

    private lateinit var binding: ActivityKnowledgeBinding
    private lateinit var viewModel: KnowledgeViewModel
    private lateinit var folderAdapter: FolderAdapter
    private lateinit var knowledgeAdapter: KnowledgeAdapter

    // Navigation state
    private val breadcrumbPath = mutableListOf<KnowledgeCategory?>()  // null = root
    private var currentCategoryId: Int? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityKnowledgeBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupWindowInsets()

        viewModel = ViewModelProvider(this)[KnowledgeViewModel::class.java]

        setupAdapters()
        setupClickListeners()
        setupObservers()
        setupBottomNavigation()

        // Initialize at root level
        navigateToRoot()

        // Load initial data
        viewModel.loadKnowledgeItems()
        viewModel.loadCategories()
        viewModel.loadDueReviewItems()
    }

    private fun setupAdapters() {
        // Folder adapter for sub-categories
        folderAdapter = FolderAdapter(
            onFolderClick = { category ->
                navigateToCategory(category)
            },
            onFolderMenuClick = { category ->
                showFolderMenu(category)
            }
        )
        binding.rvFolders.layoutManager = LinearLayoutManager(this)
        binding.rvFolders.adapter = folderAdapter

        // Knowledge items adapter
        knowledgeAdapter = KnowledgeAdapter(
            onItemClick = { item ->
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
        binding.rvKnowledge.layoutManager = LinearLayoutManager(this)
        binding.rvKnowledge.adapter = knowledgeAdapter
    }

    private fun setupClickListeners() {
        // Header buttons
        binding.btnQuickCapture.setOnClickListener {
            val intent = Intent(this, QuickCaptureActivity::class.java)
            if (currentCategoryId != null) {
                intent.putExtra("CATEGORY_ID", currentCategoryId)
            }
            startActivity(intent)
        }

        binding.btnAddKnowledge.setOnClickListener {
            val intent = Intent(this, KnowledgeEditorActivity::class.java)
            if (currentCategoryId != null) {
                intent.putExtra("CATEGORY_ID", currentCategoryId)
            }
            startActivity(intent)
        }

        binding.fabAddKnowledge.setOnClickListener {
            val intent = Intent(this, KnowledgeEditorActivity::class.java)
            if (currentCategoryId != null) {
                intent.putExtra("CATEGORY_ID", currentCategoryId)
            }
            startActivity(intent)
        }

        binding.btnAddKnowledgeEmpty.setOnClickListener {
            val intent = Intent(this, KnowledgeEditorActivity::class.java)
            if (currentCategoryId != null) {
                intent.putExtra("CATEGORY_ID", currentCategoryId)
            }
            startActivity(intent)
        }

        // Quick action cards
        binding.cheatCodeCard.setOnClickListener {
            val intent = Intent(this, CheatCodeActivity::class.java)
            startActivity(intent)
        }

        binding.reviewCard.setOnClickListener {
            val intent = Intent(this, ReviewActivity::class.java)
            startActivity(intent)
        }

        // Home icon in breadcrumb
        binding.ivHome.setOnClickListener {
            navigateToRoot()
        }

        // Search
        binding.etSearch.addTextChangedListener(object : TextWatcher {
            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {}
            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {}
            override fun afterTextChanged(s: Editable?) {
                val query = s?.toString() ?: ""
                if (query.isNotEmpty()) {
                    viewModel.setQuery(query)
                } else {
                    // Refresh current folder view
                    loadCurrentFolder()
                }
            }
        })
    }

    private fun setupObservers() {
        // Observe all categories
        viewModel.categories.observe(this) { categories ->
            // Update folder view when categories change
            loadCurrentFolder()
        }

        // Observe all knowledge items
        viewModel.knowledgeItems.observe(this) { items ->
            // Update files view when items change
            loadCurrentFolder()
        }

        // Observe filtered items (for search)
        viewModel.filteredItems.observe(this) { items ->
            val query = binding.etSearch.text.toString()
            if (query.isNotEmpty()) {
                // Show search results
                displaySearchResults(items)
            }
        }

        // Observe due review items
        viewModel.dueReviewItems.observe(this) { items ->
            val count = items.size
            binding.tvDueCount.text = if (count > 0) {
                getString(R.string.items_due_today, count)
            } else {
                getString(R.string.no_reviews_today)
            }
        }

        // Observe errors
        viewModel.error.observe(this) { error ->
            error?.let {
                Toast.makeText(this, it, Toast.LENGTH_LONG).show()
                viewModel.clearError()
            }
        }

        // Observe success messages
        viewModel.successMessage.observe(this) { message ->
            message?.let {
                Toast.makeText(this, it, Toast.LENGTH_SHORT).show()
                viewModel.clearSuccessMessage()
            }
        }
    }

    /**
     * Navigate to root level (show all top-level categories)
     */
    private fun navigateToRoot() {
        currentCategoryId = null
        breadcrumbPath.clear()
        breadcrumbPath.add(null)  // Root marker
        updateBreadcrumb()
        loadCurrentFolder()
    }

    /**
     * Navigate into a category folder
     */
    private fun navigateToCategory(category: KnowledgeCategory) {
        currentCategoryId = category.id
        breadcrumbPath.add(category)
        updateBreadcrumb()
        loadCurrentFolder()
    }

    /**
     * Navigate back to a specific breadcrumb level
     */
    private fun navigateToBreadcrumb(index: Int) {
        // Remove all items after the clicked index
        while (breadcrumbPath.size > index + 1) {
            breadcrumbPath.removeAt(breadcrumbPath.size - 1)
        }

        // Set current category
        val targetCategory = breadcrumbPath.lastOrNull()
        currentCategoryId = targetCategory?.id

        updateBreadcrumb()
        loadCurrentFolder()
    }

    /**
     * Update breadcrumb navigation UI
     */
    private fun updateBreadcrumb() {
        // Clear existing breadcrumb items (except home icon)
        binding.breadcrumbContainer.removeViews(
            1,
            binding.breadcrumbContainer.childCount - 1
        )

        // Add breadcrumb items for each level
        breadcrumbPath.forEachIndexed { index, category ->
            if (index == 0 && category == null) {
                // Root level - home icon only
                return@forEachIndexed
            }

            if (category != null) {
                // Add separator arrow
                val separatorView = LayoutInflater.from(this)
                    .inflate(R.layout.item_breadcrumb, binding.breadcrumbContainer, false)

                val tvBreadcrumb = separatorView.findViewById<TextView>(R.id.tv_breadcrumb)
                val ivSeparator = separatorView.findViewById<ImageView>(R.id.iv_separator)

                tvBreadcrumb.text = category.name
                tvBreadcrumb.setOnClickListener {
                    navigateToBreadcrumb(index)
                }

                // Hide separator for last item
                if (index == breadcrumbPath.size - 1) {
                    ivSeparator.visibility = View.GONE
                }

                binding.breadcrumbContainer.addView(separatorView)
            }
        }
    }

    /**
     * Load and display current folder contents
     */
    private fun loadCurrentFolder() {
        val allCategories = viewModel.categories.value ?: emptyList()
        val allItems = viewModel.knowledgeItems.value ?: emptyList()

        // Get sub-folders (child categories)
        val subFolders = allCategories.filter { it.parent_id == currentCategoryId }

        // Get files (items in current category)
        val files = allItems.filter { item ->
            if (currentCategoryId == null) {
                // At root: show items without category
                item.category == null || item.category_id == null
            } else {
                // In folder: show items with matching category
                item.category_id == currentCategoryId
            }
        }

        // Update UI visibility
        val isRoot = currentCategoryId == null
        val hasSubFolders = subFolders.isNotEmpty()
        val hasFiles = files.isNotEmpty()
        val isEmpty = !hasSubFolders && !hasFiles

        // Show/hide quick actions (only at root)
        binding.quickActionsContainer.visibility = if (isRoot) View.VISIBLE else View.GONE

        // Show/hide folders section
        binding.foldersSection.visibility = if (hasSubFolders) View.VISIBLE else View.GONE
        if (hasSubFolders) {
            folderAdapter.submitList(subFolders)
        }

        // Show/hide files section
        binding.filesSection.visibility = if (hasFiles) View.VISIBLE else View.GONE
        if (hasFiles) {
            knowledgeAdapter.submitList(files)
        }

        // Show/hide empty state
        binding.emptyState.visibility = if (isEmpty) View.VISIBLE else View.GONE
        if (isEmpty) {
            val currentCategory = breadcrumbPath.lastOrNull()
            if (currentCategory != null) {
                binding.tvEmptyTitle.text = "Empty folder"
                binding.tvEmptyDescription.text = "This folder doesn't have any items yet"
            } else {
                binding.tvEmptyTitle.text = getString(R.string.knowledge_empty)
                binding.tvEmptyDescription.text = getString(R.string.knowledge_empty_description)
            }
        }

        // Update folder info
        if (!isRoot) {
            val totalCount = subFolders.size + files.size
            binding.tvFolderInfo.text = "$totalCount items in this folder"
            binding.tvFolderInfo.visibility = View.VISIBLE
        } else {
            binding.tvFolderInfo.visibility = View.GONE
        }
    }

    /**
     * Display search results (global search)
     */
    private fun displaySearchResults(items: List<KnowledgeItem>) {
        // Hide folder structure, show flat list
        binding.quickActionsContainer.visibility = View.GONE
        binding.foldersSection.visibility = View.GONE
        binding.filesSection.visibility = View.VISIBLE
        binding.tvFilesHeader.text = "Search Results (${items.size})"

        if (items.isNotEmpty()) {
            binding.emptyState.visibility = View.GONE
            knowledgeAdapter.submitList(items)
        } else {
            binding.emptyState.visibility = View.VISIBLE
            binding.tvEmptyTitle.text = "No results found"
            binding.tvEmptyDescription.text = "Try different keywords"
        }
    }

    /**
     * Show folder context menu
     */
    private fun showFolderMenu(category: KnowledgeCategory) {
        val popup = PopupMenu(this, binding.rvFolders)
        popup.menuInflater.inflate(R.menu.menu_folder_actions, popup.menu)

        popup.setOnMenuItemClickListener { menuItem ->
            when (menuItem.itemId) {
                R.id.action_rename -> {
                    Toast.makeText(this, "Rename - Coming soon", Toast.LENGTH_SHORT).show()
                    true
                }
                R.id.action_delete -> {
                    Toast.makeText(this, "Delete folder - Coming soon", Toast.LENGTH_SHORT).show()
                    true
                }
                else -> false
            }
        }
        popup.show()
    }

    /**
     * Show item context menu
     */
    private fun showItemMenu(item: KnowledgeItem) {
        val popup = PopupMenu(this, binding.rvKnowledge)
        popup.menuInflater.inflate(R.menu.menu_knowledge_item_actions, popup.menu)

        if (item.is_archived) {
            popup.menu.findItem(R.id.action_archive)?.title = "復元"
        }

        popup.setOnMenuItemClickListener { menuItem ->
            when (menuItem.itemId) {
                R.id.action_edit -> {
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
     * Setup bottom navigation
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
        // Reload data when returning
        viewModel.refreshKnowledgeItems()
    }

    /**
     * Handle back press - navigate up folder hierarchy
     */
    @Deprecated("Deprecated in Java")
    override fun onBackPressed() {
        if (breadcrumbPath.size > 1) {
            // Navigate up one level
            navigateToBreadcrumb(breadcrumbPath.size - 2)
        } else {
            // At root, exit activity
            super.onBackPressed()
        }
    }
}
