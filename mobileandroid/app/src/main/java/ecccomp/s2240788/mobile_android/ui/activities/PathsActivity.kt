package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityPathsBinding
import ecccomp.s2240788.mobile_android.ui.adapters.PathsAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.PathsViewModel

/**
 * PathsActivity
 * Learning Paths 画面 - 学習ロードマップの管理
 * - Paths のリスト表示
 * - Progress 統計
 * - フィルター機能 (All/Active/Completed)
 */
class PathsActivity : BaseActivity() {

    private lateinit var binding: ActivityPathsBinding
    private lateinit var viewModel: PathsViewModel
    private lateinit var adapter: PathsAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityPathsBinding.inflate(layoutInflater)
        setContentView(binding.root)

        viewModel = ViewModelProvider(this)[PathsViewModel::class.java]

        setupRecyclerView()
        setupClickListeners()
        setupFilters()
        observeViewModel()
        setupBottomNavigation()
    }

    /**
     * RecyclerView のセットアップ
     */
    private fun setupRecyclerView() {
        adapter = PathsAdapter(
            onPathClick = { path ->
                // TODO: Open path detail
                Toast.makeText(this, "Path: ${path.title}", Toast.LENGTH_SHORT).show()
            },
            onCompleteClick = { path ->
                viewModel.completePath(path.id)
            },
            onDeleteClick = { path ->
                viewModel.deletePath(path.id)
            }
        )

        binding.rvPaths.layoutManager = LinearLayoutManager(this)
        binding.rvPaths.adapter = adapter
    }

    /**
     * クリックリスナーのセットアップ
     */
    private fun setupClickListeners() {
        binding.btnBack.setOnClickListener {
            finish()
        }

        binding.btnAddPath.setOnClickListener {
            // TODO: Open add path dialog/activity
            Toast.makeText(this, "Add Path (開発中)", Toast.LENGTH_SHORT).show()
        }

        binding.btnAddPathEmpty.setOnClickListener {
            // TODO: Open add path dialog/activity
            Toast.makeText(this, "Add Path (開発中)", Toast.LENGTH_SHORT).show()
        }
    }

    /**
     * フィルターチップのセットアップ
     */
    private fun setupFilters() {
        binding.chipAll.setOnCheckedChangeListener { _, isChecked ->
            if (isChecked) {
                viewModel.setFilter(PathsViewModel.FilterType.ALL)
            }
        }

        binding.chipActive.setOnCheckedChangeListener { _, isChecked ->
            if (isChecked) {
                viewModel.setFilter(PathsViewModel.FilterType.ACTIVE)
            }
        }

        binding.chipCompleted.setOnCheckedChangeListener { _, isChecked ->
            if (isChecked) {
                viewModel.setFilter(PathsViewModel.FilterType.COMPLETED)
            }
        }
    }

    /**
     * ViewModelの監視
     */
    private fun observeViewModel() {
        // Filtered paths
        viewModel.filteredPaths.observe(this) { paths ->
            if (paths.isEmpty()) {
                showEmptyState()
            } else {
                showPaths(paths)
            }
        }

        // Statistics
        viewModel.activePathsCount.observe(this) { count ->
            binding.tvActivePathsCount.text = count.toString()
        }

        viewModel.completedPathsCount.observe(this) { count ->
            binding.tvCompletedPathsCount.text = count.toString()
        }

        viewModel.overallProgress.observe(this) { progress ->
            binding.tvOverallProgress.text = "$progress%"
        }

        // Loading state
        viewModel.isLoading.observe(this) { isLoading ->
            // TODO: Show loading indicator
        }

        // Error messages
        viewModel.error.observe(this) { error ->
            error?.let {
                Toast.makeText(this, it, Toast.LENGTH_SHORT).show()
            }
        }
    }

    /**
     * Empty State を表示
     */
    private fun showEmptyState() {
        binding.emptyState.visibility = View.VISIBLE
        binding.rvPaths.visibility = View.GONE
    }

    /**
     * Paths を表示
     */
    private fun showPaths(paths: List<ecccomp.s2240788.mobile_android.data.models.LearningPath>) {
        binding.emptyState.visibility = View.GONE
        binding.rvPaths.visibility = View.VISIBLE
        adapter.submitList(paths)
    }

    /**
     * ボトムナビゲーションのセットアップ
     */
    private fun setupBottomNavigation() {
        binding.bottomNavigation.selectedItemId = R.id.nav_paths

        binding.bottomNavigation.setOnItemSelectedListener { item ->
            when (item.itemId) {
                R.id.nav_home -> {
                    startActivity(Intent(this, MainActivity::class.java))
                    finish()
                    true
                }
                R.id.nav_paths -> {
                    // Current activity
                    true
                }
                R.id.nav_calendar -> {
                    startActivity(Intent(this, CalendarActivity::class.java))
                    finish()
                    true
                }
                R.id.nav_knowledge -> {
                    startActivity(Intent(this, KnowledgeActivity::class.java))
                    finish()
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
        viewModel.refreshPaths()
    }
}

