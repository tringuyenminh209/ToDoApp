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
import ecccomp.s2240788.mobile_android.data.models.Subtask
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
    private val onSubtaskStartClick: (Task, Subtask) -> Unit,
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
                
                // Apply strikethrough for completed tasks
                if (task.status == "completed") {
                    tvTaskTitle.paintFlags = tvTaskTitle.paintFlags or android.graphics.Paint.STRIKE_THRU_TEXT_FLAG
                    tvTaskTitle.alpha = 0.6f
                } else {
                    tvTaskTitle.paintFlags = tvTaskTitle.paintFlags and android.graphics.Paint.STRIKE_THRU_TEXT_FLAG.inv()
                    tvTaskTitle.alpha = 1.0f
                }

                // Status Badge
                when (task.status) {
                    "completed" -> {
                        statusBadge.visibility = View.VISIBLE
                        statusBadge.setCardBackgroundColor(ContextCompat.getColor(itemView.context, R.color.success_light))
                        ivStatusIcon.setImageResource(R.drawable.ic_check)
                        ivStatusIcon.setColorFilter(ContextCompat.getColor(itemView.context, R.color.success))
                        tvStatus.text = itemView.context.getString(R.string.status_completed)
                        tvStatus.setTextColor(ContextCompat.getColor(itemView.context, R.color.success))
                    }
                    "in_progress" -> {
                        statusBadge.visibility = View.VISIBLE
                        statusBadge.setCardBackgroundColor(ContextCompat.getColor(itemView.context, R.color.warning_light))
                        ivStatusIcon.setImageResource(R.drawable.ic_play)
                        ivStatusIcon.setColorFilter(ContextCompat.getColor(itemView.context, R.color.warning))
                        tvStatus.text = itemView.context.getString(R.string.status_in_progress)
                        tvStatus.setTextColor(ContextCompat.getColor(itemView.context, R.color.warning))
                    }
                    else -> {
                        statusBadge.visibility = View.GONE
                    }
                }

                // Category Badge
                if (!task.category.isNullOrEmpty()) {
                    categoryBadge.visibility = View.VISIBLE
                    val (categoryText, categoryColor, categoryBgColor) = when (task.category.lowercase()) {
                        "study", "learning", "学習" -> Triple(R.string.category_learning, R.color.primary, R.color.primary_light)
                        "work", "仕事" -> Triple(R.string.category_work, R.color.info, R.color.info_light)
                        "personal", "個人" -> Triple(R.string.category_personal, R.color.accent, R.color.accent_light)
                        "project", "プロジェクト" -> Triple(R.string.category_project, R.color.warning, R.color.warning_light)
                        "other", "その他" -> Triple(R.string.category_other, R.color.text_muted, R.color.surface)
                        else -> Triple(R.string.category_other, R.color.text_muted, R.color.surface)
                    }
                    categoryBadge.setCardBackgroundColor(ContextCompat.getColor(itemView.context, categoryBgColor))
                    tvCategory.text = itemView.context.getString(categoryText)
                    tvCategory.setTextColor(ContextCompat.getColor(itemView.context, categoryColor))
                } else {
                    categoryBadge.visibility = View.GONE
                }

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

                // Progress bar - only show if has subtasks
                if (!task.subtasks.isNullOrEmpty()) {
                    progressBar.visibility = View.VISIBLE
                    tvProgressText.visibility = View.VISIBLE
                    
                    val completedCount = task.subtasks.count { it.is_completed }
                    val totalCount = task.subtasks.size
                    val progress = if (totalCount > 0) (completedCount * 100) / totalCount else 0
                    
                    progressBar.progress = progress
                    tvProgressText.text = "$completedCount/$totalCount 完了"
                } else {
                    progressBar.visibility = View.GONE
                    tvProgressText.visibility = View.GONE
                }

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

                // Scheduled Time - show if available (now TIME type: HH:MM:SS or HH:MM)
                if (!task.scheduled_time.isNullOrEmpty()) {
                    scheduledTimeContainer.visibility = View.VISIBLE
                    try {
                        // Backend returns HH:MM:SS or HH:MM format (time only, no date)
                        val displayFormat = SimpleDateFormat("HH:mm", Locale.getDefault())
                        val apiFormat = SimpleDateFormat("HH:mm:ss", Locale.getDefault())

                        // Parse time string
                        val time = if (task.scheduled_time.count { it == ':' } == 2) {
                            // HH:MM:SS format
                            apiFormat.parse(task.scheduled_time)
                        } else {
                            // HH:MM format
                            displayFormat.parse(task.scheduled_time)
                        }

                        tvScheduledTime.text = if (time != null) displayFormat.format(time) else task.scheduled_time
                    } catch (e: Exception) {
                        // Fallback: just display the time as-is
                        tvScheduledTime.text = task.scheduled_time
                    }
                } else {
                    scheduledTimeContainer.visibility = View.GONE
                }

                // Subtasks - display dynamically
                if (!task.subtasks.isNullOrEmpty()) {
                    subtasksContainer.visibility = View.VISIBLE
                    subtasksContainer.removeAllViews()  // Clear existing views

                    // Show max 3 subtasks
                    task.subtasks.take(3).forEach { subtask ->
                        val subtaskView = android.widget.LinearLayout(itemView.context).apply {
                            orientation = android.widget.LinearLayout.HORIZONTAL
                            layoutParams = android.view.ViewGroup.MarginLayoutParams(
                                android.view.ViewGroup.LayoutParams.MATCH_PARENT,
                                android.view.ViewGroup.LayoutParams.WRAP_CONTENT
                            ).apply {
                                bottomMargin = 8
                            }
                            gravity = android.view.Gravity.CENTER_VERTICAL
                        }

                        // Checkbox/indicator
                        val indicator = com.google.android.material.card.MaterialCardView(itemView.context).apply {
                            layoutParams = android.view.ViewGroup.LayoutParams(40, 40)
                            radius = 20f
                            cardElevation = 0f
                            strokeWidth = if (subtask.is_completed) 0 else 2
                            strokeColor = ContextCompat.getColor(context, R.color.line)
                            setCardBackgroundColor(ContextCompat.getColor(
                                context,
                                if (subtask.is_completed) R.color.success else R.color.surface
                            ))
                        }

                        if (subtask.is_completed) {
                            val checkIcon = android.widget.ImageView(itemView.context).apply {
                                setImageResource(R.drawable.ic_check)
                                setColorFilter(ContextCompat.getColor(context, R.color.white))
                                layoutParams = android.view.ViewGroup.LayoutParams(24, 24)
                            }
                            indicator.addView(checkIcon)
                        }

                        // Subtask title
                        val titleText = android.widget.TextView(itemView.context).apply {
                            text = subtask.title
                            textSize = 13f
                            setTextColor(ContextCompat.getColor(
                                context,
                                if (subtask.is_completed) R.color.text_secondary else R.color.text_muted
                            ))
                            layoutParams = android.widget.LinearLayout.LayoutParams(
                                0,
                                android.view.ViewGroup.LayoutParams.WRAP_CONTENT,
                                1f
                            ).apply {
                                marginStart = 16
                            }
                        }

                        // Time badge (if exists)
                        if (subtask.estimated_minutes != null && subtask.estimated_minutes > 0) {
                            val timeText = android.widget.TextView(itemView.context).apply {
                                text = "${subtask.estimated_minutes}分"
                                textSize = 11f
                                setTextColor(ContextCompat.getColor(context, R.color.text_muted))
                                setPadding(16, 4, 16, 4)
                                setBackgroundResource(R.drawable.time_badge_background)
                            }
                            subtaskView.addView(indicator)
                            subtaskView.addView(titleText)
                            subtaskView.addView(timeText)
                        } else {
                            subtaskView.addView(indicator)
                            subtaskView.addView(titleText)
                        }

                        // Start button for uncompleted subtasks
                        if (!subtask.is_completed) {
                            val startButton = com.google.android.material.button.MaterialButton(
                                itemView.context,
                                null,
                                com.google.android.material.R.attr.materialIconButtonStyle
                            ).apply {
                                setIconResource(R.drawable.ic_play)
                                iconTint = ContextCompat.getColorStateList(context, R.color.primary)
                                iconGravity = com.google.android.material.button.MaterialButton.ICON_GRAVITY_TEXT_START
                                layoutParams = android.widget.LinearLayout.LayoutParams(
                                    android.view.ViewGroup.LayoutParams.WRAP_CONTENT,
                                    android.view.ViewGroup.LayoutParams.WRAP_CONTENT
                                ).apply {
                                    marginStart = 8
                                }
                                setOnClickListener {
                                    onSubtaskStartClick(task, subtask)
                                }
                            }
                            subtaskView.addView(startButton)
                        }

                        subtasksContainer.addView(subtaskView)
                    }
                } else {
                    subtasksContainer.visibility = View.GONE
                }

                // Start button - hide if completed or in_progress
                if (task.status == "completed") {
                    btnStart.visibility = View.GONE
                } else if (task.status == "in_progress") {
                    btnStart.visibility = View.VISIBLE
                    btnStart.text = itemView.context.getString(R.string.task_continue)
                    btnStart.setIconResource(R.drawable.ic_play)
                    btnStart.setOnClickListener {
                        onStartClick(task)
                    }
                } else {
                    btnStart.visibility = View.VISIBLE
                    btnStart.text = itemView.context.getString(R.string.task_start)
                    btnStart.setIconResource(R.drawable.ic_play)
                    btnStart.setOnClickListener {
                        onStartClick(task)
                    }
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
