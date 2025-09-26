<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Subtask;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * タスク一覧を取得
     */
    public function index(Request $request): JsonResponse
    {
        $query = Task::where('user_id', auth()->id())
            ->with(['subtasks', 'project']);

        // フィルタリング
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('energy_level')) {
            $query->where('energy_level', $request->energy_level);
        }

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // 検索
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // ソート
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $tasks = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);
    }

    /**
     * 新しいタスクを作成
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'project_id' => 'nullable|exists:projects,id',
            'due_at' => 'nullable|date|after:now',
            'estimated_minutes' => 'nullable|integer|min:0|max:600',
            'priority' => 'required|integer|min:1|max:5',
            'energy_level' => 'required|in:low,medium,high',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => '入力データに誤りがあります',
                    'fields' => $validator->errors()
                ]
            ], 422);
        }

        $task = Task::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'project_id' => $request->project_id,
            'due_at' => $request->due_at,
            'estimated_minutes' => $request->estimated_minutes,
            'priority' => $request->priority,
            'energy_level' => $request->energy_level,
        ]);

        $task->load(['subtasks', 'project']);

        return response()->json([
            'success' => true,
            'message' => 'タスクが作成されました',
            'data' => $task
        ], 201);
    }

    /**
     * 特定のタスクを取得
     */
    public function show(Task $task): JsonResponse
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json([
                'error' => [
                    'code' => 'FORBIDDEN',
                    'message' => 'このタスクにアクセスする権限がありません'
                ]
            ], 403);
        }

        $task->load(['subtasks', 'project', 'sessions']);

        return response()->json([
            'success' => true,
            'data' => $task
        ]);
    }

    /**
     * タスクを更新
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json([
                'error' => [
                    'code' => 'FORBIDDEN',
                    'message' => 'このタスクを更新する権限がありません'
                ]
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'project_id' => 'nullable|exists:projects,id',
            'due_at' => 'nullable|date|after:now',
            'estimated_minutes' => 'nullable|integer|min:0|max:600',
            'priority' => 'sometimes|required|integer|min:1|max:5',
            'energy_level' => 'sometimes|required|in:low,medium,high',
            'status' => 'sometimes|required|in:pending,in_progress,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => '入力データに誤りがあります',
                    'fields' => $validator->errors()
                ]
            ], 422);
        }

        $task->update($request->only([
            'title', 'description', 'project_id', 'due_at',
            'estimated_minutes', 'priority', 'energy_level', 'status'
        ]));

        // タスクが完了した場合、完了日時を設定
        if ($request->status === 'completed' && !$task->completed_at) {
            $task->update(['completed_at' => now()]);
        }

        $task->load(['subtasks', 'project']);

        return response()->json([
            'success' => true,
            'message' => 'タスクが更新されました',
            'data' => $task
        ]);
    }

    /**
     * タスクを削除
     */
    public function destroy(Task $task): JsonResponse
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json([
                'error' => [
                    'code' => 'FORBIDDEN',
                    'message' => 'このタスクを削除する権限がありません'
                ]
            ], 403);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'タスクが削除されました'
        ]);
    }

    /**
     * AIによるタスク分解
     */
    public function breakdown(Request $request, Task $task): JsonResponse
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json([
                'error' => [
                    'code' => 'FORBIDDEN',
                    'message' => 'このタスクを分解する権限がありません'
                ]
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'context' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => '入力データに誤りがあります',
                    'fields' => $validator->errors()
                ]
            ], 422);
        }

        // TODO: AI service を呼び出してタスクを分解
        // 現在はサンプルデータを返す
        $subtasks = [
            [
                'title' => 'タスクの準備',
                'description' => '必要な資料やツールを準備する',
                'estimated_minutes' => 15,
                'order_index' => 1,
            ],
            [
                'title' => 'メイン作業',
                'description' => 'タスクの核心部分を実行する',
                'estimated_minutes' => 45,
                'order_index' => 2,
            ],
            [
                'title' => '確認と整理',
                'description' => '作業結果を確認し、整理する',
                'estimated_minutes' => 15,
                'order_index' => 3,
            ],
        ];

        // 既存のサブタスクを削除
        $task->subtasks()->delete();

        // 新しいサブタスクを作成
        foreach ($subtasks as $subtaskData) {
            $task->subtasks()->create($subtaskData);
        }

        $task->load(['subtasks']);

        return response()->json([
            'success' => true,
            'message' => 'タスクが分解されました',
            'data' => $task
        ]);
    }
}
