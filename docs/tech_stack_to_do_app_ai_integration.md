# Tech Stack - To-Do App tích hợp AI

**Phiên bản:** v1.0  
**Ngày tạo:** 2025-01-18  
**Mục tiêu:** Xây dựng To-Do App tích hợp AI với Flutter + Laravel

---

## 1. Tổng quan kiến trúc

```
┌─────────────────────────────────────────────────────────────┐
│                    Flutter Mobile App                       │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐          │
│  │   iOS App   │ │ Android App │ │  Web App    │          │
│  └─────────────┘ └─────────────┘ └─────────────┘          │
└─────────────────────────────────────────────────────────────┘
                              │
                              │ REST API + HTTPS
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                  Laravel 12 Backend                        │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐          │
│  │   API Layer │ │  Business   │ │   AI        │          │
│  │             │ │   Logic     │ │ Integration │          │
│  └─────────────┘ └─────────────┘ └─────────────┘          │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    Data Layer                              │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐          │
│  │   MySQL 8   │ │   Redis     │ │   File      │          │
│  │  (Primary)  │ │ (Cache/Queue)│ │  Storage    │          │
│  └─────────────┘ └─────────────┘ └─────────────┘          │
└─────────────────────────────────────────────────────────────┘
```

---

## 2. Frontend - Flutter Mobile

### 2.1 Core Framework
- **Flutter SDK**: 3.24.x
- **Dart**: 3.5.x
- **Target Platforms**: iOS 12+, Android API 21+

### 2.2 State Management
```yaml
dependencies:
  flutter_bloc: ^8.1.0
  equatable: ^2.0.5
```

**Lý do chọn BLoC:**
- Predictable state management
- Testable và maintainable
- Phù hợp cho app phức tạp
- Separation of concerns rõ ràng

### 2.3 Navigation
```yaml
dependencies:
  go_router: ^14.0.0
```

**Features:**
- Type-safe navigation
- Deep linking support
- Nested routing
- Route guards

### 2.4 Local Storage
```yaml
dependencies:
  hive: ^2.2.3
  hive_flutter: ^1.1.0
  shared_preferences: ^2.2.0
```

**Usage:**
- Hive: Complex data (tasks, settings)
- SharedPreferences: Simple key-value pairs
- Offline-first approach

### 2.5 Networking
```yaml
dependencies:
  dio: ^5.3.0
  retrofit: ^4.0.0
  json_annotation: ^4.8.0
```

**Features:**
- HTTP client với interceptors
- Automatic JSON serialization
- Request/Response logging
- Error handling

### 2.6 UI Components
```yaml
dependencies:
  flutter_screenutil: ^5.9.0
  cached_network_image: ^3.3.0
  shimmer: ^3.0.0
  lottie: ^2.7.0
```

### 2.7 Notifications
```yaml
dependencies:
  firebase_messaging: ^14.7.0
  flutter_local_notifications: ^16.3.0
  permission_handler: ^11.0.0
```

### 2.8 Development Tools
```yaml
dev_dependencies:
  flutter_test:
    sdk: flutter
  integration_test:
    sdk: flutter
  mockito: ^5.4.0
  build_runner: ^2.4.0
  json_serializable: ^6.7.0
  retrofit_generator: ^8.0.0
```

---

## 3. Backend - Laravel 12

### 3.1 Core Framework
- **Laravel**: 12.x
- **PHP**: 8.3+
- **Web Server**: Nginx 1.24+

### 3.2 Authentication
```php
// composer.json
{
    "require": {
        "laravel/sanctum": "^4.0",
        "laravel/passport": "^12.0"
    }
}
```

**Features:**
- API token authentication
- OAuth2 support
- Multi-device login
- Token refresh

### 3.3 Database & ORM
```php
// composer.json
{
    "require": {
        "laravel/framework": "^12.0",
        "spatie/laravel-query-builder": "^5.0"
    }
}
```

**Eloquent Models:**
```php
// app/Models/Task.php
class Task extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'due_at',
        'priority', 'energy_level', 'status', 'estimated_minutes'
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'completed_at' => 'datetime',
        'estimated_minutes' => 'integer',
        'priority' => 'integer',
        'energy_level' => 'string'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subtasks()
    {
        return $this->hasMany(Subtask::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }
}
```

### 3.4 API Resources
```php
// app/Http/Resources/TaskResource.php
class TaskResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'due_at' => $this->due_at?->toISOString(),
            'priority' => $this->priority,
            'energy_level' => $this->energy_level,
            'status' => $this->status,
            'estimated_minutes' => $this->estimated_minutes,
            'subtasks' => SubtaskResource::collection($this->subtasks),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
```

