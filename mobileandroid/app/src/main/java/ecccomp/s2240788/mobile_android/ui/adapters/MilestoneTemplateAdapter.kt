package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.databinding.ItemMilestoneTemplateBinding
import ecccomp.s2240788.mobile_android.data.models.LearningMilestoneTemplate

/**
 * Adapter for displaying milestone templates with expandable tasks
 */
class MilestoneTemplateAdapter : ListAdapter<LearningMilestoneTemplate, MilestoneTemplateAdapter.MilestoneViewHolder>(MilestoneDiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MilestoneViewHolder {
        val binding = ItemMilestoneTemplateBinding.inflate(
            LayoutInflater.from(parent.context),
            parent,
            false
        )
        return MilestoneViewHolder(binding)
    }

    override fun onBindViewHolder(holder: MilestoneViewHolder, position: Int) {
        holder.bind(getItem(position), position + 1)
    }

    class MilestoneViewHolder(
        private val binding: ItemMilestoneTemplateBinding
    ) : RecyclerView.ViewHolder(binding.root) {

        private var isExpanded = false
        private lateinit var taskAdapter: TaskTemplateAdapter

        fun bind(milestone: LearningMilestoneTemplate, number: Int) {
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
                
                // Duration
                milestone.estimatedHours?.let {
                    tvDuration.text = "${it}時間"
                    tvDuration.visibility = View.VISIBLE
                } ?: run {
                    tvDuration.visibility = View.GONE
                }
                
                // Tasks count
                val tasksCount = milestone.tasks?.size ?: 0
                tvTasksCount.text = "${tasksCount}タスク"
                
                // Deliverables
                if (!milestone.deliverables.isNullOrEmpty()) {
                    val deliverablesText = milestone.deliverables.joinToString("\n") { "• $it" }
                    tvDeliverables.text = deliverablesText
                    sectionDeliverables.visibility = View.VISIBLE
                } else {
                    sectionDeliverables.visibility = View.GONE
                }
                
                // Tasks RecyclerView
                if (!milestone.tasks.isNullOrEmpty()) {
                    taskAdapter = TaskTemplateAdapter()
                    rvTasks.apply {
                        layoutManager = LinearLayoutManager(binding.root.context)
                        adapter = taskAdapter
                    }
                    taskAdapter.submitList(milestone.tasks)
                    
                    // Expand/Collapse
                    updateExpandState()
                    
                    root.setOnClickListener {
                        isExpanded = !isExpanded
                        updateExpandState()
                    }
                } else {
                    rvTasks.visibility = View.GONE
                    ivExpand.visibility = View.GONE
                }
            }
        }

        private fun updateExpandState() {
            binding.apply {
                if (isExpanded) {
                    rvTasks.visibility = View.VISIBLE
                    ivExpand.rotation = 180f
                } else {
                    rvTasks.visibility = View.GONE
                    ivExpand.rotation = 0f
                }
            }
        }
    }

    class MilestoneDiffCallback : DiffUtil.ItemCallback<LearningMilestoneTemplate>() {
        override fun areItemsTheSame(
            oldItem: LearningMilestoneTemplate,
            newItem: LearningMilestoneTemplate
        ): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(
            oldItem: LearningMilestoneTemplate,
            newItem: LearningMilestoneTemplate
        ): Boolean {
            return oldItem == newItem
        }
    }
}

