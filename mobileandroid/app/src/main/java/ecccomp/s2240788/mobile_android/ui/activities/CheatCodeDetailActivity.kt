package ecccomp.s2240788.mobile_android.ui.activities

import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.CheatCodeSection
import ecccomp.s2240788.mobile_android.databinding.ActivityCheatCodeDetailBinding
import ecccomp.s2240788.mobile_android.ui.adapters.CheatCodeSectionAdapter
import ecccomp.s2240788.mobile_android.ui.adapters.SectionTitleAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.CheatCodeDetailViewModel

class CheatCodeDetailActivity : BaseActivity() {

    private lateinit var binding: ActivityCheatCodeDetailBinding
    private lateinit var viewModel: CheatCodeDetailViewModel
    private lateinit var adapter: CheatCodeSectionAdapter
    private lateinit var sectionTitleAdapter: SectionTitleAdapter

    private var languageId: Int = -1
    private var languageName: String = ""
    private var sectionsList: List<CheatCodeSection> = emptyList()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityCheatCodeDetailBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupWindowInsets()

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
        // Setup sections RecyclerView
        adapter = CheatCodeSectionAdapter(languageName) { example ->
            Toast.makeText(this, example.title, Toast.LENGTH_SHORT).show()
        }

        binding.rvSections.apply {
            layoutManager = LinearLayoutManager(this@CheatCodeDetailActivity)
            adapter = this@CheatCodeDetailActivity.adapter
            isNestedScrollingEnabled = false
        }

        // Setup section titles RecyclerView (horizontal)
        sectionTitleAdapter = SectionTitleAdapter { section, position ->
            scrollToSection(section.id, position)
        }

        binding.rvSectionTitles.apply {
            layoutManager = LinearLayoutManager(this@CheatCodeDetailActivity, LinearLayoutManager.HORIZONTAL, false)
            adapter = sectionTitleAdapter
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
            sectionsList = sections
            adapter.submitList(sections)
            sectionTitleAdapter.submitList(sections)

            // Update sections count
            val totalExamples = sections.sumOf { it.examples?.size ?: 0 }
            binding.tvSectionsCount.text = "${sections.size}個のセクション • ${totalExamples}個の例"
        }

        viewModel.isLoading.observe(this) { isLoading ->
            binding.progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
        }
    }

    /**
     * Scroll to section by section ID
     * セクションIDでセクションまでスクロール
     */
    private fun scrollToSection(sectionId: Int, position: Int) {
        // Update selected position in title adapter
        sectionTitleAdapter.setSelectedPosition(position)
        
        // Scroll title list to show selected item
        binding.rvSectionTitles.post {
            val layoutManager = binding.rvSectionTitles.layoutManager as? LinearLayoutManager
            layoutManager?.scrollToPositionWithOffset(position, 0)
        }

        // Find section view in RecyclerView and scroll to it
        binding.rvSections.post {
            val layoutManager = binding.rvSections.layoutManager as? LinearLayoutManager
            val sectionPosition = sectionsList.indexOfFirst { it.id == sectionId }
            
            if (sectionPosition != -1) {
                layoutManager?.scrollToPositionWithOffset(sectionPosition, 0)
                
                // Also scroll NestedScrollView to ensure section is visible
                binding.nestedScrollView.post {
                    val viewHolder = binding.rvSections.findViewHolderForAdapterPosition(sectionPosition)
                    viewHolder?.itemView?.let { sectionView ->
                        val scrollView = binding.nestedScrollView
                        val scrollY = sectionView.top - scrollView.paddingTop
                        scrollView.smoothScrollTo(0, scrollY)
                    }
                }
            }
        }
    }
}
