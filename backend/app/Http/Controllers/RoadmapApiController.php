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
                    $roadmapData = collect($roadmaps)->firstWhere('id', $request->roadmap_id);
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

            if (!$roadmapData) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'ロードマップが見つかりません'
                ], 404);
            }

            // Create template from roadmap data
            $template = $this->createTemplateFromRoadmap($roadmapData);

            // Auto clone to user's learning path if requested
            $learningPath = null;
            if ($request->boolean('auto_clone', true)) {
                $learningPath = $this->cloneTemplateToLearningPath($user, $template);
                $template->incrementUsage();
            }

            DB::commit();

            $responseData = [
                'template' => $template->load('milestones.tasks'),
            ];

            if ($learningPath) {
                $responseData['learning_path'] = $learningPath->load('milestones.tasks');
                $responseData['learning_path_id'] = $learningPath->id;
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
                        TaskTemplate::create([
                            'milestone_template_id' => $milestone->id,
                            'title' => $taskData['title'] ?? 'Task',
                            'description' => $taskData['description'] ?? '',
                            'estimated_minutes' => $taskData['estimated_minutes'] ?? 0,
                            'priority' => $taskData['priority'] ?? 3,
                            'sort_order' => $index + 1,
                            'subtasks' => $taskData['subtasks'] ?? [],
                            'knowledge_items' => $taskData['knowledge_items'] ?? [],
                        ]);
                    }
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
                        if (!empty($taskTemplate->subtasks) && is_array($taskTemplate->subtasks)) {
                            foreach ($taskTemplate->subtasks as $subtaskData) {
                                $task->subtasks()->create([
                                    'title' => $subtaskData['title'] ?? 'Subtask',
                                    'description' => $subtaskData['description'] ?? null,
                                    'estimated_minutes' => $subtaskData['estimated_minutes'] ?? 0,
                                    'is_completed' => false,
                                    'sort_order' => $subtaskData['sort_order'] ?? 0,
                                ]);
                            }
                        }

                        // Create knowledge items from template
                        if (!empty($taskTemplate->knowledge_items) && is_array($taskTemplate->knowledge_items)) {
                            $this->createKnowledgeItems($user->id, $learningPath->id, $task->id, $taskTemplate->knowledge_items);
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
}