### 3.5 Queue & Jobs
```php
// composer.json
{
    "require": {
        "laravel/horizon": "^5.0"
    }
}
```

**Job Example:**
```php
// app/Jobs/ProcessAIBreakdown.php
class ProcessAIBreakdown implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Task $task,
        public string $context
    ) {}

    public function handle(OpenAIService $openAI)
    {
        $subtasks = $openAI->breakdownTask($this->task->title, $this->context);
        
        foreach ($subtasks as $subtask) {
            $this->task->subtasks()->create([
                'title' => $subtask['title'],
                'description' => $subtask['description'],
                'estimated_minutes' => $subtask['estimated_minutes'],
                'order' => $subtask['order']
            ]);
        }
    }
}
```

### 3.6 Validation
```php
// app/Http/Requests/CreateTaskRequest.php
class CreateTaskRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string|max:120',
            'description' => 'nullable|string|max:500',
            'due_at' => 'nullable|date|after:now',
            'priority' => 'required|integer|min:1|max:5',
            'energy_level' => 'required|in:low,medium,high',
            'estimated_minutes' => 'nullable|integer|min:0|max:600',
            'project_id' => 'nullable|exists:projects,id'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Tiêu đề là bắt buộc',
            'title.max' => 'Tiêu đề không được vượt quá 120 ký tự',
            'priority.min' => 'Ưu tiên phải từ 1 đến 5',
            'energy_level.in' => 'Mức năng lượng phải là low, medium hoặc high'
        ];
    }
}
```

---

## 4. Database Design

### 4.1 MySQL 8.0 Schema

```sql
-- Users table
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    timezone VARCHAR(50) DEFAULT 'Asia/Ho_Chi_Minh',
    locale VARCHAR(10) DEFAULT 'vi',
    avatar_url VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_created_at (created_at)
);

-- Projects table
CREATE TABLE projects (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    color VARCHAR(7) DEFAULT '#0FA968',
    is_archived BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_user_archived (user_id, is_archived)
);

-- Tasks table
CREATE TABLE tasks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    project_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    due_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    estimated_minutes INT UNSIGNED NULL,
    actual_minutes INT UNSIGNED DEFAULT 0,
    priority TINYINT UNSIGNED DEFAULT 3,
    energy_level ENUM('low', 'medium', 'high') DEFAULT 'medium',
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    INDEX idx_user_status (user_id, status),
    INDEX idx_user_due_at (user_id, due_at),
    INDEX idx_user_priority (user_id, priority),
    FULLTEXT idx_title_description (title, description)
);

-- Subtasks table
CREATE TABLE subtasks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    estimated_minutes INT UNSIGNED NULL,
    order_index INT UNSIGNED DEFAULT 0,
    is_completed BOOLEAN DEFAULT FALSE,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    INDEX idx_task_order (task_id, order_index),
    INDEX idx_task_completed (task_id, is_completed)
);

-- Sessions table (Focus Mode)
CREATE TABLE sessions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    task_id BIGINT UNSIGNED NULL,
    start_at TIMESTAMP NOT NULL,
    end_at TIMESTAMP NULL,
    duration_minutes INT UNSIGNED DEFAULT 0,
    session_type ENUM('work', 'break') DEFAULT 'work',
    outcome ENUM('completed', 'skipped', 'interrupted') DEFAULT 'completed',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE SET NULL,
    INDEX idx_user_start_at (user_id, start_at),
    INDEX idx_user_date (user_id, DATE(start_at))
);

-- AI Summaries table
CREATE TABLE ai_summaries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    summary_date DATE NOT NULL,
    highlights JSON NULL,
    blockers JSON NULL,
    plan JSON NULL,
    insights TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_date (user_id, summary_date),
    INDEX idx_user_date (user_id, summary_date)
);

-- Push Tokens table
CREATE TABLE push_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    platform ENUM('ios', 'android', 'web') NOT NULL,
    token VARCHAR(500) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_platform_token (user_id, platform, token),
    INDEX idx_user_active (user_id, is_active)
);
```

### 4.2 Redis Configuration

```php
// config/database.php
'redis' => [
    'client' => env('REDIS_CLIENT', 'phpredis'),
    
    'options' => [
        'cluster' => env('REDIS_CLUSTER', 'redis'),
        'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
    ],
    
    'default' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_DB', '0'),
    ],
    
    'cache' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_CACHE_DB', '1'),
    ],
    
    'queue' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_QUEUE_DB', '2'),
    ],
],
```

**Redis Usage:**
- Cache: API responses, user sessions
- Queue: Background jobs, AI processing
- Rate limiting: API throttling
- Real-time: WebSocket connections

