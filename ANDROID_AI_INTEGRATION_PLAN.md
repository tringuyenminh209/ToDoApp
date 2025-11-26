# 📱 Android AI Integration Plan - TIER 1 Features

## 🎯 Mục tiêu
Tích hợp 4 AI features mới từ TIER 1 vào Android app để người dùng có thể sử dụng đầy đủ các tính năng AI vừa phát triển.

---

## 📊 Phân tích hiện trạng

### ✅ Đã có trong Android
1. **Chat AI Integration** - `ApiService.kt` lines 432-473
   - ✅ GET `/ai/chat/conversations` - Lấy danh sách conversations
   - ✅ POST `/ai/chat/conversations` - Tạo conversation mới
   - ✅ POST `/ai/chat/conversations/{id}/messages/context-aware` - Gửi tin nhắn có context
   - ✅ POST `/ai/chat/task-suggestions/confirm` - Xác nhận task suggestion
   - ✅ POST `/ai/chat/timetable-suggestions/confirm` - Xác nhận timetable suggestion

2. **Task Management** - Activities & ViewModels có sẵn
   - ✅ `TaskDetailActivity.kt` - Hiển thị chi tiết task
   - ✅ `AddTaskViewModel.kt` - Tạo task mới
   - ✅ `EditTaskViewModel.kt` - Chỉnh sửa task
   - ✅ Layouts: `activity_task_detail.xml`, `activity_add_task.xml`, `activity_edit_task.xml`

3. **Knowledge System** - Đã có adapter
   - ✅ `KnowledgeAdapter.kt` - Hiển thị knowledge items
   - ✅ `FocusKnowledgeAdapter.kt` - Knowledge trong focus mode

### ❌ Thiếu - Cần bổ sung

#### 1. **Smart Scheduling API Endpoint**
**Status:** ❌ Chưa có

**Backend có:** `GET /api/tasks/{id}/suggest-schedule`

**Android thiếu:**
- Không có function trong `ApiService.kt`
- Không có data model cho schedule suggestions
- Không có UI để hiển thị suggestions

---

#### 2. **Knowledge Q&A Integration in Chat**
**Status:** ⚠️ Một phần (có chat, thiếu knowledge search context)

**Backend có:** Knowledge query parsing trong context-aware chat (đã implement)

**Android có:**
- ✅ Chat UI (ChatRepository, conversation endpoints)
- ❌ Không hiển thị knowledge results trong chat response
- ❌ Không có UI đặc biệt cho knowledge items trong chat

---

#### 3. **Multi-Intent Parsing**
**Status:** ✅ Không cần update Android

**Backend đã tự động xử lý:** Khi user gửi message qua chat, backend tự động detect và tạo cả task + timetable + knowledge nếu cần.

**Android chỉ cần:** Hiển thị response từ backend đúng cách (đã có sẵn trong chat)

---

#### 4. **Enhanced Context Analysis**
**Status:** ✅ Không cần update Android

**Backend đã tự động áp dụng:** AI sẽ tự động phân tích priority, time gaps, productivity khi build context.

**Android chỉ cần:** Gửi request đến context-aware chat endpoint (đã có sẵn)

---

## 🚀 Kế hoạch cập nhật Android

### **PRIORITY 1: Smart Scheduling Integration** 🔴
**Timeline:** 3-4 hours
**Impact:** High - User trực tiếp thấy giá trị

#### Cần làm:

**1. Thêm API Endpoint** (30 min)
```kotlin
// File: ApiService.kt
// Thêm vào interface ApiService

@GET("tasks/{id}/suggest-schedule")
suspend fun suggestTaskSchedule(
    @Path("id") taskId: Int,
    @Query("days_ahead") daysAhead: Int = 7
): Response<ApiResponse<ScheduleSuggestionsResponse>>
```

