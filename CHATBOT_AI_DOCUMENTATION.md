# Chatbot & AI System - Complete Backend Documentation

## Overview
This document provides comprehensive documentation for the AI/Chatbot backend system, including all database tables, models, controllers, and API endpoints related to AI functionality and chat conversations.

---

## Database Tables

### 1. chat_conversations
**Purpose**: Stores chat conversation sessions between users and the AI assistant

**Schema**:
```sql
- id: bigint (Primary Key)
- user_id: bigint (Foreign Key → users.id, CASCADE DELETE)
- title: string(255) - Auto-generated or custom conversation title
- started_at: timestamp - When conversation started
- last_message_at: timestamp - When last message was sent
- message_count: integer - Total messages in conversation
- total_tokens: integer - Total tokens used (for cost tracking)
- metadata: json - Additional conversation metadata
- created_at: timestamp
- updated_at: timestamp
```

**Indexes**:
- `user_id` (for filtering user conversations)
- `last_message_at` (for sorting by recency)

---

### 2. chat_messages
**Purpose**: Stores individual messages within chat conversations

**Schema**:
```sql
- id: bigint (Primary Key)
- conversation_id: bigint (Foreign Key → chat_conversations.id, CASCADE DELETE)
- role: enum('user', 'assistant', 'system') - Message sender type
- content: text - Message content
- tokens: integer - Token count for this message
- metadata: json - Additional message metadata (tool calls, context, etc.)
- created_at: timestamp
- updated_at: timestamp
```

**Indexes**:
- `conversation_id` (for fetching conversation history)
- `created_at` (for chronological ordering)

**Cascade Delete**: When a conversation is deleted, all its messages are deleted

---

### 3. ai_suggestions
**Purpose**: Stores AI-generated task suggestions for users

**Schema**:
```sql
- id: bigint (Primary Key)
- user_id: bigint (Foreign Key → users.id, CASCADE DELETE)
- type: string(50) - Suggestion type (task, break, focus, etc.)
- title: string(255) - Suggestion title
- description: text - Detailed suggestion description
- priority: integer - Suggested priority (1-5)
- estimated_minutes: integer - Suggested time estimate
- suggested_at: timestamp - When suggestion was generated
- accepted: boolean (default: false) - User accepted suggestion
- dismissed: boolean (default: false) - User dismissed suggestion
- feedback: text (nullable) - User feedback on suggestion
- metadata: json - Additional context (reasoning, related tasks, etc.)
- created_at: timestamp
- updated_at: timestamp
```

**Indexes**:
- `user_id` (for user-specific suggestions)
- `suggested_at` (for sorting by recency)
- `accepted` (for filtering accepted suggestions)

---

### 4. ai_interactions
**Purpose**: Logs all AI API calls for monitoring, debugging, and cost tracking

**Schema**:
```sql
- id: bigint (Primary Key)
- user_id: bigint (Foreign Key → users.id, CASCADE DELETE)
- type: string(50) - Interaction type (chat, suggestion, summary, etc.)
- model: string(100) - AI model used (gpt-4, claude-3, etc.)
- prompt_tokens: integer - Input tokens
- completion_tokens: integer - Output tokens
- total_tokens: integer - Total tokens used
- cost: decimal(10,6) - Estimated cost in USD
- duration_ms: integer - API call duration in milliseconds
- success: boolean - Whether call succeeded
- error_message: text (nullable) - Error if failed
- metadata: json - Request/response details
- created_at: timestamp
- updated_at: timestamp
```

**Indexes**:
- `user_id` (for user usage tracking)
- `type` (for filtering by interaction type)
- `created_at` (for time-based analytics)

---

### 5. ai_summaries
**Purpose**: Stores AI-generated summaries of user activity and productivity

