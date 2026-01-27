<?php

namespace App\Services;

use App\Models\KnowledgeCategory;
use Illuminate\Support\Facades\Log;

class CategoryService
{
    // Default category constants
    const DEFAULT_PARENT_CATEGORY_NAME = 'プログラミング演習';
    const DEFAULT_PARENT_CATEGORY_ICON = 'folder';
    const DEFAULT_PARENT_CATEGORY_COLOR = '#0FA968';
    const DEFAULT_PARENT_CATEGORY_DESC = 'プログラミング演習用のフォルダ';

    /**
     * Get or create the default parent category for programming exercises.
     * Sets vi/en translations so the category displays in the user's locale.
     *
     * @param int $userId
     * @return KnowledgeCategory
     */
    public function getOrCreateDefaultParent(int $userId): KnowledgeCategory
    {
        $category = KnowledgeCategory::firstOrCreate(
            [
                'user_id' => $userId,
                'name' => self::DEFAULT_PARENT_CATEGORY_NAME,
                'parent_id' => null
            ],
            [
                'description' => self::DEFAULT_PARENT_CATEGORY_DESC,
                'icon' => self::DEFAULT_PARENT_CATEGORY_ICON,
                'color' => self::DEFAULT_PARENT_CATEGORY_COLOR,
                'sort_order' => 0
            ]
        );

        // Ensure vi/en translations exist so UI shows translated name (e.g. "Bài tập lập trình")
        $category->setTranslation('name', 'vi', 'Bài tập lập trình');
        $category->setTranslation('description', 'vi', 'Thư mục bài tập lập trình');
        $category->setTranslation('name', 'en', 'Programming Exercises');
        $category->setTranslation('description', 'en', 'Folder for programming exercises');

        return $category;
    }

    /**
     * Get or create a child category under the default parent
     *
     * @param int $userId
     * @param string $categoryName
     * @param array $options Additional options (description, icon, color)
     * @return KnowledgeCategory
     */
    public function getOrCreateRoadmapCategory(int $userId, string $categoryName, array $options = []): KnowledgeCategory
    {
        // Get or create parent category first
        $parentCategory = $this->getOrCreateDefaultParent($userId);

        // Get or create child category
        return KnowledgeCategory::firstOrCreate(
            [
                'user_id' => $userId,
                'name' => $categoryName,
                'parent_id' => $parentCategory->id
            ],
            [
                'description' => $options['description'] ?? 'ロードマップ: ' . $categoryName,
                'icon' => $options['icon'] ?? 'code',
                'color' => $options['color'] ?? '#3B82F6',
                'sort_order' => $options['sort_order'] ?? 0
            ]
        );
    }

