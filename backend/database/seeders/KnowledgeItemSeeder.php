<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KnowledgeItem;
use App\Models\KnowledgeCategory;
use App\Models\User;
use Carbon\Carbon;

class KnowledgeItemSeeder extends Seeder
{
    private int $userId;
    private array $categoryCache = [];

    /**
     * データベースのシードを実行します。
     * 82-100個の多様なKnowledge Itemsを作成します
     */
    public function run(): void
    {
        // 最初のユーザーを取得
        $user = User::first();

        if (!$user) {
            $this->command->error('ユーザーが見つかりません。最初にユーザーを作成してください。');
            return;
        }

        $this->userId = $user->id;
        $this->command->info("ユーザー {$user->email} のKnowledge Itemsを作成中...");

        // すべてのアイテムを収集
        $items = array_merge(
            $this->getPythonItems(),
            $this->getJavaItems(),
            $this->getJavaScriptItems(),
            $this->getAlgorithmItems(),
            $this->getInterviewPrepItems(),
            $this->getDatabaseItems(),
            $this->getResourceLinks()
        );

        // 状態データを追加（favorites, archived, review dates）
        $items = $this->addStateData($items);

        // アイテムを作成
        $created = 0;
        foreach ($items as $item) {
            KnowledgeItem::create($item);
            $created++;
        }

        $this->command->info("✅ {$created}個のKnowledge Itemsを作成しました！");
    }

    /**
     * カテゴリ名からIDを取得（キャッシュ付き）
     */
    private function getCategoryId(string $name, ?string $parentName = null): ?int
    {
        $cacheKey = $parentName ? "{$parentName} > {$name}" : $name;

        if (isset($this->categoryCache[$cacheKey])) {
            return $this->categoryCache[$cacheKey];
        }

        $query = KnowledgeCategory::where('user_id', $this->userId)
            ->where('name', $name);

        if ($parentName) {
            $parent = KnowledgeCategory::where('user_id', $this->userId)
                ->where('name', $parentName)
                ->first();

            if ($parent) {
                $query->where('parent_id', $parent->id);
            }
        }

        $category = $query->first();
        $id = $category?->id;

        if ($id) {
            $this->categoryCache[$cacheKey] = $id;
        }

        return $id;
    }

    /**
     * 状態データを追加（favorites, archived, review dates）
     */
    private function addStateData(array $items): array
    {
        $favoriteCount = 0;
        $archivedCount = 0;
        $dueReviewCount = 0;

        foreach ($items as &$item) {
            // Favorites: 10-15 items
            if ($favoriteCount < 15 && rand(1, 10) <= 2) {
                $item['is_favorite'] = true;
                $favoriteCount++;
            }

            // Archived: 5 items
            if ($archivedCount < 5 && rand(1, 20) <= 1) {
                $item['is_archived'] = true;
                $archivedCount++;
            }

            // Review data
            $reviewCount = rand(0, 7);
            $item['review_count'] = $reviewCount;
            $item['view_count'] = rand(0, 50);
            $item['retention_score'] = rand(1, 5);

            // Due for review: 5 items today
            if ($dueReviewCount < 5 && rand(1, 20) <= 1) {
                $item['next_review_date'] = Carbon::today();
                $item['last_reviewed_at'] = Carbon::today()->subDays(rand(1, 7));
            } else {
                $item['next_review_date'] = Carbon::today()->addDays(rand(-5, 30));
                $item['last_reviewed_at'] = $reviewCount > 0
                    ? Carbon::today()->subDays(rand(1, 30))
                    : null;
            }

            // Created/Updated dates
            $item['created_at'] = Carbon::now()->subDays(rand(0, 30));
            $item['updated_at'] = Carbon::now()->subDays(rand(0, 15));
        }

        return $items;
    }