**Schema**:
```sql
- id: bigint (Primary Key)
- user_id: bigint (Foreign Key → users.id, CASCADE DELETE)
- type: enum('daily', 'weekly', 'monthly') - Summary period
- period_start: date - Start of summary period
- period_end: date - End of summary period
- summary: text - AI-generated summary text
- highlights: json - Key highlights and achievements
- suggestions: json - AI suggestions for improvement
- metrics: json - Productivity metrics (tasks completed, focus time, etc.)
- generated_at: timestamp - When summary was generated
- viewed: boolean (default: false) - User viewed summary
- metadata: json - Additional summary data
- created_at: timestamp
- updated_at: timestamp
```

**Indexes**:
- `user_id` (for user summaries)
- `type` (for filtering by period type)
- `period_start` (for finding summaries by date)

**Unique Constraint**: `(user_id, type, period_start)` - One summary per user per period

---

### 6. daily_checkins
**Purpose**: Stores daily check-in responses from users (mood, energy, goals)

**Schema**:
```sql
- id: bigint (Primary Key)
- user_id: bigint (Foreign Key → users.id, CASCADE DELETE)
- date: date - Check-in date
- mood: string(50) (nullable) - User mood (happy, neutral, stressed, etc.)
- energy_level: string(50) (nullable) - Energy level (high, medium, low)
- goals: text (nullable) - Daily goals text
- notes: text (nullable) - Additional notes
- completed_at: timestamp (nullable) - When check-in was completed
- created_at: timestamp
- updated_at: timestamp
```

**Indexes**:
- `user_id` (for user check-ins)
- `date` (for filtering by date)

**Unique Constraint**: `(user_id, date)` - One check-in per user per day

---

### 7. daily_reviews
**Purpose**: Stores daily review responses (end-of-day reflection)

**Schema**:
```sql
- id: bigint (Primary Key)
- user_id: bigint (Foreign Key → users.id, CASCADE DELETE)
- date: date - Review date
- accomplishments: text (nullable) - What was accomplished
- challenges: text (nullable) - Challenges faced
- learnings: text (nullable) - What was learned
- tomorrow_goals: text (nullable) - Goals for tomorrow
- rating: integer (nullable) - Day rating (1-5)
- notes: text (nullable) - Additional notes
- completed_at: timestamp (nullable) - When review was completed
- created_at: timestamp
- updated_at: timestamp
```

**Indexes**:
- `user_id` (for user reviews)
- `date` (for filtering by date)

**Unique Constraint**: `(user_id, date)` - One review per user per day

---

## Models

### 1. ChatConversation.php
**Location**: `backend/app/Models/ChatConversation.php`

**Fillable Fields**:
```php
'user_id', 'title', 'started_at', 'last_message_at',
'message_count', 'total_tokens', 'metadata'
```

**Casts**:
```php
'started_at' => 'datetime',
'last_message_at' => 'datetime',
'message_count' => 'integer',
'total_tokens' => 'integer',
'metadata' => 'array'
```

**Relationships**:
- `user()` - BelongsTo User
- `messages()` - HasMany ChatMessage (ordered by created_at)

**Scopes**:
- `byUser($userId)` - Filter by user ID
- `recent()` - Order by last_message_at DESC
- `active()` - Conversations with messages in last 7 days

**Helper Methods**:
- `addMessage($role, $content, $tokens = 0, $metadata = [])` - Add message and update stats
- `updateStats()` - Recalculate message_count and total_tokens
- `generateTitle()` - Auto-generate title from first few messages
- `getLastMessageContent()` - Get content of last message
- `isActive()` - Check if conversation had activity in last 7 days

---

### 2. ChatMessage.php
**Location**: `backend/app/Models/ChatMessage.php`

**Fillable Fields**:
```php
'conversation_id', 'role', 'content', 'tokens', 'metadata'
```

**Casts**:
```php
'tokens' => 'integer',
'metadata' => 'array'
```

**Relationships**:
- `conversation()` - BelongsTo ChatConversation

