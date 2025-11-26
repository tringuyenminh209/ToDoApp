<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __dir__() . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\KnowledgeItem;
use App\Models\KnowledgeCategory;
use App\Services\CategoryService;
use Illuminate\Support\Facades\DB;

echo "\n=== TEST #4: Bulk Operations with Transactions ===\n\n";

$user = User::find(1);
$categoryService = new CategoryService();

// Create test category and items
$testCategory = $categoryService->getOrCreateRoadmapCategory($user->id, 'Bulk Test Category');
$testCategory2 = $categoryService->getOrCreateRoadmapCategory($user->id, 'Bulk Test Category 2');

$item1 = KnowledgeItem::create([
    'user_id' => $user->id,
    'category_id' => $testCategory->id,
    'title' => 'Bulk Test Item 1',
    'item_type' => 'note',
    'content' => 'Content 1',
    'tags' => ['tag1']
]);

$item2 = KnowledgeItem::create([
    'user_id' => $user->id,
    'category_id' => $testCategory->id,
    'title' => 'Bulk Test Item 2',
    'item_type' => 'note',
    'content' => 'Content 2',
    'tags' => ['tag2']
]);

$item3 = KnowledgeItem::create([
    'user_id' => $user->id,
    'category_id' => $testCategory->id,
    'title' => 'Bulk Test Item 3',
    'item_type' => 'note',
    'content' => 'Content 3',
    'tags' => ['tag3']
]);

echo "✅ Created 3 test items (IDs: {$item1->id}, {$item2->id}, {$item3->id})\n\n";

// ===================================
// Test Bulk Tag with Transaction
// ===================================
echo "--- Test Bulk Tag ---\n";

try {
    DB::beginTransaction();

    $itemIds = [$item1->id, $item2->id, $item3->id];
    $newTags = ['bulk-tag-test', 'transaction-test'];

    foreach ($itemIds as $itemId) {
        $item = KnowledgeItem::find($itemId);
        if ($item) {
            $currentTags = $item->tags ?? [];
            $item->tags = array_unique(array_merge($currentTags, $newTags));
            $item->save();
        }
    }

    DB::commit();
    echo "✅ Bulk tag completed with transaction\n";

    // Verify
    $item1->refresh();
    if (in_array('bulk-tag-test', $item1->tags)) {
        echo "✅ Tags applied successfully: " . implode(', ', $item1->tags) . "\n";
    } else {
        echo "❌ Tags not applied\n";
    }

} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Bulk tag failed: {$e->getMessage()}\n";
}

echo "\n";

// ===================================
// Test Bulk Move with Transaction
// ===================================
echo "--- Test Bulk Move ---\n";

try {
    DB::beginTransaction();

    $itemIds = [$item1->id, $item2->id];
    $updated = KnowledgeItem::where('user_id', $user->id)
        ->whereIn('id', $itemIds)
        ->update(['category_id' => $testCategory2->id]);

    DB::commit();
    echo "✅ Bulk move completed: {$updated} items moved\n";

    // Verify
    $item1->refresh();
    if ($item1->category_id == $testCategory2->id) {
        echo "✅ Items moved to new category (ID: {$testCategory2->id})\n";
    } else {
        echo "❌ Items not moved\n";
    }

} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Bulk move failed: {$e->getMessage()}\n";
}

echo "\n";

// ===================================
// Test Transaction Rollback
// ===================================
echo "--- Test Transaction Rollback (Simulated Error) ---\n";

$beforeCount = KnowledgeItem::where('user_id', $user->id)->count();
echo "Items before transaction: {$beforeCount}\n";

try {
    DB::beginTransaction();

    // Create an item
    $tempItem = KnowledgeItem::create([
        'user_id' => $user->id,
        'category_id' => $testCategory->id,
        'title' => 'Should be rolled back',
        'item_type' => 'note',
        'content' => 'This should not exist after rollback',
    ]);

    echo "Created temp item (ID: {$tempItem->id})\n";

    // Simulate error
    throw new \Exception('Simulated error for rollback test');

    DB::commit();

} catch (\Exception $e) {
    DB::rollBack();
    echo "✅ Transaction rolled back: {$e->getMessage()}\n";
}

$afterCount = KnowledgeItem::where('user_id', $user->id)->count();
echo "Items after rollback: {$afterCount}\n";

if ($beforeCount == $afterCount) {
    echo "✅ Rollback successful - no items were added\n";
} else {
    echo "❌ Rollback failed - item count changed\n";
}

echo "\n";

// ===================================
// Test Bulk Delete with Transaction
// ===================================
echo "--- Test Bulk Delete ---\n";

try {
    DB::beginTransaction();

    $itemIds = [$item1->id, $item2->id, $item3->id];
    $deleted = KnowledgeItem::where('user_id', $user->id)
        ->whereIn('id', $itemIds)
        ->delete();

    DB::commit();
    echo "✅ Bulk delete completed: {$deleted} items deleted\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Bulk delete failed: {$e->getMessage()}\n";
}

// Cleanup
$testCategory->delete();
$testCategory2->delete();

echo "\n=== BULK OPERATIONS TEST COMPLETED ===\n";
