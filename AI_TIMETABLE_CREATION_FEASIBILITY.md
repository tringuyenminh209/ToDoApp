# AI Chatbot Timetable Creation - Feasibility Analysis

**Date**: 2025-11-17
**Feature**: AI chatbot tạo lịch học tự động (giống tạo task)

---

## ✅ KẾT LUẬN: HOÀN TOÀN KHẢ DỤNG!

AI chatbot **CÓ THỂ** tạo lịch học tự động, giống như hiện tại đang tạo task tự động.

**Độ khả thi**: ⭐⭐⭐⭐⭐ (5/5)
**Thời gian ước tính**: 2-3 giờ
**Độ phức tạp**: Thấp (tương tự task creation)

---

## 📋 PHÂN TÍCH HIỆN TRẠNG

### 1. ✅ Task Creation Đã Hoạt Động

**File**: `backend/app/Http/Controllers/AIController.php`

**Flow hiện tại** (Line 970-1115):
```php
// 1. Parse task intent from user message
$taskData = $this->aiService->parseTaskIntent($request->message);

// 2. If task intent detected, create task
if ($taskData) {
    $createdTask = Task::create([
        'user_id' => $user->id,
        'title' => $taskData['title'],
        'description' => $taskData['description'] ?? null,
        'priority' => $priorityInt,
        'deadline' => $taskData['deadline'] ?? now()->format('Y-m-d'),
        'scheduled_time' => $taskData['scheduled_time'] ?? null,
        'status' => 'pending',
    ]);

    // Create subtasks if provided
    // Add tags if provided
}

// 3. Add confirmation to AI response
if ($createdTask) {
    $taskConfirmation = "\n\n✅ タスクを作成しました: 「{$createdTask->title}」";
    $aiResponse['message'] = $aiResponse['message'] . $taskConfirmation;
}
```

**Ví dụ hoạt động**:
- User: "英語を30分勉強する"
- AI: Parses → Creates task → Confirms: "✅ タスクを作成しました: 「英語を30分勉強する」"

---

### 2. ✅ Timetable API Đã Sẵn Sàng

**File**: `backend/app/Http/Controllers/TimetableController.php`

**API endpoint**: `POST /api/timetable/classes` (Line 105-146)

**Validation**:
```php
$request->validate([
    'name' => 'required|string|max:255',
    'description' => 'nullable|string',
    'room' => 'nullable|string|max:100',
    'instructor' => 'nullable|string|max:255',
    'day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
    'period' => 'required|integer|min:1|max:10',
    'start_time' => 'required|date_format:H:i',
    'end_time' => 'required|date_format:H:i|after:start_time',
    'color' => 'nullable|string|max:7',
    'icon' => 'nullable|string|max:50',
]);
```

**Required fields**:
- `name` (tên lớp học)
- `day` (thứ: monday-sunday)
- `period` (tiết: 1-10)
- `start_time` (giờ bắt đầu: HH:MM)
- `end_time` (giờ kết thúc: HH:MM)

**Optional fields**:
- `description`, `room`, `instructor`, `color`, `icon`

---

### 3. ✅ Task Intent Parsing Pattern

**File**: `backend/app/Services/AIService.php`

**Method**: `parseTaskIntent()` (Line 552-693)

**How it works**:
1. Accepts user message
2. Sends to AI with detailed prompt
3. AI analyzes and returns JSON:
   - `has_task_intent: true/false`
   - `task: { title, description, priority, deadline, ... }`
4. Returns parsed data or null

**Prompt structure**:
```
"以下のメッセージを分析して、**明確なタスク作成の意図があるか**判断してください。
タスク作成の意図がある場合は、タスク情報を抽出してJSONで返してください。
意図がない場合は、必ず false を返してください。

メッセージ: {$message}

タスク作成の意図がある場合のJSON形式:
{
  "has_task_intent": true,
  "task": {
    "title": "タスクのタイトル",
    "description": "タスクの説明（オプション）",
    "estimated_minutes": 推定時間（分）,
    "priority": "high/medium/low",
    "deadline": "YYYY-MM-DD"
  }
}
..."
```

---

## 🎯 ĐỀ XUẤT IMPLEMENTATION

### Bước 1: Thêm `parseTimetableIntent()` vào AIService.php

**File**: `backend/app/Services/AIService.php`

**Location**: Sau method `parseTaskIntent()` (sau line 693)