**Scopes**:
- `byConversation($conversationId)` - Filter by conversation
- `byRole($role)` - Filter by role (user/assistant/system)
- `userMessages()` - Only user messages
- `assistantMessages()` - Only assistant messages
- `ordered()` - Order by created_at ASC

**Helper Methods**:
- `isUserMessage()` - Check if message is from user
- `isAssistantMessage()` - Check if message is from assistant
- `isSystemMessage()` - Check if message is system message

---

### 3. AISuggestion.php
**Location**: `backend/app/Models/AISuggestion.php`

**Fillable Fields**:
```php
'user_id', 'type', 'title', 'description', 'priority',
'estimated_minutes', 'suggested_at', 'accepted', 'dismissed',
'feedback', 'metadata'
```

**Casts**:
```php
'priority' => 'integer',
'estimated_minutes' => 'integer',
'suggested_at' => 'datetime',
'accepted' => 'boolean',
'dismissed' => 'boolean',
'metadata' => 'array'
```

**Relationships**:
- `user()` - BelongsTo User

**Scopes**:
- `byUser($userId)` - Filter by user
- `byType($type)` - Filter by suggestion type
- `pending()` - Not accepted and not dismissed
- `accepted()` - Only accepted suggestions
- `dismissed()` - Only dismissed suggestions
- `recent($days = 7)` - Suggestions from last N days

**Helper Methods**:
- `accept($feedback = null)` - Mark as accepted
- `dismiss($feedback = null)` - Mark as dismissed
- `isPending()` - Check if pending
- `isAccepted()` - Check if accepted
- `isDismissed()` - Check if dismissed

---

### 4. AIInteraction.php
**Location**: `backend/app/Models/AIInteraction.php`

**Fillable Fields**:
```php
'user_id', 'type', 'model', 'prompt_tokens', 'completion_tokens',
'total_tokens', 'cost', 'duration_ms', 'success', 'error_message', 'metadata'
```

**Casts**:
```php
'prompt_tokens' => 'integer',
'completion_tokens' => 'integer',
'total_tokens' => 'integer',
'cost' => 'decimal:6',
'duration_ms' => 'integer',
'success' => 'boolean',
'metadata' => 'array'
```

**Relationships**:
- `user()` - BelongsTo User

**Scopes**:
- `byUser($userId)` - Filter by user
- `byType($type)` - Filter by interaction type
- `successful()` - Only successful interactions
- `failed()` - Only failed interactions
- `expensive($threshold = 0.10)` - Interactions costing more than threshold

**Helper Methods**:
- `calculateCost()` - Calculate cost based on tokens and model
- `isSuccessful()` - Check if successful
- `isFailed()` - Check if failed
- `getDurationSeconds()` - Get duration in seconds

---

### 5. AISummary.php
**Location**: `backend/app/Models/AISummary.php`

**Fillable Fields**:
```php
'user_id', 'type', 'period_start', 'period_end', 'summary',
'highlights', 'suggestions', 'metrics', 'generated_at', 'viewed', 'metadata'
```

**Casts**:
```php
'period_start' => 'date',
'period_end' => 'date',
'highlights' => 'array',
'suggestions' => 'array',
'metrics' => 'array',
'generated_at' => 'datetime',
'viewed' => 'boolean',
'metadata' => 'array'
```

**Relationships**:
- `user()` - BelongsTo User

**Scopes**:
- `byUser($userId)` - Filter by user
- `byType($type)` - Filter by period type (daily/weekly/monthly)
- `forPeriod($start, $end)` - Filter by date range
- `unviewed()` - Only unviewed summaries
- `recent()` - Order by period_start DESC

**Helper Methods**:
- `markAsViewed()` - Mark summary as viewed
- `isViewed()` - Check if viewed
- `getTasksCompleted()` - Get completed tasks count from metrics
- `getTotalFocusTime()` - Get total focus time from metrics

---

## API Endpoints (AIController.php)

### Chat Conversations

