package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ItemFocusSubtaskBinding
import ecccomp.s2240788.mobile_android.data.models.Subtask

/**
 * Adapter for displaying subtasks in Focus Session with progress tracking
 * Shows progress bar based on elapsed time vs estimated_minutes
 */
class FocusSubtaskAdapter(
    private val elapsedMinutesMap: Map<Int, Int> = emptyMap() // subtask_id -> elapsed minutes
) : ListAdapter<Subtask, FocusSubtaskAdapter.SubtaskViewHolder>(SubtaskDiffCallback()) {

    private var elapsedMinutes: Map<Int, Int> = elapsedMinutesMap

    fun updateElapsedMinutes(newMap: Map<Int, Int>) {
        elapsedMinutes = newMap
        notifyDataSetChanged()
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): SubtaskViewHolder {
        val binding = ItemFocusSubtaskBinding.inflate(
            LayoutInflater.from(parent.context),
            parent,
            false
        )
        return SubtaskViewHolder(binding)
    }

    override fun onBindViewHolder(holder: SubtaskViewHolder, position: Int) {
        holder.bind(getItem(position), elapsedMinutes)
    }

    class SubtaskViewHolder(
        private val binding: ItemFocusSubtaskBinding
    ) : RecyclerView.ViewHolder(binding.root) {

        fun bind(subtask: Subtask, elapsedMinutes: Map<Int, Int>) {
            binding.apply {
                // Set title
                tvSubtaskTitle.text = subtask.title

                // Set time indicator
                if (subtask.estimated_minutes != null && subtask.estimated_minutes > 0) {
                    tvSubtaskTime.text = "${subtask.estimated_minutes}分"
                    tvSubtaskTime.visibility = View.VISIBLE
                } else {
                    tvSubtaskTime.visibility = View.GONE
                }

                // Update completion state
                if (subtask.is_completed) {
                    // Completed: show checkmark, strike-through, hide progress
                    checkboxIndicator.setCardBackgroundColor(
                        ContextCompat.getColor(itemView.context, R.color.success_light)
                    )
                    ivCheck.setImageResource(R.drawable.ic_check_circle)
                    ivCheck.setColorFilter(
                        ContextCompat.getColor(itemView.context, R.color.success)
                    )
                    tvSubtaskTitle.paint.isStrikeThruText = true
                    tvSubtaskTitle.alpha = 0.5f
                    progressSubtask.visibility = View.GONE
                } else {
                    // Not completed: show progress if has estimated time
                    checkboxIndicator.setCardBackgroundColor(
                        ContextCompat.getColor(itemView.context, R.color.surface)
                    )
                    ivCheck.setImageResource(R.drawable.ic_check_circle)
                    ivCheck.setColorFilter(
                        ContextCompat.getColor(itemView.context, R.color.text_muted)
                    )
                    tvSubtaskTitle.paint.isStrikeThruText = false
                    tvSubtaskTitle.alpha = 1.0f

                    // Show progress bar if has estimated time
                    val estimatedMinutes = subtask.estimated_minutes ?: 0
                    val elapsed = elapsedMinutes[subtask.id] ?: 0

                    if (estimatedMinutes > 0 && elapsed > 0) {
                        progressSubtask.visibility = View.VISIBLE
                        val progress = ((elapsed * 100) / estimatedMinutes).coerceIn(0, 100)
                        progressSubtask.setProgressCompat(progress, true)
                    } else {
                        progressSubtask.visibility = View.GONE
                    }
                }
            }
        }
    }

    class SubtaskDiffCallback : DiffUtil.ItemCallback<Subtask>() {
        override fun areItemsTheSame(oldItem: Subtask, newItem: Subtask): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(oldItem: Subtask, newItem: Subtask): Boolean {
            return oldItem == newItem
        }
    }
}

