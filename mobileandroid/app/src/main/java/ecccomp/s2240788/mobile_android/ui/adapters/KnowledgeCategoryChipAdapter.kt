package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.data.models.KnowledgeCategory
import ecccomp.s2240788.mobile_android.databinding.ItemCategoryChipBinding

class KnowledgeCategoryChipAdapter(
    private val onCategoryClick: (KnowledgeCategory) -> Unit
) : ListAdapter<KnowledgeCategory, KnowledgeCategoryChipAdapter.CategoryViewHolder>(CategoryDiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): CategoryViewHolder {
        val binding = ItemCategoryChipBinding.inflate(
            LayoutInflater.from(parent.context),
            parent,
            false
        )
        return CategoryViewHolder(binding)
    }

    override fun onBindViewHolder(holder: CategoryViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    inner class CategoryViewHolder(
        private val binding: ItemCategoryChipBinding
    ) : RecyclerView.ViewHolder(binding.root) {

        fun bind(category: KnowledgeCategory) {
            binding.chipCategory.text = category.name
            binding.chipCategory.setOnClickListener {
                onCategoryClick(category)
            }
        }
    }

    private class CategoryDiffCallback : DiffUtil.ItemCallback<KnowledgeCategory>() {
        override fun areItemsTheSame(oldItem: KnowledgeCategory, newItem: KnowledgeCategory): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(oldItem: KnowledgeCategory, newItem: KnowledgeCategory): Boolean {
            return oldItem == newItem
        }
    }
}
