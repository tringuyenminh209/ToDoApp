package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.lifecycle.ViewModelProvider
import com.google.android.material.chip.Chip
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityQuickCaptureBinding
import ecccomp.s2240788.mobile_android.ui.viewmodels.KnowledgeViewModel

/**
 * QuickCaptureActivity
 * Fast knowledge capture with auto-detection
 * - Paste content quickly
 * - Auto-detect code language
 * - Auto-suggest categories
 * - Auto-generate tags
 * - Minimal UI for speed
 */
class QuickCaptureActivity : BaseActivity() {

    private lateinit var binding: ActivityQuickCaptureBinding
    private lateinit var viewModel: KnowledgeViewModel
    private var selectedCategoryId: Int? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityQuickCaptureBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupWindowInsets()

        viewModel = ViewModelProvider(this)[KnowledgeViewModel::class.java]

        setupUI()
        setupClickListeners()
        setupObservers()

        // Check if content was shared from another app
        handleSharedContent()
    }

    private fun setupUI() {
        // Default to code type (most common for quick capture)
        binding.chipTypeCode.isChecked = true
    }

    private fun setupClickListeners() {
        binding.btnBack.setOnClickListener {
            finish()
        }

        binding.btnSave.setOnClickListener {
            saveQuickCapture()
        }

        binding.btnAnalyze.setOnClickListener {
            analyzeContent()
        }
    }

    private fun setupObservers() {
        viewModel.isLoading.observe(this) { isLoading ->
            binding.loadingOverlay.visibility = if (isLoading) View.VISIBLE else View.GONE
            binding.btnSave.isEnabled = !isLoading
            binding.btnAnalyze.isEnabled = !isLoading
        }

        viewModel.error.observe(this) { error ->
            error?.let {
                Toast.makeText(this, it, Toast.LENGTH_LONG).show()
                viewModel.clearError()
            }
        }

        viewModel.quickCaptureResponse.observe(this) { response ->
            response?.let {
                showAutoDetectedInfo(response)
                viewModel.clearQuickCaptureResponse()

                // Show success and navigate to detail
                Toast.makeText(this, getString(R.string.quick_capture_success), Toast.LENGTH_SHORT).show()

                // Navigate to detail activity
                val intent = Intent(this, KnowledgeDetailActivity::class.java)
                intent.putExtra("KNOWLEDGE_ITEM_ID", response.item.id)
                startActivity(intent)
                finish()
            }
        }
    }

    /**
     * Handle content shared from other apps (Android Share feature)
     */
    private fun handleSharedContent() {
        if (intent?.action == Intent.ACTION_SEND) {
            if (intent.type == "text/plain") {
                val sharedText = intent.getStringExtra(Intent.EXTRA_TEXT)
                sharedText?.let {
                    binding.etContent.setText(it)
                    // Auto-analyze if content was shared
                    analyzeContent()
                }
            }
        }
    }

    /**
     * Analyze content and get suggestions from backend
     */
    private fun analyzeContent() {
        val content = binding.etContent.text.toString().trim()

        if (content.isEmpty()) {
            Toast.makeText(this, getString(R.string.error_content_required), Toast.LENGTH_SHORT).show()
            return
        }

        val itemType = when (binding.chipGroupType.checkedChipId) {
            R.id.chip_type_note -> "note"
            R.id.chip_type_code -> "code_snippet"
            R.id.chip_type_link -> "resource_link"
            else -> "note"
        }

        // Call quick capture API to get suggestions (without saving yet)
        viewModel.quickCapture(content, itemType, selectedCategoryId)
    }

    /**
     * Display auto-detected information
     */
    private fun showAutoDetectedInfo(response: ecccomp.s2240788.mobile_android.data.models.QuickCaptureResponse) {
        binding.cardAutoInfo.visibility = View.VISIBLE

        // Show detected language
        if (!response.auto_detected_language.isNullOrEmpty()) {
            binding.llDetectedLanguage.visibility = View.VISIBLE
            binding.tvDetectedLanguage.text = response.auto_detected_language.uppercase()
        } else {
            binding.llDetectedLanguage.visibility = View.GONE
        }

        // Show suggested categories
        if (response.suggested_categories.isNotEmpty()) {
            binding.llSuggestedCategories.visibility = View.VISIBLE
            binding.chipGroupSuggestions.removeAllViews()

            response.suggested_categories.forEach { suggestion ->
                val chip = Chip(this)
                chip.text = "${suggestion.category.name} (${(suggestion.confidence * 100).toInt()}%)"
                chip.isCheckable = true
                chip.setOnClickListener {
                    selectedCategoryId = suggestion.category.id
                }
                binding.chipGroupSuggestions.addView(chip)
            }

            // Auto-select the highest confidence category
            if (response.suggested_categories.isNotEmpty()) {
                selectedCategoryId = response.suggested_categories[0].category.id
                (binding.chipGroupSuggestions.getChildAt(0) as? Chip)?.isChecked = true
            }
        } else {
            binding.llSuggestedCategories.visibility = View.GONE
        }

        // Show auto-generated tags
        if (response.auto_generated_tags.isNotEmpty()) {
            binding.llAutoTags.visibility = View.VISIBLE
            binding.tvAutoTags.text = response.auto_generated_tags.joinToString(" ")
        } else {
            binding.llAutoTags.visibility = View.GONE
        }
    }

    /**
     * Save the quick capture
     */
    private fun saveQuickCapture() {
        val content = binding.etContent.text.toString().trim()

        if (content.isEmpty()) {
            Toast.makeText(this, getString(R.string.error_content_required), Toast.LENGTH_SHORT).show()
            return
        }

        val itemType = when (binding.chipGroupType.checkedChipId) {
            R.id.chip_type_note -> "note"
            R.id.chip_type_code -> "code_snippet"
            R.id.chip_type_link -> "resource_link"
            else -> "note"
        }

        // If not analyzed yet, analyze first then save
        if (selectedCategoryId == null) {
            // Quick capture will auto-save
            viewModel.quickCapture(content, itemType, null)
        } else {
            // Already analyzed, just save with selected category
            viewModel.quickCapture(content, itemType, selectedCategoryId)
        }
    }

    override fun onDestroy() {
        super.onDestroy()
        viewModel.clearQuickCaptureResponse()
    }
}
