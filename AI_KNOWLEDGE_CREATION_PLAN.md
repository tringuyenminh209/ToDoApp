# 🤖 AI Knowledge Creation System - Design Document

## 📋 Mục Tiêu
Phát triển tính năng AI có thể **tự động tạo Knowledge Folder (Categories) và Knowledge Items** dựa trên yêu cầu của người dùng thông qua chat.

---

## 🔍 Phân Tích Cấu Trúc Hiện Tại

### **1. Database Schema**

#### **knowledge_categories Table**
```sql
- id: BIGINT PRIMARY KEY
- user_id: BIGINT (FK to users)
- parent_id: BIGINT NULLABLE (FK to knowledge_categories - for nested folders)
- name: VARCHAR(255)
- description: TEXT NULLABLE
- sort_order: INT
- color: VARCHAR(20) NULLABLE
- icon: VARCHAR(50) NULLABLE
- item_count: INT DEFAULT 0
- created_at, updated_at: TIMESTAMP
```

**Đặc điểm:**
- ✅ Hỗ trợ nested categories (parent_id)
- ✅ Có thể customized (color, icon)
- ✅ Track số lượng items (item_count)

#### **knowledge_items Table**
```sql
- id: BIGINT PRIMARY KEY
- user_id: BIGINT (FK to users)
- category_id: BIGINT NULLABLE (FK to knowledge_categories)
- title: VARCHAR(255)
- item_type: ENUM('note', 'code_snippet', 'exercise', 'resource_link', 'attachment')
- content: TEXT NULLABLE
- code_language: VARCHAR(50) NULLABLE
- url: TEXT NULLABLE
- question: TEXT NULLABLE (for exercises)
- answer: TEXT NULLABLE (for exercises)
- difficulty: ENUM('easy', 'medium', 'hard') NULLABLE
- tags: JSON NULLABLE (array of strings)
- learning_path_id: BIGINT NULLABLE
- source_task_id: BIGINT NULLABLE
- review_count, view_count: INT
- last_reviewed_at: TIMESTAMP
- next_review_date: DATE
- retention_score: INT NULLABLE
- ai_summary: TEXT NULLABLE
- is_favorite, is_archived: BOOLEAN
- created_at, updated_at: TIMESTAMP
```

**Đặc điểm:**
- ✅ 5 loại items: note, code_snippet, exercise, resource_link, attachment
- ✅ Rich metadata (tags, difficulty, review tracking)
- ✅ Có thể link với learning_path và source_task
- ✅ Đã có trường `ai_summary` (sẵn sàng cho AI)

### **2. Existing API Endpoints**

#### **KnowledgeController.php**
```php
GET    /api/knowledge-items              // List items (with filters)
GET    /api/knowledge-items/{id}         // Get single item
POST   /api/knowledge-items              // Create item ✅
PUT    /api/knowledge-items/{id}         // Update item
DELETE /api/knowledge-items/{id}         // Delete item
POST   /api/knowledge-items/{id}/review  // Mark as reviewed
```

#### **KnowledgeCategoryController.php** (cần kiểm tra)
```php
// Giả định có endpoints tương tự cho categories
GET    /api/knowledge-categories
POST   /api/knowledge-categories
PUT    /api/knowledge-categories/{id}
DELETE /api/knowledge-categories/{id}
```

### **3. Existing AI Features**

#### **AIService.php - parseKnowledgeQueryIntent()** ✅
```php
// Lines 918-1058
// Đã có: Parse knowledge SEARCH intent
// Input: "Java list ntn?" → Output: {keywords: ["Java", "list"], item_type: "code_snippet"}
// Chỉ dùng để SEARCH, CHƯA có tạo mới
```

#### **AIController.php - Context-Aware Chat** ✅
```php
// Lines 941-1233
// Đã tích hợp: parseKnowledgeQueryIntent
// Flow: User message → Parse intent → Search knowledge → Return results
```

---

## 🎯 Tính Năng Mới Cần Phát Triển

### **Feature: AI Knowledge Creation Intent Parsing**

