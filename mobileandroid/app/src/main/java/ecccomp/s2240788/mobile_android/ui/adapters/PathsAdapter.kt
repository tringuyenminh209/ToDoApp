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

                // Status text - giờ là TextView trong LinearLayout
                val statusText = when (path.status) {
                    "completed" -> "完了"
                    "in_progress", "active" -> "進行中"
                    "paused" -> "一時停止"
                    else -> "未開始"
                }
                tvStatus.text = statusText

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

                // More button - thay thế Complete và Delete buttons
                btnMore.setOnClickListener {
                    // Show popup menu với options Complete và Delete
                    val popup = android.widget.PopupMenu(itemView.context, it)
                    popup.menuInflater.inflate(R.menu.menu_path_actions, popup.menu)
                    
                    // Hide complete option if already completed
                    if (path.status == "completed") {
                        popup.menu.findItem(R.id.action_complete)?.isVisible = false
                    }
                    
                    popup.setOnMenuItemClickListener { menuItem ->
                        when (menuItem.itemId) {
                            R.id.action_complete -> {
                                onCompleteClick(path)
                                true
                            }
                            R.id.action_delete -> {
                                onDeleteClick(path)
                                true
                            }
                            else -> false
                        }
                    }
                    popup.show()
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