#### 1. GET /api/chat/conversations
**Purpose**: Get all chat conversations for authenticated user

**Response**:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Task Planning Discussion",
      "last_message_at": "2025-11-17T10:30:00Z",
      "message_count": 15,
      "total_tokens": 2450
    }
  ]
}
```

---

#### 2. POST /api/chat/conversations
**Purpose**: Create new chat conversation

**Request**:
```json
{
  "title": "Weekly Planning" // Optional, auto-generated if not provided
}
```

**Response**:
```json
{
  "success": true,
  "data": {
    "id": 2,
    "title": "Weekly Planning",
    "started_at": "2025-11-17T11:00:00Z"
  }
}
```

---

#### 3. GET /api/chat/conversations/{id}
**Purpose**: Get conversation with all messages

**Response**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Task Planning",
    "messages": [
      {
        "id": 1,
        "role": "user",
        "content": "Help me plan my week",
        "created_at": "2025-11-17T10:00:00Z"
      },
      {
        "id": 2,
        "role": "assistant",
        "content": "I'll help you plan...",
        "created_at": "2025-11-17T10:00:05Z"
      }
    ]
  }
}
```

---

#### 4. DELETE /api/chat/conversations/{id}
**Purpose**: Delete conversation and all its messages

**Response**:
```json
{
  "success": true,
  "message": "会話を削除しました"
}
```

---

### AI Chat

#### 5. POST /api/ai/chat
**Purpose**: Send message to AI and get context-aware response

**Features**:
- Includes user's upcoming timetable classes (next 7 days)
- Includes user's pending and in-progress tasks
- Maintains conversation history
- Auto-generates conversation title if first message

**Request**:
```json
{
  "message": "What should I focus on today?",
  "conversation_id": 1 // Optional, creates new if not provided
}
```

**Response**:
```json
{
  "success": true,
  "data": {
    "message": "Based on your schedule and tasks, I recommend...",
    "conversation_id": 1,
    "tokens_used": 450,
    "context_included": {
      "timetable_classes": 3,
      "tasks": 5
    }
  }
}
```

**System Context Included**:
- User's name and current date/time
- Next 7 days of timetable classes (subject, time, room, instructor)
- All pending and in-progress tasks with subtasks
- Previous conversation history (last 20 messages)

---

### Task Intelligence

#### 6. POST /api/ai/parse-task
**Purpose**: Parse natural language into task structure using AI

**Request**:
```json
{
  "text": "Study calculus for 2 hours tomorrow at 2pm, it's urgent"
}
```

**Response**:
```json
{
  "success": true,
  "data": {
    "title": "Study calculus",
    "category": "study",
    "priority": 5,
    "estimated_minutes": 120,
    "deadline": "2025-11-18",
    "scheduled_time": "14:00:00",
    "description": "Study session for calculus",
    "suggested_subtasks": [
      "Review lecture notes",
      "Practice problems",
      "Study examples"
    ]
  }
}
```

---

#### 7. POST /api/ai/create-task-from-chat
**Purpose**: Create task directly from chat message

**Request**:
```json
{
  "message": "Remind me to finish the project report by Friday",
  "conversation_id": 1
}
```

**Response**:
```json
{
  "success": true,
  "data": {
    "task": {
      "id": 42,
      "title": "Finish project report",
      "deadline": "2025-11-22",
      "priority": 3
    },
    "message": "I've created a task for you to finish the project report by Friday."
  }
}
```

---

#### 8. POST /api/ai/breakdown
**Purpose**: AI-powered task breakdown into subtasks

**Request**:
```json
{
  "task_id": 42
}
```

**Response**:
```json
{
  "success": true,
  "data": {
    "subtasks": [
      {
        "title": "Research and gather sources",
        "estimated_minutes": 60,
        "sort_order": 0
      },
      {
        "title": "Write introduction",
        "estimated_minutes": 30,
        "sort_order": 1
      },
      {
        "title": "Write main content",
        "estimated_minutes": 90,
        "sort_order": 2
      },
      {
        "title": "Review and edit",
        "estimated_minutes": 30,
        "sort_order": 3
      }
    ],
    "reasoning": "I've broken down your task into 4 manageable subtasks..."
  }
}
```

