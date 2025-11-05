package ecccomp.s2240788.mobile_android.data.models

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
    val created_at: String,
    val updated_at: String,
    val user_id: Int,
    val project_id: Int?,
    val learning_milestone_id: Int?,
    val ai_breakdown_enabled: Boolean,
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
    val deadline: String?
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
