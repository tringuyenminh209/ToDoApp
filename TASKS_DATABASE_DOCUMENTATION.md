# 📋 Tasks Database - Complete Documentation

## Overview

Hệ thống Tasks là phần backend quản lý công việc (to-do items) cho người dùng. Nó bao gồm các bảng database, model relationships, và REST API endpoints.

---

## 📊 Database Tables

### 1. **tasks** (Main Table)

**Migration:** `2025_10_02_042313_create_tasks_table.php`

#### Columns:

##### Basic Info
- `id`: Primary key
- `user_id`: Foreign key → users table (cascade delete)
- `project_id`: Foreign key → projects table (nullable, set null on delete)
- `learning_milestone_id`: Foreign key → learning_milestones table (nullable, cascade delete)
- `title`: VARCHAR(255) - Tên task
- `category`: ENUM('study', 'work', 'personal', 'other') - Phân loại task
- `description`: TEXT (nullable) - Mô tả chi tiết

##### Priority & Energy
- `priority`: TINYINT (1-5, default 3) - Độ ưu tiên (5 = cao nhất)
- `energy_level`: ENUM('low', 'medium', 'high') - Mức năng lượng cần thiết

##### Time Management
- `estimated_minutes`: INT (nullable) - Thời gian ước tính (phút)
- `deadline`: TIMESTAMP (nullable) - Hạn chót
- `scheduled_time`: **TIME** (nullable) - Thời gian bắt đầu dự kiến (HH:mm:ss)
  - **Note:** Đã thay đổi từ TIMESTAMP → TIME trong migration `2025_11_15_120000`

##### Status
- `status`: ENUM('pending', 'in_progress', 'completed', 'cancelled') - Trạng thái task

##### AI Features
- `ai_breakdown_enabled`: BOOLEAN (default false) - Task đã được AI phân tích

##### Deep Work Mode Features
- `requires_deep_focus`: BOOLEAN (default false) - Cần tập trung cao
- `allow_interruptions`: BOOLEAN (default true) - Cho phép gián đoạn
- `focus_difficulty`: INT (1-5, default 3) - Độ khó tập trung

##### Time Tracking Features
- `warmup_minutes`: INT (nullable) - Thời gian chuẩn bị trước task
- `cooldown_minutes`: INT (nullable) - Thời gian nghỉ sau task
- `recovery_minutes`: INT (nullable) - Thời gian phục hồi

##### Context Tracking
- `last_focus_at`: TIMESTAMP (nullable) - Lần cuối tập trung vào task này
- `total_focus_minutes`: INT (default 0) - Tổng thời gian đã tập trung
- `distraction_count`: INT (default 0) - Số lần bị phân tâm

##### Timestamps
- `created_at`: TIMESTAMP
- `updated_at`: TIMESTAMP

#### Indexes:
- `user_id, status`
- `project_id, status`
- `learning_milestone_id`
- `deadline`
- `priority`
- `user_id, created_at`
- `user_id, scheduled_time`

---

### 2. **subtasks**

**Migration:** `2025_10_02_042341_create_subtasks_table.php`

Bảng lưu các công việc con của task.

#### Columns:
- `id`: Primary key
- `task_id`: Foreign key → tasks (cascade delete)
- `title`: VARCHAR(255) - Tên subtask
- `is_completed`: BOOLEAN (default false) - Đã hoàn thành chưa
- `estimated_minutes`: INT (nullable) - Thời gian ước tính
- `sort_order`: INT (default 0) - Thứ tự sắp xếp
- `created_at`, `updated_at`

#### Indexes:
- `task_id, sort_order`
- `task_id, is_completed`

---

### 3. **task_tags** (Pivot Table)

**Migration:** `2025_10_02_044237_create_task_tags_table.php`

Bảng liên kết many-to-many giữa tasks và tags.

#### Columns:
- `id`: Primary key
- `task_id`: Foreign key → tasks (cascade delete)
- `tag_id`: Foreign key → tags (cascade delete)
- `created_at`, `updated_at`

#### Indexes:
- Unique constraint: `task_id, tag_id`
- Index: `tag_id`

---

### 4. **task_templates**

**Migration:** `2025_11_01_100002_create_task_templates_table.php`

Bảng lưu template tasks cho learning paths.

