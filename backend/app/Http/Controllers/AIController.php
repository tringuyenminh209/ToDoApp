<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\AISuggestion;
use App\Models\AISummary;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    private AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * AI breakdown task thành subtasks
     * POST /api/ai/breakdown-task
     */
    public function breakdownTask(Request $request): JsonResponse
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'complexity_level' => 'in:simple,medium,complex',
        ]);

        $task = Task::with('subtasks')->findOrFail($request->task_id);

        // Kiểm tra quyền truy cập
        if ($task->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'このタスクにアクセスする権限がありません'
            ], 403);
        }

        // Kiểm tra đã có subtasks chưa
        if ($task->subtasks()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'このタスクは既にサブタスクに分割されています'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Gọi AI Service
            $subtasks = $this->aiService->breakdownTask(
                $task->title,
                $task->description ?? '',
                $request->complexity_level ?? 'medium'
            );

            // Tạo subtasks
            foreach ($subtasks as $index => $subtaskData) {
                $task->subtasks()->create([
                    'title' => $subtaskData['title'],
                    'estimated_minutes' => $subtaskData['estimated_minutes'] ?? 30,
                    'sort_order' => $index + 1,
                ]);
            }

            // Cập nhật task
            $task->update(['ai_breakdown_enabled' => true]);

            DB::commit();

            $task->load('subtasks');

            return response()->json([
                'success' => true,
                'data' => $task,
                'message' => 'タスクをAIで分割しました！'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('AI breakdown failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'AI分割に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI suggestions cho daily tasks
     * GET /api/ai/daily-suggestions
     */
    public function dailySuggestions(Request $request): JsonResponse
    {
        $user = $request->user();

        // Lấy thông tin user và tasks gần đây
        $recentTasks = Task::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->with('tags')
            ->get();

        $completedTasks = Task::where('user_id', $user->id)
            ->where('status', 'completed')
            ->where('updated_at', '>=', now()->subDays(3))
            ->get();

        try {
            // Gọi AI Service
            $suggestions = $this->aiService->generateDailySuggestions(
                $recentTasks->toArray(),
                $completedTasks->toArray()
            );

            // Lưu suggestions vào database
            $aiSuggestion = AISuggestion::create([
                'user_id' => $user->id,
                'type' => 'daily_plan',
                'content' => $suggestions,  // Model auto-casts to JSON
                'is_accepted' => false,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'suggestions' => $suggestions,
                    'suggestion_id' => $aiSuggestion->id,
                ],
                'message' => 'AI提案を取得しました！'
            ]);

        } catch (\Exception $e) {
            Log::error('AI suggestions failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'AI提案の取得に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI summary cho daily review
     * POST /api/ai/daily-summary
     */
    public function dailySummary(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $user = $request->user();
        $date = $request->date;

        // Lấy tasks và sessions của ngày
        $tasks = Task::where('user_id', $user->id)
            ->whereDate('updated_at', $date)
            ->with(['subtasks', 'tags'])
            ->get();

        $sessions = \App\Models\FocusSession::where('user_id', $user->id)
            ->whereDate('started_at', $date)
            ->where('status', 'completed')
            ->get();

        try {
            // Gọi AI Service
            $summary = $this->aiService->generateDailySummary(
                $tasks->toArray(),
                $sessions->toArray(),
                $date
            );

            // Lưu summary vào database
            $aiSummary = AISummary::create([
                'user_id' => $user->id,
                'summary_type' => 'daily',
                'date' => $date,
                'content' => $summary,  // Model auto-casts to JSON
                'metrics' => [
                    'tasks_completed' => $tasks->where('status', 'completed')->count(),
                    'tasks_total' => $tasks->count(),
                    'sessions_count' => $sessions->count(),
                    'total_focus_time' => $sessions->sum('actual_minutes'),
                ],  // Model auto-casts to JSON
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'summary' => $summary,
                    'summary_id' => $aiSummary->id,
                ],
                'message' => 'AIサマリーを生成しました！'
            ]);

        } catch (\Exception $e) {
            Log::error('AI summary failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'AIサマリーの生成に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy AI suggestions history
     * GET /api/ai/suggestions
     */
    public function suggestions(Request $request): JsonResponse
    {
        $query = AISuggestion::where('user_id', $request->user()->id);

        // Filtering
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('is_accepted')) {
            $query->where('is_accepted', $request->boolean('is_accepted'));
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSortFields = ['created_at', 'type', 'feedback_score'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $suggestions = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $suggestions,
            'message' => 'AI提案履歴を取得しました'
        ]);
    }

    /**
     * Đánh dấu suggestion đã accept
     * PUT /api/ai/suggestions/{id}/read
     */
    public function markSuggestionRead(Request $request, string $id): JsonResponse
    {
        $suggestion = AISuggestion::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $suggestion->update(['is_accepted' => true]);

        return response()->json([
            'success' => true,
            'data' => $suggestion,
            'message' => '提案を承認しました'
        ]);
    }

    /**
     * Lấy AI summaries
     * GET /api/ai/summaries
     */
    public function summaries(Request $request): JsonResponse
    {
        $query = AISummary::where('user_id', $request->user()->id);

        // Filtering
        if ($request->has('summary_type')) {
            $query->where('summary_type', $request->summary_type);
        }

        if ($request->has('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        // Sorting
        $query->orderBy('date', 'desc');

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $summaries = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $summaries,
            'message' => 'AIサマリー履歴を取得しました'
        ]);
    }

    /**
     * Get AI service status
     * GET /api/ai/status
     */
    public function status(): JsonResponse
    {
        $status = $this->aiService->getStatus();
        $isConnected = $this->aiService->testConnection();

        return response()->json([
            'success' => true,
            'data' => [
                'status' => $status,
                'connected' => $isConnected,
                'last_checked' => now()->toISOString(),
            ],
            'message' => 'AIサービスステータスを取得しました'
        ]);
    }

    /**
     * Generate productivity insights
     * POST /api/ai/insights
     */
    public function insights(Request $request): JsonResponse
    {
        $request->validate([
            'weekly_data' => 'required|array',
            'trends' => 'array',
        ]);

        try {
            $insights = $this->aiService->generateProductivityInsights(
                $request->weekly_data,
                $request->trends ?? []
            );

            return response()->json([
                'success' => true,
                'data' => $insights,
                'message' => '生産性インサイトを生成しました！'
            ]);

        } catch (\Exception $e) {
            Log::error('AI insights failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'インサイトの生成に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate learning recommendations
     * POST /api/ai/learning-recommendations
     */
    public function learningRecommendations(Request $request): JsonResponse
    {
        $request->validate([
            'completed_tasks' => 'required|array',
            'learning_paths' => 'array',
        ]);

        try {
            $recommendations = $this->aiService->generateLearningRecommendations(
                $request->completed_tasks,
                $request->learning_paths ?? []
            );

            return response()->json([
                'success' => true,
                'data' => $recommendations,
                'message' => '学習推奨事項を生成しました！'
            ]);

        } catch (\Exception $e) {
            Log::error('AI learning recommendations failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => '学習推奨事項の生成に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Analyze focus patterns
     * POST /api/ai/focus-analysis
     */
    public function focusAnalysis(Request $request): JsonResponse
    {
        $request->validate([
            'sessions' => 'required|array',
            'productivity_data' => 'array',
        ]);

        try {
            $analysis = $this->aiService->analyzeFocusPatterns(
                $request->sessions,
                $request->productivity_data ?? []
            );

            return response()->json([
                'success' => true,
                'data' => $analysis,
                'message' => 'フォーカスパターン分析を完了しました！'
            ]);

        } catch (\Exception $e) {
            Log::error('AI focus analysis failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'フォーカスパターン分析に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate motivational message
     * POST /api/ai/motivational-message
     */
    public function motivationalMessage(Request $request): JsonResponse
    {
        $request->validate([
            'mood' => 'required|string',
            'achievements' => 'array',
            'goals' => 'array',
        ]);

        try {
            $message = $this->aiService->generateMotivationalMessage(
                $request->mood,
                $request->achievements ?? [],
                $request->goals ?? []
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'message' => $message,
                    'mood' => $request->mood,
                    'generated_at' => now()->toISOString(),
                ],
                'message' => '励ましのメッセージを生成しました！'
            ]);

        } catch (\Exception $e) {
            Log::error('AI motivational message failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => '励ましのメッセージの生成に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
