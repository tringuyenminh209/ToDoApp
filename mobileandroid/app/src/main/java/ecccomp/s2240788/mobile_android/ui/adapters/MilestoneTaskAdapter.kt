package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Toast
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ItemMilestoneTaskBinding
import ecccomp.s2240788.mobile_android.data.models.Task
import com.google.android.material.card.MaterialCardView

/**
 * Adapter for displaying tasks within a milestone
 */
class MilestoneTaskAdapter(
    private val onStartTask: (Task) -> Unit = {},
    private val onStartSubtask: (Task, Int) -> Unit = { _, _ -> }
) : ListAdapter<Task, MilestoneTaskAdapter.TaskViewHolder>(TaskDiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): TaskViewHolder {
        val binding = ItemMilestoneTaskBinding.inflate(
            LayoutInflater.from(parent.context),
            parent,
            false
        )
        return TaskViewHolder(binding, onStartTask, onStartSubtask)
    }

    override fun onBindViewHolder(holder: TaskViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    class TaskViewHolder(
        private val binding: ItemMilestoneTaskBinding,
        private val onStartTask: (Task) -> Unit,
        private val onStartSubtask: (Task, Int) -> Unit
    ) : RecyclerView.ViewHolder(binding.root) {

        private var isExpanded = false

        fun bind(task: Task) {
            binding.apply {
                // Task title
                tvTaskTitle.text = task.title

                // Description
                if (!task.description.isNullOrEmpty()) {
                    tvTaskDescription.text = task.description
                    tvTaskDescription.visibility = View.VISIBLE
                } else {
                    tvTaskDescription.visibility = View.GONE
                }

                // Estimated time
                task.estimated_minutes?.let { minutes ->
                    val hours = minutes / 60
                    val mins = minutes % 60

                    val timeText = when {
                        hours > 0 && mins > 0 -> "${hours}時間${mins}分"
                        hours > 0 -> "${hours}時間"
                        else -> "${mins}分"
                    }

                    tvEstimatedTime.text = timeText
                    tvEstimatedTime.visibility = View.VISIBLE
                } ?: run {
                    tvEstimatedTime.visibility = View.GONE
                }

                // Priority badge
                val priorityText = when (task.priority) {
                    5 -> "最優先"
                    4 -> "高"
                    3 -> "中"
                    2 -> "低"
                    else -> "最低"
                }
                tvPriority.text = priorityText

                // Priority color
                val priorityColor = when (task.priority) {
                    5 -> root.context.getColor(R.color.error)
                    4 -> root.context.getColor(R.color.warning)
                    3 -> root.context.getColor(R.color.primary)
                    else -> root.context.getColor(R.color.text_muted)
                }
                tvPriority.setTextColor(priorityColor)

                // Status
                val isCompleted = task.status == "completed"
                if (isCompleted) {
                    ivCheckbox.setImageResource(R.drawable.ic_check)
                    ivCheckbox.setColorFilter(root.context.getColor(R.color.success))
                    tvTaskTitle.alpha = 0.6f
                } else {
                    ivCheckbox.setImageResource(R.drawable.ic_circle_outline)
                    ivCheckbox.setColorFilter(root.context.getColor(R.color.text_muted))
                    tvTaskTitle.alpha = 1.0f
                }

                // Knowledge Items
                val knowledgeItems = task.knowledge_items
                if (!knowledgeItems.isNullOrEmpty()) {
                    btnKnowledge.visibility = View.VISIBLE
                    val knowledgeCount = knowledgeItems.size
                    btnKnowledge.text = "学習内容 ($knowledgeCount)"

                    btnKnowledge.setOnClickListener {
                        val context = binding.root.context
                        val intent = android.content.Intent(context, ecccomp.s2240788.mobile_android.ui.activities.KnowledgeActivity::class.java)
                        intent.putExtra("TASK_ID", task.id)
                        intent.putExtra("TASK_TITLE", task.title)
                        context.startActivity(intent)
                    }
                } else {
                    btnKnowledge.visibility = View.GONE
                }

                // Subtasks
                val subtasks = task.subtasks
                if (!subtasks.isNullOrEmpty()) {
                    btnExpandSubtasks.visibility = View.VISIBLE
                    val subtaskCount = subtasks.size
                    val completedCount = subtasks.count { it.is_completed }
                    btnExpandSubtasks.text = "サブタスク ($completedCount/$subtaskCount)"

                    // Expand/collapse click
                    btnExpandSubtasks.setOnClickListener {
                        isExpanded = !isExpanded
                        updateSubtasksVisibility(task)
                    }

                    // Initial state
                    isExpanded = false
                    updateSubtasksVisibility(task)
                } else {
                    btnExpandSubtasks.visibility = View.GONE
                    subtasksSection.visibility = View.GONE
                }

                // Start button
                btnStart.setOnClickListener {
                    onStartTask(task)
                }
            }
        }

        private fun updateSubtasksVisibility(task: Task) {
            binding.apply {
                if (isExpanded) {
                    subtasksSection.visibility = View.VISIBLE
                    btnExpandSubtasks.setIconResource(R.drawable.ic_expand_less)
                    populateSubtasks(task)
                } else {
                    subtasksSection.visibility = View.GONE
                    btnExpandSubtasks.setIconResource(R.drawable.ic_expand_more)
                }
            }
        }

        private fun populateSubtasks(task: Task) {
            binding.subtasksContainer.removeAllViews()

            task.subtasks?.forEachIndexed { index, subtask ->
                val inflater = LayoutInflater.from(binding.root.context)
                val subtaskView = inflater.inflate(R.layout.item_subtask_detail, binding.subtasksContainer, false)

                // Checkbox
                val checkboxIndicator = subtaskView.findViewById<MaterialCardView>(R.id.checkbox_indicator)
                val ivCheck = subtaskView.findViewById<android.widget.ImageView>(R.id.iv_check)

                if (subtask.is_completed) {
                    checkboxIndicator.setCardBackgroundColor(
                        ContextCompat.getColor(binding.root.context, R.color.success)
                    )
                    checkboxIndicator.strokeWidth = 0
                    ivCheck.visibility = View.VISIBLE
                } else {
                    checkboxIndicator.setCardBackgroundColor(
                        ContextCompat.getColor(binding.root.context, R.color.surface)
                    )
                    checkboxIndicator.strokeWidth = 2 // 1dp
                    ivCheck.visibility = View.GONE
                }

                // Title
                val tvSubtaskTitle = subtaskView.findViewById<android.widget.TextView>(R.id.tv_subtask_title)
                tvSubtaskTitle.text = subtask.title

                // Time
                val tvSubtaskTime = subtaskView.findViewById<android.widget.TextView>(R.id.tv_subtask_time)
                subtask.estimated_minutes?.let { minutes ->
                    tvSubtaskTime.text = "${minutes}分"
                    tvSubtaskTime.visibility = View.VISIBLE
                } ?: run {
                    tvSubtaskTime.visibility = View.GONE
                }

                // Start button
                val btnStartSubtask = subtaskView.findViewById<com.google.android.material.button.MaterialButton>(R.id.btn_start_subtask)
                btnStartSubtask.setOnClickListener {
                    onStartSubtask(task, index)
                }

                binding.subtasksContainer.addView(subtaskView)
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