**User Stories:**
1. "Tạo folder JavaScript basics và thêm 5 bài tập về loops"
2. "Tôi muốn lưu lại kiến thức về React Hooks, tạo folder React và note về useState"
3. "Add code snippet Python sorting algorithms vào folder Algorithms"
4. "Tạo category Data Structures với subcategories: Arrays, Trees, Graphs"

**Requirements:**
- ✅ Parse ý định tạo folder/category
- ✅ Parse ý định tạo knowledge item
- ✅ Parse nested structure (parent-child categories)
- ✅ Parse multiple items cùng lúc
- ✅ Auto-assign metadata (tags, difficulty, item_type)
- ✅ Handle partial information (AI fill missing fields)

---

## 📐 Architecture Design

### **1. New Intent Parser: `parseKnowledgeCreationIntent()`**

**Location:** `backend/app/Services/AIService.php`

**Input:**
```php
$message = "Tạo folder JavaScript với 3 code snippets về array methods";
$conversationHistory = [...]; // Context từ chat trước
```

**Output:**
```php
[
    'has_creation_intent' => true,
    'action' => 'create', // or 'add_to_existing'
    'categories' => [
        [
            'name' => 'JavaScript',
            'description' => 'JavaScript programming basics and advanced concepts',
            'color' => '#f7df1e',
            'icon' => 'javascript',
            'parent_id' => null,
        ]
    ],
    'items' => [
        [
            'title' => 'Array map() method',
            'item_type' => 'code_snippet',
            'code_language' => 'javascript',
            'content' => 'const numbers = [1, 2, 3];\nconst doubled = numbers.map(x => x * 2);',
            'tags' => ['javascript', 'array', 'map'],
            'difficulty' => 'easy',
            'category_name' => 'JavaScript', // Link to category
        ],
        [
            'title' => 'Array filter() method',
            'item_type' => 'code_snippet',
            'code_language' => 'javascript',
            'content' => 'const numbers = [1, 2, 3, 4];\nconst evens = numbers.filter(x => x % 2 === 0);',
            'tags' => ['javascript', 'array', 'filter'],
            'difficulty' => 'easy',
            'category_name' => 'JavaScript',
        ],
        [
            'title' => 'Array reduce() method',
            'item_type' => 'code_snippet',
            'code_language' => 'javascript',
            'content' => 'const numbers = [1, 2, 3, 4];\nconst sum = numbers.reduce((acc, x) => acc + x, 0);',
            'tags' => ['javascript', 'array', 'reduce'],
            'difficulty' => 'medium',
            'category_name' => 'JavaScript',
        ]
    ],
    'ai_explanation' => 'JavaScriptフォルダを作成し、3つの配列メソッドのコードスニペットを追加しました。'
]
```

