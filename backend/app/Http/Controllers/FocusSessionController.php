<?php

namespace App\Http\Controllers;

use App\Models\FocusSession;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
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
            'duration_minutes' => 'required|integer|min:5|max:120',
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

            $actualMinutes = $session->started_at->diffInMinutes(now());

            $session->update([
                'status' => 'completed',
                'ended_at' => now(),
                'actual_minutes' => $actualMinutes,
            ]);

            DB::commit();

            $session->load('task');

            return response()->json([
                'success' => true,
                'data' => $session,
                'message' => 'フォーカスセッションを完了しました！'
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
}
