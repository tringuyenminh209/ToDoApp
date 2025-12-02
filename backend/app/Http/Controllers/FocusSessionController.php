<?php

namespace App\Http\Controllers;

use App\Models\FocusSession;
use App\Models\Task;
use App\Models\KnowledgeCategory;
use App\Models\KnowledgeItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FocusSessionController extends Controller
{
    /**
     * Bắt đầu focus session
     * POST /api/sessions/start
     */
    public function start(Request $request): JsonResponse
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'duration_minutes' => 'required|integer|min:1|max:120',  // 最低1分に変更（短いタスク対応）
            'session_type' => 'required|in:work,break,long_break',
        ]);

        // Kiểm tra có session đang chạy không
        $activeSession = FocusSession::where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->first();

        if ($activeSession) {
            return response()->json([
                'success' => false,
                'message' => '既にアクティブなセッションがあります'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $session = FocusSession::create([
                'user_id' => $request->user()->id,
                'task_id' => $request->task_id,
                'session_type' => $request->session_type,
                'duration_minutes' => $request->duration_minutes,
                'started_at' => now(),
                'status' => 'active',
            ]);

            DB::commit();

            $session->load('task');

            return response()->json([
                'success' => true,
                'data' => $session,
                'message' => 'フォーカスセッションを開始しました！'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'セッションの開始に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kết thúc focus session
     * PUT /api/sessions/{id}/stop
     */
    public function stop(Request $request, string $id): JsonResponse
    {
        // デバッグ：受信したリクエストデータをログに記録
        Log::info('Session stop request received', [
            'session_id' => $id,
            'request_all' => $request->all(),
            'request_input_notes' => $request->input('notes'),
            'request_has_notes' => $request->has('notes'),
            'request_json' => $request->json()->all(),
        ]);

        $request->validate([
            'notes' => 'nullable|string|max:1000',
            'force_complete_task' => 'nullable|boolean',
        ]);

        $session = FocusSession::where('user_id', $request->user()->id)
            ->findOrFail($id);

        if ($session->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'セッションは既に終了しています'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // セッションの実際の時間を計算
            $calculatedMinutes = $session->started_at->diffInMinutes(now());

            // セッション時間が0または負の値の場合、duration_minutesを使用
            // または、セッションが開始されてすぐに停止された場合（1分未満）
            if ($calculatedMinutes <= 0 || ($calculatedMinutes < 1 && $session->duration_minutes > 0)) {
                $actualMinutes = $session->duration_minutes > 0 ? $session->duration_minutes : 1;
                Log::info('Session actualMinutes was 0 or very small, using duration_minutes', [
                    'session_id' => $session->id,
                    'calculated_minutes' => $calculatedMinutes,
                    'duration_minutes' => $session->duration_minutes,
                    'actual_minutes' => $actualMinutes,
                ]);
            } else {
                $actualMinutes = $calculatedMinutes;
            }

            // 最低1分を保証
            $actualMinutes = max(1, $actualMinutes);

            Log::info('Session stop - time calculation', [
                'session_id' => $session->id,
                'started_at' => $session->started_at->format('Y-m-d H:i:s'),
                'ended_at' => now()->format('Y-m-d H:i:s'),
                'calculated_minutes' => $calculatedMinutes,
                'duration_minutes' => $session->duration_minutes,
                'actual_minutes' => $actualMinutes,
            ]);

            $updateData = [
                'status' => 'completed',
                'ended_at' => now(),
                'actual_minutes' => $actualMinutes,
            ];

            // メモが提供されている場合は保存
            $notes = $request->input('notes');
            Log::info('Session stop - notes received', [
                'session_id' => $session->id,
                'notes' => $notes,
                'notes_is_null' => $notes === null,
                'notes_trimmed' => $notes !== null ? trim($notes) : null,
                'notes_length' => $notes !== null ? strlen($notes) : 0,
            ]);

            if ($notes !== null && trim($notes) !== '') {
                $updateData['notes'] = $notes;
            }

            $session->update($updateData);

            // タスク完了チェック（workセッションの場合のみ）
            $task = $session->task;
            $taskCompleted = false;

            if ($session->session_type === 'work' && $task && $task->status !== 'completed') {
                // タスクをリロードして最新のサブタスク情報を取得
                $task->load('subtasks');

                // タスクのtotal_focus_minutesを更新（actualMinutesが0より大きい場合のみ）
                if ($actualMinutes > 0) {
                    $task->increment('total_focus_minutes', $actualMinutes);
                    $task->update(['last_focus_at' => now()]);
                    $task->refresh(); // total_focus_minutesを更新後にリロード
                } else {
                    // actualMinutesが0の場合、duration_minutesを使用
                    $minutesToAdd = $session->duration_minutes > 0 ? $session->duration_minutes : 1;
                    $task->increment('total_focus_minutes', $minutesToAdd);
                    $task->update(['last_focus_at' => now()]);
                    $task->refresh();
                    $actualMinutes = $minutesToAdd; // 以降のチェックで使用

                    Log::warning('Session actualMinutes was 0, using duration_minutes', [
                        'session_id' => $session->id,
                        'duration_minutes' => $session->duration_minutes,
                        'minutes_to_add' => $minutesToAdd,
                    ]);
                }

                // 条件1: 全てのサブタスクが完了している場合
                $hasSubtasks = $task->subtasks->isNotEmpty();
                $allSubtasksCompleted = $hasSubtasks &&
                    $task->subtasks->every(function($subtask) {
                        return $subtask->is_completed;
                    });

                // 条件2: サブタスクがなく、残り時間が0になった場合
                $remainingMinutes = $task->getRemainingMinutes();
                $noRemainingTime = !$hasSubtasks &&
                    $task->estimated_minutes !== null &&
                    $remainingMinutes !== null &&
                    $remainingMinutes <= 0;

                // 条件3: タスクのtotal_focus_minutesが予想時間に達した場合
                $totalFocusMinutes = $task->total_focus_minutes;
                $reachedEstimatedTime = $task->estimated_minutes !== null &&
                    $totalFocusMinutes >= $task->estimated_minutes;

                // 条件4: セッション時間が予想時間以上の場合（短いタスク用）
                // または、セッション時間が予想時間の80%以上の場合（丸め誤差を考慮）
                // または、duration_minutesが予想時間以上の場合（タイマーが設定時間まで実行された場合）
                // または、actualMinutesが0でもduration_minutesが予想時間以上の場合
                $sessionReachedEstimated = $task->estimated_minutes !== null &&
                    ($actualMinutes >= $task->estimated_minutes ||
                     ($actualMinutes >= ($task->estimated_minutes * 0.8) && $actualMinutes > 0) ||
                     ($session->duration_minutes >= $task->estimated_minutes));

                // デバッグ用ログ：タスク完了チェックの詳細
                Log::info('Task completion check', [
                    'task_id' => $task->id,
                    'estimated_minutes' => $task->estimated_minutes,
                    'total_focus_minutes' => $totalFocusMinutes,
                    'actual_minutes' => $actualMinutes,
                    'remaining_minutes' => $remainingMinutes,
                    'has_subtasks' => $hasSubtasks,
                    'all_subtasks_completed' => $allSubtasksCompleted,
                    'no_remaining_time' => $noRemainingTime,
                    'reached_estimated_time' => $reachedEstimatedTime,
                    'session_reached_estimated' => $sessionReachedEstimated,
                ]);

                // force_complete_taskパラメータをチェック
                $forceComplete = $request->input('force_complete_task', false);

                // タスクを完了にする
                if ($forceComplete || $allSubtasksCompleted || $noRemainingTime || $reachedEstimatedTime || $sessionReachedEstimated) {
                    // タスクを完了状態にマーク
                    $task->markAsCompleted();
                    $taskCompleted = true;

                    $reason = $forceComplete ? 'user_force_completed' :
                             ($allSubtasksCompleted ? 'all_subtasks_completed' :
                             ($noRemainingTime ? 'no_remaining_time' :
                             ($reachedEstimatedTime ? 'reached_estimated_time' : 'session_reached_estimated')));

                    Log::info('Task marked as completed', [
                        'task_id' => $task->id,
                        'reason' => $reason,
                        'force_complete' => $forceComplete,
                        'total_focus_minutes' => $totalFocusMinutes,
                        'estimated_minutes' => $task->estimated_minutes,
                        'actual_minutes' => $actualMinutes,
                    ]);
                } else {
                    Log::info('Task not completed', [
                        'task_id' => $task->id,
                        'has_subtasks' => $hasSubtasks,
                        'all_subtasks_completed' => $allSubtasksCompleted,
                        'no_remaining_time' => $noRemainingTime,
                        'reached_estimated_time' => $reachedEstimatedTime,
                        'session_reached_estimated' => $sessionReachedEstimated,
                        'total_focus_minutes' => $totalFocusMinutes,
                        'estimated_minutes' => $task->estimated_minutes,
                        'actual_minutes' => $actualMinutes,
                        'remaining_minutes' => $remainingMinutes,
                    ]);
                }
            }

            DB::commit();

            // トランザクション成功後、メモをKnowledge Itemとして「Note」フォルダに保存
            // セッション/タスク更新と独立して実行（失敗してもセッションは完了）
            if ($notes !== null && trim($notes) !== '') {
                Log::info('Saving session notes as knowledge item (after commit)', [
                    'session_id' => $session->id,
                    'notes_length' => strlen($notes),
                ]);
                $this->saveSessionNotesAsKnowledgeItem($session, $notes, $actualMinutes, $request->user());
            } else {
                Log::info('No notes to save', [
                    'session_id' => $session->id,
                    'reason' => $notes === null ? 'notes_is_null' : 'notes_is_empty'
                ]);
            }

            // タスクを再読み込みして最新のステータスを取得
            $task->refresh();
            $session->load('task');

            $message = 'フォーカスセッションを完了しました！';
            if ($taskCompleted) {
                $message .= ' タスクも完了しました！';
            }

            // デバッグ用ログ：タスクの最終ステータスを確認
            Log::info('Focus session stopped - Task status', [
                'task_id' => $task->id,
                'task_title' => $task->title,
                'task_status' => $task->status,
                'task_completed' => $taskCompleted,
                'total_focus_minutes' => $task->total_focus_minutes,
                'estimated_minutes' => $task->estimated_minutes,
            ]);

            return response()->json([
                'success' => true,
                'data' => $session,
                'message' => $message,
                'task_completed' => $taskCompleted
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'セッションの終了に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * セッションメモをKnowledge Itemとして「Note」フォルダに保存
     */
    private function saveSessionNotesAsKnowledgeItem(FocusSession $session, string $notes, int $actualMinutes, $user): void
    {
        try {
            Log::info('saveSessionNotesAsKnowledgeItem START', [
                'session_id' => $session->id,
                'user_id' => $user->id,
                'notes_length' => strlen($notes),
                'actual_minutes' => $actualMinutes,
            ]);

            // 「Note」カテゴリを取得または作成
            $noteCategory = KnowledgeCategory::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'name' => 'Note',
                    'parent_id' => null,
                ],
                [
                    'description' => 'メモを保存',
                    'icon' => 'note',
                    'color' => '#0FA968',
                    'sort_order' => 0,
                    'item_count' => 0,
                ]
            );

            Log::info('Note category created/found', [
                'category_id' => $noteCategory->id,
                'category_name' => $noteCategory->name,
            ]);

            // タスク情報を取得
            $task = $session->task;
            // タイトルはタスク名のみ（ユーザー要求）
            $title = $task ? $task->title : 'フォーカスセッション';

            // メモの内容を整形
            $content = $notes;
            if ($task) {
                $content = "タスク: {$task->title}\n";
                $content .= "セッション時間: {$actualMinutes}分\n";
                $content .= "開始時刻: " . $session->started_at->format('Y/m/d H:i') . "\n";
                $content .= "終了時刻: " . ($session->ended_at ? $session->ended_at->format('Y/m/d H:i') : now()->format('Y/m/d H:i')) . "\n\n";
                $content .= "メモ:\n{$notes}";
            }

            // Knowledge Itemを作成
            Log::info('Creating knowledge item', [
                'user_id' => $user->id,
                'category_id' => $noteCategory->id,
                'title' => $title,
                'item_type' => 'note',
                'content_length' => strlen($content),
                'source_task_id' => $task?->id,
            ]);

            $knowledgeItem = KnowledgeItem::create([
                'user_id' => $user->id,
                'category_id' => $noteCategory->id,
                'title' => $title,
                'item_type' => 'note',
                'content' => $content,
                'source_task_id' => $task?->id,
                'tags' => ['focus-session', 'note'],
            ]);

            Log::info('Knowledge item created successfully', [
                'knowledge_item_id' => $knowledgeItem->id,
            ]);

            // カテゴリのitem_countを更新
            $noteCategory->increment('item_count');

            Log::info('Session notes saved as knowledge item COMPLETE', [
                'session_id' => $session->id,
                'knowledge_item_id' => $knowledgeItem->id,
                'category_id' => $noteCategory->id,
            ]);

        } catch (\Exception $e) {
            // メモの保存に失敗してもセッション終了は続行
            Log::error('Failed to save session notes as knowledge item', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Lấy danh sách sessions
     * GET /api/sessions
     */
    public function index(Request $request): JsonResponse
    {
        $query = FocusSession::with('task')
            ->where('user_id', $request->user()->id);

        // Filtering
        if ($request->has('session_type')) {
            $query->where('session_type', $request->session_type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date')) {
            $date = Carbon::parse($request->date)->startOfDay();
            $query->whereBetween('started_at', [$date, $date->copy()->endOfDay()]);
        }

        if ($request->has('task_id')) {
            $query->where('task_id', $request->task_id);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'started_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSortFields = ['started_at', 'ended_at', 'duration_minutes', 'actual_minutes'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $sessions = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $sessions,
            'message' => 'フォーカスセッション一覧を取得しました'
        ]);
    }

    /**
     * Lấy session hiện tại
     * GET /api/sessions/current
     */
    public function current(Request $request): JsonResponse
    {
        $session = FocusSession::where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->with('task')
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'アクティブなセッションがありません'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $session,
            'message' => 'アクティブなセッションを取得しました'
        ]);
    }

    /**
     * Pause session
     * PUT /api/sessions/{id}/pause
     */
    public function pause(Request $request, string $id): JsonResponse
    {
        $session = FocusSession::where('user_id', $request->user()->id)
            ->findOrFail($id);

        if ($session->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'アクティブなセッションのみ一時停止できます'
            ], 400);
        }

        try {
            $session->update(['status' => 'paused']);

            $session->load('task');

            return response()->json([
                'success' => true,
                'data' => $session,
                'message' => 'セッションを一時停止しました！'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'セッションの一時停止に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resume session
     * PUT /api/sessions/{id}/resume
     */
    public function resume(Request $request, string $id): JsonResponse
    {
        $session = FocusSession::where('user_id', $request->user()->id)
            ->findOrFail($id);

        if ($session->status !== 'paused') {
            return response()->json([
                'success' => false,
                'message' => '一時停止されたセッションのみ再開できます'
            ], 400);
        }

        try {
            $session->update(['status' => 'active']);

            $session->load('task');

            return response()->json([
                'success' => true,
                'data' => $session,
                'message' => 'セッションを再開しました！'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'セッションの再開に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get session statistics
     * GET /api/sessions/stats
     */
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        // Today's sessions
        $todaySessions = FocusSession::where('user_id', $user->id)
            ->whereDate('started_at', today())
            ->where('status', 'completed')
            ->get();

        // This week's sessions
        $weekSessions = FocusSession::where('user_id', $user->id)
            ->whereBetween('started_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->where('status', 'completed')
            ->get();

        // This month's sessions
        $monthSessions = FocusSession::where('user_id', $user->id)
            ->whereBetween('started_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->where('status', 'completed')
            ->get();

        $stats = [
            'today' => [
                'sessions_count' => $todaySessions->count(),
                'total_minutes' => $todaySessions->sum('actual_minutes'),
                'work_sessions' => $todaySessions->where('session_type', 'work')->count(),
                'break_sessions' => $todaySessions->whereIn('session_type', ['break', 'long_break'])->count(),
            ],
            'this_week' => [
                'sessions_count' => $weekSessions->count(),
                'total_minutes' => $weekSessions->sum('actual_minutes'),
                'work_sessions' => $weekSessions->where('session_type', 'work')->count(),
                'break_sessions' => $weekSessions->whereIn('session_type', ['break', 'long_break'])->count(),
            ],
            'this_month' => [
                'sessions_count' => $monthSessions->count(),
                'total_minutes' => $monthSessions->sum('actual_minutes'),
                'work_sessions' => $monthSessions->where('session_type', 'work')->count(),
                'break_sessions' => $monthSessions->whereIn('session_type', ['break', 'long_break'])->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'セッション統計を取得しました'
        ]);
    }

    /**
     * Get sessions by date range
     * GET /api/sessions/by-date
     */
    public function byDate(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $sessions = FocusSession::with('task')
            ->where('user_id', $request->user()->id)
            ->whereBetween('started_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->orderBy('started_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sessions,
            'message' => '指定期間のセッションを取得しました'
        ]);
    }

    /**
     * Update session notes
     * PUT /api/sessions/{id}/notes
     */
    public function updateNotes(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $session = FocusSession::where('user_id', $request->user()->id)
            ->findOrFail($id);

        try {
            $session->update([
                'notes' => $request->notes,
            ]);

            $session->load('task');

            return response()->json([
                'success' => true,
                'data' => $session,
                'message' => 'セッションメモを更新しました！'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'メモの更新に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
