# Study Schedule System - Implementation Guide

## 📚 Overview

Hệ thống lịch học bắt buộc để rèn luyện tính kỷ luật cho người dùng khi import roadmap học tập.

**Mục tiêu**: Khi người dùng chọn roadmap (Java, Python, etc.), họ **BẮT BUỘC** phải thiết lập lịch học cụ thể với ngày và giờ học đều đặn.

---

## ✅ Implementation Status

### Backend (100% Complete)
- ✅ Database migration: `study_schedules` table
- ✅ Models: `StudySchedule.php`, `LearningPath.php`
- ✅ Controller: `StudyScheduleController.php` (8 methods)
- ✅ API Routes: 10 endpoints
- ✅ Validation: **study_schedules REQUIRED** when importing roadmap

### Frontend (95% Complete)
- ✅ Data Models: `StudySchedule.kt`, `RoadmapModels.kt`
- ✅ API Service: 8 endpoints added to `ApiService.kt`
- ✅ ViewModel: `StudyScheduleViewModel.kt`
- ✅ UI Components:
  - ✅ `ScheduleSetupBottomSheet.kt`
  - ✅ `DaySelectionAdapter.kt`
  - ✅ Layout files (bottom sheet + item)
- ⏳ **Integration**: Need to integrate into RoadmapImportActivity

---

## 🚀 How to Use

### Step 1: Show Schedule Setup Dialog

```kotlin
// In your Activity (e.g., RoadmapDetailActivity or RoadmapImportActivity)
import ecccomp.s2240788.mobile_android.ui.fragments.ScheduleSetupBottomSheet

// Show the dialog
val scheduleDialog = ScheduleSetupBottomSheet.newInstance()
scheduleDialog.setOnConfirmListener { schedules ->
    // schedules: List<StudyScheduleInput>
    // Each schedule contains: day_of_week, study_time, duration_minutes

    importRoadmapWithSchedule(roadmapId, schedules)
}
scheduleDialog.show(supportFragmentManager, "schedule_setup")
```

### Step 2: Import Roadmap with Schedule

```kotlin
private fun importRoadmapWithSchedule(
    roadmapId: String,
    schedules: List<StudyScheduleInput>
) {
    val request = ImportRoadmapRequest(
        roadmapId = roadmapId,
        source = "popular", // or "ai", "microsoft_learn"
        autoClone = true,
        studySchedules = schedules // BẮT BUỘC khi autoClone=true
    )

    // Call API
    viewModel.importRoadmap(request)
}
```

### Step 3: ViewModel Implementation (Optional)

```kotlin
// In your ViewModel
fun importRoadmap(request: ImportRoadmapRequest) {
    viewModelScope.launch {
        try {
            _isLoading.value = true
            val response = apiService.importRoadmap(request)

            if (response.isSuccessful && response.body()?.success == true) {
                val data = response.body()?.data
                val learningPathId = data?.learningPathId
                val studySchedules = data?.studySchedules

                // Navigate to learning path detail
                _successMessage.value = "Đã import roadmap thành công!"
            } else {
                _error.value = "Import failed"
            }
        } catch (e: Exception) {
            _error.value = e.message
        } finally {
            _isLoading.value = false
        }
    }
}
```

---

## 📱 UI Flow

```
┌─────────────────────────────────┐
│   Roadmap Detail Screen         │
│                                  │
│   [Import Roadmap] Button       │
└──────────────┬──────────────────┘
               │ Click
               ▼
┌─────────────────────────────────┐
│  Schedule Setup Bottom Sheet    │
│                                  │
│  ⏰ Giờ học: [19:30] [Chọn giờ] │
│                                  │
│  📅 Chọn ngày:                   │
│  [T2] [T3] [T4] [T5]            │
│  [T6] [T7] [CN]                 │
│                                  │
│  ⏱️ Thời lượng: 60 phút          │
│  [========] Slider               │
│                                  │
│  Tổng kết:                       │
│  3 buổi/tuần • 3.0 giờ/tuần     │
│                                  │
│  [Xác nhận lịch học]            │
└──────────────┬──────────────────┘
               │ Confirm
               ▼
┌─────────────────────────────────┐
│   Import Roadmap with Schedule  │
│                                  │
│   POST /api/roadmaps/import     │
│   {                              │
│     "roadmap_id": "java",        │
│     "study_schedules": [         │
│       {                          │
│         "day_of_week": 1,        │
│         "study_time": "19:30",   │
│         "duration_minutes": 60   │
│       },                         │
│       ...                        │
│     ]                            │
│   }                              │
└──────────────┬──────────────────┘
               │ Success
               ▼
┌─────────────────────────────────┐
│   Learning Path Created!        │
│   with Study Schedules          │
└─────────────────────────────────┘
```

---

## 🎯 Features

### User Perspective:
- ✅ Chọn giờ học (Time Picker 24h)
- ✅ Chọn nhiều ngày trong tuần (T2-CN)
- ✅ Điều chỉnh thời lượng (15-480 phút)
- ✅ Xem tổng kết real-time
- ✅ Validation: Phải chọn ít nhất 1 ngày

### Technical Features:
- ✅ Material Design Bottom Sheet
- ✅ Grid layout for day selection (4 columns)
- ✅ SeekBar for duration
- ✅ LiveData updates
- ✅ Error validation
- ✅ Clean callback interface

---

## 📡 API Endpoints

### Create Schedule (Auto-created on Roadmap Import)
```http
POST /api/roadmaps/import
Content-Type: application/json

{
  "roadmap_id": "java-backend",
  "source": "popular",
  "auto_clone": true,
  "study_schedules": [
    {
      "day_of_week": 1,
      "study_time": "19:30",
      "duration_minutes": 60,
      "reminder_enabled": true,
      "reminder_before_minutes": 30
    }
  ]
}
```

