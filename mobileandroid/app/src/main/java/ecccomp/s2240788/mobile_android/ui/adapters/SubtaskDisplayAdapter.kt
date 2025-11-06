package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.databinding.ItemSubtaskBinding
import ecccomp.s2240788.mobile_android.data.models.Subtask

/**
 * Adapter for displaying subtasks in TaskDetailActivity with start button
 */
class SubtaskDisplayAdapter(
    private val onToggle: (Subtask) -> Unit,
    private val onStart: (Subtask) -> Unit
) : ListAdapter<Subtask, SubtaskDisplayAdapter.SubtaskViewHolder>(SubtaskDiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): SubtaskViewHolder {
        val binding = ItemSubtaskBinding.inflate(
            LayoutInflater.from(parent.context),
            parent,
            false
        )
        return SubtaskViewHolder(binding, onToggle, onStart)
    }

    override fun onBindViewHolder(holder: SubtaskViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    class SubtaskViewHolder(
        private val binding: ItemSubtaskBinding,
        private val onToggle: (Subtask) -> Unit,
        private val onStart: (Subtask) -> Unit
    ) : RecyclerView.ViewHolder(binding.root) {

        fun bind(subtask: Subtask) {
            binding.apply {
                // Set title with estimated time if available
                val titleText = if (subtask.estimated_minutes != null && subtask.estimated_minutes > 0) {
                    "${subtask.title} (${subtask.estimated_minutes}分)"
                } else {
                    subtask.title
                }
                tvSubtaskTitle.text = titleText

                // Update check icon and strike-through based on completion
                if (subtask.is_completed) {
                    ivSubtaskCheck.setImageResource(android.R.drawable.checkbox_on_background)
                    tvSubtaskTitle.paint.isStrikeThruText = true
                    tvSubtaskTitle.alpha = 0.5f
                    // Hide start button for completed subtasks
                    btnStartSubtask.visibility = View.GONE
                } else {
                    ivSubtaskCheck.setImageResource(android.R.drawable.checkbox_off_background)
                    tvSubtaskTitle.paint.isStrikeThruText = false
                    tvSubtaskTitle.alpha = 1.0f
                    // Show start button for incomplete subtasks
                    btnStartSubtask.visibility = View.VISIBLE
                }

                // Toggle on row click
                root.setOnClickListener {
                    onToggle(subtask)
                }

                // Start button click handler
                btnStartSubtask.setOnClickListener {
                    onStart(subtask)
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
