package ecccomp.s2240788.mobile_android.data.models

import com.google.gson.annotations.SerializedName

data class Task(
    val id: Int,
    val title: String,
    val category: String?, // study, work, personal, other
    val description: String?,
    val status: String,
    val priority: Int,  // 1-5
    val energy_level: String,
    val estimated_minutes: Int?,
    val deadline: String?,
    val scheduled_time: String?, // 予定開始時刻
    val created_at: String,
    val updated_at: String,
    val user_id: Int,
    val project_id: Int?,
    val learning_milestone_id: Int?,
    val ai_breakdown_enabled: Boolean,
    // Focus enhancement features
    val requires_deep_focus: Boolean = false,
    val allow_interruptions: Boolean = true,
    val focus_difficulty: Int = 3, // 1-5: shallow to ultra-deep
    val warmup_minutes: Int? = null,
    val cooldown_minutes: Int? = null,
    val recovery_minutes: Int? = null,
    val last_focus_at: String? = null,
    val total_focus_minutes: Int = 0,
    val distraction_count: Int = 0,
    // Relations
    val subtasks: List<Subtask>? = null,
    val knowledge_items: List<KnowledgeItem>? = null
)

data class Subtask(
    val id: Int,
    val task_id: Int,
    val title: String,
    val is_completed: Boolean,
    val estimated_minutes: Int?,
    val sort_order: Int,
    val created_at: String,
    val updated_at: String
)

// Request models
data class LoginRequest(
    val email: String,
    val password: String
)

data class RegisterRequest(
    val name: String,
    val email: String,
    val password: String
)

data class ForgotPasswordRequest(
    val email: String
)

data class ResetPasswordRequest(
    val email: String,
    val token: String,
    val password: String,
    val password_confirmation: String
)

data class CreateTaskRequest(
    val title: String,
    val category: String?, // study, work, personal, other
    val description: String?,
    val priority: Int,
    val energy_level: String,
    val estimated_minutes: Int?,
    val deadline: String?,
    val scheduled_time: String?, // 予定開始時刻
    // Focus enhancement features
    val requires_deep_focus: Boolean = false,
    val allow_interruptions: Boolean = true,
    val focus_difficulty: Int = 3,
    val warmup_minutes: Int? = null,
    val cooldown_minutes: Int? = null,
    val recovery_minutes: Int? = null
)

data class CreateSubtaskRequest(
    val title: String,
    val estimated_minutes: Int?,
    val sort_order: Int?
)

data class StartFocusSessionRequest(
    val task_id: Int,
    val duration_minutes: Int,
    val session_type: String // "work", "break", "long_break"
)

data class FocusSession(
    val id: Int,
    val task_id: Int,
    val user_id: Int,
    val duration_minutes: Int,
    val notes: String?,
    val completed_at: String,
    val created_at: String
)

// Learning Path Models
data class LearningPath(
    val id: Int,
    val user_id: Int,
    val title: String,
    val description: String?,
    val category: String?,
    val status: String, // active, completed, paused
    val progress_percentage: Int,
    val total_milestones: Int,
    val completed_milestones: Int,
    val target_date: String?,
    val icon: String? = null,
    val color: String? = null,
    val created_at: String,
    val updated_at: String,
    val milestones: List<LearningMilestone>? = null
)

data class LearningMilestone(
    val id: Int,
    val learning_path_id: Int,
    val title: String,
    val description: String?,
    val status: String, // not_started, in_progress, completed
    val order_index: Int,
    val completed_at: String?,
    val created_at: String,
    val updated_at: String,
    val tasks: List<Task>? = null
)

data class CreateLearningPathRequest(
    val title: String,
    val description: String?,
    val goal_type: String,
    val target_start_date: String?,
    val target_end_date: String?,
    val estimated_hours_total: Int?,
    val tags: List<String>? = null,
    val color: String? = null,
    val icon: String? = null
)

// Statistics Models
data class UserStats(
    val total_tasks: Int,
    val completed_tasks: Int,
    val pending_tasks: Int,
    val in_progress_tasks: Int,
    val completion_rate: Float,
    val total_focus_time: Int, // minutes
    val total_focus_sessions: Int,
    val average_session_duration: Int, // minutes
    val current_streak: Int, // days
    val longest_streak: Int, // days
    val tasks_by_priority: TasksByPriority,
    val weekly_stats: WeeklyStats,
    val monthly_productivity: List<DailyProductivity>
)

data class TasksByPriority(
    val high: Int,
    val medium: Int,
    val low: Int
)

data class WeeklyStats(
    val tasks_completed: Int,
    val focus_time: Int, // minutes
    val days_active: Int
)

data class DailyProductivity(
    val date: String, // YYYY-MM-DD
    val tasks_completed: Int,
    val focus_minutes: Int
)

//Response models
data class ApiResponse<T>(
    val success: Boolean,
    val data: T?,
    val message: String,
    val error: String?
)

data class AuthResponse(
    val user: User,
    val token: String,
    val message: String
)

