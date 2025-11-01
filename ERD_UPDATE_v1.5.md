# ERD Diagram Update v1.5

## Tổng Quan

Đã cập nhật ERD diagram từ v1.4 lên v1.5 để phản ánh các thay đổi mới nhất trong database schema.

## Các Thay Đổi Chính

### 1. Timetable System (Mới)

Thêm 3 bảng mới cho hệ thống thời khóa biểu:

#### `timetable_classes` - Lớp học
- Lưu thông tin các tiết học cố định
- Các trường chính:
  - `name`: Tên lớp học
  - `day`: Thứ trong tuần (enum)
  - `period`: Tiết học (1-10)
  - `start_time`, `end_time`: Thời gian
  - `room`, `instructor`: Phòng học và giảng viên
  - `color`, `icon`: Tùy chỉnh giao diện
  - `learning_path_id`: Liên kết với learning path (optional)

#### `timetable_class_weekly_contents` - Nội dung theo tuần
- Lưu nội dung riêng cho từng tuần của mỗi lớp học
- Các trường chính:
  - `timetable_class_id`: ID lớp học
  - `year`, `week_number`: Năm và số tuần
  - `week_start_date`: Ngày bắt đầu tuần (thứ Hai)
  - `title`: Tiêu đề tuần
  - `content`: Nội dung/chủ đề
  - `homework`: Bài tập về nhà
  - `notes`: Ghi chú
  - `status`: Trạng thái (scheduled, completed, cancelled)
- Unique constraint: `(timetable_class_id, year, week_number)`

#### `timetable_studies` - Bài tập/Ôn tập
- Quản lý bài tập và ôn tập liên quan đến lớp học
- Các trường chính:
  - `title`, `description`: Tiêu đề và mô tả
  - `type`: Loại (homework, review, exam, project)
  - `due_date`: Hạn nộp
  - `priority`: Độ ưu tiên (1-5)
  - `status`: Trạng thái (pending, in_progress, completed)
  - `timetable_class_id`: Liên kết với lớp học (optional)
  - `task_id`: Liên kết với task system (optional)

### 2. Tasks Table - Thêm Category

**Trường mới**: `category`
- Type: ENUM('study', 'work', 'personal', 'other')
- Default: 'other'
- Vị trí: Sau trường `title`
- Mục đích: Phân loại task để dễ quản lý và hiển thị

### 3. Daily Checkins Table - Mở rộng

**Các trường mới**:
- `mood` (enum): 'excellent', 'good', 'average', 'poor', 'terrible'
  - Thay thế cho `mood_score` (giữ lại để tương thích)
- `sleep_hours` (decimal 4,2): Số giờ ngủ
- `stress_level` (enum): 'low', 'medium', 'high'
- `priorities` (json): Danh sách ưu tiên trong ngày
- `goals` (json): Danh sách mục tiêu trong ngày
- `notes` (text): Ghi chú chung

**Giữ lại để tương thích**:
- `mood_score`: Điểm mood cũ (1-5)
- `schedule_note`: Ghi chú lịch trình cũ

### 4. Daily Reviews Table - Mở rộng

**Các trường mới**:
- `mood` (enum): Tâm trạng (same as daily_checkins)
- `focus_time_score` (tinyint): Điểm thời gian tập trung (1-10)
- `task_completion_score` (tinyint): Điểm hoàn thành task (1-10)
- `goal_achievement_score` (tinyint): Điểm đạt mục tiêu (1-10)
- `work_life_balance_score` (tinyint): Điểm cân bằng công việc-cuộc sống (1-10)
- `achievements` (json): Danh sách thành tựu
- `gratitude` (json): Danh sách điều biết ơn
- `challenges` (json): Danh sách thách thức
- `lessons_learned` (json): Danh sách bài học
- `notes` (text): Ghi chú chung

**Cập nhật**:
- `productivity_score`: Comment đổi từ (1-5) thành (1-10)

**Giữ lại để tương thích**:
- `gratitude_note`: Ghi chú biết ơn cũ
- `challenges_faced`: Ghi chú thách thức cũ

## Relationships Mới

### Timetable System
```
users (1) → (∞) timetable_classes
users (1) → (∞) timetable_studies
timetable_classes (1) → (∞) timetable_class_weekly_contents
timetable_classes (1) → (∞) timetable_studies
timetable_classes (∞) → (1) learning_paths (optional)
timetable_studies (∞) → (1) tasks (optional)
timetable_studies (∞) → (1) timetable_classes (optional)
```

