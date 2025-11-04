package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ItemMilestoneBinding
import ecccomp.s2240788.mobile_android.data.models.LearningMilestone

/**
 * Adapter for displaying learning milestones
 */
class MilestoneAdapter(
    private val onMilestoneClick: (LearningMilestone) -> Unit
) : ListAdapter<LearningMilestone, MilestoneAdapter.MilestoneViewHolder>(MilestoneDiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MilestoneViewHolder {
        val binding = ItemMilestoneBinding.inflate(
            LayoutInflater.from(parent.context),
            parent,
            false
        )
        return MilestoneViewHolder(binding, onMilestoneClick)
    }

    override fun onBindViewHolder(holder: MilestoneViewHolder, position: Int) {
        holder.bind(getItem(position), position + 1)
    }

    class MilestoneViewHolder(
        private val binding: ItemMilestoneBinding,
        private val onMilestoneClick: (LearningMilestone) -> Unit
    ) : RecyclerView.ViewHolder(binding.root) {

        fun bind(milestone: LearningMilestone, number: Int) {
            binding.apply {
                // Number and Title
                tvNumber.text = "$number"
                tvTitle.text = milestone.title

                // Description
                if (!milestone.description.isNullOrEmpty()) {
                    tvDescription.text = milestone.description
                    tvDescription.visibility = View.VISIBLE
                } else {
                    tvDescription.visibility = View.GONE
                }

                // Status badge
                val statusText = when (milestone.status.lowercase()) {
                    "not_started", "pending" -> root.context.getString(R.string.status_pending)
                    "in_progress" -> root.context.getString(R.string.status_in_progress)
                    "completed" -> root.context.getString(R.string.status_completed)
                    else -> root.context.getString(R.string.status_pending) // Default fallback
                }
                tvStatus.text = statusText

                // Status color and background
                val (statusColor, statusBg) = when (milestone.status.lowercase()) {
                    "completed" -> Pair(
                        root.context.getColor(R.color.success),
                        R.drawable.badge_completed
                    )
                    "in_progress" -> Pair(
                        root.context.getColor(R.color.primary),
                        R.drawable.badge_in_progress
                    )
                    else -> Pair(
                        root.context.getColor(R.color.text_muted),
                        R.drawable.badge_in_progress // Use same drawable for pending/not_started
                    )
                }
                tvStatus.setTextColor(statusColor)
                tvStatus.setBackgroundResource(statusBg)

                // Completed date
                if (!milestone.completed_at.isNullOrEmpty() && milestone.status == "completed") {
                    tvCompletedDate.text = "完了日: ${milestone.completed_at}"
                    tvCompletedDate.visibility = View.VISIBLE
                } else {
                    tvCompletedDate.visibility = View.GONE
                }

                // Click listener
                root.setOnClickListener {
                    onMilestoneClick(milestone)
                }
            }
        }
    }

    class MilestoneDiffCallback : DiffUtil.ItemCallback<LearningMilestone>() {
        override fun areItemsTheSame(
            oldItem: LearningMilestone,
            newItem: LearningMilestone
        ): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(
            oldItem: LearningMilestone,
            newItem: LearningMilestone
        ): Boolean {
            return oldItem == newItem
        }
    }
}
