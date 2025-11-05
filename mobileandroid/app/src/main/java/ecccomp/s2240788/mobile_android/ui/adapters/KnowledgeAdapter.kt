package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.google.android.material.chip.Chip
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ItemKnowledgeCardBinding
import ecccomp.s2240788.mobile_android.data.models.KnowledgeItem
import java.text.SimpleDateFormat
import java.util.Locale

class KnowledgeAdapter(
    private val onItemClick: (KnowledgeItem) -> Unit = {},
    private val onFavoriteClick: (KnowledgeItem) -> Unit = {},
    private val onMenuClick: (KnowledgeItem) -> Unit = {}
) : ListAdapter<KnowledgeItem, KnowledgeAdapter.ViewHolder>(KnowledgeDiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemKnowledgeCardBinding.inflate(
            LayoutInflater.from(parent.context),
            parent,
            false
        )
        return ViewHolder(binding, onItemClick, onFavoriteClick, onMenuClick)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    class ViewHolder(
        private val binding: ItemKnowledgeCardBinding,
        private val onItemClick: (KnowledgeItem) -> Unit,
        private val onFavoriteClick: (KnowledgeItem) -> Unit,
        private val onMenuClick: (KnowledgeItem) -> Unit
    ) : RecyclerView.ViewHolder(binding.root) {

        fun bind(item: KnowledgeItem) {
            binding.apply {
                // Title
                tvTitle.text = item.title

                // Item type chip
                val typeText = when (item.item_type) {
                    "note" -> "ノート"
                    "code_snippet" -> "コード"
                    "exercise" -> "演習"
                    "resource_link" -> "リンク"
                    "attachment" -> "添付"
                    else -> item.item_type
                }
                chipType.text = typeText

                // Language chip (only for code snippets)
                if (item.item_type == "code_snippet" && !item.code_language.isNullOrEmpty()) {
                    chipLanguage.text = item.code_language
                    chipLanguage.visibility = View.VISIBLE
                } else {
                    chipLanguage.visibility = View.GONE
                }

                // Content preview
                val previewText = when (item.item_type) {
                    "note", "code_snippet" -> item.content
                    "exercise" -> item.question
                    "resource_link" -> item.url
                    else -> item.content
                }

                if (!previewText.isNullOrEmpty()) {
                    tvPreview.text = previewText
                    tvPreview.visibility = View.VISIBLE
                } else {
                    tvPreview.visibility = View.GONE
                }

                // Tags
                chipGroupTags.removeAllViews()
                if (!item.tags.isNullOrEmpty()) {
                    item.tags.take(3).forEach { tag ->
                        val chip = Chip(binding.root.context).apply {
                            text = tag
                            setChipBackgroundColorResource(R.color.primary_light)
                            setTextColor(binding.root.context.getColor(R.color.primary))
                            isClickable = false
                            isCheckable = false
                        }
                        chipGroupTags.addView(chip)
                    }
                    chipGroupTags.visibility = View.VISIBLE
                } else {
                    chipGroupTags.visibility = View.GONE
                }

                // Category
                val categoryName = item.category?.name ?: "未分類"
                tvCategory.text = categoryName

                // Last review date
                if (!item.last_reviewed_at.isNullOrEmpty()) {
                    tvLastReview.text = formatDate(item.last_reviewed_at)
                } else {
                    tvLastReview.text = "未レビュー"
                }

                // Next review date
                if (!item.next_review_date.isNullOrEmpty()) {
                    tvNextReview.text = formatDate(item.next_review_date)
                } else {
                    tvNextReview.text = "-"
                }

                // Favorite button
                val favoriteIcon = if (item.is_favorite) {
                    R.drawable.ic_star_filled
                } else {
                    R.drawable.ic_star
                }
                btnFavorite.setIconResource(favoriteIcon)

                btnFavorite.setOnClickListener {
                    onFavoriteClick(item)
                }

                // Menu button
                btnMenu.setOnClickListener {
                    onMenuClick(item)
                }

                // Card click
                root.setOnClickListener {
                    onItemClick(item)
                }
            }
        }

        private fun formatDate(dateString: String): String {
            return try {
                val inputFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
                val outputFormat = SimpleDateFormat("MM/dd", Locale.JAPAN)
                val date = inputFormat.parse(dateString)
                date?.let { outputFormat.format(it) } ?: dateString
            } catch (e: Exception) {
                dateString
            }
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
