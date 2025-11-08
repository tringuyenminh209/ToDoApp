package ecccomp.s2240788.mobile_android.ui.adapters

import android.content.ClipData
import android.content.ClipboardManager
import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.webkit.WebView
import android.widget.Toast
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.KnowledgeItem
import ecccomp.s2240788.mobile_android.databinding.ItemFocusKnowledgeBinding
import ecccomp.s2240788.mobile_android.utils.CodeHighlightHelper

/**
 * FocusKnowledgeAdapter
 * Focus Session内で学習内容を表示するアダプター
 * Compact view with expandable content and beautiful colors
 */
class FocusKnowledgeAdapter(
    private val onItemClick: (KnowledgeItem) -> Unit
) : ListAdapter<KnowledgeItem, FocusKnowledgeAdapter.ViewHolder>(KnowledgeDiffCallback()) {

    // Track expanded items
    private val expandedItems = mutableSetOf<Int>()

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemFocusKnowledgeBinding.inflate(
            LayoutInflater.from(parent.context),
            parent,
            false
        )
        return ViewHolder(binding, onItemClick)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        val item = getItem(position)
        val isExpanded = expandedItems.contains(item.id)
        holder.bind(item, isExpanded) {
            // Toggle expansion
            if (isExpanded) {
                expandedItems.remove(item.id)
            } else {
                expandedItems.add(item.id)
            }
            notifyItemChanged(position)
        }
    }

    class ViewHolder(
        private val binding: ItemFocusKnowledgeBinding,
        private val onItemClick: (KnowledgeItem) -> Unit
    ) : RecyclerView.ViewHolder(binding.root) {

        fun bind(item: KnowledgeItem, isExpanded: Boolean, onToggleExpand: () -> Unit) {
            binding.apply {
                // Set type-based colors and styles
                val (typeText, bgColor, stripColor, icon) = when (item.item_type) {
                    "note" -> Quadruple("ノート", R.color.warning_light, R.color.warning, R.drawable.ic_description)
                    "code_snippet" -> Quadruple("コード", R.color.primary_light, R.color.primary, R.drawable.ic_computer)
                    "exercise" -> Quadruple("演習", R.color.info_light, R.color.info, R.drawable.ic_checklist)
                    "resource_link" -> Quadruple("リンク", R.color.accent_light, R.color.accent, R.drawable.ic_link)
                    else -> Quadruple("その他", R.color.surface, R.color.text_muted, R.drawable.ic_book)
                }

                // Set header strip color
                headerStrip.setBackgroundColor(ContextCompat.getColor(root.context, stripColor))

                // Set type badge colors
                typeBadge.setCardBackgroundColor(ContextCompat.getColor(root.context, bgColor))
                ivTypeIcon.setImageResource(icon)
                ivTypeIcon.setColorFilter(ContextCompat.getColor(root.context, stripColor))
                chipType.text = typeText
                chipType.setTextColor(ContextCompat.getColor(root.context, stripColor))

                // Set title
                tvTitle.text = item.title

                // Set content preview
                val preview = when (item.item_type) {
                    "note" -> item.content?.take(100) ?: ""
                    "code_snippet" -> "${item.code_language}: ${item.content?.take(50) ?: ""}"
                    "exercise" -> item.question?.take(100) ?: ""
                    "resource_link" -> item.url ?: ""
                    else -> item.content?.take(100) ?: ""
                }
                tvContentPreview.text = preview

                // Update expand icon
                ivExpand.rotation = if (isExpanded) 180f else 0f

                // Handle expansion
                expandableContent.visibility = if (isExpanded) View.VISIBLE else View.GONE

                if (isExpanded) {
                    displayExpandedContent(item)
                }

                // Click listeners
                root.setOnClickListener {
                    onToggleExpand()
                }

                ivExpand.setOnClickListener {
                    onToggleExpand()
                }
            }
        }

        private fun displayExpandedContent(item: KnowledgeItem) {
            binding.apply {
                // Hide all containers first
                tvFullContent.visibility = View.GONE
                codeContainer.visibility = View.GONE
                exerciseContainer.visibility = View.GONE
                linkContainer.visibility = View.GONE

                when (item.item_type) {
                    "note" -> {
                        tvFullContent.visibility = View.VISIBLE
                        tvFullContent.text = item.content ?: ""
                    }

                    "code_snippet" -> {
                        codeContainer.visibility = View.VISIBLE
                        tvCodeLanguage.text = item.code_language?.uppercase() ?: "CODE"

                        // Use WebView with syntax highlighting
                        val code = item.content ?: ""
                        val language = item.code_language ?: "plaintext"
                        val highlightedHtml = CodeHighlightHelper.generateHighlightedHtml(
                            root.context,
                            code,
                            language
                        )

                        webviewCode.apply {
                            settings.javaScriptEnabled = true
                            setBackgroundColor(android.graphics.Color.TRANSPARENT)
                            loadDataWithBaseURL(null, highlightedHtml, "text/html", "UTF-8", null)
                        }

                        btnCopyCode.setOnClickListener {
                            copyToClipboard(it.context, code)
                        }
                    }

                    "exercise" -> {
                        exerciseContainer.visibility = View.VISIBLE
                        tvQuestion.text = item.question ?: ""
                        tvAnswer.text = item.answer ?: ""

                        // Initially hide answer
                        answerContainer.visibility = View.GONE

                        btnShowAnswer.setOnClickListener {
                            if (answerContainer.visibility == View.VISIBLE) {
                                answerContainer.visibility = View.GONE
                                btnShowAnswer.text = "答えを表示"
                                btnShowAnswer.icon = ContextCompat.getDrawable(it.context, R.drawable.ic_visibility)
                            } else {
                                answerContainer.visibility = View.VISIBLE
                                btnShowAnswer.text = "答えを隠す"
                                btnShowAnswer.icon = ContextCompat.getDrawable(it.context, R.drawable.ic_visibility_off)
                            }
                        }
                    }

                    "resource_link" -> {
                        linkContainer.visibility = View.VISIBLE
                        tvUrl.text = item.url ?: ""

                        btnOpenLink.setOnClickListener {
                            // This will be handled by autoLink in TextView
                            Toast.makeText(it.context, "リンクをタップしてください", Toast.LENGTH_SHORT).show()
                        }
                    }
                }
            }
        }

        private fun copyToClipboard(context: Context, text: String) {
            val clipboard = context.getSystemService(Context.CLIPBOARD_SERVICE) as ClipboardManager
            val clip = ClipData.newPlainText("code", text)
            clipboard.setPrimaryClip(clip)
            Toast.makeText(context, "コードをコピーしました", Toast.LENGTH_SHORT).show()
        }

        // Helper data class for multiple values
        private data class Quadruple<A, B, C, D>(val first: A, val second: B, val third: C, val fourth: D)
    }

    class KnowledgeDiffCallback : DiffUtil.ItemCallback<KnowledgeItem>() {
        override fun areItemsTheSame(oldItem: KnowledgeItem, newItem: KnowledgeItem): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(oldItem: KnowledgeItem, newItem: KnowledgeItem): Boolean {
            return oldItem == newItem
        }
    }
}