---

## 5. AI Integration

### 5.1 OpenAI Integration

```php
// composer.json
{
    "require": {
        "openai-php/laravel": "^0.7.0"
    }
}
```

**Service Class:**
```php
// app/Services/OpenAIService.php
class OpenAIService
{
    protected OpenAIClient $client;
    
    public function __construct()
    {
        $this->client = OpenAI::client(config('openai.api_key'));
    }
    
    public function breakdownTask(string $title, string $context = ''): array
    {
        $prompt = "Chia nhỏ task '{$title}' thành 3-5 bước con. Context: {$context}";
        
        $response = $this->client->chat()->create([
            'model' => 'gpt-4',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Bạn là AI assistant chuyên chia nhỏ task thành các bước con. Trả về JSON format.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'functions' => [
                [
                    'name' => 'create_subtasks',
                    'description' => 'Tạo danh sách subtasks',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'subtasks' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'title' => ['type' => 'string'],
                                        'description' => ['type' => 'string'],
                                        'estimated_minutes' => ['type' => 'integer'],
                                        'order' => ['type' => 'integer']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
        
        return json_decode($response->choices[0]->message->functionCall->arguments, true);
    }
    
    public function planToday(array $tasks, string $energyLevel, array $calendar = []): array
    {
        // AI logic for daily planning
        $prompt = "Lập kế hoạch ngày với {$energyLevel} energy level. Tasks: " . json_encode($tasks);
        
        // Implementation...
        return $this->processPlanningResponse($response);
    }
    
    public function generateNudge(string $context, string $state): string
    {
        $prompt = "Tạo lời nhắc nhở ngắn gọn cho context: {$context}, state: {$state}";
        
        $response = $this->client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'max_tokens' => 100,
            'temperature' => 0.7
        ]);
        
        return $response->choices[0]->message->content;
    }
}
```

### 5.2 AI Endpoints

```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/ai/breakdown', [AIController::class, 'breakdownTask']);
    Route::post('/ai/plan-today', [AIController::class, 'planToday']);
    Route::post('/ai/nudge', [AIController::class, 'generateNudge']);
    Route::post('/ai/review', [AIController::class, 'generateReview']);
});
```

```php
// app/Http/Controllers/AIController.php
class AIController extends Controller
{
    public function __construct(
        protected OpenAIService $openAI
    ) {}
    
    public function breakdownTask(BreakdownRequest $request)
    {
        $subtasks = $this->openAI->breakdownTask(
            $request->title,
            $request->context ?? ''
        );
        
        return response()->json([
            'success' => true,
            'data' => $subtasks
        ]);
    }
    
    public function planToday(PlanTodayRequest $request)
    {
        $plan = $this->openAI->planToday(
            $request->tasks,
            $request->energy_level,
            $request->calendar ?? []
        );
        
        return response()->json([
            'success' => true,
            'data' => $plan
        ]);
    }
}
```

---

## 6. Infrastructure & DevOps

### 6.1 Docker Configuration

```dockerfile
# Dockerfile
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    supervisor

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www/storage

EXPOSE 80

CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
```

```yaml
# docker-compose.yml
version: '3.8'

services:
  app:
    build: .
    ports:
      - "80:80"
    volumes:
      - .:/var/www
    environment:
      - APP_ENV=production
      - DB_HOST=mysql
      - REDIS_HOST=redis
    depends_on:
      - mysql
      - redis

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_DATABASE: todo_app
      MYSQL_USER: todo_user
      MYSQL_PASSWORD: todo_password
    volumes:
      - mysql_data:/var/lib/mysql
    ports:
      - "3306:3306"

  redis:
    image: redis:7-alpine
    ports:
      - "6379:6379"
    volumes:
      - redis_data:/data

  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
    volumes:
      - ./nginx.conf:/etc/nginx/nginx.conf
    depends_on:
      - app

volumes:
  mysql_data:
  redis_data:
```

### 6.2 Environment Configuration

```bash
# .env.production
APP_NAME="To-Do AI App"
APP_ENV=production
APP_KEY=base64:your-app-key-here
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=todo_app
DB_USERNAME=todo_user
DB_PASSWORD=todo_password

# Redis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# OpenAI
OPENAI_API_KEY=your-openai-api-key
OPENAI_ORGANIZATION=your-org-id

# Firebase
FCM_SERVER_KEY=your-fcm-server-key

# Queue
QUEUE_CONNECTION=redis

# Cache
CACHE_DRIVER=redis
SESSION_DRIVER=redis

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

### 6.3 CI/CD Pipeline

```yaml
# .github/workflows/deploy.yml
name: Deploy to Production