**2. Tạo Data Models** (20 min)
```kotlin
// File: data/models/ScheduleSuggestion.kt (NEW FILE)

data class ScheduleSuggestionsResponse(
    val task: TaskInfo,
    val suggestions: List<ScheduleSuggestion>,
    val days_searched: Int
)

data class TaskInfo(
    val id: Int,
    val title: String,
    val estimated_minutes: Int?,
    val priority: Int?,
    val deadline: String?
)

data class ScheduleSuggestion(
    val date: String,              // "2025-11-26"
    val day: String,               // "wednesday"
    val start_time: String,        // "14:00:00"
    val end_time: String,          // "16:00:00"
    val duration_minutes: Int,     // 120
    val score: Double,             // 4.25
    val reasons: List<String>,     // ["High priority task", "Optimal time of day"]
    val confidence: String         // "high" | "medium" | "low"
)
```

**3. Thêm vào Repository** (20 min)
```kotlin
// File: data/repository/TaskRepository.kt
// Thêm function mới

suspend fun getSuggestedSchedule(taskId: Int, daysAhead: Int = 7): Result<ScheduleSuggestionsResponse> {
    return try {
        val response = apiService.suggestTaskSchedule(taskId, daysAhead)
        if (response.isSuccessful && response.body()?.success == true) {
            Result.success(response.body()!!.data)
        } else {
            Result.failure(Exception(response.body()?.message ?: "Failed to get suggestions"))
        }
    } catch (e: Exception) {
        Result.failure(e)
    }
}
```

**4. Cập nhật ViewModel** (30 min)
```kotlin
// File: ui/viewmodels/TaskDetailViewModel.kt
// Thêm LiveData và function mới

class TaskDetailViewModel : ViewModel() {
    private val _scheduleSuggestions = MutableLiveData<List<ScheduleSuggestion>>()
    val scheduleSuggestions: LiveData<List<ScheduleSuggestion>> = _scheduleSuggestions

    private val _loadingSuggestions = MutableLiveData<Boolean>()
    val loadingSuggestions: LiveData<Boolean> = _loadingSuggestions

    fun loadScheduleSuggestions(taskId: Int) {
        viewModelScope.launch {
            _loadingSuggestions.value = true
            val result = taskRepository.getSuggestedSchedule(taskId)
            if (result.isSuccess) {
                _scheduleSuggestions.value = result.getOrNull()?.suggestions ?: emptyList()
            }
            _loadingSuggestions.value = false
        }
    }
}
```

**5. Tạo UI Layout** (45 min)
```xml
<!-- File: res/layout/item_schedule_suggestion.xml (NEW FILE) -->
<?xml version="1.0" encoding="utf-8"?>
<com.google.android.material.card.MaterialCardView
    xmlns:android="http://schemas.android.com/apk/res/android"
    xmlns:app="http://schemas.android.com/apk/res-auto"
    android:layout_width="match_parent"
    android:layout_height="wrap_content"
    android:layout_margin="8dp"
    app:cardElevation="2dp"
    app:strokeWidth="1dp"
    app:strokeColor="@color/primary_light">

    <LinearLayout
        android:layout_width="match_parent"
        android:layout_height="wrap_content"
        android:orientation="vertical"
        android:padding="16dp">

        <!-- Date & Time -->
        <TextView
            android:id="@+id/tv_date_time"
            android:layout_width="wrap_content"
            android:layout_height="wrap_content"
            android:text="水曜日, 14:00-16:00"
            android:textSize="16sp"
            android:textStyle="bold"
            android:textColor="@color/text_primary"/>

        <!-- Confidence Badge -->
        <com.google.android.material.chip.Chip
            android:id="@+id/chip_confidence"
            android:layout_width="wrap_content"
            android:layout_height="wrap_content"
            android:layout_marginTop="8dp"
            android:text="信頼度: 高"
            style="@style/Widget.Material3.Chip.Assist"/>

        <!-- Reasons -->
        <TextView
            android:id="@+id/tv_reasons"
            android:layout_width="wrap_content"
            android:layout_height="wrap_content"
            android:layout_marginTop="8dp"
            android:text="• 高優先度タスク\n• 最適な時間帯\n• 十分な時間"
            android:textSize="14sp"
            android:textColor="@color/text_secondary"/>

        <!-- Select Button -->
        <com.google.android.material.button.MaterialButton
            android:id="@+id/btn_select"
            android:layout_width="match_parent"
            android:layout_height="wrap_content"
            android:layout_marginTop="12dp"
            android:text="この時間を選択"
            style="@style/Widget.Material3.Button.OutlinedButton"/>

    </LinearLayout>

</com.google.android.material.card.MaterialCardView>
```

