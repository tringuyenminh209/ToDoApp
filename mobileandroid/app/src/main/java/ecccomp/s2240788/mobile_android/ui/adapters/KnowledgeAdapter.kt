package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.core.content.ContextCompat
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

                // Title
                tvTitle.text = item.title

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
