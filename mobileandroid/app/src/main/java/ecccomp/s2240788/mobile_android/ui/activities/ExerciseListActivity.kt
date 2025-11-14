package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.appcompat.widget.SearchView
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import com.google.android.material.chip.Chip
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityExerciseListBinding
import ecccomp.s2240788.mobile_android.ui.adapters.ExerciseAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.ExerciseListViewModel

class ExerciseListActivity : BaseActivity() {

    private lateinit var binding: ActivityExerciseListBinding
    private lateinit var viewModel: ExerciseListViewModel
    private lateinit var adapter: ExerciseAdapter

    private var languageId: Int = -1
    private var languageName: String = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityExerciseListBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupWindowInsets()

        getIntentData()
        setupViewModel()
        setupRecyclerView()
        setupUI()
        setupSearchView()
        setupFilters()
        observeViewModel()
        loadData()
    }

    private fun getIntentData() {
        languageId = intent.getIntExtra("LANGUAGE_ID", -1)
        languageName = intent.getStringExtra("LANGUAGE_NAME") ?: ""

        // Validate intent data
        if (languageId == -1 || languageName.isEmpty()) {
            Toast.makeText(this, "Invalid language data", Toast.LENGTH_SHORT).show()
            finish()
        }
    }

    private fun setupViewModel() {
        viewModel = ViewModelProvider(this)[ExerciseListViewModel::class.java]
    }

    private fun setupRecyclerView() {
        adapter = ExerciseAdapter { exercise ->
            // Navigate to ExerciseDetailActivity
            val intent = Intent(this, ExerciseDetailActivity::class.java).apply {
                putExtra("LANGUAGE_ID", languageId)
                putExtra("LANGUAGE_NAME", languageName)
                putExtra("EXERCISE_ID", exercise.id)
                putExtra("EXERCISE_TITLE", exercise.title)
            }
            startActivity(intent)
        }

        binding.rvExercises.apply {
            layoutManager = LinearLayoutManager(this@ExerciseListActivity)
            adapter = this@ExerciseListActivity.adapter
        }
    }

    private fun setupUI() {
        // Set toolbar title
        binding.toolbarTitle.text = "$languageName - 練習問題"

        // Back button
        binding.btnBack.setOnClickListener {
            finish()
        }

        // Refresh button
        binding.btnRefresh.setOnClickListener {
            viewModel.refresh(languageId)
        }

        // Swipe refresh
        binding.swipeRefresh.setOnRefreshListener {
            viewModel.refresh(languageId)
        }
    }

    private fun setupSearchView() {
        binding.searchView.setOnQueryTextListener(object : SearchView.OnQueryTextListener {
            override fun onQueryTextSubmit(query: String?): Boolean {
                query?.let { viewModel.searchExercises(it) }
                return true
            }

            override fun onQueryTextChange(newText: String?): Boolean {
                newText?.let { viewModel.searchExercises(it) }
                return true
            }
        })
    }

    private fun setupFilters() {
        // Difficulty filter chips
        binding.chipAll.setOnClickListener {
            clearChipSelection()
            binding.chipAll.isChecked = true
            viewModel.filterByDifficulty(null)
        }

        binding.chipEasy.setOnClickListener {
            clearChipSelection()
            binding.chipEasy.isChecked = true
            viewModel.filterByDifficulty("easy")
        }

        binding.chipMedium.setOnClickListener {
            clearChipSelection()
            binding.chipMedium.isChecked = true
            viewModel.filterByDifficulty("medium")
        }

        binding.chipHard.setOnClickListener {
            clearChipSelection()
            binding.chipHard.isChecked = true
            viewModel.filterByDifficulty("hard")
        }

        // Default: All selected
        binding.chipAll.isChecked = true
    }

    private fun clearChipSelection() {
        binding.chipAll.isChecked = false
        binding.chipEasy.isChecked = false
        binding.chipMedium.isChecked = false
        binding.chipHard.isChecked = false
    }

    private fun observeViewModel() {
        // Observe exercises
        viewModel.exercises.observe(this) { exercises ->
            adapter.submitList(exercises)
            binding.tvExerciseCount.text = "${exercises.size} 問題"

            // Show empty state if no exercises
            if (exercises.isEmpty() && viewModel.isLoading.value == false) {
                binding.layoutEmpty.visibility = View.VISIBLE
                binding.rvExercises.visibility = View.GONE
            } else {
                binding.layoutEmpty.visibility = View.GONE
                binding.rvExercises.visibility = View.VISIBLE
            }
        }

        // Observe language
        viewModel.language.observe(this) { language ->
            language?.let {
                binding.tvLanguageInfo.text = "${it.displayName} (${it.exercisesCount} exercises)"
            }
        }

        // Observe loading state
        viewModel.isLoading.observe(this) { isLoading ->
            binding.swipeRefresh.isRefreshing = isLoading
            binding.progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
        }

        // Observe errors
        viewModel.error.observe(this) { error ->
            error?.let {
                Toast.makeText(this, it, Toast.LENGTH_SHORT).show()
            }
        }
    }

    private fun loadData() {
        viewModel.loadExercises(languageId)
    }
}