**Code mẫu**:
```php
/**
 * Parse timetable class creation intent from user message
 * Similar to parseTaskIntent() but for timetable classes
 *
 * @param string $message User message
 * @return array|null Timetable class data if intent detected, null otherwise
 */
public function parseTimetableIntent(string $message): ?array
{
    if (!$this->apiKey) {
        return null;
    }

    $prompt = "以下のメッセージを分析して、**明確な授業登録の意図があるか**判断してください。
授業登録の意図がある場合は、授業情報を抽出してJSONで返してください。
意図がない場合は、必ず false を返してください。

メッセージ: {$message}

授業登録の意図がある場合のJSON形式:
{
  \"has_timetable_intent\": true,
  \"timetable_class\": {
    \"name\": \"授業名\",
    \"day\": \"monday/tuesday/wednesday/thursday/friday/saturday/sunday\",
    \"start_time\": \"HH:MM\",
    \"end_time\": \"HH:MM\",
    \"period\": 1-10 (オプション、指定されていない場合は時間から計算),
    \"room\": \"教室名（オプション）\",
    \"instructor\": \"教員名（オプション）\",
    \"description\": \"説明（オプション）\"
  }
}

授業登録の意図がない場合:
{
  \"has_timetable_intent\": false
}

**明確に授業登録の意図があるキーワード:**
- 「授業を追加」「授業を登録」「クラスを追加」
- 「〜の授業がある」+時間指定 (例: 「月曜日に数学の授業がある」)
- 「〜のクラスを追加」(例: 「Calculusのクラスを追加」)
- ベトナム語: 「thêm lớp」「đăng ký lớp」「lịch học」
- 日本語: 「時間割に追加」「授業を入れる」

**授業登録の意図がないもの (必ず false を返す):**
- 質問: 「今日の授業は何ですか？」「スケジュールを見せて」
- 確認: 「授業の時間を確認」「時間割を教えて」
- 雑談: 「授業が大変」「先生が厳しい」

**日本語の曜日 → 英語マッピング:**
- 月曜日 → monday
- 火曜日 → tuesday
- 水曜日 → wednesday
- 木曜日 → thursday
- 金曜日 → friday
- 土曜日 → saturday
- 日曜日 → sunday

**ベトナム語の曜日 → 英語マッピング:**
- thứ 2 → monday
- thứ 3 → tuesday
- thứ 4 → wednesday
- thứ 5 → thursday
- thứ 6 → friday
- thứ 7 → saturday
- chủ nhật → sunday

**時間フォーマット:**
- 日本語: 「9時」→ \"09:00\", 「10時半」→ \"10:30\"
- ベトナム語: \"9h\" → \"09:00\", \"9h30\" → \"09:30\"
- 英語: \"9am\" → \"09:00\", \"2:30pm\" → \"14:30\"

**例:**
❌ \"今日の授業は何ですか？\" → {\"has_timetable_intent\": false} (質問)
❌ \"月曜日のスケジュールを見せて\" → {\"has_timetable_intent\": false} (確認)
✅ \"月曜日の9時から10時までCalculusの授業を追加\" → {\"has_timetable_intent\": true}
✅ \"Thêm lớp Calculus thứ 2 lúc 9h\" → {\"has_timetable_intent\": true}
✅ \"火曜日に英語の授業を入れて、10時から11時半まで\" → {\"has_timetable_intent\": true}

注意:
- start_time と end_time は必須です (HH:MM 形式)
- period は指定されていない場合は省略してください (バックエンドで計算)
- day は必ず英語 (monday-sunday) で返してください
- 疑わしい場合は false を返してください";

    try {
        $parseTimeout = min(10, $this->timeout * 0.33);

        $useMaxCompletionTokens = in_array($this->fallbackModel, ['gpt-5', 'o1', 'o1-preview', 'o1-mini']);

        $requestBody = [
            'model' => $this->fallbackModel,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a timetable parser assistant. Analyze user messages and extract timetable class information. Always return valid JSON.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.3,
        ];

        if ($useMaxCompletionTokens) {
            $requestBody['max_completion_tokens'] = 500;
        } else {
            $requestBody['max_tokens'] = 500;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->timeout((int)$parseTimeout)->post($this->baseUrl . '/chat/completions', $requestBody);

        if ($response->successful()) {
            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? '';

            // Parse JSON response
            $parsedContent = json_decode($content, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                if (!empty($parsedContent['has_timetable_intent']) && $parsedContent['has_timetable_intent'] === true) {
                    Log::info('Timetable intent detected', ['class' => $parsedContent['timetable_class']]);
                    return $parsedContent['timetable_class'];
                }
            }

            // Try to extract JSON from response
            $jsonMatch = [];
            if (preg_match('/\{.*\}/s', $content, $jsonMatch)) {
                $parsedContent = json_decode($jsonMatch[0], true);
                if (json_last_error() === JSON_ERROR_NONE && !empty($parsedContent['has_timetable_intent'])) {
                    if ($parsedContent['has_timetable_intent'] === true) {
                        return $parsedContent['timetable_class'];
                    }
                }
            }
        }
    } catch (\Exception $e) {
        Log::error('Timetable intent parsing failed: ' . $e->getMessage());
    }

    return null;
}
```