on:
  push:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          
      - name: Install dependencies
        run: composer install --no-dev --optimize-autoloader
        
      - name: Run tests
        run: php artisan test
        
      - name: Run PHPStan
        run: ./vendor/bin/phpstan analyse

  deploy:
    needs: test
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Deploy to server
        uses: appleboy/ssh-action@v0.1.5
        with:
          host: ${{ secrets.HOST }}
          username: ${{ secrets.USERNAME }}
          key: ${{ secrets.SSH_KEY }}
          script: |
            cd /var/www/todo-app
            git pull origin main
            composer install --no-dev --optimize-autoloader
            php artisan migrate --force
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
            sudo systemctl reload nginx
```

---

## 7. Testing Strategy

### 7.1 Backend Testing

```php
// tests/Feature/TaskTest.php
class TaskTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_user_can_create_task()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/tasks', [
                'title' => 'Test Task',
                'description' => 'Test Description',
                'priority' => 3,
                'energy_level' => 'medium'
            ]);
            
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id', 'title', 'description', 'priority', 'energy_level'
                ]
            ]);
    }
    
    public function test_ai_breakdown_creates_subtasks()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);
        
        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/ai/breakdown", [
                'task_id' => $task->id,
                'context' => 'Learning Flutter'
            ]);
            
        $response->assertStatus(200);
        $this->assertDatabaseHas('subtasks', ['task_id' => $task->id]);
    }
}
```

### 7.2 Frontend Testing

```dart
// test/widget_test.dart
import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:todo_app/main.dart';

void main() {
  group('Task List Widget Tests', () {
    testWidgets('should display task list', (WidgetTester tester) async {
      await tester.pumpWidget(MyApp());
      
      expect(find.byType(ListView), findsOneWidget);
      expect(find.text('Add Task'), findsOneWidget);
    });
    
    testWidgets('should add new task', (WidgetTester tester) async {
      await tester.pumpWidget(MyApp());
      
      await tester.tap(find.text('Add Task'));
      await tester.pumpAndSettle();
      
      await tester.enterText(find.byType(TextField), 'New Task');
      await tester.tap(find.text('Save'));
      await tester.pumpAndSettle();
      
      expect(find.text('New Task'), findsOneWidget);
    });
  });
}
```

---

## 8. Performance Optimization

### 8.1 Database Optimization

```sql
-- Indexes for performance
CREATE INDEX idx_tasks_user_status_due ON tasks(user_id, status, due_at);
CREATE INDEX idx_sessions_user_date ON sessions(user_id, DATE(start_at));
CREATE INDEX idx_subtasks_task_order ON subtasks(task_id, order_index);

-- Query optimization
EXPLAIN SELECT * FROM tasks 
WHERE user_id = 1 
AND status = 'pending' 
AND due_at >= NOW() 
ORDER BY priority DESC, created_at ASC;
```

### 8.2 Caching Strategy

```php
// app/Services/CacheService.php
class CacheService
{
    public function getUserTasks(int $userId): Collection
    {
        return Cache::remember("user_tasks_{$userId}", 300, function () use ($userId) {
            return Task::where('user_id', $userId)
                ->with('subtasks')
                ->orderBy('priority', 'desc')
                ->get();
        });
    }
    
