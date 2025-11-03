package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.databinding.ItemTaskTemplateBinding
import ecccomp.s2240788.mobile_android.data.models.TaskTemplate

/**
 * Adapter for displaying task templates
 */
class TaskTemplateAdapter : ListAdapter<TaskTemplate, TaskTemplateAdapter.TaskViewHolder>(TaskDiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): TaskViewHolder {
        val binding = ItemTaskTemplateBinding.inflate(
            LayoutInflater.from(parent.context),
            parent,
            false
        )
        return TaskViewHolder(binding)
    }

    override fun onBindViewHolder(holder: TaskViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    class TaskViewHolder(
        private val binding: ItemTaskTemplateBinding
    ) : RecyclerView.ViewHolder(binding.root) {

        fun bind(task: TaskTemplate) {
            binding.apply {
                // Title
                tvTitle.text = task.title
                
                // Description
                if (!task.description.isNullOrEmpty()) {
                    tvDescription.text = task.description
                    tvDescription.visibility = View.VISIBLE
                } else {
                    tvDescription.visibility = View.GONE
                }
                
                // Duration
                task.estimatedMinutes?.let {
                    val hours = it / 60
                    val minutes = it % 60
                    tvDuration.text = if (hours > 0) {
                        "${hours}時間${minutes}分"
                    } else {
                        "${minutes}分"
                    }
                    tvDuration.visibility = View.VISIBLE
                } ?: run {
                    tvDuration.visibility = View.GONE
                }
                
                // Priority indicator
                val priorityColor = when (task.priority) {
                    5 -> android.graphics.Color.parseColor("#F44336") // High
                    4 -> android.graphics.Color.parseColor("#FF9800") // Medium-High
                    3 -> android.graphics.Color.parseColor("#FFC107") // Medium
                    2 -> android.graphics.Color.parseColor("#4CAF50") // Low
                    else -> android.graphics.Color.parseColor("#9E9E9E") // Very Low
                }
                viewPriority.setBackgroundColor(priorityColor)
                
                // Resources
                if (!task.resources.isNullOrEmpty()) {
                    tvResources.text = "📚 ${task.resources.size}個のリソース"
                    tvResources.visibility = View.VISIBLE
                } else {
                    tvResources.visibility = View.GONE
                }
            }
        }
    }

    class TaskDiffCallback : DiffUtil.ItemCallback<TaskTemplate>() {
        override fun areItemsTheSame(oldItem: TaskTemplate, newItem: TaskTemplate): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(oldItem: TaskTemplate, newItem: TaskTemplate): Boolean {
            return oldItem == newItem
        }
    }
}

