package ecccomp.s2240788.mobile_android.ui.adapters

import android.content.ClipData
import android.content.ClipboardManager
import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Toast
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.KnowledgeItem
import ecccomp.s2240788.mobile_android.databinding.ItemFocusKnowledgeBinding

/**
 * FocusKnowledgeAdapter
 * Focus Session内で学習内容を表示するアダプター
 * Compact view with expandable content
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
                // Set type badge and icon
                val (typeText, typeColor, icon) = when (item.item_type) {
                    "note" -> Triple("ノート", R.color.primary, R.drawable.ic_description)
                    "code_snippet" -> Triple("コード", R.color.info, R.drawable.ic_computer)
                    "exercise" -> Triple("演習", R.color.success, R.drawable.ic_checklist)
                    "resource_link" -> Triple("リンク", R.color.warning, R.drawable.ic_link)
                    else -> Triple("その他", R.color.text_muted, R.drawable.ic_book)
                }

                ivTypeIcon.setImageResource(icon)
                chipType.text = typeText
                chipType.setChipBackgroundColorResource(typeColor)

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
                        tvCodeContent.text = item.content ?: ""

                        btnCopyCode.setOnClickListener {
                            copyToClipboard(it.context, item.content ?: "")
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
                            } else {
                                answerContainer.visibility = View.VISIBLE
                                btnShowAnswer.text = "答えを隠す"
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
