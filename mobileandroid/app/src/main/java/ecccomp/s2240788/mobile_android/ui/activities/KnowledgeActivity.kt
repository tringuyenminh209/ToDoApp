package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityKnowledgeBinding
import ecccomp.s2240788.mobile_android.ui.adapters.PathsAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.PathsViewModel

/**
 * KnowledgeActivity
 * 知識管理画面 - Learning Paths経由で学習内容にアクセス
 * Flow: Learning Paths → Milestones → Knowledge Items → Detail
 */
class KnowledgeActivity : BaseActivity() {

    private lateinit var binding: ActivityKnowledgeBinding
    private lateinit var viewModel: PathsViewModel
    private lateinit var adapter: PathsAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityKnowledgeBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupWindowInsets()

        viewModel = ViewModelProvider(this)[PathsViewModel::class.java]

        setupUI()
        setupClickListeners()
        setupObservers()
        setupBottomNavigation()

        // Load learning paths
        viewModel.fetchPaths()
    }

    private fun setupUI() {
        // Hide unused UI elements
        // Note: These views might not exist in the current layout binding
        // binding.chipGroupFilters.visibility = View.GONE
        // binding.searchLayout.visibility = View.GONE
        // binding.pathSelectorCard.visibility = View.GONE

        // Update title
        binding.tvTitle.text = getString(R.string.knowledge_title)
        binding.tvSubtitle.text = getString(R.string.knowledge_select_path)

        // Setup Paths adapter
        adapter = PathsAdapter(
            onPathClick = { path ->
                // Navigate to learning path detail to see milestones
                val intent = Intent(this, LearningPathDetailActivity::class.java)
                intent.putExtra("LEARNING_PATH_ID", path.id)
                startActivity(intent)
            },
            onCompleteClick = { path ->
                viewModel.completePath(path.id)
            },
            onDeleteClick = { path ->
                viewModel.deletePath(path.id)
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

        binding.btnAddKnowledge.setOnClickListener {
            // Navigate to template browser to add new path
            val intent = Intent(this, TemplateBrowserActivity::class.java)
            startActivity(intent)
        }

        // CheatCode navigation
        binding.cheatCodeCard.setOnClickListener {
            val intent = Intent(this, CheatCodeActivity::class.java)
            startActivity(intent)
        }
    }

    private fun setupObservers() {
        // Observe filtered paths
        viewModel.filteredPaths.observe(this) { paths ->
            if (paths.isEmpty()) {
                binding.emptyState.visibility = View.VISIBLE
                binding.rvKnowledge.visibility = View.GONE
            } else {
                binding.emptyState.visibility = View.GONE
                binding.rvKnowledge.visibility = View.VISIBLE
                adapter.submitList(paths)
            }
        }

        viewModel.error.observe(this) { error ->
            error?.let {
                Toast.makeText(this, it, Toast.LENGTH_LONG).show()
            }
        }

        viewModel.isLoading.observe(this) { isLoading ->
            // Show/hide loading indicator if needed
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
        // Reload paths when returning
        viewModel.fetchPaths()
    }
}
