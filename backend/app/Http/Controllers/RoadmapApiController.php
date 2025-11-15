<?php

namespace App\Http\Controllers;

use App\Services\RoadmapApiService;
use App\Models\LearningPathTemplate;
use App\Models\LearningMilestoneTemplate;
use App\Models\TaskTemplate;
use App\Models\LearningPath;
use App\Models\Task;
use App\Models\KnowledgeItem;
use App\Models\KnowledgeCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * RoadmapApiController
 * External roadmap API integration controller
 * 外部ロードマップAPI統合コントローラー
 */
class RoadmapApiController extends Controller
{
    private RoadmapApiService $roadmapService;

    public function __construct(RoadmapApiService $roadmapService)
    {
        $this->roadmapService = $roadmapService;
    }

    /**
     * Get popular IT roadmaps
     * GET /api/roadmaps/popular
     */
    public function popular(): JsonResponse
    {
        try {
            $roadmaps = $this->roadmapService->getPopularRoadmaps();

            return response()->json([
                'success' => true,
                'data' => $roadmaps,
                'message' => '人気のロードマップを取得しました'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching popular roadmaps: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'ロードマップの取得に失敗しました'
            ], 500);
        }
    }

    /**
     * Generate roadmap using AI
     * POST /api/roadmaps/generate
     */
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'topic' => 'required|string|max:255',
            'level' => 'nullable|in:beginner,intermediate,advanced',
        ]);

        try {
            $topic = $request->topic;
            $level = $request->level ?? 'beginner';

            $roadmap = $this->roadmapService->generateRoadmapWithAI($topic, $level);

            if (empty($roadmap)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ロードマップの生成に失敗しました'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $roadmap,
                'message' => 'ロードマップを生成しました'
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating roadmap: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'ロードマップの生成に失敗しました: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import roadmap as template
     * POST /api/roadmaps/import
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'roadmap_id' => 'nullable|string',
            'topic' => 'nullable|string',
            'level' => 'nullable|in:beginner,intermediate,advanced',
            'source' => 'required|in:popular,ai,microsoft_learn',
            'auto_clone' => 'nullable|boolean', // 自動的に学習パスにクローンするか
            // Study schedule is REQUIRED when auto_clone is true
            'study_schedules' => 'required_if:auto_clone,true|array|min:1',
            'study_schedules.*.day_of_week' => 'required|integer|between:0,6',
            'study_schedules.*.study_time' => 'required|date_format:H:i',
            'study_schedules.*.duration_minutes' => 'nullable|integer|min:15|max:480',
        ]);

        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => '認証が必要です'
                ], 401);
            }

            DB::beginTransaction();

            $roadmapData = null;

            // Fetch roadmap based on source
            switch ($request->source) {
                case 'popular':
                    $roadmaps = $this->roadmapService->getPopularRoadmaps();
                    $popularRoadmap = collect($roadmaps)->firstWhere('id', $request->roadmap_id);

                    if (!$popularRoadmap) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'ロードマップが見つかりません'
                        ], 404);
                    }

                    // Popular roadmaps chỉ có metadata, cần generate chi tiết bằng AI
                    $topic = $popularRoadmap['title'];
                    $level = $popularRoadmap['difficulty'] ?? 'beginner';
                    $roadmapData = $this->roadmapService->generateRoadmapWithAI($topic, $level);

                    // Merge metadata từ popular roadmap
                    if (!empty($roadmapData)) {
                        $roadmapData['title'] = $popularRoadmap['title'];
                        $roadmapData['description'] = $popularRoadmap['description'] ?? $roadmapData['description'] ?? '';
                        $roadmapData['category'] = $popularRoadmap['category'] ?? $roadmapData['category'] ?? 'programming';
                        $roadmapData['difficulty'] = $popularRoadmap['difficulty'] ?? $roadmapData['difficulty'] ?? 'beginner';
                        $roadmapData['estimated_hours'] = $popularRoadmap['estimated_hours'] ?? $roadmapData['estimated_hours'] ?? 0;
                        $roadmapData['url'] = $popularRoadmap['url'] ?? null;
                    }
                    break;

                case 'ai':
                    if (!$request->topic) {
                        return response()->json([
                            'success' => false,
                            'message' => 'トピックが必要です'
                        ], 400);
                    }
                    $roadmapData = $this->roadmapService->generateRoadmapWithAI(
                        $request->topic,
                        $request->level ?? 'beginner'
                    );
                    break;

                case 'microsoft_learn':
                    $roadmapData = $this->roadmapService->fetchMicrosoftLearnRoadmap(
                        $request->topic ?? 'developer'
                    );
                    break;
            }

            if (!$roadmapData || empty($roadmapData['milestones'])) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'ロードマップの詳細を取得できませんでした。もう一度お試しください。'
                ], 404);
            }

            // Create template from roadmap data
            $template = $this->createTemplateFromRoadmap($roadmapData);

            // Auto clone to user's learning path if requested
            $learningPath = null;
            if ($request->boolean('auto_clone', true)) {
                $learningPath = $this->cloneTemplateToLearningPath($user, $template);

                // Create study schedules for the learning path
                if ($request->has('study_schedules')) {
                    foreach ($request->study_schedules as $scheduleData) {
                        \App\Models\StudySchedule::create([
                            'learning_path_id' => $learningPath->id,
                            'day_of_week' => $scheduleData['day_of_week'],
                            'study_time' => $scheduleData['study_time'] . ':00', // Convert HH:MM to HH:MM:SS
                            'duration_minutes' => $scheduleData['duration_minutes'] ?? 60,
                            'is_active' => true,
                            'reminder_enabled' => $scheduleData['reminder_enabled'] ?? true,
                            'reminder_before_minutes' => $scheduleData['reminder_before_minutes'] ?? 30,
                        ]);
                    }

                    // Assign tasks to study schedules
                    $this->assignTasksToStudySchedules($learningPath);
                }

                $template->incrementUsage();
            }

            DB::commit();

            $responseData = [
                'template' => $template->load('milestones.tasks'),
            ];

            if ($learningPath) {
                $responseData['learning_path'] = $learningPath->load('milestones.tasks', 'studySchedules');
                $responseData['learning_path_id'] = $learningPath->id;
                $responseData['study_schedules'] = $learningPath->studySchedules;
                $responseData['weekly_schedule'] = $learningPath->getWeeklyScheduleSummary();
            }

            return response()->json([
                'success' => true,
                'data' => $responseData,
                'message' => $learningPath
                    ? 'ロードマップを学習パスとして追加しました'
                    : 'ロードマップをテンプレートとしてインポートしました'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error importing roadmap: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'ロードマップのインポートに失敗しました: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create LearningPathTemplate from roadmap data
     */
    private function createTemplateFromRoadmap(array $roadmapData): LearningPathTemplate
    {
        $template = LearningPathTemplate::create([
            'title' => $roadmapData['title'] ?? 'Imported Roadmap',
            'description' => $roadmapData['description'] ?? '',
            'category' => $roadmapData['category'] ?? 'programming',
            'difficulty' => $roadmapData['difficulty'] ?? 'beginner',
            'estimated_hours_total' => $roadmapData['estimated_hours'] ?? 0,
            'tags' => $roadmapData['tags'] ?? [],
            'is_featured' => false,
            'usage_count' => 0,
        ]);

        // Create milestones
        if (isset($roadmapData['milestones']) && is_array($roadmapData['milestones'])) {
            foreach ($roadmapData['milestones'] as $milestoneData) {
                $milestone = $template->milestones()->create([
                    'title' => $milestoneData['title'] ?? 'Milestone',
                    'description' => $milestoneData['description'] ?? '',
                    'sort_order' => $milestoneData['sort_order'] ?? 0,
                    'estimated_hours' => $milestoneData['estimated_hours'] ?? 0,
                ]);

                // Create tasks
                if (isset($milestoneData['tasks']) && is_array($milestoneData['tasks'])) {
                    foreach ($milestoneData['tasks'] as $index => $taskData) {
                        // Đảm bảo có subtasks và knowledge_items
                        $subtasks = $taskData['subtasks'] ?? [];
                        $knowledgeItems = $taskData['knowledge_items'] ?? [];

                        // Nếu không có subtasks, tự động tạo từ task description
                        if (empty($subtasks) && !empty($taskData['description'])) {
                            $subtasks = $this->generateDefaultSubtasks($taskData['title'], $taskData['description'], $taskData['estimated_minutes'] ?? 120);
                        }

                        // Nếu không có knowledge_items, tự động tạo từ task
                        if (empty($knowledgeItems)) {
                            $knowledgeItems = $this->generateDefaultKnowledgeItems($taskData['title'], $taskData['description']);
                        }

                        TaskTemplate::create([
                            'milestone_template_id' => $milestone->id,
                            'title' => $taskData['title'] ?? 'Task',
                            'description' => $taskData['description'] ?? '',
                            'estimated_minutes' => $taskData['estimated_minutes'] ?? 0,
                            'priority' => $taskData['priority'] ?? 3,
                            'sort_order' => $index + 1,
                            'subtasks' => $subtasks,
                            'knowledge_items' => $knowledgeItems,
                        ]);
                    }
                } else {
                    // Nếu milestone không có tasks, tạo default task
                    $this->createDefaultTaskForMilestone($milestone, $milestoneData);
                }
            }
        }

        return $template;
    }

    /**
     * Clone template to user's learning path
     * テンプレートをユーザーの学習パスにクローン
     */
    private function cloneTemplateToLearningPath(User $user, LearningPathTemplate $template): LearningPath
    {
        // Load template with relationships
        $template->load('milestones.tasks');

        // Create learning path from template
        $learningPath = LearningPath::create([
            'user_id' => $user->id,
            'title' => $template->title,
            'description' => $template->description,
            'goal_type' => $this->mapCategoryToGoalType($template->category),
            'estimated_hours_total' => $template->estimated_hours_total ?? 0,
            'status' => 'active',
            'is_ai_generated' => true, // Roadmap from API is AI-generated
            'ai_prompt' => null,
        ]);

        // Clone milestones and tasks
        if ($template->milestones && $template->milestones->count() > 0) {
            foreach ($template->milestones as $milestoneTemplate) {
                $milestone = $learningPath->milestones()->create([
                    'title' => $milestoneTemplate->title ?? 'Untitled Milestone',
                    'description' => $milestoneTemplate->description,
                    'sort_order' => $milestoneTemplate->sort_order ?? 0,
                    'estimated_hours' => $milestoneTemplate->estimated_hours ?? 0,
                    'status' => 'pending',
                ]);

                // Clone tasks if they exist
                if ($milestoneTemplate->tasks && $milestoneTemplate->tasks->count() > 0) {
                    foreach ($milestoneTemplate->tasks as $taskTemplate) {
                        $task = Task::create([
                            'user_id' => $user->id,
                            'learning_milestone_id' => $milestone->id,
                            'title' => $taskTemplate->title ?? 'Untitled Task',
                            'description' => $taskTemplate->description,
                            'category' => 'study',
                            'estimated_minutes' => $taskTemplate->estimated_minutes ?? 0,
                            'priority' => $taskTemplate->priority ?? 3,
                            'status' => 'pending',
                        ]);

                        // Create subtasks from template
                        // TaskTemplate casts subtasks to array, so check if it's array and not empty
                        if (is_array($taskTemplate->subtasks) && count($taskTemplate->subtasks) > 0) {
                            foreach ($taskTemplate->subtasks as $subtaskData) {
                                $task->subtasks()->create([
                                    'title' => $subtaskData['title'] ?? 'Subtask',
                                    'description' => $subtaskData['description'] ?? null,
                                    'estimated_minutes' => $subtaskData['estimated_minutes'] ?? 0,
                                    'is_completed' => false,
                                    'sort_order' => $subtaskData['sort_order'] ?? 0,
                                ]);
                            }
                        } else {
                            // Nếu không có subtasks trong template, tạo default subtasks
                            $defaultSubtasks = $this->generateDefaultSubtasks(
                                $taskTemplate->title,
                                $taskTemplate->description ?? '',
                                $taskTemplate->estimated_minutes ?? 120
                            );
                            foreach ($defaultSubtasks as $subtaskData) {
                                $task->subtasks()->create([
                                    'title' => $subtaskData['title'],
                                    'description' => $subtaskData['description'] ?? null,
                                    'estimated_minutes' => $subtaskData['estimated_minutes'] ?? 0,
                                    'is_completed' => false,
                                    'sort_order' => $subtaskData['sort_order'] ?? 0,
                                ]);
                            }
                        }

                        // Create knowledge items from template
                        // TaskTemplate casts knowledge_items to array, so check if it's array and not empty
                        if (is_array($taskTemplate->knowledge_items) && count($taskTemplate->knowledge_items) > 0) {
                            $this->createKnowledgeItems($user->id, $learningPath->id, $task->id, $taskTemplate->knowledge_items);
                        } else {
                            // Nếu không có knowledge_items trong template, tạo default knowledge items
                            $defaultKnowledgeItems = $this->generateDefaultKnowledgeItems(
                                $taskTemplate->title,
                                $taskTemplate->description ?? ''
                            );
                            $this->createKnowledgeItems($user->id, $learningPath->id, $task->id, $defaultKnowledgeItems);
                        }
                    }
                }
            }
        }

        return $learningPath;
    }

    /**
     * Map template category to learning path goal_type
     * テンプレートカテゴリーを学習パスのgoal_typeにマッピング
     */
    private function mapCategoryToGoalType($category)
    {
        $mapping = [
            'programming' => 'skill',
            'design' => 'skill',
            'business' => 'career',
            'language' => 'skill',
            'data_science' => 'skill',
            'other' => 'personal',
        ];

        return $mapping[$category] ?? 'personal';
    }

    /**
     * Create knowledge items for a task from template data
     * テンプレートデータからタスクのナレッジアイテムを作成
     */
    private function createKnowledgeItems($userId, $learningPathId, $taskId, $knowledgeItemsData)
    {
        // Get or create default category
        $category = KnowledgeCategory::firstOrCreate(
            ['user_id' => $userId, 'name' => 'プログラミング学習'],
            ['description' => 'プログラミング学習用のメモとコード', 'icon' => 'code', 'color' => '#3B82F6']
        );

        foreach ($knowledgeItemsData as $itemData) {
            $knowledgeItem = [
                'user_id' => $userId,
                'category_id' => $category->id,
                'learning_path_id' => $learningPathId,
                'source_task_id' => $taskId,
                'title' => $itemData['title'] ?? 'Untitled',
                'item_type' => $itemData['type'] ?? 'note',
                'view_count' => 0,
                'review_count' => 0,
                'retention_score' => 3,
                'is_favorite' => false,
                'is_archived' => false,
            ];

            // Add type-specific fields
            switch ($itemData['type'] ?? 'note') {
                case 'note':
                    $knowledgeItem['content'] = $itemData['content'] ?? '';
                    break;
                case 'code_snippet':
                    $knowledgeItem['content'] = $itemData['content'] ?? '';
                    $knowledgeItem['code_language'] = $itemData['code_language'] ?? 'java';
                    break;
                case 'resource_link':
                    $knowledgeItem['title'] = $itemData['title'] ?? 'Resource';
                    $knowledgeItem['url'] = $itemData['url'] ?? '';
                    $knowledgeItem['content'] = $itemData['description'] ?? '';
                    break;
                case 'exercise':
                    $knowledgeItem['question'] = $itemData['question'] ?? '';
                    $knowledgeItem['answer'] = $itemData['answer'] ?? '';
                    $knowledgeItem['difficulty'] = $itemData['difficulty'] ?? 'medium';
                    break;
            }

            KnowledgeItem::create($knowledgeItem);
        }
    }

    /**
     * Generate default subtasks from task description
     * タスクの説明からデフォルトのサブタスクを生成
     */
    private function generateDefaultSubtasks(string $taskTitle, string $description, int $estimatedMinutes): array
    {
        $subtasks = [];
        $estimatedPerSubtask = max(30, intval($estimatedMinutes / 3)); // Chia thành 3 subtasks

        // Tạo subtasks cơ bản dựa trên task title và description
        $subtasks[] = [
            'title' => $taskTitle . 'の基礎を学習',
            'description' => '基本概念と理論を理解する',
            'estimated_minutes' => $estimatedPerSubtask,
            'sort_order' => 1,
        ];

        $subtasks[] = [
            'title' => $taskTitle . 'を実践',
            'description' => '実際にコードを書いて練習する',
            'estimated_minutes' => $estimatedPerSubtask,
            'sort_order' => 2,
        ];

        $subtasks[] = [
            'title' => $taskTitle . 'を復習',
            'description' => '学習内容を確認し、理解を深める',
            'estimated_minutes' => $estimatedPerSubtask,
            'sort_order' => 3,
        ];

        return $subtasks;
    }

    /**
     * Generate default knowledge items for a task
     * タスクのデフォルトナレッジアイテムを生成
     */
    private function generateDefaultKnowledgeItems(string $taskTitle, string $description): array
    {
        $knowledgeItems = [];

        // Tạo note knowledge item
        $knowledgeItems[] = [
            'type' => 'note',
            'title' => $taskTitle . 'のメモ',
            'content' => $description . "\n\n## 学習ポイント\n\n- 基本概念を理解する\n- 実践を通じて習得する\n- 定期的に復習する",
            'sort_order' => 1,
        ];

        // Tạo resource link knowledge item (nếu có description)
        if (!empty($description)) {
            $knowledgeItems[] = [
                'type' => 'resource_link',
                'title' => $taskTitle . 'の学習リソース',
                'url' => '',
                'description' => '公式ドキュメントやチュートリアルを参照してください',
                'sort_order' => 2,
            ];
        }

        return $knowledgeItems;
    }

    /**
     * Create default task for milestone if no tasks exist
     * マイルストーンにタスクがない場合のデフォルトタスクを作成
     */
    private function createDefaultTaskForMilestone($milestone, array $milestoneData): void
    {
        $taskTitle = $milestoneData['title'] ?? '学習タスク';
        $description = $milestoneData['description'] ?? '';
        $estimatedHours = $milestoneData['estimated_hours'] ?? 0;
        $estimatedMinutes = $estimatedHours * 60;

        $subtasks = $this->generateDefaultSubtasks($taskTitle, $description, $estimatedMinutes);
        $knowledgeItems = $this->generateDefaultKnowledgeItems($taskTitle, $description);

        TaskTemplate::create([
            'milestone_template_id' => $milestone->id,
            'title' => $taskTitle,
            'description' => $description,
            'estimated_minutes' => $estimatedMinutes,
            'priority' => 3,
            'sort_order' => 1,
            'subtasks' => $subtasks,
            'knowledge_items' => $knowledgeItems,
        ]);
    }

    /**
     * Assign tasks to study schedules by distributing them across scheduled study times
     * タスクを学習スケジュールに割り当てて、予定された学習時間に分散させる
     */
    private function assignTasksToStudySchedules(LearningPath $learningPath): void
    {
        try {
            // Refresh and load study schedules and tasks
            $learningPath->refresh();
            $learningPath->load([
                'studySchedules' => function ($query) {
                    $query->where('is_active', true)
                        ->orderBy('day_of_week')
                        ->orderBy('study_time');
                },
                'milestones.tasks' => function ($query) {
                    $query->orderBy('id');
                }
            ]);

            $studySchedules = $learningPath->studySchedules;

            Log::info('Assigning tasks to study schedules', [
                'learning_path_id' => $learningPath->id,
                'schedules_count' => $studySchedules->count(),
            ]);

            // If no study schedules, skip assignment
            if ($studySchedules->isEmpty()) {
                Log::warning('No study schedules found for learning path', ['learning_path_id' => $learningPath->id]);
                return;
            }

            // Collect all tasks from all milestones in order
            $allTasks = collect();
            foreach ($learningPath->milestones as $milestone) {
                $allTasks = $allTasks->merge($milestone->tasks);
            }

            Log::info('Collected tasks from milestones', [
                'tasks_count' => $allTasks->count(),
                'milestones_count' => $learningPath->milestones->count()
            ]);

            // If no tasks, nothing to assign
            if ($allTasks->isEmpty()) {
                Log::warning('No tasks found in learning path', ['learning_path_id' => $learningPath->id]);
                return;
            }

            // Get current date and time
            $now = now();
            $currentDate = $now->copy()->startOfDay();

            // Find the next study session starting from today
            $nextStudyDate = $this->findNextStudyDate($currentDate, $studySchedules);

            Log::info('Starting task assignment', [
                'next_study_date' => $nextStudyDate->toDateString(),
                'schedules' => $studySchedules->map(fn($s) => [
                    'day' => $s->day_of_week,
                    'time' => $s->study_time
                ])->toArray()
            ]);

            // Distribute tasks across study schedules
            $taskIndex = 0;
            $tasksCount = $allTasks->count();
            $schedulesCount = $studySchedules->count();
            $currentSessionDate = $nextStudyDate;

            foreach ($allTasks as $task) {
                // Get the schedule for this task (round-robin through schedules)
                $scheduleIndex = $taskIndex % $schedulesCount;
                $schedule = $studySchedules[$scheduleIndex];

                // Set scheduled_time for this task
                $scheduledDateTime = $currentSessionDate->copy();
                $timeParts = explode(':', $schedule->study_time);
                $scheduledDateTime->setTime((int)$timeParts[0], (int)$timeParts[1], 0);

                // Update the task
                $task->update([
                    'scheduled_time' => $scheduledDateTime
                ]);

                Log::info('Assigned task to schedule', [
                    'task_id' => $task->id,
                    'task_title' => $task->title,
                    'scheduled_time' => $scheduledDateTime->toDateTimeString(),
                    'day_of_week' => $schedule->day_of_week,
                    'study_time' => $schedule->study_time,
                ]);

                // Move to next schedule slot
                $taskIndex++;

                // If we've cycled through all schedules, move to next week
                if ($taskIndex % $schedulesCount === 0) {
                    $currentSessionDate = $this->findNextStudyDate($currentSessionDate->addDay(), $studySchedules);
                }
            }

            Log::info('Task assignment completed', [
                'learning_path_id' => $learningPath->id,
                'tasks_assigned' => $tasksCount
            ]);

        } catch (\Exception $e) {
            Log::error('Error assigning tasks to study schedules', [
                'learning_path_id' => $learningPath->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Don't throw - let the import continue even if assignment fails
        }
    }

    /**
     * Find the next study date from given date based on study schedules
     * 学習スケジュールに基づいて指定された日付から次の学習日を見つける
     */
    private function findNextStudyDate($fromDate, $studySchedules)
    {
        $date = \Carbon\Carbon::instance($fromDate)->copy();

        // Try up to 14 days to find next study date
        for ($i = 0; $i < 14; $i++) {
            $dayOfWeek = $date->dayOfWeek; // 0 = Sunday, 6 = Saturday

            // Check if this day has any study schedule
            $hasSchedule = $studySchedules->contains(function ($schedule) use ($dayOfWeek) {
                return $schedule->day_of_week === $dayOfWeek;
            });

            if ($hasSchedule) {
                return $date;
            }

            $date->addDay();
        }

        // If no study date found in 14 days, just return the original date
        return \Carbon\Carbon::instance($fromDate);
    }
}

