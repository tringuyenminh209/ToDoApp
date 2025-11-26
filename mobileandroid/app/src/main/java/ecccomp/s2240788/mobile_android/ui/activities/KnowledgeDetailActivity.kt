package ecccomp.s2240788.mobile_android.ui.activities

import android.annotation.SuppressLint
import android.content.ClipData
import android.content.ClipboardManager
import android.content.Context
import android.content.Intent
import android.net.Uri
import android.os.Bundle
import android.view.View
import android.widget.PopupMenu
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.lifecycle.ViewModelProvider
import com.google.android.material.chip.Chip
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.KnowledgeItem
import ecccomp.s2240788.mobile_android.databinding.ActivityKnowledgeDetailBinding
import ecccomp.s2240788.mobile_android.ui.viewmodels.KnowledgeDetailViewModel
import ecccomp.s2240788.mobile_android.utils.CodeHighlightHelper

/**
 * KnowledgeDetailActivity
 * 知識アイテムの詳細表示画面
 * - Different layouts for different item types
 * - Review tracking
 * - Favorite, Archive, Edit, Delete actions
 */
class KnowledgeDetailActivity : BaseActivity() {

    private lateinit var binding: ActivityKnowledgeDetailBinding
    private lateinit var viewModel: KnowledgeDetailViewModel
    private var knowledgeItemId: Int = -1

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityKnowledgeDetailBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupWindowInsets()

        viewModel = ViewModelProvider(this)[KnowledgeDetailViewModel::class.java]

        // Get knowledge item ID from intent
        knowledgeItemId = intent.getIntExtra("KNOWLEDGE_ITEM_ID", -1)
        if (knowledgeItemId == -1) {
            Toast.makeText(this, "知識アイテムが見つかりません", Toast.LENGTH_SHORT).show()
            finish()
            return
        }

        setupWebView()
        setupClickListeners()
        setupObservers()

