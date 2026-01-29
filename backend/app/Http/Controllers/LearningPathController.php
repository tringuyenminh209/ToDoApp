<?php

namespace App\Http\Controllers;

use App\Models\LearningPath;
use App\Models\LearningPathTemplate;
use App\Services\CategoryService;
use App\Services\CourseTranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class LearningPathController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Get all learning paths for the authenticated user
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            $query = LearningPath::where('user_id', $user->id)
                ->with(['milestones', 'knowledgeItems', 'studySchedules']);

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by goal type
            if ($request->has('goal_type')) {
                $query->where('goal_type', $request->goal_type);
            }

            // Sort
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $paths = $query->get();
            $this->hydratePathsIconColor($paths);

            // Apply course translation for title/description when locale is vi or en
            $locale = app()->getLocale();
            $data = $paths->map(function ($path) use ($locale) {
                $arr = $path->toArray();
                if (in_array($locale, ['vi', 'en'])) {
                    $trans = CourseTranslationService::getTemplateTranslation($path->title, $locale);
                    if ($trans) {
                        $arr['title'] = $trans['title'];
                        $arr['description'] = $trans['description'];
                    }
                }
                return $arr;
            });

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching learning paths: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Learning paths の取得に失敗しました'
            ], 500);
        }
    }

    /**
     * Get a specific learning path
     */
    public function show($id)
    {
        try {
            $user = Auth::user();

            $path = LearningPath::where('user_id', $user->id)
                ->with([
                    'milestones.tasks.subtasks',
                    'milestones.tasks.knowledgeItems' => function($query) {
                        $query->orderBy('item_type')->orderBy('created_at');
                    },
                    'knowledgeItems',
                    'studySchedules'
                ])
                ->findOrFail($id);
            $this->hydratePathsIconColor(collect([$path]));

            $pathData = $path->toArray();
            $locale = app()->getLocale();
            if (in_array($locale, ['vi', 'en'])) {
                $trans = CourseTranslationService::getTemplateTranslation($path->title, $locale);
                if ($trans) {
                    $pathData['title'] = $trans['title'];
                    $pathData['description'] = $trans['description'];
                }
                // Milestone / task / subtask の title・description をコース翻訳で上書き
                $pathData = CourseTranslationService::applyPathDetailTranslations($pathData, $path->title, $locale);
            }

            return response()->json([
                'success' => true,
                'data' => $pathData
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching learning path: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Learning path の取得に失敗しました'
            ], 404);
        }
    }

    /**
     * Create a new learning path with optional milestones, tasks, subtasks and knowledge items
     * 新しい学習パスを作成（オプションでマイルストーン、タスク、サブタスク、ナレッジアイテムを含む）
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'goal_type' => ['required', Rule::in(['career', 'skill', 'certification', 'hobby'])],
                'target_start_date' => 'nullable|date',
                'target_end_date' => 'nullable|date|after_or_equal:target_start_date',
                'estimated_hours_total' => 'nullable|integer|min:0',
                'tags' => 'nullable|array',
                'color' => 'nullable|string|max:20',
                'icon' => 'nullable|string|max:50',
                // Milestones array validation
                'milestones' => 'nullable|array',
                'milestones.*.title' => 'required_with:milestones|string|max:255',
                'milestones.*.description' => 'nullable|string',
                'milestones.*.estimated_hours' => 'nullable|integer|min:0',
                'milestones.*.sort_order' => 'nullable|integer|min:0',
                'milestones.*.position_x' => 'nullable|numeric|min:0|max:100',
                'milestones.*.position_y' => 'nullable|numeric|min:0|max:100',
                // Tasks under milestones
                'milestones.*.tasks' => 'nullable|array',
                'milestones.*.tasks.*.title' => 'required_with:milestones.*.tasks|string|max:255',
                'milestones.*.tasks.*.description' => 'nullable|string',
                'milestones.*.tasks.*.estimated_minutes' => 'nullable|integer|min:0',
                'milestones.*.tasks.*.priority' => 'nullable|integer|min:1|max:5',
                // Subtasks under tasks
                'milestones.*.tasks.*.subtasks' => 'nullable|array',
                'milestones.*.tasks.*.subtasks.*.title' => 'required_with:milestones.*.tasks.*.subtasks|string|max:255',
                // Knowledge items under tasks
                'milestones.*.tasks.*.knowledge_items' => 'nullable|array',
                'milestones.*.tasks.*.knowledge_items.*.title' => 'required_with:milestones.*.tasks.*.knowledge_items|string|max:255',
                'milestones.*.tasks.*.knowledge_items.*.content' => 'nullable|string',
                'milestones.*.tasks.*.knowledge_items.*.item_type' => 'nullable|string|in:note,concept,code,link,reference',
            ]);

            $user = Auth::user();

            // Use database transaction to ensure data integrity
            $path = DB::transaction(function () use ($validated, $user) {
                // Create the learning path
                $path = \App\Models\LearningPath::create([
                    'user_id' => $user->id,
                    'title' => $validated['title'],
                    'description' => $validated['description'] ?? null,
                    'goal_type' => $validated['goal_type'],
                    'target_start_date' => $validated['target_start_date'] ?? null,
                    'target_end_date' => $validated['target_end_date'] ?? null,
                    'estimated_hours_total' => $validated['estimated_hours_total'] ?? null,
                    'tags' => $validated['tags'] ?? null,
                    'color' => $validated['color'] ?? '#3B82F6',
                    'icon' => $validated['icon'] ?? 'code',
                    'status' => 'active',
                    'progress_percentage' => 0,
                    'is_ai_generated' => false,
                ]);

                // Create milestones with tasks, subtasks and knowledge items
                if (!empty($validated['milestones'])) {
                    $this->createMilestonesWithTasks($path, $user, $validated['milestones']);
                }

                return $path;
            });

            // Auto-create Knowledge Category for this roadmap using CategoryService
            try {
                $this->categoryService->getOrCreateRoadmapCategory(
                    $user->id,
                    $validated['title'],
                    [
                        'color' => $validated['color'] ?? '#3B82F6',
                        'icon' => $validated['icon'] ?? 'code',
                    ]
                );
            } catch (\Exception $e) {
                // Log but don't fail the roadmap creation if category creation fails
                Log::warning('Failed to create knowledge category for roadmap: ' . $e->getMessage());
            }

            // Load relationships for response
            $path->load(['milestones.tasks.subtasks', 'milestones.tasks.knowledgeItems']);

            return response()->json([
                'success' => true,
                'data' => $path,
                'message' => 'Learning path を作成しました'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating learning path: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Learning path の作成に失敗しました'
            ], 500);
        }
    }

    /**
     * Create milestones with nested tasks, subtasks and knowledge items
     * マイルストーンをネストされたタスク、サブタスク、ナレッジアイテムとともに作成
     */
    private function createMilestonesWithTasks($path, $user, array $milestonesData): void
    {
        foreach ($milestonesData as $index => $milestoneData) {
            $milestone = $path->milestones()->create([
                'title' => $milestoneData['title'],
                'description' => $milestoneData['description'] ?? null,
                'estimated_hours' => $milestoneData['estimated_hours'] ?? null,
                'sort_order' => $milestoneData['sort_order'] ?? $index,
                'status' => 'pending',
                'progress_percentage' => 0,
            ]);

            // Create tasks for this milestone
            if (!empty($milestoneData['tasks'])) {
                foreach ($milestoneData['tasks'] as $taskIndex => $taskData) {
                    $task = \App\Models\Task::create([
                        'user_id' => $user->id,
                        'learning_milestone_id' => $milestone->id,
                        'title' => $taskData['title'],
                        'description' => $taskData['description'] ?? null,
                        'estimated_minutes' => $taskData['estimated_minutes'] ?? 30,
                        'priority' => $taskData['priority'] ?? 3,
                        'status' => 'pending',
                        'energy_level' => 3,
                    ]);

                    // Create subtasks for this task
                    if (!empty($taskData['subtasks'])) {
                        foreach ($taskData['subtasks'] as $subtaskIndex => $subtaskData) {
                            $task->subtasks()->create([
                                'title' => $subtaskData['title'],
                                'sort_order' => $subtaskIndex,
                                'is_completed' => false,
                            ]);
                        }
                    }

                    // Create knowledge items for this task
                    if (!empty($taskData['knowledge_items'])) {
                        foreach ($taskData['knowledge_items'] as $knowledgeData) {
                            \App\Models\KnowledgeItem::create([
                                'user_id' => $user->id,
                                'task_id' => $task->id,
                                'learning_path_id' => $path->id,
                                'title' => $knowledgeData['title'],
                                'content' => $knowledgeData['content'] ?? '',
                                'item_type' => $knowledgeData['item_type'] ?? 'note',
                                'status' => 'active',
                            ]);
                        }
                    }
                }
            }
        }
    }


    /**
     * Update a learning path
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();

            $path = LearningPath::where('user_id', $user->id)->findOrFail($id);

            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'goal_type' => ['sometimes', Rule::in(['career', 'skill', 'certification', 'hobby'])],
                'target_start_date' => 'nullable|date',
                'target_end_date' => 'nullable|date',
                'status' => ['sometimes', Rule::in(['active', 'paused', 'completed', 'abandoned'])],
                'estimated_hours_total' => 'nullable|integer|min:0',
                'tags' => 'nullable|array',
                'color' => 'nullable|string|max:20',
                'icon' => 'nullable|string|max:50',
            ]);

            // If title changed, sync with knowledge category
            if (isset($validated['title']) && $validated['title'] !== $path->title) {
                try {
                    $this->categoryService->syncCategoryWithRoadmapTitle(
                        $user->id,
                        $path->title,
                        $validated['title']
                    );
                } catch (\Exception $e) {
                    Log::warning('Failed to sync category with roadmap title: ' . $e->getMessage());
                }
            }

            $path->update($validated);

            return response()->json([
                'success' => true,
                'data' => $path,
                'message' => 'Learning path を更新しました'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating learning path: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Learning path の更新に失敗しました'
            ], 500);
        }
    }

    /**
     * Delete a learning path
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();

            $path = LearningPath::where('user_id', $user->id)->findOrFail($id);
            $path->delete();

            return response()->json([
                'success' => true,
                'message' => 'Learning path を削除しました'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting learning path: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Learning path の削除に失敗しました'
            ], 500);
        }
    }

    /**
     * Mark a learning path as completed
     */
    public function complete($id)
    {
        try {
            $user = Auth::user();

            $path = LearningPath::where('user_id', $user->id)->findOrFail($id);

            $path->update([
                'status' => 'completed',
                'progress_percentage' => 100
            ]);

            return response()->json([
                'success' => true,
                'data' => $path,
                'message' => 'Learning path を完了しました'
            ]);
        } catch (\Exception $e) {
            Log::error('Error completing learning path: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Learning path の完了に失敗しました'
            ], 500);
        }
    }

    /**
     * Get learning path statistics
     */
    public function stats()
    {
        try {
            $user = Auth::user();

            $stats = [
                'total' => LearningPath::where('user_id', $user->id)->count(),
                'active' => LearningPath::where('user_id', $user->id)->where('status', 'active')->count(),
                'completed' => LearningPath::where('user_id', $user->id)->where('status', 'completed')->count(),
                'paused' => LearningPath::where('user_id', $user->id)->where('status', 'paused')->count(),
                'average_progress' => LearningPath::where('user_id', $user->id)->avg('progress_percentage') ?? 0,
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching learning path stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '統計の取得に失敗しました'
            ], 500);
        }
    }

    /**
     * Get milestones for a learning path
     * GET /api/learning-paths/{id}/milestones
     */
    public function getMilestones($id)
    {
        try {
            $user = Auth::user();
            $path = LearningPath::where('user_id', $user->id)->findOrFail($id);
            
            $milestones = $path->milestones()
                ->with(['tasks.subtasks', 'tasks.knowledgeItems'])
                ->orderBy('sort_order')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $milestones
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching milestones: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'マイルストーンの取得に失敗しました'
            ], 500);
        }
    }

    /**
     * Create a new milestone for a learning path
     * POST /api/learning-paths/{id}/milestones
     */
    public function storeMilestone(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $path = LearningPath::where('user_id', $user->id)->findOrFail($id);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'estimated_hours' => 'nullable|integer|min:0',
                'sort_order' => 'nullable|integer|min:0',
                'tasks' => 'nullable|array',
                'tasks.*.title' => 'required_with:tasks|string|max:255',
                'tasks.*.description' => 'nullable|string',
                'tasks.*.estimated_minutes' => 'nullable|integer|min:0',
                'tasks.*.priority' => 'nullable|integer|min:1|max:5',
            ]);

            $milestone = DB::transaction(function () use ($path, $user, $validated) {
                $milestone = $path->milestones()->create([
                    'title' => $validated['title'],
                    'description' => $validated['description'] ?? null,
                    'estimated_hours' => $validated['estimated_hours'] ?? null,
                    'sort_order' => $validated['sort_order'] ?? $path->milestones()->count(),
                    'status' => 'pending',
                    'progress_percentage' => 0,
                ]);

                // Create tasks if provided
                if (!empty($validated['tasks'])) {
                    foreach ($validated['tasks'] as $taskData) {
                        \App\Models\Task::create([
                            'user_id' => $user->id,
                            'learning_milestone_id' => $milestone->id,
                            'title' => $taskData['title'],
                            'description' => $taskData['description'] ?? null,
                            'estimated_minutes' => $taskData['estimated_minutes'] ?? 30,
                            'priority' => $taskData['priority'] ?? 3,
                            'status' => 'pending',
                            'energy_level' => 3,
                        ]);
                    }
                }

                return $milestone;
            });

            $milestone->load(['tasks.subtasks', 'tasks.knowledgeItems']);

            return response()->json([
                'success' => true,
                'data' => $milestone,
                'message' => 'マイルストーンを作成しました'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating milestone: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'マイルストーンの作成に失敗しました'
            ], 500);
        }
    }

    /**
     * Update a milestone
     * PUT /api/learning-paths/milestones/{milestoneId}
     */
    public function updateMilestone(Request $request, $milestoneId)
    {
        try {
            $user = Auth::user();
            
            $milestone = \App\Models\LearningMilestone::whereHas('learningPath', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->findOrFail($milestoneId);

            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'estimated_hours' => 'nullable|integer|min:0',
                'sort_order' => 'nullable|integer|min:0',
                'status' => 'sometimes|in:pending,in_progress,completed,skipped',
            ]);

            $milestone->update($validated);

            return response()->json([
                'success' => true,
                'data' => $milestone,
                'message' => 'マイルストーンを更新しました'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating milestone: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'マイルストーンの更新に失敗しました'
            ], 500);
        }
    }

    /**
     * Delete a milestone
     * DELETE /api/learning-paths/milestones/{milestoneId}
     */
    public function destroyMilestone($milestoneId)
    {
        try {
            $user = Auth::user();
            
            $milestone = \App\Models\LearningMilestone::whereHas('learningPath', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->findOrFail($milestoneId);

            $milestone->delete();

            return response()->json([
                'success' => true,
                'message' => 'マイルストーンを削除しました'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting milestone: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'マイルストーンの削除に失敗しました'
            ], 500);
        }
    }

    /**
     * Add a task to a milestone
     * POST /api/learning-paths/milestones/{milestoneId}/tasks
     */
    public function storeTaskToMilestone(Request $request, $milestoneId)
    {
        try {
            $user = Auth::user();
            
            $milestone = \App\Models\LearningMilestone::whereHas('learningPath', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->findOrFail($milestoneId);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'estimated_minutes' => 'nullable|integer|min:0',
                'priority' => 'nullable|integer|min:1|max:5',
                'subtasks' => 'nullable|array',
                'subtasks.*.title' => 'required_with:subtasks|string|max:255',
                'knowledge_items' => 'nullable|array',
                'knowledge_items.*.title' => 'required_with:knowledge_items|string|max:255',
                'knowledge_items.*.content' => 'nullable|string',
                'knowledge_items.*.item_type' => 'nullable|string|in:note,concept,code,link,reference',
            ]);

            $task = DB::transaction(function () use ($user, $milestone, $validated) {
                $task = \App\Models\Task::create([
                    'user_id' => $user->id,
                    'learning_milestone_id' => $milestone->id,
                    'title' => $validated['title'],
                    'description' => $validated['description'] ?? null,
                    'estimated_minutes' => $validated['estimated_minutes'] ?? 30,
                    'priority' => $validated['priority'] ?? 3,
                    'status' => 'pending',
                    'energy_level' => 3,
                ]);

                // Create subtasks if provided
                if (!empty($validated['subtasks'])) {
                    foreach ($validated['subtasks'] as $index => $subtaskData) {
                        $task->subtasks()->create([
                            'title' => $subtaskData['title'],
                            'sort_order' => $index,
                            'is_completed' => false,
                        ]);
                    }
                }

                // Create knowledge items if provided
                if (!empty($validated['knowledge_items'])) {
                    foreach ($validated['knowledge_items'] as $knowledgeData) {
                        \App\Models\KnowledgeItem::create([
                            'user_id' => $user->id,
                            'task_id' => $task->id,
                            'learning_path_id' => $milestone->learning_path_id,
                            'title' => $knowledgeData['title'],
                            'content' => $knowledgeData['content'] ?? '',
                            'item_type' => $knowledgeData['item_type'] ?? 'note',
                            'status' => 'active',
                        ]);
                    }
                }

                return $task;
            });

            $task->load(['subtasks', 'knowledgeItems']);

            return response()->json([
                'success' => true,
                'data' => $task,
                'message' => 'タスクを作成しました'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating task: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'タスクの作成に失敗しました'
            ], 500);
        }
    }


    /**
     * Backfill icon/color from templates for existing paths.
     */
    private function hydratePathsIconColor($paths): void
    {
        if ($paths->isEmpty()) {
            return;
        }

        $missingTitles = $paths
            ->filter(function ($path) {
                $iconEmpty = !$path->icon || trim((string) $path->icon) === '';
                $colorEmpty = !$path->color || trim((string) $path->color) === '';
                return $iconEmpty || $colorEmpty;
            })
            ->pluck('title')
            ->filter()
            ->unique()
            ->values();

        if ($missingTitles->isEmpty()) {
            return;
        }

        $templates = LearningPathTemplate::whereIn('title', $missingTitles)
            ->get(['title', 'icon', 'color'])
            ->keyBy('title');

        $paths->each(function ($path) use ($templates) {
            $template = $templates->get($path->title);
            if (!$template) {
                return;
            }

            $updates = [];
            if ((!$path->icon || trim((string) $path->icon) === '') && $template->icon) {
                $updates['icon'] = $template->icon;
                $path->icon = $template->icon;
            }
            if ((!$path->color || trim((string) $path->color) === '') && $template->color) {
                $updates['color'] = $template->color;
                $path->color = $template->color;
            }

            if (!empty($updates)) {
                $path->update($updates);
            }
        });
    }
}