---

### Bước 2: Thêm Timetable Creation vào AIController.php

**File**: `backend/app/Http/Controllers/AIController.php`

**Location**: Sau task intent parsing (sau line 1033)

**Code changes**:

#### 2.1. Add timetable intent parsing (sau line 970)

```php
// Parse task intent from user message
$taskData = $this->aiService->parseTaskIntent($request->message);
$createdTask = null;

// Parse timetable intent from user message
$timetableData = $this->aiService->parseTimetableIntent($request->message);
$createdTimetableClass = null;
```

#### 2.2. Add timetable class creation (sau line 1033)

```php
// If timetable intent detected, create timetable class
if ($timetableData) {
    try {
        // Calculate period if not provided (assume 1 period = 1 hour)
        $period = $timetableData['period'] ?? null;
        if (!$period) {
            // Calculate period from time duration
            $start = \Carbon\Carbon::createFromFormat('H:i', $timetableData['start_time']);
            $end = \Carbon\Carbon::createFromFormat('H:i', $timetableData['end_time']);
            $durationHours = $start->diffInHours($end);
            $period = max(1, $durationHours); // At least 1 period
        }

        $createdTimetableClass = \App\Models\TimetableClass::create([
            'user_id' => $user->id,
            'name' => $timetableData['name'],
            'description' => $timetableData['description'] ?? null,
            'room' => $timetableData['room'] ?? null,
            'instructor' => $timetableData['instructor'] ?? null,
            'day' => $timetableData['day'],
            'period' => $period,
            'start_time' => $timetableData['start_time'],
            'end_time' => $timetableData['end_time'],
            'color' => $timetableData['color'] ?? '#6366f1', // Default indigo
            'icon' => $timetableData['icon'] ?? '📚', // Default book icon
        ]);

        Log::info('Timetable class created from context-aware chat', [
            'class_id' => $createdTimetableClass->id,
            'conversation_id' => $conversation->id
        ]);
    } catch (\Exception $e) {
        Log::error('Failed to create timetable class from context-aware chat: ' . $e->getMessage());
        // Continue without timetable creation
    }
}
```

#### 2.3. Add confirmation message (sau line 1115)

```php
// If task was created, add confirmation to AI response
if ($createdTask) {
    $taskConfirmation = "\n\n✅ タスクを作成しました: 「{$createdTask->title}」";
    if ($createdTask->subtasks->count() > 0) {
        $taskConfirmation .= "\n📝 サブタスク: {$createdTask->subtasks->count()}個";
    }
    $aiResponse['message'] = $aiResponse['message'] . $taskConfirmation;
}

// If timetable class was created, add confirmation to AI response
if ($createdTimetableClass) {
    $dayNameMap = [
        'monday' => '月曜日',
        'tuesday' => '火曜日',
        'wednesday' => '水曜日',
        'thursday' => '木曜日',
        'friday' => '金曜日',
        'saturday' => '土曜日',
        'sunday' => '日曜日',
    ];
    $dayJapanese = $dayNameMap[$createdTimetableClass->day] ?? $createdTimetableClass->day;

    $classConfirmation = "\n\n🎓 授業を登録しました: 「{$createdTimetableClass->name}」\n";
    $classConfirmation .= "📅 {$dayJapanese} {$createdTimetableClass->start_time} - {$createdTimetableClass->end_time}";

    if ($createdTimetableClass->room) {
        $classConfirmation .= "\n🏫 教室: {$createdTimetableClass->room}";
    }
    if ($createdTimetableClass->instructor) {
        $classConfirmation .= "\n👨‍🏫 教員: {$createdTimetableClass->instructor}";
    }

    $aiResponse['message'] = $aiResponse['message'] . $classConfirmation;
}
```

#### 2.4. Update response data (line ~1139)

```php
$responseData = [
    'user_message' => $userMessage,
    'assistant_message' => $assistantMessage,
    'created_task' => $createdTask,
    'created_timetable_class' => $createdTimetableClass, // ← NEW
    'task_suggestion' => $aiResponse['task_suggestion'] ?? null,
];
```

---

## 🧪 TEST CASES

### Test Case 1: Simple Class Creation (Japanese)

**User message**:
```
月曜日の9時から10時までCalculusの授業を追加してください
```

