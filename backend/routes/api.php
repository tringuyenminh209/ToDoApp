<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\FocusSessionController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\DailyCheckinController;
use App\Http\Controllers\DailyReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/test', function(){
    return response()->json([
        'message' => 'API',
        'time' => now()
    ]);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // Task routes
    Route::apiResource('tasks', TaskController::class);

    // Additional task routes
    Route::put('/tasks/{id}/complete', [TaskController::class, 'complete']);
    Route::put('/tasks/{id}/start', [TaskController::class, 'start']);
    Route::get('/tasks/stats', [TaskController::class, 'stats']);
    Route::get('/tasks/by-priority/{priority}', [TaskController::class, 'byPriority']);
    Route::get('/tasks/overdue', [TaskController::class, 'overdue']);
    Route::get('/tasks/due-soon', [TaskController::class, 'dueSoon']);

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

            // AI routes
            Route::prefix('ai')->group(function () {
                Route::get('/status', [AIController::class, 'status']);
                Route::post('/breakdown-task', [AIController::class, 'breakdownTask']);
                Route::get('/daily-suggestions', [AIController::class, 'dailySuggestions']);
                Route::post('/daily-summary', [AIController::class, 'dailySummary']);
                Route::get('/suggestions', [AIController::class, 'suggestions']);
                Route::put('/suggestions/{id}/read', [AIController::class, 'markSuggestionRead']);
                Route::get('/summaries', [AIController::class, 'summaries']);
                Route::post('/insights', [AIController::class, 'insights']);
                Route::post('/learning-recommendations', [AIController::class, 'learningRecommendations']);
                Route::post('/focus-analysis', [AIController::class, 'focusAnalysis']);
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

});
