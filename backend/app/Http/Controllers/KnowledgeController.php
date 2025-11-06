<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeItem;
use App\Models\KnowledgeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KnowledgeController extends Controller
{
    /**
     * Get all knowledge items for authenticated user
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = KnowledgeItem::where('user_id', $user->id)
            ->with(['category', 'learningPath', 'sourceTask']);

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
        if ($request->has('learning_path_id')) {
            $query->where('learning_path_id', $request->learning_path_id);
        }

        // Filter by source task
        if ($request->has('source_task_id')) {
            $query->where('source_task_id', $request->source_task_id);
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

        $items = $query->get();

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
     */
    public function markReviewed(Request $request, $id)
    {
        $user = $request->user();
        $item = KnowledgeItem::where('user_id', $user->id)->findOrFail($id);

        $item->review_count = ($item->review_count ?? 0) + 1;
        $item->last_reviewed_at = now();

        // Calculate next review date using spaced repetition algorithm
        $interval = $this->calculateReviewInterval($item->review_count);
        $item->next_review_date = now()->addDays($interval)->toDateString();

        $item->save();

        return response()->json([
            'success' => true,
            'data' => $item,
            'message' => 'Item marked as reviewed'
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
     * Calculate review interval based on review count (spaced repetition)
     *
     * @param int $reviewCount
     * @return int Number of days until next review
     */
    private function calculateReviewInterval($reviewCount)
    {
        // Spaced repetition intervals: 1, 3, 7, 14, 30, 60, 120 days
        $intervals = [1, 3, 7, 14, 30, 60, 120];

        $index = min($reviewCount - 1, count($intervals) - 1);
        return $intervals[$index];
    }
}
