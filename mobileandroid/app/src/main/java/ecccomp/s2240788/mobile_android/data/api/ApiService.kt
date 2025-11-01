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
    
    // Timetable
    @GET("timetable")
    suspend fun getTimetable(
        @Query("year") year: Int? = null,
        @Query("week") week: Int? = null
    ): Response<ApiResponse<TimetableResponse>>
    
    @GET("timetable/classes")
    suspend fun getTimetableClasses(): Response<ApiResponse<List<TimetableClass>>>
    
    @POST("timetable/classes")
    suspend fun createTimetableClass(@Body request: CreateTimetableClassRequest): Response<ApiResponse<TimetableClass>>
    
    @PUT("timetable/classes/{id}")
    suspend fun updateTimetableClass(@Path("id") id: Int, @Body request: CreateTimetableClassRequest): Response<ApiResponse<TimetableClass>>
    
    @DELETE("timetable/classes/{id}")
    suspend fun deleteTimetableClass(@Path("id") id: Int): Response<ApiResponse<Unit>>
    
    // Weekly Content endpoints
    @GET("timetable/classes/{id}/weekly-content")
    suspend fun getWeeklyContent(
        @Path("id") classId: Int,
        @Query("year") year: Int? = null,
        @Query("week") week: Int? = null
    ): Response<ApiResponse<TimetableClassWeeklyContent>>
    
    @POST("timetable/classes/{id}/weekly-content")
    suspend fun updateWeeklyContent(
        @Path("id") classId: Int,
        @Body request: UpdateWeeklyContentRequest
    ): Response<ApiResponse<TimetableClassWeeklyContent>>
    
    @DELETE("timetable/weekly-content/{id}")
    suspend fun deleteWeeklyContent(@Path("id") id: Int): Response<ApiResponse<Unit>>
    
    @GET("timetable/studies")
    suspend fun getTimetableStudies(@Query("status") status: String? = null): Response<ApiResponse<List<TimetableStudy>>>
    
    @POST("timetable/studies")
    suspend fun createTimetableStudy(@Body request: CreateTimetableStudyRequest): Response<ApiResponse<TimetableStudy>>
    
    @PUT("timetable/studies/{id}/toggle")
    suspend fun toggleTimetableStudy(@Path("id") id: Int): Response<ApiResponse<TimetableStudy>>
    
    @DELETE("timetable/studies/{id}")
    suspend fun deleteTimetableStudy(@Path("id") id: Int): Response<ApiResponse<Unit>>
    
    // Stats endpoints
    @GET("stats/dashboard")
    suspend fun getStatsDashboard(): Response<ApiResponse<StatsDashboard>>
    
    @GET("stats/tasks")
    suspend fun getTasksStats(@Query("period") period: String? = null): Response<ApiResponse<TasksStats>>
    
    @GET("stats/sessions")
    suspend fun getSessionsStats(@Query("period") period: String? = null): Response<ApiResponse<SessionsStats>>
    
    @GET("stats/trends")
    suspend fun getTrends(@Query("period") period: String, @Query("metric") metric: String? = null): Response<ApiResponse<TrendsData>>
    
    // Knowledge Items
    @GET("knowledge")
    suspend fun getKnowledgeItems(@Query("filter") filter: String? = null): Response<ApiResponse<List<KnowledgeItem>>>
    
    @POST("knowledge")
    suspend fun createKnowledgeItem(@Body request: CreateKnowledgeItemRequest): Response<ApiResponse<KnowledgeItem>>
    
    @GET("knowledge/{id}")
    suspend fun getKnowledgeItem(@Path("id") id: Int): Response<ApiResponse<KnowledgeItem>>
    
    @PUT("knowledge/{id}")
    suspend fun updateKnowledgeItem(@Path("id") id: Int, @Body request: CreateKnowledgeItemRequest): Response<ApiResponse<KnowledgeItem>>
    
    @DELETE("knowledge/{id}")
    suspend fun deleteKnowledgeItem(@Path("id") id: Int): Response<ApiResponse<Unit>>
    
    @PUT("knowledge/{id}/favorite")
    suspend fun toggleKnowledgeFavorite(@Path("id") id: Int): Response<ApiResponse<KnowledgeItem>>
    
    @PUT("knowledge/{id}/archive")
    suspend fun toggleKnowledgeArchive(@Path("id") id: Int): Response<ApiResponse<KnowledgeItem>>
    
    @PUT("knowledge/{id}/review")
    suspend fun markKnowledgeReviewed(@Path("id") id: Int): Response<ApiResponse<KnowledgeItem>>
}