package ecccomp.s2240788.mobile_android.data.models

import com.google.gson.annotations.SerializedName

/**
 * StudySchedule Model
 * スケジュール学習モデル
 *
 * Purpose: Enforce study discipline with scheduled learning times
 */
data class StudySchedule(
    val id: Int,
    val learning_path_id: Int,
    val study_time: String, // HH:MM:SS format
    val day_of_week: Int, // 0=Sunday, 1=Monday, ..., 6=Saturday
    val duration_minutes: Int,
    val is_active: Boolean,
    val reminder_enabled: Boolean,
    val reminder_before_minutes: Int,
    val completed_sessions: Int,
    val missed_sessions: Int,
    val last_studied_at: String?,
    val created_at: String,
    val updated_at: String
) {
    /**
     * Get day name in Vietnamese
     */
    fun getDayNameVietnamese(): String {
        return when (day_of_week) {
            0 -> "Chủ Nhật"
            1 -> "Thứ Hai"
            2 -> "Thứ Ba"
            3 -> "Thứ Tư"
            4 -> "Thứ Năm"
            5 -> "Thứ Sáu"
            6 -> "Thứ Bảy"
            else -> "Không xác định"
        }
    }

    /**
     * Get day name short form
     */
    fun getDayNameShort(): String {
        return when (day_of_week) {
            0 -> "CN"
            1 -> "T2"
            2 -> "T3"
            3 -> "T4"
            4 -> "T5"
            5 -> "T6"
            6 -> "T7"
            else -> "?"
        }
    }

    /**
     * Get formatted study time (HH:MM)
     */
    fun getFormattedTime(): String {
        return study_time.substring(0, 5) // Extract HH:MM from HH:MM:SS
    }

    /**
     * Calculate completion rate
     */
    fun getCompletionRate(): Float {
        val total = completed_sessions + missed_sessions
        if (total == 0) return 0f
        return (completed_sessions.toFloat() / total) * 100
    }

    /**
     * Get consistency score (0-100)
     */
    fun getConsistencyScore(): Int {
        val completionRate = getCompletionRate()
        val penalty = minOf(missed_sessions * 2, 20)
        return maxOf(0, minOf(100, (completionRate - penalty).toInt()))
    }
}

/**
 * Request to create a new study schedule
 */
data class CreateStudyScheduleRequest(
    val day_of_week: Int, // 0-6
    val study_time: String, // HH:MM format
    val duration_minutes: Int = 60,
    val reminder_enabled: Boolean = true,
    val reminder_before_minutes: Int = 30
)

/**
 * Request to update study schedule
 */
data class UpdateStudyScheduleRequest(
    val day_of_week: Int? = null,
    val study_time: String? = null, // HH:MM format
    val duration_minutes: Int? = null,
    val is_active: Boolean? = null,
    val reminder_enabled: Boolean? = null,
    val reminder_before_minutes: Int? = null
)

/**
 * Study schedule for roadmap import
 */
data class StudyScheduleInput(
    val day_of_week: Int,
    val study_time: String, // HH:MM format
    val duration_minutes: Int = 60,
    val reminder_enabled: Boolean = true,
    val reminder_before_minutes: Int = 30
)

/**
 * Weekly schedule summary
 */
data class WeeklyScheduleSummary(
    val schedules: List<StudySchedule>,
    val weekly_hours: Float,
    val total_schedules: Int
)

/**
 * Study schedule statistics
 */
data class StudyScheduleStats(
    val total_schedules: Int,
    val active_schedules: Int,
    val total_completed: Int,
    val total_missed: Int,
    val average_completion_rate: Float,
    val average_consistency_score: Float,
    val weekly_study_hours: Float
)

/**
 * Today's session info
 */
data class TodaySessionsResponse(
    val all_sessions: List<StudyScheduleWithPath>,
    val upcoming_sessions: List<StudyScheduleWithPath>,
    val total_study_minutes: Int
)

/**
 * Study schedule with learning path info
 */