```xml
<!-- File: res/layout/bottom_sheet_schedule_suggestions.xml (NEW FILE) -->
<?xml version="1.0" encoding="utf-8"?>
<LinearLayout
    xmlns:android="http://schemas.android.com/apk/res/android"
    android:layout_width="match_parent"
    android:layout_height="wrap_content"
    android:orientation="vertical"
    android:padding="16dp">

    <!-- Header -->
    <TextView
        android:layout_width="wrap_content"
        android:layout_height="wrap_content"
        android:text="スケジュール提案"
        android:textSize="20sp"
        android:textStyle="bold"
        android:textColor="@color/text_primary"/>

    <TextView
        android:layout_width="wrap_content"
        android:layout_height="wrap_content"
        android:layout_marginTop="4dp"
        android:text="AIがあなたの予定を分析して、最適な時間を提案しました"
        android:textSize="14sp"
        android:textColor="@color/text_secondary"/>

    <!-- Loading -->
    <ProgressBar
        android:id="@+id/progress_loading"
        android:layout_width="wrap_content"
        android:layout_height="wrap_content"
        android:layout_gravity="center"
        android:layout_marginTop="16dp"
        android:visibility="gone"/>

    <!-- Suggestions List -->
    <androidx.recyclerview.widget.RecyclerView
        android:id="@+id/rv_suggestions"
        android:layout_width="match_parent"
        android:layout_height="wrap_content"
        android:layout_marginTop="16dp"
        android:nestedScrollingEnabled="false"/>

    <!-- No suggestions message -->
    <TextView
        android:id="@+id/tv_no_suggestions"
        android:layout_width="wrap_content"
        android:layout_height="wrap_content"
        android:layout_gravity="center"
        android:layout_marginTop="24dp"
        android:text="適切なスケジュール枠が見つかりませんでした"
        android:textSize="14sp"
        android:textColor="@color/text_secondary"
        android:visibility="gone"/>

</LinearLayout>
```