### Get Schedules for Learning Path
```http
GET /api/learning-paths/{id}/study-schedules
```

### Today's Sessions
```http
GET /api/study-schedules/today
```

### Mark Completed
```http
POST /api/study-schedules/{id}/complete
```

---

## 🔧 Integration Checklist

### To Complete Integration:

1. **Find RoadmapImportActivity** (or equivalent)
   ```bash
   # Search for roadmap import activity
   find . -name "*Roadmap*Activity*.kt"
   ```

2. **Add Schedule Setup Step**
   - Before calling import API
   - Show `ScheduleSetupBottomSheet`
   - Wait for user confirmation

3. **Update Import Request**
   - Add `study_schedules` field
   - Use `List<StudyScheduleInput>` from dialog

4. **Handle Response**
   - Display success message
   - Navigate to learning path detail
   - Show schedule summary

### Example Integration:

```kotlin
// In RoadmapDetailActivity.kt or similar

private fun showImportDialog() {
    // Show schedule setup first
    val scheduleDialog = ScheduleSetupBottomSheet.newInstance()
    scheduleDialog.setOnConfirmListener { schedules ->
        // Now import with schedules
        importRoadmap(schedules)
    }
    scheduleDialog.show(supportFragmentManager, "schedule_setup")
}

private fun importRoadmap(schedules: List<StudyScheduleInput>) {
    val request = ImportRoadmapRequest(
        roadmapId = currentRoadmap.id,
        source = "popular",
        autoClone = true,
        studySchedules = schedules
    )

    viewModel.importRoadmap(request)
}
```

---

## 🎨 UI Components

### ScheduleSetupBottomSheet
- Material Design Bottom Sheet Dialog
- Adaptive height
- Dismissible with back button or close icon

### DaySelectionAdapter
- RecyclerView with GridLayoutManager (4 columns)
- Toggle selection on click
- Visual feedback (color change)

### Layouts:
- `bottom_sheet_schedule_setup.xml` - Main dialog layout
- `item_day_selection.xml` - Day item card

---

## 📊 Data Flow

```
User Input (UI)
      ↓
ViewModel (Validation)
      ↓
List<StudyScheduleInput>
      ↓
ImportRoadmapRequest
      ↓
API Call
      ↓
Backend (Create Learning Path + Schedules)
      ↓
Response
      ↓
UI Update
```

---

## 🧪 Testing

### Manual Test Steps:

1. **Open roadmap detail screen**
2. **Click "Import" button**
3. **Schedule Setup Dialog appears**:
   - ✅ Can select time (19:30 default)
   - ✅ Can toggle days (T2, T4, T6 selected)
   - ✅ Can adjust duration (60 minutes default)
   - ✅ Summary updates: "3 buổi/tuần • 3.0 giờ/tuần"
4. **Click "Xác nhận lịch học"**:
   - ✅ Validation passes (at least 1 day)
   - ✅ API called with study_schedules
5. **Learning Path created successfully**:
   - ✅ Has study schedules attached
   - ✅ Can view schedules in detail screen

### API Test (Postman/Insomnia):

```http
POST http://localhost:8080/api/roadmaps/import
Authorization: Bearer YOUR_TOKEN

{
  "roadmap_id": "java-backend",
  "source": "popular",
  "auto_clone": true,
  "study_schedules": [
    {"day_of_week": 1, "study_time": "19:30", "duration_minutes": 60},
    {"day_of_week": 3, "study_time": "19:30", "duration_minutes": 60},
    {"day_of_week": 5, "study_time": "19:30", "duration_minutes": 60}
  ]
}
```

Expected Response:
```json
{
  "success": true,
  "data": {
    "learning_path_id": 123,
    "study_schedules": [...],
    "weekly_schedule": {...}
  },
  "message": "ロードマップを学習パスとして追加しました"
}
```

---

## 📝 Commit History

| Commit | Description |
|--------|-------------|
| `eb31070` | Backend: Study schedule system (migration, models, controller, routes) |
| `68868d4` | Frontend: Data models (StudySchedule.kt, RoadmapModels.kt) + API endpoints |
| `e20fd91` | Fix: Load studySchedules in LearningPathController |
| `73cc094` | Frontend: UI components (ViewModel, BottomSheet, Adapter, Layouts) |

---

## 🚧 Known Limitations

1. **Reminder system**: Backend supports, but not implemented in UI yet
2. **Edit schedule**: Can create, but edit UI not yet implemented
3. **Conflict detection**: Phase 2 - AI will check timetable conflicts
4. **Statistics display**: Stats endpoint ready, but UI widget not created yet

---

## 🎯 Next Steps (Phase 2)

1. **AI Schedule Suggestion**:
   - Analyze user's timetable
   - Suggest optimal study times
   - Avoid conflicts with existing classes

2. **Today's Sessions Widget**:
   - Show on home screen
   - Quick "Mark Completed" button
   - Reminder notifications

3. **Statistics Dashboard**:
   - Completion rate tracking
   - Consistency score
   - Streak calculation

---

## 💡 Tips

- **Default time**: 19:30 (7:30 PM) - typical study time
- **Min duration**: 15 minutes
- **Max duration**: 480 minutes (8 hours)
- **Recommended**: 60-90 minutes per session
- **Best practice**: Study same time each day for discipline

---

## 📞 Support

For issues or questions:
1. Check backend logs: `docker logs todoapp-app`
2. Check API response in network tab
3. Verify study_schedules validation
4. Ensure at least 1 day selected

---

**Status**: Backend ✅ Complete | Frontend ⏳ 95% (needs integration)

**Last Updated**: 2025-11-13