## Tổng Số Bảng

**Trước (v1.4)**: 21 bảng
**Sau (v1.5)**: 24 bảng (+3 bảng timetable)

### Danh Sách Đầy Đủ:
1. users
2. user_profiles
3. user_settings
4. tags
5. projects
6. tasks *(updated)*
7. subtasks
8. task_tags
9. focus_sessions
10. daily_checkins *(updated)*
11. daily_reviews *(updated)*
12. ai_suggestions
13. ai_summaries
14. ai_interactions
15. user_stats
16. performance_metrics
17. notifications
18. activity_logs
19. learning_paths
20. learning_milestones
21. knowledge_categories
22. knowledge_items
23. knowledge_item_tags
24. **timetable_classes** *(new)*
25. **timetable_class_weekly_contents** *(new)*
26. **timetable_studies** *(new)*

## Sử Dụng ERD

### Xem trên dbdiagram.io

1. Truy cập: https://dbdiagram.io/d
2. Click "Import" → "From DBML"
3. Copy nội dung file `docs/erd_diagram_dol_leaf_v1.4.dbml`
4. Paste và click "Import"

### Tính Năng Hữu Ích

- **Zoom**: Phóng to/thu nhỏ diagram
- **Export**: Xuất ra PNG, PDF, SQL
- **Share**: Chia sẻ link với team
- **Auto-layout**: Tự động sắp xếp bảng

## Migration Status

Các bảng mới và cập nhật đã được implement trong migrations:

### Đã Tạo:
- ✅ `2025_10_02_042313_create_tasks_table.php` (updated with category)
- ✅ `2025_10_02_044338_create_daily_checkins_table.php` (updated)
- ✅ `2025_10_02_044410_create_daily_reviews_table.php` (updated)
- ✅ `2025_10_31_000000_create_timetable_classes_table.php`
- ✅ `2025_10_31_000001_create_timetable_studies_table.php`
- ✅ `2025_11_01_000000_create_timetable_class_weekly_contents_table.php`

### Đã Chạy:
- ✅ Timetable migrations đã chạy thành công trong Docker
- ⏳ Cần chạy lại full migration để áp dụng updates cho tasks, daily_checkins, daily_reviews

## Tính Năng Mới Được Hỗ Trợ

### 1. Timetable Management
- Quản lý thời khóa biểu theo tuần
- Nội dung riêng cho từng tuần
- Liên kết với learning paths
- Quản lý bài tập và ôn tập

### 2. Enhanced Daily Tracking
- Theo dõi giấc ngủ và stress
- Đặt priorities và goals hàng ngày
- Đánh giá chi tiết hơn (4 scores riêng biệt)
- Ghi nhận achievements, challenges, lessons learned

### 3. Task Categorization
- Phân loại task: study, work, personal, other
- Hiển thị badge theo category
- Filter và group theo category

## Best Practices

### JSON Fields
Các trường JSON nên lưu array of strings hoặc array of objects:

```json
// priorities
["Hoàn thành bài tập Toán", "Ôn tập tiếng Anh", "Đi gym"]

// achievements
[
  {"title": "Hoàn thành project", "time": "14:30"},
  {"title": "Học xong 3 chương", "time": "16:00"}
]

// lessons_learned
["Cần ngủ đủ giấc", "Nên chia nhỏ task lớn"]
```

### Enum Values
Sử dụng lowercase và underscore:
- ✅ `'in_progress'`, `'work_life_balance_score'`
- ❌ `'InProgress'`, `'WorkLifeBalanceScore'`

### Naming Convention
- Tables: snake_case, plural (e.g., `timetable_classes`)
- Foreign keys: singular_id (e.g., `timetable_class_id`)
- Enums: lowercase with underscores

## Next Steps

1. ✅ Update ERD diagram
2. ⏳ Run full migration (`migrate:fresh`)
3. ⏳ Update Models (DailyCheckin, DailyReview, Task)
4. ⏳ Update API documentation
5. ⏳ Update Android app models
6. ⏳ Test all features

## Version History

- **v1.5** (2025-11-01): Added timetable system, updated daily_checkins, daily_reviews, tasks
- **v1.4** (2025-10-28): Fixed focus_sessions.status, added ai_summaries
- **v1.3**: Added learning paths and knowledge repository
- **v1.2**: Enhanced AI integration
- **v1.1**: Initial comprehensive schema
- **v1.0**: Basic task management system

