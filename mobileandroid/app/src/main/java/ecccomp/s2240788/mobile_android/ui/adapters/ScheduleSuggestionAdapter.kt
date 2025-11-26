package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.google.android.material.button.MaterialButton
import com.google.android.material.chip.Chip
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.ScheduleSuggestion
import java.text.SimpleDateFormat
import java.util.*

class ScheduleSuggestionAdapter(
    private val onSuggestionSelected: (ScheduleSuggestion) -> Unit
) : RecyclerView.Adapter<ScheduleSuggestionAdapter.ViewHolder>() {

    private var suggestions: List<ScheduleSuggestion> = emptyList()

    fun submitList(newSuggestions: List<ScheduleSuggestion>) {
        suggestions = newSuggestions
        notifyDataSetChanged()
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_schedule_suggestion, parent, false)
        return ViewHolder(view)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(suggestions[position])
    }

    override fun getItemCount() = suggestions.size

    inner class ViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val tvDateTime: TextView = itemView.findViewById(R.id.tv_date_time)
        private val tvScore: TextView = itemView.findViewById(R.id.tv_score)
        private val chipConfidence: Chip = itemView.findViewById(R.id.chip_confidence)
        private val tvReasons: TextView = itemView.findViewById(R.id.tv_reasons)
        private val btnSelect: MaterialButton = itemView.findViewById(R.id.btn_select)

        fun bind(suggestion: ScheduleSuggestion) {
            // Format date & time
            val dateTime = "${formatDate(suggestion.date)}, ${formatTime(suggestion.startTime)}-${formatTime(suggestion.endTime)}"
            tvDateTime.text = dateTime

            // Score
            tvScore.text = "スコア: ${suggestion.score}"

            // Confidence badge
            val confidenceText = when (suggestion.confidence) {
                "high" -> "信頼度: 高"
                "medium" -> "信頼度: 中"
                else -> "信頼度: 低"
            }
            chipConfidence.text = confidenceText

            // Set chip color based on confidence
            val chipColorRes = when (suggestion.confidence) {
                "high" -> android.R.color.holo_green_light
                "medium" -> android.R.color.holo_orange_light
                else -> android.R.color.holo_red_light
            }
            chipConfidence.setChipBackgroundColorResource(chipColorRes)

            // Reasons
            val reasonsText = suggestion.reasons.joinToString("\n") { "• $it" }
            tvReasons.text = reasonsText

            // Select button
            btnSelect.setOnClickListener {
                onSuggestionSelected(suggestion)
            }
        }

        private fun formatDate(dateString: String): String {
            return try {
                // Parse "2025-11-26" to "水曜日, 11/26"
                val inputFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
                val date = inputFormat.parse(dateString) ?: return dateString

                val dayOfWeek = SimpleDateFormat("EEEE", Locale.JAPANESE).format(date)
                val monthDay = SimpleDateFormat("MM/dd", Locale.getDefault()).format(date)
                "$dayOfWeek, $monthDay"
            } catch (e: Exception) {
                dateString
            }
        }

        private fun formatTime(timeString: String): String {
            // Parse "14:00:00" to "14:00"
            return timeString.substring(0, 5)
        }
    }
}
