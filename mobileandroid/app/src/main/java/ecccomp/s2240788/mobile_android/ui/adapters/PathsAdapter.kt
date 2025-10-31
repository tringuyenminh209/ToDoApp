package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.LearningPath
import ecccomp.s2240788.mobile_android.databinding.ItemPathCardBinding
import java.text.SimpleDateFormat
import java.util.*

/**
 * PathsAdapter
 * Learning Paths リスト表示用アダプター
 */
class PathsAdapter(
    private val onPathClick: (LearningPath) -> Unit,
    private val onCompleteClick: (LearningPath) -> Unit,
    private val onDeleteClick: (LearningPath) -> Unit
) : ListAdapter<LearningPath, PathsAdapter.PathViewHolder>(PathDiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): PathViewHolder {
        val binding = ItemPathCardBinding.inflate(
            LayoutInflater.from(parent.context),
            parent,
            false
        )
        return PathViewHolder(binding)
    }

    override fun onBindViewHolder(holder: PathViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    inner class PathViewHolder(
        private val binding: ItemPathCardBinding
    ) : RecyclerView.ViewHolder(binding.root) {

        fun bind(path: LearningPath) {
            binding.apply {
                // Title
                tvPathTitle.text = path.title

                // Description
                if (!path.description.isNullOrEmpty()) {
                    tvPathDescription.visibility = View.VISIBLE
                    tvPathDescription.text = path.description
                } else {
                    tvPathDescription.visibility = View.GONE
                }

                // Status indicator color
                val statusColor = when (path.status) {
                    "completed" -> R.color.success
                    "in_progress", "active" -> R.color.primary
                    "paused" -> R.color.warning
                    else -> R.color.text_muted
                }
                statusIndicator.setCardBackgroundColor(
                    ContextCompat.getColor(itemView.context, statusColor)
                )

                // Progress bar
                progressBar.setProgressCompat(path.progress_percentage, true)

                // Progress text
                val progressText = "${path.completed_milestones}/${path.total_milestones} milestones • ${path.progress_percentage}%"
                tvProgressText.text = progressText

                // Target date
                if (!path.target_date.isNullOrEmpty()) {
                    tvTargetDate.visibility = View.VISIBLE
                    try {
                        val inputFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
                        val outputFormat = SimpleDateFormat("MM/yyyy", Locale.getDefault())
                        val date = inputFormat.parse(path.target_date)
                        tvTargetDate.text = "Target: ${if (date != null) outputFormat.format(date) else path.target_date}"
                    } catch (e: Exception) {
                        tvTargetDate.text = "Target: ${path.target_date}"
                    }
                } else {
                    tvTargetDate.visibility = View.GONE
                }

                // Complete button - hide if already completed
                if (path.status == "completed") {
                    btnComplete.visibility = View.GONE
                } else {
                    btnComplete.visibility = View.VISIBLE
                    btnComplete.setOnClickListener {
                        onCompleteClick(path)
                    }
                }

                // Delete button
                btnDelete.setOnClickListener {
                    onDeleteClick(path)
                }

                // Card click
                root.setOnClickListener {
                    onPathClick(path)
                }
            }
        }
    }

    class PathDiffCallback : DiffUtil.ItemCallback<LearningPath>() {
        override fun areItemsTheSame(oldItem: LearningPath, newItem: LearningPath): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(oldItem: LearningPath, newItem: LearningPath): Boolean {
            return oldItem == newItem
        }
    }
}