---

### Daily Intelligence

#### 9. POST /api/ai/daily-suggestions
**Purpose**: Get AI-generated daily task suggestions

**Features**:
- Analyzes user's schedule and tasks
- Suggests optimal tasks based on time, energy, and priority
- Considers upcoming deadlines and timetable

**Response**:
```json
{
  "success": true,
  "data": {
    "suggestions": [
      {
        "id": 15,
        "type": "task",
        "title": "Complete calculus homework",
        "description": "You have calculus class tomorrow and this task is high priority",
        "priority": 5,
        "estimated_minutes": 45,
        "metadata": {
          "reasoning": "Due soon and relevant to upcoming class"
        }
      }
    ],
    "summary": "Based on your schedule, I recommend focusing on..."
  }
}
```

---

#### 10. POST /api/ai/daily-summary
**Purpose**: Generate AI summary of daily activity

**Response**:
```json
{
  "success": true,
  "data": {
    "summary": "Today you completed 5 tasks and spent 3.5 hours in focused work...",
    "highlights": [
      "Completed all calculus homework",
      "Maintained 85% focus score",
      "Finished 2 days ahead of deadline"
    ],
    "suggestions": [
      "Try scheduling breaks between long focus sessions",
      "Consider tackling high-energy tasks earlier in the day"
    ],
    "metrics": {
      "tasks_completed": 5,
      "focus_time_minutes": 210,
      "focus_sessions": 6,
      "average_focus_score": 85
    }
  }
}
```

---

#### 11. POST /api/ai/daily-plan
**Purpose**: Generate AI-powered daily plan

**Features**:
- Analyzes timetable, tasks, deadlines, and user energy patterns
- Creates optimized daily schedule
- Suggests task prioritization

**Response**:
```json
{
  "success": true,
  "data": {
    "plan": "Here's your optimized plan for today...",
    "time_blocks": [
      {
        "time": "09:00-10:30",
        "activity": "Calculus class",
        "type": "timetable"
      },
      {
        "time": "11:00-12:00",
        "activity": "Complete project report (high energy task)",
        "type": "task",
        "task_id": 42
      }
    ],
    "priorities": [
      "Focus on project report before deadline",
      "Review calculus notes after class"
    ]
  }
}
```

---

### Weekly Intelligence

#### 12. POST /api/ai/weekly-insights
**Purpose**: Generate AI-powered weekly insights and analytics

**Response**:
```json
{
  "success": true,
  "data": {
    "summary": "This week you made great progress...",
    "insights": [
      "Your most productive time is 9-11am",
      "You complete more tasks on days with morning exercise",
      "High-priority tasks are completed 20% faster"
    ],
    "achievements": [
      "Completed all tasks on time",
      "Maintained 7-day focus streak"
    ],
    "suggestions": [
      "Schedule difficult tasks during your peak hours (9-11am)",
      "Take more breaks on high-workload days"
    ],
    "metrics": {
      "tasks_completed": 23,
      "total_focus_time": 1200,
      "average_daily_focus": 171,
      "completion_rate": 95
    }
  }
}
```

---

### Check-ins and Reviews

#### 13. POST /api/ai/daily-checkin
**Purpose**: Submit daily check-in

**Request**:
```json
{
  "mood": "focused",
  "energy_level": "high",
  "goals": "Complete project report and study for exam",
  "notes": "Feeling motivated today"
}
```

**Response**:
```json
{
  "success": true,
  "data": {
    "id": 10,
    "date": "2025-11-17",
    "ai_response": "Great to hear you're feeling motivated! Based on your high energy..."
  }
}
```

---

#### 14. POST /api/ai/daily-review
**Purpose**: Submit daily review

