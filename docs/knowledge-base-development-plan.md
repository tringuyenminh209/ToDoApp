# Knowledge Base Development Plan
**Hệ thống Quản lý Kiến thức cho Học sinh IT**

Last Updated: 2025-11-23
Status: Planning Phase

---

## 📋 Tổng quan

Hệ thống Knowledge Base được thiết kế để giúp học sinh IT:
- Lưu trữ và tổ chức kiến thức học được
- Ôn tập theo phương pháp Spaced Repetition
- Quản lý code snippets, notes, exercises
- Chuẩn bị cho kỳ thi và phỏng vấn
- Xây dựng thư viện kiến thức cá nhân

---

## 🎯 Mục tiêu chính

### 1. Use Cases chính
- **Học lập trình**: Lưu code snippets, concepts, algorithms
- **Chuẩn bị thi/phỏng vấn**: Câu hỏi, bài tập, flashcards
- **Tài liệu tham khảo**: Links, tutorials, cheat sheets
- **Dự án cá nhân**: Project notes, architecture decisions

### 2. Tính năng cốt lõi
- ✅ Phân loại đa cấp (hierarchical categories)
- ✅ 5 loại content: Note, Code Snippet, Exercise, Resource Link, Attachment
- ✅ Spaced Repetition System (SRS)
- ✅ Full-text search
- ✅ Tags và favorites
- ✅ Link với Learning Paths và Tasks

---

## 🗂️ Cấu trúc Categories - Kết hợp 3 Phương án

### Phương án 1: Technology-based (Chính)
**Mục đích**: Phân loại theo ngôn ngữ/công nghệ - phù hợp cho học IT

```
📁 Programming Languages
  ├─ 🐍 Python
  │   ├─ Basics
  │   ├─ Data Structures
  │   ├─ Libraries (pandas, numpy, etc.)
  │   └─ Interview Questions
  ├─ ☕ Java
  │   ├─ Core Java
  │   ├─ Spring Framework
  │   └─ Design Patterns
  ├─ 🔷 JavaScript
  │   ├─ ES6+ Features
  │   ├─ React.js
  │   ├─ Node.js
  │   └─ TypeScript
  ├─ 🐘 PHP
  │   ├─ Laravel
  │   └─ Best Practices
  ├─ 🔷 C++
  │   ├─ STL
  │   └─ Memory Management
  └─ 🐹 Go
      ├─ Concurrency
      └─ Web Services

📁 Computer Science Fundamentals
  ├─ 📊 Data Structures
  │   ├─ Arrays & Strings
  │   ├─ Linked Lists
  │   ├─ Trees & Graphs
  │   ├─ Hash Tables
  │   └─ Heaps & Stacks
  ├─ ⚡ Algorithms
  │   ├─ Sorting & Searching
  │   ├─ Dynamic Programming
  │   ├─ Greedy Algorithms
  │   └─ Graph Algorithms
  ├─ 🗄️ Database Theory
  │   ├─ SQL Fundamentals
  │   ├─ Normalization
  │   ├─ Indexing
  │   └─ Transactions
  ├─ 🌐 Networks
  │   ├─ TCP/IP
  │   ├─ HTTP/HTTPS
  │   └─ DNS & Routing
  └─ 💻 Operating Systems
      ├─ Process Management
      ├─ Memory Management
      └─ File Systems

📁 Web Development
  ├─ Frontend
  │   ├─ HTML & CSS
  │   ├─ JavaScript
  │   ├─ Frameworks (React, Vue)
  │   └─ UI/UX Best Practices
  ├─ Backend
  │   ├─ REST APIs
  │   ├─ Authentication
  │   ├─ Caching
  │   └─ Microservices
  ├─ DevOps
  │   ├─ CI/CD
  │   ├─ Monitoring
  │   └─ Cloud Services
  └─ Security
      ├─ OWASP Top 10
      ├─ Authentication & Authorization
      └─ Encryption

📁 Tools & Workflow
  ├─ 🔧 Git & Version Control
  │   ├─ Basic Commands
  │   ├─ Branching Strategies
  │   └─ Merge Conflicts
  ├─ 🐳 Docker
  │   ├─ Dockerfile
  │   ├─ Docker Compose
  │   └─ Container Orchestration
  ├─ 🐧 Linux Commands
  │   ├─ File Operations
  │   ├─ Process Management
  │   └─ Shell Scripting
  ├─ 📝 IDEs & Editors
  │   ├─ VS Code Tips
  │   ├─ IntelliJ IDEA
  │   └─ Vim
  └─ 🧪 Testing
      ├─ Unit Testing
      ├─ Integration Testing
      └─ Test-Driven Development

📁 Interview Preparation
  ├─ 💻 Coding Challenges
  │   ├─ LeetCode Easy
  │   ├─ LeetCode Medium
  │   ├─ LeetCode Hard
  │   └─ HackerRank
  ├─ 🏗️ System Design
  │   ├─ Scalability
  │   ├─ Load Balancing
  │   └─ Database Design
  ├─ 🗣️ Behavioral Questions
  │   ├─ STAR Method
  │   └─ Common Questions
  └─ 📊 Complexity Analysis
      ├─ Time Complexity
      └─ Space Complexity

📁 Projects & Ideas
  ├─ 💡 Project Ideas
  ├─ 📝 Project Notes
  ├─ 🏗️ Architecture Decisions
  └─ 🔧 Code Snippets Library
```

