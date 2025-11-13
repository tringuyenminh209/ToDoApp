package ecccomp.s2240788.mobile_android.data.models

import com.google.gson.annotations.SerializedName

/**
 * Roadmap API Models
 * ロードマップAPIモデル
 */

// Popular Roadmap
data class PopularRoadmap(
    @SerializedName("id") val id: String,
    @SerializedName("title") val title: String,
    @SerializedName("description") val description: String?,
    @SerializedName("category") val category: String,
    @SerializedName("difficulty") val difficulty: String, // beginner, intermediate, advanced
    @SerializedName("estimated_hours") val estimatedHours: Int?,
    @SerializedName("url") val url: String?,
    @SerializedName("icon") val icon: String?
)

// Roadmap List Response
data class RoadmapListResponse(
    @SerializedName("success") val success: Boolean,
    @SerializedName("data") val data: List<PopularRoadmap>,
    @SerializedName("message") val message: String?
)

// Generate Roadmap Request
data class GenerateRoadmapRequest(
    @SerializedName("topic") val topic: String,
    @SerializedName("level") val level: String = "beginner" // beginner, intermediate, advanced
)

// Generate Roadmap Response
data class RoadmapGenerateResponse(
    @SerializedName("success") val success: Boolean,
    @SerializedName("data") val data: GeneratedRoadmap,
    @SerializedName("message") val message: String?
)

data class GeneratedRoadmap(
    @SerializedName("title") val title: String,
    @SerializedName("description") val description: String?,
    @SerializedName("category") val category: String,
    @SerializedName("difficulty") val difficulty: String,
    @SerializedName("estimated_hours") val estimatedHours: Int,
    @SerializedName("milestones") val milestones: List<RoadmapMilestone>
)

data class RoadmapMilestone(
    @SerializedName("title") val title: String,
    @SerializedName("description") val description: String?,
    @SerializedName("sort_order") val sortOrder: Int,
    @SerializedName("estimated_hours") val estimatedHours: Int,
    @SerializedName("tasks") val tasks: List<RoadmapTask>
)

data class RoadmapTask(
    @SerializedName("title") val title: String,
    @SerializedName("description") val description: String?,
    @SerializedName("estimated_minutes") val estimatedMinutes: Int,
    @SerializedName("priority") val priority: Int
)

// Import Roadmap Request (WITH MANDATORY STUDY SCHEDULE)
data class ImportRoadmapRequest(
    @SerializedName("roadmap_id") val roadmapId: String? = null,
    @SerializedName("topic") val topic: String? = null,
    @SerializedName("level") val level: String = "beginner",
    @SerializedName("source") val source: String, // popular, ai, microsoft_learn
    @SerializedName("auto_clone") val autoClone: Boolean = true,
    @SerializedName("study_schedules") val studySchedules: List<StudyScheduleInput> // REQUIRED when auto_clone=true
)

// Import Roadmap Response
data class ImportRoadmapResponse(
    @SerializedName("success") val success: Boolean,
    @SerializedName("data") val data: ImportRoadmapData,
    @SerializedName("message") val message: String?
)

data class ImportRoadmapData(
    @SerializedName("template") val template: LearningPathTemplate,
    @SerializedName("learning_path") val learningPath: LearningPath?,
    @SerializedName("learning_path_id") val learningPathId: Long?,
    @SerializedName("study_schedules") val studySchedules: List<StudySchedule>?,
    @SerializedName("weekly_schedule") val weeklySchedule: Map<String, List<WeeklyScheduleItem>>?
)

data class WeeklyScheduleItem(
    @SerializedName("time") val time: String,
    @SerializedName("duration") val duration: Int,
    @SerializedName("day_name") val dayName: String
)

/**
 * Helper extension functions
 */
fun PopularRoadmap.getDifficultyColor(): String {
    return when (difficulty.lowercase()) {
        "beginner" -> "#4CAF50"
        "intermediate" -> "#FF9800"
        "advanced" -> "#F44336"
        else -> "#9E9E9E"
    }
}

fun PopularRoadmap.getDifficultyDisplay(): String {
    return when (difficulty.lowercase()) {
        "beginner" -> "初級"
        "intermediate" -> "中級"
        "advanced" -> "上級"
        else -> "不明"
    }
}

fun PopularRoadmap.getCategoryDisplay(): String {
    return when (category.lowercase()) {
        "programming" -> "プログラミング"
        "design" -> "デザイン"
        "business" -> "ビジネス"
        "language" -> "語学"
        "data_science" -> "データサイエンス"
        else -> "その他"
    }
}

fun PopularRoadmap.getFormattedHours(): String {
    val hours = estimatedHours ?: 0
    return when {
        hours < 10 -> "${hours}時間"
        hours < 100 -> "${hours}時間"
        else -> "${hours / 10 * 10}時間以上"
    }
}