**Request**:
```json
{
  "accomplishments": "Completed project report, studied calculus",
  "challenges": "Hard time focusing in the afternoon",
  "learnings": "Morning is my best time for deep work",
  "tomorrow_goals": "Review exam notes, attend study group",
  "rating": 4
}
```

**Response**:
```json
{
  "success": true,
  "data": {
    "id": 10,
    "date": "2025-11-17",
    "ai_insights": "I noticed you struggled with afternoon focus. Consider..."
  }
}
```

---

#### 15. GET /api/ai/checkin-history
**Purpose**: Get check-in history

**Query Parameters**:
- `days` (optional, default: 7) - Number of days to retrieve

**Response**:
```json
{
  "success": true,
  "data": [
    {
      "date": "2025-11-17",
      "mood": "focused",
      "energy_level": "high",
      "goals": "Complete project..."
    }
  ]
}
```

---

#### 16. GET /api/ai/review-history
**Purpose**: Get review history

**Query Parameters**:
- `days` (optional, default: 7) - Number of days to retrieve

**Response**:
```json
{
  "success": true,
  "data": [
    {
      "date": "2025-11-17",
      "accomplishments": "Completed project...",
      "rating": 4
    }
  ]
}
```

---

### Suggestions Management

#### 17. GET /api/ai/suggestions
**Purpose**: Get AI suggestions for user

**Query Parameters**:
- `type` (optional) - Filter by type
- `pending_only` (optional, default: true) - Only show pending suggestions

**Response**:
```json
{
  "success": true,
  "data": [
    {
      "id": 15,
      "type": "task",
      "title": "Take a break",
      "description": "You've been working for 2 hours...",
      "priority": 3,
      "accepted": false,
      "dismissed": false
    }
  ]
}
```

---

#### 18. PUT /api/ai/suggestions/{id}/accept
**Purpose**: Accept AI suggestion

**Request**:
```json
{
  "feedback": "Good suggestion, thanks!" // Optional
}
```

**Response**:
```json
{
  "success": true,
  "data": {
    "id": 15,
    "accepted": true
  },
  "message": "提案を受け入れました"
}
```

---

#### 19. PUT /api/ai/suggestions/{id}/dismiss
**Purpose**: Dismiss AI suggestion

**Request**:
```json
{
  "feedback": "Not relevant right now" // Optional
}
```

**Response**:
```json
{
  "success": true,
  "data": {
    "id": 15,
    "dismissed": true
  },
  "message": "提案を非表示にしました"
}
```

---

### Analytics

#### 20. GET /api/ai/usage-stats
**Purpose**: Get AI usage statistics for user

**Response**:
```json
{
  "success": true,
  "data": {
    "total_interactions": 150,
    "total_tokens": 45000,
    "total_cost": 2.35,
    "by_type": {
      "chat": 80,
      "summary": 25,
      "suggestion": 45
    },
    "average_tokens_per_interaction": 300,
    "last_7_days": {
      "interactions": 25,
      "tokens": 7500
    }
  }
}
```

---

## Key Features

### 1. Context-Aware Chat
The AI chat system includes comprehensive context:
- **User Profile**: Name, preferences, timezone
- **Schedule Context**: Upcoming timetable classes (next 7 days)
- **Task Context**: All pending and in-progress tasks with subtasks
- **Conversation History**: Last 20 messages for continuity

**Implementation** (AIController.php lines 200-250):
```php
// Build context from timetable
$timetableClasses = TimetableClass::where('user_id', $user->id)
    ->whereBetween('date', [now(), now()->addDays(7)])
    ->orderBy('date')->orderBy('start_time')
    ->get();

// Build context from tasks
$tasks = Task::where('user_id', $user->id)
    ->whereIn('status', ['pending', 'in_progress'])
    ->with('subtasks')
    ->get();

// Build system message with full context
$systemMessage = "You are a helpful AI assistant for {$user->name}...
Current date: " . now()->format('Y-m-d H:i') . "
Upcoming classes: [details]
Current tasks: [details]";
```

