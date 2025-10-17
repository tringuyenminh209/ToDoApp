package ecccomp.s2240788.mobile_android.data.models

data class Task(
    val id: Int,
    val title: String,
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
    val ai_breakdown_enabled: Boolean
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

data class CreateTaskRequest(
    val title: String,
    val description: String?,
    val priority: Int,
    val energy_level: String,
    val estimated_minutes: Int?,
    val deadline: String?
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
