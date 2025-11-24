<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeCategory;
use App\Models\KnowledgeItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class KnowledgeCategoryController extends Controller
{
    /**
     * Get all categories for authenticated user
     * GET /api/knowledge/categories
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $categories = KnowledgeCategory::where('user_id', $user->id)
            ->with(['parent', 'children'])
            ->ordered()
            ->get();

        // Get item counts for all categories in one query (more efficient)
        $categoryIds = $categories->pluck('id');
        $itemCounts = KnowledgeItem::whereIn('category_id', $categoryIds)
            ->where('is_archived', false)
            ->groupBy('category_id')
            ->selectRaw('category_id, count(*) as count')
            ->pluck('count', 'category_id');

        // Update item_count for each category
        $categories->each(function ($category) use ($itemCounts) {
            $category->item_count = $itemCounts->get($category->id, 0);
        });

        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => 'Categories retrieved successfully'
        ]);
    }

    /**
     * Get category tree (hierarchical structure)
     * GET /api/knowledge/categories/tree
     */
    public function tree(Request $request): JsonResponse
    {
        $user = $request->user();

        // Get all categories for this user
        $allCategories = KnowledgeCategory::where('user_id', $user->id)->get();

        // Get item counts for all categories in one query (more efficient)
        $categoryIds = $allCategories->pluck('id');
        $itemCounts = KnowledgeItem::whereIn('category_id', $categoryIds)
            ->where('is_archived', false)
            ->groupBy('category_id')
            ->selectRaw('category_id, count(*) as count')
            ->pluck('count', 'category_id');

        // Get only root categories (parent_id is null)
        $rootCategories = $allCategories->whereNull('parent_id')
            ->sortBy(function ($category) {
                return [$category->sort_order, $category->name];
            });

        // Load all children recursively with pre-calculated counts
        $tree = $rootCategories->map(function ($category) use ($allCategories, $itemCounts) {
            return $this->buildCategoryTreeWithCounts($category, $allCategories, $itemCounts);
        });

        return response()->json([
            'success' => true,
            'data' => $tree,
            'message' => 'Category tree retrieved successfully'
        ]);
    }

    /**
     * Get a single category with its items
     * GET /api/knowledge/categories/{id}
     */
    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        $category = KnowledgeCategory::where('user_id', $user->id)
            ->with(['parent', 'children', 'knowledgeItems' => function($query) {
                $query->where('is_archived', false)
                      ->orderBy('created_at', 'desc');
            }])
            ->findOrFail($id);

        // Calculate real-time item count (excluding archived items)
        $category->item_count = KnowledgeItem::where('category_id', $category->id)
            ->where('is_archived', false)
            ->count();

        // Get breadcrumb path
        $breadcrumb = $this->getBreadcrumb($category);

        $response = $category->toArray();
        $response['breadcrumb'] = $breadcrumb;

        return response()->json([
            'success' => true,
            'data' => $response,
            'message' => 'Category retrieved successfully'
        ]);
    }

    /**
     * Create a new category
     * POST /api/knowledge/categories
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:knowledge_categories,id',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
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

        // Verify parent belongs to user if parent_id is provided
        if (isset($data['parent_id'])) {
            $parent = KnowledgeCategory::where('user_id', $user->id)
                ->find($data['parent_id']);

            if (!$parent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parent category not found or does not belong to you'
                ], 404);
            }

            // Check for circular reference
            if ($this->wouldCreateCircularReference(null, $data['parent_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot create circular reference in category hierarchy'
                ], 422);
            }
        }

        $category = KnowledgeCategory::create($data);

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Category created successfully'
        ], 201);
    }

    /**
     * Update a category
     * PUT /api/knowledge/categories/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        $category = KnowledgeCategory::where('user_id', $user->id)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:knowledge_categories,id',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // Verify parent belongs to user if parent_id is being changed
        if (isset($data['parent_id'])) {
            if ($data['parent_id'] == $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category cannot be its own parent'
                ], 422);
            }

            if ($data['parent_id']) {
                $parent = KnowledgeCategory::where('user_id', $user->id)
                    ->find($data['parent_id']);

                if (!$parent) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Parent category not found or does not belong to you'
                    ], 404);
                }

                // Check for circular reference
                if ($this->wouldCreateCircularReference($id, $data['parent_id'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot create circular reference in category hierarchy'
                    ], 422);
                }
            }
        }

        $category->update($data);

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Category updated successfully'
        ]);
    }

    /**
     * Delete a category
     * DELETE /api/knowledge/categories/{id}
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        $category = KnowledgeCategory::where('user_id', $user->id)
            ->findOrFail($id);

        // Check if category has children
        if ($category->children()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with subcategories. Please delete or move subcategories first.'
            ], 422);
        }

        // Check if category has items
        if ($category->knowledgeItems()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with items. Please move or delete items first.'
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }

    /**
     * Move category to new parent
     * POST /api/knowledge/categories/{id}/move
     */
    public function move(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        $category = KnowledgeCategory::where('user_id', $user->id)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'new_parent_id' => 'nullable|exists:knowledge_categories,id',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $newParentId = $request->input('new_parent_id');

        // Cannot move to itself
        if ($newParentId == $id) {
            return response()->json([
                'success' => false,
                'message' => 'Category cannot be its own parent'
            ], 422);
        }

        // Verify new parent belongs to user
        if ($newParentId) {
            $newParent = KnowledgeCategory::where('user_id', $user->id)
                ->find($newParentId);

            if (!$newParent) {
                return response()->json([
                    'success' => false,
                    'message' => 'New parent category not found or does not belong to you'
                ], 404);
            }

            // Check for circular reference
            if ($this->wouldCreateCircularReference($id, $newParentId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot create circular reference in category hierarchy'
                ], 422);
            }
        }

        $category->parent_id = $newParentId;

        if ($request->has('sort_order')) {
            $category->sort_order = $request->input('sort_order');
        }

        $category->save();

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Category moved successfully'
        ]);
    }

    /**
     * Update item count for a category
     * POST /api/knowledge/categories/{id}/update-count
     */
    public function updateItemCount(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        $category = KnowledgeCategory::where('user_id', $user->id)
            ->findOrFail($id);

        $count = KnowledgeItem::where('category_id', $id)
            ->where('is_archived', false)
            ->count();

        $category->item_count = $count;
        $category->save();

        return response()->json([
            'success' => true,
            'data' => [
                'category_id' => $id,
                'item_count' => $count
            ],
            'message' => 'Item count updated successfully'
        ]);
    }

    /**
     * Reorder categories (batch update sort_order)
     * POST /api/knowledge/categories/reorder
     */
    public function reorder(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:knowledge_categories,id',
            'categories.*.sort_order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($request->input('categories') as $categoryData) {
                $category = KnowledgeCategory::where('user_id', $user->id)
                    ->find($categoryData['id']);

                if ($category) {
                    $category->sort_order = $categoryData['sort_order'];
                    $category->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Categories reordered successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get category statistics
     * GET /api/knowledge/categories/stats
     */
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        $totalCategories = KnowledgeCategory::where('user_id', $user->id)->count();
        $rootCategories = KnowledgeCategory::where('user_id', $user->id)
            ->rootCategories()
            ->count();

        $categoriesWithItems = KnowledgeCategory::where('user_id', $user->id)
            ->where('item_count', '>', 0)
            ->count();

        $mostUsedCategory = KnowledgeCategory::where('user_id', $user->id)
            ->orderBy('item_count', 'desc')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'total_categories' => $totalCategories,
                'root_categories' => $rootCategories,
                'categories_with_items' => $categoriesWithItems,
                'most_used_category' => $mostUsedCategory ? [
                    'id' => $mostUsedCategory->id,
                    'name' => $mostUsedCategory->name,
                    'item_count' => $mostUsedCategory->item_count
                ] : null
            ],
            'message' => 'Category statistics retrieved successfully'
        ]);
    }

    // ==================== Helper Methods ====================

    /**
     * Build category tree recursively (old method, kept for backward compatibility)
     */
    private function buildCategoryTree(KnowledgeCategory $category): array
    {
        $data = $category->toArray();

        // Calculate real-time item count (excluding archived items)
        $data['item_count'] = KnowledgeItem::where('category_id', $category->id)
            ->where('is_archived', false)
            ->count();

        // Load children
        $children = $category->children()->ordered()->get();

        if ($children->isNotEmpty()) {
            $data['children'] = $children->map(function ($child) {
                return $this->buildCategoryTree($child);
            })->toArray();
        } else {
            $data['children'] = [];
        }

        return $data;
    }

    /**
     * Build category tree recursively with pre-calculated item counts (optimized)
     */
    private function buildCategoryTreeWithCounts(KnowledgeCategory $category, $allCategories, $itemCounts): array
    {
        $data = $category->toArray();

        // Set item count from pre-calculated counts
        $data['item_count'] = $itemCounts->get($category->id, 0);

        // Get children from pre-loaded collection
        $children = $allCategories->where('parent_id', $category->id)
            ->sortBy(function ($child) {
                return [$child->sort_order, $child->name];
            });

        // Build children tree recursively
        if ($children->isNotEmpty()) {
            $data['children'] = $children->map(function ($child) use ($allCategories, $itemCounts) {
                return $this->buildCategoryTreeWithCounts($child, $allCategories, $itemCounts);
            })->values()->toArray();
        } else {
            $data['children'] = [];
        }

        return $data;
    }

    /**
     * Get breadcrumb path for a category
     */
    private function getBreadcrumb(KnowledgeCategory $category): array
    {
        $breadcrumb = [];
        $current = $category;

        while ($current) {
            array_unshift($breadcrumb, [
                'id' => $current->id,
                'name' => $current->name
            ]);
            $current = $current->parent;
        }

        return $breadcrumb;
    }

    /**
     * Check if moving category would create circular reference
     */
    private function wouldCreateCircularReference(?int $categoryId, ?int $newParentId): bool
    {
        if (!$newParentId || !$categoryId) {
            return false;
        }

        $current = KnowledgeCategory::find($newParentId);

        while ($current) {
            if ($current->id == $categoryId) {
                return true;
            }
            $current = $current->parent;
        }

        return false;
    }
}
