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
    suspend fun getTasks(@Query("per_page") perPage: Int = 100): Response<ApiResponse<Any>>

    @GET("tasks/{id}")
    suspend fun getTask(@Path("id") id: Int): Response<ApiResponse<Task>>

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

    @PUT("subtasks/{id}/complete")
    suspend fun completeSubtask(@Path("id") id: Int): Response<ApiResponse<SubtaskCompleteResponse>>

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

    @PUT("timetable/studies/{id}")
    suspend fun updateTimetableStudy(@Path("id") id: Int, @Body request: CreateTimetableStudyRequest): Response<ApiResponse<TimetableStudy>>

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
    
    // ==================== Knowledge Base Endpoints ====================

    // Knowledge Category endpoints
    @GET("knowledge/categories/stats")
    suspend fun getKnowledgeCategoryStats(): Response<ApiResponse<KnowledgeCategoryStats>>

    @GET("knowledge/categories/tree")
    suspend fun getKnowledgeCategoryTree(): Response<ApiResponse<List<KnowledgeCategory>>>

    @POST("knowledge/categories/reorder")
    suspend fun reorderKnowledgeCategories(@Body request: ReorderCategoriesRequest): Response<ApiResponse<Unit>>

    @GET("knowledge/categories")
    suspend fun getKnowledgeCategories(): Response<ApiResponse<List<KnowledgeCategory>>>

    @POST("knowledge/categories")
    suspend fun createKnowledgeCategory(@Body request: CreateKnowledgeCategoryRequest): Response<ApiResponse<KnowledgeCategory>>

    @GET("knowledge/categories/{id}")
    suspend fun getKnowledgeCategory(@Path("id") id: Int): Response<ApiResponse<KnowledgeCategory>>

    @PUT("knowledge/categories/{id}")
    suspend fun updateKnowledgeCategory(@Path("id") id: Int, @Body request: CreateKnowledgeCategoryRequest): Response<ApiResponse<KnowledgeCategory>>

    @DELETE("knowledge/categories/{id}")
    suspend fun deleteKnowledgeCategory(@Path("id") id: Int): Response<ApiResponse<Unit>>

    @POST("knowledge/categories/{id}/move")
    suspend fun moveKnowledgeCategory(@Path("id") id: Int, @Body request: MoveCategoryRequest): Response<ApiResponse<KnowledgeCategory>>

    @POST("knowledge/categories/{id}/update-count")
    suspend fun updateKnowledgeCategoryCount(@Path("id") id: Int): Response<ApiResponse<KnowledgeCategory>>

    // Knowledge Item endpoints
    @GET("knowledge/stats")
    suspend fun getKnowledgeStats(): Response<ApiResponse<KnowledgeStats>>

    @GET("knowledge/due-review")
    suspend fun getKnowledgeDueReview(): Response<ApiResponse<List<KnowledgeItem>>>

    @POST("knowledge/quick-capture")
    suspend fun quickCaptureKnowledge(@Body request: QuickCaptureRequest): Response<ApiResponse<QuickCaptureResponse>>

    @POST("knowledge/suggest-category")
    suspend fun suggestCategory(@Body request: SuggestCategoryRequest): Response<ApiResponse<SuggestCategoryResponse>>

    @POST("knowledge/suggest-tags")
    suspend fun suggestTags(@Body request: SuggestTagsRequest): Response<ApiResponse<SuggestTagsResponse>>

    @PUT("knowledge/bulk-tag")
    suspend fun bulkTagKnowledgeItems(@Body request: BulkTagRequest): Response<ApiResponse<BulkOperationResponse>>

    @PUT("knowledge/bulk-move")
    suspend fun bulkMoveKnowledgeItems(@Body request: BulkMoveRequest): Response<ApiResponse<BulkOperationResponse>>

    @DELETE("knowledge/bulk-delete")
    suspend fun bulkDeleteKnowledgeItems(@Body request: BulkDeleteRequest): Response<ApiResponse<BulkOperationResponse>>

    @GET("knowledge")
    suspend fun getKnowledgeItems(
        @Query("category_id") categoryId: Int? = null,
        @Query("item_type") itemType: String? = null,
        @Query("is_favorite") isFavorite: Boolean? = null,
        @Query("is_archived") isArchived: Boolean? = null,
        @Query("search") search: String? = null,
        @Query("tags") tags: List<String>? = null,
        @Query("sort_by") sortBy: String = "created_at",
        @Query("sort_order") sortOrder: String = "desc",
        @Query("per_page") perPage: Int = 20
    ): Response<ApiResponse<Any>>

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
    suspend fun markKnowledgeReviewed(
        @Path("id") id: Int,
        @Body request: MarkReviewRequest
    ): Response<ApiResponse<KnowledgeItem>>

    @POST("knowledge/{id}/add-to-review")
    suspend fun addKnowledgeToReview(@Path("id") id: Int): Response<ApiResponse<KnowledgeItem>>

    @POST("knowledge/{id}/clone")
    suspend fun cloneKnowledgeItem(@Path("id") id: Int, @Body request: CloneKnowledgeRequest): Response<ApiResponse<KnowledgeItem>>

    @GET("knowledge/{id}/related")
    suspend fun getRelatedKnowledgeItems(@Path("id") id: Int, @Query("limit") limit: Int = 5): Response<ApiResponse<List<KnowledgeItem>>>
    
    // Learning Path Templates
    @GET("learning-path-templates")
    suspend fun getTemplates(@QueryMap filters: Map<String, String>? = null): Response<TemplateListResponse>
    
    @GET("learning-path-templates/featured")
    suspend fun getFeaturedTemplates(): Response<TemplateListResponse>
    
    @GET("learning-path-templates/popular")
    suspend fun getPopularTemplates(): Response<TemplateListResponse>
    
    @GET("learning-path-templates/categories")
    suspend fun getTemplateCategories(): Response<TemplateCategoriesResponse>
    
    @GET("learning-path-templates/category/{category}")
    suspend fun getTemplatesByCategory(@Path("category") category: String): Response<TemplateListResponse>
    
    @GET("learning-path-templates/{id}")
    suspend fun getTemplateDetail(@Path("id") id: Long): Response<TemplateDetailResponse>
    
    @POST("learning-path-templates/{id}/clone")
    suspend fun cloneTemplate(
        @Path("id") id: Long,
        @Body request: CloneTemplateRequest
    ): Response<CloneTemplateResponse>

    // Roadmap API endpoints
    @GET("roadmaps/popular")
    suspend fun getPopularRoadmaps(): Response<RoadmapListResponse>

    @POST("roadmaps/generate")
    suspend fun generateRoadmap(@Body request: GenerateRoadmapRequest): Response<RoadmapGenerateResponse>

    @POST("roadmaps/import")
    suspend fun importRoadmap(@Body request: ImportRoadmapRequest): Response<ImportRoadmapResponse>
    
    // Cheat Code endpoints (public - no authentication required)
    @GET("cheat-code/languages")
    suspend fun getCheatCodeLanguages(
        @Query("category") category: String? = null,
        @Query("search") search: String? = null,
        @Query("sort_by") sortBy: String? = null,
        @Query("sort_order") sortOrder: String? = null
    ): Response<ApiResponse<List<CheatCodeLanguage>>>
    
    @GET("cheat-code/languages/{id}")
    suspend fun getCheatCodeLanguage(@Path("id") id: Int): Response<ApiResponse<CheatCodeLanguage>>
    
    @GET("cheat-code/languages/{languageId}/sections")
    suspend fun getCheatCodeSections(@Path("languageId") languageId: Int): Response<ApiResponse<CheatCodeSectionsResponse>>
    
    @GET("cheat-code/languages/{languageId}/sections/{sectionId}")
    suspend fun getCheatCodeSection(
        @Path("languageId") languageId: Int,
        @Path("sectionId") sectionId: Int
    ): Response<ApiResponse<CheatCodeSection>>
    
    @GET("cheat-code/languages/{languageId}/sections/{sectionId}/examples")
    suspend fun getCodeExamples(
        @Path("languageId") languageId: Int,
        @Path("sectionId") sectionId: Int,
        @Query("difficulty") difficulty: String? = null,
        @Query("search") search: String? = null
    ): Response<ApiResponse<List<CodeExample>>>
    
    @GET("cheat-code/languages/{languageId}/sections/{sectionId}/examples/{exampleId}")
    suspend fun getCodeExample(
        @Path("languageId") languageId: Int,
        @Path("sectionId") sectionId: Int,
        @Path("exampleId") exampleId: Int
    ): Response<ApiResponse<CodeExample>>
    
    @GET("cheat-code/categories")
    suspend fun getCheatCodeCategories(): Response<ApiResponse<List<String>>>

    // Exercise endpoints
    @GET("cheat-code/languages/{languageId}/exercises")
    suspend fun getExercises(
        @Path("languageId") languageId: Int,
        @Query("difficulty") difficulty: String? = null,
        @Query("search") search: String? = null,
        @Query("sort_by") sortBy: String? = null,
        @Query("sort_order") sortOrder: String? = null
    ): Response<ExerciseListResponse>

    @GET("cheat-code/languages/{languageId}/exercises/{exerciseId}")
    suspend fun getExercise(
        @Path("languageId") languageId: Int,
        @Path("exerciseId") exerciseId: Int
    ): Response<ApiResponse<ExerciseDetail>>

    @POST("cheat-code/languages/{languageId}/exercises/{exerciseId}/submit")
    suspend fun submitExerciseSolution(
        @Path("languageId") languageId: Int,
        @Path("exerciseId") exerciseId: Int,
        @Body request: SubmitSolutionRequest
    ): Response<ApiResponse<SubmitSolutionResponse>>

    @GET("cheat-code/languages/{languageId}/exercises/{exerciseId}/solution")
    suspend fun getExerciseSolution(
        @Path("languageId") languageId: Int,
        @Path("exerciseId") exerciseId: Int
    ): Response<ApiResponse<SolutionResponse>>

    @GET("cheat-code/languages/{languageId}/exercises/{exerciseId}/statistics")
    suspend fun getExerciseStatistics(
        @Path("languageId") languageId: Int,
        @Path("exerciseId") exerciseId: Int
    ): Response<ApiResponse<ExerciseStatistics>>

    // Focus Enhancement endpoints
    // Environment Checklist
    @POST("focus/environment/check")
    suspend fun saveEnvironmentCheck(@Body request: SaveEnvironmentCheckRequest): Response<ApiResponse<FocusEnvironment>>

    @GET("focus/environment/task/{taskId}")
    suspend fun getEnvironmentHistory(@Path("taskId") taskId: Int): Response<ApiResponse<List<FocusEnvironment>>>

    // Distraction Logging
    @POST("focus/distraction/log")
    suspend fun logDistraction(@Body request: LogDistractionRequest): Response<ApiResponse<DistractionLog>>

    @GET("focus/distraction/task/{taskId}")
    suspend fun getDistractionLogs(@Path("taskId") taskId: Int): Response<ApiResponse<List<DistractionLog>>>

    @GET("focus/distraction/analytics")
    suspend fun getDistractionAnalytics(@Query("days") days: Int = 7): Response<ApiResponse<DistractionAnalytics>>

    // Context Switching
    @POST("focus/context-switch/check")
    suspend fun checkContextSwitch(@Body request: CheckContextSwitchRequest): Response<ApiResponse<ContextSwitchResponse>>

    @PUT("focus/context-switch/{id}/proceed")
    suspend fun confirmContextSwitch(@Path("id") id: Int, @Body note: Map<String, String?>): Response<ApiResponse<ContextSwitch>>

    @GET("focus/context-switch/analytics")
    suspend fun getContextSwitchAnalytics(@Query("days") days: Int = 7): Response<ApiResponse<ContextSwitchAnalytics>>

    // ==================== Chat AI Endpoints ====================

    @GET("ai/chat/conversations")
    suspend fun getChatConversations(
        @Query("status") status: String? = null,
        @Query("sort_by") sortBy: String = "last_message_at",
        @Query("sort_order") sortOrder: String = "desc",
        @Query("per_page") perPage: Int = 20
    ): Response<ApiResponse<ChatConversationsResponse>>

    @POST("ai/chat/conversations")
    suspend fun createChatConversation(@Body request: CreateConversationRequest): Response<ApiResponse<CreateConversationResponse>>

    @GET("ai/chat/conversations/{id}")
    suspend fun getChatConversation(@Path("id") id: Long): Response<ApiResponse<ChatConversation>>

    @PUT("ai/chat/conversations/{id}")
    suspend fun updateChatConversation(
        @Path("id") id: Long,
        @Body request: UpdateConversationRequest
    ): Response<ApiResponse<ChatConversation>>

    @DELETE("ai/chat/conversations/{id}")
    suspend fun deleteChatConversation(@Path("id") id: Long): Response<ApiResponse<Unit>>

    @POST("ai/chat/conversations/{id}/messages")
    suspend fun sendChatMessage(
        @Path("id") id: Long,
        @Body request: SendMessageRequest
    ): Response<ApiResponse<SendMessageResponse>>

    @POST("ai/chat/conversations/{id}/messages/context-aware")
    suspend fun sendChatMessageWithContext(
        @Path("id") id: Long,
        @Body request: SendMessageRequest
    ): Response<ApiResponse<SendMessageResponse>>

    @POST("ai/chat/task-suggestions/confirm")
    suspend fun confirmTaskSuggestion(@Body request: TaskSuggestion): Response<ApiResponse<Task>>

    @POST("ai/chat/timetable-suggestions/confirm")
    suspend fun confirmTimetableSuggestion(@Body request: TimetableClassSuggestion): Response<ApiResponse<TimetableClass>>

    // ==================== AI Task Breakdown Endpoint ====================

    @POST("ai/breakdown-task")
    suspend fun breakdownTask(@Body request: BreakdownTaskRequest): Response<ApiResponse<Task>>

    // ==================== Daily Review Endpoints ====================

    @POST("daily-review")
    suspend fun createDailyReview(@Body request: CreateDailyReviewRequest): Response<ApiResponse<DailyReview>>

    @GET("daily-review/today")
    suspend fun getTodayReview(): Response<ApiResponse<DailyReview>>

    @GET("daily-review/{date}")
    suspend fun getReviewByDate(@Path("date") date: String): Response<ApiResponse<DailyReview>>

    @PUT("daily-review/{id}")
    suspend fun updateDailyReview(
        @Path("id") id: Int,
        @Body request: UpdateDailyReviewRequest
    ): Response<ApiResponse<DailyReview>>

    @DELETE("daily-review/{id}")
    suspend fun deleteDailyReview(@Path("id") id: Int): Response<ApiResponse<Unit>>

    @GET("daily-review")
    suspend fun getDailyReviews(
        @Query("start_date") startDate: String? = null,
        @Query("end_date") endDate: String? = null,
        @Query("mood") mood: String? = null,
        @Query("min_score") minScore: Int? = null,
        @Query("max_score") maxScore: Int? = null,
        @Query("sort_by") sortBy: String = "date",
        @Query("sort_order") sortOrder: String = "desc",
        @Query("per_page") perPage: Int = 20
    ): Response<ApiResponse<Any>>

    @GET("daily-review/stats")
    suspend fun getDailyReviewStats(
        @Query("period") period: String = "month" // week, month, year, all
    ): Response<ApiResponse<DailyReviewStats>>

    @GET("daily-review/trends")
    suspend fun getDailyReviewTrends(
        @Query("period") period: String, // week, month, year
        @Query("metric") metric: String = "productivity" // productivity, focus_time, task_completion, goal_achievement, work_life_balance
    ): Response<ApiResponse<List<DailyReviewTrend>>>

    @GET("daily-review/insights")
    suspend fun getDailyReviewInsights(
        @Query("period") period: String = "month" // week, month, year, all
    ): Response<ApiResponse<DailyReviewInsights>>

    // ==================== Study Schedule Endpoints ====================

    // Get all study schedules for the user (for calendar)
    @GET("study-schedules")
    suspend fun getAllStudySchedules(): Response<ApiResponse<List<StudyScheduleWithPath>>>

    // Get all schedules for a learning path
    @GET("learning-paths/{id}/study-schedules")
    suspend fun getStudySchedules(@Path("id") learningPathId: Int): Response<ApiResponse<Any>>

    // Create new study schedule for a learning path
    @POST("learning-paths/{id}/study-schedules")
    suspend fun createStudySchedule(
        @Path("id") learningPathId: Int,
        @Body request: CreateStudyScheduleRequest
    ): Response<ApiResponse<StudySchedule>>

    // Update study schedule
    @PUT("study-schedules/{id}")
    suspend fun updateStudySchedule(
        @Path("id") scheduleId: Int,
        @Body request: UpdateStudyScheduleRequest
    ): Response<ApiResponse<StudySchedule>>

    // Delete study schedule
    @DELETE("study-schedules/{id}")
    suspend fun deleteStudySchedule(@Path("id") scheduleId: Int): Response<ApiResponse<Unit>>

    // Mark session as completed
    @POST("study-schedules/{id}/complete")
    suspend fun markScheduleCompleted(@Path("id") scheduleId: Int): Response<ApiResponse<StudySchedule>>

    // Mark session as missed
    @POST("study-schedules/{id}/missed")
    suspend fun markScheduleMissed(@Path("id") scheduleId: Int): Response<ApiResponse<StudySchedule>>

    // Assign tasks to study schedules for a learning path
    @POST("learning-paths/{id}/assign-schedules")
    suspend fun assignTasksToSchedules(@Path("id") learningPathId: Int): Response<ApiResponse<Any>>

    // Get today's scheduled sessions
    @GET("study-schedules/today")
    suspend fun getTodaySessions(): Response<ApiResponse<TodaySessionsResponse>>

    // Get schedule statistics
    @GET("study-schedules/stats")
    suspend fun getStudyScheduleStats(): Response<ApiResponse<StudyScheduleStats>>

    // Get combined timeline items (Study Schedules + Timetable Classes)
    @GET("study-schedules/timeline")
    suspend fun getTimelineItems(): Response<ApiResponse<List<TimelineItem>>>

    // ==================== Settings Endpoints ====================

    // Get user settings
    @GET("settings")
    suspend fun getSettings(): Response<SettingsResponse>

    // Update user settings
    @PUT("settings")
    suspend fun updateSettings(@Body request: SettingsRequest): Response<SettingsResponse>

    // Reset settings to default
    @POST("settings/reset")
    suspend fun resetSettings(): Response<SettingsResponse>

    // Update specific setting by key
    @PATCH("settings/{key}")
    suspend fun updateSetting(
        @Path("key") key: String,
        @Body value: Map<String, Any>
    ): Response<SettingsResponse>
}