### Phương án 2: Course-based (Phụ - User tự tạo)
**Mục đích**: Phân loại theo môn học/khóa học - linh hoạt cho từng trường

```
📁 Academic
  ├─ 📚 Semester 1
  │   ├─ Programming Fundamentals (C++)
  │   ├─ Computer Architecture
  │   ├─ Math for CS
  │   └─ Introduction to IT
  ├─ 📚 Semester 2
  │   ├─ Data Structures & Algorithms
  │   ├─ Database Systems
  │   ├─ Object-Oriented Programming (Java)
  │   └─ Web Development
  ├─ 📚 Semester 3
  │   ├─ Software Engineering
  │   ├─ Operating Systems
  │   ├─ Computer Networks
  │   └─ Mobile App Development
  └─ 📚 Semester 4+
      ├─ AI & Machine Learning
      ├─ Cloud Computing
      └─ Cybersecurity

📁 Online Courses
  ├─ 🎓 Udemy Courses
  ├─ 🎓 Coursera Courses
  ├─ 🎓 edX Courses
  └─ 🎓 YouTube Tutorials

📁 Certifications
  ├─ ☁️ AWS Certifications
  ├─ 🔵 Azure Certifications
  ├─ 📊 Google Cloud
  └─ 🐳 Docker Certified
```

### Phương án 3: Content-type based (Ngang - Dùng Tags)
**Mục đích**: Phân loại theo mục đích sử dụng - dùng tags

```
Tags hệ thống:
#quick-reference    - Cheat sheets, command lists
#learning-notes     - Concepts, tutorials, explanations
#code-library       - Reusable code, templates, boilerplates
#problem-solving    - Bug fixes, debugging tips, solutions
#resources          - Links, books, videos
#exam-prep          - For exams and tests
#interview-prep     - For job interviews
#project-related    - From personal projects
#todo-review        - Need to review
#important          - High priority
#beginner           - Beginner level
#intermediate       - Intermediate level
#advanced           - Advanced level
```

---

## 📊 Database Schema

### Tables
Hiện có 3 bảng chính:

#### 1. `knowledge_categories`
```sql
- id, user_id, parent_id (hierarchical)
- name, description, sort_order
- color, icon
- item_count
- timestamps
```

#### 2. `knowledge_items`
```sql
- id, user_id, category_id
- title, item_type (note/code_snippet/exercise/resource_link/attachment)
- content, code_language, url
- question, answer, difficulty
- attachment_path, attachment_mime, attachment_size
- tags (JSON), learning_path_id, source_task_id
- review_count, last_reviewed_at, next_review_date, retention_score
- ai_summary
- view_count, is_favorite, is_archived
- timestamps
```

#### 3. `knowledge_item_tags`
```sql
- id, knowledge_item_id, tag_name
- created_at
```

---

## 🎨 Tính năng cần phát triển

### Phase 1: Core Features (Backend) ✅

#### A. KnowledgeCategoryController
```php
POST   /api/knowledge/categories              // Create category
GET    /api/knowledge/categories              // List all (tree structure)
GET    /api/knowledge/categories/{id}         // Get details
PUT    /api/knowledge/categories/{id}         // Update
DELETE /api/knowledge/categories/{id}         // Delete
POST   /api/knowledge/categories/{id}/move    // Move to new parent
GET    /api/knowledge/categories/tree         // Get hierarchical tree
```

