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
                });

                // Lighter operations
                Route::get('/suggestions', [AIController::class, 'suggestions']);
                Route::put('/suggestions/{id}/read', [AIController::class, 'markSuggestionRead']);
                Route::get('/summaries', [AIController::class, 'summaries']);
                Route::post('/motivational-message', [AIController::class, 'motivationalMessage']);
            });

    // Stats routes
    Route::prefix('stats')->group(function () {
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

});
