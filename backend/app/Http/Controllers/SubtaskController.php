<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubtaskController extends Controller
{
    /**
     * Get all subtasks for a task
     * GET /api/tasks/{taskId}/subtasks
     */
    public function index(Request $request, string $taskId): JsonResponse
    {
        $task = Task::where('id', $taskId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $subtasks = $task->subtasks()->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $subtasks
        ]);
    }

    /**
     * Create a new subtask
     * POST /api/tasks/{taskId}/subtasks
     */
    public function store(Request $request, string $taskId): JsonResponse
    {
        $task = Task::where('id', $taskId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'estimated_minutes' => 'nullable|integer|min:1|max:600',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        try {
            // Get the next sort order if not provided
            $sortOrder = $request->sort_order ?? $task->subtasks()->max('sort_order') + 1;

            $subtask = Subtask::create([
                'task_id' => $task->id,
                'title' => $request->title,
                'estimated_minutes' => $request->estimated_minutes,
                'sort_order' => $sortOrder,
                'is_completed' => false,
            ]);

            return response()->json([
                'success' => true,
                'data' => $subtask,
                'message' => 'サブタスクを作成しました！'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'サブタスクの作成に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a subtask
     * PUT /api/subtasks/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $subtask = Subtask::findOrFail($id);

        // Check if user owns the parent task
        $task = Task::where('id', $subtask->task_id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'is_completed' => 'sometimes|required|boolean',
            'estimated_minutes' => 'nullable|integer|min:1|max:600',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        try {
            $subtask->update($request->only([
                'title',
                'is_completed',
                'estimated_minutes',
                'sort_order',
            ]));

            return response()->json([
                'success' => true,
                'data' => $subtask->fresh(),
                'message' => 'サブタスクを更新しました！'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'サブタスクの更新に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle subtask completion
     * PUT /api/subtasks/{id}/toggle
     */
    public function toggle(Request $request, string $id): JsonResponse
    {
        $subtask = Subtask::findOrFail($id);

        // Check if user owns the parent task
        $task = Task::where('id', $subtask->task_id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        try {
            $subtask->toggleCompletion();

            return response()->json([
                'success' => true,
                'data' => $subtask->fresh(),
                'message' => $subtask->is_completed ?
                    'サブタスクを完了しました！' :
                    'サブタスクを未完了にしました'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'サブタスクの更新に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a subtask
     * DELETE /api/subtasks/{id}
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $subtask = Subtask::findOrFail($id);

        // Check if user owns the parent task
        $task = Task::where('id', $subtask->task_id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        try {
            $subtask->delete();

            return response()->json([
                'success' => true,
                'message' => 'サブタスクを削除しました'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'サブタスクの削除に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder subtasks
     * POST /api/tasks/{taskId}/subtasks/reorder
     */
    public function reorder(Request $request, string $taskId): JsonResponse
    {
        $task = Task::where('id', $taskId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $request->validate([
            'subtask_ids' => 'required|array',
            'subtask_ids.*' => 'exists:subtasks,id',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->subtask_ids as $index => $subtaskId) {
                Subtask::where('id', $subtaskId)
                    ->where('task_id', $task->id)
                    ->update(['sort_order' => $index]);
            }

            DB::commit();

            $subtasks = $task->subtasks()->ordered()->get();

            return response()->json([
                'success' => true,
                'data' => $subtasks,
                'message' => 'サブタスクの順序を更新しました'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'サブタスクの順序更新に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
