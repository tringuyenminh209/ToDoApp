<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /api/tasks
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Task::with(['project', 'learningMilestone', 'subtasks', 'tags'])
            ->where('user_id', $user->id);

        // Filtering
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

        if ($request->has('milestone_id')) {
            $query->where('learning_milestone_id', $request->milestone_id);
        }

        if ($request->has('overdue')) {
            $query->overdue();
        }

        if ($request->has('due_soon')) {
            $query->dueSoon($request->get('due_soon', 3));
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        // Default sorting: by date (scheduled_time or deadline), then by priority
        if (!$request->has('sort_by')) {
            // Use scheduled_time if available, otherwise use deadline
            // Order by earliest date first (NULLs last), then by highest priority
            $query->orderByRaw('CASE WHEN scheduled_time IS NULL AND deadline IS NULL THEN 1 ELSE 0 END')
                  ->orderByRaw('COALESCE(scheduled_time, deadline) ASC')
                  ->orderBy('priority', 'desc')
                  ->orderBy('created_at', 'desc');
        } else {
            // Custom sorting if specified
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $allowedSortFields = ['created_at', 'updated_at', 'priority', 'deadline', 'title', 'scheduled_time'];
            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy($sortBy, $sortOrder);
            }
        }

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $tasks = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $tasks,
            'message' => 'タスク一覧を取得しました'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * POST /api/tasks
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|in:study,work,personal,other',
            'description' => 'nullable|string|max:1000',
            'priority' => 'required|integer|min:1|max:5',
            'energy_level' => 'required|in:low,medium,high',
            'estimated_minutes' => 'nullable|integer|min:1|max:600',
            'deadline' => 'nullable|date|after_or_equal:today',
            'scheduled_time' => 'nullable|date_format:H:i:s,H:i',
            'project_id' => 'nullable|exists:projects,id',
            'learning_milestone_id' => 'nullable|exists:learning_milestones,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
            // Focus enhancement fields
            'requires_deep_focus' => 'nullable|boolean',
            'allow_interruptions' => 'nullable|boolean',
            'focus_difficulty' => 'nullable|integer|min:1|max:5',
            'warmup_minutes' => 'nullable|integer|min:0|max:60',
            'cooldown_minutes' => 'nullable|integer|min:0|max:60',
            'recovery_minutes' => 'nullable|integer|min:0|max:120',
        ]);

        try {
            DB::beginTransaction();

            $task = Task::create([
                'user_id' => $request->user()->id,
                'title' => $request->title,
                'category' => $request->category ?? 'other',
                'description' => $request->description,
                'priority' => $request->priority,
                'energy_level' => $request->energy_level,
                'estimated_minutes' => $request->estimated_minutes,
                'deadline' => $request->deadline,
                'scheduled_time' => $request->scheduled_time,
                'project_id' => $request->project_id,
                'learning_milestone_id' => $request->learning_milestone_id,
                'status' => 'pending',
                'ai_breakdown_enabled' => false,
                // Focus enhancement fields
                'requires_deep_focus' => $request->requires_deep_focus ?? false,
                'allow_interruptions' => $request->allow_interruptions ?? true,
                'focus_difficulty' => $request->focus_difficulty ?? 3,
                'warmup_minutes' => $request->warmup_minutes,
                'cooldown_minutes' => $request->cooldown_minutes,
                'recovery_minutes' => $request->recovery_minutes,
            ]);

            // Attach tags if provided
            if ($request->has('tag_ids')) {
                $task->tags()->attach($request->tag_ids);
            }

            DB::commit();

            $task->load(['project', 'learningMilestone', 'subtasks', 'tags']);

            return response()->json([
                'success' => true,
                'data' => $task,
                'message' => 'タスクを作成しました！'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'タスクの作成に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * GET /api/tasks/{id}
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $task = Task::with([
            'project',
            'learningMilestone',
            'subtasks',
            'tags',
            'focusSessions' => function($query) {
                $query->orderBy('started_at', 'desc')->limit(10);
            }
        ])
        ->where('user_id', $request->user()->id)
        ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $task,
            'message' => 'タスク詳細を取得しました'
        ]);
    }

    /**
     * Update the specified resource in storage.
     * PUT /api/tasks/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $task = Task::where('user_id', $request->user()->id)->findOrFail($id);

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'category' => 'nullable|in:study,work,personal,other',
            'description' => 'nullable|string|max:1000',
            'priority' => 'sometimes|integer|min:1|max:5',
            'energy_level' => 'sometimes|in:low,medium,high',
            'estimated_minutes' => 'nullable|integer|min:1|max:600',
            'deadline' => 'nullable|date',
            'scheduled_time' => 'nullable|date_format:H:i:s,H:i',
            'status' => 'sometimes|in:pending,in_progress,completed,cancelled',
            'project_id' => 'nullable|exists:projects,id',
            'learning_milestone_id' => 'nullable|exists:learning_milestones,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
            // Deep Work fields
            'requires_deep_focus' => 'nullable|boolean',
            'allow_interruptions' => 'nullable|boolean',
            'focus_difficulty' => 'nullable|integer|min:1|max:5',
            'warmup_minutes' => 'nullable|integer|min:0|max:60',
            'cooldown_minutes' => 'nullable|integer|min:0|max:60',
        ]);

        try {
            DB::beginTransaction();

            $updateData = $request->only([
                'title', 'category', 'description', 'priority', 'energy_level',
                'estimated_minutes', 'deadline', 'status', 'project_id',
                'learning_milestone_id',
                // Deep Work fields
                'requires_deep_focus', 'allow_interruptions', 'focus_difficulty',
                'warmup_minutes', 'cooldown_minutes'
            ]);

            $task->update($updateData);

            // Update tags if provided
            if ($request->has('tag_ids')) {
                $task->tags()->sync($request->tag_ids);
            }

            DB::commit();

            $task->load(['project', 'learningMilestone', 'subtasks', 'tags']);

            return response()->json([
                'success' => true,
                'data' => $task,
                'message' => 'タスクを更新しました！'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'タスクの更新に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /api/tasks/{id}
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $task = Task::where('user_id', $request->user()->id)->findOrFail($id);

        try {
            DB::beginTransaction();

            // Delete related data (tags need manual detach, others cascade automatically)
            $task->tags()->detach();

            // Subtasks and FocusSessions cascade delete via foreign key constraints
            $task->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'タスクを削除しました！'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'タスクの削除に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark task as completed
     * PUT /api/tasks/{id}/complete
     */
    public function complete(Request $request, string $id): JsonResponse
    {
        $task = Task::where('user_id', $request->user()->id)->findOrFail($id);

        if ($task->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'タスクは既に完了しています'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $task->markAsCompleted();

            DB::commit();

            $task->load(['project', 'learningMilestone', 'subtasks', 'tags']);

            return response()->json([
                'success' => true,
                'data' => $task,
                'message' => 'タスクを完了しました！'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'タスクの完了に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Start task (mark as in progress)
     * PUT /api/tasks/{id}/start
     */
    public function start(Request $request, string $id): JsonResponse
    {
        $task = Task::where('user_id', $request->user()->id)->findOrFail($id);

        if ($task->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => '完了したタスクは開始できません'
            ], 400);
        }

        if ($task->status === 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'タスクは既に進行中です'
            ], 400);
        }

        try {
            $task->markAsInProgress();

            $task->load(['project', 'learningMilestone', 'subtasks', 'tags']);

            return response()->json([
                'success' => true,
                'data' => $task,
                'message' => 'タスクを開始しました！'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'タスクの開始に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get task statistics
     * GET /api/tasks/stats
     */
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        $stats = [
            'total' => Task::where('user_id', $user->id)->count(),
            'pending' => Task::where('user_id', $user->id)->pending()->count(),
            'in_progress' => Task::where('user_id', $user->id)->inProgress()->count(),
            'completed' => Task::where('user_id', $user->id)->completed()->count(),
            'cancelled' => Task::where('user_id', $user->id)->cancelled()->count(),
            'overdue' => Task::where('user_id', $user->id)->overdue()->count(),
            'due_soon' => Task::where('user_id', $user->id)->dueSoon()->count(),
            'high_priority' => Task::where('user_id', $user->id)->highPriority()->count(),
            'with_ai_breakdown' => Task::where('user_id', $user->id)->aiBreakdownEnabled()->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'タスク統計を取得しました'
        ]);
    }

    /**
     * Get tasks by priority
     * GET /api/tasks/by-priority/{priority}
     */
    public function byPriority(Request $request, int $priority): JsonResponse
    {
        if ($priority < 1 || $priority > 5) {
            return response()->json([
                'success' => false,
                'message' => '優先度は1-5の範囲で指定してください'
            ], 400);
        }

        $tasks = Task::with(['project', 'learningMilestone', 'subtasks', 'tags'])
            ->where('user_id', $request->user()->id)
            ->where('priority', $priority)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $tasks,
            'message' => "優先度{$priority}のタスクを取得しました"
        ]);
    }

    /**
     * Get overdue tasks
     * GET /api/tasks/overdue
     */
    public function overdue(Request $request): JsonResponse
    {
        $tasks = Task::with(['project', 'learningMilestone', 'subtasks', 'tags'])
            ->where('user_id', $request->user()->id)
            ->overdue()
            ->orderBy('deadline', 'asc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $tasks,
            'message' => '期限切れタスクを取得しました'
        ]);
    }

    /**
     * Get tasks due soon
     * GET /api/tasks/due-soon
     */
    public function dueSoon(Request $request): JsonResponse
    {
        $days = $request->get('days', 3);

        $tasks = Task::with(['project', 'learningMilestone', 'subtasks', 'tags'])
            ->where('user_id', $request->user()->id)
            ->dueSoon($days)
            ->orderBy('deadline', 'asc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $tasks,
            'message' => "{$days}日以内の期限タスクを取得しました"
        ]);
    }
}
