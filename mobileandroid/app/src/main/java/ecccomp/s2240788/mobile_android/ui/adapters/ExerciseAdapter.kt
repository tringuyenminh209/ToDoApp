package ecccomp.s2240788.mobile_android.ui.adapters

import android.graphics.Color
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.google.android.material.card.MaterialCardView
import com.google.android.material.chip.Chip
import com.google.android.material.chip.ChipGroup
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.ExerciseSummary

class ExerciseAdapter(
    private val onExerciseClick: (ExerciseSummary) -> Unit
) : ListAdapter<ExerciseSummary, ExerciseAdapter.ViewHolder>(DiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_exercise, parent, false)
        return ViewHolder(view)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position), onExerciseClick)
    }

    class ViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val cardExercise: MaterialCardView = itemView.findViewById(R.id.card_exercise)
        private val tvExerciseTitle: TextView = itemView.findViewById(R.id.tv_exercise_title)
        private val tvExerciseDescription: TextView = itemView.findViewById(R.id.tv_exercise_description)
        private val tvDifficulty: TextView = itemView.findViewById(R.id.tv_difficulty)
        private val tvPoints: TextView = itemView.findViewById(R.id.tv_points)
        private val tvSuccessRate: TextView = itemView.findViewById(R.id.tv_success_rate)
        private val chipGroupTags: ChipGroup = itemView.findViewById(R.id.chip_group_tags)

        fun bind(exercise: ExerciseSummary, onExerciseClick: (ExerciseSummary) -> Unit) {
            // Set title and description
            tvExerciseTitle.text = exercise.title
            tvExerciseDescription.text = exercise.description

            // Set difficulty with color
            tvDifficulty.text = getDifficultyText(exercise.difficulty)
            tvDifficulty.setTextColor(getDifficultyColor(exercise.difficulty))

            // Set points
            tvPoints.text = "${exercise.points}pt"

            // Set success rate
            tvSuccessRate.text = if (exercise.submissionsCount > 0) {
                "${exercise.successRate.toInt()}%"
            } else {
                "New"
            }

            // Add tags
            chipGroupTags.removeAllViews()
            exercise.tags?.take(3)?.forEach { tag ->
                val chip = Chip(itemView.context).apply {
                    text = tag
                    isClickable = false
                    isCheckable = false
                    setChipBackgroundColorResource(R.color.chip_background)
                    setTextColor(Color.parseColor("#666666"))
                }
                chipGroupTags.addView(chip)
            }

            // Click listener
            cardExercise.setOnClickListener {
                onExerciseClick(exercise)
            }
        }

        private fun getDifficultyText(difficulty: String): String {
            return when (difficulty.lowercase()) {
                "easy" -> "簡単"
                "medium" -> "中級"
                "hard" -> "難しい"
                else -> difficulty
            }
        }

        private fun getDifficultyColor(difficulty: String): Int {
            return Color.parseColor(
                when (difficulty.lowercase()) {
                    "easy" -> "#4CAF50" // Green
                    "medium" -> "#FF9800" // Orange
                    "hard" -> "#F44336" // Red
                    else -> "#9E9E9E" // Grey
                }
            )
        }
    }

    class DiffCallback : DiffUtil.ItemCallback<ExerciseSummary>() {
        override fun areItemsTheSame(oldItem: ExerciseSummary, newItem: ExerciseSummary): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(oldItem: ExerciseSummary, newItem: ExerciseSummary): Boolean {
            return oldItem == newItem
        }
    }
}
