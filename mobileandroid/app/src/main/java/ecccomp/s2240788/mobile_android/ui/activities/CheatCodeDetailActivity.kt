package ecccomp.s2240788.mobile_android.ui.activities

import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityCheatCodeDetailBinding
import ecccomp.s2240788.mobile_android.ui.adapters.CheatCodeSectionAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.CheatCodeDetailViewModel

class CheatCodeDetailActivity : BaseActivity() {

    private lateinit var binding: ActivityCheatCodeDetailBinding
    private lateinit var viewModel: CheatCodeDetailViewModel
    private lateinit var adapter: CheatCodeSectionAdapter

    private var languageId: Int = -1
    private var languageName: String = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityCheatCodeDetailBinding.inflate(layoutInflater)
        setContentView(binding.root)

        getIntentData()
        setupViewModel()
        setupRecyclerView()
        setupUI()
        observeViewModel()
        loadData()
    }

    private fun getIntentData() {
        languageId = intent.getIntExtra("LANGUAGE_ID", -1)
        languageName = intent.getStringExtra("LANGUAGE_NAME") ?: ""
        
        // Validate intent data
        if (languageId == -1 || languageName.isEmpty()) {
            Toast.makeText(this, getString(R.string.error_invalid_language_data), Toast.LENGTH_SHORT).show()
            finish()
        }
    }

    private fun setupViewModel() {
        viewModel = ViewModelProvider(this)[CheatCodeDetailViewModel::class.java]
    }

    private fun setupRecyclerView() {
        adapter = CheatCodeSectionAdapter { example ->
            Toast.makeText(this, example.title, Toast.LENGTH_SHORT).show()
        }

        binding.rvSections.apply {
            layoutManager = LinearLayoutManager(this@CheatCodeDetailActivity)
            adapter = this@CheatCodeDetailActivity.adapter
            isNestedScrollingEnabled = false
        }
    }

    private fun setupUI() {
        // Set language name
        binding.tvLanguageName.text = languageName

        // Back button
        binding.btnBack.setOnClickListener {
            finish()
        }

        // Exercises button
        binding.btnExercises.setOnClickListener {
            Toast.makeText(this, "演習機能は開発中です", Toast.LENGTH_SHORT).show()
            // TODO: Navigate to exercises activity
            // val intent = Intent(this, CheatCodeExercisesActivity::class.java)
            // intent.putExtra("LANGUAGE_ID", languageId)
            // startActivity(intent)
        }
    }

    private fun loadData() {
        if (languageId != -1) {
            viewModel.loadSections(languageId)
        }
    }

    private fun observeViewModel() {
        viewModel.sections.observe(this) { sections ->
            adapter.submitList(sections)

            // Update sections count
            val totalExamples = sections.sumOf { it.examples?.size ?: 0 }
            binding.tvSectionsCount.text = "${sections.size}個のセクション • ${totalExamples}個の例"
        }

        viewModel.isLoading.observe(this) { isLoading ->
            binding.progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
        }
    }
}
