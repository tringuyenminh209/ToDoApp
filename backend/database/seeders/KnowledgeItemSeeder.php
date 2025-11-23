<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KnowledgeItem;
use App\Models\User;

class KnowledgeItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user
        $user = User::first();

        if (!$user) {
            $this->command->error('No users found. Please create a user first.');
            return;
        }

        // Create sample knowledge items
        $items = [
            [
                'user_id' => $user->id,
                'title' => 'Binary Search Algorithm',
                'item_type' => 'code_snippet',
                'content' => 'def binary_search(arr, target):
    left, right = 0, len(arr) - 1
    while left <= right:
        mid = (left + right) // 2
        if arr[mid] == target:
            return mid
        elif arr[mid] < target:
            left = mid + 1
        else:
            right = mid - 1
    return -1',
                'code_language' => 'python',
                'tags' => ['#python', '#algorithm', '#binary-search'],
                'category_id' => 2, // Python category
                'difficulty' => 'medium',
            ],
            [
                'user_id' => $user->id,
                'title' => 'Fibonacci Recursive',
                'item_type' => 'code_snippet',
                'content' => 'def fibonacci(n):
    if n <= 1:
        return n
    return fibonacci(n-1) + fibonacci(n-2)',
                'code_language' => 'python',
                'tags' => ['#python', '#algorithm', '#recursion'],
                'category_id' => 2, // Python category
                'difficulty' => 'easy',
            ],
            [
                'user_id' => $user->id,
                'title' => 'React Hooks Tutorial',
                'item_type' => 'resource_link',
                'url' => 'https://react.dev/reference/react/hooks',
                'content' => 'Official React documentation for Hooks - useState, useEffect, and more',
                'tags' => ['#react', '#javascript', '#hooks', '#tutorial'],
                'category_id' => 8, // JavaScript category (if exists)
            ],
            [
                'user_id' => $user->id,
                'title' => 'Array Methods Cheatsheet',
                'item_type' => 'note',
                'content' => '# JavaScript Array Methods

## Common Methods:
- map() - Transform each element
- filter() - Select elements that match condition
- reduce() - Reduce array to single value
- forEach() - Loop through elements
- find() - Find first matching element
- some() - Check if any element matches
- every() - Check if all elements match

## Examples:
const arr = [1, 2, 3, 4, 5];
arr.map(x => x * 2); // [2, 4, 6, 8, 10]
arr.filter(x => x > 2); // [3, 4, 5]
arr.reduce((sum, x) => sum + x, 0); // 15',
                'tags' => ['#javascript', '#arrays', '#cheatsheet'],
                'category_id' => 8, // JavaScript category
            ],
            [
                'user_id' => $user->id,
                'title' => 'Two Sum Problem',
                'item_type' => 'exercise',
                'question' => 'Given an array of integers nums and an integer target, return indices of the two numbers such that they add up to target.',
                'answer' => 'def two_sum(nums, target):
    seen = {}
    for i, num in enumerate(nums):
        complement = target - num
        if complement in seen:
            return [seen[complement], i]
        seen[num] = i
    return []

# Time: O(n), Space: O(n)',
                'content' => 'Classic LeetCode problem - use hash map for O(n) solution',
                'difficulty' => 'easy',
                'tags' => ['#algorithm', '#hash-map', '#leetcode', '#interview'],
                'category_id' => 5, // Algorithms category (if exists)
            ],
            [
                'user_id' => $user->id,
                'title' => 'SQL JOIN Types',
                'item_type' => 'note',
                'content' => '# SQL JOIN Types

## INNER JOIN
Returns records that have matching values in both tables
```sql
SELECT * FROM orders
INNER JOIN customers ON orders.customer_id = customers.id;
```

## LEFT JOIN (LEFT OUTER JOIN)
Returns all records from left table, matched records from right
```sql
SELECT * FROM customers
LEFT JOIN orders ON customers.id = orders.customer_id;
```

## RIGHT JOIN (RIGHT OUTER JOIN)
Returns all records from right table, matched records from left
```sql
SELECT * FROM orders
RIGHT JOIN customers ON orders.customer_id = customers.id;
```

## FULL OUTER JOIN
Returns all records when there is a match in either table
```sql
SELECT * FROM customers
FULL OUTER JOIN orders ON customers.id = orders.customer_id;
```',
                'tags' => ['#sql', '#database', '#joins'],
                'category_id' => 1, // Programming Languages or Database category
            ],
            [
                'user_id' => $user->id,
                'title' => 'Git Commands Cheatsheet',
                'item_type' => 'note',
                'content' => '# Essential Git Commands

## Basic
- `git init` - Initialize repository
- `git clone <url>` - Clone repository
- `git status` - Check status
- `git add .` - Stage all changes
- `git commit -m "message"` - Commit changes
- `git push` - Push to remote

## Branching
- `git branch` - List branches
- `git branch <name>` - Create branch
- `git checkout <branch>` - Switch branch
- `git merge <branch>` - Merge branch

## Undo
- `git reset HEAD~1` - Undo last commit
- `git revert <commit>` - Revert commit
- `git checkout -- <file>` - Discard changes

## History
- `git log` - View commit history
- `git diff` - View changes',
                'tags' => ['#git', '#version-control', '#cheatsheet'],
                'category_id' => 1,
                'is_favorite' => true,
            ],
            [
                'user_id' => $user->id,
                'title' => 'Docker Compose Tutorial',
                'item_type' => 'resource_link',
                'url' => 'https://docs.docker.com/compose/',
                'content' => 'Official Docker Compose documentation - Learn how to define and run multi-container applications',
                'tags' => ['#docker', '#devops', '#containers'],
                'category_id' => 1,
            ],
        ];

        foreach ($items as $item) {
            KnowledgeItem::create($item);
        }

        $this->command->info('✅ Created ' . count($items) . ' sample knowledge items');
    }
}
