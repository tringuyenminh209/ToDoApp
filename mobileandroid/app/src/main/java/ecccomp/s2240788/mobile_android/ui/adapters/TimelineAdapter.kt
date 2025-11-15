package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.Task
import ecccomp.s2240788.mobile_android.databinding.ItemTimelineHourBinding
import ecccomp.s2240788.mobile_android.databinding.ItemTimelineTaskBinding
import java.text.SimpleDateFormat
import java.util.*

/**
 * TimelineSlot
 * 時間スロットのデータモデル
 */
data class TimelineSlot(
    val hour: Int,
    val tasks: List<Task>,
    val isCurrentHour: Boolean = false
)

/**
 * TimelineAdapter
 * カレンダーの時間軸表示用アダプター
 */
class TimelineAdapter(
    private val onTaskClick: (Task) -> Unit
) : RecyclerView.Adapter<TimelineAdapter.TimelineViewHolder>() {

    private var timelineSlots: List<TimelineSlot> = emptyList()

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): TimelineViewHolder {
        val binding = ItemTimelineHourBinding.inflate(
            LayoutInflater.from(parent.context),
            parent,
            false
        )
        return TimelineViewHolder(binding)
    }

    override fun onBindViewHolder(holder: TimelineViewHolder, position: Int) {
        holder.bind(timelineSlots[position])
    }

    override fun getItemCount(): Int = timelineSlots.size

    fun submitList(tasks: List<Task>) {
        val currentHour = Calendar.getInstance().get(Calendar.HOUR_OF_DAY)

        // 24時間分のスロットを生成 (00:00 - 23:00)
        timelineSlots = (0..23).map { hour ->
            // この時間帯のタスクをフィルター
            val tasksInThisHour = tasks.filter { task ->
                getTaskHour(task) == hour
            }

            TimelineSlot(
                hour = hour,
                tasks = tasksInThisHour,
                isCurrentHour = (hour == currentHour)
            )
        }

        notifyDataSetChanged()
    }

    /**
     * タスクの時間を取得（scheduled_timeから）
     * Backend now returns TIME type: "HH:mm:ss" or "HH:mm"
     */
    private fun getTaskHour(task: Task): Int {
        return try {
            if (!task.scheduled_time.isNullOrEmpty()) {
                val scheduledTime = task.scheduled_time
                // Format: "HH:mm:ss" or "HH:mm"
                val parts = scheduledTime.split(":")
                parts.getOrNull(0)?.toIntOrNull() ?: -1
            } else {
                -1 // 時間指定なし
            }
        } catch (e: Exception) {
            -1
        }
    }

    inner class TimelineViewHolder(
        private val binding: ItemTimelineHourBinding
    ) : RecyclerView.ViewHolder(binding.root) {

        fun bind(slot: TimelineSlot) {
            binding.apply {
                // 時間ラベル
                tvHour.text = String.format("%02d:00", slot.hour)

                // 現在時刻インジケーター
                if (slot.isCurrentHour) {
                    currentTimeIndicator.visibility = View.VISIBLE
                } else {
                    currentTimeIndicator.visibility = View.GONE
                }

                // タスクコンテナをクリア
                tasksContainer.removeAllViews()

                // タスクがある場合は表示
                if (slot.tasks.isNotEmpty()) {
                    slot.tasks.forEach { task ->
                        val taskView = createTaskView(task)
                        tasksContainer.addView(taskView)
                    }
                }
            }
        }

        /**
         * タスクビューを動的に生成
         */
        private fun createTaskView(task: Task): View {
            val taskBinding = ItemTimelineTaskBinding.inflate(
                LayoutInflater.from(itemView.context),
                binding.tasksContainer,
                false
            )

            taskBinding.apply {
                // タスクタイトル
                tvTaskTitle.text = task.title

                // 時間表示
                if (task.estimated_minutes != null && task.estimated_minutes > 0) {
                    tvTime.visibility = View.VISIBLE
                    tvTime.text = "${task.estimated_minutes}分"
                } else {
                    tvTime.visibility = View.GONE
                }

                // カテゴリーバッジ
                if (!task.category.isNullOrEmpty()) {
                    categoryBadge.visibility = View.VISIBLE

                    val (categoryText, categoryColor, categoryBgColor) = when (task.category.lowercase()) {
                        "study", "learning", "学習" -> Triple(
                            itemView.context.getString(R.string.category_learning),
                            R.color.primary,
                            R.color.primary_light
                        )
                        "work", "仕事" -> Triple(
                            itemView.context.getString(R.string.category_work),
                            R.color.info,
                            R.color.info_light
                        )
                        "personal", "個人" -> Triple(
                            itemView.context.getString(R.string.category_personal),
                            R.color.accent,
                            R.color.accent_light
                        )
                        "project", "プロジェクト" -> Triple(
                            itemView.context.getString(R.string.category_project),
                            R.color.warning,
                            R.color.warning_light
                        )
                        else -> Triple(
                            itemView.context.getString(R.string.category_other),
                            R.color.text_muted,
                            R.color.surface
                        )
                    }

                    categoryBadge.setCardBackgroundColor(
                        ContextCompat.getColor(itemView.context, categoryBgColor)
                    )
                    tvCategory.text = categoryText
                    tvCategory.setTextColor(
                        ContextCompat.getColor(itemView.context, categoryColor)
                    )
                } else {
                    categoryBadge.visibility = View.GONE
                }

                // クリックリスナー
                root.setOnClickListener {
                    onTaskClick(task)
                }
            }

            return taskBinding.root
        }
    }
}