---

### 2. Natural Language Task Parsing
AI can parse task intent from natural language and extract:
- Title
- Category (study, work, personal, etc.)
- Priority (1-5)
- Estimated time
- Deadline
- Scheduled time
- Suggested subtasks

**Example Prompts**:
- "Study calculus for 2 hours tomorrow at 2pm, it's urgent"
- "Finish the project report by Friday"
- "Call mom this evening"

---

### 3. Proactive Daily Planning
AI generates optimized daily plans by:
1. Analyzing timetable classes
2. Evaluating task priorities and deadlines
3. Considering user's energy patterns (from check-ins)
4. Creating time-blocked schedule
5. Suggesting task prioritization

---

### 4. Weekly Insights
AI analyzes patterns and provides:
- Productivity insights (best times, completion patterns)
- Behavioral insights (exercise correlation, break patterns)
- Achievement highlights
- Personalized suggestions for improvement

---

### 5. Auto-Task Creation from Chat
Users can create tasks naturally through conversation:
- User: "Remind me to finish the project report by Friday"
- AI: Creates task automatically and confirms in chat
- Task is linked to conversation for context

---

### 6. Cost Tracking
All AI interactions are logged with:
- Token usage (prompt + completion)
- Estimated cost (based on model pricing)
- Duration (performance monitoring)
- Success/failure status
- Error messages (for debugging)

**Use cases**:
- Monitor per-user AI costs
- Identify expensive queries
- Optimize prompts
- Debug failures

---

## Integration Points

### 1. With Tasks System
- AI can create, breakdown, and suggest tasks
- Task context included in chat conversations
- Smart task prioritization based on deadlines

### 2. With Timetable System
- Timetable classes included in chat context
- Daily planning considers class schedule
- Suggestions avoid class times

### 3. With Focus Sessions
- Daily summaries include focus time
- Weekly insights analyze focus patterns
- Suggestions consider focus difficulty

### 4. With User Profile
- Personalized responses using user name
- Timezone-aware scheduling
- Energy level tracking (check-ins)

---

## AI Models and Configuration

**Supported Models**:
- GPT-4 (OpenAI) - For complex reasoning
- GPT-3.5 Turbo (OpenAI) - For faster responses
- Claude 3 (Anthropic) - Alternative provider

**Configuration** (likely in .env):
```env
OPENAI_API_KEY=sk-...
OPENAI_MODEL=gpt-4
OPENAI_MAX_TOKENS=2000
AI_DAILY_LIMIT_PER_USER=100
```

---

## Best Practices

### 1. Token Management
- Limit conversation history to last 20 messages
- Truncate long task/timetable lists
- Set max_tokens per request

### 2. Error Handling
- Log all failures to ai_interactions
- Provide fallback responses
- Retry on timeout (with exponential backoff)

### 3. User Privacy
- Never share user data across conversations
- Delete conversations on user request
- Cascade delete on user deletion

### 4. Cost Optimization
- Use cheaper models for simple tasks
- Cache common suggestions
- Implement daily limits per user

---

## Future Enhancements

### Potential Features (not yet implemented):
1. **Voice Input**: Speech-to-text for chat
2. **Image Analysis**: Analyze study materials, screenshots
3. **Multi-modal Learning**: Combine text, images, audio
4. **Collaborative Planning**: Group task planning
5. **Habit Tracking**: AI-powered habit formation insights
6. **Smart Notifications**: AI-driven reminder timing
7. **Learning Style Analysis**: Personalized study recommendations
8. **Productivity Forecasting**: Predict completion likelihood

---

## Related Documentation
- See `TASKS_DATABASE_DOCUMENTATION.md` for task system details
- See `backend/app/Http/Controllers/AIController.php` for implementation
- See database migrations for schema details
