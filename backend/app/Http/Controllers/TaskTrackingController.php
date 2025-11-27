<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskAbandonment;
use App\Services\TaskAbandonmentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class TaskTrackingController extends Controller
{
    protected TaskAbandonmentService $abandonmentService;

    public function __construct(TaskAbandonmentService $abandonmentService)
    {
        $this->abandonmentService = $abandonmentService;
    }

    /**
     * Send heartbeat to update task's last_active_at
     * POST /api/tasks/{id}/heartbeat
     */
    public function heartbeat(Request $request, int $id): JsonResponse
    {
        try {
            $userId = $request->user()->id;

            $success = $this->abandonmentService->updateTaskHeartbeat($id, $userId);

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task not found or unauthorized'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Heartbeat updated',
                'data' => [
                    'last_active_at' => now()->toIso8601String()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update heartbeat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Manually mark task as abandoned
     * POST /api/tasks/{id}/abandon
     */
    public function abandonTask(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'nullable|in:app_switched,long_inactivity,manual,deadline_passed',
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = $request->user()->id;
            $task = Task::where('id', $id)
                ->where('user_id', $userId)
                ->firstOrFail();

            $abandonment = $this->abandonmentService->markTaskAsAbandoned(
                $task,
                $request->input('type', 'manual'),
                $request->input('reason'),
                false // manual abandonment
            );

            if (!$abandonment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to mark task as abandoned'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'タスクを中断しました',
                'data' => $abandonment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to abandon task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resume abandoned task
     * POST /api/tasks/{id}/resume
     */
    public function resumeTask(Request $request, int $id): JsonResponse
    {
        try {
            $userId = $request->user()->id;

            $success = $this->abandonmentService->resumeAbandonedTask($id, $userId);

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task not found or unauthorized'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'タスクを再開しました'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resume task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get abandonment history for a task
     * GET /api/tasks/{id}/abandonments
     */
    public function getTaskAbandonments(Request $request, int $id): JsonResponse
    {
        try {
            $userId = $request->user()->id;

            $task = Task::where('id', $id)
                ->where('user_id', $userId)
                ->firstOrFail();

            $abandonments = $task->abandonments()
                ->orderBy('abandoned_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'abandonments' => $abandonments,
                    'total_count' => $abandonments->count(),
                    'task_abandonment_count' => $task->abandonment_count,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get abandonment history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's abandonment statistics
     * GET /api/abandonments/stats
     */
    public function getAbandonmentStats(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $days = $request->query('days', 7);

            $stats = $this->abandonmentService->getAbandonmentStats($userId, $days);

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get abandonment stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all abandonments for user
     * GET /api/abandonments
     */
    public function getUserAbandonments(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $perPage = $request->query('per_page', 20);
            $type = $request->query('type');
            $resumed = $request->query('resumed');

            $query = TaskAbandonment::where('user_id', $userId)
                ->with(['task:id,title,category'])
                ->orderBy('abandoned_at', 'desc');

            // Filter by type
            if ($type) {
                $query->where('abandonment_type', $type);
            }

            // Filter by resumed status
            if ($resumed === 'true' || $resumed === '1') {
                $query->where('resumed', true);
            } elseif ($resumed === 'false' || $resumed === '0') {
                $query->where('resumed', false);
            }

            $abandonments = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $abandonments->items(),
                'pagination' => [
                    'total' => $abandonments->total(),
                    'per_page' => $abandonments->perPage(),
                    'current_page' => $abandonments->currentPage(),
                    'last_page' => $abandonments->lastPage(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get abandonments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get abandoned tasks (tasks currently marked as abandoned)
     * GET /api/tasks/abandoned
     */
    public function getAbandonedTasks(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;

            $tasks = Task::where('user_id', $userId)
                ->where('is_abandoned', true)
                ->with(['abandonments' => function($query) {
                    $query->latest()->limit(1);
                }])
                ->orderBy('updated_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $tasks
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get abandoned tasks',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