**Features**:
- ✅ CRUD operations
- ✅ Hierarchical structure support (parent-child)
- ✅ Auto-count items in category
- ✅ Reorder categories (sort_order)
- ✅ Color and icon customization
- ✅ Validation: prevent circular references

#### B. Enhanced KnowledgeController
```php
// Already exists:
GET    /api/knowledge                  // List items
POST   /api/knowledge                  // Create item
GET    /api/knowledge/{id}             // Get details
PUT    /api/knowledge/{id}             // Update
DELETE /api/knowledge/{id}             // Delete
PUT    /api/knowledge/{id}/favorite    // Toggle favorite
PUT    /api/knowledge/{id}/archive     // Toggle archive
PUT    /api/knowledge/{id}/review      // Mark reviewed
GET    /api/knowledge/stats            // Statistics

// To add:
POST   /api/knowledge/quick-capture    // Quick save with template
GET    /api/knowledge/due-review       // Items due for review today
POST   /api/knowledge/{id}/clone       // Duplicate item
PUT    /api/knowledge/bulk-tag         // Add tags to multiple items
PUT    /api/knowledge/bulk-move        // Move items to new category
POST   /api/knowledge/import           // Import from external sources
GET    /api/knowledge/export           // Export to markdown/json
```

**New Features**:
- ✅ Quick capture templates
- ✅ Bulk operations (tag, move, delete)
- ✅ Auto-categorization suggestions
- ✅ Smart tag suggestions based on content
- ✅ Import/Export functionality
- ✅ Duplicate/Clone items
- ✅ Related items suggestions (based on tags, category)

#### C. Spaced Repetition System
```php
GET    /api/knowledge/review/today     // Items to review today
GET    /api/knowledge/review/upcoming  // Upcoming reviews (7 days)
POST   /api/knowledge/{id}/review-result // Mark review + quality (1-5)
GET    /api/knowledge/review/stats     // Review statistics
```

**Algorithm**:
```
Quality 1-2 (Hard):     Review in 1 day
Quality 3 (Good):       Review in 3 days
Quality 4 (Easy):       Review in 7 days
Quality 5 (Very Easy):  Review in 14 days

After first review, use progressive intervals:
[1, 3, 7, 14, 30, 60, 120, 240, 365] days
```

#### D. Search & Discovery
```php
GET    /api/knowledge/search?q=...          // Full-text search
GET    /api/knowledge/related/{id}          // Related items
GET    /api/knowledge/popular               // Most viewed
GET    /api/knowledge/recent                // Recently added/updated
GET    /api/knowledge/tags                  // All tags with counts
GET    /api/knowledge/by-tag/{tag}          // Items by tag
```

#### E. Default Categories Seeder
```php
// KnowledgeCategorySeeder.php
Seeds default categories:
- Programming Languages (with subcategories)
- Computer Science Fundamentals
- Web Development
- Tools & Workflow
- Interview Preparation
- Projects & Ideas
```

---

### Phase 2: Smart Features (AI Integration) 🔮

#### A. Auto-categorization
```php
POST /api/knowledge/suggest-category
Request: { "title": "...", "content": "...", "item_type": "..." }
Response: { "suggested_categories": [...], "confidence": 0.85 }

Examples:
- Code snippet with "def" → Python category
- URL contains "leetcode.com" → Interview Preparation
- Content mentions "Docker" → Tools > Docker
```

#### B. Auto-tagging
```php
POST /api/knowledge/suggest-tags
Request: { "content": "..." }
Response: { "suggested_tags": ["#algorithm", "#medium", "#interview-prep"] }
```

#### C. AI Summary
```php
POST /api/knowledge/{id}/generate-summary
- Auto-generate summary for long notes
- Extract key points from code snippets
- Summarize resource links content
```

#### D. Smart Review
```php
- Predict which items user might forget
- Suggest related items to review together
- Group similar items for batch review
```

---

### Phase 3: Mobile App (Android) 📱

#### A. Màn hình chính - Knowledge Base
```kotlin
KnowledgeActivity.kt
- Tab 1: Categories (TreeView)
- Tab 2: All Items (List with filters)
- Tab 3: Review (Due items + statistics)
- Tab 4: Search & Discover

Features:
- Quick action buttons: [+ Code] [+ Note] [+ Q&A]
- Filter by: type, category, tags, favorites
- Sort by: date, views, reviews
- Search with highlighting
```

