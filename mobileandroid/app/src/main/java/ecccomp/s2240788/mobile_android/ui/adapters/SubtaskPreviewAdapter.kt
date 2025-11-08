package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.data.models.Subtask
import ecccomp.s2240788.mobile_android.databinding.ItemSubtaskPreviewBinding

/**
 * SubtaskPreviewAdapter
 * サブタスクプレビュー用のアダプター
 */
class SubtaskPreviewAdapter(
    private val subtasks: List<Subtask>
) : ListAdapter<Subtask, SubtaskPreviewAdapter.SubtaskPreviewViewHolder>(SubtaskDiffCallback()) {

    init {
        submitList(subtasks)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): SubtaskPreviewViewHolder {
        val binding = ItemSubtaskPreviewBinding.inflate(
            LayoutInflater.from(parent.context),
            parent,
            false
        )
        return SubtaskPreviewViewHolder(binding)
    }

    override fun onBindViewHolder(holder: SubtaskPreviewViewHolder, position: Int) {
        holder.bind(getItem(position), position + 1)
    }

    class SubtaskPreviewViewHolder(
        private val binding: ItemSubtaskPreviewBinding
    ) : RecyclerView.ViewHolder(binding.root) {

        fun bind(subtask: Subtask, index: Int) {
            binding.apply {
                tvSubtaskNumber.text = "$index."
                tvSubtaskTitle.text = subtask.title
                
                // Show estimated time if available
                subtask.estimated_minutes?.let { minutes ->
                    tvSubtaskTime.text = "${minutes}分"
                    tvSubtaskTime.visibility = android.view.View.VISIBLE
                } ?: run {
                    tvSubtaskTime.visibility = android.view.View.GONE
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

