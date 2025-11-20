<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\AISuggestion;
use App\Models\AISummary;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\TimetableClass;
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

        // Check if API key is set
        $apiKeySet = !empty(config('services.openai.api_key'));
        $apiKeyMasked = $apiKeySet ? substr(config('services.openai.api_key'), 0, 7) . '...' : 'Not set';

        return response()->json([
            'success' => true,
            'data' => [
                'status' => $status,
                'connected' => $isConnected,
                'api_key_configured' => $apiKeySet,
                'api_key_preview' => $apiKeyMasked,
                'model' => $status['model'] ?? 'unknown',
                'fallback_model' => $status['fallback_model'] ?? 'unknown',
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

    /**
     * Get all conversations for authenticated user
     * GET /api/ai/chat/conversations
     */
    public function getConversations(Request $request): JsonResponse
    {
        $query = ChatConversation::where('user_id', $request->user()->id)
            ->with(['messages' => function($q) {
                $q->latest()->limit(1); // Only get last message for preview
            }]);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'last_message_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginate
        $perPage = min($request->get('per_page', 20), 100);
        $conversations = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $conversations,
            'message' => '会話リストを取得しました'
        ]);
    }

    /**
     * Get a specific conversation with messages
     * GET /api/ai/chat/conversations/{id}
     */
    public function getConversation(Request $request, string $id): JsonResponse
    {
        $conversation = ChatConversation::where('user_id', $request->user()->id)
            ->with('messages')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $conversation,
            'message' => '会話を取得しました'
        ]);
    }

    /**
     * Create a new conversation
     * POST /api/ai/chat/conversations
     */
    public function createConversation(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        try {
            DB::beginTransaction();

            // Create conversation
            $conversation = ChatConversation::create([
                'user_id' => $request->user()->id,
                'title' => $request->title,
                'status' => 'active',
            ]);

            // Create first user message
            $userMessage = ChatMessage::create([
                'conversation_id' => $conversation->id,
                'user_id' => $request->user()->id,
                'role' => 'user',
                'content' => $request->message,
            ]);

            // Parse task intent from user message
            $taskData = $this->aiService->parseTaskIntent($request->message);
            $createdTask = null;

            // If task intent detected, create task
            if ($taskData) {
                try {
                    // Convert priority string to integer
                    $priorityMap = [
                        'low' => 2,
                        'medium' => 3,
                        'high' => 5,
                    ];
                    $priorityValue = $taskData['priority'] ?? 'medium';
                    if (is_string($priorityValue)) {
                        $priorityValue = strtolower($priorityValue);
                        $priorityInt = $priorityMap[$priorityValue] ?? 3;
                    } else {
                        $priorityInt = $priorityValue;
                    }

                    $createdTask = Task::create([
                        'user_id' => $request->user()->id,
                        'title' => $taskData['title'],
                        'description' => $taskData['description'] ?? null,
                        'estimated_minutes' => $taskData['estimated_minutes'] ?? null,
                        'priority' => $priorityInt,
                        'deadline' => $taskData['deadline'] ?? now()->format('Y-m-d'),
                        'scheduled_time' => $taskData['scheduled_time'] ?? null,
                        'status' => 'pending',
                    ]);

                    // Create subtasks if provided
                    if (!empty($taskData['subtasks'])) {
                        foreach ($taskData['subtasks'] as $index => $subtaskData) {
                            $createdTask->subtasks()->create([
                                'title' => $subtaskData['title'],
                                'estimated_minutes' => $subtaskData['estimated_minutes'] ?? null,
                                'sort_order' => $index + 1,
                            ]);
                        }
                    }

                    // Add tags if provided
                    if (!empty($taskData['tags'])) {
                        foreach ($taskData['tags'] as $tagName) {
                            $tag = \App\Models\Tag::firstOrCreate([
                                'user_id' => $request->user()->id,
                                'name' => $tagName
                            ]);
                            $createdTask->tags()->attach($tag->id);
                        }
                    }

                    $createdTask->load(['subtasks', 'tags']);

                    Log::info('Task created from chat', [
                        'task_id' => $createdTask->id,
                        'conversation_id' => $conversation->id
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to create task from chat: ' . $e->getMessage());
                    // Continue without task creation
                }
            }

            // Get AI response
            $aiResponse = $this->aiService->chat([
                [
                    'role' => 'user',
                    'content' => $request->message
                ]
            ]);

            // Check if AI service returned an error
            if (!empty($aiResponse['error'])) {
                DB::rollBack();
                Log::warning('AI service error during conversation creation', [
                    'user_id' => $request->user()->id,
                    'message' => $aiResponse['message'] ?? 'Unknown error',
                    'debug_info' => $aiResponse['debug_info'] ?? null
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $aiResponse['message'] ?? 'AIサービスに接続できませんでした',
                    'error' => 'ai_service_unavailable',
                    'debug' => $aiResponse['debug_info'] ?? null
                ], 503);
            }

            // If task was created, add confirmation to AI response
            if ($createdTask) {
                $taskConfirmation = "\n\n✅ タスクを作成しました: 「{$createdTask->title}」";
                if ($createdTask->subtasks->count() > 0) {
                    $taskConfirmation .= "\n📝 サブタスク: {$createdTask->subtasks->count()}個";
                }
                $aiResponse['message'] = $aiResponse['message'] . $taskConfirmation;
            }

            // Create assistant message
            $assistantMessage = ChatMessage::create([
                'conversation_id' => $conversation->id,
                'user_id' => $request->user()->id,
                'role' => 'assistant',
                'content' => $aiResponse['message'] ?? '応答を生成できませんでした',
                'token_count' => $aiResponse['tokens'] ?? null,
                'metadata' => [
                    'model' => $aiResponse['model'] ?? null,
                    'finish_reason' => $aiResponse['finish_reason'] ?? null,
                ],
            ]);

            // Update conversation stats
            $conversation->updateStats();

            // Generate title if not provided
            if (!$request->title) {
                $conversation->generateTitle();
            }

            DB::commit();

            $conversation->load('messages');

            $responseData = [
                'conversation' => $conversation,
            ];

            // Include created task if any
            if ($createdTask) {
                $responseData['created_task'] = $createdTask;
            }

            return response()->json([
                'success' => true,
                'data' => $responseData,
                'message' => '新しい会話を作成しました！'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Chat conversation creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => '会話の作成に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send a message in existing conversation
     * POST /api/ai/chat/conversations/{id}/messages
     */
    public function sendMessage(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $conversation = ChatConversation::where('user_id', $request->user()->id)
            ->findOrFail($id);

        if ($conversation->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'この会話は現在アクティブではありません'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Create user message
            $userMessage = ChatMessage::create([
                'conversation_id' => $conversation->id,
                'user_id' => $request->user()->id,
                'role' => 'user',
                'content' => $request->message,
            ]);

            // Parse task intent from user message
            $taskData = $this->aiService->parseTaskIntent($request->message);
            $createdTask = null;

            // If task intent detected, create task
            if ($taskData) {
                try {
                    // Convert priority string to integer
                    $priorityMap = [
                        'low' => 2,
                        'medium' => 3,
                        'high' => 5,
                    ];
                    $priorityValue = $taskData['priority'] ?? 'medium';
                    if (is_string($priorityValue)) {
                        $priorityValue = strtolower($priorityValue);
                        $priorityInt = $priorityMap[$priorityValue] ?? 3;
                    } else {
                        $priorityInt = $priorityValue;
                    }

                    $createdTask = Task::create([
                        'user_id' => $request->user()->id,
                        'title' => $taskData['title'],
                        'description' => $taskData['description'] ?? null,
                        'estimated_minutes' => $taskData['estimated_minutes'] ?? null,
                        'priority' => $priorityInt,
                        'deadline' => $taskData['deadline'] ?? now()->format('Y-m-d'),
                        'scheduled_time' => $taskData['scheduled_time'] ?? null,
                        'status' => 'pending',
                    ]);

                    // Create subtasks if provided
                    if (!empty($taskData['subtasks'])) {
                        foreach ($taskData['subtasks'] as $index => $subtaskData) {
                            $createdTask->subtasks()->create([
                                'title' => $subtaskData['title'],
                                'estimated_minutes' => $subtaskData['estimated_minutes'] ?? null,
                                'sort_order' => $index + 1,
                            ]);
                        }
                    }

                    // Add tags if provided
                    if (!empty($taskData['tags'])) {
                        foreach ($taskData['tags'] as $tagName) {
                            $tag = \App\Models\Tag::firstOrCreate([
                                'user_id' => $request->user()->id,
                                'name' => $tagName
                            ]);
                            $createdTask->tags()->attach($tag->id);
                        }
                    }

                    $createdTask->load(['subtasks', 'tags']);

                    Log::info('Task created from chat', [
                        'task_id' => $createdTask->id,
                        'conversation_id' => $conversation->id
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to create task from chat: ' . $e->getMessage());
                    // Continue without task creation
                }
            }

            // Get conversation history (last 10 messages for context)
            $history = $conversation->messages()
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->reverse()
                ->map(function($msg) {
                    return [
                        'role' => $msg->role,
                        'content' => $msg->content
                    ];
                })
                ->toArray();

            // Get AI response
            $aiResponse = $this->aiService->chat($history);

            // Check if AI service returned an error
            if (!empty($aiResponse['error'])) {
                DB::rollBack();
                Log::warning('AI service error during message sending', [
                    'user_id' => $request->user()->id,
                    'conversation_id' => $conversation->id,
                    'message' => $aiResponse['message'] ?? 'Unknown error',
                    'debug_info' => $aiResponse['debug_info'] ?? null
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $aiResponse['message'] ?? 'AIサービスに接続できませんでした',
                    'error' => 'ai_service_unavailable',
                    'debug' => $aiResponse['debug_info'] ?? null
                ], 503);
            }

            // If task was created, add confirmation to AI response
            if ($createdTask) {
                $taskConfirmation = "\n\n✅ タスクを作成しました: 「{$createdTask->title}」";
                if ($createdTask->subtasks->count() > 0) {
                    $taskConfirmation .= "\n📝 サブタスク: {$createdTask->subtasks->count()}個";
                }
                $aiResponse['message'] = $aiResponse['message'] . $taskConfirmation;
            }

            // Create assistant message
            $assistantMessage = ChatMessage::create([
                'conversation_id' => $conversation->id,
                'user_id' => $request->user()->id,
                'role' => 'assistant',
                'content' => $aiResponse['message'] ?? '応答を生成できませんでした',
                'token_count' => $aiResponse['tokens'] ?? null,
                'metadata' => [
                    'model' => $aiResponse['model'] ?? null,
                    'finish_reason' => $aiResponse['finish_reason'] ?? null,
                ],
            ]);

            // Update conversation stats
            $conversation->updateStats();

            DB::commit();

            $responseData = [
                'user_message' => $userMessage,
                'assistant_message' => $assistantMessage,
            ];

            // Include created task if any
            if ($createdTask) {
                $responseData['created_task'] = $createdTask;
            }

            return response()->json([
                'success' => true,
                'data' => $responseData,
                'message' => 'メッセージを送信しました！'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Chat message sending failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'メッセージの送信に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update conversation (title, status)
     * PUT /api/ai/chat/conversations/{id}
     */
    public function updateConversation(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,archived',
        ]);

        $conversation = ChatConversation::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $conversation->update($request->only(['title', 'status']));

        return response()->json([
            'success' => true,
            'data' => $conversation,
            'message' => '会話を更新しました'
        ]);
    }

    /**
     * Delete conversation
     * DELETE /api/ai/chat/conversations/{id}
     */
    public function deleteConversation(Request $request, string $id): JsonResponse
    {
        $conversation = ChatConversation::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $conversation->delete();

        return response()->json([
            'success' => true,
            'message' => '会話を削除しました'
        ]);
    }

    /**
     * Send message with user context (tasks + timetable)
     * This enables AI to give context-aware suggestions
     * POST /api/ai/chat/conversations/{id}/messages/context-aware
     */
    public function sendMessageWithContext(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $conversation = ChatConversation::where('user_id', $request->user()->id)
            ->findOrFail($id);

        if ($conversation->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'この会話は現在アクティブではありません'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $user = $request->user();

            // Create user message
            $userMessage = ChatMessage::create([
                'conversation_id' => $conversation->id,
                'user_id' => $user->id,
                'role' => 'user',
                'content' => $request->message,
            ]);

            // Get conversation history for context-aware intent parsing
            $historyForParsing = $conversation->messages()
                ->orderBy('created_at', 'desc')
                ->limit(5) // Last 5 messages for context
                ->get()
                ->reverse()
                ->map(function($msg) {
                    return [
                        'role' => $msg->role,
                        'content' => $msg->content
                    ];
                })
                ->toArray();

            // Parse timetable intent FIRST (higher priority than task)
            $timetableData = $this->aiService->parseTimetableIntent($request->message, $historyForParsing);
            Log::info('AIController: parseTimetableIntent returned', [
                'has_data' => !is_null($timetableData),
                'data' => $timetableData
            ]);
            $createdTimetableClass = null;

            // Parse task intent only if NO timetable intent detected
            $taskData = null;
            $createdTask = null;
            if (!$timetableData) {
                $taskData = $this->aiService->parseTaskIntent($request->message);
                Log::info('AIController: parseTaskIntent returned', [
                    'has_data' => !is_null($taskData),
                    'data' => $taskData
                ]);
            } else {
                Log::info('AIController: Skipping task intent parsing because timetable intent was detected');
            }

            // If task intent detected, create task
            if ($taskData) {
                try {
                    // Convert priority string to integer
                    $priorityMap = [
                        'low' => 2,
                        'medium' => 3,
                        'high' => 5,
                    ];
                    $priorityValue = $taskData['priority'] ?? 'medium';
                    if (is_string($priorityValue)) {
                        $priorityValue = strtolower($priorityValue);
                        $priorityInt = $priorityMap[$priorityValue] ?? 3;
                    } else {
                        $priorityInt = $priorityValue;
                    }

                    $createdTask = Task::create([
                        'user_id' => $user->id,
                        'title' => $taskData['title'],
                        'description' => $taskData['description'] ?? null,
                        'estimated_minutes' => $taskData['estimated_minutes'] ?? null,
                        'priority' => $priorityInt,
                        'deadline' => $taskData['deadline'] ?? now()->format('Y-m-d'),
                        'scheduled_time' => $taskData['scheduled_time'] ?? null,
                        'status' => 'pending',
                    ]);

                    // Create subtasks if provided
                    if (!empty($taskData['subtasks'])) {
                        foreach ($taskData['subtasks'] as $index => $subtaskData) {
                            $createdTask->subtasks()->create([
                                'title' => $subtaskData['title'],
                                'estimated_minutes' => $subtaskData['estimated_minutes'] ?? null,
                                'sort_order' => $index + 1,
                            ]);
                        }
                    }

                    // Add tags if provided
                    if (!empty($taskData['tags'])) {
                        foreach ($taskData['tags'] as $tagName) {
                            $tag = \App\Models\Tag::firstOrCreate([
                                'user_id' => $user->id,
                                'name' => $tagName
                            ]);
                            $createdTask->tags()->attach($tag->id);
                        }
                    }

                    $createdTask->load(['subtasks', 'tags']);

                    Log::info('Task created from context-aware chat', [
                        'task_id' => $createdTask->id,
                        'conversation_id' => $conversation->id
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to create task from context-aware chat: ' . $e->getMessage());
                    // Continue without task creation
                }
            }

            // If timetable intent detected, prepare suggestion (don't auto-create)
            $timetableSuggestion = null;
            if ($timetableData) {
                Log::info('AIController: Timetable data detected, preparing suggestion', [
                    'timetable_data' => $timetableData,
                    'user_id' => $user->id
                ]);

                // Calculate period if not provided (assume 1 period = 1 hour)
                $period = $timetableData['period'] ?? null;
                if (!$period) {
                    // Calculate period from time duration
                    $start = \Carbon\Carbon::createFromFormat('H:i', $timetableData['start_time']);
                    $end = \Carbon\Carbon::createFromFormat('H:i', $timetableData['end_time']);
                    $durationHours = $start->diffInHours($end);
                    $period = max(1, round($durationHours)); // At least 1 period
                }

                // Prepare suggestion for user confirmation
                $timetableSuggestion = [
                    'name' => $timetableData['name'],
                    'description' => $timetableData['description'] ?? null,
                    'room' => $timetableData['room'] ?? null,
                    'instructor' => $timetableData['instructor'] ?? null,
                    'day' => $timetableData['day'],
                    'period' => $period,
                    'start_time' => $timetableData['start_time'],
                    'end_time' => $timetableData['end_time'],
                    'color' => $timetableData['color'] ?? '#6366f1',
                    'icon' => $timetableData['icon'] ?? '📚',
                ];

                Log::info('AIController: Timetable suggestion prepared', [
                    'suggestion' => $timetableSuggestion
                ]);
            }

            // Load user context: tasks + timetable
            $tasks = Task::where('user_id', $user->id)
                ->where('status', '!=', 'completed')
                ->where('status', '!=', 'cancelled')
                ->with(['subtasks', 'tags'])
                ->orderBy('priority', 'desc')
                ->orderBy('deadline', 'asc')
                ->limit(20) // Limit to avoid token overflow
                ->get();

            // Load all timetable (entire week) so AI can answer questions about any day
            $today = now();
            $dayNames = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
            $todayDayName = $dayNames[$today->dayOfWeek];

            $allTimetable = \App\Models\TimetableClass::where('user_id', $user->id)
                ->orderBy('day', 'asc')
                ->orderBy('start_time', 'asc')
                ->get();

            // Group timetable by day
            $timetableByDay = [];
            foreach ($allTimetable as $class) {
                if (!isset($timetableByDay[$class->day])) {
                    $timetableByDay[$class->day] = [];
                }
                $timetableByDay[$class->day][] = [
                    'time' => $class->start_time,
                    'title' => $class->name,
                    'class_name' => $class->name,
                ];
            }

            // Build user context with full week timetable
            $userContext = [
                'tasks' => $tasks->toArray(),
                'timetable' => $timetableByDay,
                'today' => $todayDayName,
            ];

            // Get conversation history (last 10 messages for context)
            $history = $conversation->messages()
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->reverse()
                ->map(function($msg) {
                    return [
                        'role' => $msg->role,
                        'content' => $msg->content
                    ];
                })
                ->toArray();

            // Get AI response WITH CONTEXT
            $aiResponse = $this->aiService->chatWithUserContext($history, $userContext);

            // Check if AI service returned an error
            if (!empty($aiResponse['error'])) {
                DB::rollBack();
                Log::warning('AI service error during context-aware message', [
                    'user_id' => $user->id,
                    'conversation_id' => $conversation->id,
                    'message' => $aiResponse['message'] ?? 'Unknown error'
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $aiResponse['message'] ?? 'AIサービスに接続できませんでした',
                    'error' => 'ai_service_unavailable'
                ], 503);
            }

            // If task was created, add confirmation to AI response
            if ($createdTask) {
                $taskConfirmation = "\n\n✅ タスクを作成しました: 「{$createdTask->title}」";
                if ($createdTask->subtasks->count() > 0) {
                    $taskConfirmation .= "\n📝 サブタスク: {$createdTask->subtasks->count()}個";
                }
                $aiResponse['message'] = $aiResponse['message'] . $taskConfirmation;
            }

            // Note: Timetable suggestions are handled by Android UI, no need to modify message

            // Create assistant message
            $assistantMessage = ChatMessage::create([
                'conversation_id' => $conversation->id,
                'user_id' => $user->id,
                'role' => 'assistant',
                'content' => $aiResponse['message'] ?? '応答を生成できませんでした',
                'token_count' => $aiResponse['tokens'] ?? null,
                'metadata' => [
                    'model' => $aiResponse['model'] ?? null,
                    'finish_reason' => $aiResponse['finish_reason'] ?? null,
                    'has_task_suggestion' => !empty($aiResponse['task_suggestion']),
                ],
            ]);

            // Update conversation stats
            $conversation->updateStats();

            DB::commit();

            $responseData = [
                'user_message' => $userMessage,
                'assistant_message' => $assistantMessage,
                'created_task' => $createdTask, // Auto-created task from parseTaskIntent
                'task_suggestion' => $aiResponse['task_suggestion'] ?? null, // AI task suggestion (requires user confirmation)
                'timetable_suggestion' => $timetableSuggestion, // Timetable class suggestion (requires user confirmation)
            ];

            return response()->json([
                'success' => true,
                'data' => $responseData,
                'message' => 'メッセージを送信しました！'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Context-aware chat message failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'メッセージの送信に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get proactive daily planning from AI
     * AI analyzes user's schedule, tasks, and provides proactive suggestions
     * GET /api/ai/daily-plan
     */
    public function getDailyPlan(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Load user context: tasks + timetable
            $tasks = Task::where('user_id', $user->id)
                ->where('status', '!=', 'completed')
                ->where('status', '!=', 'cancelled')
                ->with(['subtasks', 'tags'])
                ->orderBy('priority', 'desc')
                ->orderBy('deadline', 'asc')
                ->limit(20)
                ->get();

            // Load all timetable (entire week) so AI can answer questions about any day
            $today = now();
            $dayNames = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
            $todayDayName = $dayNames[$today->dayOfWeek];

            $allTimetable = \App\Models\TimetableClass::where('user_id', $user->id)
                ->orderBy('day', 'asc')
                ->orderBy('start_time', 'asc')
                ->get();

            // Group timetable by day
            $timetableByDay = [];
            foreach ($allTimetable as $class) {
                if (!isset($timetableByDay[$class->day])) {
                    $timetableByDay[$class->day] = [];
                }
                $timetableByDay[$class->day][] = [
                    'time' => $class->start_time,
                    'title' => $class->name,
                    'class_name' => $class->name,
                ];
            }

            // Build user context with full week timetable
            $userContext = [
                'tasks' => $tasks->toArray(),
                'timetable' => $timetableByDay,
                'today' => $todayDayName,
            ];

            // Create proactive prompt for daily planning
            $proactivePrompt = "今日の予定とタスクを分析して、最適な一日の計画を立ててください。以下の点に注目してください:

1. **期限が近いタスク**: 優先的に取り組むべきタスクを特定
2. **空き時間の活用**: スケジュールの隙間時間を効率的に使う方法
3. **タスクの配置**: 各タスクに最適な時間帯を提案
4. **バランス**: 作業と休憩のバランスを考慮
5. **具体的なアクション**: 今すぐ始められる具体的なステップ

可能であれば、最も重要なタスクをtask_suggestionとして提案してください。";

            // Get AI response WITH CONTEXT
            $aiResponse = $this->aiService->chatWithUserContext([
                ['role' => 'user', 'content' => $proactivePrompt]
            ], $userContext);

            // Check if AI service returned an error
            if (!empty($aiResponse['error'])) {
                Log::warning('AI service error during daily plan generation', [
                    'user_id' => $user->id,
                    'message' => $aiResponse['message'] ?? 'Unknown error'
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $aiResponse['message'] ?? 'AIサービスに接続できませんでした',
                    'error' => 'ai_service_unavailable'
                ], 503);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'plan' => $aiResponse['message'],
                    'task_suggestion' => $aiResponse['task_suggestion'] ?? null,
                    'generated_at' => now()->toISOString(),
                ],
                'message' => '本日の計画を生成しました！'
            ]);

        } catch (\Exception $e) {
            Log::error('Daily plan generation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => '計画の生成に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get proactive weekly summary and suggestions
     * AI analyzes past week and suggests improvements
     * GET /api/ai/weekly-insights
     */
    public function getWeeklyInsights(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Get tasks from past week
            $weekAgo = now()->subDays(7);
            $tasks = Task::where('user_id', $user->id)
                ->where('updated_at', '>=', $weekAgo)
                ->with(['subtasks', 'tags'])
                ->get();

            $completedTasks = $tasks->where('status', 'completed');
            $pendingTasks = $tasks->where('status', 'pending');
            $inProgressTasks = $tasks->where('status', 'in_progress');

            // Get focus sessions from past week
            $sessions = \App\Models\FocusSession::where('user_id', $user->id)
                ->where('started_at', '>=', $weekAgo)
                ->where('status', 'completed')
                ->get();

            $totalFocusTime = $sessions->sum('actual_minutes');
            $avgSessionLength = $sessions->avg('actual_minutes');

            // Build insights prompt
            $insightsPrompt = "過去1週間のデータを分析して、洞察と改善提案を提供してください:

週間統計:
- 完了タスク: " . $completedTasks->count() . "個
- 進行中タスク: " . $inProgressTasks->count() . "個
- 保留タスク: " . $pendingTasks->count() . "個
- 総フォーカス時間: " . $totalFocusTime . "分
- 平均セッション長: " . round($avgSessionLength, 1) . "分
- フォーカスセッション数: " . $sessions->count() . "回

以下の点について分析してください:
1. **達成度**: タスク完了率とその評価
2. **生産性パターン**: 最も生産的な時間帯や曜日
3. **改善点**: 来週改善できること
4. **強み**: 良かった点や継続すべきこと
5. **推奨アクション**: 具体的な改善策

励ましの言葉と共に、建設的なフィードバックを提供してください。";

            // Get AI response
            $aiResponse = $this->aiService->chat([
                ['role' => 'user', 'content' => $insightsPrompt]
            ], [
                'system_prompt' => 'あなたは親切で励ましを与える生産性コーチです。ユーザーの努力を認め、建設的なアドバイスを提供してください。日本語で応答してください。',
                'temperature' => 0.7,
            ]);

            // Check if AI service returned an error
            if (!empty($aiResponse['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => $aiResponse['message'] ?? 'AIサービスに接続できませんでした',
                    'error' => 'ai_service_unavailable'
                ], 503);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'insights' => $aiResponse['message'],
                    'stats' => [
                        'completed_tasks' => $completedTasks->count(),
                        'pending_tasks' => $pendingTasks->count(),
                        'in_progress_tasks' => $inProgressTasks->count(),
                        'total_focus_time' => $totalFocusTime,
                        'average_session_length' => round($avgSessionLength, 1),
                        'total_sessions' => $sessions->count(),
                    ],
                    'generated_at' => now()->toISOString(),
                ],
                'message' => '週間インサイトを生成しました！'
            ]);

        } catch (\Exception $e) {
            Log::error('Weekly insights generation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'インサイトの生成に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm and create task from AI suggestion
     * POST /api/ai/chat/task-suggestions/confirm
     */
    public function confirmTaskSuggestion(Request $request): JsonResponse
    {
        // Normalize scheduled_time: add :00 if only HH:mm format
        if ($request->has('scheduled_time') && $request->scheduled_time) {
            $time = $request->scheduled_time;
            // If format is HH:mm (only 1 colon), add :00 for seconds
            if (substr_count($time, ':') === 1) {
                $request->merge(['scheduled_time' => $time . ':00']);
            }
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'estimated_minutes' => 'nullable|integer|min:1|max:600',
            'priority' => 'required|in:high,medium,low',
            'scheduled_time' => 'nullable|date_format:H:i:s',
        ]);

        try {
            DB::beginTransaction();

            $user = $request->user();

            // Convert priority string to integer
            $priorityMap = [
                'low' => 2,
                'medium' => 3,
                'high' => 5,
            ];

            $task = Task::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'description' => $request->description,
                'estimated_minutes' => $request->estimated_minutes,
                'priority' => $priorityMap[$request->priority] ?? 3,
                'deadline' => now()->format('Y-m-d'),
                'scheduled_time' => $request->scheduled_time,
                'status' => 'pending',
                'category' => 'other',
                'energy_level' => 'medium',
            ]);

            DB::commit();

            $task->load(['subtasks', 'tags']);

            Log::info('Task created from AI suggestion', [
                'task_id' => $task->id,
                'user_id' => $user->id,
                'title' => $task->title
            ]);

            return response()->json([
                'success' => true,
                'data' => $task,
                'message' => 'タスクを作成しました！'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Task suggestion confirmation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'タスクの作成に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm and create timetable class from AI suggestion
     * POST /api/ai/chat/timetable-suggestions/confirm
     */
    public function confirmTimetableSuggestion(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'room' => 'nullable|string|max:100',
            'instructor' => 'nullable|string|max:255',
            'day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'period' => 'required|integer|min:1|max:10',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:10',
        ]);

        try {
            DB::beginTransaction();

            $user = $request->user();

            $timetableClass = TimetableClass::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'description' => $request->description,
                'room' => $request->room,
                'instructor' => $request->instructor,
                'day' => $request->day,
                'period' => $request->period,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'color' => $request->color ?? '#6366f1',
                'icon' => $request->icon ?? '📚',
            ]);

            DB::commit();

            Log::info('Timetable class created from AI suggestion', [
                'class_id' => $timetableClass->id,
                'user_id' => $user->id,
                'name' => $timetableClass->name
            ]);

            return response()->json([
                'success' => true,
                'data' => $timetableClass,
                'message' => '授業を登録しました！'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Timetable suggestion confirmation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => '授業の登録に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