    /**
     * Python Items (15 items)
     */
    private function getPythonItems(): array
    {
        $userId = $this->userId;
        $pythonBasics = $this->getCategoryId('基礎', 'Python');
        $pythonDataStructures = $this->getCategoryId('データ構造', 'Python');
        $pythonInterview = $this->getCategoryId('面接問題', 'Python');
        $algorithms = $this->getCategoryId('ソート & 検索', 'アルゴリズム');

        return [
            // Code Snippets (8)
            [
                'user_id' => $userId,
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
    return -1

# Time: O(log n), Space: O(1)',
                'code_language' => 'python',
                'tags' => ['#python', '#algorithm', '#binary-search', '#searching'],
                'category_id' => $algorithms,
                'difficulty' => 'medium',
            ],
            [
                'user_id' => $userId,
                'title' => 'Quick Sort Implementation',
                'item_type' => 'code_snippet',
                'content' => 'def quicksort(arr):
    if len(arr) <= 1:
        return arr
    pivot = arr[len(arr) // 2]
    left = [x for x in arr if x < pivot]
    middle = [x for x in arr if x == pivot]
    right = [x for x in arr if x > pivot]
    return quicksort(left) + middle + quicksort(right)

# Time: O(n log n) average, O(n²) worst case',
                'code_language' => 'python',
                'tags' => ['#python', '#algorithm', '#sorting', '#quicksort'],
                'category_id' => $algorithms,
                'difficulty' => 'medium',
            ],
            [
                'user_id' => $userId,
                'title' => 'Python Decorators',
                'item_type' => 'code_snippet',
                'content' => 'def timer_decorator(func):
    def wrapper(*args, **kwargs):
        import time
        start = time.time()
        result = func(*args, **kwargs)
        end = time.time()
        print(f"{func.__name__} took {end - start:.4f} seconds")
        return result
    return wrapper

@timer_decorator
def my_function():
    # Your code here
    pass',
                'code_language' => 'python',
                'tags' => ['#python', '#decorators', '#advanced', '#code'],
                'category_id' => $pythonBasics,
                'difficulty' => 'hard',
            ],
            [
                'user_id' => $userId,
                'title' => 'List Comprehension Examples',
                'item_type' => 'code_snippet',
                'content' => '# Basic list comprehension
squares = [x**2 for x in range(10)]

# With condition
evens = [x for x in range(20) if x % 2 == 0]

# Nested
matrix = [[i*j for j in range(3)] for i in range(3)]

# Dictionary comprehension
squares_dict = {x: x**2 for x in range(10)}',
                'code_language' => 'python',
                'tags' => ['#python', '#list-comprehension', '#beginner', '#code'],
                'category_id' => $pythonBasics,
                'difficulty' => 'easy',
            ],
            [
                'user_id' => $userId,
                'title' => 'Generator Functions',
                'item_type' => 'code_snippet',
                'content' => 'def fibonacci_generator():
    a, b = 0, 1
    while True:
        yield a
        a, b = b, a + b

# Usage
fib = fibonacci_generator()
for i in range(10):
    print(next(fib))

# Generator expression
squares = (x**2 for x in range(10))',
                'code_language' => 'python',
                'tags' => ['#python', '#generators', '#intermediate', '#code'],
                'category_id' => $pythonBasics,
                'difficulty' => 'medium',
            ],
            [
                'user_id' => $userId,
                'title' => 'Context Managers',
                'item_type' => 'code_snippet',
                'content' => 'from contextlib import contextmanager

@contextmanager
def file_handler(filename, mode):
    file = open(filename, mode)
    try:
        yield file
    finally:
        file.close()

# Usage
with file_handler("test.txt", "w") as f:
    f.write("Hello, World!")',
                'code_language' => 'python',
                'tags' => ['#python', '#context-managers', '#intermediate', '#code'],
                'category_id' => $pythonBasics,
                'difficulty' => 'medium',
            ],
            [
                'user_id' => $userId,
                'title' => 'Async/Await Example',
                'item_type' => 'code_snippet',
                'content' => 'import asyncio

async def fetch_data(url):
    # Simulate API call
    await asyncio.sleep(1)
    return f"Data from {url}"

async def main():
    urls = ["url1", "url2", "url3"]
    results = await asyncio.gather(*[fetch_data(url) for url in urls])
    return results

# Run
asyncio.run(main())',
                'code_language' => 'python',
                'tags' => ['#python', '#async', '#await', '#advanced', '#code'],
                'category_id' => $pythonBasics,
                'difficulty' => 'hard',
            ],
            [
                'user_id' => $userId,
                'title' => 'Class Example with Properties',
                'item_type' => 'code_snippet',
                'content' => 'class Person:
    def __init__(self, first_name, last_name):
        self._first_name = first_name
        self._last_name = last_name

    @property
    def full_name(self):
        return f"{self._first_name} {self._last_name}"

    @full_name.setter
    def full_name(self, name):
        self._first_name, self._last_name = name.split()

# Usage
person = Person("John", "Doe")
print(person.full_name)  # John Doe',
                'code_language' => 'python',
                'tags' => ['#python', '#oop', '#properties', '#intermediate', '#code'],
                'category_id' => $pythonBasics,
                'difficulty' => 'medium',
            ],
            // Notes (4)
            [
                'user_id' => $userId,
                'title' => 'Python List Comprehension ガイド',
                'item_type' => 'note',
                'content' => '# Python List Comprehension

## 基本構文
```python
[expression for item in iterable]
```

## 条件付き
```python
[expression for item in iterable if condition]
```

## ネスト
```python
[[i*j for j in range(3)] for i in range(3)]
```

## メリット
- 読みやすい
- 高速
- メモリ効率が良い',
                'tags' => ['#python', '#list-comprehension', '#learning-notes', '#beginner'],
                'category_id' => $pythonBasics,
            ],
            [
                'user_id' => $userId,
                'title' => 'Python Decorators 解説',
                'item_type' => 'note',
                'content' => '# Python Decorators

Decoratorは関数をラップして機能を追加するパターンです。

## 基本的な使い方
```python
@decorator
def my_function():
    pass
```

## 実用例
- タイマー
- ログ記録
- 認証チェック
- キャッシング',
                'tags' => ['#python', '#decorators', '#learning-notes', '#advanced'],
                'category_id' => $pythonBasics,
            ],
            [
                'user_id' => $userId,
                'title' => 'Python Generators の仕組み',
                'item_type' => 'note',
                'content' => '# Python Generators

Generatorはメモリ効率的なイテレータです。

## 特徴
- `yield`キーワードを使用
- 遅延評価（lazy evaluation）
- メモリ効率が良い

## 使用例
- 大きなデータセットの処理
- 無限シーケンス
- パイプライン処理',
                'tags' => ['#python', '#generators', '#learning-notes', '#intermediate'],
                'category_id' => $pythonBasics,
            ],
            [
                'user_id' => $userId,
                'title' => 'Python Virtual Environment セットアップ',
                'item_type' => 'note',
                'content' => '# Python Virtual Environment

## 作成
```bash
python -m venv venv
```

## 有効化
```bash
# Windows
venv\Scripts\activate

# Linux/Mac
source venv/bin/activate
```

## パッケージインストール
```bash
pip install package_name
```

## requirements.txt
```bash
pip freeze > requirements.txt
pip install -r requirements.txt
```',
                'tags' => ['#python', '#virtual-env', '#learning-notes', '#beginner'],
                'category_id' => $pythonBasics,
            ],
            // Exercises (3)
            [
                'user_id' => $userId,
                'title' => 'Fibonacci Sequence',
                'item_type' => 'exercise',
                'question' => 'フィボナッチ数列のn番目の数を返す関数を実装してください。\n\n例: fibonacci(5) → 5',
                'answer' => 'def fibonacci(n):
    if n <= 1:
        return n
    return fibonacci(n-1) + fibonacci(n-2)

# 最適化版（メモ化）
def fibonacci_memo(n, memo={}):
    if n in memo:
        return memo[n]
    if n <= 1:
        return n
    memo[n] = fibonacci_memo(n-1, memo) + fibonacci_memo(n-2, memo)
    return memo[n]',
                'content' => '再帰とメモ化の両方のアプローチを学ぶ',
                'difficulty' => 'easy',
                'tags' => ['#python', '#algorithm', '#recursion', '#exercise', '#beginner'],
                'category_id' => $pythonInterview,
            ],
            [
                'user_id' => $userId,
                'title' => 'Palindrome Check',
                'item_type' => 'exercise',
                'question' => '文字列が回文（palindrome）かどうかを判定する関数を実装してください。\n\n例: "racecar" → True, "hello" → False',
                'answer' => 'def is_palindrome(s):
    s = s.lower().replace(" ", "")
    return s == s[::-1]

# 再帰版
def is_palindrome_recursive(s):
    if len(s) <= 1:
        return True
    if s[0] != s[-1]:
        return False
    return is_palindrome_recursive(s[1:-1])',
                'content' => '文字列操作と再帰の練習',
                'difficulty' => 'easy',
                'tags' => ['#python', '#string', '#exercise', '#beginner'],
                'category_id' => $pythonInterview,
            ],
            [
                'user_id' => $userId,
                'title' => 'Two Sum Problem',
                'item_type' => 'exercise',
                'question' => '配列内の2つの数の合計がtargetになるインデックスを返してください。\n\n例: nums = [2,7,11,15], target = 9 → [0,1]',
                'answer' => 'def two_sum(nums, target):
    seen = {}
    for i, num in enumerate(nums):
        complement = target - num
        if complement in seen:
            return [seen[complement], i]
        seen[num] = i
    return []

# Time: O(n), Space: O(n)',
                'content' => 'LeetCode Easy問題 - ハッシュマップを使用',
                'difficulty' => 'easy',
                'tags' => ['#python', '#algorithm', '#hash-map', '#leetcode', '#interview-prep'],
                'category_id' => $pythonInterview,
            ],
        ];
    }

    /**
     * Java Items (12 items)
     */
    private function getJavaItems(): array
    {
        $userId = $this->userId;
        $javaCore = $this->getCategoryId('コアJava', 'Java');
        $javaPatterns = $this->getCategoryId('デザインパターン', 'Java');
        $algorithms = $this->getCategoryId('ソート & 検索', 'アルゴリズム');

        return [
            // Code Snippets (6)
            [
                'user_id' => $userId,
                'title' => 'Singleton Pattern',
                'item_type' => 'code_snippet',
                'content' => 'public class Singleton {
    private static Singleton instance;

    private Singleton() {}

    public static Singleton getInstance() {
        if (instance == null) {
            instance = new Singleton();
        }
        return instance;
    }
}',
                'code_language' => 'java',
                'tags' => ['#java', '#design-pattern', '#singleton', '#intermediate'],
                'category_id' => $javaPatterns,
                'difficulty' => 'medium',
            ],
            [
                'user_id' => $userId,
                'title' => 'Factory Pattern',
                'item_type' => 'code_snippet',
                'content' => 'interface Shape {
    void draw();
}

class Circle implements Shape {
    public void draw() {
        System.out.println("Drawing Circle");
    }
}

class ShapeFactory {
    public Shape getShape(String shapeType) {
        if (shapeType.equalsIgnoreCase("CIRCLE")) {
            return new Circle();
        }
        return null;
    }
}',
                'code_language' => 'java',
                'tags' => ['#java', '#design-pattern', '#factory', '#intermediate'],
                'category_id' => $javaPatterns,
                'difficulty' => 'medium',
            ],
            [
                'user_id' => $userId,
                'title' => 'Java Collections Example',
                'item_type' => 'code_snippet',
                'content' => 'import java.util.*;

List<String> list = new ArrayList<>();
list.add("Apple");

Map<String, Integer> map = new HashMap<>();
map.put("Apple", 1);

Set<String> set = new HashSet<>();
set.add("Apple");',
                'code_language' => 'java',
                'tags' => ['#java', '#collections', '#beginner', '#code'],
                'category_id' => $javaCore,
                'difficulty' => 'easy',
            ],
            [
                'user_id' => $userId,
                'title' => 'Java Streams API',
                'item_type' => 'code_snippet',
                'content' => 'import java.util.stream.Collectors;

List<Integer> numbers = Arrays.asList(1, 2, 3, 4, 5);

List<Integer> squares = numbers.stream()
    .filter(n -> n % 2 == 0)
    .map(n -> n * n)
    .collect(Collectors.toList());',
                'code_language' => 'java',
                'tags' => ['#java', '#streams', '#functional', '#intermediate'],
                'category_id' => $javaCore,
                'difficulty' => 'medium',
            ],
            [
                'user_id' => $userId,
                'title' => 'Lambda Expressions',
                'item_type' => 'code_snippet',
                'content' => '// Lambda expression
Collections.sort(list, (s1, s2) -> s1.compareTo(s2));

// Method reference
list.forEach(System.out::println);',
                'code_language' => 'java',
                'tags' => ['#java', '#lambda', '#functional', '#intermediate'],
                'category_id' => $javaCore,
                'difficulty' => 'medium',
            ],
            [
                'user_id' => $userId,
                'title' => 'Exception Handling',
                'item_type' => 'code_snippet',
                'content' => 'try {
    int result = 10 / 0;
} catch (ArithmeticException e) {
    System.out.println("Error: " + e.getMessage());
} finally {
    System.out.println("Always executes");
}',
                'code_language' => 'java',
                'tags' => ['#java', '#exceptions', '#beginner', '#code'],
                'category_id' => $javaCore,
                'difficulty' => 'easy',
            ],
            // Notes (4)
            [
                'user_id' => $userId,
                'title' => 'Java Collections Framework',
                'item_type' => 'note',
                'content' => '# Java Collections Framework

## 主要インターフェース
- **List**: ArrayList, LinkedList
- **Set**: HashSet, TreeSet
- **Map**: HashMap, TreeMap',
                'tags' => ['#java', '#collections', '#learning-notes', '#beginner'],
                'category_id' => $javaCore,
            ],
            [
                'user_id' => $userId,
                'title' => 'Java Streams API ガイド',
                'item_type' => 'note',
                'content' => '# Java Streams API

## 主要操作
- filter(): 条件でフィルタ
- map(): 変換
- reduce(): 集約
- collect(): 結果を収集',
                'tags' => ['#java', '#streams', '#learning-notes', '#intermediate'],
                'category_id' => $javaCore,
            ],
            [
                'user_id' => $userId,
                'title' => 'OOP Principles in Java',
                'item_type' => 'note',
                'content' => '# OOP Principles

1. Encapsulation
2. Inheritance
3. Polymorphism
4. Abstraction',
                'tags' => ['#java', '#oop', '#learning-notes', '#beginner'],
                'category_id' => $javaCore,
            ],
            [
                'user_id' => $userId,
                'title' => 'Java Annotations',
                'item_type' => 'note',
                'content' => '# Java Annotations

- @Override
- @Deprecated
- @SuppressWarnings',
                'tags' => ['#java', '#annotations', '#learning-notes', '#intermediate'],
                'category_id' => $javaCore,
            ],
            // Exercises (2)
            [
                'user_id' => $userId,
                'title' => 'Valid Parentheses',
                'item_type' => 'exercise',
                'question' => '括弧の組み合わせが有効かどうかを判定してください。',
                'answer' => 'public boolean isValid(String s) {
    Stack<Character> stack = new Stack<>();
    for (char c : s.toCharArray()) {
        if (c == \'(\' || c == \'[\' || c == \'{\') {
            stack.push(c);
        } else {
            if (stack.isEmpty()) return false;
            char top = stack.pop();
            if ((c == \')\' && top != \'(\') ||
                (c == \']\' && top != \'[\') ||
                (c == \'}\' && top != \'{\')) {
                return false;
            }
        }
    }
    return stack.isEmpty();
}',
                'content' => 'Stackを使用した括弧の検証',
                'difficulty' => 'easy',
                'tags' => ['#java', '#algorithm', '#stack', '#exercise', '#beginner'],
                'category_id' => $algorithms,
            ],
            [
                'user_id' => $userId,
                'title' => 'Reverse Linked List',
                'item_type' => 'exercise',
                'question' => '単方向連結リストを反転してください。',
                'answer' => 'public ListNode reverseList(ListNode head) {
    ListNode prev = null;
    ListNode curr = head;
    while (curr != null) {
        ListNode next = curr.next;
        curr.next = prev;
        prev = curr;
        curr = next;
    }
    return prev;
}',
                'content' => '連結リストの反転アルゴリズム',
                'difficulty' => 'medium',
                'tags' => ['#java', '#algorithm', '#linked-list', '#exercise', '#intermediate'],
                'category_id' => $algorithms,
            ],
        ];
    }

    /**
     * JavaScript Items (12 items)
     */
    private function getJavaScriptItems(): array
    {
        $userId = $this->userId;
        $jsEs6 = $this->getCategoryId('ES6+機能', 'JavaScript');
        $react = $this->getCategoryId('React.js', 'JavaScript');
        $nodejs = $this->getCategoryId('Node.js', 'JavaScript');

        return [
            // Code Snippets (6)
            [
                'user_id' => $userId,
                'title' => 'Promises and Async/Await',
                'item_type' => 'code_snippet',
                'content' => '// Promise
function fetchData() {
    return fetch(\'https://api.example.com/data\')
        .then(response => response.json())
        .then(data => console.log(data))
        .catch(error => console.error(error));
}

// Async/Await
async function fetchDataAsync() {
    try {
        const response = await fetch(\'https://api.example.com/data\');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error(error);
    }
}',
                'code_language' => 'javascript',
                'tags' => ['#javascript', '#promises', '#async', '#intermediate'],
                'category_id' => $jsEs6,
                'difficulty' => 'medium',
            ],
            [
                'user_id' => $userId,
                'title' => 'Closures Example',
                'item_type' => 'code_snippet',
                'content' => 'function outerFunction(x) {
    return function innerFunction(y) {
        return x + y;
    };
}

const addFive = outerFunction(5);
console.log(addFive(3)); // 8

// Counter example
function createCounter() {
    let count = 0;
    return {
        increment: () => ++count,
        decrement: () => --count,
        getCount: () => count
    };
}',
                'code_language' => 'javascript',
                'tags' => ['#javascript', '#closures', '#intermediate', '#code'],
                'category_id' => $jsEs6,
                'difficulty' => 'medium',
            ],
            [
                'user_id' => $userId,
                'title' => 'Destructuring Assignment',
                'item_type' => 'code_snippet',
                'content' => '// Array destructuring
const [a, b, c] = [1, 2, 3];

// Object destructuring
const {name, age} = {name: "John", age: 30};

// With default values
const {name = "Unknown", age = 0} = person;

// Nested destructuring
const {address: {city, country}} = user;

// Rest operator
const [first, ...rest] = [1, 2, 3, 4];',
                'code_language' => 'javascript',
                'tags' => ['#javascript', '#destructuring', '#es6', '#beginner'],
                'category_id' => $jsEs6,
                'difficulty' => 'easy',
            ],
            [
                'user_id' => $userId,
                'title' => 'Array Methods',
                'item_type' => 'code_snippet',
                'content' => 'const numbers = [1, 2, 3, 4, 5];

// map - transform
const doubled = numbers.map(n => n * 2);

// filter - select
const evens = numbers.filter(n => n % 2 === 0);

// reduce - aggregate
const sum = numbers.reduce((acc, n) => acc + n, 0);

// find - find first match
const found = numbers.find(n => n > 3);

// some - check if any match
const hasEven = numbers.some(n => n % 2 === 0);',
                'code_language' => 'javascript',
                'tags' => ['#javascript', '#arrays', '#beginner', '#code'],
                'category_id' => $jsEs6,
                'difficulty' => 'easy',
            ],
            [
                'user_id' => $userId,
                'title' => 'Event Handling',
                'item_type' => 'code_snippet',
                'content' => '// Add event listener
button.addEventListener(\'click\', function(event) {
    console.log(\'Button clicked\');
});

// Arrow function
button.addEventListener(\'click\', (event) => {
    console.log(\'Button clicked\');
});

// Event delegation
document.addEventListener(\'click\', (event) => {
    if (event.target.matches(\'.button\')) {
        console.log(\'Button clicked\');
    }
});',
                'code_language' => 'javascript',
                'tags' => ['#javascript', '#events', '#beginner', '#code'],
                'category_id' => $jsEs6,
                'difficulty' => 'easy',
            ],
            [
                'user_id' => $userId,
                'title' => 'React Hooks Example',
                'item_type' => 'code_snippet',
                'content' => 'import { useState, useEffect } from \'react\';

function Counter() {
    const [count, setCount] = useState(0);

    useEffect(() => {
        document.title = `Count: ${count}`;
    }, [count]);

    return (
        <div>
            <p>Count: {count}</p>
            <button onClick={() => setCount(count + 1)}>
                Increment
            </button>
        </div>
    );
}',
                'code_language' => 'javascript',
                'tags' => ['#javascript', '#react', '#hooks', '#intermediate'],
                'category_id' => $react,
                'difficulty' => 'medium',
            ],
            // Notes (4)
            [
                'user_id' => $userId,
                'title' => 'JavaScript Closures 解説',
                'item_type' => 'note',
                'content' => '# JavaScript Closures

## 定義
Closureは内部関数が外部関数の変数にアクセスできる機能です。

## 特徴
- 変数のプライベート化
- データの永続化
- 関数ファクトリー

## 使用例
- カウンター
- モジュールパターン
- コールバック関数',
                'tags' => ['#javascript', '#closures', '#learning-notes', '#intermediate'],
                'category_id' => $jsEs6,
            ],
            [
                'user_id' => $userId,
                'title' => 'Event Loop の仕組み',
                'item_type' => 'note',
                'content' => '# JavaScript Event Loop

## 実行順序
1. Call Stack（同期コード）
2. Web APIs（非同期処理）
3. Callback Queue
4. Event Loop

## 非同期処理
- setTimeout
- Promise
- async/await

## 注意点
- ブロッキングコードに注意
- 無限ループを避ける',
                'tags' => ['#javascript', '#event-loop', '#learning-notes', '#advanced'],
                'category_id' => $jsEs6,
            ],
            [
                'user_id' => $userId,
                'title' => 'Hoisting とは',
                'item_type' => 'note',
                'content' => '# JavaScript Hoisting

## 定義
変数と関数の宣言がスコープの先頭に移動される動作。

## var vs let/const
- **var**: ホイスティングされる（undefined）
- **let/const**: TDZ（Temporal Dead Zone）

## 関数宣言
```javascript
// 関数宣言はホイスティングされる
sayHello(); // Works

function sayHello() {
    console.log("Hello");
}
```',
                'tags' => ['#javascript', '#hoisting', '#learning-notes', '#intermediate'],
                'category_id' => $jsEs6,
            ],
            [
                'user_id' => $userId,
                'title' => 'Prototype Chain',
                'item_type' => 'note',
                'content' => '# JavaScript Prototype Chain

## プロトタイプ継承
JavaScriptはプロトタイプベースの継承を使用。

## プロトタイプチェーン
```javascript
Object.prototype
  ↑
Array.prototype
  ↑
[1, 2, 3]
```

## 使用例
- メソッドの共有
- 継承の実装
- メモリ効率',
                'tags' => ['#javascript', '#prototype', '#learning-notes', '#advanced'],
                'category_id' => $jsEs6,
            ],
            // Exercises (2)
            [
                'user_id' => $userId,
                'title' => 'Debounce Function',
                'item_type' => 'exercise',
                'question' => 'debounce関数を実装してください。連続して呼ばれた関数を指定時間後に1回だけ実行します。',
                'answer' => 'function debounce(func, delay) {
    let timeoutId;
    return function(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            func.apply(this, args);
        }, delay);
    };
}

// Usage
const debouncedSearch = debounce((query) => {
    console.log("Searching:", query);
}, 300);',
                'content' => 'パフォーマンス最適化のテクニック',
                'difficulty' => 'medium',
                'tags' => ['#javascript', '#debounce', '#exercise', '#intermediate'],
                'category_id' => $jsEs6,
            ],
            [
                'user_id' => $userId,
                'title' => 'Throttle Function',
                'item_type' => 'exercise',
                'question' => 'throttle関数を実装してください。指定時間内に1回だけ関数を実行します。',
                'answer' => 'function throttle(func, delay) {
    let lastCall = 0;
    return function(...args) {
        const now = Date.now();
        if (now - lastCall >= delay) {
            lastCall = now;
            func.apply(this, args);
        }
    };
}

// Usage
const throttledScroll = throttle(() => {
    console.log("Scrolling");
}, 100);',
                'content' => 'イベントハンドラーの最適化',
                'difficulty' => 'medium',
                'tags' => ['#javascript', '#throttle', '#exercise', '#intermediate'],
                'category_id' => $jsEs6,
            ],
        ];
    }

    /**
     * Algorithm Items (10 items) - Multi-language
     */
    private function getAlgorithmItems(): array
    {
        $userId = $this->userId;
        $algorithms = $this->getCategoryId('ソート & 検索', 'アルゴリズム');
        $dp = $this->getCategoryId('動的計画法', 'アルゴリズム');
        $graph = $this->getCategoryId('グラフアルゴリズム', 'アルゴリズム');

        return [
            [
                'user_id' => $userId,
                'title' => 'Binary Search (Python)',
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
                'tags' => ['#python', '#algorithm', '#binary-search', '#code'],
                'category_id' => $algorithms,
                'difficulty' => 'medium',
            ],
            [
                'user_id' => $userId,
                'title' => 'Binary Search (Java)',
                'item_type' => 'code_snippet',
                'content' => 'public int binarySearch(int[] arr, int target) {
    int left = 0, right = arr.length - 1;
    while (left <= right) {
        int mid = left + (right - left) / 2;
        if (arr[mid] == target) return mid;
        if (arr[mid] < target) left = mid + 1;
        else right = mid - 1;
    }
    return -1;
}',
                'code_language' => 'java',
                'tags' => ['#java', '#algorithm', '#binary-search', '#code'],
                'category_id' => $algorithms,
                'difficulty' => 'medium',
            ],
            [
                'user_id' => $userId,
                'title' => 'Binary Search (JavaScript)',
                'item_type' => 'code_snippet',
                'content' => 'function binarySearch(arr, target) {
    let left = 0, right = arr.length - 1;
    while (left <= right) {
        const mid = Math.floor((left + right) / 2);
        if (arr[mid] === target) return mid;
        if (arr[mid] < target) left = mid + 1;
        else right = mid - 1;
    }
    return -1;
}',
                'code_language' => 'javascript',
                'tags' => ['#javascript', '#algorithm', '#binary-search', '#code'],
                'category_id' => $algorithms,
                'difficulty' => 'medium',
            ],
            [
                'user_id' => $userId,
                'title' => 'Quick Sort (Python)',
                'item_type' => 'code_snippet',
                'content' => 'def quicksort(arr):
    if len(arr) <= 1:
        return arr
    pivot = arr[len(arr) // 2]
    left = [x for x in arr if x < pivot]
    middle = [x for x in arr if x == pivot]
    right = [x for x in arr if x > pivot]
    return quicksort(left) + middle + quicksort(right)',
                'code_language' => 'python',
                'tags' => ['#python', '#algorithm', '#sorting', '#quicksort', '#code'],
                'category_id' => $algorithms,
                'difficulty' => 'medium',
            ],
            [
                'user_id' => $userId,
                'title' => 'Merge Sort (Python)',
                'item_type' => 'code_snippet',
                'content' => 'def merge_sort(arr):
    if len(arr) <= 1:
        return arr

    mid = len(arr) // 2
    left = merge_sort(arr[:mid])
    right = merge_sort(arr[mid:])

    return merge(left, right)

def merge(left, right):
    result = []
    i = j = 0
    while i < len(left) and j < len(right):
        if left[i] <= right[j]:
            result.append(left[i])
            i += 1
        else:
            result.append(right[j])
            j += 1
    result.extend(left[i:])
    result.extend(right[j:])
    return result',
                'code_language' => 'python',
                'tags' => ['#python', '#algorithm', '#sorting', '#merge-sort', '#code'],
                'category_id' => $algorithms,
                'difficulty' => 'medium',
            ],
            [
                'user_id' => $userId,
                'title' => 'DFS (Depth-First Search)',
                'item_type' => 'code_snippet',
                'content' => 'def dfs(graph, start, visited=None):
    if visited is None:
        visited = set()

    visited.add(start)
    print(start)

    for neighbor in graph[start]:
        if neighbor not in visited:
            dfs(graph, neighbor, visited)

# Example usage
graph = {
    \'A\': [\'B\', \'C\'],
    \'B\': [\'D\', \'E\'],
    \'C\': [\'F\'],
    \'D\': [],
    \'E\': [],
    \'F\': []
}
dfs(graph, \'A\')',
                'code_language' => 'python',
                'tags' => ['#python', '#algorithm', '#graph', '#dfs', '#code'],
                'category_id' => $graph,
                'difficulty' => 'medium',
            ],
            [
                'user_id' => $userId,
                'title' => 'BFS (Breadth-First Search)',
                'item_type' => 'code_snippet',
                'content' => 'from collections import deque

def bfs(graph, start):
    visited = set()
    queue = deque([start])
    visited.add(start)

    while queue:
        node = queue.popleft()
        print(node)

        for neighbor in graph[node]:
            if neighbor not in visited:
                visited.add(neighbor)
                queue.append(neighbor)',
                'code_language' => 'python',
                'tags' => ['#python', '#algorithm', '#graph', '#bfs', '#code'],
                'category_id' => $graph,
                'difficulty' => 'medium',
            ],
            [
                'user_id' => $userId,
                'title' => 'Fibonacci with Memoization',
                'item_type' => 'code_snippet',
                'content' => 'def fibonacci(n, memo={}):
    if n in memo:
        return memo[n]
    if n <= 1:
        return n
    memo[n] = fibonacci(n-1, memo) + fibonacci(n-2, memo)
    return memo[n]

# Time: O(n), Space: O(n)',
                'code_language' => 'python',
                'tags' => ['#python', '#algorithm', '#dynamic-programming', '#memoization', '#code'],
                'category_id' => $dp,
                'difficulty' => 'medium',
            ],
            [
                'user_id' => $userId,
                'title' => 'アルゴリズムの時間計算量',
                'item_type' => 'note',
                'content' => '# 時間計算量（Time Complexity）

## よくある計算量
- O(1): 定数時間
- O(log n): 対数時間（二分探索）
- O(n): 線形時間（線形探索）
- O(n log n): マージソート、クイックソート
- O(n²): バブルソート、選択ソート
- O(2ⁿ): 指数時間（再帰的フィボナッチ）',
                'tags' => ['#algorithm', '#time-complexity', '#learning-notes', '#beginner'],
                'category_id' => $algorithms,
            ],
            [
                'user_id' => $userId,
                'title' => '動的計画法の基本',
                'item_type' => 'note',
                'content' => '# 動的計画法（Dynamic Programming）

## 特徴
- 部分問題の結果を再利用
- メモ化（Memoization）
- ボトムアップアプローチ

## 適用例
- フィボナッチ数列
- ナップサック問題
- 最長共通部分列（LCS）',
                'tags' => ['#algorithm', '#dynamic-programming', '#learning-notes', '#intermediate'],
                'category_id' => $dp,
            ],
        ];
    }

    /**
     * Interview Prep Items (15 items)
     */
    private function getInterviewPrepItems(): array
    {
        $userId = $this->userId;
        $leetcodeEasy = $this->getCategoryId('LeetCode Easy', 'コーディングチャレンジ');
        $leetcodeMedium = $this->getCategoryId('LeetCode Medium', 'コーディングチャレンジ');
        $systemDesign = $this->getCategoryId('スケーラビリティ', 'システム設計');
        $loadBalancing = $this->getCategoryId('ロードバランシング', 'システム設計');

        return [
            // LeetCode Easy (5)
            [
                'user_id' => $userId,
                'title' => 'Two Sum (LeetCode Easy)',
                'item_type' => 'exercise',
                'question' => '配列内の2つの数の合計がtargetになるインデックスを返してください。\n\n例: nums = [2,7,11,15], target = 9 → [0,1]',
                'answer' => 'def two_sum(nums, target):
    seen = {}
    for i, num in enumerate(nums):
        complement = target - num
        if complement in seen:
            return [seen[complement], i]
        seen[num] = i
    return []

# Time: O(n), Space: O(n)',
                'content' => 'LeetCode #1 - ハッシュマップを使用',
                'difficulty' => 'easy',
                'tags' => ['#leetcode', '#array', '#hash-map', '#interview-prep', '#easy'],
                'category_id' => $leetcodeEasy,
            ],
            [
                'user_id' => $userId,
                'title' => 'Valid Parentheses (LeetCode Easy)',
                'item_type' => 'exercise',
                'question' => '括弧の組み合わせが有効かどうかを判定してください。\n\n例: "()[]{}" → true, "([)]" → false',
                'answer' => 'def isValid(s):
    stack = []
    mapping = {\')\': \'(\', \']\': \'[\', \'}\': \'{\'}
    for char in s:
        if char in mapping:
            if not stack or stack.pop() != mapping[char]:
                return False
        else:
            stack.append(char)
    return not stack',
                'content' => 'LeetCode #20 - Stackを使用',
                'difficulty' => 'easy',
                'tags' => ['#leetcode', '#stack', '#string', '#interview-prep', '#easy'],
                'category_id' => $leetcodeEasy,
            ],
            [
                'user_id' => $userId,
                'title' => 'Palindrome Number (LeetCode Easy)',
                'item_type' => 'exercise',
                'question' => '整数が回文数かどうかを判定してください。\n\n例: 121 → true, -121 → false',
                'answer' => 'def isPalindrome(x):
    if x < 0:
        return False
    original = x
    reversed_num = 0
    while x > 0:
        reversed_num = reversed_num * 10 + x % 10
        x //= 10
    return original == reversed_num',
                'content' => 'LeetCode #9 - 数値の反転',
                'difficulty' => 'easy',
                'tags' => ['#leetcode', '#math', '#interview-prep', '#easy'],
                'category_id' => $leetcodeEasy,
            ],
            [
                'user_id' => $userId,
                'title' => 'Reverse String (LeetCode Easy)',
                'item_type' => 'exercise',
                'question' => '文字列を反転してください（in-place）。\n\n例: ["h","e","l","l","o"] → ["o","l","l","e","h"]',
                'answer' => 'def reverseString(s):
    left, right = 0, len(s) - 1
    while left < right:
        s[left], s[right] = s[right], s[left]
        left += 1
        right -= 1',
                'content' => 'LeetCode #344 - Two Pointers',
                'difficulty' => 'easy',
                'tags' => ['#leetcode', '#string', '#two-pointers', '#interview-prep', '#easy'],
                'category_id' => $leetcodeEasy,
            ],
            [
                'user_id' => $userId,
                'title' => 'Contains Duplicate (LeetCode Easy)',
                'item_type' => 'exercise',
                'question' => '配列に重複要素があるかどうかを判定してください。',
                'answer' => 'def containsDuplicate(nums):
    return len(nums) != len(set(nums))

# Alternative: Using hash set
def containsDuplicate(nums):
    seen = set()
    for num in nums:
        if num in seen:
            return True
        seen.add(num)
    return False',
                'content' => 'LeetCode #217 - Setを使用',
                'difficulty' => 'easy',
                'tags' => ['#leetcode', '#array', '#hash-set', '#interview-prep', '#easy'],
                'category_id' => $leetcodeEasy,
            ],
            // LeetCode Medium (7)
            [
                'user_id' => $userId,
                'title' => 'Longest Substring (LeetCode Medium)',
                'item_type' => 'exercise',
                'question' => '重複文字のない最長の部分文字列の長さを返してください。\n\n例: "abcabcbb" → 3 ("abc")',
                'answer' => 'def lengthOfLongestSubstring(s):
    char_map = {}
    start = max_len = 0
    for end, char in enumerate(s):
        if char in char_map:
            start = max(start, char_map[char] + 1)
        char_map[char] = end
        max_len = max(max_len, end - start + 1)
    return max_len',
                'content' => 'LeetCode #3 - Sliding Window',
                'difficulty' => 'medium',
                'tags' => ['#leetcode', '#string', '#sliding-window', '#interview-prep', '#medium'],
                'category_id' => $leetcodeMedium,
            ],
            [
                'user_id' => $userId,
                'title' => 'Binary Tree Traversal (LeetCode Medium)',
                'item_type' => 'exercise',
                'question' => '二分木のInorder Traversalを実装してください。',
                'answer' => 'def inorderTraversal(root):
    result = []

    def inorder(node):
        if node:
            inorder(node.left)
            result.append(node.val)
            inorder(node.right)

    inorder(root)
    return result

# Iterative version
def inorderTraversal(root):
    result = []
    stack = []
    current = root

    while stack or current:
        while current:
            stack.append(current)
            current = current.left
        current = stack.pop()
        result.append(current.val)
        current = current.right

    return result',
                'content' => 'LeetCode #94 - 再帰とイテレーション',
                'difficulty' => 'medium',
                'tags' => ['#leetcode', '#tree', '#traversal', '#interview-prep', '#medium'],
                'category_id' => $leetcodeMedium,
            ],
            [
                'user_id' => $userId,
                'title' => 'Product of Array Except Self',
                'item_type' => 'exercise',
                'question' => '自分以外の要素の積を返す配列を作成してください（除算を使わずに）。',
                'answer' => 'def productExceptSelf(nums):
    n = len(nums)
    result = [1] * n

    # Left products
    for i in range(1, n):
        result[i] = result[i-1] * nums[i-1]

    # Right products
    right = 1
    for i in range(n-1, -1, -1):
        result[i] *= right
        right *= nums[i]

    return result',
                'content' => 'LeetCode #238 - Two Pass',
                'difficulty' => 'medium',
                'tags' => ['#leetcode', '#array', '#interview-prep', '#medium'],
                'category_id' => $leetcodeMedium,
            ],
            [
                'user_id' => $userId,
                'title' => 'Group Anagrams',
                'item_type' => 'exercise',
                'question' => 'アナグラムをグループ化してください。',
                'answer' => 'def groupAnagrams(strs):
    from collections import defaultdict
    groups = defaultdict(list)

    for s in strs:
        key = \'\'.join(sorted(s))
        groups[key].append(s)

    return list(groups.values())',
                'content' => 'LeetCode #49 - ソートをキーに使用',
                'difficulty' => 'medium',
                'tags' => ['#leetcode', '#string', '#hash-map', '#interview-prep', '#medium'],
                'category_id' => $leetcodeMedium,
            ],
            [
                'user_id' => $userId,
                'title' => 'Merge Intervals',
                'item_type' => 'exercise',
                'question' => '重複する区間をマージしてください。',
                'answer' => 'def merge(intervals):
    if not intervals:
        return []

    intervals.sort(key=lambda x: x[0])
    merged = [intervals[0]]

    for current in intervals[1:]:
        if current[0] <= merged[-1][1]:
            merged[-1][1] = max(merged[-1][1], current[1])
        else:
            merged.append(current)

    return merged',
                'content' => 'LeetCode #56 - ソートとマージ',
                'difficulty' => 'medium',
                'tags' => ['#leetcode', '#array', '#sorting', '#interview-prep', '#medium'],
                'category_id' => $leetcodeMedium,
            ],
            [
                'user_id' => $userId,
                'title' => 'Rotate Array',
                'item_type' => 'exercise',
                'question' => '配列をkステップ右に回転してください。',
                'answer' => 'def rotate(nums, k):
    n = len(nums)
    k = k % n

    def reverse(start, end):
        while start < end:
            nums[start], nums[end] = nums[end], nums[start]
            start += 1
            end -= 1

    reverse(0, n - 1)
    reverse(0, k - 1)
    reverse(k, n - 1)',
                'content' => 'LeetCode #189 - Reverse Technique',
                'difficulty' => 'medium',
                'tags' => ['#leetcode', '#array', '#interview-prep', '#medium'],
                'category_id' => $leetcodeMedium,
            ],
            [
                'user_id' => $userId,
                'title' => 'Three Sum',
                'item_type' => 'exercise',
                'question' => '合計が0になる3つの数の組み合わせを全て見つけてください。',
                'answer' => 'def threeSum(nums):
    nums.sort()
    result = []
    n = len(nums)

    for i in range(n - 2):
        if i > 0 and nums[i] == nums[i-1]:
            continue

        left, right = i + 1, n - 1
        while left < right:
            total = nums[i] + nums[left] + nums[right]
            if total == 0:
                result.append([nums[i], nums[left], nums[right]])
                while left < right and nums[left] == nums[left+1]:
                    left += 1
                while left < right and nums[right] == nums[right-1]:
                    right -= 1
                left += 1
                right -= 1
            elif total < 0:
                left += 1
            else:
                right -= 1

    return result',
                'content' => 'LeetCode #15 - Two Pointers',
                'difficulty' => 'medium',
                'tags' => ['#leetcode', '#array', '#two-pointers', '#interview-prep', '#medium'],
                'category_id' => $leetcodeMedium,
            ],
            // System Design (3)
            [
                'user_id' => $userId,
                'title' => 'Load Balancing Strategies',
                'item_type' => 'note',
                'content' => '# Load Balancing Strategies

## アルゴリズム
1. **Round Robin**: 順番に分散
2. **Least Connections**: 接続数が少ないサーバーに
3. **IP Hash**: IPアドレスでハッシュ
4. **Weighted Round Robin**: 重み付け

## メリット
- 高可用性
- スケーラビリティ
- パフォーマンス向上',
                'tags' => ['#system-design', '#load-balancing', '#learning-notes', '#intermediate'],
                'category_id' => $loadBalancing,
            ],
            [
                'user_id' => $userId,
                'title' => 'Caching Strategies',
                'item_type' => 'note',
                'content' => '# Caching Strategies

## キャッシュ戦略
- **Cache-Aside**: アプリケーションが管理
- **Write-Through**: 書き込み時にキャッシュとDB両方
- **Write-Back**: キャッシュに書き込み、後でDB

## キャッシュの種類
- Redis
- Memcached
- CDN',
                'tags' => ['#system-design', '#caching', '#learning-notes', '#intermediate'],
                'category_id' => $systemDesign,
            ],
            [
                'user_id' => $userId,
                'title' => 'Database Sharding',
                'item_type' => 'note',
                'content' => '# Database Sharding

## シャーディング戦略
- **Horizontal Sharding**: 行を分割
- **Vertical Sharding**: 列を分割
- **Directory-based**: ルックアップテーブル

## メリット
- スケーラビリティ
- パフォーマンス向上

## 課題
- データの再分配
- クエリの複雑化',
                'tags' => ['#system-design', '#database', '#sharding', '#learning-notes', '#advanced'],
                'category_id' => $systemDesign,
            ],
        ];
    }

    /**
     * Database Items (8 items)
     */
    private function getDatabaseItems(): array
    {
        $userId = $this->userId;
        $sqlFundamentals = $this->getCategoryId('SQL基礎', 'データベース理論');
        $normalization = $this->getCategoryId('正規化', 'データベース理論');
        $indexing = $this->getCategoryId('インデックス', 'データベース理論');
        $transactions = $this->getCategoryId('トランザクション', 'データベース理論');

        return [
            // SQL Queries (4)
            [
                'user_id' => $userId,
                'title' => 'SQL JOIN Types',
                'item_type' => 'note',
                'content' => '# SQL JOIN Types

## INNER JOIN
```sql
SELECT * FROM orders
INNER JOIN customers ON orders.customer_id = customers.id;
```

## LEFT JOIN
```sql
SELECT * FROM customers
LEFT JOIN orders ON customers.id = orders.customer_id;
```

## RIGHT JOIN
```sql
SELECT * FROM orders
RIGHT JOIN customers ON orders.customer_id = customers.id;
```

## FULL OUTER JOIN
```sql
SELECT * FROM customers
FULL OUTER JOIN orders ON customers.id = orders.customer_id;
```',
                'tags' => ['#sql', '#database', '#joins', '#learning-notes'],
                'category_id' => $sqlFundamentals,
            ],
            [
                'user_id' => $userId,
                'title' => 'SQL Subqueries',
                'item_type' => 'code_snippet',
                'content' => '-- Subquery in WHERE
SELECT * FROM employees
WHERE salary > (SELECT AVG(salary) FROM employees);

-- Subquery in FROM
SELECT dept_name, avg_salary
FROM (
    SELECT department, AVG(salary) as avg_salary
    FROM employees
    GROUP BY department
) AS dept_avg;

-- EXISTS
SELECT * FROM customers c
WHERE EXISTS (
    SELECT 1 FROM orders o
    WHERE o.customer_id = c.id
);',
                'code_language' => 'sql',
                'tags' => ['#sql', '#database', '#subqueries', '#code'],
                'category_id' => $sqlFundamentals,
                'difficulty' => 'medium',
            ],
            [
                'user_id' => $userId,
                'title' => 'SQL Window Functions',
                'item_type' => 'code_snippet',
                'content' => '-- ROW_NUMBER
SELECT
    name,
    salary,
    ROW_NUMBER() OVER (ORDER BY salary DESC) as rank
FROM employees;

-- RANK and DENSE_RANK
SELECT
    name,
    salary,
    RANK() OVER (ORDER BY salary DESC) as rank,
    DENSE_RANK() OVER (ORDER BY salary DESC) as dense_rank
FROM employees;

-- PARTITION BY
SELECT
    department,
    name,
    salary,
    AVG(salary) OVER (PARTITION BY department) as dept_avg
FROM employees;',
                'code_language' => 'sql',
                'tags' => ['#sql', '#database', '#window-functions', '#code'],
                'category_id' => $sqlFundamentals,
                'difficulty' => 'hard',
            ],
            [
                'user_id' => $userId,
                'title' => 'SQL Indexes',
                'item_type' => 'code_snippet',
                'content' => '-- Create index
CREATE INDEX idx_email ON users(email);

-- Composite index
CREATE INDEX idx_name_age ON users(name, age);

-- Unique index
CREATE UNIQUE INDEX idx_username ON users(username);

-- Show indexes
SHOW INDEXES FROM users;

-- Drop index
DROP INDEX idx_email ON users;',
                'code_language' => 'sql',
                'tags' => ['#sql', '#database', '#indexes', '#code'],
                'category_id' => $indexing,
                'difficulty' => 'medium',
            ],
            // Notes (4)
            [
                'user_id' => $userId,
                'title' => 'Database Normalization',
                'item_type' => 'note',
                'content' => '# Database Normalization

## 正規化の目的
- データの重複を削減
- 整合性の向上
- ストレージの効率化

## 正規形
- **1NF**: 原子性（各セルに1つの値）
- **2NF**: 1NF + 部分関数従属の排除
- **3NF**: 2NF + 推移的従属の排除

## デメリット
- クエリが複雑になる
- JOINが増える',
                'tags' => ['#database', '#normalization', '#learning-notes', '#intermediate'],
                'category_id' => $normalization,
            ],
            [
                'user_id' => $userId,
                'title' => 'Database Indexing',
                'item_type' => 'note',
                'content' => '# Database Indexing

## インデックスの種類
- **B-Tree**: 一般的なインデックス
- **Hash**: 等価検索に最適
- **Full-Text**: テキスト検索

## メリット
- 検索速度の向上
- WHERE句の高速化

## デメリット
- ストレージ使用量増加
- INSERT/UPDATEが遅くなる',
                'tags' => ['#database', '#indexing', '#learning-notes', '#intermediate'],
                'category_id' => $indexing,
            ],
            [
                'user_id' => $userId,
                'title' => 'ACID Properties',
                'item_type' => 'note',
                'content' => '# ACID Properties

## ACID
- **Atomicity**: すべて成功またはすべて失敗
- **Consistency**: データの整合性を保つ
- **Isolation**: トランザクション間の独立性
- **Durability**: コミット後は永続化

## トランザクション分離レベル
- Read Uncommitted
- Read Committed
- Repeatable Read
- Serializable',
                'tags' => ['#database', '#acid', '#transactions', '#learning-notes', '#intermediate'],
                'category_id' => $transactions,
            ],
            [
                'user_id' => $userId,
                'title' => 'SQL Transactions',
                'item_type' => 'code_snippet',
                'content' => '-- Start transaction
START TRANSACTION;

-- Operations
UPDATE accounts SET balance = balance - 100 WHERE id = 1;
UPDATE accounts SET balance = balance + 100 WHERE id = 2;

-- Commit or Rollback
COMMIT;
-- OR
ROLLBACK;

-- Savepoint
SAVEPOINT sp1;
-- ... operations ...
ROLLBACK TO sp1;',
                'code_language' => 'sql',
                'tags' => ['#sql', '#database', '#transactions', '#code'],
                'category_id' => $transactions,
                'difficulty' => 'medium',
            ],
        ];
    }

    /**
     * Resource Links (10 items)
     */
    private function getResourceLinks(): array
    {
        $userId = $this->userId;
        $react = $this->getCategoryId('React.js', 'JavaScript');
        $docker = $this->getCategoryId('Docker Compose', 'Docker');
        $git = $this->getCategoryId('基本コマンド', 'Git & バージョン管理');
        $python = $this->getCategoryId('Python', 'プログラミング言語');
        $java = $this->getCategoryId('Java', 'プログラミング言語');
        $jsEs6 = $this->getCategoryId('ES6+機能', 'JavaScript');
        $laravel = $this->getCategoryId('Laravel', 'PHP');

        return [
            [
                'user_id' => $userId,
                'title' => 'React Official Documentation',
                'item_type' => 'resource_link',
                'url' => 'https://react.dev',
                'content' => 'React公式ドキュメント - Hooks、コンポーネント、ベストプラクティス',
                'tags' => ['#react', '#documentation', '#resources', '#tutorial'],
                'category_id' => $react,
            ],
            [
                'user_id' => $userId,
                'title' => 'Vue.js Official Guide',
                'item_type' => 'resource_link',
                'url' => 'https://vuejs.org',
                'content' => 'Vue.js公式ガイド - コンポーネント、リアクティビティ、ルーティング',
                'tags' => ['#vue', '#documentation', '#resources', '#tutorial'],
                'category_id' => $react,
            ],
            [
                'user_id' => $userId,
                'title' => 'Docker Compose Tutorial',
                'item_type' => 'resource_link',
                'url' => 'https://docs.docker.com/compose/',
                'content' => 'Docker Compose公式ドキュメント - マルチコンテナアプリケーションの定義と実行',
                'tags' => ['#docker', '#devops', '#containers', '#tutorial'],
                'category_id' => $docker,
            ],
            [
                'user_id' => $userId,
                'title' => 'Docker Official Documentation',
                'item_type' => 'resource_link',
                'url' => 'https://docs.docker.com',
                'content' => 'Docker公式ドキュメント - コンテナ、イメージ、ネットワーク',
                'tags' => ['#docker', '#documentation', '#resources', '#tutorial'],
                'category_id' => $docker,
            ],
            [
                'user_id' => $userId,
                'title' => 'Git Cheat Sheet',
                'item_type' => 'resource_link',
                'url' => 'https://education.github.com/git-cheat-sheet-education.pdf',
                'content' => 'Gitコマンドのクイックリファレンス',
                'tags' => ['#git', '#quick-reference', '#cheatsheet'],
                'category_id' => $git,
            ],
            [
                'user_id' => $userId,
                'title' => 'Python Official Tutorial',
                'item_type' => 'resource_link',
                'url' => 'https://docs.python.org/3/tutorial/',
                'content' => 'Python公式チュートリアル - 基礎から応用まで',
                'tags' => ['#python', '#documentation', '#tutorial', '#resources'],
                'category_id' => $python,
            ],
            [
                'user_id' => $userId,
                'title' => 'Java Documentation',
                'item_type' => 'resource_link',
                'url' => 'https://docs.oracle.com/javase/tutorial/',
                'content' => 'Oracle Java公式チュートリアル - 言語仕様、API、ベストプラクティス',
                'tags' => ['#java', '#documentation', '#tutorial', '#resources'],
                'category_id' => $java,
            ],
            [
                'user_id' => $userId,
                'title' => 'MDN Web Docs - JavaScript',
                'item_type' => 'resource_link',
                'url' => 'https://developer.mozilla.org/en-US/docs/Web/JavaScript',
                'content' => 'MDN JavaScriptリファレンス - 包括的なドキュメントと例',
                'tags' => ['#javascript', '#documentation', '#resources', '#tutorial'],
                'category_id' => $jsEs6,
            ],
            [
                'user_id' => $userId,
                'title' => 'Laravel Documentation',
                'item_type' => 'resource_link',
                'url' => 'https://laravel.com/docs',
                'content' => 'Laravel公式ドキュメント - フレームワーク、Eloquent、ルーティング',
                'tags' => ['#laravel', '#php', '#documentation', '#resources', '#tutorial'],
                'category_id' => $laravel,
            ],
            [
                'user_id' => $userId,
                'title' => 'LeetCode - Practice Problems',
                'item_type' => 'resource_link',
                'url' => 'https://leetcode.com',
                'content' => 'LeetCode - コーディング面接の練習問題集',
                'tags' => ['#leetcode', '#interview-prep', '#algorithm', '#resources', '#problem-solving'],
                'category_id' => $this->getCategoryId('LeetCode Easy', 'コーディングチャレンジ'),
            ],
        ];
    }
}
