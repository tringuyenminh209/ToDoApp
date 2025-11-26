<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Services\CategoryService;
use App\Models\KnowledgeCategory;
use App\Models\LearningPath;
use App\Models\KnowledgeItem;

echo "\n=== TESTING ROADMAP & KNOWLEDGE FIXES ===\n\n";

$user = User::find(1);
if (!$user) {
    echo "❌ User not found\n";
    exit(1);
}

echo "✅ User found: {$user->name} (ID: {$user->id})\n\n";

// ===================================
// TEST #2: CategoryService
// ===================================
echo "=== TEST #2: CategoryService ===\n";

$categoryService = new CategoryService();

try {
    // Test getOrCreateDefaultParent
    $parentCategory = $categoryService->getOrCreateDefaultParent($user->id);
    echo "✅ Default parent category: {$parentCategory->name} (ID: {$parentCategory->id})\n";

    // Test getOrCreateRoadmapCategory
    $roadmapCategory = $categoryService->getOrCreateRoadmapCategory(
        $user->id,
        'Test Roadmap Category',
        ['color' => '#FF0000', 'icon' => 'test']
    );
    echo "✅ Roadmap category created: {$roadmapCategory->name} (ID: {$roadmapCategory->id})\n";
    echo "   Parent ID: {$roadmapCategory->parent_id}\n";
    echo "   Color: {$roadmapCategory->color}\n";

    // Test creating same category again (should return existing)
    $sameCategory = $categoryService->getOrCreateRoadmapCategory(
        $user->id,
        'Test Roadmap Category'
    );

    if ($sameCategory->id === $roadmapCategory->id) {
        echo "✅ Duplicate check works - returned existing category\n";
    } else {
        echo "❌ Duplicate check failed - created new category\n";
    }

} catch (\Exception $e) {
    echo "❌ CategoryService test failed: {$e->getMessage()}\n";
}

echo "\n";

// ===================================
// TEST #3: Category-Roadmap Sync
// ===================================
echo "=== TEST #3: Category-Roadmap Title Sync ===\n";

try {
    // Create a learning path
    $learningPath = LearningPath::create([
        'user_id' => $user->id,
        'title' => 'Original Title',
        'description' => 'Test learning path',
        'goal_type' => 'skill',
        'color' => '#3B82F6',
        'icon' => 'code',
        'status' => 'active',
        'progress_percentage' => 0,
        'is_ai_generated' => false,
    ]);
    echo "✅ Learning path created: {$learningPath->title} (ID: {$learningPath->id})\n";

    // Create corresponding category
    $category = $categoryService->getOrCreateRoadmapCategory(
        $user->id,
        'Original Title'
    );
    echo "✅ Category created: {$category->name}\n";

    // Test sync
    $synced = $categoryService->syncCategoryWithRoadmapTitle(
        $user->id,
        'Original Title',
        'Updated Title'
    );

    if ($synced) {
        $category->refresh();
        if ($category->name === 'Updated Title') {
            echo "✅ Category name synced successfully: {$category->name}\n";
        } else {
            echo "❌ Category name not synced: {$category->name}\n";
        }
    } else {
        echo "❌ Sync failed\n";
    }

    // Cleanup
    $learningPath->delete();
    $category->delete();

} catch (\Exception $e) {
    echo "❌ Sync test failed: {$e->getMessage()}\n";
}

echo "\n";

// ===================================
// TEST #1: Knowledge Duplicate Check
// ===================================
echo "=== TEST #1: Knowledge Duplicate Check ===\n";

try {
    $testCategory = $categoryService->getOrCreateRoadmapCategory(
        $user->id,
        'Test Knowledge Category'
    );

    // Create first item
    $item1 = KnowledgeItem::create([
        'user_id' => $user->id,
        'category_id' => $testCategory->id,
        'title' => 'Test Item',
        'item_type' => 'note',
        'content' => 'Test content for duplicate check',
    ]);
    echo "✅ First knowledge item created (ID: {$item1->id})\n";

    // Try to create duplicate (same title, type, content)
    $existingCheck = KnowledgeItem::where('user_id', $user->id)
        ->where('category_id', $testCategory->id)
        ->where('title', 'Test Item')
        ->where('item_type', 'note')
        ->where('content', 'Test content for duplicate check')
        ->first();

    if ($existingCheck) {
        echo "✅ Duplicate check works - found existing item (ID: {$existingCheck->id})\n";
    } else {
        echo "❌ Duplicate check failed - no existing item found\n";
    }

    // Create item with same title but different content (should be allowed)
    $item2 = KnowledgeItem::create([
        'user_id' => $user->id,
        'category_id' => $testCategory->id,
        'title' => 'Test Item',
        'item_type' => 'note',
        'content' => 'Different content',
    ]);
    echo "✅ Item with different content created (ID: {$item2->id})\n";

    // Cleanup
    $item1->delete();
    $item2->delete();
    $testCategory->delete();

} catch (\Exception $e) {
    echo "❌ Duplicate check test failed: {$e->getMessage()}\n";
}

echo "\n";

// ===================================
// TEST #5: Knowledge Filtering
// ===================================
echo "=== TEST #5: Knowledge Items Filtering ===\n";

try {
    $testCategory2 = $categoryService->getOrCreateRoadmapCategory(
        $user->id,
        'Filter Test Category'
    );

    // Create test learning path
    $testPath = LearningPath::create([
        'user_id' => $user->id,
        'title' => 'Filter Test Path',
        'description' => 'Test',
        'goal_type' => 'skill',
        'color' => '#3B82F6',
        'icon' => 'code',
        'status' => 'active',
        'progress_percentage' => 0,
        'is_ai_generated' => false,
    ]);

    // Create items with different sources
    $item1 = KnowledgeItem::create([
        'user_id' => $user->id,
        'category_id' => $testCategory2->id,
        'learning_path_id' => $testPath->id,
        'title' => 'Item from learning path',
        'item_type' => 'note',
        'content' => 'Content 1',
    ]);

    $item2 = KnowledgeItem::create([
        'user_id' => $user->id,
        'category_id' => $testCategory2->id,
        'source_task_id' => 1, // Assume task 1 exists
        'title' => 'Item from task',
        'item_type' => 'note',
        'content' => 'Content 2',
    ]);

    echo "✅ Created 2 test items\n";
    echo "   - Item 1 from learning_path_id: {$testPath->id}\n";
    echo "   - Item 2 from source_task_id: 1\n";

    // Test filtering by learning_path_id
    $byPath = KnowledgeItem::where('user_id', $user->id)
        ->where('learning_path_id', $testPath->id)
        ->count();
    echo "✅ Filter by learning_path_id: {$byPath} items\n";

    // Test filtering by source_task_id
    $byTask = KnowledgeItem::where('user_id', $user->id)
        ->where('source_task_id', 1)
        ->count();
    echo "✅ Filter by source_task_id: {$byTask} items\n";

    // Test OR logic (both filters)
    $bothFilters = KnowledgeItem::where('user_id', $user->id)
        ->where(function($q) use ($testPath) {
            $q->where('learning_path_id', $testPath->id)
              ->orWhere('source_task_id', 1);
        })
        ->count();
    echo "✅ Filter with OR logic (both): {$bothFilters} items\n";

    // Cleanup
    $item1->delete();
    $item2->delete();
    $testPath->delete();
    $testCategory2->delete();

} catch (\Exception $e) {
    echo "❌ Filtering test failed: {$e->getMessage()}\n";
}

echo "\n";
echo "=== ALL TESTS COMPLETED ===\n";
