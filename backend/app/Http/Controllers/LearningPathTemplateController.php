<?php

namespace App\Http\Controllers;

use App\Models\LearningPath;
use App\Models\LearningPathTemplate;
use App\Models\Task;
use App\Models\KnowledgeItem;
use App\Models\KnowledgeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LearningPathTemplateController extends Controller
{
    /**
     * Get all templates with optional filters
     */
    public function index(Request $request)
    {
        try {
            $query = LearningPathTemplate::query();

            // Filter by category
            if ($request->has('category')) {
                $query->byCategory($request->category);
            }

            // Filter by difficulty
            if ($request->has('difficulty')) {
                $query->byDifficulty($request->difficulty);
            }

            // Filter featured
            if ($request->has('featured') && $request->featured) {
                $query->featured();
            }

            // Sort
            $sortBy = $request->get('sort_by', 'usage_count');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginate or get all
            if ($request->has('per_page')) {
                $templates = $query->paginate($request->per_page);
            } else {
                $templates = $query->get();
            }

            return response()->json([
                'success' => true,
                'data' => $templates
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching templates: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'テンプレートの取得に失敗しました'
            ], 500);
        }
    }

    /**
     * Get template detail with milestones and tasks
     */
    public function show($id)
    {
        try {
            $template = LearningPathTemplate::with([
                'milestones.tasks' => function ($query) {
                    $query->orderBy('sort_order');
                }
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $template
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching template detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'テンプレート詳細の取得に失敗しました'
            ], 500);
        }
    }

    /**
     * Get featured templates
     */
    public function featured()
    {
        try {
            $templates = LearningPathTemplate::featured()
                ->orderBy('usage_count', 'desc')
                ->limit(6)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $templates
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching featured templates: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'おすすめテンプレートの取得に失敗しました'
            ], 500);
        }
    }

    /**
     * Get templates by category
     */
    public function byCategory($category)
    {
        try {
            $templates = LearningPathTemplate::byCategory($category)
                ->orderBy('usage_count', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $templates
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching templates by category: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'カテゴリー別テンプレートの取得に失敗しました'
            ], 500);
        }
    }

    /**
     * Clone template to user's learning path
     */
    public function clone(Request $request, $id)
    {
        try {
            // Validate user
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => '認証が必要です'
                ], 401);
            }

            // Get template with relationships
            $template = LearningPathTemplate::with('milestones.tasks')->findOrFail($id);

            DB::beginTransaction();

            // Create learning path from template
            $learningPath = LearningPath::create([
                'user_id' => $user->id,
                'title' => $template->title,
                'description' => $template->description,
                'goal_type' => $this->mapCategoryToGoalType($template->category),
                'estimated_hours_total' => $template->estimated_hours_total ?? 0,
                'status' => 'active',
                'is_ai_generated' => false,
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

            // Increment template usage count
            $template->incrementUsage();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'テンプレートから学習パスを作成しました',
                'data' => [
                    'learning_path_id' => $learningPath->id,
                    'learning_path' => $learningPath->load('milestones.tasks')
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::error('Template not found: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'テンプレートが見つかりません'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cloning template: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'テンプレートのクローンに失敗しました: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get popular templates
     */
    public function popular()
    {
        try {
            $templates = LearningPathTemplate::popular(10)->get();

            return response()->json([
                'success' => true,
                'data' => $templates
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching popular templates: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '人気テンプレートの取得に失敗しました'
            ], 500);
        }
    }

    /**
     * Get available categories
     */
    public function categories()
    {
        try {
            $categories = DB::table('learning_path_templates')
                ->select('category', DB::raw('COUNT(*) as count'))
                ->groupBy('category')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching categories: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'カテゴリーの取得に失敗しました'
            ], 500);
        }
    }

    /**
     * Map template category to learning path goal_type
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