#### Columns:
- `id`: Primary key
- `milestone_template_id`: Foreign key → learning_milestone_templates (cascade delete)
- `title`: VARCHAR(255) - Tên template
- `description`: TEXT (nullable) - Mô tả
- `sort_order`: INT (default 0) - Thứ tự
- `estimated_minutes`: INT (nullable) - Thời gian ước tính
- `priority`: TINYINT (1-5, default 3) - Độ ưu tiên
- `resources`: JSON (nullable) - Tài liệu tham khảo (links, videos, etc.)
- `subtasks`: JSON (nullable) - Danh sách subtasks template
- `knowledge_items`: JSON (nullable) - Nội dung học tập (notes, code examples, exercises)
- `created_at`, `updated_at`

#### Indexes:
- `milestone_template_id, sort_order`

---

## 🔗 Model Relationships

**Model:** `backend/app/Models/Task.php`

### Relationships:

#### BelongsTo (N:1)
- `user()` → User model
- `project()` → Project model
- `learningMilestone()` → LearningMilestone model

#### HasMany (1:N)
- `subtasks()` → Subtask model (ordered by sort_order)
- `focusSessions()` → FocusSession model
- `knowledgeItems()` → KnowledgeItem model (source_task_id)
- `focusEnvironments()` → FocusEnvironment model
- `distractionLogs()` → DistractionLog model
- `contextSwitchesFrom()` → ContextSwitch model (from_task_id)
- `contextSwitchesTo()` → ContextSwitch model (to_task_id)

#### BelongsToMany (N:N)
- `tags()` → Tag model (through task_tags pivot)

---

## 🎯 Model Scopes

Query scopes available in Task model:

### Status Scopes:
- `byStatus($status)` - Filter by status
- `pending()` - Status = pending
- `inProgress()` - Status = in_progress
- `completed()` - Status = completed
- `cancelled()` - Status = cancelled

### Priority Scopes:
- `byPriority($priority)` - Filter by priority
- `highPriority()` - Priority >= 4
- `lowPriority()` - Priority <= 2

### Energy Scopes:
- `byEnergyLevel($level)` - Filter by energy level
- `highEnergy()` - Energy level = high
- `lowEnergy()` - Energy level = low

### Time Scopes:
- `withDeadline()` - Has deadline
- `overdue()` - Deadline passed and not completed
- `dueSoon($days = 3)` - Deadline within N days
- `withEstimatedTime()` - Has estimated_minutes
- `withoutEstimatedTime()` - No estimated_minutes

### User/Project Scopes:
- `byUser($userId)` - Filter by user
- `byProject($projectId)` - Filter by project
- `byMilestone($milestoneId)` - Filter by learning milestone

### AI Scope:
- `aiBreakdownEnabled()` - AI breakdown enabled = true

---

## 🔧 Model Accessors (Computed Attributes)

### Boolean Attributes:
- `is_overdue` - Task đã quá hạn chưa
- `is_due_soon` - Deadline trong vòng 3 ngày tới

### Display Attributes:
- `status_display` - Status tiếng Nhật (待機中, 進行中, 完了, キャンセル)
- `priority_display` - Priority tiếng Nhật (低, やや低, 中, やや高, 高)
- `energy_level_display` - Energy level tiếng Nhật (低, 中, 高)

### Time Attributes:
- `completion_percentage` - % hoàn thành dựa trên subtasks
- `estimated_hours` - Thời gian ước tính (giờ)
- `estimated_time_formatted` - Format: "2h 30m" hoặc "45m"
- `days_until_deadline` - Số ngày còn lại đến deadline

---

## 🛠 Model Helper Methods

### Status Management:
- `markAsCompleted()` - Đánh dấu hoàn thành (auto-complete subtasks)
- `markAsInProgress()` - Đánh dấu đang làm
- `markAsPending()` - Đánh dấu chờ làm
- `markAsCancelled()` - Hủy task

### Status Checks:
- `isCompleted()` - Đã hoàn thành?
- `isInProgress()` - Đang làm?
- `isPending()` - Chờ làm?
- `isCancelled()` - Đã hủy?
- `isHighPriority()` - Priority >= 4?
- `isLowPriority()` - Priority <= 2?
- `requiresHighEnergy()` - Energy = high?
- `requiresLowEnergy()` - Energy = low?
- `canBeStarted()` - Có thể bắt đầu? (pending & not overdue)
- `needsAttention()` - Cần chú ý? (overdue or due soon)

### Subtask Management:
- `getTotalEstimatedTime()` - Tổng thời gian (task + subtasks)
- `getNextSubtask()` - Subtask tiếp theo chưa hoàn thành
- `getCompletedSubtasksCount()` - Số subtasks đã hoàn thành
- `getPendingSubtasksCount()` - Số subtasks chưa hoàn thành
- `getTotalSubtasksCount()` - Tổng số subtasks
- `hasSubtasks()` - Có subtasks không?
- `getProgressSummary()` - Object: {total, completed, pending, percentage}

