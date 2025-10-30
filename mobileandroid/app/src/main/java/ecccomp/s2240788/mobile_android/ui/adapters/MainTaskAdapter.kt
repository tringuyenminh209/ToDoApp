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
import ecccomp.s2240788.mobile_android.databinding.ItemTaskCardBinding
import java.text.SimpleDateFormat
import java.util.*

/**
 * MainTaskAdapter
 * MainActivity用の簡易タスクリストアダプター
 */
class MainTaskAdapter(
    private val onTaskClick: (Task) -> Unit,
    private val onStartClick: (Task) -> Unit,
    private val onMoreClick: (Task) -> Unit
) : ListAdapter<Task, MainTaskAdapter.TaskViewHolder>(MainTaskDiffCallback()) {

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

                // Priority color
                val priorityColor = when (task.priority) {
                    5 -> R.color.error        // High
                    4 -> R.color.warning      // Medium-high
                    3 -> R.color.warning      // Medium
                    2 -> R.color.success      // Medium-low
                    else -> R.color.success   // Low
                }
                priorityIndicator.setCardBackgroundColor(
                    ContextCompat.getColor(itemView.context, priorityColor)
                )

                // Progress
                val progress = when (task.status) {
                    "completed" -> 100
                    "in_progress" -> 50
                    else -> 0
                }
                progressBar.progress = progress

                // Progress text
                val progressText = when (task.status) {
                    "completed" -> "完了"
                    "in_progress" -> "進行中"
                    else -> "未着手"
                }
                tvProgressText.text = progressText

                // Estimated time
                if (task.estimated_minutes != null && task.estimated_minutes > 0) {
                    timeContainer.visibility = View.VISIBLE
                    tvTaskTime.text = "${task.estimated_minutes}分"
                } else {
                    timeContainer.visibility = View.GONE
                }

                // Deadline
                if (!task.deadline.isNullOrEmpty()) {
                    dateContainer.visibility = View.VISIBLE
                    try {
                        val inputFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
                        val outputFormat = SimpleDateFormat("MM/dd", Locale.getDefault())
                        val date = inputFormat.parse(task.deadline)
                        val formattedDate = if (date != null) outputFormat.format(date) else task.deadline
                        tvTaskDate.text = formattedDate
                    } catch (e: Exception) {
                        tvTaskDate.text = task.deadline
                    }
                } else {
                    dateContainer.visibility = View.GONE
                }

                // Start button - mark task as in_progress
                btnStart.setOnClickListener {
                    onStartClick(task)
                }

                // More button
                btnMore.setOnClickListener {
                    onMoreClick(task)
                }

                // Card click - view task details
                root.setOnClickListener {
                    onTaskClick(task)
                }
            }
        }
    }
}

class MainTaskDiffCallback : DiffUtil.ItemCallback<Task>() {
    override fun areItemsTheSame(oldItem: Task, newItem: Task): Boolean {
        return oldItem.id == newItem.id
    }

    override fun areContentsTheSame(oldItem: Task, newItem: Task): Boolean {
        return oldItem == newItem
    }
}
