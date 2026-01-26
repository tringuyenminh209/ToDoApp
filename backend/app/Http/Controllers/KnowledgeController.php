<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeItem;
use App\Models\KnowledgeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class KnowledgeController extends Controller
{
    /**
     * Get all knowledge items for authenticated user
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = KnowledgeItem::where('user_id', $user->id)
            ->withTranslations()
            ->with(['category' => function ($q) {
                $q->withTranslations();
            }, 'learningPath', 'sourceTask']);

        // Debug logging - check all query params
        Log::info('Knowledge API request', [
            'all_query' => $request->all(),
            'source_task_id' => $request->source_task_id,
            'source_task_id_type' => gettype($request->source_task_id),
            'learning_path_id' => $request->learning_path_id,
            'user_id' => $user->id,
            'query_string' => $request->getQueryString(),
            'full_url' => $request->fullUrl(),
        ]);

        // Filter by type (also support 'filter' parameter for backward compatibility)
        if ($request->has('type')) {
            $query->where('item_type', $request->type);
        } elseif ($request->has('filter')) {
            $query->where('item_type', $request->filter);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by learning path
        // Note: If both learning_path_id and source_task_id are provided,
        // we use OR logic (items from either source)
        $hasLearningPathFilter = $request->has('learning_path_id');
        $hasSourceTaskFilter = $request->has('source_task_id');

        if ($hasLearningPathFilter && !$hasSourceTaskFilter) {
            // Only learning_path_id provided
            $learningPathId = $request->learning_path_id;
            if (is_array($learningPathId)) {
                $query->whereIn('learning_path_id', $learningPathId);
            } else {
                $query->where('learning_path_id', $learningPathId);
            }
        }

        // Filter by source task (support single ID or array of IDs)
        // When multiple query params with same name are sent (e.g., source_task_id=45&source_task_id=142),
        // Retrofit sends them as multiple query params with the same name
        // We need to manually extract all values from the query string using regex
        if ($request->has('source_task_id')) {
            $sourceTaskId = [];
            // IMPORTANT: Use $_SERVER['QUERY_STRING'] to get RAW query string before PHP processing
            // $request->getQueryString() returns already-parsed string where PHP keeps only last value
            $queryString = $_SERVER['QUERY_STRING'] ?? $request->getQueryString();

            // First, try to extract using regex (most reliable for multiple params with same name)
            // Support both formats: ?source_task_id=1&source_task_id=2 and ?source_task_id[]=1&source_task_id[]=2
            if ($queryString) {
                preg_match_all('/[&?]source_task_id(?:\[\])?=(\d+)/', $queryString, $matches);
                if (!empty($matches[1])) {
                    $sourceTaskId = array_map('intval', $matches[1]);
                }
            }

            // Fallback: try parse_str() which should create array for duplicate keys
            if (empty($sourceTaskId) && $queryString) {
                parse_str($queryString, $parsed);
                if (isset($parsed['source_task_id'])) {
                    $sourceTaskId = $parsed['source_task_id'];
                    if (!is_array($sourceTaskId)) {
                        $sourceTaskId = [$sourceTaskId];
                    }
                }
            }

            // Last fallback: try request input (Laravel might have parsed it)
            if (empty($sourceTaskId)) {
                $inputValue = $request->input('source_task_id');
                if ($inputValue !== null) {
                    $sourceTaskId = is_array($inputValue) ? $inputValue : [$inputValue];
                }
            }

            Log::info('Processing source_task_id filter', [
                'raw_value' => $sourceTaskId,
                'type' => gettype($sourceTaskId),
                'is_array' => is_array($sourceTaskId),
                'query_string' => $queryString,
                'regex_matches' => $matches[1] ?? null,
            ]);

            // Filter out null values and ensure all are integers
            $sourceTaskId = array_filter(array_map('intval', $sourceTaskId), function($id) {
                return $id > 0;
            });

            Log::info('Filtered source_task_id', [
                'filtered_ids' => $sourceTaskId,
                'count' => count($sourceTaskId),
            ]);

            if (!empty($sourceTaskId)) {
                // If both learning_path_id and source_task_id are provided,
                // use OR logic (items from either source)
                if ($hasLearningPathFilter) {
                    $learningPathId = $request->learning_path_id;
                    $query->where(function($q) use ($sourceTaskId, $learningPathId) {
                        $q->whereIn('source_task_id', $sourceTaskId);
                        if (is_array($learningPathId)) {
                            $q->orWhereIn('learning_path_id', $learningPathId);
                        } else {
                            $q->orWhere('learning_path_id', $learningPathId);
                        }
                    });
                } else {
                    // Only source_task_id provided
                    $query->whereIn('source_task_id', $sourceTaskId);
                }
            } else {
                Log::warning('source_task_id filter is empty after processing', [
                    'query_string' => $queryString,
                    'request_all' => $request->all(),
                ]);
            }
        }

        // Filter favorites
        if ($request->has('favorites') && $request->favorites) {
            $query->where('is_favorite', true);
        }

        // Filter archived
        if ($request->has('archived')) {
            $query->where('is_archived', $request->archived);
        } else {
            // By default, don't show archived items
            $query->where('is_archived', false);
        }

        // Filter due for review
        if ($request->has('due_review') && $request->due_review) {
            $query->dueForReview();
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Log the SQL query before execution
        Log::info('Knowledge API query SQL', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings(),
        ]);

        $items = $query->get()->map(function ($item) {
            $itemArray = $item->toArray();
            // Apply translations
            if (method_exists($item, 'getTranslatableFields')) {
                foreach ($item->getTranslatableFields() as $field) {
                    $translated = $item->getTranslation($field);
                    if ($translated !== null) {
                        $itemArray[$field] = $translated;
                    }
                }
            }
            // Apply category translations if exists
            if ($item->category && method_exists($item->category, 'getTranslatableFields')) {
                $categoryArray = $item->category->toArray();
                foreach ($item->category->getTranslatableFields() as $field) {
                    $translated = $item->category->getTranslation($field);
                    if ($translated !== null) {
                        $categoryArray[$field] = $translated;
                    }
                }
                $itemArray['category'] = $categoryArray;
            }
            return $itemArray;
        });

        // Debug logging - detailed
        Log::info('Knowledge API response', [
            'count' => $items->count(),
            'source_task_id_filter' => $request->source_task_id,
            'learning_path_id_filter' => $request->learning_path_id,
            'user_id' => $user->id,
            'first_item_id' => $items->first()['id'] ?? null,
            'all_query_params' => $request->all(),
            'query_string' => $request->getQueryString(),
        ]);

        // Also log if no items found but should have items
        if ($items->count() == 0 && $request->has('source_task_id')) {
            // Test query with source_task_id only
            $testQuery = KnowledgeItem::where('user_id', $user->id)
                ->where('is_archived', false);
            if (is_array($request->source_task_id)) {
                $testQuery->whereIn('source_task_id', $request->source_task_id);
            } else {
                $testQuery->where('source_task_id', $request->source_task_id);
            }
            $testCount = $testQuery->count();

            // Test query with learning_path_id if provided
            $testCountByPath = 0;
            if ($request->has('learning_path_id') && $request->learning_path_id) {
                $testQueryByPath = KnowledgeItem::where('user_id', $user->id)
                    ->where('is_archived', false)
                    ->where('learning_path_id', $request->learning_path_id);
                $testCountByPath = $testQueryByPath->count();
            }

            // Check if task has learning_milestone_id and get learning_path_id from it
            $taskLearningPathId = null;
            if (is_array($request->source_task_id)) {
                $firstTaskId = $request->source_task_id[0] ?? null;
            } else {
                $firstTaskId = $request->source_task_id;
            }

            if ($firstTaskId) {
                $task = \App\Models\Task::with('learningMilestone')->find($firstTaskId);
                if ($task && $task->learningMilestone) {
                    $taskLearningPathId = $task->learningMilestone->learning_path_id;
                }
            }

            Log::warning('No knowledge items found', [
                'test_count_by_source_task' => $testCount,
                'test_count_by_learning_path' => $testCountByPath,
                'source_task_id_from_request' => $request->source_task_id,
                'learning_path_id_from_request' => $request->learning_path_id,
                'task_learning_path_id' => $taskLearningPathId,
                'task_id' => $firstTaskId,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $items,
            'message' => 'Knowledge items retrieved successfully'
        ]);
    }

    /**
     * Get a single knowledge item
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $item = KnowledgeItem::where('user_id', $user->id)
            ->with(['category', 'learningPath', 'sourceTask'])
            ->findOrFail($id);

        // Increment view count
        $item->increment('view_count');

        return response()->json([
            'success' => true,
            'data' => $item,
            'message' => 'Knowledge item retrieved successfully'
        ]);
    }

    /**
     * Create a new knowledge item
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'item_type' => 'required|in:note,code_snippet,exercise,resource_link,attachment',
            'category_id' => 'nullable|exists:knowledge_categories,id',
            'content' => 'nullable|string',
            'code_language' => 'nullable|string',
            'url' => 'nullable|url',
            'question' => 'nullable|string',
            'answer' => 'nullable|string',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'tags' => 'nullable|array',
            'learning_path_id' => 'nullable|exists:learning_paths,id',
            'source_task_id' => 'nullable|exists:tasks,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = $user->id;

        // Auto-assign category from learning path if learning_path_id is provided but no category
        if (isset($data['learning_path_id']) && !isset($data['category_id'])) {
            $learningPath = \App\Models\LearningPath::find($data['learning_path_id']);
            if ($learningPath) {
                // Find category with same name as roadmap
                $category = \App\Models\KnowledgeCategory::where('user_id', $user->id)
                    ->where('name', $learningPath->title)
                    ->orderBy('created_at', 'desc') // Get the most recent one
                    ->first();

                if ($category) {
                    $data['category_id'] = $category->id;
                }
            }
        }

        $item = KnowledgeItem::create($data);

        return response()->json([
            'success' => true,
            'data' => $item,
            'message' => 'Knowledge item created successfully'
        ], 201);
    }

    /**
     * Update a knowledge item
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $item = KnowledgeItem::where('user_id', $user->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'item_type' => 'sometimes|required|in:note,code_snippet,exercise,resource_link,attachment',
            'category_id' => 'nullable|exists:knowledge_categories,id',
            'content' => 'nullable|string',
            'code_language' => 'nullable|string',
            'url' => 'nullable|url',
            'question' => 'nullable|string',
            'answer' => 'nullable|string',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'tags' => 'nullable|array',
            'learning_path_id' => 'nullable|exists:learning_paths,id',
            'source_task_id' => 'nullable|exists:tasks,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $item->update($validator->validated());

        return response()->json([
            'success' => true,
            'data' => $item,
            'message' => 'Knowledge item updated successfully'
        ]);
    }

    /**
     * Delete a knowledge item
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $item = KnowledgeItem::where('user_id', $user->id)->findOrFail($id);

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Knowledge item deleted successfully'
        ]);
    }

    /**
     * Toggle favorite status
     */
    public function toggleFavorite(Request $request, $id)
    {
        $user = $request->user();
        $item = KnowledgeItem::where('user_id', $user->id)->findOrFail($id);

        $item->is_favorite = !$item->is_favorite;
        $item->save();

        return response()->json([
            'success' => true,
            'data' => $item,
            'message' => 'Favorite status updated successfully'
        ]);
    }

    /**
     * Toggle archive status
     */
    public function toggleArchive(Request $request, $id)
    {
        $user = $request->user();
        $item = KnowledgeItem::where('user_id', $user->id)->findOrFail($id);

        $item->is_archived = !$item->is_archived;
        $item->save();

        return response()->json([
            'success' => true,
            'data' => $item,
            'message' => 'Archive status updated successfully'
        ]);
    }

    /**
     * Mark item as reviewed
     * Quality: "hard" | "good" | "easy"
     */
    public function markReviewed(Request $request, $id)
    {
        $user = $request->user();
        $item = KnowledgeItem::where('user_id', $user->id)->findOrFail($id);

        // Validate request
        $validator = Validator::make($request->all(), [
            'quality' => 'nullable|string|in:hard,good,easy',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get quality from request (default: "good")
        $quality = $request->input('quality', 'good');

        // Update review count based on quality
        $currentReviewCount = $item->review_count ?? 0;

        if ($quality === 'hard') {
            // Hard: Reset to beginning or decrease review count
            // If review_count > 0, decrease by 1, otherwise keep at 0
            $item->review_count = max(0, $currentReviewCount - 1);
        } elseif ($quality === 'easy') {
            // Easy: Increase review count to advance faster
            $item->review_count = $currentReviewCount + 2;
        } else {
            // Good: Normal progression
            $item->review_count = $currentReviewCount + 1;
        }

        $item->last_reviewed_at = now();

        // Calculate next review date using spaced repetition algorithm with quality
        $interval = $this->calculateReviewInterval($item->review_count, $quality);
        $item->next_review_date = now()->addDays($interval)->toDateString();

        $item->save();

        return response()->json([
            'success' => true,
            'data' => $item,
            'message' => 'Item marked as reviewed'
        ]);
    }

    /**
     * Add item to review list
     * POST /api/knowledge/{id}/add-to-review
     * Sets next_review_date to today or tomorrow so the item appears in review list
     */
    public function addToReview(Request $request, $id)
    {
        $user = $request->user();
        $item = KnowledgeItem::where('user_id', $user->id)->findOrFail($id);

        // If item is not in review list yet, add it
        if (!$item->next_review_date || $item->next_review_date > now()->toDateString()) {
            // Set next_review_date to today so it appears in due review list
            $item->next_review_date = now()->toDateString();

            // If review_count is 0, this is the first time adding to review
            if ($item->review_count == 0) {
                // Keep review_count at 0, just set the date
            }

            $item->save();

            return response()->json([
                'success' => true,
                'data' => $item,
                'message' => 'Item added to review list'
            ]);
        }

        // Item is already in review list
        return response()->json([
            'success' => true,
            'data' => $item,
            'message' => 'Item is already in review list'
        ]);
    }

    /**
     * Get knowledge statistics
     */
    public function stats(Request $request)
    {
        $user = $request->user();

        $stats = [
            'total' => KnowledgeItem::where('user_id', $user->id)->count(),
            'by_type' => [
                'notes' => KnowledgeItem::where('user_id', $user->id)->notes()->count(),
                'code_snippets' => KnowledgeItem::where('user_id', $user->id)->codeSnippets()->count(),
                'exercises' => KnowledgeItem::where('user_id', $user->id)->exercises()->count(),
                'resource_links' => KnowledgeItem::where('user_id', $user->id)->resourceLinks()->count(),
                'attachments' => KnowledgeItem::where('user_id', $user->id)->attachments()->count(),
            ],
            'favorites' => KnowledgeItem::where('user_id', $user->id)->favorites()->count(),
            'archived' => KnowledgeItem::where('user_id', $user->id)->archived()->count(),
            'due_review' => KnowledgeItem::where('user_id', $user->id)->dueForReview()->count(),
            'total_reviews' => KnowledgeItem::where('user_id', $user->id)->sum('review_count'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Knowledge statistics retrieved successfully'
        ]);
    }

    /**
     * Suggest category based on content
     * POST /api/knowledge/suggest-category
     */
    public function suggestCategory(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string',
            'content' => 'required|string',
            'item_type' => 'required|in:note,code_snippet,exercise,resource_link',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $content = $request->input('title', '') . ' ' . $request->input('content');
        $itemType = $request->input('item_type');

        // Auto-detect code language for better suggestions
        $codeLanguage = null;
        if ($itemType === 'code_snippet') {
            $codeLanguage = $this->detectCodeLanguage($content);
        }

        // Get category suggestions
        $suggestions = $this->suggestCategories($user->id, $content, $itemType, $codeLanguage);

        return response()->json([
            'success' => true,
            'data' => [
                'suggested_categories' => $suggestions,
                'detected_language' => $codeLanguage,
                'confidence' => !empty($suggestions) ? $suggestions[0]['confidence'] : 0
            ]
        ]);
    }

    /**
     * Suggest tags based on content
     * POST /api/knowledge/suggest-tags
     */
    public function suggestTags(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'item_type' => 'nullable|in:note,code_snippet,exercise,resource_link',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $content = $request->input('content');
        $itemType = $request->input('item_type', 'note');

        // Auto-detect code language
        $codeLanguage = $this->detectCodeLanguage($content);

        // Generate tags
        $tags = $this->generateTags($content, $itemType, $codeLanguage);

        return response()->json([
            'success' => true,
            'data' => [
                'suggested_tags' => $tags,
                'detected_language' => $codeLanguage
            ]
        ]);
    }

    /**
     * Quick capture - Fast create with auto-categorization
     * POST /api/knowledge/quick-capture
     */
    public function quickCapture(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'item_type' => 'required|in:note,code_snippet,exercise,resource_link',
            'auto_categorize' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $content = $request->input('content');
        $itemType = $request->input('item_type');
        $autoCateg = $request->input('auto_categorize', true);

        // Auto-detect title from content
        $title = $this->extractTitle($content, $itemType);

        // Auto-detect code language for code snippets
        $codeLanguage = null;
        if ($itemType === 'code_snippet') {
            $codeLanguage = $this->detectCodeLanguage($content);
        }

        // Auto-suggest category
        $suggestedCategories = [];
        $categoryId = null;
        if ($autoCateg) {
            $suggestedCategories = $this->suggestCategories($user->id, $content, $itemType, $codeLanguage);
            if (!empty($suggestedCategories)) {
                $categoryId = $suggestedCategories[0]['id'];
            }
        }

        // Auto-generate tags
        $tags = $this->generateTags($content, $itemType, $codeLanguage);

        // Create item
        $item = KnowledgeItem::create([
            'user_id' => $user->id,
            'category_id' => $categoryId ?? $request->input('category_id'),
            'title' => $title,
            'item_type' => $itemType,
            'content' => $content,
            'code_language' => $codeLanguage,
            'tags' => $tags,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'item' => $item,
                'suggested_categories' => $suggestedCategories,
                'auto_detected_language' => $codeLanguage,
                'auto_generated_tags' => $tags,
            ],
            'message' => 'Item captured successfully'
        ], 201);
    }

    /**
     * Bulk operations - tag multiple items
     * PUT /api/knowledge/bulk-tag
     */
    public function bulkTag(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:knowledge_items,id',
            'tags' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            \DB::beginTransaction();

            $updated = 0;
            foreach ($request->input('item_ids') as $itemId) {
                $item = KnowledgeItem::where('user_id', $user->id)->find($itemId);
                if ($item) {
                    $currentTags = $item->tags ?? [];
                    $newTags = array_unique(array_merge($currentTags, $request->input('tags')));
                    $item->tags = $newTags;
                    $item->save();
                    $updated++;
                }
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'data' => ['updated_count' => $updated],
                'message' => "Tagged {$updated} items successfully"
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Bulk tag failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Bulk tag operation failed'
            ], 500);
        }
    }

    /**
     * Bulk move items to new category
     * PUT /api/knowledge/bulk-move
     */
    public function bulkMove(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:knowledge_items,id',
            'category_id' => 'required|exists:knowledge_categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify category belongs to user
        $category = KnowledgeCategory::where('user_id', $user->id)
            ->find($request->input('category_id'));

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found or does not belong to you'
            ], 404);
        }

        try {
            \DB::beginTransaction();

            $updated = KnowledgeItem::where('user_id', $user->id)
                ->whereIn('id', $request->input('item_ids'))
                ->update(['category_id' => $category->id]);

            \DB::commit();

            return response()->json([
                'success' => true,
                'data' => ['updated_count' => $updated],
                'message' => "Moved {$updated} items successfully"
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Bulk move failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Bulk move operation failed'
            ], 500);
        }
    }

    /**
     * Bulk delete items
     * DELETE /api/knowledge/bulk-delete
     */
    public function bulkDelete(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:knowledge_items,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            \DB::beginTransaction();

            $deleted = KnowledgeItem::where('user_id', $user->id)
                ->whereIn('id', $request->input('item_ids'))
                ->delete();

            \DB::commit();

            return response()->json([
                'success' => true,
                'data' => ['deleted_count' => $deleted],
                'message' => "Deleted {$deleted} items successfully"
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Bulk delete failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Bulk delete operation failed'
            ], 500);
        }
    }

    /**
     * Clone/duplicate an item
     * POST /api/knowledge/{id}/clone
     */
    public function clone(Request $request, $id)
    {
        $user = $request->user();
        $original = KnowledgeItem::where('user_id', $user->id)->findOrFail($id);

        $clone = $original->replicate();
        $clone->title = $original->title . ' (Copy)';
        $clone->created_at = now();
        $clone->updated_at = now();
        $clone->save();

        return response()->json([
            'success' => true,
            'data' => $clone,
            'message' => 'Item cloned successfully'
        ], 201);
    }

    /**
     * Get items due for review today
     * GET /api/knowledge/due-review
     */
    public function dueReview(Request $request)
    {
        $user = $request->user();

        $items = KnowledgeItem::where('user_id', $user->id)
            ->dueForReview()
            ->with(['category'])
            ->orderBy('next_review_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items,
            'message' => 'Due review items retrieved successfully'
        ]);
    }

    /**
     * Get related items based on tags and category
     * GET /api/knowledge/{id}/related
     */
    public function related(Request $request, $id)
    {
        $user = $request->user();
        $item = KnowledgeItem::where('user_id', $user->id)->findOrFail($id);

        $relatedItems = KnowledgeItem::where('user_id', $user->id)
            ->where('id', '!=', $id)
            ->where(function($query) use ($item) {
                $query->where('category_id', $item->category_id);

                if ($item->tags && count($item->tags) > 0) {
                    foreach ($item->tags as $tag) {
                        $query->orWhereJsonContains('tags', $tag);
                    }
                }
            })
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $relatedItems,
            'message' => 'Related items retrieved successfully'
        ]);
    }

    // ==================== Helper Methods ====================

    /**
     * Extract title from content
     */
    private function extractTitle($content, $itemType)
    {
        if ($itemType === 'code_snippet') {
            // Extract first line or function name
            $lines = explode("\n", trim($content));
            $firstLine = trim($lines[0]);

            // Try to extract function/class name
            if (preg_match('/(function|class|def|public|private)\s+(\w+)/', $firstLine, $matches)) {
                return $matches[2];
            }

            return substr($firstLine, 0, 50);
        }

        // For notes, use first line or first 50 chars
        $lines = explode("\n", trim($content));
        $firstLine = trim($lines[0]);

        // Remove markdown heading markers
        $firstLine = preg_replace('/^#+\s*/', '', $firstLine);

        return substr($firstLine, 0, 100);
    }

    /**
     * Detect programming language from code
     */
    private function detectCodeLanguage($content)
    {
        // Simple keyword-based detection
        $patterns = [
            'python' => ['def ', 'import ', 'from ', 'class ', '__init__'],
            'javascript' => ['const ', 'let ', 'var ', 'function ', '=>', 'console.log'],
            'java' => ['public class', 'private ', 'protected ', 'System.out'],
            'php' => ['<?php', 'namespace ', 'use ', '::'],
            'go' => ['func ', 'package ', 'import ', 'type '],
            'cpp' => ['#include', 'std::', 'cout', 'cin'],
            'sql' => ['SELECT ', 'INSERT ', 'UPDATE ', 'DELETE ', 'FROM '],
        ];

        foreach ($patterns as $lang => $keywords) {
            foreach ($keywords as $keyword) {
                if (stripos($content, $keyword) !== false) {
                    return $lang;
                }
            }
        }

        return null;
    }

    /**
     * Suggest categories based on content
     */
    private function suggestCategories($userId, $content, $itemType, $codeLanguage)
    {
        $suggestions = [];

        // Get all user categories
        $categories = KnowledgeCategory::where('user_id', $userId)->get();

        foreach ($categories as $category) {
            $score = 0;

            // Match by code language
            if ($codeLanguage && stripos($category->name, $codeLanguage) !== false) {
                $score += 0.9;
            }

            // Match by category name in content
            if (stripos($content, $category->name) !== false) {
                $score += 0.7;
            }

            // Match by keywords
            if (stripos($content, 'leetcode') !== false && stripos($category->name, 'interview') !== false) {
                $score += 0.8;
            }

            if ($score > 0) {
                $suggestions[] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'confidence' => round($score, 2)
                ];
            }
        }

        // Sort by confidence
        usort($suggestions, function($a, $b) {
            return $b['confidence'] <=> $a['confidence'];
        });

        return array_slice($suggestions, 0, 3);
    }

    /**
     * Generate tags based on content
     */
    private function generateTags($content, $itemType, $codeLanguage)
    {
        $tags = [];

        // Add language tag
        if ($codeLanguage) {
            $tags[] = '#' . $codeLanguage;
        }

        // Add type tag
        if ($itemType === 'code_snippet') {
            $tags[] = '#code';
        } elseif ($itemType === 'exercise') {
            $tags[] = '#exercise';
        }

        // Detect difficulty
        if (stripos($content, 'easy') !== false || stripos($content, 'beginner') !== false) {
            $tags[] = '#beginner';
        } elseif (stripos($content, 'hard') !== false || stripos($content, 'advanced') !== false) {
            $tags[] = '#advanced';
        } elseif (stripos($content, 'medium') !== false || stripos($content, 'intermediate') !== false) {
            $tags[] = '#intermediate';
        }

        // Detect common topics
        $topics = [
            'algorithm' => ['algorithm', 'sorting', 'searching', 'tree', 'graph'],
            'interview' => ['leetcode', 'interview', 'hackerrank'],
            'database' => ['database', 'sql', 'mysql', 'postgresql'],
            'web' => ['web', 'html', 'css', 'frontend', 'backend'],
        ];

        foreach ($topics as $tag => $keywords) {
            foreach ($keywords as $keyword) {
                if (stripos($content, $keyword) !== false) {
                    $tags[] = '#' . $tag;
                    break;
                }
            }
        }

        return array_unique($tags);
    }

    /**
     * Calculate review interval based on review count and quality (spaced repetition)
     *
     * @param int $reviewCount
     * @param string $quality "hard" | "good" | "easy"
     * @return int Number of days until next review
     */
    private function calculateReviewInterval($reviewCount, $quality = 'good')
    {
        // Spaced repetition intervals: 1, 3, 7, 14, 30, 60, 120 days
        $intervals = [1, 3, 7, 14, 30, 60, 120];

        // Base index calculation
        $baseIndex = max(0, min($reviewCount - 1, count($intervals) - 1));

        // Adjust interval based on quality
        if ($quality === 'hard') {
            // Hard: Use shorter interval (previous level or minimum)
            $index = max(0, $baseIndex - 1);
        } elseif ($quality === 'easy') {
            // Easy: Use longer interval (next level or maximum)
            $index = min($baseIndex + 1, count($intervals) - 1);
        } else {
            // Good: Use normal interval
            $index = $baseIndex;
        }

        return $intervals[$index];
    }
}
