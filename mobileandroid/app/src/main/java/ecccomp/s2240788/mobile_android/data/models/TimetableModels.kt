package ecccomp.s2240788.mobile_android.data.models

import com.google.gson.annotations.SerializedName
import java.io.Serializable

/**
 * Timetable Response from API
 */
data class TimetableResponse(
    @SerializedName("classes") val classes: List<TimetableClass>,
    @SerializedName("studies") val studies: List<TimetableStudy>,
    @SerializedName("current_class") val currentClass: TimetableClass? = null,
    @SerializedName("next_class") val nextClass: TimetableClass? = null,
    @SerializedName("current_time") val currentTime: String? = null,
    @SerializedName("current_day") val currentDay: String? = null,
    @SerializedName("year") val year: Int? = null,
    @SerializedName("week_number") val weekNumber: Int? = null
)

/**
 * Timetable Class (授業)
 */
data class TimetableClass(
    @SerializedName("id") val id: Int,
    @SerializedName("user_id") val userId: Int,
    @SerializedName("name") val name: String,
    @SerializedName("description") val description: String? = null,
    @SerializedName("room") val room: String? = null,
    @SerializedName("instructor") val instructor: String? = null,
    @SerializedName("day") val day: String, // monday, tuesday, etc.
    @SerializedName("period") val period: Int, // 1-10
    @SerializedName("start_time") val startTime: String, // HH:mm
    @SerializedName("end_time") val endTime: String, // HH:mm
    @SerializedName("color") val color: String = "#4F46E5",
    @SerializedName("icon") val icon: String? = null,
    @SerializedName("notes") val notes: String? = null,
    @SerializedName("learning_path_id") val learningPathId: Int? = null,
    @SerializedName("weekly_content") val weeklyContent: TimetableClassWeeklyContent? = null,
    @SerializedName("created_at") val createdAt: String,
    @SerializedName("updated_at") val updatedAt: String
) : Serializable

/**
 * Timetable Class Weekly Content (週別授業内容)
 */
data class TimetableClassWeeklyContent(
    @SerializedName("id") val id: Int,
    @SerializedName("timetable_class_id") val timetableClassId: Int,
    @SerializedName("year") val year: Int,
    @SerializedName("week_number") val weekNumber: Int,
    @SerializedName("week_start_date") val weekStartDate: String,
    @SerializedName("title") val title: String? = null,
    @SerializedName("content") val content: String? = null,
    @SerializedName("homework") val homework: String? = null,
    @SerializedName("notes") val notes: String? = null,
    @SerializedName("status") val status: String = "scheduled", // scheduled, completed, cancelled
    @SerializedName("created_at") val createdAt: String,
    @SerializedName("updated_at") val updatedAt: String
) : Serializable

/**
 * Timetable Study (宿題・復習)
 */
data class TimetableStudy(
    @SerializedName("id") val id: Int,
    @SerializedName("user_id") val userId: Int,
    @SerializedName("timetable_class_id") val timetableClassId: Int? = null,
    @SerializedName("title") val title: String,
    @SerializedName("description") val description: String? = null,
    @SerializedName("type") val type: String, // homework, review, exam, project
    @SerializedName("subject") val subject: String? = null,
    @SerializedName("due_date") val dueDate: String? = null,
    @SerializedName("priority") val priority: Int = 3, // 1-5
    @SerializedName("status") val status: String = "pending", // pending, in_progress, completed
    @SerializedName("completed_at") val completedAt: String? = null,
    @SerializedName("task_id") val taskId: Int? = null,
    @SerializedName("created_at") val createdAt: String,
    @SerializedName("updated_at") val updatedAt: String
)

/**
 * Create Timetable Class Request
 */
data class CreateTimetableClassRequest(
    @SerializedName("name") val name: String,
    @SerializedName("description") val description: String? = null,
    @SerializedName("room") val room: String? = null,
    @SerializedName("instructor") val instructor: String? = null,
    @SerializedName("day") val day: String,
    @SerializedName("period") val period: Int,
    @SerializedName("start_time") val startTime: String,
    @SerializedName("end_time") val endTime: String,
    @SerializedName("color") val color: String? = null,
    @SerializedName("icon") val icon: String? = null,
    @SerializedName("notes") val notes: String? = null,
    @SerializedName("learning_path_id") val learningPathId: Int? = null
)

