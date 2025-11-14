<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\SubtaskController;
use App\Http\Controllers\FocusSessionController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\DailyCheckinController;
use App\Http\Controllers\DailyReviewController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\LearningPathController;
use App\Http\Controllers\LearningPathTemplateController;
use App\Http\Controllers\CheatCodeController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\KnowledgeController;
use App\Http\Controllers\FocusEnhancementController;
use App\Http\Controllers\SettingsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/test', function(){
    return response()->json([
        'message' => 'API',
        'time' => now()
    ]);
});

// Authentication routes with rate limiting
Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:3,1'); // 3 requests per minute

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1'); // 5 requests per minute

// Password reset routes with rate limiting
Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword'])
    ->middleware('throttle:3,1'); // 3 requests per minute

Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
    ->middleware('throttle:5,1'); // 5 requests per minute

// Email verification routes
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->name('verification.verify');

// Cheat Code routes (public - no authentication required)
Route::prefix('cheat-code')->group(function () {
    Route::get('/languages', [CheatCodeController::class, 'getLanguages']);
    Route::get('/languages/{id}', [CheatCodeController::class, 'getLanguage']);
    Route::get('/languages/{languageId}/sections', [CheatCodeController::class, 'getSections']);
    Route::get('/languages/{languageId}/sections/{sectionId}', [CheatCodeController::class, 'getSection']);
    Route::get('/languages/{languageId}/sections/{sectionId}/examples', [CheatCodeController::class, 'getExamples']);
    Route::get('/languages/{languageId}/sections/{sectionId}/examples/{exampleId}', [CheatCodeController::class, 'getExample']);
    Route::get('/categories', [CheatCodeController::class, 'getCategories']);

    // Exercise routes
    Route::get('/languages/{languageId}/exercises', [ExerciseController::class, 'getExercises']);
    Route::get('/languages/{languageId}/exercises/{exerciseId}', [ExerciseController::class, 'getExercise']);
    Route::get('/languages/{languageId}/exercises/{exerciseId}/solution', [ExerciseController::class, 'getSolution']);
    Route::get('/languages/{languageId}/exercises/{exerciseId}/statistics', [ExerciseController::class, 'getStatistics']);
    Route::post('/languages/{languageId}/exercises/{exerciseId}/submit', [ExerciseController::class, 'submitSolution'])
        ->middleware('throttle:10,1'); // 10 submissions per minute
});

// Roadmap API routes - Public endpoint for browsing popular roadmaps
Route::prefix('roadmaps')->group(function () {
    Route::get('/popular', [\App\Http\Controllers\RoadmapApiController::class, 'popular']);
});