**Implementation:**
```php
// backend/app/Services/AIService.php

/**
 * Parse knowledge creation intent from user message
 * Detects when user wants to CREATE categories and knowledge items
 *
 * @param string $message User message
 * @param array $conversationHistory Optional conversation context
 * @param User $user The user (to check existing categories)
 * @return array|null Creation data if intent detected, null otherwise
 */
public function parseKnowledgeCreationIntent(string $message, array $conversationHistory = [], $user = null): ?array
{
    if (!$this->apiKey) {
        return null;
    }

    // Build context - include user's existing categories
    $existingCategories = [];
    if ($user) {
        $existingCategories = \App\Models\KnowledgeCategory::where('user_id', $user->id)
            ->select('id', 'name', 'parent_id', 'description')
            ->get()
            ->toArray();
    }

    $contextText = '';
    if (!empty($conversationHistory)) {
        $contextText = "\n会話履歴:\n";
        $recentHistory = array_slice($conversationHistory, -3);
        foreach ($recentHistory as $msg) {
            $role = $msg['role'] === 'user' ? 'ユーザー' : 'アシスタント';
            $contextText .= "{$role}: {$msg['content']}\n";
        }
    }

    // Build existing categories context
    $categoriesContext = '';
    if (!empty($existingCategories)) {
        $categoriesContext = "\n\n既存のフォルダ/カテゴリ:\n";
        foreach ($existingCategories as $cat) {
            $parentInfo = $cat['parent_id'] ? " (親: {$cat['parent_id']})" : '';
            $categoriesContext .= "- [{$cat['id']}] {$cat['name']}{$parentInfo}\n";
        }
    }

    $systemPrompt = "あなたは知識管理アシスタントです。
ユーザーのメッセージを分析して、knowledge folderやknowledge itemを作成する意図があるかを判断してください。

## 判定基準:
- 「作成」「追加」「保存」「記録」「フォルダ」「ノート」などのキーワード
- 具体的な技術/トピック名の言及 (例: JavaScript, Python, React)
- コードスニペット、メモ、演習問題などの言及

## 出力形式 (JSON):
{
    \"has_creation_intent\": true/false,
    \"action\": \"create\" | \"add_to_existing\",
    \"categories\": [
        {
            \"name\": \"カテゴリ名\",
            \"description\": \"説明 (自動生成)\",
            \"color\": \"#hex色コード (適切な色を選択)\",
            \"icon\": \"アイコン名 (技術に合ったもの)\",
            \"parent_id\": null or 既存カテゴリID
        }
    ],
    \"items\": [
        {
            \"title\": \"タイトル\",
            \"item_type\": \"note\" | \"code_snippet\" | \"exercise\" | \"resource_link\" | \"attachment\",
            \"content\": \"内容 (note/code_snippet用)\",
            \"code_language\": \"言語 (code_snippet用)\",
            \"url\": \"URL (resource_link用)\",
            \"question\": \"問題文 (exercise用)\",
            \"answer\": \"解答 (exercise用)\",
            \"difficulty\": \"easy\" | \"medium\" | \"hard\",
            \"tags\": [\"tag1\", \"tag2\"],
            \"category_name\": \"所属カテゴリ名\"
        }
    ],
    \"ai_explanation\": \"実行内容の説明\"
}

## 重要:
- item_typeを正しく判定 (コード→code_snippet, メモ→note, 問題→exercise, リンク→resource_link)
- code_snippetの場合、code_languageを必ず設定
- tagsは関連キーワードから自動抽出
- 既存カテゴリがあれば再利用 (parent_idを設定)
- ユーザーが具体的な内容を提供していない場合、サンプル/テンプレートを生成
{$categoriesContext}";

    $userPrompt = "ユーザーのメッセージ: {$message}{$contextText}";

    try {
        $modelToUse = $this->primaryModel;
        $parseTimeout = $this->timeout;

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ];

        $requestBody = [
            'model' => $modelToUse,
            'messages' => $messages,
            'temperature' => 0.3, // Lower temperature for structured parsing
            'max_tokens' => 2000, // Allow longer response for multiple items
        ];

        // Add response format for JSON mode if using gpt-4o or later
        if (str_contains($modelToUse, 'gpt-4') || str_contains($modelToUse, 'gpt-5')) {
            $requestBody['response_format'] = ['type' => 'json_object'];
        }

        Log::info('parseKnowledgeCreationIntent: Sending request', [
            'model' => $modelToUse,
            'message' => $message
        ]);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->timeout((int)$parseTimeout)->post($this->baseUrl . '/chat/completions', $requestBody);

        if ($response->successful()) {
            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? '';

            Log::info('parseKnowledgeCreationIntent: AI response', ['response' => $content]);

            $parsedContent = json_decode($content, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                if (!empty($parsedContent['has_creation_intent']) && $parsedContent['has_creation_intent'] === true) {
                    Log::info('Knowledge creation intent detected', ['data' => $parsedContent]);
                    return $parsedContent;
                }
            } else {
                Log::error('JSON parse error in knowledge creation intent', [
                    'error' => json_last_error_msg(),
                    'content' => $content
                ]);
            }
        }

        return null;

    } catch (\Exception $e) {
        Log::error('parseKnowledgeCreationIntent: Exception', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return null;
    }
}
```

---

### **2. New Service: `KnowledgeCreationService`**

**Location:** `backend/app/Services/KnowledgeCreationService.php` (NEW FILE)

**Purpose:** Orchestrate category and item creation from AI-parsed data