    /**
     * Sync category name when roadmap title changes
     *
     * @param int $userId
     * @param string $oldTitle
     * @param string $newTitle
     * @return bool
     */
    public function syncCategoryWithRoadmapTitle(int $userId, string $oldTitle, string $newTitle): bool
    {
        try {
            // Get parent category
            $parentCategory = KnowledgeCategory::where('user_id', $userId)
                ->where('name', self::DEFAULT_PARENT_CATEGORY_NAME)
                ->whereNull('parent_id')
                ->first();

            if (!$parentCategory) {
                Log::warning('Parent category not found for category sync', [
                    'user_id' => $userId,
                    'old_title' => $oldTitle,
                    'new_title' => $newTitle
                ]);
                return false;
            }

            // Find and update the category
            $updated = KnowledgeCategory::where('user_id', $userId)
                ->where('name', $oldTitle)
                ->where('parent_id', $parentCategory->id)
                ->update([
                    'name' => $newTitle,
                    'description' => 'ロードマップ: ' . $newTitle
                ]);

            if ($updated > 0) {
                Log::info('Category name synced with roadmap title', [
                    'user_id' => $userId,
                    'old_title' => $oldTitle,
                    'new_title' => $newTitle
                ]);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Failed to sync category with roadmap title', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'old_title' => $oldTitle,
                'new_title' => $newTitle
            ]);
            return false;
        }
    }

    /**
     * Delete a category and handle its children and items according to strategy
     *
     * @param int $userId
     * @param int $categoryId
     * @param string $strategy "delete_all", "move_up", or "move_to_uncategorized"
     * @return array Result with success status and message
     */
    public function deleteCategory(int $userId, int $categoryId, string $strategy = 'move_up'): array
    {
        try {
            $category = KnowledgeCategory::where('user_id', $userId)
                ->findOrFail($categoryId);

            // Cannot delete default parent category
            if ($category->name === self::DEFAULT_PARENT_CATEGORY_NAME && $category->parent_id === null) {
                return [
                    'success' => false,
                    'message' => 'デフォルトのフォルダは削除できません'
                ];
            }

            \DB::beginTransaction();

            switch ($strategy) {
                case 'move_up':
                    $this->handleMoveUpStrategy($userId, $category);
                    break;
                case 'delete_all':
                    $this->handleDeleteAllStrategy($userId, $category);
                    break;
                case 'move_to_uncategorized':
                    $this->handleMoveToUncategorizedStrategy($userId, $category);
                    break;
                default:
                    throw new \Exception('Invalid deletion strategy');
            }

            $category->delete();
            \DB::commit();

            return [
                'success' => true,
                'message' => 'カテゴリを削除しました'
            ];
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Failed to delete category', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'category_id' => $categoryId
            ]);

            return [
                'success' => false,
                'message' => 'カテゴリの削除に失敗しました: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Move items and subcategories up to parent category
     */
    private function handleMoveUpStrategy(int $userId, KnowledgeCategory $category)
    {
        $isRootCategory = $category->parent_id === null;

        if ($isRootCategory) {
            // For root categories: items go to "Uncategorized", children become root
            $uncategorizedCategory = $this->getOrCreateUncategorizedCategory($userId);

            // Move items to uncategorized
            \App\Models\KnowledgeItem::where('category_id', $category->id)
                ->update(['category_id' => $uncategorizedCategory->id]);

            // Move children to root (parent_id = NULL)
            KnowledgeCategory::where('parent_id', $category->id)
                ->update(['parent_id' => null]);
        } else {
            // For non-root categories: move everything to parent
            \App\Models\KnowledgeItem::where('category_id', $category->id)
                ->update(['category_id' => $category->parent_id]);

            KnowledgeCategory::where('parent_id', $category->id)
                ->update(['parent_id' => $category->parent_id]);
        }
    }

    /**
     * Delete all items and subcategories recursively
     */
    private function handleDeleteAllStrategy(int $userId, KnowledgeCategory $category)
    {
        // Delete all knowledge items in this category
        \App\Models\KnowledgeItem::where('category_id', $category->id)->delete();

        // Recursively delete all subcategories
        $subcategories = KnowledgeCategory::where('parent_id', $category->id)->get();
        foreach ($subcategories as $subcategory) {
            $this->handleDeleteAllStrategy($userId, $subcategory);
            $subcategory->delete();
        }
    }

    /**
     * Move all items to uncategorized category
     */
    private function handleMoveToUncategorizedStrategy(int $userId, KnowledgeCategory $category)
    {
        $uncategorizedCategory = $this->getOrCreateUncategorizedCategory($userId);

        // Move items to uncategorized
        \App\Models\KnowledgeItem::where('category_id', $category->id)
            ->update(['category_id' => $uncategorizedCategory->id]);

        // Move children to parent or root
        if ($category->parent_id) {
            KnowledgeCategory::where('parent_id', $category->id)
                ->update(['parent_id' => $category->parent_id]);
        } else {
            KnowledgeCategory::where('parent_id', $category->id)
                ->update(['parent_id' => null]);
        }
    }

    /**
     * Get or create "未分類" (Uncategorized) category
     */
    private function getOrCreateUncategorizedCategory(int $userId): KnowledgeCategory
    {
        return KnowledgeCategory::firstOrCreate(
            [
                'user_id' => $userId,
                'name' => '未分類',
                'parent_id' => null
            ],
            [
                'description' => '未分類のアイテム',
                'icon' => 'question',
                'color' => '#9CA3AF',
                'sort_order' => 999
            ]
        );
    }
}
