package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageButton
import android.widget.ImageView
import android.widget.TextView
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.KnowledgeCategory

/**
 * FolderAdapter
 * Adapter for displaying categories as folders
 * Used in the hierarchical knowledge navigation
 */
class FolderAdapter(
    private val onFolderClick: (KnowledgeCategory) -> Unit,
    private val onFolderMenuClick: (KnowledgeCategory) -> Unit
) : ListAdapter<KnowledgeCategory, FolderAdapter.FolderViewHolder>(FolderDiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): FolderViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_knowledge_folder, parent, false)
        return FolderViewHolder(view)
    }

    override fun onBindViewHolder(holder: FolderViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    inner class FolderViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val ivFolderIcon: ImageView = itemView.findViewById(R.id.iv_folder_icon)
        private val tvFolderName: TextView = itemView.findViewById(R.id.tv_folder_name)
        private val tvFolderDescription: TextView = itemView.findViewById(R.id.tv_folder_description)
        private val tvItemCount: TextView = itemView.findViewById(R.id.tv_item_count)
        private val btnFolderMenu: ImageButton = itemView.findViewById(R.id.btn_folder_menu)

        fun bind(category: KnowledgeCategory) {
            // Set folder name
            tvFolderName.text = category.name

            // Set description if available
            if (!category.description.isNullOrEmpty()) {
                tvFolderDescription.text = category.description
                tvFolderDescription.visibility = View.VISIBLE
            } else {
                tvFolderDescription.visibility = View.GONE
            }

            // Set item count
            tvItemCount.text = category.item_count.toString()

            // Set folder icon (could be customized based on category icon)
            // For now, use default folder icon
            ivFolderIcon.setImageResource(R.drawable.ic_folder)

            // Click listeners
            itemView.setOnClickListener {
                onFolderClick(category)
            }

            btnFolderMenu.setOnClickListener {
                onFolderMenuClick(category)
            }
        }
    }

    private class FolderDiffCallback : DiffUtil.ItemCallback<KnowledgeCategory>() {
        override fun areItemsTheSame(
            oldItem: KnowledgeCategory,
            newItem: KnowledgeCategory
        ): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(
            oldItem: KnowledgeCategory,
            newItem: KnowledgeCategory
        ): Boolean {
            return oldItem == newItem
        }
    }
}
