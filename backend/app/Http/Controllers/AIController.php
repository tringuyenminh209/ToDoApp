<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
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
use Carbon\Carbon;

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

            $instantReply = $this->getInstantReplyResponse($request->message);
            if ($instantReply !== null) {
                $assistantMessage = ChatMessage::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $request->user()->id,
                    'role' => 'assistant',
                    'content' => $instantReply,
                    'token_count' => null,
                    'metadata' => [
                        'model' => 'local_instant_reply',
                        'finish_reason' => 'stop',
                    ],
                ]);

                $conversation->updateStats();

                // Generate title if not provided
                if (!$request->title) {
                    $conversation->generateTitle();
                }

                DB::commit();

                // Load messages for response
                $conversation->load('messages');

                return response()->json([
                    'success' => true,
                    'data' => [
                        'conversation' => $conversation,
                        'user_message' => $userMessage,
                        'assistant_message' => $assistantMessage,
                    ],
                    'message' => 'メッセージを送信しました！'
                ], 201);
            }

            // Parse task intent from user message
            $taskData = $this->aiService->parseTaskIntent($request->message);
            $createdTask = null;

            // Debug: Log task intent parsing result
            Log::info('Task intent parsing result in createConversation', [
                'message' => $request->message,
                'has_task_data' => !is_null($taskData),
                'task_data' => $taskData
            ]);

            // Fallback: If parseTaskIntent failed but message clearly indicates task creation, try simple extraction
            if (!$taskData && $this->hasTaskCreationKeywords($request->message)) {
                Log::info('parseTaskIntent failed but task keywords detected, trying simple extraction');
                $taskData = $this->extractTaskFromMessage($request->message);
                if ($taskData) {
                    Log::info('Task extracted from message using fallback method', ['task_data' => $taskData]);
                }
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

                    $deadline = $taskData['deadline'] ?? null;
                    if (!$deadline) {
                        $deadline = $this->inferDeadlineFromMessage($request->message, now());
                    }
                    $deadline = $deadline ?? now()->format('Y-m-d');

                    $createdTask = Task::create([
                        'user_id' => $request->user()->id,
                        'title' => $taskData['title'],
                        'description' => $taskData['description'] ?? null,
                        'estimated_minutes' => $taskData['estimated_minutes'] ?? null,
                        'priority' => $priorityInt,
                        'deadline' => $deadline,
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

            $isScheduleQuestion = $this->isScheduleQuestion($request->message);
            if ($isScheduleQuestion) {
                $userContext = $this->buildTimetableContext($request->user(), $request->message);
                $aiResponse = $this->aiService->chatWithUserContext([
                    [
                        'role' => 'user',
                        'content' => $request->message
                    ]
                ], $userContext, [
                    'timeout' => $this->aiService->getContextChatTimeout(12),
                    'max_tokens' => 400,
                    'temperature' => 0.4,
                ]);
            } else {
                // Get AI response
                $aiResponse = $this->aiService->chat([
                    [
                        'role' => 'user',
                        'content' => $request->message
                    ]
                ]);
            }

            // Check if AI service returned an error
            if (!empty($aiResponse['error'])) {
                Log::warning('AI service error during conversation creation', [
                    'user_id' => $request->user()->id,
                    'message' => $aiResponse['message'] ?? 'Unknown error',
                    'debug_info' => $aiResponse['debug_info'] ?? null
                ]);

                $assistantMessage = ChatMessage::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $request->user()->id,
                    'role' => 'assistant',
                    'content' => $this->buildAiUnavailableResponse(),
                    'token_count' => null,
                    'metadata' => [
                        'model' => 'fallback_unavailable',
                        'finish_reason' => 'stop',
                    ],
                ]);

                $conversation->updateStats();
                DB::commit();

                return response()->json([
                    'success' => true,
                    'data' => [
                        'user_message' => $userMessage,
                        'assistant_message' => $assistantMessage,
                    ],
                    'message' => 'メッセージを送信しました！'
                ], 201);
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

            $instantReply = $this->getInstantReplyResponse($request->message);
            if ($instantReply !== null) {
                $assistantMessage = ChatMessage::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $request->user()->id,
                    'role' => 'assistant',
                    'content' => $instantReply,
                    'token_count' => null,
                    'metadata' => [
                        'model' => 'local_instant_reply',
                        'finish_reason' => 'stop',
                    ],
                ]);

                $conversation->updateStats();
                DB::commit();

                return response()->json([
                    'success' => true,
                    'data' => [
                        'user_message' => $userMessage,
                        'assistant_message' => $assistantMessage,
                    ],
                    'message' => 'メッセージを送信しました！'
                ], 201);
            }

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

                    $deadline = $taskData['deadline'] ?? null;
                    if (!$deadline) {
                        $deadline = $this->inferDeadlineFromMessage($request->message, now());
                    }
                    $deadline = $deadline ?? now()->format('Y-m-d');

                    $createdTask = Task::create([
                        'user_id' => $request->user()->id,
                        'title' => $taskData['title'],
                        'description' => $taskData['description'] ?? null,
                        'estimated_minutes' => $taskData['estimated_minutes'] ?? null,
                        'priority' => $priorityInt,
                        'deadline' => $deadline,
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
                ->limit(6)
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
                Log::warning('AI service error during message sending', [
                    'user_id' => $request->user()->id,
                    'conversation_id' => $conversation->id,
                    'message' => $aiResponse['message'] ?? 'Unknown error',
                    'debug_info' => $aiResponse['debug_info'] ?? null
                ]);

                $assistantMessage = ChatMessage::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $request->user()->id,
                    'role' => 'assistant',
                    'content' => $this->buildAiUnavailableResponse(),
                    'token_count' => null,
                    'metadata' => [
                        'model' => 'fallback_unavailable',
                        'finish_reason' => 'stop',
                    ],
                ]);

                $conversation->updateStats();
                DB::commit();

                return response()->json([
                    'success' => true,
                    'data' => [
                        'user_message' => $userMessage,
                        'assistant_message' => $assistantMessage,
                    ],
                    'message' => 'メッセージを送信しました！'
                ], 201);
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

            $instantReply = $this->getInstantReplyResponse($request->message);
            if ($instantReply !== null) {
                $assistantMessage = ChatMessage::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $user->id,
                    'role' => 'assistant',
                    'content' => $instantReply,
                    'token_count' => null,
                    'metadata' => [
                        'model' => 'local_instant_reply',
                        'finish_reason' => 'stop',
                    ],
                ]);

                $conversation->updateStats();
                DB::commit();

                return response()->json([
                    'success' => true,
                    'data' => [
                        'user_message' => $userMessage,
                        'assistant_message' => $assistantMessage,
                    ],
                    'message' => 'メッセージを送信しました！'
                ], 201);
            }

            // Get conversation history for context-aware intent parsing
            $historyForParsing = $conversation->messages()
                ->orderBy('created_at', 'desc')
                ->limit(3) // Shorter history for faster parsing
                ->get()
                ->reverse()
                ->map(function($msg) {
                    return [
                        'role' => $msg->role,
                        'content' => $msg->content
                    ];
                })
                ->toArray();

            $message = $request->message;
            $shouldParseTask = preg_match('/(タスク|task|やる|やりたい|したい|作成|追加|登録|study|work|learn)/iu', $message);
            $shouldParseTimetable = preg_match('/(授業|クラス|class|lecture|時間割|schedule|lịch học|thứ|monday|tuesday|wednesday|thursday|friday|saturday|sunday)/iu', $message);
            $shouldParseKnowledgeQuery = preg_match('/(メモ|ノート|記録|コード|演習|問題|資料|リンク|review|復習|search|探して|見せて)/iu', $message);
            $shouldParseKnowledgeCreation = preg_match('/(追加|作成|保存|記録|フォルダ|カテゴリ|knowledge|note|snippet|exercise|resource)/iu', $message);

            $taskData = null;
            $timetableData = null;
            $knowledgeQueryData = null;
            $knowledgeCreationData = null;
            $hasKnowledgeCreation = false;

            if ($shouldParseTask || $shouldParseTimetable || $shouldParseKnowledgeQuery || $shouldParseKnowledgeCreation) {
                $quickParse = $this->aiService->parseQuickIntents($message, $historyForParsing);

                if ($quickParse !== null) {
                    $taskData = $quickParse['task'] ?? null;
                    $timetableData = $quickParse['timetable'] ?? null;
                    $knowledgeQueryData = $quickParse['knowledge_query'] ?? null;
                    $hasKnowledgeCreation = !empty($quickParse['has_knowledge_creation']);

                    Log::info('AIController: quick intent parse result', [
                        'task' => !is_null($taskData),
                        'timetable' => !is_null($timetableData),
                        'knowledge_query' => !is_null($knowledgeQueryData),
                        'has_knowledge_creation' => $hasKnowledgeCreation
                    ]);
                } else {
                    // Fallback to individual parsers if quick parsing fails
                    $timetableData = $shouldParseTimetable
                        ? $this->aiService->parseTimetableIntent($message, $historyForParsing)
                        : null;
                    Log::info('AIController: parseTimetableIntent returned', [
                        'has_data' => !is_null($timetableData),
                        'data' => $timetableData
                    ]);

                    $taskData = $shouldParseTask
                        ? $this->aiService->parseTaskIntent($message)
                        : null;
                    Log::info('AIController: parseTaskIntent returned', [
                        'has_data' => !is_null($taskData),
                        'data' => $taskData
                    ]);

                    $knowledgeQueryData = $shouldParseKnowledgeQuery
                        ? $this->aiService->parseKnowledgeQueryIntent($message, $historyForParsing)
                        : null;
                    Log::info('AIController: parseKnowledgeQueryIntent returned', [
                        'has_data' => !is_null($knowledgeQueryData),
                        'data' => $knowledgeQueryData
                    ]);

                    $hasKnowledgeCreation = $shouldParseKnowledgeCreation;
                }
            }

            if ($hasKnowledgeCreation) {
                $knowledgeCreationData = $this->aiService->parseKnowledgeCreationIntent(
                    $message,
                    $historyForParsing,
                    $user
                );
                Log::info('AIController: parseKnowledgeCreationIntent returned', [
                    'has_data' => !is_null($knowledgeCreationData),
                    'data' => $knowledgeCreationData
                ]);
            }

            // Allow ALL intents to execute simultaneously
            $createdTimetableClass = null;
            $createdTask = null;
            $knowledgeResults = null;
            $knowledgeCreationResults = null;

            // Log if both intents detected (no longer ignore task)
            if ($timetableData && $taskData) {
                Log::info('AIController: Both intents detected - will create BOTH timetable class AND task');
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

                    $deadline = $taskData['deadline'] ?? null;
                    if (!$deadline) {
                        $deadline = $this->inferDeadlineFromMessage($request->message, now());
                    }
                    $deadline = $deadline ?? now()->format('Y-m-d');

                    $createdTask = Task::create([
                        'user_id' => $user->id,
                        'title' => $taskData['title'],
                        'description' => $taskData['description'] ?? null,
                        'estimated_minutes' => $taskData['estimated_minutes'] ?? null,
                        'priority' => $priorityInt,
                        'deadline' => $deadline,
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

            // NEW: If knowledge query intent detected, search knowledge items
            if ($knowledgeQueryData) {
                Log::info('AIController: Knowledge query detected, searching items', [
                    'query' => $knowledgeQueryData
                ]);

                try {
                    $knowledgeLimit = $this->aiService->isLocalProvider() ? 3 : 5;
                    $query = \App\Models\KnowledgeItem::where('user_id', $user->id)
                        ->where('is_archived', false);

                    // Filter by item type if specified
                    if (!empty($knowledgeQueryData['item_type']) && $knowledgeQueryData['item_type'] !== 'any') {
                        $query->where('item_type', $knowledgeQueryData['item_type']);
                    }

                    // Search by keywords in title, content, question, tags
                    if (!empty($knowledgeQueryData['keywords'])) {
                        $query->where(function($q) use ($knowledgeQueryData) {
                            foreach ($knowledgeQueryData['keywords'] as $keyword) {
                                $q->orWhere('title', 'LIKE', "%{$keyword}%")
                                  ->orWhere('content', 'LIKE', "%{$keyword}%")
                                  ->orWhere('question', 'LIKE', "%{$keyword}%")
                                  ->orWhereJsonContains('tags', $keyword)
                                  ->orWhereJsonContains('tags', "#{$keyword}");
                            }
                        });
                    }

                    // Filter by learning path if specified
                    if (!empty($knowledgeQueryData['learning_path_id'])) {
                        $query->where('learning_path_id', $knowledgeQueryData['learning_path_id']);
                    }

                    // Filter by category if specified
                    if (!empty($knowledgeQueryData['category_id'])) {
                        $query->where('category_id', $knowledgeQueryData['category_id']);
                    }

                    // Get results with relations
                    $knowledgeResults = $query
                        ->with(['category', 'learningPath'])
                        ->orderBy('last_reviewed_at', 'desc')
                        ->orderBy('view_count', 'desc')
                        ->limit($knowledgeLimit)
                        ->get();

                    Log::info('AIController: Knowledge search completed', [
                        'results_count' => $knowledgeResults->count()
                    ]);

                } catch (\Exception $e) {
                    Log::error('AIController: Knowledge search failed', [
                        'error' => $e->getMessage()
                    ]);
                    $knowledgeResults = collect([]);
                }
            }

            // NEW: If knowledge CREATION intent detected, create categories and items
            if ($knowledgeCreationData && !empty($knowledgeCreationData['has_creation_intent'])) {
                Log::info('AIController: Knowledge creation intent detected, creating items...');

                try {
                    $creationService = app(\App\Services\KnowledgeCreationService::class);
                    $knowledgeCreationResults = $creationService->createKnowledgeFromIntent($knowledgeCreationData, $user);

                    Log::info('AIController: Knowledge creation completed', [
                        'success' => $knowledgeCreationResults['success'],
                        'categories_created' => $knowledgeCreationResults['summary']['categories_created'] ?? 0,
                        'items_created' => $knowledgeCreationResults['summary']['items_created'] ?? 0,
                    ]);

                } catch (\Exception $e) {
                    Log::error('AIController: Knowledge creation failed', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    $knowledgeCreationResults = [
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                }
            }

            if (
                $this->aiService->isLocalProvider()
                && $this->isLightweightMessage($message)
                && !$taskData
                && !$timetableData
                && !$knowledgeQueryData
                && !$knowledgeCreationData
            ) {
                $history = $conversation->messages()
                    ->orderBy('created_at', 'desc')
                    ->limit(4)
                    ->get()
                    ->reverse()
                    ->map(function($msg) {
                        return [
                            'role' => $msg->role,
                            'content' => $msg->content
                        ];
                    })
                    ->toArray();

                $aiResponse = $this->aiService->chat($history, [
                    'timeout' => 180, // Ollama 別サーバー: 応答 ~40–60s のため 120s
                    'max_tokens' => 200,
                    'temperature' => 0.6,
                ]);

                if (!empty($aiResponse['error'])) {
                    DB::rollBack();
                    Log::warning('AI service error during lightweight message', [
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

                $assistantMessage = ChatMessage::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $user->id,
                    'role' => 'assistant',
                    'content' => $aiResponse['message'] ?? '応答を生成できませんでした',
                    'token_count' => $aiResponse['tokens'] ?? null,
                    'metadata' => [
                        'model' => $aiResponse['model'] ?? null,
                        'finish_reason' => $aiResponse['finish_reason'] ?? null,
                    ],
                ]);

                $conversation->updateStats();
                DB::commit();

                return response()->json([
                    'success' => true,
                    'data' => [
                        'user_message' => $userMessage,
                        'assistant_message' => $assistantMessage,
                    ],
                    'message' => 'メッセージを送信しました！'
                ], 201);
            }

            // Load user context: tasks + timetable
            $tasks = Task::where('user_id', $user->id)
                ->where('status', '!=', 'completed')
                ->where('status', '!=', 'cancelled')
                ->with(['subtasks', 'tags'])
                ->orderBy('priority', 'desc')
                ->orderBy('deadline', 'asc')
                ->limit(10) // Reduce context size for faster response
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

            // NEW: Add knowledge results to context if available
            if ($knowledgeResults && $knowledgeResults->count() > 0) {
                $userContext['knowledge_items'] = $knowledgeResults->map(function($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'type' => $item->item_type,
                        'content' => $item->content ? substr($item->content, 0, 200) : null, // Limit content length
                        'code_language' => $item->code_language,
                        'url' => $item->url,
                        'question' => $item->question,
                        'answer' => $item->answer ? substr($item->answer, 0, 200) : null,
                        'tags' => $item->tags,
                        'category' => $item->category ? $item->category->name : null,
                        'learning_path' => $item->learningPath ? $item->learningPath->title : null,
                        'last_reviewed' => $item->last_reviewed_at ? $item->last_reviewed_at->diffForHumans() : null,
                    ];
                })->toArray();

                Log::info('AIController: Added knowledge items to context', [
                    'count' => count($userContext['knowledge_items'])
                ]);
            }

            // Get conversation history (last 10 messages for context)
            $historyLimit = $this->aiService->isLocalProvider() ? 6 : 10;
            $history = $conversation->messages()
                ->orderBy('created_at', 'desc')
                ->limit($historyLimit)
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
            $maxTokens = $this->aiService->isLocalProvider() ? 400 : 800;
            // Local provider用: タイムアウトを大幅に延長（180秒 = 3分）
            $timeout = $this->aiService->isLocalProvider() ? 180 : $this->aiService->getContextChatTimeout(12);
            $aiResponse = $this->aiService->chatWithUserContext($history, $userContext, [
                'timeout' => $timeout,
                'max_tokens' => $maxTokens,
                'temperature' => 0.6,
            ]);

            // Check if AI service returned an error
            if (!empty($aiResponse['error'])) {
                Log::warning('AI service error during context-aware message', [
                    'user_id' => $user->id,
                    'conversation_id' => $conversation->id,
                    'message' => $aiResponse['message'] ?? 'Unknown error',
                    'debug_info' => $aiResponse['debug_info'] ?? null,
                    'has_created_task' => !is_null($createdTask),
                    'has_timetable_suggestion' => !is_null($timetableSuggestion),
                ]);

                // タスクや時間割が作成された場合、AI応答が失敗しても成功メッセージを返す
                if ($createdTask || $timetableSuggestion || $knowledgeCreationResults) {
                    $successMessage = '';

                    if ($createdTask) {
                        $successMessage .= "✅ タスクを作成しました: 「{$createdTask->title}」\n";
                        if ($createdTask->subtasks->count() > 0) {
                            $successMessage .= "📝 サブタスク: {$createdTask->subtasks->count()}個\n";
                        }
                    }

                    if ($timetableSuggestion) {
                        $successMessage .= "📅 時間割の提案を準備しました。確認してください。\n";
                    }

                    if ($knowledgeCreationResults && $knowledgeCreationResults['success']) {
                        $itemsCount = $knowledgeCreationResults['summary']['items_created'] ?? 0;
                        $successMessage .= "📚 Knowledgeアイテムを作成しました: {$itemsCount}個\n";
                    }

                    $successMessage .= "\n（AI応答の生成に失敗しましたが、リクエストは処理されました）";

                    $assistantMessage = ChatMessage::create([
                        'conversation_id' => $conversation->id,
                        'user_id' => $user->id,
                        'role' => 'assistant',
                        'content' => $successMessage,
                        'token_count' => null,
                        'metadata' => [
                            'model' => 'fallback_partial_success',
                            'finish_reason' => 'stop',
                        ],
                    ]);

                    $conversation->updateStats();
                    DB::commit();

                    $responseData = [
                        'user_message' => $userMessage,
                        'assistant_message' => $assistantMessage,
                    ];

                    if ($createdTask) {
                        $responseData['created_task'] = $createdTask;
                    }
                    if ($timetableSuggestion) {
                        $responseData['timetable_suggestion'] = $timetableSuggestion;
                    }
                    if ($knowledgeCreationResults) {
                        $responseData['knowledge_creation'] = $knowledgeCreationResults;
                    }

                    return response()->json([
                        'success' => true,
                        'data' => $responseData,
                        'message' => 'メッセージを送信しました！'
                    ], 201);
                }

                // 何も作成されなかった場合のみ、エラーメッセージを返す
                DB::rollBack();
                $assistantMessage = ChatMessage::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $user->id,
                    'role' => 'assistant',
                    'content' => $this->buildAiUnavailableResponse(),
                    'token_count' => null,
                    'metadata' => [
                        'model' => 'fallback_unavailable',
                        'finish_reason' => 'stop',
                    ],
                ]);

                $conversation->updateStats();
                DB::commit();

                return response()->json([
                    'success' => true,
                    'data' => [
                        'user_message' => $userMessage,
                        'assistant_message' => $assistantMessage,
                    ],
                    'message' => 'メッセージを送信しました！'
                ], 201);
            }

            // If task was created, add confirmation to AI response
            if ($createdTask) {
                $taskConfirmation = "\n\n✅ タスクを作成しました: 「{$createdTask->title}」";
                if ($createdTask->subtasks->count() > 0) {
                    $taskConfirmation .= "\n📝 サブタスク: {$createdTask->subtasks->count()}個";
                }
                $aiResponse['message'] = $aiResponse['message'] . $taskConfirmation;
            }

            // NEW: If knowledge was created, add confirmation to AI response
            if ($knowledgeCreationResults && $knowledgeCreationResults['success']) {
                $categoriesCount = $knowledgeCreationResults['summary']['categories_created'] ?? 0;
                $itemsCount = $knowledgeCreationResults['summary']['items_created'] ?? 0;

                $knowledgeConfirmation = "\n\n✅ Knowledge作成完了:";
                if ($categoriesCount > 0) {
                    $knowledgeConfirmation .= "\n📁 フォルダ: {$categoriesCount}個";
                }
                if ($itemsCount > 0) {
                    $knowledgeConfirmation .= "\n📝 アイテム: {$itemsCount}個";
                }

                // Add details about created items
                if (!empty($knowledgeCreationResults['items'])) {
                    $knowledgeConfirmation .= "\n\n作成されたアイテム:";
                    foreach ($knowledgeCreationResults['items'] as $item) {
                        $typeEmoji = [
                            'note' => '📝',
                            'code_snippet' => '💻',
                            'exercise' => '✏️',
                            'resource_link' => '🔗',
                            'attachment' => '📎'
                        ];
                        $emoji = $typeEmoji[$item->item_type] ?? '📄';
                        $knowledgeConfirmation .= "\n{$emoji} {$item->title}";
                    }
                }

                $aiResponse['message'] = $aiResponse['message'] . $knowledgeConfirmation;

                Log::info('AIController: Added knowledge creation confirmation to response');
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
                'knowledge_creation' => $knowledgeCreationResults, // NEW: Knowledge creation results
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
     * Send message with streaming response (Server-Sent Events)
     * POST /api/ai/chat/conversations/{id}/messages/stream
     */
    public function sendMessageStream(Request $request, string $id)
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

            // Get conversation history
            $historyLimit = $this->aiService->isLocalProvider() ? 6 : 10;
            $history = $conversation->messages()
                ->orderBy('created_at', 'desc')
                ->limit($historyLimit)
                ->get()
                ->reverse()
                ->map(function($msg) {
                    return [
                        'role' => $msg->role,
                        'content' => $msg->content
                    ];
                })
                ->toArray();

            DB::commit();

            // Set headers for Server-Sent Events
            return response()->stream(function() use ($conversation, $user, $userMessage, $history) {
                $fullContent = '';
                $hasError = false;

                try {
                    // Stream AI response
                    foreach ($this->aiService->chatStream($history, [
                        'timeout' => $this->aiService->getContextChatTimeout(12),
                        'max_tokens' => $this->aiService->isLocalProvider() ? 400 : 800,
                        'temperature' => 0.6,
                    ]) as $chunk) {
                        if (!empty($chunk['error'])) {
                            $hasError = true;
                            echo "data: " . json_encode([
                                'type' => 'error',
                                'content' => $chunk['content']
                            ]) . "\n\n";
                            flush();
                            break;
                        }

                        if (!empty($chunk['content'])) {
                            $fullContent .= $chunk['content'];
                            echo "data: " . json_encode([
                                'type' => 'chunk',
                                'content' => $chunk['content']
                            ]) . "\n\n";
                            flush();
                        }

                        if (!empty($chunk['done'])) {
                            if (!empty($chunk['full_message'])) {
                                $fullContent = $chunk['full_message'];
                            }
                            break;
                        }
                    }

                    // Save assistant message to database
                    if (!$hasError && !empty($fullContent)) {
                        try {
                            DB::beginTransaction();
                            $assistantMessage = ChatMessage::create([
                                'conversation_id' => $conversation->id,
                                'user_id' => $user->id,
                                'role' => 'assistant',
                                'content' => $fullContent,
                                'token_count' => null,
                                'metadata' => [
                                    'model' => $this->aiService->isLocalProvider() ? 'ollama' : 'openai',
                                    'finish_reason' => 'stop',
                                ],
                            ]);
                            $conversation->updateStats();
                            DB::commit();

                            echo "data: " . json_encode([
                                'type' => 'done',
                                'message_id' => $assistantMessage->id,
                                'full_content' => $fullContent
                            ]) . "\n\n";
                        } catch (\Exception $e) {
                            DB::rollBack();
                            Log::error('Failed to save streaming message: ' . $e->getMessage());
                            echo "data: " . json_encode([
                                'type' => 'error',
                                'content' => 'メッセージの保存に失敗しました'
                            ]) . "\n\n";
                        }
                    }

                } catch (\Exception $e) {
                    Log::error('Streaming error: ' . $e->getMessage());
                    echo "data: " . json_encode([
                        'type' => 'error',
                        'content' => 'ストリーミング中にエラーが発生しました'
                    ]) . "\n\n";
                }

                flush();
            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no', // Nginx bufferingを無効化
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Streaming chat message failed: ' . $e->getMessage());

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

    private function inferDeadlineFromMessage(string $message, Carbon $reference): ?string
    {
        $normalized = mb_strtolower($message, 'UTF-8');

        if (preg_match('/\b(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})\b/', $normalized, $match)) {
            $year = (int) $match[1];
            $month = (int) $match[2];
            $day = (int) $match[3];
            if (checkdate($month, $day, $year)) {
                return Carbon::create($year, $month, $day)->format('Y-m-d');
            }
        }

        if (preg_match('/(\d{1,2})月(\d{1,2})日/u', $message, $match)) {
            $month = (int) $match[1];
            $day = (int) $match[2];
            $year = $reference->year;
            if (checkdate($month, $day, $year)) {
                return Carbon::create($year, $month, $day)->format('Y-m-d');
            }
        }

        if (preg_match('/(明後日|あさって|day after tomorrow|ngày mốt)/iu', $normalized)) {
            return $reference->copy()->addDays(2)->format('Y-m-d');
        }

        if (preg_match('/(明日|tomorrow|ngày mai)/iu', $normalized)) {
            return $reference->copy()->addDay()->format('Y-m-d');
        }

        if (preg_match('/(今日|today|tonight|hôm nay)/iu', $normalized)) {
            return $reference->format('Y-m-d');
        }

        $isNextWeek = preg_match('/(来週|next week|tuần sau)/iu', $normalized);
        $weekday = $this->inferWeekdayFromMessage($message);
        if ($weekday) {
            $target = $reference->copy()->startOfDay();
            $daysUntil = ($weekday - $target->dayOfWeekIso + 7) % 7;
            $target->addDays($daysUntil);
            if ($isNextWeek) {
                $target->addDays(7);
            }
            return $target->format('Y-m-d');
        }

        return null;
    }

    private function inferWeekdayFromMessage(string $message): ?int
    {
        if (preg_match('/(月曜|月曜日)/u', $message)) {
            return 1;
        }
        if (preg_match('/(火曜|火曜日)/u', $message)) {
            return 2;
        }
        if (preg_match('/(水曜|水曜日)/u', $message)) {
            return 3;
        }
        if (preg_match('/(木曜|木曜日)/u', $message)) {
            return 4;
        }
        if (preg_match('/(金曜|金曜日)/u', $message)) {
            return 5;
        }
        if (preg_match('/(土曜|土曜日)/u', $message)) {
            return 6;
        }
        if (preg_match('/(日曜|日曜日)/u', $message)) {
            return 7;
        }

        if (preg_match('/\b(monday)\b/i', $message)) {
            return 1;
        }
        if (preg_match('/\b(tuesday)\b/i', $message)) {
            return 2;
        }
        if (preg_match('/\b(wednesday)\b/i', $message)) {
            return 3;
        }
        if (preg_match('/\b(thursday)\b/i', $message)) {
            return 4;
        }
        if (preg_match('/\b(friday)\b/i', $message)) {
            return 5;
        }
        if (preg_match('/\b(saturday)\b/i', $message)) {
            return 6;
        }
        if (preg_match('/\b(sunday)\b/i', $message)) {
            return 7;
        }

        if (preg_match('/(chủ nhật|chu nhat)/iu', $message)) {
            return 7;
        }
        if (preg_match('/(thứ|thu)\s*([2-7])/iu', $message, $match)) {
            $day = (int) $match[2];
            return match ($day) {
                2 => 1,
                3 => 2,
                4 => 3,
                5 => 4,
                6 => 5,
                7 => 6,
                default => null,
            };
        }

        return null;
    }

    private function isSimpleGreeting(string $message): bool
    {
        $normalized = trim(mb_strtolower($message));

        if ($normalized === '' || mb_strlen($normalized) > 20) {
            return false;
        }

        return (bool)preg_match('/^(hi|hello|hey|xin chào|xin chao|chào|chao|こんにちは|こんばんは|おはよう|やあ|もしもし)[!！。.\s]*$/u', $normalized);
    }

    private function buildGreetingResponse(): string
    {
        return 'こんにちは！今日は何をお手伝いしましょうか？';
    }

    private function getInstantReplyResponse(string $message): ?string
    {
        $normalized = trim(mb_strtolower($message));

        if ($normalized === '') {
            return null;
        }

        // 重要な意図（タスク作成、時間割作成など）を含むメッセージは除外
        if ($this->hasImportantIntent($message)) {
            return null;
        }

        if ($this->isSimpleGreeting($message)) {
            return $this->buildGreetingResponse();
        }

        if (preg_match('/^(help|ヘルプ|使い方|どう使う|使い方を教えて|hướng dẫn|huong dan)$/u', $normalized)) {
            return "使い方: チャットで質問するか、\n- 「タスクを作成して」\n- 「時間割を追加」\n- 「ノートを探して」\nのように送ってください。";
        }

        // 時刻を尋ねる質問のみ（タスクの時間指定ではない）
        // 「今何時」「いま何時」など、明確に現在時刻を尋ねる場合のみ
        if (preg_match('/^(今何時|いま何時|現在何時|mấy giờ rồi|may gio roi|what time is it now)$/u', $normalized)) {
            return '現在時刻は ' . now()->format('H:i') . ' です。';
        }

        // 日付を尋ねる質問のみ
        if (preg_match('/(今日は何日|今日の日付|何日|hôm nay|hom nay|date)/u', $normalized)) {
            return '今日は ' . now()->format('Y-m-d') . ' です。';
        }

        if (preg_match('/(あなたは誰|あなたはだれ|who are you|ban la ai|bạn là ai)/u', $normalized)) {
            return '私はあなたの学習とタスク管理を手伝うアシスタントです。';
        }

        if (preg_match('/(できること|何ができる|chức năng|tính năng|what can you do)/u', $normalized)) {
            return 'できること: タスク作成、時間割登録、Knowledge検索、学習アドバイス。';
        }

        return null;
    }

    /**
     * Check if message contains important intent (task creation, timetable, etc.)
     * These should not be handled by instant reply
     */
    private function hasImportantIntent(string $message): bool
    {
        $normalized = trim(mb_strtolower($message));

        // タスク作成の意図（より包括的なパターン）
        $taskKeywords = '/(タスク|task|やる|やりたい|したい|作成|追加|登録|つくって|作って|勉強|study|work|learn|作業|宿題|課題|準備|予習|復習|練習)/iu';
        if (preg_match($taskKeywords, $normalized)) {
            return true;
        }

        // 時間割・スケジュールの意図
        if (preg_match('/(授業|クラス|class|lecture|時間割|schedule|スケジュール|lịch học|thứ|monday|tuesday|wednesday|thursday|friday|saturday|sunday|月曜|火曜|水曜|木曜|金曜|土曜|日曜)/iu', $normalized)) {
            return true;
        }

        // Knowledge関連の意図
        if (preg_match('/(メモ|ノート|記録|コード|演習|問題|資料|リンク|review|復習|search|探して|見せて|knowledge|保存|フォルダ|カテゴリ)/iu', $normalized)) {
            return true;
        }

        // 時間指定 + 行動動詞の組み合わせ（タスクの時間指定の可能性が高い）
        // 「10時に勉強する」「1時間で作る」などのパターン
        if (preg_match('/(\d+時|\d+時間|\d+分|時|時間|分|hour|minute|h|m).*(する|やる|やりたい|したい|作成|追加|勉強|study|work|learn|作業|作る|つくる|準備)/iu', $normalized) ||
            preg_match('/(する|やる|やりたい|したい|作成|追加|勉強|study|work|learn|作業|作る|つくる|準備).*(\d+時|\d+時間|\d+分|時|時間|分|hour|minute|h|m)/iu', $normalized)) {
            return true;
        }

        // 日付・曜日指定 + 行動動詞の組み合わせ（タスクの期限指定の可能性が高い）
        // 「来週の月曜日に勉強する」「明日やる」などのパターン
        if (preg_match('/(来週|今週|来月|今月|明日|今日|明後日|月曜|火曜|水曜|木曜|金曜|土曜|日曜|next week|tomorrow|today|monday|tuesday|wednesday|thursday|friday|saturday|sunday).*(する|やる|やりたい|したい|作成|追加|勉強|study|work|learn|作業|作る|つくる|準備|開始|終了)/iu', $normalized) ||
            preg_match('/(する|やる|やりたい|したい|作成|追加|勉強|study|work|learn|作業|作る|つくる|準備|開始|終了).*(来週|今週|来月|今月|明日|今日|明後日|月曜|火曜|水曜|木曜|金曜|土曜|日曜|next week|tomorrow|today|monday|tuesday|wednesday|thursday|friday|saturday|sunday)/iu', $normalized)) {
            return true;
        }

        // 具体的な時間指定（「10時に」「14:30に」など）+ 何らかの行動
        if (preg_match('/(\d{1,2}時|\d{1,2}:\d{2}).*(する|やる|やりたい|したい|作成|追加|勉強|study|work|learn|作業|作る|つくる|準備)/iu', $normalized)) {
            return true;
        }

        return false;
    }

    private function buildAiUnavailableResponse(): string
    {
        return '申し訳ありません。AIが混雑中のため簡易返信になります。少し後で再度お試しください。';
    }

    private function isLightweightMessage(string $message): bool
    {
        $normalized = trim(mb_strtolower($message));

        if ($normalized === '' || mb_strlen($normalized) > 40) {
            return false;
        }

        return !preg_match('/(タスク|task|授業|クラス|class|lecture|時間割|schedule|lịch học|thứ|monday|tuesday|wednesday|thursday|friday|saturday|sunday|メモ|ノート|記録|コード|演習|問題|資料|リンク|review|復習|search|探して|見せて|追加|作成|保存|フォルダ|カテゴリ|knowledge|note|snippet|exercise|resource)/iu', $normalized);
    }

    private function isScheduleQuestion(string $message): bool
    {
        return (bool)preg_match('/(スケジュール|時間割|予定|授業|クラス|schedule|class|lecture|lịch học|thứ)/iu', $message);
    }

    private function buildTimetableContext(User $user, string $message): array
    {
        $today = now();
        $dayNames = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $todayDayName = $dayNames[$today->dayOfWeek];

        $onlyToday = (bool)preg_match('/(今日|きょう|today|hôm nay|hom nay)/iu', $message);

        $timetableQuery = \App\Models\TimetableClass::where('user_id', $user->id)
            ->orderBy('day', 'asc')
            ->orderBy('start_time', 'asc');

        if ($onlyToday) {
            $timetableQuery->where('day', $todayDayName);
        }

        $allTimetable = $timetableQuery->get();

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

        return [
            'tasks' => [],
            'timetable' => $timetableByDay,
            'today' => $todayDayName,
        ];
    }

    /**
     * Check if message has task creation keywords
     */
    private function hasTaskCreationKeywords(string $message): bool
    {
        $keywords = [
            'タスク', 'task', 'つくって', '作って', '作成', '追加', '登録',
            '勉強', 'study', '学習', '作業', 'やる', 'やりたい', 'したい'
        ];

        $normalized = mb_strtolower($message);
        foreach ($keywords as $keyword) {
            if (mb_strpos($normalized, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract task information from message using simple pattern matching
     * Fallback when parseTaskIntent times out
     */
    private function extractTaskFromMessage(string $message): ?array
    {
        // Extract time information
        $scheduledTime = null;
        if (preg_match('/(\d+)時/', $message, $matches)) {
            $hour = (int)$matches[1];
            $scheduledTime = sprintf('%02d:00:00', $hour);
        }

        // Extract duration
        $estimatedMinutes = null;
        if (preg_match('/(\d+)時間/', $message, $matches)) {
            $estimatedMinutes = (int)$matches[1] * 60;
        } elseif (preg_match('/(\d+)分/', $message, $matches)) {
            $estimatedMinutes = (int)$matches[1];
        }

        // Extract task title (remove time/duration keywords)
        $title = $message;
        $title = preg_replace('/来週の/', '', $title);
        $title = preg_replace('/月曜日|火曜日|水曜日|木曜日|金曜日|土曜日|日曜日/', '', $title);
        $title = preg_replace('/\d+時/', '', $title);
        $title = preg_replace('/\d+時間/', '', $title);
        $title = preg_replace('/\d+分/', '', $title);
        $title = preg_replace('/タスクを.*?つくって/', '', $title);
        $title = preg_replace('/タスクを.*?作って/', '', $title);
        $title = preg_replace('/タスクを.*?作成/', '', $title);
        $title = preg_replace('/ください/', '', $title);
        $title = trim($title);
        // 日付・時刻パターン除去後の残り「の」(例: 水曜日の)、「に」(例: 10時に) を先頭から削除
        $title = preg_replace('/^[のに\s]+/u', '', $title);
        $title = trim($title);

        // If title is too short or empty, use original message
        if (mb_strlen($title) < 3) {
            $title = $message;
        }

        // Extract deadline using the same logic as inferDeadlineFromMessage for consistency
        // This ensures "来週の月曜日" is correctly calculated as next week's Monday
        $deadline = $this->inferDeadlineFromMessage($message, now());

        if (!$deadline) {
            // Fallback: simple pattern matching for common phrases
            if (preg_match('/(明日|あした|あす)/', $message)) {
                $deadline = now()->addDay()->format('Y-m-d');
            } elseif (preg_match('/(明後日|あさって)/', $message)) {
                $deadline = now()->addDays(2)->format('Y-m-d');
            } elseif (preg_match('/(今日|きょう)/', $message)) {
                $deadline = now()->format('Y-m-d');
            }
        }

        if ($deadline) {
            Log::info('Deadline extracted from message', [
                'message' => $message,
                'deadline' => $deadline
            ]);
        }

        return [
            'title' => $title,
            'description' => null,
            'estimated_minutes' => $estimatedMinutes,
            'priority' => 'medium',
            'deadline' => $deadline,
            'scheduled_time' => $scheduledTime,
            'tags' => [],
            'subtasks' => []
        ];
    }
}
