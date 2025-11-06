package ecccomp.s2240788.mobile_android.ui.adapters

import android.graphics.Color
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageView
import android.widget.TextView
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.google.android.material.card.MaterialCardView
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
        private val cardLanguage: MaterialCardView = itemView.findViewById(R.id.card_language)
        private val ivLanguageIcon: ImageView = itemView.findViewById(R.id.iv_language_icon)
        private val tvLanguageName: TextView = itemView.findViewById(R.id.tv_language_name)
        private val tvTag: TextView = itemView.findViewById(R.id.tv_tag)

        fun bind(language: CheatCodeLanguage, onLanguageClick: (CheatCodeLanguage) -> Unit) {
            // Set language name
            tvLanguageName.text = language.displayName

            // Set background color based on language
            val backgroundColor = getLanguageColor(language.name)
            cardLanguage.setCardBackgroundColor(Color.parseColor(backgroundColor))

            // Set icon tint to black/dark
            ivLanguageIcon.setColorFilter(Color.parseColor("#000000"))

            // Set tag if needed (e.g., Laravel -> PHP)
            if (language.name == "laravel" || language.name == "php") {
                tvTag.visibility = View.VISIBLE
                tvTag.text = if (language.name == "laravel") "PHP" else null
            } else {
                tvTag.visibility = View.GONE
            }

            // TODO: Set language icon based on language.name
            // For now, using default icon
            ivLanguageIcon.setImageResource(R.drawable.ic_computer)

            // Click listener
            itemView.setOnClickListener {
                onLanguageClick(language)
            }
        }

        private fun getLanguageColor(languageName: String): String {
            return when (languageName.lowercase()) {
                "ejs" -> "#C8E6C9" // Light green
                "kotlin" -> "#E1BEE7" // Light purple
                "kubernetes" -> "#BBDEFB" // Light blue
                "matlab" -> "#BCAAA4" // Brown
                "ini" -> "#BBDEFB" // Light blue
                "rust" -> "#9E9E9E" // Dark grey
                "laravel", "php" -> "#FFCCBC" // Red-orange
                "json" -> "#9E9E9E" // Grey
                "graphql" -> "#F8BBD0" // Pink
                "swift" -> "#FFCCBC" // Red-orange
                "express" -> "#FFF9C4" // Yellow
                "es6", "javascript" -> "#FFF9C4" // Yellow
                "c" -> "#9C27B0" // Dark purple
                "latex" -> "#9C27B0" // Dark purple
                "csharp", "c#" -> "#E1BEE7" // Light purple
                "dart" -> "#BBDEFB" // Light blue
                "html" -> "#FFCCBC" // Red-orange
                "cpp", "c++" -> "#BBDEFB" // Light blue
                "python" -> "#3776AB"
                "go" -> "#00ADD8"
                "java" -> "#ED8B00"
                "css3", "css" -> "#1572B6"
                else -> "#E0E0E0" // Default light grey
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