```php
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
```

---

### **3. Update AIController - Integrate Creation Intent**

**Location:** `backend/app/Http/Controllers/AIController.php`

**Update Method:** `contextAwareChat()` (around line 941-1233)

```php
// Add after parseKnowledgeQueryIntent() call (around line 997-1002)

// NEW: Parse knowledge CREATION intent
$knowledgeCreationData = $this->aiService->parseKnowledgeCreationIntent(
    $request->message,
    $historyForParsing,
    $user
);

if ($knowledgeCreationData && !empty($knowledgeCreationData['has_creation_intent'])) {
    Log::info('Knowledge creation intent detected, creating items...');

    $creationService = app(\App\Services\KnowledgeCreationService::class);
    $result = $creationService->createKnowledgeFromIntent($knowledgeCreationData, $user);

    if ($result['success']) {
        // Build response message
        $categoriesCount = $result['summary']['categories_created'];
        $itemsCount = $result['summary']['items_created'];

        $responseMessage = $knowledgeCreationData['ai_explanation'] ?? '';
        $responseMessage .= "\n\n✅ 作成完了:\n";
        $responseMessage .= "- フォルダ: {$categoriesCount}個\n";
        $responseMessage .= "- アイテム: {$itemsCount}個\n";

        // Add details about created items
        if (!empty($result['items'])) {
            $responseMessage .= "\n作成されたアイテム:\n";
            foreach ($result['items'] as $item) {
                $typeLabel = $this->getItemTypeLabel($item->item_type);
                $responseMessage .= "- [{$typeLabel}] {$item->title}\n";
            }
        }

        // Store in conversation
        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $responseMessage,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'message' => $responseMessage,
                'created_categories' => $result['categories'],
                'created_items' => $result['items'],
                'knowledge_creation' => true, // Flag for frontend
            ],
        ]);
    } else {
        $errorMessage = "申し訳ありません。Knowledge作成中にエラーが発生しました: " . ($result['error'] ?? 'Unknown error');

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $errorMessage,
        ]);

        return response()->json([
            'success' => false,
            'message' => $errorMessage,
        ]);
    }
}

// Helper method
private function getItemTypeLabel(string $type): string
{
    $labels = [
        'note' => 'ノート',
        'code_snippet' => 'コード',
        'exercise' => '演習',
        'resource_link' => 'リンク',
        'attachment' => '添付',
    ];
    return $labels[$type] ?? $type;
}
```

---

### **4. New API Endpoint (Optional - Direct Creation)**

**Location:** `backend/routes/api.php`

```php
// Add new route for direct AI knowledge creation
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/ai/knowledge/create', [AIController::class, 'createKnowledgeWithAI']);
});
```

**Controller Method:**
```php
// backend/app/Http/Controllers/AIController.php

/**
 * Create knowledge items using AI
 * Direct endpoint (alternative to chat-based creation)
 */
public function createKnowledgeWithAI(Request $request): JsonResponse
{
    $user = $request->user();

    $validator = Validator::make($request->all(), [
        'prompt' => 'required|string|max:1000',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    $prompt = $request->prompt;

    // Parse intent
    $creationData = $this->aiService->parseKnowledgeCreationIntent($prompt, [], $user);

    if (!$creationData || empty($creationData['has_creation_intent'])) {
        return response()->json([
            'success' => false,
            'message' => 'Knowledge作成の意図が検出されませんでした。もっと具体的に説明してください。',
        ], 400);
    }

    // Create knowledge
    $creationService = app(\App\Services\KnowledgeCreationService::class);
    $result = $creationService->createKnowledgeFromIntent($creationData, $user);

    if ($result['success']) {
        return response()->json([
            'success' => true,
            'data' => $result,
            'message' => 'Knowledge items created successfully',
        ]);
    } else {
        return response()->json([
            'success' => false,
            'message' => $result['error'] ?? 'Creation failed',
        ], 500);
    }
}
```

---

## 🚀 Implementation Plan

### **Phase 1: Backend Core (Days 1-3)**