**6. Tạo Adapter** (30 min)
```kotlin
// File: ui/adapters/ScheduleSuggestionAdapter.kt (NEW FILE)

class ScheduleSuggestionAdapter(
    private val onSuggestionSelected: (ScheduleSuggestion) -> Unit
) : RecyclerView.Adapter<ScheduleSuggestionAdapter.ViewHolder>() {

    private var suggestions: List<ScheduleSuggestion> = emptyList()

    fun submitList(newSuggestions: List<ScheduleSuggestion>) {
        suggestions = newSuggestions
        notifyDataSetChanged()
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemScheduleSuggestionBinding.inflate(
            LayoutInflater.from(parent.context), parent, false
        )
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(suggestions[position])
    }

    override fun getItemCount() = suggestions.size

    inner class ViewHolder(private val binding: ItemScheduleSuggestionBinding) :
        RecyclerView.ViewHolder(binding.root) {

        fun bind(suggestion: ScheduleSuggestion) {
            // Format date & time
            val dateTime = "${formatDate(suggestion.date)}, ${formatTime(suggestion.start_time)}-${formatTime(suggestion.end_time)}"
            binding.tvDateTime.text = dateTime

            // Confidence badge
            val confidenceText = when (suggestion.confidence) {
                "high" -> "信頼度: 高"
                "medium" -> "信頼度: 中"
                else -> "信頼度: 低"
            }
            binding.chipConfidence.text = confidenceText
            binding.chipConfidence.setChipBackgroundColorResource(
                when (suggestion.confidence) {
                    "high" -> R.color.success_light
                    "medium" -> R.color.warning_light
                    else -> R.color.error_light
                }
            )

            // Reasons
            val reasonsText = suggestion.reasons.joinToString("\n") { "• $it" }
            binding.tvReasons.text = reasonsText

            // Select button
            binding.btnSelect.setOnClickListener {
                onSuggestionSelected(suggestion)
            }
        }

        private fun formatDate(dateString: String): String {
            // Parse "2025-11-26" to "水曜日, 11/26"
            val date = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).parse(dateString)
            val dayOfWeek = SimpleDateFormat("EEEE", Locale.JAPANESE).format(date)
            val monthDay = SimpleDateFormat("MM/dd", Locale.getDefault()).format(date)
            return "$dayOfWeek, $monthDay"
        }

        private fun formatTime(timeString: String): String {
            // Parse "14:00:00" to "14:00"
            return timeString.substring(0, 5)
        }
    }
}
```

**7. Tạo Bottom Sheet Dialog** (30 min)
```kotlin
// File: ui/dialogs/ScheduleSuggestionsBottomSheet.kt (NEW FILE)

class ScheduleSuggestionsBottomSheet(
    private val taskId: Int,
    private val onSuggestionSelected: (ScheduleSuggestion) -> Unit
) : BottomSheetDialogFragment() {

    private var _binding: BottomSheetScheduleSuggestionsBinding? = null
    private val binding get() = _binding!!

    private lateinit var adapter: ScheduleSuggestionAdapter
    private lateinit var viewModel: TaskDetailViewModel

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = BottomSheetScheduleSuggestionsBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        viewModel = ViewModelProvider(requireActivity())[TaskDetailViewModel::class.java]

        setupRecyclerView()
        observeViewModel()

        viewModel.loadScheduleSuggestions(taskId)
    }

    private fun setupRecyclerView() {
        adapter = ScheduleSuggestionAdapter { suggestion ->
            onSuggestionSelected(suggestion)
            dismiss()
        }
        binding.rvSuggestions.layoutManager = LinearLayoutManager(requireContext())
        binding.rvSuggestions.adapter = adapter
    }

    private fun observeViewModel() {
        viewModel.scheduleSuggestions.observe(viewLifecycleOwner) { suggestions ->
            if (suggestions.isEmpty()) {
                binding.rvSuggestions.visibility = View.GONE
                binding.tvNoSuggestions.visibility = View.VISIBLE
            } else {
                binding.rvSuggestions.visibility = View.VISIBLE
                binding.tvNoSuggestions.visibility = View.GONE
                adapter.submitList(suggestions)
            }
        }

        viewModel.loadingSuggestions.observe(viewLifecycleOwner) { loading ->
            binding.progressLoading.visibility = if (loading) View.VISIBLE else View.GONE
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
```

**8. Tích hợp vào TaskDetailActivity** (20 min)
```kotlin
// File: ui/activities/TaskDetailActivity.kt
// Thêm button vào layout và handler

private fun setupClicks() {
    // ... existing code ...

    // NEW: Smart Scheduling Button
    binding.btnSmartSchedule.setOnClickListener {
        val bottomSheet = ScheduleSuggestionsBottomSheet(taskId) { suggestion ->
            // User selected a suggestion, update task scheduled_time
            updateTaskScheduledTime(suggestion.date, suggestion.start_time)
        }
        bottomSheet.show(supportFragmentManager, "ScheduleSuggestionsBottomSheet")
    }
}

private fun updateTaskScheduledTime(date: String, time: String) {
    // Convert to scheduled_time format and update task
    // This will call the existing update task API
}
```

