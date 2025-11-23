# Knowledge Base API Endpoints
**Phase 1 Implementation Complete**

Last Updated: 2025-11-23

---

## 📚 Category Endpoints

### 1. Get All Categories (Flat List)
```http
GET /api/knowledge/categories
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "parent_id": null,
      "name": "Programming Languages",
      "description": "...",
      "color": "#0FA968",
      "icon": "code",
      "sort_order": 1,
      "item_count": 125,
      "created_at": "...",
      "updated_at": "..."
    }
  ]
}
```

### 2. Get Category Tree (Hierarchical)
```http
GET /api/knowledge/categories/tree
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Programming Languages",
      "children": [
        {
          "id": 2,
          "name": "Python",
          "children": [...]
        }
      ]
    }
  ]
}
```

### 3. Get Single Category with Items
```http
GET /api/knowledge/categories/{id}
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": {
    "id": 2,
    "name": "Python",
    "breadcrumb": [
      {"id": 1, "name": "Programming Languages"},
      {"id": 2, "name": "Python"}
    ],
    "knowledge_items": [...]
  }
}
```

### 4. Create Category
```http
POST /api/knowledge/categories
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "React.js",
  "parent_id": 5,
  "description": "React framework notes",
  "color": "#61DAFB",
  "icon": "react",
  "sort_order": 1
}

Response:
{
  "success": true,
  "data": {...},
  "message": "Category created successfully"
}
```

### 5. Update Category
```http
PUT /api/knowledge/categories/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Updated Name",
  "color": "#FF0000"
}
```

### 6. Delete Category
```http
DELETE /api/knowledge/categories/{id}
Authorization: Bearer {token}

Note: Cannot delete if category has children or items
```

### 7. Move Category
```http
POST /api/knowledge/categories/{id}/move
Authorization: Bearer {token}
Content-Type: application/json

{
  "new_parent_id": 8,
  "sort_order": 2
}
```

### 8. Reorder Categories (Batch)
```http
POST /api/knowledge/categories/reorder
Authorization: Bearer {token}
Content-Type: application/json

{
  "categories": [
    {"id": 1, "sort_order": 1},
    {"id": 2, "sort_order": 2}
  ]
}
```

### 9. Update Item Count
```http
POST /api/knowledge/categories/{id}/update-count
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": {
    "category_id": 5,
    "item_count": 42
  }
}
```

### 10. Category Statistics
```http
GET /api/knowledge/categories/stats
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": {
    "total_categories": 45,
    "root_categories": 6,
    "categories_with_items": 32,
    "most_used_category": {
      "id": 5,
      "name": "Python",
      "item_count": 89
    }
  }
}
```

---

## 📝 Knowledge Item Endpoints

### 11. Get All Items
```http
GET /api/knowledge?type=code_snippet&category_id=5&search=binary&favorites=true
Authorization: Bearer {token}

Query Parameters:
- type: note|code_snippet|exercise|resource_link|attachment
- category_id: Filter by category
- learning_path_id: Filter by learning path
- source_task_id: Filter by source task
- favorites: true|false
- archived: true|false
- due_review: true|false
- search: Search term
- sort_by: created_at|updated_at|view_count (default: created_at)
- sort_order: asc|desc (default: desc)
```

### 12. Get Single Item
```http
GET /api/knowledge/{id}
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": {
    "id": 123,
    "title": "Binary Tree Traversal",
    "item_type": "code_snippet",
    "content": "...",
    "code_language": "python",
    "tags": ["#python", "#algorithm", "#tree"],
    "category": {...},
    "view_count": 15,
    "review_count": 3,
    "next_review_date": "2025-12-01"
  }
}
```

### 13. Create Item
```http
POST /api/knowledge
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Quick Sort Algorithm",
  "item_type": "code_snippet",
  "category_id": 5,
  "content": "def quicksort(arr):\n    ...",
  "code_language": "python",
  "tags": ["#algorithm", "#sorting"],
  "difficulty": "medium"
}
```

### 14. Update Item
```http
PUT /api/knowledge/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Updated Title",
  "content": "Updated content..."
}
```

### 15. Delete Item
```http
DELETE /api/knowledge/{id}
Authorization: Bearer {token}
```

### 16. Quick Capture ⭐ NEW
```http
POST /api/knowledge/quick-capture
Authorization: Bearer {token}
Content-Type: application/json

{
  "content": "def fibonacci(n):\n    if n <= 1:\n        return n\n    return fibonacci(n-1) + fibonacci(n-2)",
  "item_type": "code_snippet",
  "auto_categorize": true
}

Response:
{
  "success": true,
  "data": {
    "item": {...},
    "suggested_categories": [
      {"id": 5, "name": "Python", "confidence": 0.9},
      {"id": 12, "name": "Algorithms", "confidence": 0.7}
    ],
    "auto_detected_language": "python",
    "auto_generated_tags": ["#python", "#algorithm", "#code"]
  }
}
```

### 17. Toggle Favorite
```http
PUT /api/knowledge/{id}/favorite
Authorization: Bearer {token}
```

### 18. Toggle Archive
```http
PUT /api/knowledge/{id}/archive
Authorization: Bearer {token}
```

### 19. Mark as Reviewed
```http
PUT /api/knowledge/{id}/review
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": {
    "review_count": 4,
    "last_reviewed_at": "2025-11-23 10:30:00",
    "next_review_date": "2025-12-07"
  }
}
```