**Expected AI parsing**:
```json
{
  "has_timetable_intent": true,
  "timetable_class": {
    "name": "Calculus",
    "day": "monday",
    "start_time": "09:00",
    "end_time": "10:00"
  }
}
```

**Expected backend response**:
- Creates TimetableClass with:
  - name: "Calculus"
  - day: "monday"
  - start_time: "09:00"
  - end_time: "10:00"
  - period: 1 (calculated from duration)
  - user_id: current user

**Expected AI response**:
```
承知しました！月曜日の9時から10時までCalculusの授業を追加しますね。

🎓 授業を登録しました: 「Calculus」
📅 月曜日 09:00 - 10:00
```

---

### Test Case 2: Class with Room and Instructor (Vietnamese)

**User message**:
```
Thêm lớp Programming thứ 3 lúc 10h đến 11h30, phòng A101, thầy Nguyễn Văn A
```

**Expected AI parsing**:
```json
{
  "has_timetable_intent": true,
  "timetable_class": {
    "name": "Programming",
    "day": "tuesday",
    "start_time": "10:00",
    "end_time": "11:30",
    "room": "A101",
    "instructor": "Nguyễn Văn A"
  }
}
```

**Expected AI response**:
```
Đã thêm lớp Programming vào lịch học!

🎓 授業を登録しました: 「Programming」
📅 火曜日 10:00 - 11:30
🏫 教室: A101
👨‍🏫 教員: Nguyễn Văn A
```

---

### Test Case 3: Multiple Classes at Once

**User message**:
```
水曜日に英語の授業を追加して、9時から10時半まで
```

**Expected**: Works individually for each class

---

### Test Case 4: Question (Should NOT Create)

**User message**:
```
今日の授業は何ですか？
```

**Expected AI parsing**:
```json
{
  "has_timetable_intent": false
}
```

**Expected behavior**: No class created, AI answers from existing timetable context

---

### Test Case 5: Schedule Confirmation (Should NOT Create)

**User message**:
```
月曜日のスケジュールを確認
```

**Expected AI parsing**:
```json
{
  "has_timetable_intent": false
}
```

**Expected behavior**: No class created, AI shows Monday schedule

---

## 🎯 CONVERSATION EXAMPLES

### Example 1: Create Class Successfully

```
User: 月曜日の9時から10時までCalculusの授業を追加してください

AI: 承知しました！Calculusの授業を時間割に追加しますね。

🎓 授業を登録しました: 「Calculus」
📅 月曜日 09:00 - 10:00

時間割に追加しました。月曜日の朝は数学から始まりますね！
頑張ってください！
```

---

### Example 2: Create Class with Details (Vietnamese)

```
User: Thêm lớp Database Systems thứ 5 lúc 13h-15h, phòng B205, cô Trần Thị B

AI: Đã thêm lớp Database Systems vào lịch học của bạn!

🎓 授業を登録しました: 「Database Systems」
📅 木曜日 13:00 - 15:00
🏫 教室: B205
👨‍🏫 教員: Trần Thị B

Lớp này kéo dài 2 tiếng, bạn nên chuẩn bị tốt trước khi đến lớp nhé!
```

---

### Example 3: Create Class + Ask About Schedule

```
User: 火曜日に英語の授業を追加して、10時から11時半まで。今週の予定はどうですか？

AI: 英語の授業を追加しました！

🎓 授業を登録しました: 「英語」
📅 火曜日 10:00 - 11:30

今週のスケジュールを確認しました:

**月曜日:**
  - 09:00-10:00: Calculus

**火曜日:**
  - 10:00-11:30: 英語 (今追加しました！)

**木曜日:**
  - 13:00-15:00: Database Systems

今週は3つの授業がありますね。バランスの良いスケジュールです！
```

---

### Example 4: Question Only (No Creation)

```
User: 今日の授業は何ですか？

AI: 今日（月曜日）の授業を確認しました:

- 09:00-10:00: Calculus

1つの授業があります。頑張ってください！
```

---

## ⚠️ CHALLENGES & SOLUTIONS

### Challenge 1: Day Name Parsing (Multilingual)

**Problem**: User might use:
- Japanese: 月曜日, 火曜日
- Vietnamese: thứ 2, thứ 3
- English: Monday, Tuesday
- Casual: 月曜, 火曜 (without 日)

**Solution**:
- AI prompt includes mapping for all formats
- AI returns standardized English day (monday-sunday)
- Backend validates: `in:monday,tuesday,...`

---

### Challenge 2: Time Format Variations

**Problem**: User might use:
- Japanese: 9時, 10時半, 9時15分
- Vietnamese: 9h, 9h30, 9h15
- English: 9am, 9:30am, 2pm
- 24-hour: 14:00, 14:30

