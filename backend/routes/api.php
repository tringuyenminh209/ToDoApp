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
use App\Http\Controllers\KnowledgeCategoryController;
use App\Http\Controllers\FocusEnhancementController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TaskTrackingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/test', function(){
    return response()->json([
        'message' => 'API',
        'time' => now()
    ]);
});

// 認証ルート（レート制限付き）
Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:3,1'); // 1分あたり3リクエスト

Route::post('/login', [AuthController::class, 'login'])
    ->name('login')
    ->middleware('throttle:5,1'); // 1分あたり5リクエスト

// パスワードリセットルート（レート制限付き）
Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword'])
    ->middleware('throttle:3,1'); // 1分あたり3リクエスト

Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
    ->middleware('throttle:5,1'); // 1分あたり5リクエスト

// メール認証ルート
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->name('verification.verify');

// チートコードルート（公開 - 認証不要）
Route::prefix('cheat-code')->group(function () {
    Route::get('/languages', [CheatCodeController::class, 'getLanguages']);
    Route::get('/languages/{id}', [CheatCodeController::class, 'getLanguage']);
    Route::get('/languages/{languageId}/sections', [CheatCodeController::class, 'getSections']);
    Route::get('/languages/{languageId}/sections/{sectionId}', [CheatCodeController::class, 'getSection']);
    Route::get('/languages/{languageId}/sections/{sectionId}/examples', [CheatCodeController::class, 'getExamples']);
    Route::get('/languages/{languageId}/sections/{sectionId}/examples/{exampleId}', [CheatCodeController::class, 'getExample']);
    Route::post('/languages/{languageId}/sections/{sectionId}/examples/{exampleId}/run', [CheatCodeController::class, 'runExample'])
        ->middleware('throttle:10,1');
    Route::post('/languages/{languageId}/sections/{sectionId}/examples/{exampleId}/run-custom', [CheatCodeController::class, 'runCustomExample'])
        ->middleware('throttle:10,1');
    Route::get('/categories', [CheatCodeController::class, 'getCategories']);

    // 演習ルート
    Route::get('/languages/{languageId}/exercises', [ExerciseController::class, 'getExercises']);
    Route::get('/languages/{languageId}/exercises/{exerciseId}', [ExerciseController::class, 'getExercise']);
    Route::get('/languages/{languageId}/exercises/{exerciseId}/solution', [ExerciseController::class, 'getSolution']);
    Route::get('/languages/{languageId}/exercises/{exerciseId}/statistics', [ExerciseController::class, 'getStatistics']);
    Route::post('/languages/{languageId}/exercises/{exerciseId}/submit', [ExerciseController::class, 'submitSolution'])
        ->middleware('throttle:10,1'); // 1分あたり10回の提出
});

// ロードマップAPIルート - 人気のロードマップを閲覧するための公開エンドポイント
Route::prefix('roadmaps')->group(function () {
    Route::get('/popular', [\App\Http\Controllers\RoadmapApiController::class, 'popular']);
});