data class StudyScheduleWithPath(
    val id: Int,
    val learning_path_id: Int,
    val study_time: String,
    val day_of_week: Int,
    val duration_minutes: Int,
    val is_active: Boolean,
    val completed_sessions: Int,
    val missed_sessions: Int,
    val learning_path: LearningPathBasic? = null
) {
    fun getFormattedTime(): String {
        return study_time.substring(0, 5)
    }

    fun getDayNameVietnamese(): String {
        return when (day_of_week) {
            0 -> "Chủ Nhật"
            1 -> "Thứ Hai"
            2 -> "Thứ Ba"
            3 -> "Thứ Tư"
            4 -> "Thứ Năm"
            5 -> "Thứ Sáu"
            6 -> "Thứ Bảy"
            else -> "Không xác định"
        }
    }

    /**
     * Get day name in Japanese
     */
    fun getDayNameJapanese(): String {
        return when (day_of_week) {
            0 -> "日"
            1 -> "月"
            2 -> "火"
            3 -> "水"
            4 -> "木"
            5 -> "金"
            6 -> "土"
            else -> "?"
        }
    }
}

/**
 * Basic learning path info for schedule display
 */
data class LearningPathBasic(
    val id: Int,
    val title: String,
    val status: String
)

/**
 * Day selector item for UI
 */
data class DayItem(
    val dayOfWeek: Int,
    val dayName: String,
    val dayShort: String,
    var isSelected: Boolean = false
)

/**
 * Helper to create day items for UI
 */
object DayItemHelper {
    fun getAllDays(): List<DayItem> {
        return listOf(
            DayItem(1, "月曜日", "月"),
            DayItem(2, "火曜日", "火"),
            DayItem(3, "水曜日", "水"),
            DayItem(4, "木曜日", "木"),
            DayItem(5, "金曜日", "金"),
            DayItem(6, "土曜日", "土"),
            DayItem(0, "日曜日", "日")
        )
    }
}

/**
 * Timeline Item - Unified format for both Study Schedules and Timetable Classes
 * タイムラインアイテム - スケジュールと時間割の統合フォーマット
 *
 * Combines both Study Schedules and Timetable Classes into a single format for timeline display
 */
data class TimelineItem(
    val id: String,                           // "study_123" or "class_456"
    val type: String,                         // "study_schedule" or "timetable_class"
    val title: String,                        // Display title with emoji
    val day_of_week: Int,                     // 0-6 (Sunday-Saturday)
    val scheduled_time: String,               // HH:mm:ss format
    val duration_minutes: Int,                // Duration in minutes
    val category: String,                     // "study" or "class"

    // Optional fields for timetable classes
    val room: String? = null,
    val instructor: String? = null,
    val period: Int? = null,
    val color: String? = null,
    val icon: String? = null,

    // Learning path reference (for both types)
    val learning_path_id: Int? = null,
    val learning_path: LearningPathBasic? = null
) {
    /**
     * Convert to Task object for timeline adapter compatibility
     */
    fun toTask(selectedDate: String): Task {
        // Extract hour from scheduled_time for timeline display
        val hour = try {
            scheduled_time.split(":")[0].toIntOrNull() ?: -1
        } catch (e: Exception) {
            -1
        }

        return Task(
            id = if (type == "study_schedule") {
                -id.substringAfter("_").toIntOrNull() ?: 0
            } else {
                -10000 - (id.substringAfter("_").toIntOrNull() ?: 0)
            },
            title = title,
            category = category,
            description = when (type) {
                "study_schedule" -> "学習セッション: ${duration_minutes}分"
                "timetable_class" -> buildString {
                    append("授業")
                    period?.let { append(" - 第${it}時限") }
                    room?.let { append(" - ${it}") }
                    instructor?.let { append(" - ${it}") }
                    append(" - ${duration_minutes}分")
                }
                else -> ""
            },
            status = "pending",
            priority = if (type == "timetable_class") 5 else 4,
            energy_level = "medium",
            estimated_minutes = duration_minutes,
            deadline = selectedDate,
            scheduled_time = scheduled_time,
            created_at = "",
            updated_at = "",
            user_id = 0,
            project_id = null,
            learning_milestone_id = learning_path_id,
            ai_breakdown_enabled = false,
            subtasks = null,
            knowledge_items = null
        )
    }

    /**
     * Get day name in Japanese
     */
    fun getDayNameJapanese(): String {
        return when (day_of_week) {
            0 -> "日"
            1 -> "月"
            2 -> "火"
            3 -> "水"
            4 -> "木"
            5 -> "金"
            6 -> "土"
            else -> "?"
        }
    }

    /**
     * Get formatted time (HH:mm)
     */
    fun getFormattedTime(): String {
        return try {
            scheduled_time.substring(0, 5)
        } catch (e: Exception) {
            scheduled_time
        }
    }
}
