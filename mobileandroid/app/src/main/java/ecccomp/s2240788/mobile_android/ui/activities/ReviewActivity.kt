package ecccomp.s2240788.mobile_android.ui.activities

import android.annotation.SuppressLint
import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.lifecycle.ViewModelProvider
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.KnowledgeItem
import ecccomp.s2240788.mobile_android.databinding.ActivityReviewBinding
import ecccomp.s2240788.mobile_android.ui.viewmodels.KnowledgeViewModel
import ecccomp.s2240788.mobile_android.utils.CodeHighlightHelper

/**
 * ReviewActivity
 * Spaced Repetition Review System
 * Features:
 * - Flashcard mode
 * - Show/Hide answer
 * - Quality feedback (Hard/Good/Easy)
 * - Progress tracking
 * - Auto-schedule next review
 */
class ReviewActivity : BaseActivity() {

    private lateinit var binding: ActivityReviewBinding
    private lateinit var viewModel: KnowledgeViewModel

    private var reviewItems: List<KnowledgeItem> = emptyList()
    private var currentIndex: Int = 0
    private var currentItem: KnowledgeItem? = null
    private var isAnswerShown: Boolean = false

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityReviewBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupWindowInsets()

        viewModel = ViewModelProvider(this)[KnowledgeViewModel::class.java]

        setupWebView()
        setupClickListeners()
        setupObservers()