Route::middleware('auth:sanctum')->group(function () {
    // メール認証
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1'); // 1分あたり6リクエスト

    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    Route::post('/user/fcm-token', [AuthController::class, 'updateFCMToken']);

    // 追加タスクルート（apiResourceより前に配置する必要がある）
    Route::get('/tasks/stats', [TaskController::class, 'stats']);
    Route::get('/tasks/by-priority/{priority}', [TaskController::class, 'byPriority']);
    Route::get('/tasks/overdue', [TaskController::class, 'overdue']);
    Route::get('/tasks/due-soon', [TaskController::class, 'dueSoon']);
    Route::get('/tasks/abandoned', [TaskTrackingController::class, 'getAbandonedTasks']);
    Route::get('/tasks/{id}/suggest-schedule', [TaskController::class, 'suggestSchedule']);
    Route::get('/tasks/{id}/abandonments', [TaskTrackingController::class, 'getTaskAbandonments']);
    Route::put('/tasks/{id}/complete', [TaskController::class, 'complete']);
    Route::put('/tasks/{id}/start', [TaskController::class, 'start']);
    Route::post('/tasks/{id}/heartbeat', [TaskTrackingController::class, 'heartbeat']);
    Route::post('/tasks/{id}/abandon', [TaskTrackingController::class, 'abandonTask']);
    Route::post('/tasks/{id}/resume', [TaskTrackingController::class, 'resumeTask']);

    // タスクルート（リソースルートは最後に配置する必要がある）
    Route::apiResource('tasks', TaskController::class);

    // サブタスクルート
    Route::get('/tasks/{taskId}/subtasks', [SubtaskController::class, 'index']);
    Route::post('/tasks/{taskId}/subtasks', [SubtaskController::class, 'store']);
    Route::post('/tasks/{taskId}/subtasks/reorder', [SubtaskController::class, 'reorder']);
    Route::put('/subtasks/{id}', [SubtaskController::class, 'update']);
    Route::put('/subtasks/{id}/toggle', [SubtaskController::class, 'toggle']);
    Route::put('/subtasks/{id}/complete', [SubtaskController::class, 'complete']);
    Route::delete('/subtasks/{id}', [SubtaskController::class, 'destroy']);

    // フォーカスセッションルート
    Route::prefix('sessions')->group(function () {
        Route::post('/start', [FocusSessionController::class, 'start']);
        Route::get('/current', [FocusSessionController::class, 'current']);
        Route::get('/stats', [FocusSessionController::class, 'stats']);
        Route::get('/by-date', [FocusSessionController::class, 'byDate']);
        Route::put('/{id}/stop', [FocusSessionController::class, 'stop']);
        Route::put('/{id}/pause', [FocusSessionController::class, 'pause']);
        Route::put('/{id}/resume', [FocusSessionController::class, 'resume']);
        Route::put('/{id}/notes', [FocusSessionController::class, 'updateNotes']);
        Route::get('/', [FocusSessionController::class, 'index']);
    });

    // AIルート
    Route::prefix('ai')->group(function () {
        Route::get('/status', [AIController::class, 'status']);

        // 重いAI操作
        Route::post('/breakdown-task', [AIController::class, 'breakdownTask']);
        Route::get('/daily-suggestions', [AIController::class, 'dailySuggestions']);
        Route::post('/daily-summary', [AIController::class, 'dailySummary']);
        Route::post('/insights', [AIController::class, 'insights']);
        Route::post('/learning-recommendations', [AIController::class, 'learningRecommendations']);
        Route::post('/focus-analysis', [AIController::class, 'focusAnalysis']);

        // プロアクティブなAI計画とインサイト
        Route::get('/daily-plan', [AIController::class, 'getDailyPlan']);
        Route::get('/weekly-insights', [AIController::class, 'getWeeklyInsights']);

        // 軽量操作
        Route::get('/suggestions', [AIController::class, 'suggestions']);
        Route::put('/suggestions/{id}/read', [AIController::class, 'markSuggestionRead']);
        Route::get('/summaries', [AIController::class, 'summaries']);
        Route::post('/motivational-message', [AIController::class, 'motivationalMessage']);

        // チャットルート
        Route::prefix('chat')->group(function () {
            Route::get('/conversations', [AIController::class, 'getConversations']);
            Route::post('/conversations', [AIController::class, 'createConversation']);
            Route::get('/conversations/{id}', [AIController::class, 'getConversation']);
            Route::put('/conversations/{id}', [AIController::class, 'updateConversation']);
            Route::delete('/conversations/{id}', [AIController::class, 'deleteConversation']);
            Route::post('/conversations/{id}/messages', [AIController::class, 'sendMessage']);
            Route::post('/conversations/{id}/messages/context-aware', [AIController::class, 'sendMessageWithContext']);
            Route::post('/task-suggestions/confirm', [AIController::class, 'confirmTaskSuggestion']);
            Route::post('/timetable-suggestions/confirm', [AIController::class, 'confirmTimetableSuggestion']);
        });
    });

    // フォーカス強化ルート
    Route::prefix('focus')->group(function () {
        // 環境チェックリスト
        Route::post('/environment/check', [FocusEnhancementController::class, 'saveEnvironmentCheck']);
        Route::get('/environment/task/{taskId}', [FocusEnhancementController::class, 'getEnvironmentHistory']);

        // 気が散る記録
        Route::post('/distraction/log', [FocusEnhancementController::class, 'logDistraction']);
        Route::get('/distraction/task/{taskId}', [FocusEnhancementController::class, 'getDistractionLogs']);
        Route::get('/distraction/analytics', [FocusEnhancementController::class, 'getDistractionAnalytics']);

        // コンテキスト切り替え
        Route::post('/context-switch/check', [FocusEnhancementController::class, 'checkContextSwitch']);
        Route::put('/context-switch/{id}/proceed', [FocusEnhancementController::class, 'confirmContextSwitch']);
        Route::get('/context-switch/analytics', [FocusEnhancementController::class, 'getContextSwitchAnalytics']);
    });

    // 統計ルート
    Route::prefix('stats')->group(function () {
        Route::get('/user', [StatsController::class, 'getUserStats']);
        Route::get('/dashboard', [StatsController::class, 'dashboard']);
        Route::get('/tasks', [StatsController::class, 'tasks']);
        Route::get('/sessions', [StatsController::class, 'sessions']);
        Route::get('/trends', [StatsController::class, 'trends']);
        Route::get('/performance', [StatsController::class, 'performance']);
        Route::get('/golden-time', [StatsController::class, 'goldenTime']);
    });

    // デイリーチェックインルート
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

    // デイリーレビュールート
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

    // 時間割ルート
    Route::prefix('timetable')->group(function () {
        Route::get('/', [TimetableController::class, 'index']);

        // クラス
        Route::get('/classes', [TimetableController::class, 'getClasses']);
        Route::post('/classes', [TimetableController::class, 'createClass']);
        Route::put('/classes/{id}', [TimetableController::class, 'updateClass']);
        Route::delete('/classes/{id}', [TimetableController::class, 'deleteClass']);

        // 週次コンテンツ
        Route::get('/classes/{id}/weekly-content', [TimetableController::class, 'getWeeklyContent']);
        Route::post('/classes/{id}/weekly-content', [TimetableController::class, 'updateWeeklyContent']);
        Route::delete('/weekly-content/{id}', [TimetableController::class, 'deleteWeeklyContent']);

        // 学習（宿題/復習）
        Route::get('/studies', [TimetableController::class, 'getStudies']);
        Route::post('/studies', [TimetableController::class, 'createStudy']);
        Route::put('/studies/{id}', [TimetableController::class, 'updateStudy']);
        Route::put('/studies/{id}/toggle', [TimetableController::class, 'toggleStudy']);
        Route::delete('/studies/{id}', [TimetableController::class, 'deleteStudy']);
    });

    // 学習パスルート
    Route::prefix('learning-paths')->group(function () {
        Route::get('/stats', [LearningPathController::class, 'stats']);
        Route::get('/', [LearningPathController::class, 'index']);
        Route::post('/', [LearningPathController::class, 'store']);
        Route::get('/{id}', [LearningPathController::class, 'show']);
        Route::put('/{id}', [LearningPathController::class, 'update']);
        Route::delete('/{id}', [LearningPathController::class, 'destroy']);
        Route::put('/{id}/complete', [LearningPathController::class, 'complete']);

        // 特定の学習パスの学習スケジュール
        Route::get('/{id}/study-schedules', [\App\Http\Controllers\StudyScheduleController::class, 'index']);
        Route::post('/{id}/study-schedules', [\App\Http\Controllers\StudyScheduleController::class, 'store']);

        // 学習スケジュールにタスクを割り当て
        Route::post('/{id}/assign-schedules', [\App\Http\Controllers\RoadmapApiController::class, 'assignSchedules']);
    });

    // 学習スケジュールルート
    Route::prefix('study-schedules')->group(function () {
        Route::get('/today', [\App\Http\Controllers\StudyScheduleController::class, 'todaySessions']);
        Route::get('/stats', [\App\Http\Controllers\StudyScheduleController::class, 'stats']);
        Route::get('/timeline', [\App\Http\Controllers\StudyScheduleController::class, 'getTimelineItems']);
        Route::get('/', [\App\Http\Controllers\StudyScheduleController::class, 'allSchedules']);
        Route::put('/{id}', [\App\Http\Controllers\StudyScheduleController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\StudyScheduleController::class, 'destroy']);
        Route::post('/{id}/complete', [\App\Http\Controllers\StudyScheduleController::class, 'markCompleted']);
        Route::post('/{id}/missed', [\App\Http\Controllers\StudyScheduleController::class, 'markMissed']);
    });

    // ロードマップAPIルート - 外部ロードマップ統合（認証が必要）
    Route::prefix('roadmaps')->group(function () {
        Route::post('/generate', [\App\Http\Controllers\RoadmapApiController::class, 'generate']);
        Route::post('/import', [\App\Http\Controllers\RoadmapApiController::class, 'import']);
    });

    // 学習パステンプレートルート
    Route::prefix('learning-path-templates')->group(function () {
        // テンプレートを閲覧
        Route::get('/', [LearningPathTemplateController::class, 'index']);
        Route::get('/featured', [LearningPathTemplateController::class, 'featured']);
        Route::get('/popular', [LearningPathTemplateController::class, 'popular']);
        Route::get('/categories', [LearningPathTemplateController::class, 'categories']);
        Route::get('/category/{category}', [LearningPathTemplateController::class, 'byCategory']);

        // テンプレート詳細
        Route::get('/{id}', [LearningPathTemplateController::class, 'show']);

        // テンプレートをユーザーの学習パスにクローン
        Route::post('/{id}/clone', [LearningPathTemplateController::class, 'clone']);
    });

    // ナレッジルート
    Route::prefix('knowledge')->group(function () {
        // カテゴリルート
        Route::prefix('categories')->group(function () {
            Route::get('/stats', [KnowledgeCategoryController::class, 'stats']);
            Route::get('/tree', [KnowledgeCategoryController::class, 'tree']);
            Route::post('/reorder', [KnowledgeCategoryController::class, 'reorder']);
            Route::get('/', [KnowledgeCategoryController::class, 'index']);
            Route::post('/', [KnowledgeCategoryController::class, 'store']);
            Route::get('/{id}', [KnowledgeCategoryController::class, 'show']);
            Route::put('/{id}', [KnowledgeCategoryController::class, 'update']);
            Route::delete('/{id}', [KnowledgeCategoryController::class, 'destroy']);
            Route::post('/{id}/move', [KnowledgeCategoryController::class, 'move']);
            Route::post('/{id}/update-count', [KnowledgeCategoryController::class, 'updateItemCount']);
        });

        // ナレッジアイテムルート
        Route::get('/stats', [KnowledgeController::class, 'stats']);
        Route::get('/due-review', [KnowledgeController::class, 'dueReview']);
        Route::post('/quick-capture', [KnowledgeController::class, 'quickCapture']);

        // スマートAI機能
        Route::post('/suggest-category', [KnowledgeController::class, 'suggestCategory']);
        Route::post('/suggest-tags', [KnowledgeController::class, 'suggestTags']);

        Route::put('/bulk-tag', [KnowledgeController::class, 'bulkTag']);
        Route::put('/bulk-move', [KnowledgeController::class, 'bulkMove']);
        Route::delete('/bulk-delete', [KnowledgeController::class, 'bulkDelete']);
        Route::get('/', [KnowledgeController::class, 'index']);
        Route::post('/', [KnowledgeController::class, 'store']);
        Route::get('/{id}', [KnowledgeController::class, 'show']);
        Route::put('/{id}', [KnowledgeController::class, 'update']);
        Route::delete('/{id}', [KnowledgeController::class, 'destroy']);
        Route::put('/{id}/favorite', [KnowledgeController::class, 'toggleFavorite']);
        Route::put('/{id}/archive', [KnowledgeController::class, 'toggleArchive']);
        Route::put('/{id}/review', [KnowledgeController::class, 'markReviewed']);
        Route::post('/{id}/add-to-review', [KnowledgeController::class, 'addToReview']);
        Route::post('/{id}/clone', [KnowledgeController::class, 'clone']);
        Route::get('/{id}/related', [KnowledgeController::class, 'related']);
    });

    // 設定ルート
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index']);
        Route::put('/', [SettingsController::class, 'update']);
        Route::post('/reset', [SettingsController::class, 'reset']);
        Route::patch('/{key}', [SettingsController::class, 'updateSetting']);
    });

    // 通知ルート
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread/count', [NotificationController::class, 'unreadCount']);
        Route::get('/recent', [NotificationController::class, 'recent']);
        Route::get('/stats', [NotificationController::class, 'stats']);
        Route::post('/', [NotificationController::class, 'store']);
        Route::put('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::put('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
        Route::delete('/clear-read', [NotificationController::class, 'clearRead']);
    });

    // 放棄統計ルート
    Route::prefix('abandonments')->group(function () {
        Route::get('/', [TaskTrackingController::class, 'getUserAbandonments']);
        Route::get('/stats', [TaskTrackingController::class, 'getAbandonmentStats']);
    });

});