#### B. Category Management
```kotlin
CategoryListActivity.kt
- Show hierarchical categories
- Create/Edit/Delete categories
- Drag to reorder
- Color picker for customization
- Icon selector

CategoryDetailActivity.kt
- List items in category
- Category statistics
- Quick filters
```

#### C. Item Detail & Editor
```kotlin
KnowledgeDetailActivity.kt
- Display based on item_type:
  * Note: Markdown rendering
  * Code: Syntax highlighting
  * Exercise: Show question/answer
  * Link: Preview + open button
  * Attachment: Download/View

KnowledgeEditorActivity.kt
- Templates based on type
- Rich text editor for notes
- Code editor with syntax highlighting
- Markdown preview
- Tag selector with autocomplete
- Category picker (tree view)
```

#### D. Review System
```kotlin
ReviewActivity.kt
- Flashcard mode for exercises
- Code snippet quiz
- Spaced repetition algorithm
- Progress tracking
- Review statistics

Features:
- Swipe gestures (Hard/Good/Easy)
- Show answer button
- Skip/Mark for later
- Daily streak tracking
```

#### E. Quick Capture
```kotlin
QuickCaptureActivity.kt
- Template selector
- Minimal fields for fast input
- Auto-suggest category based on:
  * Current learning path
  * Recent categories
  * Content analysis
```

---

### Phase 4: Advanced Features 🚀

#### A. Collaboration (Future)
```
- Share knowledge items with classmates
- Public/Private categories
- Import from shared collections
- Collaborative notes
```

#### B. Export & Backup
```
- Export to Markdown files
- Export to Notion/Obsidian format
- Auto-backup to cloud
- Import from other apps
```

#### C. Analytics
```
- Learning patterns analysis
- Most reviewed topics
- Knowledge gaps identification
- Study time tracking
```

#### D. Integration
```
- Link with Timetable (auto-create categories from classes)
- Link with Learning Paths (auto-save from milestones)
- Link with Tasks (save code from completed tasks)
- Link with Cheat Code (save to personal knowledge)
```

---

## 🗓️ Implementation Roadmap

### Week 1: Backend Foundation
- [x] Database schema analysis
- [ ] Create KnowledgeCategoryController
- [ ] Add missing routes for categories
- [ ] Create default categories seeder
- [ ] Add category tree endpoint
- [ ] Unit tests for category operations

### Week 2: Enhanced Features
- [ ] Add bulk operations to KnowledgeController
- [ ] Implement quick capture endpoint
- [ ] Add related items suggestion
- [ ] Implement import/export
- [ ] Add search improvements
- [ ] Unit tests

### Week 3: Mobile UI (Categories)
- [ ] Create Category list screen
- [ ] Create Category detail screen
- [ ] Create Category tree view
- [ ] Add category picker dialog
- [ ] Create/Edit category forms
- [ ] Category color/icon pickers

### Week 4: Mobile UI (Items)
- [ ] Knowledge list screen with filters
- [ ] Knowledge detail screen (all types)
- [ ] Knowledge editor (all types)
- [ ] Code syntax highlighting
- [ ] Markdown rendering
- [ ] Tag management

### Week 5: Review System
- [ ] Review algorithm implementation
- [ ] Review screen UI
- [ ] Flashcard mode
- [ ] Review statistics
- [ ] Notification for due reviews

### Week 6: Quick Capture & Polish
- [ ] Quick capture templates
- [ ] Auto-categorization
- [ ] Auto-tagging
- [ ] Search & discovery
- [ ] Polish UI/UX
- [ ] Bug fixes

### Week 7-8: Testing & Refinement
- [ ] Integration testing
- [ ] User testing
- [ ] Performance optimization
- [ ] Documentation
- [ ] Release v1.0

---

## 📝 API Design Examples

### Category Endpoints

#### 1. Get Category Tree
```http
GET /api/knowledge/categories/tree

Response:
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Programming Languages",
      "icon": "code",
      "color": "#0FA968",
      "item_count": 125,
      "children": [
        {
          "id": 2,
          "name": "Python",
          "icon": "python",
          "color": "#3776AB",
          "item_count": 45,
          "children": [
            {
              "id": 3,
              "name": "Basics",
              "item_count": 12,
              "children": []
            }
          ]
        }
      ]
    }
  ]
}
```