/**
 * Create Timetable Study Request
 */
data class CreateTimetableStudyRequest(
    @SerializedName("title") val title: String,
    @SerializedName("description") val description: String? = null,
    @SerializedName("type") val type: String,
    @SerializedName("subject") val subject: String? = null,
    @SerializedName("due_date") val dueDate: String? = null,
    @SerializedName("priority") val priority: Int = 3,
    @SerializedName("timetable_class_id") val timetableClassId: Int? = null,
    @SerializedName("task_id") val taskId: Int? = null
)

/**
 * Update Weekly Content Request
 */
data class UpdateWeeklyContentRequest(
    @SerializedName("year") val year: Int,
    @SerializedName("week_number") val weekNumber: Int,
    @SerializedName("week_start_date") val weekStartDate: String,
    @SerializedName("title") val title: String? = null,
    @SerializedName("content") val content: String? = null,
    @SerializedName("homework") val homework: String? = null,
    @SerializedName("notes") val notes: String? = null,
    @SerializedName("status") val status: String? = null
)

/**
 * Stats Dashboard Response
 */
data class StatsDashboard(
    @SerializedName("tasks") val tasks: TasksSummary,
    @SerializedName("sessions") val sessions: SessionsSummary,
    @SerializedName("projects") val projects: ProjectsSummary? = null,
    @SerializedName("performance") val performance: PerformanceSummary,
    @SerializedName("generated_at") val generatedAt: String
)

data class TasksSummary(
    @SerializedName("today") val today: Map<String, Int>,
    @SerializedName("this_week") val thisWeek: Map<String, Int>,
    @SerializedName("this_month") val thisMonth: Map<String, Int>
)

data class SessionsSummary(
    @SerializedName("today") val today: Map<String, Int>,
    @SerializedName("this_week") val thisWeek: Map<String, Int>,
    @SerializedName("this_month") val thisMonth: Map<String, Int>
)

data class ProjectsSummary(
    @SerializedName("total") val total: Int,
    @SerializedName("active") val active: Int,
    @SerializedName("completed") val completed: Int,
    @SerializedName("average_progress") val averageProgress: Double
)

data class PerformanceSummary(
    @SerializedName("current_score") val currentScore: Double,
    @SerializedName("average_score") val averageScore: Double,
    @SerializedName("trend") val trend: String, // up, down, stable
    @SerializedName("recent_scores") val recentScores: List<Double>
)

/**
 * Tasks Stats Response
 */
data class TasksStats(
    @SerializedName("total_tasks") val totalTasks: Int,
    @SerializedName("completed_tasks") val completedTasks: Int,
    @SerializedName("in_progress_tasks") val inProgressTasks: Int,
    @SerializedName("pending_tasks") val pendingTasks: Int,
    @SerializedName("overdue_tasks") val overdueTasks: Int,
    @SerializedName("completion_rate") val completionRate: Double,
    @SerializedName("average_completion_time") val averageCompletionTime: Double,
    @SerializedName("priority_distribution") val priorityDistribution: Map<String, Int>,
    @SerializedName("energy_level_distribution") val energyLevelDistribution: Map<String, Int>
)

/**
 * Sessions Stats Response
 */
data class SessionsStats(
    @SerializedName("total_sessions") val totalSessions: Int,
    @SerializedName("total_minutes") val totalMinutes: Int,
    @SerializedName("total_hours") val totalHours: Double,
    @SerializedName("average_session_duration") val averageSessionDuration: Double,
    @SerializedName("session_types") val sessionTypes: Map<String, Int>,
    @SerializedName("daily_average") val dailyAverage: Double,
    @SerializedName("efficiency_score") val efficiencyScore: Double
)

/**
 * Trends Data Response
 */
data class TrendsData(
    @SerializedName("data") val data: List<TrendPoint>
)

data class TrendPoint(
    @SerializedName("date") val date: String,
    @SerializedName("value") val value: Any // Can be Int, Double, or Map
)
