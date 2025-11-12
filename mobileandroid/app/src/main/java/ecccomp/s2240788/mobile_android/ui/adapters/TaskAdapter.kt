package ecccomp.s2240788.mobile_android.ui.adapters

import android.graphics.Paint
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.Task
import ecccomp.s2240788.mobile_android.databinding.ItemTaskCardBinding
import java.text.SimpleDateFormat
import java.util.*

/**
 * TaskAdapter
 * タスクリスト表示用RecyclerViewアダプター
 */
class TaskAdapter(
    private val onTaskClick: (Task) -> Unit,
    private val onTaskComplete: (Task) -> Unit,
    private val onTaskDelete: (Task) -> Unit
) : ListAdapter<Task, TaskAdapter.TaskViewHolder>(TaskDiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): TaskViewHolder {
        val binding = ItemTaskCardBinding.inflate(
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
        private val binding: ItemTaskCardBinding
    ) : RecyclerView.ViewHolder(binding.root) {

        fun bind(task: Task) {
            binding.apply {
                // Title
                tvTaskTitle.text = task.title

                // Priority color indicator
                val priorityColor = when (task.priority) {
                    5 -> R.color.error
                    4 -> R.color.warning
                    3 -> R.color.warning
                    2 -> R.color.success
                    else -> R.color.success
                }
                priorityIndicator.setCardBackgroundColor(
                    ContextCompat.getColor(itemView.context, priorityColor)
                )

                // Progress bar - calculate from subtasks if available
                val subtasks = task.subtasks
                if (!subtasks.isNullOrEmpty()) {
                    val completedCount = subtasks.count { it.is_completed }
                    val totalCount = subtasks.size
                    val progress = (completedCount * 100) / totalCount

                    progressBar.visibility = View.VISIBLE
                    progressBar.progress = progress

                    tvProgressText.visibility = View.VISIBLE
                    tvProgressText.text = "$completedCount/$totalCount 完了"
                } else {
                    // Simple progress based on status
                    val progress = when (task.status) {
                        "completed" -> 100
                        "in_progress" -> 50
                        else -> 0
                    }

                    if (progress > 0) {
                        progressBar.visibility = View.VISIBLE
                        progressBar.progress = progress

                        tvProgressText.visibility = View.VISIBLE
                        val progressText = when (task.status) {
                            "completed" -> "完了"
                            "in_progress" -> "進行中"
                            else -> "未着手"
                        }
                        tvProgressText.text = progressText
                    } else {
                        progressBar.visibility = View.GONE
                        tvProgressText.visibility = View.GONE
                    }
                }

                // Estimated time
                if (task.estimated_minutes != null && task.estimated_minutes > 0) {
                    timeContainer.visibility = View.VISIBLE
                    tvTaskTime.text = "${task.estimated_minutes}分"
                } else {
                    timeContainer.visibility = View.GONE
                }

                // Category badge
                if (!task.category.isNullOrEmpty()) {
                    categoryBadge.visibility = View.VISIBLE
                    val categoryText = when (task.category) {
                        "study" -> "学習"
                        "work" -> "仕事"
                        "personal" -> "個人"
                        "other" -> "その他"
                        else -> task.category
                    }
                    tvCategory.text = categoryText

                    // Category color
                    val (bgColor, textColor) = when (task.category) {
                        "study" -> Pair(R.color.primary_light, R.color.primary)
                        "work" -> Pair(R.color.warning_light, R.color.warning)
                        "personal" -> Pair(R.color.success_light, R.color.success)
                        else -> Pair(R.color.surface, R.color.text_muted)
                    }
                    categoryBadge.setCardBackgroundColor(
                        ContextCompat.getColor(itemView.context, bgColor)
                    )
                    tvCategory.setTextColor(
                        ContextCompat.getColor(itemView.context, textColor)
                    )
                } else {
                    categoryBadge.visibility = View.GONE
                }

                // Deadline
                if (!task.deadline.isNullOrEmpty()) {
                    dateContainer.visibility = View.VISIBLE
                    try {
                        val inputFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
                        val outputFormat = SimpleDateFormat("MM/dd", Locale.getDefault())
                        val date = inputFormat.parse(task.deadline)
                        tvTaskDate.text = if (date != null) outputFormat.format(date) else task.deadline
                    } catch (e: Exception) {
                        tvTaskDate.text = task.deadline
                    }
                } else {
                    dateContainer.visibility = View.GONE
                }

                // Scheduled Time
                if (!task.scheduled_time.isNullOrEmpty()) {
                    scheduledTimeContainer.visibility = View.VISIBLE
                    try {
                        val inputFormat = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault())
                        val outputFormat = SimpleDateFormat("HH:mm", Locale.getDefault())
                        val datetime = inputFormat.parse(task.scheduled_time)
                        tvScheduledTime.text = if (datetime != null) outputFormat.format(datetime) else task.scheduled_time
                    } catch (e: Exception) {
                        // Try alternative format (HH:mm only)
                        try {
                            val inputFormat2 = SimpleDateFormat("HH:mm", Locale.getDefault())
                            val time = inputFormat2.parse(task.scheduled_time)
                            if (time != null) {
                                tvScheduledTime.text = task.scheduled_time
                            } else {
                                scheduledTimeContainer.visibility = View.GONE
                            }
                        } catch (e2: Exception) {
                            scheduledTimeContainer.visibility = View.GONE
                        }
                    }
                } else {
                    scheduledTimeContainer.visibility = View.GONE
                }

                // Subtasks - display compact (up to 3)
                if (!task.subtasks.isNullOrEmpty()) {
                    subtasksContainer.visibility = View.VISIBLE
                    subtasksContainer.removeAllViews()

                    val inflater = LayoutInflater.from(itemView.context)
                    task.subtasks.take(3).forEach { subtask ->
                        val row = inflater.inflate(R.layout.item_subtask_mini, subtasksContainer, false)
                        val indicator = row.findViewById<com.google.android.material.card.MaterialCardView>(R.id.indicator)
                        val check = row.findViewById<android.widget.ImageView>(R.id.iv_check)
                        val title = row.findViewById<android.widget.TextView>(R.id.tv_subtask_title)
                        val time = row.findViewById<android.widget.TextView>(R.id.tv_subtask_time)

                        val completed = subtask.is_completed
                        if (completed) {
                            indicator.strokeWidth = 0
                            indicator.setCardBackgroundColor(ContextCompat.getColor(itemView.context, R.color.success))
                            check.visibility = View.VISIBLE
                            title.setTextColor(ContextCompat.getColor(itemView.context, R.color.text_secondary))
                        } else {
                            indicator.strokeWidth = 1
                            indicator.setCardBackgroundColor(ContextCompat.getColor(itemView.context, R.color.surface))
                            indicator.setStrokeColor(ContextCompat.getColor(itemView.context, R.color.line))
                            check.visibility = View.GONE
                            title.setTextColor(ContextCompat.getColor(itemView.context, R.color.text_muted))
                        }

                        title.text = subtask.title
                        val minutes = subtask.estimated_minutes
                        if (minutes != null && minutes > 0) {
                            time.visibility = View.VISIBLE
                            time.text = "${minutes}p"
                        } else {
                            time.visibility = View.GONE
                        }

                        subtasksContainer.addView(row)
                    }
                } else {
                    subtasksContainer.visibility = View.GONE
                }

                // Start button
                btnStart.setOnClickListener {
                    onTaskComplete(task)  // Using onTaskComplete callback for start action
                }

                // More button
                btnMore.setOnClickListener {
                    onTaskDelete(task)  // Using onTaskDelete callback for more options
                }

                // Card click
                root.setOnClickListener {
                    onTaskClick(task)
                }
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

