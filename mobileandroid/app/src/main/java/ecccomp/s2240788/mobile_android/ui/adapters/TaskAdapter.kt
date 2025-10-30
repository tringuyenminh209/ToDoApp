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
import ecccomp.s2240788.mobile_android.databinding.ItemTaskBinding
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
        val binding = ItemTaskBinding.inflate(
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
        private val binding: ItemTaskBinding
    ) : RecyclerView.ViewHolder(binding.root) {

        fun bind(task: Task) {
            binding.apply {
                // タイトル
                tvTaskTitle.text = task.title

                // 説明（nullチェック）
                if (!task.description.isNullOrEmpty()) {
                    tvTaskDescription.text = task.description
                    tvTaskDescription.visibility = View.VISIBLE
                } else {
                    tvTaskDescription.visibility = View.GONE
                }

                // ステータスに応じた表示
                when (task.status) {
                    "completed" -> {
                        tvTaskTitle.paintFlags = tvTaskTitle.paintFlags or Paint.STRIKE_THRU_TEXT_FLAG
                        tvTaskTitle.alpha = 0.6f
                        tvTaskDescription.alpha = 0.6f
                        btnComplete.visibility = View.GONE
                        tvStatusBadge.text = "完了"
                        tvStatusBadge.setBackgroundResource(R.drawable.badge_completed)
                        tvStatusBadge.visibility = View.VISIBLE
                    }
                    "in_progress" -> {
                        tvTaskTitle.paintFlags = tvTaskTitle.paintFlags and Paint.STRIKE_THRU_TEXT_FLAG.inv()
                        tvTaskTitle.alpha = 1.0f
                        tvTaskDescription.alpha = 1.0f
                        btnComplete.visibility = View.VISIBLE
                        tvStatusBadge.text = "進行中"
                        tvStatusBadge.setBackgroundResource(R.drawable.badge_in_progress)
                        tvStatusBadge.visibility = View.VISIBLE
                    }
                    else -> {
                        tvTaskTitle.paintFlags = tvTaskTitle.paintFlags and Paint.STRIKE_THRU_TEXT_FLAG.inv()
                        tvTaskTitle.alpha = 1.0f
                        tvTaskDescription.alpha = 1.0f
                        btnComplete.visibility = View.VISIBLE
                        tvStatusBadge.visibility = View.GONE
                    }
                }

                // 優先度の表示 (1-5 -> low/medium/high)
                when (task.priority) {
                    4, 5 -> {
                        ivPriority.setColorFilter(
                            ContextCompat.getColor(root.context, R.color.priority_high)
                        )
                        ivPriority.visibility = View.VISIBLE
                    }
                    3 -> {
                        ivPriority.setColorFilter(
                            ContextCompat.getColor(root.context, R.color.priority_medium)
                        )
                        ivPriority.visibility = View.VISIBLE
                    }
                    1, 2 -> {
                        ivPriority.setColorFilter(
                            ContextCompat.getColor(root.context, R.color.priority_low)
                        )
                        ivPriority.visibility = View.VISIBLE
                    }
                    else -> {
                        ivPriority.visibility = View.GONE
                    }
                }

                // 期限の表示 (deadline instead of due_date)
                if (!task.deadline.isNullOrEmpty()) {
                    try {
                        val inputFormat = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault())
                        val outputFormat = SimpleDateFormat("MM/dd", Locale.getDefault())
                        val date = inputFormat.parse(task.deadline)
                        tvDueDate.text = "〆 ${outputFormat.format(date!!)}"
                        tvDueDate.visibility = View.VISIBLE

                        // 期限切れチェック
                        if (date.before(Date()) && task.status != "completed") {
                            tvDueDate.setTextColor(
                                ContextCompat.getColor(root.context, R.color.error)
                            )
                        } else {
                            tvDueDate.setTextColor(
                                ContextCompat.getColor(root.context, R.color.text_secondary)
                            )
                        }
                    } catch (e: Exception) {
                        tvDueDate.visibility = View.GONE
                    }
                } else {
                    tvDueDate.visibility = View.GONE
                }

                // クリックリスナー
                root.setOnClickListener {
                    onTaskClick(task)
                }

                // 完了ボタン
                btnComplete.setOnClickListener {
                    onTaskComplete(task)
                }

                // 削除ボタン
                btnDelete.setOnClickListener {
                    onTaskDelete(task)
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

