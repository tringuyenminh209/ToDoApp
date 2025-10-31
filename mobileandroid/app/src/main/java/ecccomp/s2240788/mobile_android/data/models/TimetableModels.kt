package ecccomp.s2240788.mobile_android.data.models

/**
 * Data class for Class/Subject in Timetable
 */
data class ClassModel(
    val id: String,
    val name: String,
    val room: String,
    val day: Int, // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
    val period: Int, // 1-5
    val color: ClassColor,
    val teacher: String? = null,
    val notes: String? = null,
    val reminderMinutes: Int? = null // Minutes before class to remind
)

/**
 * Enum for class color coding
 */
enum class ClassColor(val colorResId: Int) {
    PRIMARY(ecccomp.s2240788.mobile_android.R.color.primary),
    ACCENT(ecccomp.s2240788.mobile_android.R.color.accent),
    SUCCESS(ecccomp.s2240788.mobile_android.R.color.success),
    WARNING(ecccomp.s2240788.mobile_android.R.color.warning),
    DANGER(ecccomp.s2240788.mobile_android.R.color.error)
}

/**
 * Data class for Study (Homework/Review/Exam)
 */
data class StudyModel(
    val id: String,
    val title: String,
    val type: StudyType,
    val subject: String,
    val dueDate: String? = null,
    val priority: Priority,
    val description: String? = null,
    val completed: Boolean = false,
    val createdAt: Long = System.currentTimeMillis()
)

/**
 * Enum for study type
 */
enum class StudyType {
    HOMEWORK,
    REVIEW,
    EXAM
}

/**
 * Enum for priority level
 */
enum class Priority {
    LOW,
    MEDIUM,
    HIGH
}

/**
 * Data class for Timetable
 */
data class Timetable(
    val classes: List<ClassModel>,
    val studies: List<StudyModel>
)
