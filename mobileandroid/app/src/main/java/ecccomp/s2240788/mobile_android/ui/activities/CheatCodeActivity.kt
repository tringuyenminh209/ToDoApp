package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.text.Editable
import android.text.TextWatcher
import android.view.View
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.GridLayoutManager
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityCheatCodeBinding
import ecccomp.s2240788.mobile_android.ui.adapters.CheatCodeLanguageAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.CheatCodeViewModel

class CheatCodeActivity : BaseActivity() {

    private lateinit var binding: ActivityCheatCodeBinding
    private lateinit var viewModel: CheatCodeViewModel
    private lateinit var adapter: CheatCodeLanguageAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityCheatCodeBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupWindowInsets()

        setupViewModel()
        setupRecyclerView()
        setupUI()
        observeViewModel()
    }

    private fun setupViewModel() {
        viewModel = ViewModelProvider(this)[CheatCodeViewModel::class.java]
    }

    private fun setupRecyclerView() {
        adapter = CheatCodeLanguageAdapter { language ->
            val intent = Intent(this, CheatCodeDetailActivity::class.java)
            intent.putExtra("LANGUAGE_ID", language.id)
            intent.putExtra("LANGUAGE_NAME", language.displayName)
            startActivity(intent)
        }

        binding.rvLanguages.apply {
            layoutManager = GridLayoutManager(this@CheatCodeActivity, 2)
            adapter = this@CheatCodeActivity.adapter
        }
    }

    private fun setupUI() {
        // Back button
        binding.btnBack.setOnClickListener {
            finish()
        }

        // Search button
        binding.btnSearch.setOnClickListener {
            toggleSearchBar()
        }

        // Search input
        binding.etSearch.addTextChangedListener(object : TextWatcher {
            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {}

            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {
                viewModel.searchLanguages(s?.toString() ?: "")
            }

            override fun afterTextChanged(s: Editable?) {}
        })

        // Category chips
        binding.chipGroupCategory.setOnCheckedStateChangeListener { _, checkedIds ->
            if (checkedIds.isNotEmpty()) {
                val category = when (checkedIds[0]) {
                    R.id.chip_all -> "all"
                    R.id.chip_programming -> "programming"
                    R.id.chip_markup -> "markup"
                    R.id.chip_database -> "database"
                    else -> "all"
                }
                viewModel.filterByCategory(category)
                updateCategoryTitle(category)
            }
        }
        
        // Set initial category title
        updateCategoryTitle("all")
    }

    private fun updateCategoryTitle(category: String) {
        val title = when (category) {
            "programming" -> getString(R.string.programming)
            "markup" -> getString(R.string.markup)
            "database" -> getString(R.string.database)
            else -> getString(R.string.all)
        }
        binding.tvCategoryTitle.text = title
    }

    private fun toggleSearchBar() {
        if (binding.searchLayout.visibility == View.VISIBLE) {
            binding.searchLayout.visibility = View.GONE
            binding.etSearch.setText("")
        } else {
            binding.searchLayout.visibility = View.VISIBLE
            binding.etSearch.requestFocus()
        }
    }

    private fun observeViewModel() {
        viewModel.filteredLanguages.observe(this) { languages ->
            adapter.submitList(languages)

            // Update language count
            binding.tvLanguageCount.text = "${languages.size}個の言語"

            // Show/hide empty state
            if (languages.isEmpty()) {
                binding.emptyState.visibility = View.VISIBLE
                binding.rvLanguages.visibility = View.GONE
            } else {
                binding.emptyState.visibility = View.GONE
                binding.rvLanguages.visibility = View.VISIBLE
            }
        }

        viewModel.isLoading.observe(this) { isLoading ->
            binding.progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
        }
    }
}