### Tag Management:
- `attachTag($tagId)` - Gắn tag vào task
- `detachTag($tagId)` - Xóa tag khỏi task
- `syncTags($tagIds)` - Sync danh sách tags
- `getTagNames()` - Array tên các tags

---

## 🌐 API Endpoints

**Controller:** `backend/app/Http/Controllers/TaskController.php`

### 1. **GET /api/tasks**
Lấy danh sách tasks của user.

**Query Parameters:**
- `status` - Filter by status
- `priority` - Filter by priority
- `energy_level` - Filter by energy level
- `project_id` - Filter by project
- `milestone_id` - Filter by learning milestone
- `overdue` - Chỉ tasks quá hạn
- `due_soon` - Tasks sắp đến hạn (default 3 days)
- `search` - Tìm kiếm trong title/description
- `sort_by` - Field để sort (created_at, priority, deadline, title, scheduled_time)
- `sort_order` - asc/desc
- `per_page` - Số items per page (max 100, default 20)

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [/* tasks with relations */],
    "total": 50
  },
  "message": "タスク一覧を取得しました"
}
```

**Default Sorting:**
- Tasks có scheduled_time hoặc deadline → sort by earliest date
- Tasks không có date → cuối danh sách
- Cùng date → sort by priority (cao → thấp)
- Cùng priority → sort by created_at (mới → cũ)

---

### 2. **POST /api/tasks**
Tạo task mới.

**Request Body:**
```json
{
  "title": "Task title (required)",
  "category": "study|work|personal|other (required)",
  "description": "Task description (optional)",
  "priority": 1-5 (optional, default 3),
  "energy_level": "low|medium|high (optional, default medium)",
  "estimated_minutes": 60 (optional),
  "deadline": "2025-11-20 15:30:00" (optional),
  "scheduled_time": "09:15:00" (optional, TIME format),
  "status": "pending|in_progress|completed|cancelled (optional, default pending)",
  "project_id": 1 (optional),
  "learning_milestone_id": 2 (optional),
  "ai_breakdown_enabled": false (optional),
  "requires_deep_focus": false (optional),
  "allow_interruptions": true (optional),
  "focus_difficulty": 1-5 (optional),
  "warmup_minutes": 10 (optional),
  "cooldown_minutes": 5 (optional),
  "recovery_minutes": 15 (optional)
}
```

**Response:**
```json
{
  "success": true,
  "data": {/* created task */},
  "message": "タスクを作成しました"
}
```

---

### 3. **GET /api/tasks/{id}**
Lấy chi tiết 1 task.

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Task title",
    /* ... all fields */,
    "project": {/* project object */},
    "learning_milestone": {/* milestone object */},
    "subtasks": [/* subtasks array */],
    "tags": [/* tags array */],
    "knowledge_items": [/* knowledge items */]
  },
  "message": "タスク詳細を取得しました"
}
```

---

### 4. **PUT /api/tasks/{id}**
Cập nhật task.

**Request Body:** Same as POST (all fields optional)

**Response:**
```json
{
  "success": true,
  "data": {/* updated task */},
  "message": "タスクを更新しました"
}
```

---

### 5. **DELETE /api/tasks/{id}**
Xóa task.

**Response:**
```json
{
  "success": true,
  "message": "タスクを削除しました"
}
```

---

### 6. **PUT /api/tasks/{id}/complete**
Đánh dấu task hoàn thành.

**Response:**
```json
{
  "success": true,
  "data": {/* completed task */},
  "message": "タスクを完了しました"
}
```

---

### 7. **PUT /api/tasks/{id}/start**
Bắt đầu task (status → in_progress).

**Response:**
```json
{
  "success": true,
  "data": {/* updated task */},
  "message": "タスクを開始しました"
}
```

---

### 8. **GET /api/tasks/stats**
Lấy thống kê tasks của user.

**Response:**
```json
{
  "success": true,
  "data": {
    "total": 50,
    "pending": 20,
    "in_progress": 5,
    "completed": 23,
    "cancelled": 2,
    "overdue": 3,
    "due_soon": 7,
    "high_priority": 10,
    "total_estimated_hours": 125.5
  },
  "message": "タスク統計を取得しました"
}
```

---

