package ecccomp.s2240788.mobile_android.data.models

import com.google.gson.annotations.SerializedName

// ==================== Task Abandonment Models ====================

data class TaskAbandonment(
    val id: Int,
    val user_id: Int,
    val task_id: Int,
    val focus_session_id: Int?,

    // Timing
    val started_at: String,
    val last_active_at: String,
    val abandoned_at: String,
    val duration_minutes: Int,

    // Abandonment details
    val abandonment_type: String, // app_switched, long_inactivity, manual, deadline_passed
    val inactivity_minutes: Int?,
    val auto_detected: Boolean = true,
    val reason: String?,

    // Resume tracking
    val resumed: Boolean = false,
    val resumed_at: String?,

    val created_at: String,
    val updated_at: String,

    // Relations
    val task: Task? = null
)

// Abandonment list response (paginated)
data class AbandonmentListResponse(
    val current_page: Int,
    val data: List<TaskAbandonment>,
    val total: Int,
    val per_page: Int,
    val last_page: Int
)

// Abandonment stats response
data class AbandonmentStatsResponse(
    val total_abandonments: Int,
    val auto_detected_count: Int,
    val manual_count: Int,
    val resumed_count: Int,
    val resume_rate: Double, // percentage
    val abandonment_rate: Double, // percentage
    val average_work_time_minutes: Int,
    val most_common_type: String?,
    val by_type: Map<String, Int>,
    val period_days: Int
)

// Abandon task request
data class AbandonTaskRequest(
    val reason: String? = null,
    val abandonment_type: String = "manual" // manual, app_switched, deadline_passed
)

// Task with abandonment info
data class TaskWithAbandonmentInfo(
    val id: Int,
    val title: String,
    val status: String,
    val last_active_at: String?,
    val is_abandoned: Boolean = false,
    val abandonment_count: Int = 0
)