Route::middleware('auth:sanctum')->group(function () {
    // Email verification
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1'); // 6 requests per minute
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);

    // Additional task routes (must be before apiResource)
    Route::get('/tasks/stats', [TaskController::class, 'stats']);
    Route::get('/tasks/by-priority/{priority}', [TaskController::class, 'byPriority']);
    Route::get('/tasks/overdue', [TaskController::class, 'overdue']);
    Route::get('/tasks/due-soon', [TaskController::class, 'dueSoon']);
    Route::put('/tasks/{id}/complete', [TaskController::class, 'complete']);
    Route::put('/tasks/{id}/start', [TaskController::class, 'start']);

    // Task routes (Resource routes must be last)
    Route::apiResource('tasks', TaskController::class);

    // Subtask routes
    Route::get('/tasks/{taskId}/subtasks', [SubtaskController::class, 'index']);
    Route::post('/tasks/{taskId}/subtasks', [SubtaskController::class, 'store']);
    Route::post('/tasks/{taskId}/subtasks/reorder', [SubtaskController::class, 'reorder']);
    Route::put('/subtasks/{id}', [SubtaskController::class, 'update']);
    Route::put('/subtasks/{id}/toggle', [SubtaskController::class, 'toggle']);
    Route::delete('/subtasks/{id}', [SubtaskController::class, 'destroy']);

    // Focus Session routes
    Route::prefix('sessions')->group(function () {
        Route::post('/start', [FocusSessionController::class, 'start']);
        Route::get('/current', [FocusSessionController::class, 'current']);
        Route::get('/stats', [FocusSessionController::class, 'stats']);
        Route::get('/by-date', [FocusSessionController::class, 'byDate']);
        Route::put('/{id}/stop', [FocusSessionController::class, 'stop']);
        Route::put('/{id}/pause', [FocusSessionController::class, 'pause']);
        Route::put('/{id}/resume', [FocusSessionController::class, 'resume']);
        Route::get('/', [FocusSessionController::class, 'index']);
    });

            // AI routes (with rate limiting to prevent abuse)
            Route::prefix('ai')->middleware('throttle:20,1')->group(function () {
                Route::get('/status', [AIController::class, 'status']);

                // Heavy AI operations - stricter rate limit (10 requests per minute)
                Route::middleware('throttle:10,1')->group(function () {
                    Route::post('/breakdown-task', [AIController::class, 'breakdownTask']);
                    Route::get('/daily-suggestions', [AIController::class, 'dailySuggestions']);
                    Route::post('/daily-summary', [AIController::class, 'dailySummary']);
                    Route::post('/insights', [AIController::class, 'insights']);
                    Route::post('/learning-recommendations', [AIController::class, 'learningRecommendations']);
                    Route::post('/focus-analysis', [AIController::class, 'focusAnalysis']);

                    // Proactive AI Planning & Insights
                    Route::get('/daily-plan', [AIController::class, 'getDailyPlan']);
                    Route::get('/weekly-insights', [AIController::class, 'getWeeklyInsights']);
                });

                // Lighter operations
                Route::get('/suggestions', [AIController::class, 'suggestions']);
                Route::put('/suggestions/{id}/read', [AIController::class, 'markSuggestionRead']);
                Route::get('/summaries', [AIController::class, 'summaries']);
                Route::post('/motivational-message', [AIController::class, 'motivationalMessage']);

                // Chat routes - moderate rate limit (30 requests per minute for chat)
                Route::prefix('chat')->middleware('throttle:30,1')->group(function () {
                    Route::get('/conversations', [AIController::class, 'getConversations']);
                    Route::post('/conversations', [AIController::class, 'createConversation']);
                    Route::get('/conversations/{id}', [AIController::class, 'getConversation']);
                    Route::put('/conversations/{id}', [AIController::class, 'updateConversation']);
                    Route::delete('/conversations/{id}', [AIController::class, 'deleteConversation']);
                    Route::post('/conversations/{id}/messages', [AIController::class, 'sendMessage']);
                    Route::post('/conversations/{id}/messages/context-aware', [AIController::class, 'sendMessageWithContext']);
                    Route::post('/task-suggestions/confirm', [AIController::class, 'confirmTaskSuggestion']);
                });
            });

    // Focus Enhancement routes
    Route::prefix('focus')->group(function () {
        // Environment Checklist
        Route::post('/environment/check', [FocusEnhancementController::class, 'saveEnvironmentCheck']);
        Route::get('/environment/task/{taskId}', [FocusEnhancementController::class, 'getEnvironmentHistory']);

        // Distraction Logging
        Route::post('/distraction/log', [FocusEnhancementController::class, 'logDistraction']);
        Route::get('/distraction/task/{taskId}', [FocusEnhancementController::class, 'getDistractionLogs']);
        Route::get('/distraction/analytics', [FocusEnhancementController::class, 'getDistractionAnalytics']);

        // Context Switching
        Route::post('/context-switch/check', [FocusEnhancementController::class, 'checkContextSwitch']);
        Route::put('/context-switch/{id}/proceed', [FocusEnhancementController::class, 'confirmContextSwitch']);
        Route::get('/context-switch/analytics', [FocusEnhancementController::class, 'getContextSwitchAnalytics']);
    });

    // Stats routes
    Route::prefix('stats')->group(function () {
        Route::get('/user', [StatsController::class, 'getUserStats']);
        Route::get('/dashboard', [StatsController::class, 'dashboard']);
        Route::get('/tasks', [StatsController::class, 'tasks']);
        Route::get('/sessions', [StatsController::class, 'sessions']);
        Route::get('/trends', [StatsController::class, 'trends']);
        Route::get('/performance', [StatsController::class, 'performance']);
    });

    // Daily Check-in routes
    Route::prefix('daily-checkin')->group(function () {
        Route::get('/today', [DailyCheckinController::class, 'today']);
        Route::get('/stats', [DailyCheckinController::class, 'stats']);
        Route::get('/trends', [DailyCheckinController::class, 'trends']);
        Route::get('/{date}', [DailyCheckinController::class, 'show']);
        Route::get('/', [DailyCheckinController::class, 'index']);
        Route::post('/', [DailyCheckinController::class, 'store']);
        Route::put('/{id}', [DailyCheckinController::class, 'update']);
        Route::delete('/{id}', [DailyCheckinController::class, 'destroy']);
    });

    // Daily Review routes
    Route::prefix('daily-review')->group(function () {
        Route::get('/today', [DailyReviewController::class, 'today']);
        Route::get('/stats', [DailyReviewController::class, 'stats']);
        Route::get('/trends', [DailyReviewController::class, 'trends']);
        Route::get('/insights', [DailyReviewController::class, 'insights']);
        Route::get('/{date}', [DailyReviewController::class, 'show']);
        Route::get('/', [DailyReviewController::class, 'index']);
        Route::post('/', [DailyReviewController::class, 'store']);
        Route::put('/{id}', [DailyReviewController::class, 'update']);
        Route::delete('/{id}', [DailyReviewController::class, 'destroy']);
    });

    // Timetable routes
    Route::prefix('timetable')->group(function () {
        Route::get('/', [TimetableController::class, 'index']);

        // Classes
        Route::get('/classes', [TimetableController::class, 'getClasses']);
        Route::post('/classes', [TimetableController::class, 'createClass']);
        Route::put('/classes/{id}', [TimetableController::class, 'updateClass']);
        Route::delete('/classes/{id}', [TimetableController::class, 'deleteClass']);

        // Weekly Content
        Route::get('/classes/{id}/weekly-content', [TimetableController::class, 'getWeeklyContent']);
        Route::post('/classes/{id}/weekly-content', [TimetableController::class, 'updateWeeklyContent']);
        Route::delete('/weekly-content/{id}', [TimetableController::class, 'deleteWeeklyContent']);

        // Studies (homework/review)
        Route::get('/studies', [TimetableController::class, 'getStudies']);
        Route::post('/studies', [TimetableController::class, 'createStudy']);
        Route::put('/studies/{id}/toggle', [TimetableController::class, 'toggleStudy']);
        Route::delete('/studies/{id}', [TimetableController::class, 'deleteStudy']);
    });

    // Learning Path routes
    Route::prefix('learning-paths')->group(function () {
        Route::get('/stats', [LearningPathController::class, 'stats']);
        Route::get('/', [LearningPathController::class, 'index']);
        Route::post('/', [LearningPathController::class, 'store']);
        Route::get('/{id}', [LearningPathController::class, 'show']);
        Route::put('/{id}', [LearningPathController::class, 'update']);
        Route::delete('/{id}', [LearningPathController::class, 'destroy']);
        Route::put('/{id}/complete', [LearningPathController::class, 'complete']);

        // Study schedules for specific learning path
        Route::get('/{id}/study-schedules', [\App\Http\Controllers\StudyScheduleController::class, 'index']);
        Route::post('/{id}/study-schedules', [\App\Http\Controllers\StudyScheduleController::class, 'store']);
    });

    // Study Schedule routes
    Route::prefix('study-schedules')->group(function () {
        Route::get('/today', [\App\Http\Controllers\StudyScheduleController::class, 'todaySessions']);
        Route::get('/stats', [\App\Http\Controllers\StudyScheduleController::class, 'stats']);
        Route::put('/{id}', [\App\Http\Controllers\StudyScheduleController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\StudyScheduleController::class, 'destroy']);
        Route::post('/{id}/complete', [\App\Http\Controllers\StudyScheduleController::class, 'markCompleted']);
        Route::post('/{id}/missed', [\App\Http\Controllers\StudyScheduleController::class, 'markMissed']);
    });

    // Roadmap API routes - External roadmap integration (requires authentication)
    Route::prefix('roadmaps')->group(function () {
        Route::post('/generate', [\App\Http\Controllers\RoadmapApiController::class, 'generate']);
        Route::post('/import', [\App\Http\Controllers\RoadmapApiController::class, 'import']);
    });

    // Learning Path Template routes
    Route::prefix('learning-path-templates')->group(function () {
        // Browse templates
        Route::get('/', [LearningPathTemplateController::class, 'index']);
        Route::get('/featured', [LearningPathTemplateController::class, 'featured']);
        Route::get('/popular', [LearningPathTemplateController::class, 'popular']);
        Route::get('/categories', [LearningPathTemplateController::class, 'categories']);
        Route::get('/category/{category}', [LearningPathTemplateController::class, 'byCategory']);

        // Template detail
        Route::get('/{id}', [LearningPathTemplateController::class, 'show']);

        // Clone template to user's learning path
        Route::post('/{id}/clone', [LearningPathTemplateController::class, 'clone']);
    });

    // Knowledge routes
    Route::prefix('knowledge')->group(function () {
        Route::get('/stats', [KnowledgeController::class, 'stats']);
        Route::get('/', [KnowledgeController::class, 'index']);
        Route::post('/', [KnowledgeController::class, 'store']);
        Route::get('/{id}', [KnowledgeController::class, 'show']);
        Route::put('/{id}', [KnowledgeController::class, 'update']);
        Route::delete('/{id}', [KnowledgeController::class, 'destroy']);
        Route::put('/{id}/favorite', [KnowledgeController::class, 'toggleFavorite']);
        Route::put('/{id}/archive', [KnowledgeController::class, 'toggleArchive']);
        Route::put('/{id}/review', [KnowledgeController::class, 'markReviewed']);
    });

    // Settings routes
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index']);
        Route::put('/', [SettingsController::class, 'update']);
        Route::post('/reset', [SettingsController::class, 'reset']);
        Route::patch('/{key}', [SettingsController::class, 'updateSetting']);
    });

});