### 9. **GET /api/tasks/by-priority/{priority}**
Lấy tasks theo priority (1-5).

**Response:** Same as GET /api/tasks

---

### 10. **GET /api/tasks/overdue**
Lấy danh sách tasks quá hạn.

**Response:** Same as GET /api/tasks

---

### 11. **GET /api/tasks/due-soon**
Lấy tasks sắp đến hạn (trong 3 ngày).

**Response:** Same as GET /api/tasks

---

## 🔐 Authorization

Tất cả endpoints đều yêu cầu authentication (middleware: auth:sanctum).

Mỗi user chỉ được access tasks của chính họ:
- API tự động filter tasks theo `user_id` của user đang login
- Khi tạo task mới, `user_id` được set tự động từ Auth::id()

---

## 📝 Important Notes

### 1. **scheduled_time vs deadline:**
- `scheduled_time` (TIME): Giờ bắt đầu task trong ngày (e.g., 09:15:00)
- `deadline` (TIMESTAMP): Ngày + giờ deadline (e.g., 2025-11-20 15:30:00)

### 2. **Migration History:**
- `scheduled_time` ban đầu là TIMESTAMP
- Đã thay đổi thành TIME trong migration `2025_11_15_120000`
- Lý do: Để align với study_schedules table và chỉ lưu giờ (không lưu ngày)

### 3. **Relationship với Timeline:**
- Tasks được hiển thị trong Calendar timeline view
- Filter theo `scheduled_time` để hiển thị tasks theo giờ
- Được kết hợp với Study Schedules và Timetable Classes trong timeline

### 4. **Auto-completion:**
- Khi task được mark completed, tất cả subtasks tự động completed
- Completion percentage tính dựa trên subtasks completed / total subtasks

### 5. **Soft Features:**
Tasks có nhiều tính năng nâng cao chưa được sử dụng hết:
- Deep Work Mode
- Focus tracking
- Distraction logging
- Context switching
- Warmup/cooldown/recovery times

---

## 🔄 Integration Points

### With Study Schedules:
- Tasks có thể link đến learning_milestone_id
- Hiển thị cùng nhau trong timeline view
- Cùng format scheduled_time (TIME type)

### With Timetable Classes:
- Tất cả 3 loại (Tasks, Study Schedules, Timetable Classes) hiển thị trong Timeline
- Được convert sang unified TimelineItem format
- Timeline API: `/api/study-schedules/timeline`

### With Projects:
- Tasks có thể thuộc về project
- Cascade: project deleted → task.project_id = NULL

### With Tags:
- Many-to-many relationship
- Có thể tag tasks để phân loại, tìm kiếm

---

## 🎨 Category Colors (Frontend)

Theo TimelineAdapter.kt:
- **study/learning/学習** → Primary color (xanh dương)
- **work/仕事** → Info color (xanh lam)
- **personal/個人** → Accent color (tím)
- **project/プロジェクト** → Warning color (vàng)
- **class** → Primary color (xanh dương) với badge "授業"
- **other** → Text muted (xám)

---

## 📚 Related Files

### Backend:
- **Migrations:** `backend/database/migrations/2025_10_02_042313_create_tasks_table.php`
- **Model:** `backend/app/Models/Task.php`
- **Controller:** `backend/app/Http/Controllers/TaskController.php`
- **Routes:** `backend/routes/api.php` (prefix: /api/tasks)

### Android:
- **Model:** `mobileandroid/app/src/main/java/ecccomp/s2240788/mobile_android/data/models/Task.kt`
- **API Service:** `mobileandroid/app/src/main/java/ecccomp/s2240788/mobile_android/data/api/ApiService.kt`
- **Adapter:** `mobileandroid/app/src/main/java/ecccomp/s2240788/mobile_android/ui/adapters/TimelineAdapter.kt`
- **Layout:** `mobileandroid/app/src/main/res/layout/item_timeline_task.xml`

---

## ✅ Summary

Hệ thống Tasks là một phần quan trọng của ứng dụng ToDoApp:

**Database:** 4 tables (tasks, subtasks, task_tags, task_templates)
**API Endpoints:** 11 endpoints
**Model Methods:** 50+ helper methods và accessors
**Features:** Priority, Energy, Deep Focus, Time tracking, AI breakdown, Subtasks, Tags

Tasks được tích hợp với:
- Projects (optional grouping)
- Learning Paths (via milestones)
- Timeline View (cùng với Study Schedules và Timetable Classes)
- Tags (categorization)
- Focus tracking (deep work features)
