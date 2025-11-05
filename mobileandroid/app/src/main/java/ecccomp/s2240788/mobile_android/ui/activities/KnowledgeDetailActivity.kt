package ecccomp.s2240788.mobile_android.ui.activities

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

        viewModel = ViewModelProvider(this)[KnowledgeDetailViewModel::class.java]

        // Get knowledge item ID from intent
        knowledgeItemId = intent.getIntExtra("KNOWLEDGE_ITEM_ID", -1)
        if (knowledgeItemId == -1) {
            Toast.makeText(this, "知識アイテムが見つかりません", Toast.LENGTH_SHORT).show()
            finish()
            return
        }

        setupClickListeners()
        setupObservers()

        // Load knowledge item
        viewModel.loadKnowledgeItem(knowledgeItemId)
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
        binding.tvLastReviewed.text = item.last_reviewed_at ?: "未復習"
        binding.tvNextReview.text = item.next_review_date ?: "未設定"

        // Display meta information
        binding.tvViewCount.text = "${item.view_count}回"
        binding.tvCreatedAt.text = item.created_at.substring(0, 10)
    }

    private fun hideAllContentCards() {
        binding.cardContent.visibility = View.GONE
        binding.cardCode.visibility = View.GONE
        binding.cardExercise.visibility = View.GONE
        binding.cardLink.visibility = View.GONE
    }

    private fun displayNote(item: KnowledgeItem) {
        binding.cardContent.visibility = View.VISIBLE
        binding.tvContent.text = item.content ?: ""
    }

    private fun displayCodeSnippet(item: KnowledgeItem) {
        binding.cardCode.visibility = View.VISIBLE
        binding.tvCodeLanguage.text = item.code_language?.uppercase() ?: "CODE"
        binding.tvCodeContent.text = item.content ?: ""
    }

    private fun displayExercise(item: KnowledgeItem) {
        binding.cardExercise.visibility = View.VISIBLE
        binding.tvQuestion.text = item.question ?: ""
        binding.tvAnswer.text = item.answer ?: ""

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
        val codeContent = binding.tvCodeContent.text.toString()
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
                    // TODO: Open edit activity
                    Toast.makeText(this, "編集機能（開発中）", Toast.LENGTH_SHORT).show()
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

    override fun onResume() {
        super.onResume()
        // Reload to get updated data
        if (knowledgeItemId != -1) {
            viewModel.loadKnowledgeItem(knowledgeItemId)
        }
    }
}