        // Load knowledge item
        viewModel.loadKnowledgeItem(knowledgeItemId)
    }

    @SuppressLint("SetJavaScriptEnabled")
    private fun setupWebView() {
        // Setup code WebView
        binding.webviewCode.settings.apply {
            javaScriptEnabled = true
            domStorageEnabled = true
            loadWithOverviewMode = false
            useWideViewPort = false
            builtInZoomControls = false
            displayZoomControls = false
            setSupportZoom(false)
        }
        binding.webviewCode.isVerticalScrollBarEnabled = false
        binding.webviewCode.isHorizontalScrollBarEnabled = false

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

        // Setup note WebView
        binding.webviewNote.settings.apply {
            javaScriptEnabled = true
            domStorageEnabled = true
            loadWithOverviewMode = false
            useWideViewPort = false
            builtInZoomControls = false
            displayZoomControls = false
            setSupportZoom(false)
        }
        binding.webviewNote.isVerticalScrollBarEnabled = false
        binding.webviewNote.isHorizontalScrollBarEnabled = false
    }

    private fun setupClickListeners() {
        binding.btnBack.setOnClickListener {
            finish()
        }

        binding.btnFavorite.setOnClickListener {
            viewModel.toggleFavorite()
        }

        binding.btnMore.setOnClickListener {
            showMoreMenu()
        }

        binding.btnCopyCode.setOnClickListener {
            copyCodeToClipboard()
        }

        binding.btnShowAnswer.setOnClickListener {
            toggleAnswer()
        }

        binding.btnOpenLink.setOnClickListener {
            openUrlInBrowser()
        }

        binding.btnMarkReviewed.setOnClickListener {
            viewModel.markAsReviewed()
        }
    }

    private fun setupObservers() {
        viewModel.knowledgeItem.observe(this) { item ->
            item?.let {
                displayKnowledgeItem(it)
            }
        }

        viewModel.isLoading.observe(this) { isLoading ->
            binding.progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
        }

        viewModel.error.observe(this) { error ->
            error?.let {
                Toast.makeText(this, it, Toast.LENGTH_LONG).show()
                viewModel.clearError()
            }
        }

        viewModel.toast.observe(this) { message ->
            message?.let {
                Toast.makeText(this, it, Toast.LENGTH_SHORT).show()
                viewModel.clearToast()
            }
        }

        viewModel.finishActivity.observe(this) { shouldFinish ->
            if (shouldFinish) {
                finish()
            }
        }
    }

    private fun displayKnowledgeItem(item: KnowledgeItem) {
        // Set title
        binding.tvTitle.text = item.title

        // Set favorite icon
        val favoriteIcon = if (item.is_favorite) R.drawable.ic_star_filled else R.drawable.ic_star
        binding.btnFavorite.setImageResource(favoriteIcon)
        val favoriteTint = if (item.is_favorite) R.color.warning else R.color.text_muted
        binding.btnFavorite.setColorFilter(resources.getColor(favoriteTint, null))

        // Set type badge
        val (typeText, typeColor) = when (item.item_type) {
            "note" -> Pair("ノート", R.color.primary)
            "code_snippet" -> Pair("コード", R.color.info)
            "exercise" -> Pair("演習", R.color.success)
            "resource_link" -> Pair("リンク", R.color.warning)
            "attachment" -> Pair("添付", R.color.accent)
            else -> Pair("その他", R.color.text_muted)
        }
        binding.chipType.text = typeText
        binding.chipType.setChipBackgroundColorResource(typeColor)

        // Display tags
        if (!item.tags.isNullOrEmpty()) {
            binding.chipGroupTags.visibility = View.VISIBLE
            binding.chipGroupTags.removeAllViews()
            item.tags.forEach { tag ->
                val chip = Chip(this)
                chip.text = tag
                chip.isClickable = false
                chip.setChipBackgroundColorResource(R.color.surface)
                chip.setTextColor(resources.getColor(R.color.text_secondary, null))
                binding.chipGroupTags.addView(chip)
            }
        } else {
            binding.chipGroupTags.visibility = View.GONE
        }

        // Display content based on item type
        hideAllContentCards()

        when (item.item_type) {
            "note" -> displayNote(item)
            "code_snippet" -> displayCodeSnippet(item)
            "exercise" -> displayExercise(item)
            "resource_link" -> displayResourceLink(item)
            "attachment" -> displayAttachment(item)
        }

        // Display review information
        binding.tvReviewCount.text = "${item.review_count}回"
        binding.tvLastReviewed.text = formatDateTime(item.last_reviewed_at) ?: "未復習"
        binding.tvNextReview.text = formatDate(item.next_review_date) ?: "未設定"

        // Display meta information
        binding.tvViewCount.text = "${item.view_count}回"
        binding.tvCreatedAt.text = formatDate(item.created_at) ?: ""
    }

    private fun hideAllContentCards() {
        binding.cardContent.visibility = View.GONE
        binding.cardCode.visibility = View.GONE
        binding.cardExercise.visibility = View.GONE
        binding.cardLink.visibility = View.GONE
    }

    private fun displayNote(item: KnowledgeItem) {
        binding.cardContent.visibility = View.VISIBLE

        val content = item.content ?: ""
        val isCode = isCodeContent(content)

        android.util.Log.d("KnowledgeDetail", "displayNote - isCode: $isCode, content length: ${content.length}")

        if (isCode) {
            // Use WebView for code highlighting
            binding.tvContent.visibility = View.GONE
            binding.webviewNote.visibility = View.VISIBLE

            // Extract code from code blocks if present
            val (blockLanguage, codeContent) = extractCodeFromBlock(content)
            val detectedLanguage = detectCodeLanguage(codeContent)

            // Use language from code block, detected language, item's code_language, or default
            val language = blockLanguage ?: detectedLanguage ?: item.code_language ?: "plaintext"

            android.util.Log.d("KnowledgeDetail", "Language detection - block: $blockLanguage, detected: $detectedLanguage, final: $language")

            val html = CodeHighlightHelper.generateHighlightedHtml(this, codeContent, language)
            binding.webviewNote.loadDataWithBaseURL(
                null,
                html,
                "text/html",
                "UTF-8",
                null
            )
        } else {
            // Use TextView for plain text
            binding.tvContent.visibility = View.VISIBLE
            binding.webviewNote.visibility = View.GONE
            binding.tvContent.text = content
        }
    }

    private fun displayCodeSnippet(item: KnowledgeItem) {
        binding.cardCode.visibility = View.VISIBLE
        binding.tvCodeLanguage.text = item.code_language?.uppercase() ?: "CODE"

        val code = item.content ?: ""
        val language = item.code_language ?: "plaintext"

        if (code.isNotEmpty()) {
            // Use WebView with syntax highlighting
            binding.scrollCode.visibility = View.GONE
            binding.webviewCode.visibility = View.VISIBLE

            val html = CodeHighlightHelper.generateHighlightedHtml(this, code, language)
            binding.webviewCode.loadDataWithBaseURL(
                null,
                html,
                "text/html",
                "UTF-8",
                null
            )
        } else {
            // Fallback to TextView for empty code
            binding.scrollCode.visibility = View.VISIBLE
            binding.webviewCode.visibility = View.GONE
            binding.tvCodeContent.text = code
        }
    }

    private fun displayExercise(item: KnowledgeItem) {
        binding.cardExercise.visibility = View.VISIBLE
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

        // Set difficulty chip
        val (diffText, diffColor) = when (item.difficulty) {
            "easy" -> Pair("簡単", R.color.success)
            "medium" -> Pair("普通", R.color.warning)
            "hard" -> Pair("難しい", R.color.error)
            else -> Pair("未設定", R.color.text_muted)
        }
        binding.chipDifficulty.text = diffText
        binding.chipDifficulty.setChipBackgroundColorResource(diffColor)

        // Initially hide answer
        binding.layoutAnswer.visibility = View.GONE
        binding.btnShowAnswer.text = getString(R.string.show_answer)
    }

    /**
     * Check if content is code (contains code blocks or code patterns)
     */
    private fun isCodeContent(content: String): Boolean {
        if (content.isEmpty()) return false

        // Check for code blocks (```...```)
        if (content.contains("```")) {
            android.util.Log.d("KnowledgeDetail", "isCodeContent: Found code block markers")
            return true
        }

        // Check for common code patterns (case-sensitive)
        val codePatterns = listOf(
            "def ", "function ", "class ", "import ", "from ",
            "const ", "let ", "var ", "public ", "private ",
            "<?php", "package ", "func ", "#include",
            "if (", "for (", "while (", "return ",
            "->", "::", "=>", "&&", "||"
        )

        // Check for SQL patterns (case-insensitive, no trailing space required)
        val sqlPatterns = listOf(
            "SELECT", "INSERT", "UPDATE", "DELETE", "CREATE", "ALTER",
            "DROP", "FROM", "WHERE", "JOIN", "SHOW", "USE", "DATABASE",
            "TABLE", "INDEX", "VIEW", "PROCEDURE", "TRIGGER",
            "GROUP BY", "ORDER BY", "LIMIT", "OFFSET", "HAVING",
            "INNER JOIN", "LEFT JOIN", "RIGHT JOIN", "PRIMARY KEY"
        )

        // Check for markdown patterns
        val markdownPatterns = listOf(
            "**", "##", "###", "####", "- **", "* **", "```",
            "# ", "## ", "### "
        )

        val contentUpper = content.uppercase()
        val hasCodePattern = codePatterns.any { content.contains(it) }
        val hasSqlPattern = sqlPatterns.any { contentUpper.contains(it) }
        val hasMarkdownPattern = markdownPatterns.any { content.contains(it) }

        val result = hasCodePattern || hasSqlPattern || hasMarkdownPattern

        android.util.Log.d("KnowledgeDetail", "isCodeContent: code=$hasCodePattern, sql=$hasSqlPattern, markdown=$hasMarkdownPattern, result=$result")

        return result
    }

    /**
     * Extract code and language from code blocks (```language\ncode\n```)
     * Returns Pair(language, code)
     */
    private fun extractCodeFromBlock(content: String): Pair<String?, String> {
        // Check for code blocks
        if (content.contains("```")) {
            // Try to extract language and code: ```language\ncode\n``` or ```language code```
            // More flexible regex that handles various formats
            val regex = Regex("```(\\w+)?\\s*([\\s\\S]*?)```", RegexOption.DOT_MATCHES_ALL)
            val match = regex.find(content)
            if (match != null) {
                val language = match.groupValues[1].takeIf { it.isNotEmpty() }
                val code = match.groupValues[2].trim()
                android.util.Log.d("KnowledgeDetail", "Extracted code block - language: $language, code length: ${code.length}")
                return Pair(language, code)
            }
        }
        return Pair(null, content.trim())
    }

    /**
     * Detect code language from content
     */
    private fun detectCodeLanguage(content: String): String? {
        val contentUpper = content.uppercase()

        // Check for markdown first (if has markdown patterns)
        if (content.contains("**") || content.contains("##") || content.contains("###") || content.contains("# ")) {
            android.util.Log.d("KnowledgeDetail", "Detected markdown")
            return "markdown"
        }

        // Check for SQL (case-insensitive, no space required)
        val sqlKeywords = listOf(
            "SELECT", "INSERT", "UPDATE", "DELETE", "CREATE", "ALTER",
            "DROP", "SHOW", "USE", "DATABASE", "TABLE", "INDEX",
            "VIEW", "PROCEDURE", "TRIGGER", "FROM", "WHERE", "JOIN"
        )
        if (sqlKeywords.any { contentUpper.contains(it) }) {
            android.util.Log.d("KnowledgeDetail", "Detected SQL")
            return "sql"
        }

        val patterns = mapOf(
            "python" to listOf("def ", "import ", "from ", "class ", "__init__", "print("),
            "javascript" to listOf("const ", "let ", "var ", "function ", "=>", "console.log"),
            "java" to listOf("public class", "private ", "protected ", "System.out", "public static"),
            "php" to listOf("<?php", "namespace ", "use ", "::", "$"),
            "go" to listOf("func ", "package ", "import ", "type ", "var "),
            "cpp" to listOf("#include", "std::", "cout", "cin", "using namespace"),
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

    private fun displayResourceLink(item: KnowledgeItem) {
        binding.cardLink.visibility = View.VISIBLE
        binding.tvUrl.text = item.url ?: ""

        // Also show content if available
        if (!item.content.isNullOrEmpty()) {
            binding.cardContent.visibility = View.VISIBLE
            binding.tvContent.text = item.content
        }
    }

    private fun displayAttachment(item: KnowledgeItem) {
        // For now, just show content
        binding.cardContent.visibility = View.VISIBLE
        val attachmentInfo = buildString {
            append("ファイル名: ${item.attachment_path ?: "不明"}\n")
            append("種類: ${item.attachment_mime ?: "不明"}\n")
            append("サイズ: ${formatFileSize(item.attachment_size ?: 0)}\n\n")
            if (!item.content.isNullOrEmpty()) {
                append(item.content)
            }
        }
        binding.tvContent.text = attachmentInfo
    }

    private fun formatFileSize(bytes: Int): String {
        return when {
            bytes < 1024 -> "$bytes B"
            bytes < 1024 * 1024 -> "${bytes / 1024} KB"
            else -> "${bytes / (1024 * 1024)} MB"
        }
    }

    private fun copyCodeToClipboard() {
        val item = viewModel.knowledgeItem.value
        val codeContent = item?.content ?: binding.tvCodeContent.text.toString()
        val clipboard = getSystemService(Context.CLIPBOARD_SERVICE) as ClipboardManager
        val clip = ClipData.newPlainText("code", codeContent)
        clipboard.setPrimaryClip(clip)
        Toast.makeText(this, R.string.code_copied, Toast.LENGTH_SHORT).show()
    }

    private fun toggleAnswer() {
        if (binding.layoutAnswer.visibility == View.VISIBLE) {
            binding.layoutAnswer.visibility = View.GONE
            binding.btnShowAnswer.text = getString(R.string.show_answer)
        } else {
            binding.layoutAnswer.visibility = View.VISIBLE
            binding.btnShowAnswer.text = getString(R.string.hide_answer)
        }
    }

    private fun openUrlInBrowser() {
        val url = binding.tvUrl.text.toString()
        if (url.isNotEmpty()) {
            try {
                val intent = Intent(Intent.ACTION_VIEW, Uri.parse(url))
                startActivity(intent)
            } catch (e: Exception) {
                Toast.makeText(this, "URLを開けませんでした", Toast.LENGTH_SHORT).show()
            }
        }
    }

    private fun showMoreMenu() {
        val popup = PopupMenu(this, binding.btnMore)
        popup.menuInflater.inflate(R.menu.menu_knowledge_detail, popup.menu)

        // Update archive menu item text
        val item = viewModel.knowledgeItem.value
        if (item != null) {
            val archiveItem = popup.menu.findItem(R.id.action_archive)
            archiveItem?.title = if (item.is_archived) "復元" else "アーカイブ"
        }

        popup.setOnMenuItemClickListener { menuItem ->
            when (menuItem.itemId) {
                R.id.action_edit -> {
                    val intent = Intent(this, KnowledgeEditorActivity::class.java)
                    intent.putExtra("KNOWLEDGE_ITEM_ID", knowledgeItemId)
                    startActivity(intent)
                    true
                }
                R.id.action_review -> {
                    addToReviewList()
                    true
                }
                R.id.action_archive -> {
                    viewModel.toggleArchive()
                    true
                }
                R.id.action_delete -> {
                    showDeleteConfirmation()
                    true
                }
                R.id.action_share -> {
                    shareKnowledgeItem()
                    true
                }
                else -> false
            }
        }
        popup.show()
    }

    private fun showDeleteConfirmation() {
        AlertDialog.Builder(this)
            .setTitle("削除確認")
            .setMessage("この知識アイテムを削除しますか？")
            .setPositiveButton("削除") { _, _ ->
                viewModel.deleteItem()
            }
            .setNegativeButton("キャンセル", null)
            .show()
    }

    private fun shareKnowledgeItem() {
        val item = viewModel.knowledgeItem.value ?: return
        val shareText = buildString {
            append("${item.title}\n\n")
            when (item.item_type) {
                "note" -> append(item.content ?: "")
                "code_snippet" -> append("${item.code_language}:\n${item.content}")
                "exercise" -> {
                    append("問題: ${item.question}\n")
                    append("答え: ${item.answer}")
                }
                "resource_link" -> append(item.url ?: "")
            }
        }

        val intent = Intent(Intent.ACTION_SEND).apply {
            type = "text/plain"
            putExtra(Intent.EXTRA_TEXT, shareText)
        }
        startActivity(Intent.createChooser(intent, "共有"))
    }

    /**
     * 復習リストに追加する
     * アイテムを復習リストに追加して、後で復習できるようにする
     */
    private fun addToReviewList() {
        val item = viewModel.knowledgeItem.value ?: return

        // 既に復習リストにあるかチェック（next_review_dateが今日以前なら既に復習対象）
        val today = java.text.SimpleDateFormat("yyyy-MM-dd", java.util.Locale.getDefault())
            .format(java.util.Date())
        
        if (!item.next_review_date.isNullOrEmpty() && item.next_review_date <= today) {
            // 既に復習リストにある場合
            AlertDialog.Builder(this)
                .setTitle(getString(R.string.review_title))
                .setMessage("このアイテムは既に復習リストに追加されています。\n復習画面で復習できます。")
                .setPositiveButton("OK", null)
                .show()
            return
        }

        // 復習リストに追加
        viewModel.addToReview()
    }

    /**
     * 日付文字列をフォーマット（yyyy-MM-dd形式から日本語形式へ）
     */
    private fun formatDate(dateString: String?): String? {
        if (dateString.isNullOrEmpty()) return null
        
        return try {
            // ISO 8601形式またはyyyy-MM-dd形式をパース
            val datePart = if (dateString.contains("T")) {
                dateString.substring(0, 10) // "2025-10-27T15:00:00.000000Z" -> "2025-10-27"
            } else {
                dateString
            }
            
            val inputFormat = java.text.SimpleDateFormat("yyyy-MM-dd", java.util.Locale.getDefault())
            val outputFormat = java.text.SimpleDateFormat("yyyy年MM月dd日", java.util.Locale.getDefault())
            val date = inputFormat.parse(datePart)
            date?.let { outputFormat.format(it) }
        } catch (e: Exception) {
            // パースに失敗した場合は、手動でフォーマット
            try {
                val parts = dateString.substring(0, 10).split("-")
                if (parts.size == 3) {
                    "${parts[0]}年${parts[1]}月${parts[2]}日"
                } else {
                    dateString
                }
            } catch (e2: Exception) {
                dateString
            }
        }
    }

    /**
     * 日時文字列をフォーマット（ISO 8601形式から日本語形式へ）
     */
    private fun formatDateTime(dateTimeString: String?): String? {
        if (dateTimeString.isNullOrEmpty()) return null
        
        return try {
            // ISO 8601形式をパース（複数の形式に対応）
            val datePart = dateTimeString.substring(0, 10) // "2025-10-27"
            val timePart = if (dateTimeString.contains("T") && dateTimeString.length > 11) {
                // "2025-10-27T15:00:00.000000Z" -> "15:00"
                val timeStart = dateTimeString.indexOf("T") + 1
                val timeEnd = dateTimeString.indexOf(".", timeStart).takeIf { it > 0 } 
                    ?: dateTimeString.indexOf("Z", timeStart).takeIf { it > 0 }
                    ?: dateTimeString.length
                dateTimeString.substring(timeStart, minOf(timeEnd, timeStart + 5)) // "HH:mm"のみ取得
            } else {
                null
            }
            
            val dateFormatted = formatDate(datePart)
            if (timePart != null && dateFormatted != null) {
                "$dateFormatted $timePart"
            } else {
                dateFormatted
            }
        } catch (e: Exception) {
            // パースに失敗した場合は、日付部分のみを表示
            if (dateTimeString.length >= 10) {
                formatDate(dateTimeString.substring(0, 10))
            } else {
                dateTimeString
            }
        }
    }

    override fun onResume() {
        super.onResume()
        // Reload to get updated data
        if (knowledgeItemId != -1) {
            viewModel.loadKnowledgeItem(knowledgeItemId)
        }
    }
}