        // Load items due for review
        viewModel.loadDueReviewItems()
    }

    @SuppressLint("SetJavaScriptEnabled")
    private fun setupWebView() {
        binding.webviewContent.settings.apply {
            javaScriptEnabled = true
            domStorageEnabled = true
            loadWithOverviewMode = false
            useWideViewPort = false
            builtInZoomControls = false
            displayZoomControls = false
            setSupportZoom(false)
        }

        binding.webviewContent.isVerticalScrollBarEnabled = false
        binding.webviewContent.isHorizontalScrollBarEnabled = false
    }

    private fun setupClickListeners() {
        binding.btnBack.setOnClickListener {
            finish()
        }

        binding.btnBackToKnowledge.setOnClickListener {
            finish()
        }

        binding.btnShowAnswer.setOnClickListener {
            showAnswer()
        }

        // Review quality buttons
        binding.btnHard.setOnClickListener {
            submitReview("hard")
        }

        binding.btnGood.setOnClickListener {
            submitReview("good")
        }

        binding.btnEasy.setOnClickListener {
            submitReview("easy")
        }

        binding.btnSkip.setOnClickListener {
            skipCurrentItem()
        }
    }

    private fun setupObservers() {
        viewModel.dueReviewItems.observe(this) { items ->
            reviewItems = items
            if (items.isEmpty()) {
                showEmptyState()
            } else {
                currentIndex = 0
                showCurrentItem()
            }
        }

        viewModel.isLoading.observe(this) { isLoading ->
            binding.loadingOverlay.visibility = if (isLoading) View.VISIBLE else View.GONE
        }

        viewModel.error.observe(this) { error ->
            error?.let {
                Toast.makeText(this, it, Toast.LENGTH_LONG).show()
                viewModel.clearError()
            }
        }

        viewModel.successMessage.observe(this) { message ->
            message?.let {
                // Review marked successfully, move to next item with a short delay
                viewModel.clearSuccessMessage()

                // Delay to allow visual feedback to complete
                binding.root.postDelayed({
                    moveToNextItem()
                }, 500)
            }
        }
    }

    private fun showCurrentItem() {
        if (currentIndex >= reviewItems.size) {
            // All items reviewed
            showCompletionMessage()
            return
        }

        currentItem = reviewItems[currentIndex]
        isAnswerShown = false

        // Show flashcard
        binding.flashcard.visibility = View.VISIBLE
        binding.emptyState.visibility = View.GONE

        // Update progress
        updateProgress()

        // Populate card content
        currentItem?.let { item ->
            binding.tvTitle.text = item.title

            // Show category breadcrumb
            val categoryName = item.category?.name ?: "未分類"
            binding.tvCategory.text = categoryName

            // Show type chip
            binding.chipType.text = when (item.item_type) {
                "note" -> getString(R.string.type_note)
                "code_snippet" -> getString(R.string.type_code)
                "exercise" -> getString(R.string.type_exercise)
                "resource_link" -> getString(R.string.type_link)
                else -> item.item_type
            }

            // Show review count
            binding.tvReviewCount.text = getString(R.string.reviewed_times, item.review_count)

            // Show content based on type
            when (item.item_type) {
                "exercise" -> {
                    // Show question/answer format
                    binding.llQuestion.visibility = View.VISIBLE
                    binding.llContent.visibility = View.GONE
                    binding.tvQuestion.text = item.question ?: ""
                    binding.tvAnswer.text = item.answer ?: ""
                }
                "code_snippet" -> {
                    // Show code with syntax highlighting
                    binding.llQuestion.visibility = View.GONE
                    binding.llContent.visibility = View.VISIBLE
                    binding.tvContentLabel.text = getString(R.string.code)

                    // Use WebView for code highlighting
                    val code = item.content ?: ""
                    val language = item.code_language ?: "plaintext"

                    if (code.isNotEmpty()) {
                        binding.tvContent.visibility = View.GONE
                        binding.webviewContent.visibility = View.VISIBLE

                        val html = CodeHighlightHelper.generateHighlightedHtml(this, code, language)
                        binding.webviewContent.loadDataWithBaseURL(
                            null,
                            html,
                            "text/html",
                            "UTF-8",
                            null
                        )
                    } else {
                        binding.tvContent.visibility = View.VISIBLE
                        binding.webviewContent.visibility = View.GONE
                        binding.tvContent.text = code
                    }
                }
                else -> {
                    // Show regular content
                    binding.llQuestion.visibility = View.GONE
                    binding.llContent.visibility = View.VISIBLE
                    binding.tvContentLabel.text = getString(R.string.content)

                    // Use TextView for non-code content
                    binding.tvContent.visibility = View.VISIBLE
                    binding.webviewContent.visibility = View.GONE
                    binding.tvContent.text = item.content ?: ""
                }
            }

            // Reset UI state
            binding.llAnswer.visibility = View.GONE
            binding.btnShowAnswer.visibility = View.VISIBLE
            binding.llReviewButtons.visibility = View.GONE

            // Update next review info
            updateNextReviewInfo()
        }
    }

    private fun showAnswer() {
        isAnswerShown = true

        // Show answer section
        binding.llAnswer.visibility = View.VISIBLE
        binding.btnShowAnswer.visibility = View.GONE
        binding.llReviewButtons.visibility = View.VISIBLE
    }

    private fun submitReview(quality: String) {
        currentItem?.let { item: KnowledgeItem ->
            // Disable buttons to prevent double-tap
            binding.btnHard.isEnabled = false
            binding.btnGood.isEnabled = false
            binding.btnEasy.isEnabled = false

            // Show visual feedback
            val selectedButton = when (quality) {
                "hard" -> binding.btnHard
                "easy" -> binding.btnEasy
                else -> binding.btnGood
            }
            selectedButton.alpha = 0.5f

            // Mark as reviewed on backend
            viewModel.markAsReviewed(item.id)

            // Add a short delay before moving to next card for better UX
            binding.root.postDelayed({
                // Re-enable buttons
                binding.btnHard.isEnabled = true
                binding.btnGood.isEnabled = true
                binding.btnEasy.isEnabled = true
                selectedButton.alpha = 1.0f
            }, 300)

            // Note: Backend auto-calculates next_review_date based on review_count
            // moveToNextItem() will be called by the successMessage observer
        }
    }

    private fun moveToNextItem() {
        currentIndex++
        showCurrentItem()
    }

    private fun skipCurrentItem() {
        // Move to next item without submitting review
        // Reset the flashcard to front side
        binding.btnShowAnswer.visibility = View.VISIBLE
        binding.llReviewButtons.visibility = View.GONE

        // Move to next
        currentIndex++
        showCurrentItem()
    }

    private fun updateProgress() {
        val total = reviewItems.size
        val current = currentIndex + 1
        val remaining = total - current

        binding.tvProgress.text = "$current / $total"
        binding.tvRemaining.text = getString(R.string.items_left, remaining)

        val progressPercent = (current * 100) / total
        binding.progressBar.progress = progressPercent
    }

    private fun updateNextReviewInfo() {
        // Show estimated next review times based on spaced repetition intervals
        val currentReviewCount = currentItem?.review_count ?: 0

        // Intervals: [1, 3, 7, 14, 30, 60, 120] days
        val hardInterval = getNextInterval(currentReviewCount, "hard")
        val goodInterval = getNextInterval(currentReviewCount, "good")
        val easyInterval = getNextInterval(currentReviewCount, "easy")

        val hardText = formatInterval(hardInterval)
        val goodText = formatInterval(goodInterval)
        val easyText = formatInterval(easyInterval)

        binding.tvNextReviewInfo.text = getString(
            R.string.next_review_intervals,
            hardText, goodText, easyText
        )
    }

    private fun getNextInterval(currentCount: Int, quality: String): Int {
        // Spaced repetition intervals in days
        val intervals = listOf(1, 3, 7, 14, 30, 60, 120)

        return when (quality) {
            "hard" -> intervals.getOrElse(0) { 1 } // Reset to 1 day
            "good" -> intervals.getOrElse(currentCount) { 120 }
            "easy" -> intervals.getOrElse(currentCount + 1) { 120 }
            else -> intervals.getOrElse(currentCount) { 120 }
        }
    }

    private fun formatInterval(days: Int): String {
        return when {
            days == 1 -> getString(R.string.one_day)
            days < 7 -> getString(R.string.days_count, days)
            days < 30 -> getString(R.string.weeks_count, days / 7)
            days < 365 -> getString(R.string.months_count, days / 30)
            else -> getString(R.string.years_count, days / 365)
        }
    }

    private fun showEmptyState() {
        binding.flashcard.visibility = View.GONE
        binding.emptyState.visibility = View.VISIBLE
        binding.llReviewButtons.visibility = View.GONE
        binding.tvProgress.text = "0 / 0"
        binding.tvRemaining.text = getString(R.string.items_left, 0)
        binding.progressBar.progress = 100
    }

    private fun showCompletionMessage() {
        Toast.makeText(
            this,
            getString(R.string.review_completed),
            Toast.LENGTH_LONG
        ).show()

        // Show completion state
        binding.flashcard.visibility = View.GONE
        binding.emptyState.visibility = View.VISIBLE
        binding.llReviewButtons.visibility = View.GONE

        // Update empty state text for completion
        binding.emptyState.findViewById<android.widget.TextView>(
            resources.getIdentifier("tv_empty_message", "id", packageName)
        )?.text = getString(R.string.all_reviews_completed)
    }
}
