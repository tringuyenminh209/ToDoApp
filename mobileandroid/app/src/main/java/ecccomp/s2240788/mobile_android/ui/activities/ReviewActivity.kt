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
        // Setup content WebView
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

        // Setup answer WebView
        binding.webviewAnswer.settings.apply {
            javaScriptEnabled = true
            domStorageEnabled = true
            loadWithOverviewMode = false
            useWideViewPort = false
            builtInZoomControls = false
            displayZoomControls = false
            setSupportZoom(false)
        }
        binding.webviewAnswer.isVerticalScrollBarEnabled = false
        binding.webviewAnswer.isHorizontalScrollBarEnabled = false
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
                    
                    // Display answer with code highlighting if it contains code
                    val answer = item.answer ?: ""
                    val isCode = isCodeContent(answer)
                    
                    if (isCode) {
                        // Use WebView for code highlighting
                        binding.tvAnswer.visibility = View.GONE
                        binding.webviewAnswer.visibility = View.VISIBLE
                        
                        // Extract code from code blocks if present
                        val (blockLanguage, codeContent) = extractCodeFromBlock(answer)
                        val detectedLanguage = detectCodeLanguage(codeContent)
                        
                        // Use language from code block, detected language, item's code_language, or default
                        val language = blockLanguage ?: detectedLanguage ?: item.code_language ?: "plaintext"
                        val html = CodeHighlightHelper.generateHighlightedHtml(this, codeContent, language)
                        binding.webviewAnswer.loadDataWithBaseURL(
                            null,
                            html,
                            "text/html",
                            "UTF-8",
                            null
                        )
                    } else {
                        // Use TextView for plain text
                        binding.tvAnswer.visibility = View.VISIBLE
                        binding.webviewAnswer.visibility = View.GONE
                        binding.tvAnswer.text = answer
                    }
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

            // Mark as reviewed on backend with quality parameter
            viewModel.markAsReviewed(item.id, quality)

            // Add a short delay before moving to next card for better UX
            binding.root.postDelayed({
                // Re-enable buttons
                binding.btnHard.isEnabled = true
                binding.btnGood.isEnabled = true
                binding.btnEasy.isEnabled = true
                selectedButton.alpha = 1.0f
            }, 300)

            // Note: Backend calculates next_review_date based on review_count and quality
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
        // Spaced repetition intervals in days (matches backend algorithm)
        val intervals = listOf(1, 3, 7, 14, 30, 60, 120)

        // Calculate base index (same as backend)
        val baseIndex = maxOf(0, minOf(currentCount - 1, intervals.size - 1))

        return when (quality) {
            "hard" -> {
                // Hard: Use shorter interval (previous level or minimum)
                val index = maxOf(0, baseIndex - 1)
                intervals.getOrElse(index) { 1 }
            }
            "easy" -> {
                // Easy: Use longer interval (next level or maximum)
                val index = minOf(baseIndex + 1, intervals.size - 1)
                intervals.getOrElse(index) { 120 }
            }
            else -> {
                // Good: Use normal interval
                intervals.getOrElse(baseIndex) { 120 }
            }
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

    /**
     * Check if content is code (contains code blocks or code patterns)
     */
    private fun isCodeContent(content: String): Boolean {
        if (content.isEmpty()) return false
        
        // Check for code blocks (```...```)
        if (content.contains("```")) {
            return true
        }
        
        // Check for common code patterns
        val codePatterns = listOf(
            "def ", "function ", "class ", "import ", "from ",
            "const ", "let ", "var ", "public ", "private ",
            "<?php", "package ", "func ", "#include",
            "SELECT ", "INSERT ", "UPDATE ", "DELETE ",
            "if (", "for (", "while (", "return ",
            "->", "::", "=>", "&&", "||"
        )
        
        return codePatterns.any { content.contains(it) }
    }

    /**
     * Extract code and language from code blocks (```language\ncode\n```)
     * Returns Pair(language, code)
     */
    private fun extractCodeFromBlock(content: String): Pair<String?, String> {
        // Check for code blocks
        if (content.contains("```")) {
            // Try to extract language and code: ```language\ncode\n```
            val regex = Regex("```(\\w+)?\\s*\\n([\\s\\S]*?)\\n```")
            val match = regex.find(content)
            if (match != null) {
                val language = match.groupValues[1].takeIf { it.isNotEmpty() }
                val code = match.groupValues[2].trim()
                return Pair(language, code)
            }
        }
        return Pair(null, content.trim())
    }

    /**
     * Detect code language from content
     */
    private fun detectCodeLanguage(content: String): String? {
        val patterns = mapOf(
            "python" to listOf("def ", "import ", "from ", "class ", "__init__", "print("),
            "javascript" to listOf("const ", "let ", "var ", "function ", "=>", "console.log"),
            "java" to listOf("public class", "private ", "protected ", "System.out", "public static"),
            "php" to listOf("<?php", "namespace ", "use ", "::", "$"),
            "go" to listOf("func ", "package ", "import ", "type ", "var "),
            "cpp" to listOf("#include", "std::", "cout", "cin", "using namespace"),
            "sql" to listOf("SELECT ", "INSERT ", "UPDATE ", "DELETE ", "FROM ", "WHERE "),
            "bash" to listOf("#!/bin/bash", "#!/bin/sh", "echo ", "if [", "for "),
            "html" to listOf("<!DOCTYPE", "<html", "<div", "<script", "<style"),
            "css" to listOf("@media", "@keyframes", "margin:", "padding:", "background:")
        )
        
        for ((lang, keywords) in patterns) {
            if (keywords.any { content.contains(it, ignoreCase = true) }) {
                return lang
            }
        }
        
        return null
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
