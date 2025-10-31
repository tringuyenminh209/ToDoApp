package ecccomp.s2240788.mobile_android.data.api

import ecccomp.s2240788.mobile_android.data.models.*
import retrofit2.Response
import retrofit2.http.*
interface ApiService {

    //Authentication
    @POST("register")
    suspend fun register(@Body request: RegisterRequest): Response<AuthResponse>

    @POST("login")
    suspend fun login(@Body request: LoginRequest): Response<AuthResponse>

    @GET("user")
    suspend fun getUser(): Response<ApiResponse<User>>

    @POST("logout")
    suspend fun logout(): Response<Map<String, Any>>

    @POST("forgot-password")
    suspend fun forgotPassword(@Body request: ForgotPasswordRequest): Response<Map<String, String>>

    @POST("reset-password")
    suspend fun resetPassword(@Body request: ResetPasswordRequest): Response<Map<String, String>>

    @POST("refresh-token")
    suspend fun refreshToken(): Response<AuthResponse>

    @POST("email/verification-notification")
    suspend fun resendVerificationEmail(): Response<Map<String, String>>

    @GET("email/verify/{id}/{hash}")
    suspend fun verifyEmail(@Path("id") id: Int, @Path("hash") hash: String): Response<Map<String, String>>

    //Tasks
    @GET("tasks")
    suspend fun getTasks(): Response<ApiResponse<Any>>

    @POST("tasks")
    suspend fun createTask(@Body task: CreateTaskRequest): Response<ApiResponse<Task>>

    @PUT("tasks/{id}")
    suspend fun updateTask(@Path("id") id: Int, @Body task: CreateTaskRequest): Response<ApiResponse<Task>>

    @DELETE("tasks/{id}")
    suspend fun deleteTask(@Path("id") id: Int): Response<ApiResponse<Unit>>

    @PUT("tasks/{id}/complete")
    suspend fun completeTask(@Path("id") id: Int): Response<ApiResponse<Task>>

    @PUT("tasks/{id}/start")
    suspend fun startTask(@Path("id") id: Int): Response<ApiResponse<Task>>

    // Subtasks
    @GET("tasks/{taskId}/subtasks")
    suspend fun getSubtasks(@Path("taskId") taskId: Int): Response<ApiResponse<List<Subtask>>>

    @POST("tasks/{taskId}/subtasks")
    suspend fun createSubtask(@Path("taskId") taskId: Int, @Body request: CreateSubtaskRequest): Response<ApiResponse<Subtask>>

    @PUT("subtasks/{id}")
    suspend fun updateSubtask(@Path("id") id: Int, @Body request: CreateSubtaskRequest): Response<ApiResponse<Subtask>>

    @PUT("subtasks/{id}/toggle")
    suspend fun toggleSubtask(@Path("id") id: Int): Response<ApiResponse<Subtask>>

    @DELETE("subtasks/{id}")
    suspend fun deleteSubtask(@Path("id") id: Int): Response<ApiResponse<Unit>>

    @POST("tasks/{taskId}/subtasks/reorder")
    suspend fun reorderSubtasks(@Path("taskId") taskId: Int, @Body subtaskIds: Map<String, List<Int>>): Response<ApiResponse<List<Subtask>>>

    // Focus Sessions
    @POST("sessions/start")
    suspend fun startFocusSession(@Body request: StartFocusSessionRequest): Response<ApiResponse<FocusSession>>

    @PUT("sessions/{id}/stop")
    suspend fun stopFocusSession(@Path("id") sessionId: Int): Response<ApiResponse<FocusSession>>

    @PUT("sessions/{id}/pause")
    suspend fun pauseFocusSession(@Path("id") sessionId: Int): Response<ApiResponse<FocusSession>>

    @PUT("sessions/{id}/resume")
    suspend fun resumeFocusSession(@Path("id") sessionId: Int): Response<ApiResponse<FocusSession>>

    @GET("sessions/current")
    suspend fun getCurrentSession(): Response<ApiResponse<FocusSession>>

    @GET("sessions")
    suspend fun getFocusSessions(): Response<ApiResponse<List<FocusSession>>>

    @GET("sessions/stats")
    suspend fun getSessionStats(): Response<ApiResponse<Map<String, Any>>>

    // Learning Paths
    @GET("learning-paths")
    suspend fun getLearningPaths(): Response<ApiResponse<List<LearningPath>>>

    @POST("learning-paths")
    suspend fun createLearningPath(@Body request: CreateLearningPathRequest): Response<ApiResponse<LearningPath>>

    @GET("learning-paths/{id}")
    suspend fun getLearningPath(@Path("id") id: Int): Response<ApiResponse<LearningPath>>

    @PUT("learning-paths/{id}")
    suspend fun updateLearningPath(@Path("id") id: Int, @Body request: CreateLearningPathRequest): Response<ApiResponse<LearningPath>>

    @DELETE("learning-paths/{id}")
    suspend fun deleteLearningPath(@Path("id") id: Int): Response<ApiResponse<Unit>>

    @PUT("learning-paths/{id}/complete")
    suspend fun completeLearningPath(@Path("id") id: Int): Response<ApiResponse<LearningPath>>

    // Statistics
    @GET("stats/user")
    suspend fun getUserStats(): Response<ApiResponse<UserStats>>
}