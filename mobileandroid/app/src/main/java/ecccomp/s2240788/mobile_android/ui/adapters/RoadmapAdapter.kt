package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.PopularRoadmap

/**
 * RoadmapAdapter
 * 人気のロードマップ一覧を表示
 */
class RoadmapAdapter(
    private val onRoadmapClick: (PopularRoadmap) -> Unit,
    private val onGenerateClick: () -> Unit
) : ListAdapter<PopularRoadmap, RecyclerView.ViewHolder>(RoadmapDiffCallback()) {

    companion object {
        private const val TYPE_ROADMAP = 0
        private const val TYPE_GENERATE_BUTTON = 1
    }

    override fun getItemViewType(position: Int): Int {
        return if (position == currentList.size) {
            TYPE_GENERATE_BUTTON
        } else {
            TYPE_ROADMAP
        }
    }

    override fun getItemCount(): Int {
        return super.getItemCount() + 1 // +1 for generate button
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): RecyclerView.ViewHolder {
        return when (viewType) {
            TYPE_GENERATE_BUTTON -> {
                val view = LayoutInflater.from(parent.context)
                    .inflate(R.layout.item_roadmap_generate_button, parent, false)
                GenerateButtonViewHolder(view, onGenerateClick)
            }
            else -> {
                val view = LayoutInflater.from(parent.context)
                    .inflate(R.layout.item_roadmap, parent, false)
                RoadmapViewHolder(view, onRoadmapClick)
            }
        }
    }

    override fun onBindViewHolder(holder: RecyclerView.ViewHolder, position: Int) {
        when (holder) {
            is RoadmapViewHolder -> {
                val roadmap = getItem(position)
                holder.bind(roadmap)
            }
            is GenerateButtonViewHolder -> {
                // No binding needed for button
            }
        }
    }

    class RoadmapViewHolder(
        itemView: View,
        private val onClick: (PopularRoadmap) -> Unit
    ) : RecyclerView.ViewHolder(itemView) {

        private val titleText: TextView = itemView.findViewById(R.id.tv_roadmap_title)
        private val descriptionText: TextView = itemView.findViewById(R.id.tv_roadmap_description)
        private val categoryText: TextView = itemView.findViewById(R.id.tv_roadmap_category)
        private val difficultyText: TextView = itemView.findViewById(R.id.tv_roadmap_difficulty)
        private val hoursText: TextView = itemView.findViewById(R.id.tv_roadmap_hours)

        fun bind(roadmap: PopularRoadmap) {
            titleText.text = roadmap.title
            descriptionText.text = roadmap.description
            categoryText.text = roadmap.category
            difficultyText.text = roadmap.difficulty
            hoursText.text = "${roadmap.estimatedHours}時間"

            itemView.setOnClickListener {
                onClick(roadmap)
            }
        }
    }

    class GenerateButtonViewHolder(
        itemView: View,
        private val onClick: () -> Unit
    ) : RecyclerView.ViewHolder(itemView) {
        init {
            itemView.setOnClickListener {
                onClick()
            }
        }
    }

    class RoadmapDiffCallback : DiffUtil.ItemCallback<PopularRoadmap>() {
        override fun areItemsTheSame(oldItem: PopularRoadmap, newItem: PopularRoadmap): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(oldItem: PopularRoadmap, newItem: PopularRoadmap): Boolean {
            return oldItem == newItem
        }
    }
}

