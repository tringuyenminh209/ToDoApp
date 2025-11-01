package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageView
import android.widget.TextView
import androidx.cardview.widget.CardView
import androidx.recyclerview.widget.RecyclerView
import com.google.android.material.checkbox.MaterialCheckBox
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.StudyModel
import ecccomp.s2240788.mobile_android.data.models.StudyType

/**
 * Adapter for Study List in Timetable
 */
class StudyAdapter(
    private var studies: List<StudyModel>,
    private val onStudyClick: (StudyModel) -> Unit,
    private val onCheckboxClick: (StudyModel) -> Unit
) : RecyclerView.Adapter<StudyAdapter.StudyViewHolder>() {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): StudyViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_study, parent, false)
        return StudyViewHolder(view)
    }

    override fun onBindViewHolder(holder: StudyViewHolder, position: Int) {
        holder.bind(studies[position])
    }

    override fun getItemCount(): Int = studies.size

    fun updateStudies(newStudies: List<StudyModel>) {
        studies = newStudies
        notifyDataSetChanged()
    }

    inner class StudyViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val card: CardView = itemView.findViewById(R.id.study_item_card)
        private val iconContainer: CardView = itemView.findViewById(R.id.type_icon_container)
        private val icon: ImageView = itemView.findViewById(R.id.img_type_icon)
        private val title: TextView = itemView.findViewById(R.id.tv_study_title)
        private val subtitle: TextView = itemView.findViewById(R.id.tv_study_subtitle)
        private val progress: TextView = itemView.findViewById(R.id.tv_study_progress)
        private val checkbox: MaterialCheckBox = itemView.findViewById(R.id.checkbox_complete)

        fun bind(study: StudyModel) {
            // Set title
            title.text = study.title
            
            // Set subtitle
            val typeText = when (study.type) {
                StudyType.HOMEWORK -> itemView.context.getString(R.string.timetable_study_type_homework)
                StudyType.REVIEW -> itemView.context.getString(R.string.timetable_study_type_review)
                StudyType.EXAM -> itemView.context.getString(R.string.timetable_study_type_exam)
                StudyType.PROJECT -> itemView.context.getString(R.string.timetable_study_type_project)
            }
            val subtitleText = buildString {
                append(typeText)
                append(" • ")
                append(study.subject)
                study.dueDate?.let {
                    append(" • ")
                    append(itemView.context.getString(R.string.timetable_study_due_date_hint))
                    append(": ")
                    append(it)
                }
            }
            subtitle.text = subtitleText
            
            // Set icon and color based on type
            val (iconRes, bgColor) = when (study.type) {
                StudyType.HOMEWORK -> Pair(R.drawable.ic_edit, R.color.warning)
                StudyType.REVIEW -> Pair(R.drawable.ic_book, R.color.accent)
                StudyType.EXAM -> Pair(R.drawable.ic_target, R.color.error)
                StudyType.PROJECT -> Pair(R.drawable.ic_folder, R.color.primary)
            }
            icon.setImageResource(iconRes)
            iconContainer.setCardBackgroundColor(itemView.context.getColor(bgColor))
            
            // Set progress text and color based on priority (1-5)
            val (progressText, progressColor) = when (study.priority) {
                1, 2 -> Pair(itemView.context.getString(R.string.priority_high), R.color.error)
                3 -> Pair(itemView.context.getString(R.string.priority_medium), R.color.warning)
                4, 5 -> Pair(itemView.context.getString(R.string.priority_low), R.color.success)
                else -> Pair(itemView.context.getString(R.string.priority_medium), R.color.warning)
            }
            progress.text = if (study.completed) {
                itemView.context.getString(R.string.timetable_study_completed)
            } else {
                itemView.context.getString(R.string.timetable_study_pending)
            }
            progress.setTextColor(itemView.context.getColor(progressColor))
            
            // Set checkbox
            checkbox.isChecked = study.completed
            
            // Set click listeners
            card.setOnClickListener { onStudyClick(study) }
            checkbox.setOnClickListener { onCheckboxClick(study) }
            
            // Apply completed style
            if (study.completed) {
                card.alpha = 0.6f
                title.paintFlags = title.paintFlags or android.graphics.Paint.STRIKE_THRU_TEXT_FLAG
            } else {
                card.alpha = 1.0f
                title.paintFlags = title.paintFlags and android.graphics.Paint.STRIKE_THRU_TEXT_FLAG.inv()
            }
        }
    }
}
