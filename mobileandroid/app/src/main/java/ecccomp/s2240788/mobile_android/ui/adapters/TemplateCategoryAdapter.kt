package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.databinding.ItemTemplateCategoryBinding
import ecccomp.s2240788.mobile_android.data.models.TemplateCategory
import ecccomp.s2240788.mobile_android.data.models.TemplateCategoryCount

/**
 * Adapter for displaying template categories in grid
 */
class TemplateCategoryAdapter(
    private val onCategoryClick: (TemplateCategory) -> Unit
) : ListAdapter<TemplateCategoryCount, TemplateCategoryAdapter.CategoryViewHolder>(CategoryDiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): CategoryViewHolder {
        val binding = ItemTemplateCategoryBinding.inflate(
            LayoutInflater.from(parent.context),
            parent,
            false
        )
        return CategoryViewHolder(binding, onCategoryClick)
    }

    override fun onBindViewHolder(holder: CategoryViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    class CategoryViewHolder(
        private val binding: ItemTemplateCategoryBinding,
        private val onCategoryClick: (TemplateCategory) -> Unit
    ) : RecyclerView.ViewHolder(binding.root) {

        fun bind(categoryCount: TemplateCategoryCount) {
            val category = TemplateCategory.fromValue(categoryCount.category)
            
            binding.apply {
                // Icon
                tvIcon.text = getCategoryIcon(category)
                
                // Name
                tvCategoryName.text = category.displayName
                
                // Count badge
                tvCount.text = "${categoryCount.count}個"
                
                // Click listener
                root.setOnClickListener {
                    onCategoryClick(category)
                }
            }
        }

        private fun getCategoryIcon(category: TemplateCategory): String {
            return when (category) {
                TemplateCategory.PROGRAMMING -> "💻"
                TemplateCategory.DESIGN -> "🎨"
                TemplateCategory.BUSINESS -> "💼"
                TemplateCategory.LANGUAGE -> "🗣️"
                TemplateCategory.DATA_SCIENCE -> "📊"
                TemplateCategory.OTHER -> "📚"
            }
        }
    }

    class CategoryDiffCallback : DiffUtil.ItemCallback<TemplateCategoryCount>() {
        override fun areItemsTheSame(
            oldItem: TemplateCategoryCount,
            newItem: TemplateCategoryCount
        ): Boolean {
            return oldItem.category == newItem.category
        }

        override fun areContentsTheSame(
            oldItem: TemplateCategoryCount,
            newItem: TemplateCategoryCount
        ): Boolean {
            return oldItem == newItem
        }
    }
}

