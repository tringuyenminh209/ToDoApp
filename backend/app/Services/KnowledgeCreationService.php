<?php

namespace App\Services;

use App\Models\KnowledgeCategory;
use App\Models\KnowledgeItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KnowledgeCreationService
{
    /**
     * Create categories and items from AI-parsed data
     *
     * @param array $creationData Data from parseKnowledgeCreationIntent()
     * @param User $user The user creating the knowledge
     * @return array Created categories and items with metadata
     */
    public function createKnowledgeFromIntent(array $creationData, User $user): array
    {
        $createdCategories = [];
        $createdItems = [];
        $errors = [];

        DB::beginTransaction();

        try {
            // Step 1: Create/Get Categories
            $categoryMap = []; // name => category_id

            foreach ($creationData['categories'] ?? [] as $categoryData) {
                $category = $this->createOrGetCategory($categoryData, $user);
                $categoryMap[$categoryData['name']] = $category->id;
                $createdCategories[] = $category;
            }

            // Step 2: Create Knowledge Items
            foreach ($creationData['items'] ?? [] as $itemData) {
                // Resolve category_id from category_name
                $categoryId = null;
                if (!empty($itemData['category_name']) && isset($categoryMap[$itemData['category_name']])) {
                    $categoryId = $categoryMap[$itemData['category_name']];
                } elseif (!empty($itemData['category_id'])) {
                    $categoryId = $itemData['category_id'];
                }

                $item = $this->createKnowledgeItem($itemData, $user, $categoryId);

                if ($item) {
                    $createdItems[] = $item;
                } else {
                    $errors[] = "Failed to create item: {$itemData['title']}";
                }
            }

            // Step 3: Update category item_count
            foreach ($categoryMap as $categoryId) {
                $this->updateCategoryItemCount($categoryId);
            }

            DB::commit();

            Log::info('Knowledge creation successful', [
                'categories' => count($createdCategories),
                'items' => count($createdItems),
                'user_id' => $user->id
            ]);

            return [
                'success' => true,
                'categories' => $createdCategories,
                'items' => $createdItems,
                'errors' => $errors,
                'summary' => [
                    'categories_created' => count($createdCategories),
                    'items_created' => count($createdItems),
                ]
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Knowledge creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'categories' => [],
                'items' => [],
            ];
        }
    }

    /**
     * Create or get existing category
     */
    private function createOrGetCategory(array $categoryData, User $user): KnowledgeCategory
    {
        // Check if category already exists
        $existing = KnowledgeCategory::where('user_id', $user->id)
            ->where('name', $categoryData['name'])
            ->first();

        if ($existing) {
            Log::info('Using existing category', ['name' => $categoryData['name'], 'id' => $existing->id]);
            return $existing;
        }

        // Get next sort_order
        $maxOrder = KnowledgeCategory::where('user_id', $user->id)
            ->max('sort_order') ?? 0;

        // Create new category
        $category = KnowledgeCategory::create([
            'user_id' => $user->id,
            'parent_id' => $categoryData['parent_id'] ?? null,
            'name' => $categoryData['name'],
            'description' => $categoryData['description'] ?? '',
            'color' => $categoryData['color'] ?? $this->getDefaultColor($categoryData['name']),
            'icon' => $categoryData['icon'] ?? $this->getDefaultIcon($categoryData['name']),
            'sort_order' => $maxOrder + 1,
            'item_count' => 0,
        ]);

        Log::info('Created new category', ['name' => $category->name, 'id' => $category->id]);

        return $category;
    }

    /**
     * Create knowledge item
     */
    private function createKnowledgeItem(array $itemData, User $user, ?int $categoryId): ?KnowledgeItem
    {
        try {
            // Validate required fields
            if (empty($itemData['title']) || empty($itemData['item_type'])) {
                Log::warning('Missing required fields for item', ['data' => $itemData]);
                return null;
            }

            // Build item data
            $data = [
                'user_id' => $user->id,
                'category_id' => $categoryId,
                'title' => $itemData['title'],
                'item_type' => $itemData['item_type'],
                'tags' => $itemData['tags'] ?? [],
            ];

            // Add type-specific fields
            switch ($itemData['item_type']) {
                case 'code_snippet':
                    $data['content'] = $itemData['content'] ?? '';
                    $data['code_language'] = $itemData['code_language'] ?? 'plaintext';
                    $data['ai_summary'] = "AI-generated code snippet for {$itemData['title']}";
                    break;

                case 'note':
                    $data['content'] = $itemData['content'] ?? '';
                    $data['ai_summary'] = "AI-generated note about {$itemData['title']}";
                    break;

                case 'exercise':
                    $data['question'] = $itemData['question'] ?? '';
                    $data['answer'] = $itemData['answer'] ?? '';
                    $data['difficulty'] = $itemData['difficulty'] ?? 'medium';
                    $data['ai_summary'] = "AI-generated exercise for {$itemData['title']}";
                    break;

                case 'resource_link':
                    $data['url'] = $itemData['url'] ?? '';
                    $data['content'] = $itemData['content'] ?? ''; // Description
                    $data['ai_summary'] = "AI-generated resource link for {$itemData['title']}";
                    break;

                case 'attachment':
                    // Attachments need file upload, skip for now
                    Log::warning('Attachment type not supported in AI creation', ['title' => $itemData['title']]);
                    return null;
            }

            $item = KnowledgeItem::create($data);

            Log::info('Created knowledge item', [
                'id' => $item->id,
                'title' => $item->title,
                'type' => $item->item_type
            ]);

            return $item;

        } catch (\Exception $e) {
            Log::error('Failed to create knowledge item', [
                'error' => $e->getMessage(),
                'data' => $itemData
            ]);
            return null;
        }
    }

    /**
     * Update category item count
     */
    private function updateCategoryItemCount(int $categoryId): void
    {
        $count = KnowledgeItem::where('category_id', $categoryId)
            ->where('is_archived', false)
            ->count();

        KnowledgeCategory::where('id', $categoryId)->update(['item_count' => $count]);
    }

    /**
     * Get default color for category based on name
     */
    private function getDefaultColor(string $name): string
    {
        $colorMap = [
            'javascript' => '#f7df1e',
            'python' => '#3776ab',
            'java' => '#007396',
            'php' => '#777bb4',
            'react' => '#61dafb',
            'vue' => '#42b883',
            'angular' => '#dd0031',
            'typescript' => '#3178c6',
            'go' => '#00add8',
            'rust' => '#dea584',
            'sql' => '#cc2927',
            'html' => '#e34f26',
            'css' => '#1572b6',
            'docker' => '#2496ed',
            'kubernetes' => '#326ce5',
        ];

        $nameLower = strtolower($name);
        foreach ($colorMap as $key => $color) {
            if (str_contains($nameLower, $key)) {
                return $color;
            }
        }

        // Default colors
        $defaults = ['#6366f1', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#06b6d4'];
        return $defaults[array_rand($defaults)];
    }

    /**
     * Get default icon for category based on name
     */
    private function getDefaultIcon(string $name): string
    {
        $iconMap = [
            'javascript' => 'javascript',
            'python' => 'python',
            'java' => 'java',
            'php' => 'php',
            'react' => 'react',
            'vue' => 'vuejs',
            'angular' => 'angular',
            'database' => 'database',
            'algorithm' => 'chart',
            'data structure' => 'tree',
            'design pattern' => 'pattern',
        ];

        $nameLower = strtolower($name);
        foreach ($iconMap as $key => $icon) {
            if (str_contains($nameLower, $key)) {
                return $icon;
            }
        }

        return 'folder';
    }
}