### 20. Clone Item ⭐ NEW
```http
POST /api/knowledge/{id}/clone
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": {
    "id": 456,
    "title": "Original Title (Copy)",
    ...
  }
}
```

### 21. Get Related Items ⭐ NEW
```http
GET /api/knowledge/{id}/related
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": [
    {
      "id": 124,
      "title": "Merge Sort",
      "item_type": "code_snippet",
      "category_id": 5,
      "tags": ["#python", "#algorithm", "#sorting"]
    }
  ]
}
```

### 22. Get Items Due for Review ⭐ NEW
```http
GET /api/knowledge/due-review
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": [
    {
      "id": 123,
      "title": "Binary Tree",
      "next_review_date": "2025-11-23",
      "review_count": 3
    }
  ]
}
```

---

## 🔄 Bulk Operations ⭐ NEW

### 23. Bulk Tag
```http
PUT /api/knowledge/bulk-tag
Authorization: Bearer {token}
Content-Type: application/json

{
  "item_ids": [123, 124, 125],
  "tags": ["#important", "#review-needed"]
}

Response:
{
  "success": true,
  "data": {"updated_count": 3},
  "message": "Tagged 3 items successfully"
}
```

### 24. Bulk Move
```http
PUT /api/knowledge/bulk-move
Authorization: Bearer {token}
Content-Type: application/json

{
  "item_ids": [123, 124, 125],
  "category_id": 8
}

Response:
{
  "success": true,
  "data": {"updated_count": 3},
  "message": "Moved 3 items successfully"
}
```

### 25. Bulk Delete
```http
DELETE /api/knowledge/bulk-delete
Authorization: Bearer {token}
Content-Type: application/json

{
  "item_ids": [123, 124, 125]
}

Response:
{
  "success": true,
  "data": {"deleted_count": 3},
  "message": "Deleted 3 items successfully"
}
```

---

## 📊 Statistics

### 26. Knowledge Statistics
```http
GET /api/knowledge/stats
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": {
    "total": 150,
    "by_type": {
      "notes": 80,
      "code_snippets": 45,
      "exercises": 15,
      "resource_links": 8,
      "attachments": 2
    },
    "favorites": 25,
    "archived": 10,
    "due_review": 5,
    "total_reviews": 450
  }
}
```

---

## 🎯 Auto-Detection Features

### Code Language Detection
When using quick-capture with `item_type=code_snippet`, the system automatically detects:
- Python: `def `, `import `, `class `
- JavaScript: `const `, `let `, `=>`, `function `
- Java: `public class`, `System.out`
- PHP: `<?php`, `namespace `, `::`
- Go: `func `, `package `
- C/C++: `#include`, `std::`
- SQL: `SELECT `, `INSERT `

### Auto-Categorization
Categories are suggested based on:
- Detected code language
- Content keywords
- URL patterns (e.g., leetcode.com → Interview Preparation)

Confidence score: 0.0 to 1.0

### Auto-Tagging
Tags are automatically generated for:
- **Language**: `#python`, `#javascript`, `#java`
- **Difficulty**: `#beginner`, `#intermediate`, `#advanced`
- **Topics**: `#algorithm`, `#interview`, `#database`, `#web`
- **Type**: `#code`, `#exercise`

---

## 🔄 Spaced Repetition Algorithm

Review intervals based on review count:
1. 1st review → 1 day
2. 2nd review → 3 days
3. 3rd review → 7 days
4. 4th review → 14 days
5. 5th review → 30 days
6. 6th review → 60 days
7. 7th+ review → 120 days

---

## ⚠️ Validation Rules

### Category
- `name`: required, max 255 chars
- `parent_id`: must exist in knowledge_categories
- `color`: must be HEX format (#RRGGBB)
- `icon`: max 50 chars
- `sort_order`: integer >= 0

### Knowledge Item
- `title`: required, max 500 chars
- `item_type`: required, must be one of: note|code_snippet|exercise|resource_link|attachment
- `category_id`: must exist in knowledge_categories
- `content`: required for notes and code snippets
- `code_language`: max 50 chars
- `url`: must be valid URL format
- `difficulty`: must be one of: easy|medium|hard

---

## 🚀 Usage Examples

### Example 1: Create Python Code Snippet
```bash
curl -X POST http://localhost:8000/api/knowledge/quick-capture \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "content": "def binary_search(arr, target):\n    left, right = 0, len(arr) - 1\n    while left <= right:\n        mid = (left + right) // 2\n        if arr[mid] == target:\n            return mid\n        elif arr[mid] < target:\n            left = mid + 1\n        else:\n            right = mid - 1\n    return -1",
    "item_type": "code_snippet",
    "auto_categorize": true
  }'
```

### Example 2: Get Category Tree
```bash
curl -X GET http://localhost:8000/api/knowledge/categories/tree \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Example 3: Search Python Code Snippets
```bash
curl -X GET "http://localhost:8000/api/knowledge?type=code_snippet&search=binary&sort_by=view_count" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## 📈 Phase 1 Summary

✅ **Completed Features:**
- Category CRUD with hierarchical structure
- Knowledge item CRUD (5 types)
- Quick capture with auto-detection
- Bulk operations (tag, move, delete)
- Spaced repetition system
- Auto-categorization
- Auto-tagging
- Related items suggestion
- Clone functionality
- Statistics

**Total Endpoints:** 26

**Next Phase:**
- Mobile Android UI
- Advanced search
- Import/Export
- AI-powered features
