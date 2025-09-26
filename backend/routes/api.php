<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});

// API v1 routes
Route::prefix('v1')->group(function () {
    // Authentication routes
    Route::post('/auth/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('/auth/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::post('/auth/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // User routes
        Route::get('/user/profile', [App\Http\Controllers\Api\UserController::class, 'profile']);
        Route::put('/user/profile', [App\Http\Controllers\Api\UserController::class, 'updateProfile']);

        // Project routes
        Route::apiResource('projects', App\Http\Controllers\Api\ProjectController::class);

        // Task routes
        Route::apiResource('tasks', App\Http\Controllers\Api\TaskController::class);
        Route::post('/tasks/{task}/breakdown', [App\Http\Controllers\Api\TaskController::class, 'breakdown']);

        // Session routes (Focus Mode)
        Route::apiResource('sessions', App\Http\Controllers\Api\SessionController::class);
        Route::post('/sessions/{session}/complete', [App\Http\Controllers\Api\SessionController::class, 'complete']);

        // AI routes
        Route::post('/ai/plan-today', [App\Http\Controllers\Api\AIController::class, 'planToday']);
        Route::post('/ai/nudge', [App\Http\Controllers\Api\AIController::class, 'nudge']);
        Route::post('/ai/review', [App\Http\Controllers\Api\AIController::class, 'review']);

        // Stats routes
        Route::get('/stats/dashboard', [App\Http\Controllers\Api\StatsController::class, 'dashboard']);
        Route::get('/stats/streak', [App\Http\Controllers\Api\StatsController::class, 'streak']);
        Route::get('/stats/heatmap', [App\Http\Controllers\Api\StatsController::class, 'heatmap']);
    });
});
