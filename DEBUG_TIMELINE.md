# 🐛 DEBUG TIMELINE - TIMETABLE CLASSES NOT SHOWING

## ✅ BACKEND VERIFICATION (PASSED)

Backend logic đã được test và hoạt động đúng:

```
✓ Day mapping: monday → 1
✓ Time parsing: 09:15:00 - 10:45:00 = 90 minutes
✓ Scheduled time: 09:15:00
```

Expected API response:
```json
{
  "success": true,
  "data": [
    {
      "id": "class_1",
      "type": "timetable_class",
      "title": "🎓 test",
      "day_of_week": 1,
      "scheduled_time": "09:15:00",
      "duration_minutes": 90,
      "category": "class",
      "room": "1603",
      "instructor": "杉原",
      "period": 1,
      "color": "#4F46E5",
      "icon": "computer"
    }
  ]
}
```

---

## 🔍 ANDROID DEBUG STEPS

### Step 1: Check API Call

Mở Android Logcat và tìm log sau khi chọn thứ 2:

```
CalendarViewModel: Loaded X timeline items from API
CalendarViewModel: Timeline view showing Y timeline items for 2025-11-18 (day 1)
```

**Expected:**
- X = số lớp học + study schedules
- Y = số items cho thứ 2 (day 1)

**If X = 0:**
- API không trả về dữ liệu
- Check token authentication
- Check backend logs

**If Y = 0 but X > 0:**
- Filter logic có vấn đề
- Check day_of_week mapping

---

### Step 2: Check Day of Week Calculation

Trong `CalendarViewModel.kt:174-177`:

```kotlin
val calendar = Calendar.getInstance()
calendar.time = selectedDateValue
val dayOfWeek = calendar.get(Calendar.DAY_OF_WEEK) - 1
```

**Calendar.DAY_OF_WEEK returns:**
- Sunday = 1 → dayOfWeek = 0 ✓
- Monday = 2 → dayOfWeek = 1 ✓
- Tuesday = 3 → dayOfWeek = 2 ✓
- etc.

**Check trong Logcat:**
```
CalendarViewModel: Timeline view showing ... (day X)
```

X phải là 1 khi chọn thứ 2.

---

### Step 3: Check Date Selected

Đảm bảo ngày bạn chọn trong Calendar là **thứ 2**:

```kotlin
// In CalendarFragment
Log.d("CalendarFragment", "Date selected: ${SimpleDateFormat("yyyy-MM-dd EEEE", Locale.getDefault()).format(selectedDate)}")
```

Expected output:
```
CalendarFragment: Date selected: 2025-11-17 Monday
```

---

### Step 4: Enable Debug Logging

Thêm log trong `CalendarViewModel.applyFilter()`:

```kotlin
// At line 223
android.util.Log.d("CalendarViewModel",
    "Filtering timeline: total items=${timelineItems.size}, " +
    "dayOfWeek=$dayOfWeek, selectedDate=$selectedDateString")

// After filter
timelineFiltered.forEach { item ->
    android.util.Log.d("CalendarViewModel",
        "Timeline item: ${item.title}, scheduled=${item.scheduled_time}")
}
```

---

### Step 5: Check TimelineItem Parsing

Thêm log trong `TimelineItem.toTask()`:

```kotlin
fun toTask(selectedDate: String): Task {
    android.util.Log.d("TimelineItem",
        "Converting: id=$id, type=$type, day_of_week=$day_of_week, time=$scheduled_time")

    return Task(...)
}
```

---

## 🔧 COMMON ISSUES

### Issue 1: API Token Expired
**Symptom:** X = 0 (no data from API)
**Fix:** Re-login to get new token

### Issue 2: Wrong Date Selected
**Symptom:** Y = 0 but X > 0
**Fix:** Make sure you select Monday (thứ 2)

### Issue 3: Day Mapping Mismatch
**Symptom:** Items show on wrong day
**Fix:** Check Calendar.DAY_OF_WEEK calculation

### Issue 4: Time Format Error
**Symptom:** Backend error in logs
**Fix:** Already fixed - backend now handles H:i:s format

---

## 🎯 QUICK TEST

1. **Backend:** Run `php backend/test_timeline_api.php` ✓ (PASSED)

2. **Android:**
   ```
   - Open app
   - Go to Calendar
   - Switch to Timeline view
   - Select Monday (17/11/2025)
   - Check Logcat for:
     * "Loaded X timeline items from API"
     * "Timeline view showing Y timeline items"
   ```

3. **Expected Result:**
   - See "🎓 test" at 09:15
   - With details: "授業 - 第1時限 - 1603 - 杉原 - 90分"

---

## 📝 BACKEND LOGS

Check Laravel logs:

```bash
tail -f backend/storage/logs/laravel.log | grep Timeline
```

Expected output:
```
Timeline API - User: 1, Study Schedules: 0, Timetable Classes: 1
Converting class: test, day=monday, day_of_week=1
```

---

## 🆘 STILL NOT WORKING?

**UPDATE: Timeline items are loading correctly but not displaying in the UI.**

See **TIMELINE_DEBUG_STEPS.md** for comprehensive debugging guide with:
- Detailed logging added to TimelineAdapter and CalendarFragment
- Step-by-step testing instructions
- Expected log output examples
- Common issues and solutions

### Quick Logs to Provide:

1. **Logcat output:**
   ```
   CalendarFragment: === UPDATE TIMELINE VIEW ===
   CalendarFragment: Received X tasks
   TimelineAdapter: === SUBMIT LIST DEBUG ===
   TimelineAdapter: Received Y tasks
   ```

2. **Selected date:** (e.g., 2025-11-17 Monday)

3. **Backend logs:** (Timeline API logs)

4. **API response:** (from network inspector)