**Day 1:**
1. ✅ Create `parseKnowledgeCreationIntent()` in AIService.php
2. ✅ Test prompt engineering để AI parse đúng intent
3. ✅ Handle edge cases (incomplete data, ambiguous requests)

**Day 2:**
1. ✅ Create `KnowledgeCreationService.php`
2. ✅ Implement category creation logic
3. ✅ Implement item creation logic
4. ✅ Add transaction handling

**Day 3:**
1. ✅ Integrate into `AIController::contextAwareChat()`
2. ✅ Add optional direct API endpoint
3. ✅ Test end-to-end flow

### **Phase 2: Testing & Refinement (Days 4-5)**

**Day 4:**
1. Unit tests cho `parseKnowledgeCreationIntent()`
2. Integration tests cho full creation flow
3. Test various user prompts:
   - Simple: "Create JavaScript folder"
   - Complex: "Create React folder with useState, useEffect, useContext hooks as code snippets"
   - Nested: "Create Programming folder with subfolders: Frontend, Backend, DevOps"

**Day 5:**
1. Error handling improvements
2. AI prompt refinement based on test results
3. Performance optimization
4. Documentation

### **Phase 3: Android Integration (Days 6-7)**

**Day 6:**
1. Update Android `ApiService.kt` - add createKnowledgeWithAI endpoint
2. Update Android chat UI để show creation confirmation
3. Add visual feedback khi AI đang tạo knowledge

**Day 7:**
1. Test Android → Backend → Database flow
2. Handle success/error states in Android
3. UI polish

---

## 📊 Example Use Cases

### **Use Case 1: Create Simple Folder + Notes**
**User Input:**
```
"Tạo folder React hooks và thêm note về useState"
```

**AI Parse Output:**
```json
{
  "has_creation_intent": true,
  "action": "create",
  "categories": [
    {
      "name": "React Hooks",
      "description": "React Hooks for state and side effects management",
      "color": "#61dafb",
      "icon": "react"
    }
  ],
  "items": [
    {
      "title": "useState Hook - State Management",
      "item_type": "note",
      "content": "useState is a React Hook that lets you add state to function components.\n\nSyntax:\nconst [state, setState] = useState(initialValue);\n\nExample:\nconst [count, setCount] = useState(0);",
      "tags": ["react", "hooks", "useState", "state"],
      "difficulty": "easy",
      "category_name": "React Hooks"
    }
  ],
  "ai_explanation": "React Hooksフォルダを作成し、useStateに関するノートを追加しました。"
}
```

**Database Result:**
- ✅ 1 category created: "React Hooks"
- ✅ 1 note created: "useState Hook - State Management"

---

### **Use Case 2: Create Multiple Code Snippets**
**User Input:**
```
"Add 5 Python sorting algorithms to Algorithms folder"
```

**AI Parse Output:**
```json
{
  "has_creation_intent": true,
  "action": "add_to_existing",
  "categories": [
    {
      "name": "Algorithms",
      "description": "Common algorithms and data structures",
      "color": "#3776ab",
      "icon": "chart"
    }
  ],
  "items": [
    {
      "title": "Bubble Sort",
      "item_type": "code_snippet",
      "code_language": "python",
      "content": "def bubble_sort(arr):\n    n = len(arr)\n    for i in range(n):\n        for j in range(0, n-i-1):\n            if arr[j] > arr[j+1]:\n                arr[j], arr[j+1] = arr[j+1], arr[j]\n    return arr",
      "tags": ["python", "sorting", "bubble-sort", "algorithm"],
      "difficulty": "easy",
      "category_name": "Algorithms"
    },
    {
      "title": "Quick Sort",
      "item_type": "code_snippet",
      "code_language": "python",
      "content": "def quick_sort(arr):\n    if len(arr) <= 1:\n        return arr\n    pivot = arr[len(arr) // 2]\n    left = [x for x in arr if x < pivot]\n    middle = [x for x in arr if x == pivot]\n    right = [x for x in arr if x > pivot]\n    return quick_sort(left) + middle + quick_sort(right)",
      "tags": ["python", "sorting", "quick-sort", "algorithm"],
      "difficulty": "medium",
      "category_name": "Algorithms"
    }
    // ... 3 more algorithms ...
  ],
  "ai_explanation": "Algorithmsフォルダに5つのPythonソートアルゴリズムを追加しました。"
}
```