#### 2. Create Category
```http
POST /api/knowledge/categories

Request:
{
  "name": "React.js",
  "parent_id": 5,  // JavaScript category
  "description": "React framework notes",
  "color": "#61DAFB",
  "icon": "react",
  "sort_order": 1
}

Response:
{
  "success": true,
  "data": {
    "id": 15,
    "name": "React.js",
    "parent_id": 5,
    ...
  },
  "message": "Category created successfully"
}
```

#### 3. Move Category
```http
POST /api/knowledge/categories/15/move

Request:
{
  "new_parent_id": 8,  // Move to different parent
  "sort_order": 2
}
```

### Item Endpoints

#### 4. Quick Capture
```http
POST /api/knowledge/quick-capture

Request:
{
  "template": "code_snippet",
  "content": "def fibonacci(n):\n    ...",
  "auto_categorize": true
}

Response:
{
  "success": true,
  "data": {
    "id": 234,
    "title": "Fibonacci Function",  // Auto-generated
    "item_type": "code_snippet",
    "code_language": "python",  // Auto-detected
    "category_id": 3,  // Auto-suggested: Python > Basics
    "tags": ["#algorithm", "#recursion"],  // Auto-tagged
    "suggested_categories": [
      {"id": 3, "name": "Python > Basics", "confidence": 0.9},
      {"id": 42, "name": "Algorithms", "confidence": 0.7}
    ]
  }
}
```

#### 5. Search with Filters
```http
GET /api/knowledge/search?q=binary+tree&type=code_snippet&tags=algorithm,interview&category=10

Response:
{
  "success": true,
  "data": [
    {
      "id": 156,
      "title": "Binary Tree Traversal",
      "item_type": "code_snippet",
      "code_language": "python",
      "content": "...",
      "tags": ["#algorithm", "#interview", "#tree"],
      "category": {
        "id": 10,
        "name": "Data Structures > Trees",
        "breadcrumb": "CS Fundamentals > Data Structures > Trees"
      },
      "relevance_score": 0.95,
      "last_reviewed_at": "2025-11-20",
      "review_count": 3
    }
  ],
  "total": 5,
  "filters_applied": {
    "search": "binary tree",
    "type": "code_snippet",
    "tags": ["algorithm", "interview"],
    "category_id": 10
  }
}
```

---

## 🎯 Success Metrics

### User Engagement
- [ ] 80%+ users create at least 1 category
- [ ] Average 10+ knowledge items per user per month
- [ ] 60%+ daily active users access knowledge base
- [ ] Average 5+ reviews per day per user

### Content Quality
- [ ] 70%+ items have tags
- [ ] 50%+ items linked to learning paths or tasks
- [ ] 40%+ code snippets have proper syntax highlighting
- [ ] Average 3+ items per category

### Review System
- [ ] 80%+ users complete daily reviews
- [ ] Average retention score > 3.5
- [ ] Review completion rate > 75%

---

## 🔧 Technical Considerations

### Performance
- Implement pagination for large lists
- Cache category tree structure
- Index search fields properly
- Optimize full-text search queries

### Security
- Validate user ownership for all operations
- Sanitize markdown content (XSS prevention)
- Limit file upload sizes
- Rate limiting for AI features

### Data Migration
- Provide migration from other apps (Notion, Evernote)
- Export to standard formats (Markdown, JSON)
- Backup and restore functionality

---

## 📚 References

### Similar Apps to Learn From
- Notion - Hierarchical organization
- Obsidian - Markdown + linking
- Anki - Spaced repetition
- Quizlet - Flashcards
- Stack Overflow - Code snippets
- Gist - Code sharing

### Technologies
- Backend: Laravel 11, MySQL 8.0
- Mobile: Kotlin, Jetpack Compose
- Markdown: CommonMark
- Syntax Highlighting: highlight.js / Prism
- Search: MySQL Full-Text Search / ElasticSearch (future)

---

## 📞 Support & Documentation

### User Guide (To Create)
- [ ] How to organize knowledge effectively
- [ ] Best practices for tagging
- [ ] Using spaced repetition
- [ ] Quick capture tips
- [ ] Import/Export guide

### Developer Guide
- [ ] API documentation
- [ ] Database schema
- [ ] Seeder documentation
- [ ] Testing guide

---

**Next Steps**: Implement KnowledgeCategoryController and default seeder
