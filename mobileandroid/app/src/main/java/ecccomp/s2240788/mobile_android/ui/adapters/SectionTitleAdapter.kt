package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.CheatCodeSection

/**
 * SectionTitleAdapter
 * セクションタイトルのアダプター（水平スクロール用）
 */
class SectionTitleAdapter(
    private val onSectionClick: (CheatCodeSection, Int) -> Unit
) : ListAdapter<CheatCodeSection, SectionTitleAdapter.ViewHolder>(DiffCallback()) {

    private var selectedPosition = 0

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_section_title, parent, false)
        return ViewHolder(view)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position), position, position == selectedPosition, onSectionClick)
    }

    fun setSelectedPosition(position: Int) {
        val oldPosition = selectedPosition
        selectedPosition = position
        notifyItemChanged(oldPosition)
        notifyItemChanged(selectedPosition)
    }

    class ViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val tvTitle: TextView = itemView.findViewById(R.id.tv_section_title)

        fun bind(
            section: CheatCodeSection,
            position: Int,
            isSelected: Boolean,
            onSectionClick: (CheatCodeSection, Int) -> Unit
        ) {
            tvTitle.text = section.title

            // Update selected state
            if (isSelected) {
                tvTitle.setBackgroundResource(R.drawable.bg_section_title_selected)
                tvTitle.setTextColor(itemView.context.getColor(R.color.white))
            } else {
                tvTitle.setBackgroundResource(R.drawable.bg_section_title_normal)
                tvTitle.setTextColor(itemView.context.getColor(R.color.text_primary))
            }

            itemView.setOnClickListener {
                onSectionClick(section, position)
            }
        }
    }

    private class DiffCallback : DiffUtil.ItemCallback<CheatCodeSection>() {
        override fun areItemsTheSame(oldItem: CheatCodeSection, newItem: CheatCodeSection): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(oldItem: CheatCodeSection, newItem: CheatCodeSection): Boolean {
            return oldItem.title == newItem.title && oldItem.sortOrder == newItem.sortOrder
        }
    }
}