    public function getDailyStats(int $userId, string $date): array
    {
        return Cache::remember("daily_stats_{$userId}_{$date}", 600, function () use ($userId, $date) {
            return [
                'completed_tasks' => Task::where('user_id', $userId)
                    ->whereDate('completed_at', $date)
                    ->count(),
                'focus_time' => Session::where('user_id', $userId)
                    ->whereDate('start_at', $date)
                    ->sum('duration_minutes'),
                'streak_days' => $this->calculateStreak($userId)
            ];
        });
    }
}
```

### 8.3 API Optimization

```php
// app/Http/Controllers/TaskController.php
class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::where('user_id', auth()->id())
            ->with(['subtasks' => function ($query) {
                $query->orderBy('order_index');
            }]);
            
        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }
        
        // Pagination
        $tasks = $query->paginate(20);
        
        return TaskResource::collection($tasks);
    }
}
```

---

## 9. Security Considerations

### 9.1 API Security

```php
// app/Http/Middleware/RateLimitMiddleware.php
class RateLimitMiddleware
{
    public function handle($request, Closure $next)
    {
        $key = 'rate_limit_' . $request->ip();
        $attempts = Redis::incr($key);
        
        if ($attempts === 1) {
            Redis::expire($key, 60); // 1 minute
        }
        
        if ($attempts > 100) { // 100 requests per minute
            return response()->json(['error' => 'Rate limit exceeded'], 429);
        }
        
        return $next($request);
    }
}
```

### 9.2 Data Validation

```php
// app/Http/Requests/UpdateTaskRequest.php
class UpdateTaskRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => 'sometimes|required|string|max:120',
            'description' => 'nullable|string|max:500',
            'due_at' => 'nullable|date|after:now',
            'priority' => 'sometimes|required|integer|min:1|max:5',
            'energy_level' => 'sometimes|required|in:low,medium,high',
            'status' => 'sometimes|required|in:pending,in_progress,completed,cancelled'
        ];
    }
    
    public function authorize()
    {
        $task = Task::find($this->route('task'));
        return $task && $task->user_id === auth()->id();
    }
}
```

---

## 10. Monitoring & Analytics

### 10.1 Application Monitoring

```php
// composer.json
{
    "require": {
        "sentry/sentry-laravel": "^3.0",
        "laravel/telescope": "^4.0"
    }
}
```

### 10.2 Performance Monitoring

```php
// app/Http/Middleware/PerformanceMiddleware.php
class PerformanceMiddleware
{
    public function handle($request, Closure $next)
    {
        $start = microtime(true);
        
        $response = $next($request);
        
        $duration = microtime(true) - $start;
        
        if ($duration > 1.0) { // Log slow requests
            Log::warning('Slow request detected', [
                'url' => $request->url(),
                'method' => $request->method(),
                'duration' => $duration,
                'user_id' => auth()->id()
            ]);
        }
        
        return $response;
    }
}
```

---

## 11. Deployment Checklist

### 11.1 Pre-deployment

- [ ] All tests passing
- [ ] Code review completed
- [ ] Database migrations tested
- [ ] Environment variables configured
- [ ] SSL certificates installed
- [ ] Backup strategy implemented

### 11.2 Post-deployment

- [ ] Health checks passing
- [ ] Performance metrics normal
- [ ] Error rates within acceptable limits
- [ ] User feedback collected
- [ ] Monitoring alerts configured

---

## 12. Cost Estimation

### 12.1 Monthly Costs

| Service | Cost (USD) | Description |
|---------|------------|-------------|
| VPS (4GB RAM) | $20 | DigitalOcean droplet |
| Database | $15 | Managed MySQL |
| Redis | $10 | Managed Redis |
| Storage | $5 | File storage |
| OpenAI API | $50-200 | AI processing |
| Monitoring | $20 | Sentry + Analytics |
| **Total** | **$120-270** | Monthly operational cost |

### 12.2 Development Costs

| Tool/Service | Cost | Description |
|--------------|------|-------------|
| Flutter SDK | Free | Open source |
| Laravel | Free | Open source |
| VS Code | Free | Open source |
| GitHub | Free | Public repos |
| **Total** | **$0** | Development tools |

---

## 13. Timeline & Milestones

### 13.1 Development Phases

**Phase 1: Foundation (4 weeks)**
- [ ] Project setup
- [ ] Authentication system
- [ ] Basic CRUD operations
- [ ] Database design
- [ ] API development

**Phase 2: Core Features (6 weeks)**
- [ ] Task management
- [ ] Focus mode
- [ ] Calendar integration
- [ ] AI integration
- [ ] Push notifications

**Phase 3: Advanced Features (4 weeks)**
- [ ] Statistics & analytics
- [ ] AI coaching
- [ ] Performance optimization
- [ ] Testing & bug fixes

**Phase 4: Production (2 weeks)**
- [ ] Deployment setup
- [ ] Monitoring & logging
- [ ] Security audit
- [ ] App store submission

### 13.2 Success Metrics

- **Performance**: API response time < 300ms
- **Reliability**: 99.9% uptime
- **User Experience**: 2-tap focus start
- **AI Quality**: 85% task breakdown accuracy
- **User Retention**: 35% D7 retention

---

## 14. Conclusion

Tech stack này cung cấp:

✅ **Scalability**: Kiến trúc microservices-ready  
✅ **Performance**: Tối ưu cho mobile và web  
✅ **Maintainability**: Code clean, testable  
✅ **Security**: Best practices, data protection  
✅ **Cost-effective**: Open source, cloud-native  
✅ **AI Integration**: OpenAI, function calling  
✅ **Real-time**: WebSocket, push notifications  
✅ **Offline-first**: Local storage, sync  

**Next Steps:**
1. Setup development environment
2. Create project structure
3. Implement authentication
4. Build core features
5. Integrate AI capabilities
6. Deploy to production

---

*Tài liệu này sẽ được cập nhật thường xuyên theo tiến độ phát triển dự án.*
