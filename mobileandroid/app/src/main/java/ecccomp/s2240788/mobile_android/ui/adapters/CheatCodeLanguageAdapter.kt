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

            // Set background color from database, fallback to default if invalid
            val backgroundColor = try {
                if (!language.color.isNullOrBlank() && language.color.startsWith("#")) {
                    language.color
                } else {
                    getLanguageColor(language.name) // Fallback to hardcoded colors
                }
            } catch (e: Exception) {
                getLanguageColor(language.name) // Fallback to hardcoded colors
            }
            cardLanguage.setCardBackgroundColor(Color.parseColor(backgroundColor))
            
            // Adjust text color based on background brightness for better readability
            val textColor = getContrastTextColor(backgroundColor)
            tvLanguageName.setTextColor(textColor)

            // Set icon from database - NO COLOR FILTER to show original colors
            if (!language.icon.isNullOrBlank()) {
                val iconResId = itemView.context.resources.getIdentifier(
                    language.icon,
                    "drawable",
                    itemView.context.packageName
                )
                if (iconResId != 0) {
                    ivLanguageIcon.setImageResource(iconResId)
                    // Remove color filter to show original icon colors
                    ivLanguageIcon.clearColorFilter()
                } else {
                    // Fallback to default icon
                    ivLanguageIcon.setImageResource(R.drawable.ic_computer)
                    ivLanguageIcon.clearColorFilter()
                }
            } else {
                // Default icon if no icon specified
                ivLanguageIcon.setImageResource(R.drawable.ic_computer)
                ivLanguageIcon.clearColorFilter()
            }

            // Set tag if needed (e.g., Laravel -> PHP)
            if (language.name == "laravel" || language.name == "php") {
                tvTag.visibility = View.VISIBLE
                tvTag.text = if (language.name == "laravel") "PHP" else null
            } else {
                tvTag.visibility = View.GONE
            }

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
                "mysql" -> "#4479A1"
                "docker" -> "#0DB7ED"
                "yaml" -> "#9E9E9E"
                "bash" -> "#9E9E9E"
                else -> "#E0E0E0" // Default light grey
            }
        }

        /**
         * Calculate contrast text color (black or white) based on background brightness
         */
        private fun getContrastTextColor(backgroundColorHex: String): Int {
            return try {
                val color = Color.parseColor(backgroundColorHex)
                // Calculate luminance using relative luminance formula
                val r = Color.red(color) / 255.0
                val g = Color.green(color) / 255.0
                val b = Color.blue(color) / 255.0
                
                val luminance = 0.299 * r + 0.587 * g + 0.114 * b
                
                // Use black text on light backgrounds, white on dark backgrounds
                if (luminance > 0.5) {
                    Color.parseColor("#000000") // Black for light backgrounds
                } else {
                    Color.parseColor("#FFFFFF") // White for dark backgrounds
                }
            } catch (e: Exception) {
                Color.parseColor("#000000") // Default to black
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