**Solution**:
- AI prompt includes examples for all formats
- AI returns standardized HH:MM format
- Backend validates: `date_format:H:i`

---

### Challenge 3: Period Calculation

**Problem**: User might not specify period number

**Solution**:
- Make period optional in AI parsing
- Calculate from duration: 1 hour = 1 period
- Default to 1 if calculation fails

---

### Challenge 4: Ambiguous Messages

**Problem**: "月曜日にCalculusがある" - Is this creating or confirming?

**Solution**:
- AI prompt emphasizes keywords for creation:
  - "追加" (add), "登録" (register), "作成" (create)
  - "thêm", "đăng ký"
- Without these keywords → treat as question
- AI returns `has_timetable_intent: false`

---

### Challenge 5: Conflict Detection

**Problem**: User adds class that overlaps with existing class

**Solution** (Future enhancement):
- Check for conflicts before creating
- Warn user: "月曜日 9:00 に既にPhysicsの授業があります。続けますか？"
- Current version: No conflict detection (create anyway)

---

## 📊 ESTIMATED IMPLEMENTATION TIME

| Task | Time | Details |
|------|------|---------|
| **1. Add parseTimetableIntent()** | 30 min | Copy parseTaskIntent pattern, modify prompt |
| **2. Modify AIController.php** | 45 min | Add parsing call, class creation, confirmation |
| **3. Testing (Manual)** | 45 min | Test Japanese, Vietnamese, English inputs |
| **4. Edge Cases** | 30 min | Handle missing period, invalid times, etc. |
| **5. Documentation** | 15 min | Update API docs |
| **TOTAL** | **2.5 hours** | ~3 hours including breaks |

---

## ✅ IMPLEMENTATION CHECKLIST

- [ ] Add `parseTimetableIntent()` to AIService.php (after line 693)
- [ ] Add `use App\Models\TimetableClass;` to AIController.php imports
- [ ] Add timetable intent parsing call in `sendMessageWithContext()` (after line 970)
- [ ] Add timetable class creation logic (after line 1033)
- [ ] Add confirmation message for timetable class (after line 1115)
- [ ] Update response data to include `created_timetable_class` (line ~1139)
- [ ] Test with Japanese messages
- [ ] Test with Vietnamese messages
- [ ] Test with English messages
- [ ] Test that questions don't trigger creation
- [ ] Test period calculation
- [ ] Test with optional fields (room, instructor)
- [ ] Commit and push changes

---

## 🔄 COMPARISON: Task vs Timetable Creation

| Feature | Task Creation | Timetable Creation |
|---------|--------------|-------------------|
| **Intent Keyword** | タスクを追加, 〜したい | 授業を追加, lớp học |
| **AI Method** | parseTaskIntent() | parseTimetableIntent() |
| **Required Fields** | title, priority | name, day, start_time, end_time |
| **Optional Fields** | description, deadline, tags, subtasks | room, instructor, description, color |
| **Validation** | Priority (low/medium/high) | Day (monday-sunday), Time (HH:MM) |
| **Confirmation** | ✅ タスクを作成しました | 🎓 授業を登録しました |
| **Response Field** | created_task | created_timetable_class |

---

## 🎯 FUTURE ENHANCEMENTS (Phase 2)

### 1. Conflict Detection
- Check for overlapping classes before creating
- Warn user if conflict exists

### 2. Bulk Creation
- Allow creating multiple classes at once
- Example: "月曜日と水曜日に英語の授業を追加"

### 3. Recurring Classes
- Specify class frequency
- Example: "毎週月曜日にCalculus"

### 4. Update/Delete Classes
- Modify existing classes via chat
- Example: "月曜日のCalculusを火曜日に変更"

### 5. Learning Path Integration
- Link classes to learning paths
- AI suggests related learning paths

---

## 📝 CONCLUSION

**✅ AI chatbot CAN create timetable classes automatically**

**Implementation approach**:
1. Mirror task creation pattern
2. Add `parseTimetableIntent()` to AIService
3. Add class creation to AIController
4. Add confirmation message
5. Test with multiple languages

**Benefits**:
- ✅ Natural language class creation
- ✅ Multilingual support (Japanese, Vietnamese, English)
- ✅ No manual form filling needed
- ✅ AI confirms creation immediately
- ✅ Integrates with existing timetable system

**Timeline**: 2.5-3 hours for complete implementation

User chỉ cần nói "月曜日の9時からCalculusの授業を追加して" và AI sẽ tự động tạo lịch học! 🎓

---

**Next Step**: Implement `parseTimetableIntent()` and integrate into AIController.php
