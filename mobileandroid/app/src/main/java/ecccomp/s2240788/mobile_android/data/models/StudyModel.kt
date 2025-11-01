package ecccomp.s2240788.mobile_android.data.models

/**
 * StudyModel - Temporary compatibility model for StudyAdapter
 * 学習課題表示用の互換モデル
 */
data class StudyModel(
    val id: String,
    val title: String,
    val type: StudyType,
    val subject: String?,
    val dueDate: String?,
    val priority: Int = 3,
    val completed: Boolean = false
)

/**
 * Study Type enum
 */
enum class StudyType {
    HOMEWORK,     // 宿題
    REVIEW,       // 復習
    EXAM,         // 試験
    PROJECT       // プロジェクト
}

/**
 * Convert TimetableStudy to StudyModel for adapter compatibility
 * TimetableStudy を StudyModel に変換
 */
fun TimetableStudy.toStudyModel(): StudyModel {
    val studyType = when (type.lowercase()) {
        "homework" -> StudyType.HOMEWORK
        "review" -> StudyType.REVIEW
        "exam" -> StudyType.EXAM
        "project" -> StudyType.PROJECT
        else -> StudyType.HOMEWORK
    }
    
    return StudyModel(
        id = id.toString(),
        title = title,
        type = studyType,
        subject = subject,
        dueDate = dueDate,
        priority = priority,
        completed = status == "completed"
    )
}

