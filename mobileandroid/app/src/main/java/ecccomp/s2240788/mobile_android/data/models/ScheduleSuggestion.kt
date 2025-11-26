package ecccomp.s2240788.mobile_android.data.models

import com.google.gson.annotations.SerializedName

/**
 * Response from GET /api/tasks/{id}/suggest-schedule
 */
data class ScheduleSuggestionsResponse(
    @SerializedName("task")
    val task: TaskInfo,

    @SerializedName("suggestions")
    val suggestions: List<ScheduleSuggestion>,

    @SerializedName("days_searched")
    val daysSearched: Int
)

/**
 * Task information included in schedule suggestions response
 */
data class TaskInfo(
    @SerializedName("id")
    val id: Int,

    @SerializedName("title")
    val title: String,

    @SerializedName("estimated_minutes")
    val estimatedMinutes: Int?,

    @SerializedName("priority")
    val priority: Int?,

    @SerializedName("deadline")
    val deadline: String?
)

/**
 * Individual schedule suggestion with scoring and reasoning
 */
data class ScheduleSuggestion(
    @SerializedName("date")
    val date: String,              // "2025-11-26"

    @SerializedName("day")
    val day: String,               // "wednesday"

    @SerializedName("start_time")
    val startTime: String,        // "14:00:00"

    @SerializedName("end_time")
    val endTime: String,          // "16:00:00"

    @SerializedName("duration_minutes")
    val durationMinutes: Int,     // 120

    @SerializedName("score")
    val score: Double,             // 4.25

    @SerializedName("reasons")
    val reasons: List<String>,     // ["High priority task", "Optimal time of day"]

    @SerializedName("confidence")
    val confidence: String         // "high" | "medium" | "low"
)
