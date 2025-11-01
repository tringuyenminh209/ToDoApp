package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.Task
import ecccomp.s2240788.mobile_android.databinding.ItemStartTaskBinding
import java.text.SimpleDateFormat
import java.util.*

/**
 * StartTaskAdapter
 * dialog_start_task.xml内のタスクリスト用Adapter
 */
class StartTaskAdapter(
    private val onTaskClick: (Task) -> Unit,
    private val onStartClick: (Task) -> Unit
) : ListAdapter<Task, StartTaskAdapter.TaskViewHolder>(TaskDiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): TaskViewHolder {
        val binding = ItemStartTaskBinding.inflate(
            LayoutInflater.from(parent.context),
            parent,
            false
        )
        return TaskViewHolder(binding)
    }

    override fun onBindViewHolder(holder: TaskViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    inner class TaskViewHolder(
        private val binding: ItemStartTaskBinding
    ) : RecyclerView.ViewHolder(binding.root) {

        fun bind(task: Task) {
            binding.apply {
                // Set task title
                tvTaskTitle.text = task.title

                // Set priority indicator color and badge
                val (priorityColor, priorityBgColor, priorityText) = when (task.priority) {
                    5 -> Triple(R.color.error, R.color.error_light, R.string.priority_high)
                    4 -> Triple(R.color.warning, R.color.warning_light, R.string.priority_medium)
                    else -> Triple(R.color.success, R.color.success_light, R.string.priority_low)
                }

                priorityIndicator.setBackgroundColor(
                    ContextCompat.getColor(itemView.context, priorityColor)
                )
                priorityBadge.setCardBackgroundColor(
                    ContextCompat.getColor(itemView.context, priorityBgColor)
                )
                tvPriority.text = itemView.context.getString(priorityText)
                tvPriority.setTextColor(
                    ContextCompat.getColor(itemView.context, priorityColor)
                )

                // Set estimated time
                if (task.estimated_minutes != null && task.estimated_minutes > 0) {
                    timeContainer.visibility = View.VISIBLE
                    val hours = task.estimated_minutes / 60
                    val minutes = task.estimated_minutes % 60
                    tvEstimatedTime.text = if (hours > 0) {
                        "${hours}h ${minutes}m"
                    } else {
                        "${minutes}m"
                    }
                } else {
                    timeContainer.visibility = View.GONE
                }

                // Set energy level
                if (task.energy_level != null) {
                    energyContainer.visibility = View.VISIBLE
                    val energyText = when (task.energy_level.lowercase()) {
                        "high" -> R.string.energy_high
                        "medium" -> R.string.energy_medium
                        "low" -> R.string.energy_low
                        else -> R.string.energy_medium
                    }
                    tvEnergyLevel.text = itemView.context.getString(energyText)
                } else {
                    energyContainer.visibility = View.GONE
                }

                // Set category (if available)
                // TODO: Add category field to Task model
                tvCategory.text = "学習" // Placeholder

                // Set due date
                if (task.deadline != null) {
                    dueDateBadge.visibility = View.VISIBLE
                    tvDueDate.text = formatDueDate(task.deadline)
                } else {
                    dueDateBadge.visibility = View.GONE
                }

                // Set subtasks progress (if available)
                // TODO: Load subtasks data
                subtasksContainer.visibility = View.GONE

                // Click listeners
                root.setOnClickListener {
                    onTaskClick(task)
                }

                btnStart.setOnClickListener {
                    onStartClick(task)
                }
            }
        }

        private fun formatDueDate(dueDate: String): String {
            return try {
                val inputFormat = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault())
                val outputFormat = SimpleDateFormat("MM/dd", Locale.getDefault())
                val date = inputFormat.parse(dueDate)
                
                // Check if today
                val today = Calendar.getInstance()
                val dueCalendar = Calendar.getInstance()
                dueCalendar.time = date ?: return dueDate
                
                when {
                    today.get(Calendar.YEAR) == dueCalendar.get(Calendar.YEAR) &&
                    today.get(Calendar.DAY_OF_YEAR) == dueCalendar.get(Calendar.DAY_OF_YEAR) -> {
                        itemView.context.getString(R.string.today)
                    }
                    else -> outputFormat.format(date ?: return dueDate)
                }
            } catch (e: Exception) {
                dueDate
            }
        }
    }

    class TaskDiffCallback : DiffUtil.ItemCallback<Task>() {
        override fun areItemsTheSame(oldItem: Task, newItem: Task): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(oldItem: Task, newItem: Task): Boolean {
            return oldItem == newItem
        }
    }
}

