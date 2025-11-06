package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageView
import android.widget.TextView
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.CheatCodeLanguage

class CheatCodeLanguageAdapter(
    private val onLanguageClick: (CheatCodeLanguage) -> Unit
) : ListAdapter<CheatCodeLanguage, CheatCodeLanguageAdapter.ViewHolder>(DiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_cheat_code_language, parent, false)
        return ViewHolder(view)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position), onLanguageClick)
    }

    class ViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val ivLanguageIcon: ImageView = itemView.findViewById(R.id.iv_language_icon)
        private val tvLanguageName: TextView = itemView.findViewById(R.id.tv_language_name)
        private val tvExamplesCount: TextView = itemView.findViewById(R.id.tv_examples_count)

        fun bind(language: CheatCodeLanguage, onLanguageClick: (CheatCodeLanguage) -> Unit) {
            // Set language name
            tvLanguageName.text = language.displayName

            // Set examples count
            val examplesText = if (language.exercisesCount > 0) {
                "${language.examplesCount}個の例 • ${language.exercisesCount}個の演習"
            } else {
                "${language.examplesCount}個の例"
            }
            tvExamplesCount.text = examplesText

            // TODO: Set language icon based on language.name
            // For now, using default icon
            ivLanguageIcon.setImageResource(R.drawable.ic_computer)

            // Click listener
            itemView.setOnClickListener {
                onLanguageClick(language)
            }
        }
    }

    private class DiffCallback : DiffUtil.ItemCallback<CheatCodeLanguage>() {
        override fun areItemsTheSame(oldItem: CheatCodeLanguage, newItem: CheatCodeLanguage): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(oldItem: CheatCodeLanguage, newItem: CheatCodeLanguage): Boolean {
            return oldItem == newItem
        }
    }
}