---

### **Use Case 3: Create Nested Categories**
**User Input:**
```
"Create Web Development folder with HTML, CSS, JavaScript subfolders"
```

**AI Parse Output:**
```json
{
  "has_creation_intent": true,
  "action": "create",
  "categories": [
    {
      "name": "Web Development",
      "description": "Frontend web development technologies",
      "color": "#e34f26",
      "icon": "globe",
      "parent_id": null
    },
    {
      "name": "HTML",
      "description": "HTML markup and structure",
      "color": "#e34f26",
      "icon": "html5",
      "parent_name": "Web Development"
    },
    {
      "name": "CSS",
      "description": "CSS styling and layout",
      "color": "#1572b6",
      "icon": "css3",
      "parent_name": "Web Development"
    },
    {
      "name": "JavaScript",
      "description": "JavaScript programming",
      "color": "#f7df1e",
      "icon": "javascript",
      "parent_name": "Web Development"
    }
  ],
  "items": [],
  "ai_explanation": "Web Developmentフォルダと3つのサブフォルダ（HTML、CSS、JavaScript）を作成しました。"
}
```

**Note:** `parent_name` sẽ được resolve thành `parent_id` trong `KnowledgeCreationService`

---

## 🎯 Success Metrics

### **Technical Metrics:**
- ✅ Intent detection accuracy: >85%
- ✅ Category creation success rate: >95%
- ✅ Item creation success rate: >90%
- ✅ Average response time: <3 seconds
- ✅ Zero data corruption/rollback issues

### **User Experience Metrics:**
- ✅ User satisfaction with AI-generated content: >80%
- ✅ Reduction in manual knowledge creation time: >60%
- ✅ Knowledge item usage after creation: >70%

---

## 💡 Future Enhancements

### **Phase 2 (Optional):**
1. **Content Generation Enhancement:**
   - AI tự động generate content khi user chỉ cung cấp title
   - Example: "Create note about React useEffect" → AI tự viết nội dung note

2. **Bulk Import:**
   - Import từ URL (parse article/documentation)
   - Import từ file (PDF, markdown)

3. **Smart Categorization:**
   - AI suggest category khi create item standalone
   - Auto-tag items based on content

4. **Knowledge Graph:**
   - Detect relationships between items
   - Suggest related items

---

## 📝 Testing Checklist

### **Backend Tests:**
- [ ] `parseKnowledgeCreationIntent()` returns null for non-creation messages
- [ ] `parseKnowledgeCreationIntent()` detects simple creation: "Create folder X"
- [ ] `parseKnowledgeCreationIntent()` detects complex creation with multiple items
- [ ] `parseKnowledgeCreationIntent()` handles nested categories
- [ ] `KnowledgeCreationService` creates category successfully
- [ ] `KnowledgeCreationService` reuses existing category
- [ ] `KnowledgeCreationService` creates items with correct type
- [ ] `KnowledgeCreationService` handles transaction rollback on error
- [ ] `KnowledgeCreationService` updates category item_count
- [ ] API endpoint `/chat/send` returns creation results
- [ ] API endpoint `/ai/knowledge/create` works standalone

### **Integration Tests:**
- [ ] Chat → Parse → Create → Database (full flow)
- [ ] Multiple categories + items in one request
- [ ] Error handling when category creation fails
- [ ] Error handling when item creation fails
- [ ] Concurrent creation requests don't conflict

### **Android Tests:**
- [ ] Chat shows creation confirmation message
- [ ] Knowledge list refreshes after creation
- [ ] Error toast shows when creation fails

---

## 🚦 Ready to Implement?

**Recommendation:** Start with **Phase 1, Day 1** - implement `parseKnowledgeCreationIntent()` first and test thoroughly before building the rest.

Would you like me to start implementing this feature? I can begin with creating the `parseKnowledgeCreationIntent()` method in AIService.php.
