package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityMilestoneKnowledgeBinding
import ecccomp.s2240788.mobile_android.ui.viewmodels.KnowledgeViewModel

/**
 * MilestoneKnowledgeActivity
 * マイルストーン の学習内容を表示
 * - Filter knowledge items by milestone tasks
 * - Different content types (notes, code, exercises, links)
 * - Search and filter functionality
 */
class MilestoneKnowledgeActivity : BaseActivity() {

    private lateinit var binding: ActivityMilestoneKnowledgeBinding
    private lateinit var viewModel: KnowledgeViewModel
    private lateinit var adapter: ecccomp.s2240788.mobile_android.ui.adapters.KnowledgeAdapter

    private var milestoneId: Int = -1
    private var milestoneTitle: String = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityMilestoneKnowledgeBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupWindowInsets()

        viewModel = ViewModelProvider(this)[KnowledgeViewModel::class.java]

        // Get milestone info from intent
        milestoneId = intent.getIntExtra("MILESTONE_ID", -1)
        milestoneTitle = intent.getStringExtra("MILESTONE_TITLE") ?: ""

        if (milestoneId == -1) {
            Toast.makeText(this, "エラー: マイルストーンIDが無効です", Toast.LENGTH_SHORT).show()
            finish()
            return
        }

        setupUI()
        setupClickListeners()
        setupObservers()

        // Set title
        binding.tvTitle.text = milestoneTitle

        // Load knowledge items for this milestone
        viewModel.loadKnowledgeItemsByMilestone(milestoneId)
    }

    private fun setupUI() {
        // Setup Knowledge adapter
        adapter = ecccomp.s2240788.mobile_android.ui.adapters.KnowledgeAdapter(
            onItemClick = { item ->
                // Open knowledge detail activity
                val intent = Intent(this, KnowledgeDetailActivity::class.java)
                intent.putExtra("KNOWLEDGE_ITEM_ID", item.id)
                startActivity(intent)
            },
            onFavoriteClick = { item ->
                viewModel.toggleFavorite(item.id) {
                    Toast.makeText(this, "お気に入り更新", Toast.LENGTH_SHORT).show()
                }
            },
            onMenuClick = { item ->
                // TODO: Show menu options
                Toast.makeText(this, "Menu: ${item.title}", Toast.LENGTH_SHORT).show()
            }
        )

        // RecyclerView setup
        binding.rvKnowledge.layoutManager = LinearLayoutManager(this)
        binding.rvKnowledge.adapter = adapter
    }

    private fun setupClickListeners() {
        binding.btnBack.setOnClickListener {
            finish()
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
        // Observe filtered items
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
            binding.progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
        }
    }

    override fun onResume() {
        super.onResume()
        // Reload knowledge items when returning from detail
        viewModel.loadKnowledgeItemsByMilestone(milestoneId)
    }
}