**9. Cập nhật activity_task_detail.xml** (15 min)
```xml
<!-- File: res/layout/activity_task_detail.xml -->
<!-- Thêm button Smart Schedule vào layout -->

<com.google.android.material.button.MaterialButton
    android:id="@+id/btn_smart_schedule"
    android:layout_width="match_parent"
    android:layout_height="wrap_content"
    android:layout_marginTop="8dp"
    android:text="AIスケジュール提案"
    android:drawableLeft="@drawable/ic_schedule_smart"
    style="@style/Widget.Material3.Button.TonalButton"/>
```

---

### **PRIORITY 2: Knowledge Q&A in Chat Enhancement** 🟡
**Timeline:** 2 hours
**Impact:** Medium - Cải thiện trải nghiệm chat

#### Cần làm:

**1. Cập nhật Chat Response UI** (1 hour)
```kotlin
// File: ui/adapters/ChatMessageAdapter.kt
// Thêm logic để hiển thị knowledge items trong chat response

// Khi backend trả về knowledge results, hiển thị dạng cards đặc biệt
```

**2. Thêm quick action buttons trong chat** (30 min)
```xml
<!-- Suggestion chips khi user hỏi về knowledge -->
<com.google.android.material.chip.ChipGroup>
    <Chip text="Javaのメモを見せて"/>
    <Chip text="演習問題を探す"/>
</com.google.android.material.chip.ChipGroup>
```

**3. Test với backend context-aware chat** (30 min)

---

### **PRIORITY 3: Testing & Polish** 🟢
**Timeline:** 1 hour

1. Test Smart Scheduling với real data
2. Test Knowledge Q&A trong chat
3. Kiểm tra UI/UX trên nhiều screen sizes
4. Update strings.xml với Japanese translations
5. Add loading states và error handling

---

## 📝 Tổng kết

### Cần làm ngay (PRIORITY 1):
- ✅ Multi-Intent Parsing: Không cần update Android
- ✅ Enhanced Context: Không cần update Android
- ❌ **Smart Scheduling: CẦN IMPLEMENT** (3-4 hours)
- ⚠️ **Knowledge Q&A: CẦN ENHANCE UI** (2 hours)

### Timeline tổng:
- **Smart Scheduling Integration:** 3-4 hours
- **Knowledge Q&A Enhancement:** 2 hours
- **Testing & Polish:** 1 hour
- **TOTAL: 6-7 hours**

### Files cần tạo mới:
1. `data/models/ScheduleSuggestion.kt`
2. `ui/adapters/ScheduleSuggestionAdapter.kt`
3. `ui/dialogs/ScheduleSuggestionsBottomSheet.kt`
4. `res/layout/item_schedule_suggestion.xml`
5. `res/layout/bottom_sheet_schedule_suggestions.xml`

### Files cần cập nhật:
1. `data/api/ApiService.kt` - Thêm suggest-schedule endpoint
2. `data/repository/TaskRepository.kt` - Thêm getSuggestedSchedule function
3. `ui/viewmodels/TaskDetailViewModel.kt` - Thêm schedule suggestions logic
4. `ui/activities/TaskDetailActivity.kt` - Thêm Smart Schedule button handler
5. `res/layout/activity_task_detail.xml` - Thêm Smart Schedule button
6. `ui/adapters/ChatMessageAdapter.kt` - Enhance knowledge display

---

## 🎯 Kết luận

Android app đã có nền tảng rất tốt với Chat AI integration. Chỉ cần bổ sung:
1. **Smart Scheduling UI/API** - Feature hoàn toàn mới, cần implement từ đầu
2. **Knowledge Q&A Enhancement** - Đã có chat, chỉ cần enhance UI hiển thị knowledge

Với 6-7 giờ công việc, có thể tích hợp đầy đủ TIER 1 AI features vào Android app!
