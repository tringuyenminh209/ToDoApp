package ecccomp.s2240788.mobile_android.ui.adapters

import android.graphics.Color
import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ItemTemplateCardBinding
import ecccomp.s2240788.mobile_android.data.models.LearningPathTemplate
import ecccomp.s2240788.mobile_android.data.models.getFormattedDuration
import ecccomp.s2240788.mobile_android.data.models.getTotalMilestones
import ecccomp.s2240788.mobile_android.data.models.getTotalTasks

/**
 * Adapter for displaying learning path templates in RecyclerView
 */
class TemplateListAdapter(
    private val onTemplateClick: (LearningPathTemplate) -> Unit
) : ListAdapter<LearningPathTemplate, TemplateListAdapter.TemplateViewHolder>(TemplateDiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): TemplateViewHolder {
        val binding = ItemTemplateCardBinding.inflate(
            LayoutInflater.from(parent.context),
            parent,
            false
        )
        return TemplateViewHolder(binding, onTemplateClick)
    }

    override fun onBindViewHolder(holder: TemplateViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    class TemplateViewHolder(
        private val binding: ItemTemplateCardBinding,
        private val onTemplateClick: (LearningPathTemplate) -> Unit
    ) : RecyclerView.ViewHolder(binding.root) {

        fun bind(template: LearningPathTemplate) {
            binding.apply {
                // Icon - Load drawable from template.icon field
                val iconResId = getIconResId(template.icon)
                tvIcon.setImageResource(iconResId)
                
                // Title
                tvTitle.text = template.title
                
                // Description
                tvDescription.text = template.description ?: ""
                
                // Category badge
                tvCategory.text = template.category.displayName
                
                // Difficulty badge
                tvDifficulty.text = template.difficulty.displayName
                tvDifficulty.setTextColor(Color.parseColor(template.difficulty.color))
                
                // Stats
                tvDuration.text = template.getFormattedDuration()
                tvMilestones.text = "${template.getTotalMilestones()}マイルストーン"
                tvTasks.text = "${template.getTotalTasks()}タスク"
                
                // Usage count
                if (template.usageCount > 0) {
                    tvUsageCount.text = "${template.usageCount}人が使用中"
                } else {
                    tvUsageCount.text = "新着"
                }
                
                // Featured badge
                if (template.isFeatured) {
                    badgeFeatured.visibility = android.view.View.VISIBLE
                } else {
                    badgeFeatured.visibility = android.view.View.GONE
                }
                
                // Card color accent
                try {
                    cardView.strokeColor = Color.parseColor(template.color)
                } catch (e: Exception) {
                    // Use default color if parsing fails
                }
                
                // Click listener
                root.setOnClickListener {
                    onTemplateClick(template)
                }
            }
        }

        private fun getIconResId(iconName: String?): Int {
            if (iconName.isNullOrEmpty()) {
                return R.drawable.ic_computer // Default icon
            }
            
            val context = binding.root.context
            val resId = context.resources.getIdentifier(
                iconName,
                "drawable",
                context.packageName
            )
            
            return if (resId != 0) {
                resId
            } else {
                R.drawable.ic_computer // Fallback to default icon
            }
        }
    }

    class TemplateDiffCallback : DiffUtil.ItemCallback<LearningPathTemplate>() {
        override fun areItemsTheSame(
            oldItem: LearningPathTemplate,
            newItem: LearningPathTemplate
        ): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(
            oldItem: LearningPathTemplate,
            newItem: LearningPathTemplate
        ): Boolean {
            return oldItem == newItem
        }
    }
}