// Focus Enhancement Models
data class FocusEnvironment(
    val id: Int,
    val task_id: Int,
    val user_id: Int,
    val focus_session_id: Int?,
    val quiet_space: Boolean = false,
    val phone_silent: Boolean = false,
    val materials_ready: Boolean = false,
    val water_coffee_ready: Boolean = false,
    val comfortable_position: Boolean = false,
    val notifications_off: Boolean = false,
    val apps_closed: List<String>? = null,
    val all_checks_passed: Boolean = false,
    val notes: String? = null,
    val created_at: String,
    val updated_at: String
)

data class SaveEnvironmentCheckRequest(
    val task_id: Int,
    val focus_session_id: Int? = null,
    val quiet_space: Boolean = false,
    val phone_silent: Boolean = false,
    val materials_ready: Boolean = false,
    val water_coffee_ready: Boolean = false,
    val comfortable_position: Boolean = false,
    val notifications_off: Boolean = false,
    val apps_closed: List<String>? = null,
    val notes: String? = null
)

data class DistractionLog(
    val id: Int,
    val task_id: Int,
    val user_id: Int,
    val focus_session_id: Int?,
    val distraction_type: String, // phone, social_media, noise, person, thoughts, hunger_thirst, fatigue, other
    val duration_seconds: Int?,
    val notes: String?,
    val occurred_at: String,
    val time_of_day: String?,
    val created_at: String,
    val updated_at: String
)

data class LogDistractionRequest(
    val task_id: Int,
    val focus_session_id: Int? = null,
    val distraction_type: String,
    val duration_seconds: Int? = null,
    val notes: String? = null
)

data class DistractionAnalytics(
    val total_distractions: Int,
    val average_duration_seconds: Int,
    val top_distractions: List<DistractionStat>,
    val by_time_of_day: List<TimeOfDayStat>,
    val period_days: Int
)

data class DistractionStat(
    val distraction_type: String,
    val count: Int,
    val total_duration: Int?
)

data class TimeOfDayStat(
    val hour: Int,
    val count: Int
)

data class ContextSwitch(
    val id: Int,
    val user_id: Int,
    val from_task_id: Int?,
    val from_category: String?,
    val from_focus_difficulty: Int?,
    val to_task_id: Int,
    val to_category: String?,
    val to_focus_difficulty: Int?,
    val is_significant_switch: Boolean,
    val estimated_cost_minutes: Int,
    val user_proceeded: Boolean,
    val user_note: String?,
    val created_at: String,
    val updated_at: String
)

data class CheckContextSwitchRequest(
    val from_task_id: Int? = null,
    val to_task_id: Int
)

data class ContextSwitchResponse(
    val context_switch: ContextSwitch,
    val should_warn: Boolean,
    val warning_message: String?
)

data class ContextSwitchAnalytics(
    val total_switches: Int,
    val significant_switches: Int,
    val average_cost_minutes: Int,
    val total_cost_minutes: Int,
    val total_cost_hours: Double,
    val common_patterns: List<SwitchPattern>,
    val period_days: Int
)

data class SwitchPattern(
    val from_category: String?,
    val to_category: String?,
    val count: Int
)

// ==================== Chat AI Models ====================

data class ChatConversation(
    val id: Long,
    val user_id: Long,
    val title: String?,
    val status: String, // "active" or "archived"
    val last_message_at: String?,
    val message_count: Int,
    val created_at: String,
    val updated_at: String,
    val messages: List<ChatMessage>? = null
)

data class ChatMessage(
    val id: Long,
    val conversation_id: Long,
    val user_id: Long? = null,
    val role: String, // "user", "assistant", or "system"
    val content: String,
    val metadata: Map<String, Any>? = null,
    val token_count: Int? = null,
    val created_at: String,
    val updated_at: String? = null
)

data class CreateConversationRequest(
    val title: String? = null,
    val message: String
)

data class SendMessageRequest(
    val message: String
)

data class UpdateConversationRequest(
    val title: String? = null,
    val status: String? = null
)

data class ChatConversationsResponse(
    val current_page: Int,
    val data: List<ChatConversation>,
    val total: Int,
    val per_page: Int
)

data class CreateConversationResponse(
    val conversation: ChatConversation,
    val created_task: Task? = null
)

// Task Suggestion from AI (not auto-created, requires user confirmation)
data class TaskSuggestion(
    val title: String,
    val description: String?,
    val estimated_minutes: Int?,
    val priority: String, // "high", "medium", "low"
    val scheduled_time: String?,
    val reason: String // Why AI suggests this task
)

data class SendMessageResponse(
    val user_message: ChatMessage,
    val assistant_message: ChatMessage,
    val created_task: Task? = null,
    val task_suggestion: TaskSuggestion? = null // New field for AI suggestions
)

// ==================== AI Task Breakdown Models ====================

data class BreakdownTaskRequest(
    val task_id: Int,
    val complexity_level: String = "medium" // "simple", "medium", "complex"
)