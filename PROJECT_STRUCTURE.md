# Cấu trúc dự án To-Do AI App

## Tổng quan kiến trúc

```
TodoApp/
├── 📱 Android Studio Mobile App (Frontend)
├── 🚀 Laravel 12 Backend (API)
├── 🐳 Docker Environment
├── 📊 Database (MySQL + Redis)
├── 🤖 AI Integration (OpenAI)
└── 📚 Documentation
```

## Cấu trúc thư mục chi tiết

### 1. Root Directory
```
TodoApp/
├── 📁 demo/                    # UI/UX Prototypes
├── 📁 docker/                  # Docker Configuration
├── 📁 docs/                    # Project Documentation
├── 📁 backend/                 # Laravel 12 Backend (sẽ tạo)
├── 📁 mobile-android/          # Android Studio Mobile App (sẽ tạo)
├── 📁 shared/                  # Shared Resources (sẽ tạo)
├── 📄 docker-compose.yml       # Docker Services
├── 📄 Dockerfile              # Laravel Container
├── 📄 env.example             # Environment Template
├── 📄 README-Docker.md        # Docker Documentation
└── 📄 *.sh                    # Docker Scripts
```

### 2. Backend Structure (Laravel 12)
```
backend/
├── 📁 app/
│   ├── 📁 Console/
│   │   └── 📄 Commands/           # Custom Artisan Commands
│   ├── 📁 Http/
│   │   ├── 📁 Controllers/
│   │   │   ├── 📄 Api/            # API Controllers
│   │   │   │   ├── 📄 AuthController.php
│   │   │   │   ├── 📄 TaskController.php
│   │   │   │   ├── 📄 ProjectController.php
│   │   │   │   ├── 📄 SessionController.php
│   │   │   │   ├── 📄 AIController.php
│   │   │   │   └── 📄 StatsController.php
│   │   │   └── 📄 Controller.php
│   │   ├── 📁 Middleware/
│   │   ├── 📁 Requests/
│   │   │   ├── 📄 CreateTaskRequest.php
│   │   │   ├── 📄 UpdateTaskRequest.php
│   │   │   └── 📄 AISummaryRequest.php
│   │   └── 📁 Resources/
│   │       ├── 📄 TaskResource.php
│   │       ├── 📄 ProjectResource.php
│   │       └── 📄 UserResource.php
│   ├── 📁 Models/
│   │   ├── 📄 User.php
│   │   ├── 📄 Task.php
│   │   ├── 📄 Project.php
│   │   ├── 📄 Subtask.php
│   │   ├── 📄 Session.php
│   │   ├── 📄 AISummary.php
│   │   └── 📄 PushToken.php
│   ├── 📁 Services/
│   │   ├── 📄 AIService.php
│   │   ├── 📄 TaskBreakdownService.php
│   │   ├── 📄 NotificationService.php
│   │   └── 📄 StatsService.php
│   ├── 📁 Jobs/
│   │   ├── 📄 ProcessAIBreakdown.php
│   │   ├── 📄 SendNotification.php
│   │   └── 📄 GenerateDailySummary.php
│   ├── 📁 Events/
│   │   ├── 📄 TaskCompleted.php
│   │   ├── 📄 SessionStarted.php
│   │   └── 📄 AISummaryGenerated.php
│   └── 📁 Listeners/
│       ├── 📄 UpdateUserStats.php
│       └── 📄 SendPushNotification.php
├── 📁 config/
│   ├── 📄 ai.php              # AI Configuration
│   ├── 📄 queue.php           # Queue Configuration
│   └── 📄 sanctum.php         # API Authentication
├── 📁 database/
│   ├── 📁 migrations/
│   │   ├── 📄 2024_01_01_000001_create_users_table.php
│   │   ├── 📄 2024_01_01_000002_create_projects_table.php
│   │   ├── 📄 2024_01_01_000003_create_tasks_table.php
│   │   ├── 📄 2024_01_01_000004_create_subtasks_table.php
│   │   ├── 📄 2024_01_01_000005_create_sessions_table.php
│   │   ├── 📄 2024_01_01_000006_create_ai_summaries_table.php
│   │   └── 📄 2024_01_01_000007_create_push_tokens_table.php
│   ├── 📁 seeders/
│   │   ├── 📄 DatabaseSeeder.php
│   │   ├── 📄 UserSeeder.php
│   │   └── 📄 TaskSeeder.php
│   └── 📁 factories/
│       ├── 📄 UserFactory.php
│       ├── 📄 TaskFactory.php
│       └── 📄 ProjectFactory.php
├── 📁 routes/
│   ├── 📄 api.php             # API Routes
│   ├── 📄 web.php             # Web Routes
│   └── 📄 channels.php        # Broadcasting Routes
├── 📁 storage/
│   ├── 📁 app/
│   ├── 📁 logs/
│   └── 📁 framework/
├── 📁 tests/
│   ├── 📁 Feature/
│   │   ├── 📄 AuthTest.php
│   │   ├── 📄 TaskTest.php
│   │   └── 📄 AITest.php
│   └── 📁 Unit/
│       ├── 📄 TaskServiceTest.php
│       └── 📄 AIServiceTest.php
├── 📄 composer.json
├── 📄 artisan
└── 📄 .env
```

### 3. Mobile App Structure (Android Studio)
```
mobile-android/
├── 📁 app/
│   ├── 📁 src/main/
│   │   ├── 📁 java/com/todoapp/
│   │   │   ├── 📁 ui/
│   │   │   │   ├── 📁 auth/
│   │   │   │   │   ├── 📄 LoginActivity.kt
│   │   │   │   │   ├── 📄 RegisterActivity.kt
│   │   │   │   │   └── 📄 ForgotPasswordActivity.kt
│   │   │   │   ├── 📁 home/
│   │   │   │   │   ├── 📄 HomeActivity.kt
│   │   │   │   │   ├── 📄 DashboardFragment.kt
│   │   │   │   │   └── 📄 Top3TasksFragment.kt
│   │   │   │   ├── 📁 tasks/
│   │   │   │   │   ├── 📄 TaskListActivity.kt
│   │   │   │   │   ├── 📄 AddTaskActivity.kt
│   │   │   │   │   └── 📄 EditTaskActivity.kt
│   │   │   │   ├── 📁 focus/
│   │   │   │   │   ├── 📄 FocusActivity.kt
│   │   │   │   │   ├── 📄 PomodoroTimerFragment.kt
│   │   │   │   │   └── 📄 FocusStatsFragment.kt
│   │   │   │   ├── 📁 stats/
│   │   │   │   │   ├── 📄 StatsActivity.kt
│   │   │   │   │   ├── 📄 AnalyticsFragment.kt
│   │   │   │   │   └── 📄 StreakFragment.kt
│   │   │   │   └── 📁 settings/
│   │   │   │       ├── 📄 SettingsActivity.kt
│   │   │   │       ├── 📄 ProfileFragment.kt
│   │   │   │       └── 📄 NotificationSettingsFragment.kt
│   │   │   ├── 📁 data/
│   │   │   │   ├── 📁 api/
│   │   │   │   │   ├── 📄 ApiService.kt
│   │   │   │   │   ├── 📄 AuthApi.kt
│   │   │   │   │   ├── 📄 TaskApi.kt
│   │   │   │   │   └── 📄 AIApi.kt
│   │   │   │   ├── 📁 local/
│   │   │   │   │   ├── 📄 TodoDatabase.kt
│   │   │   │   │   ├── 📄 TaskDao.kt
│   │   │   │   │   ├── 📄 UserDao.kt
│   │   │   │   │   └── 📄 SessionDao.kt
│   │   │   │   └── 📁 repository/
│   │   │   │       ├── 📄 AuthRepositoryImpl.kt
│   │   │   │       ├── 📄 TaskRepositoryImpl.kt
│   │   │   │       └── 📄 SessionRepositoryImpl.kt
│   │   │   ├── 📁 domain/
│   │   │   │   ├── 📁 model/
│   │   │   │   │   ├── 📄 User.kt
│   │   │   │   │   ├── 📄 Task.kt
│   │   │   │   │   ├── 📄 Project.kt
│   │   │   │   │   └── 📄 Session.kt
│   │   │   │   ├── 📁 usecase/
│   │   │   │   │   ├── 📄 LoginUseCase.kt
│   │   │   │   │   ├── 📄 CreateTaskUseCase.kt
│   │   │   │   │   └── 📄 StartFocusUseCase.kt
│   │   │   │   └── 📁 repository/
│   │   │   │       ├── 📄 AuthRepository.kt
│   │   │   │       ├── 📄 TaskRepository.kt
│   │   │   │       └── 📄 SessionRepository.kt
│   │   │   └── 📁 di/
│   │   │       ├── 📄 NetworkModule.kt
│   │   │       ├── 📄 DatabaseModule.kt
│   │   │       └── 📄 RepositoryModule.kt
│   │   ├── 📁 res/
│   │   │   ├── 📁 layout/
│   │   │   │   ├── 📄 activity_login.xml
│   │   │   │   ├── 📄 activity_home.xml
│   │   │   │   ├── 📄 fragment_task_list.xml
│   │   │   │   └── 📄 fragment_focus_timer.xml
│   │   │   ├── 📁 values/
│   │   │   │   ├── 📄 colors.xml
│   │   │   │   ├── 📄 strings.xml
│   │   │   │   ├── 📄 styles.xml
│   │   │   │   └── 📄 themes.xml
│   │   │   ├── 📁 drawable/
│   │   │   │   ├── 📄 ic_add_task.xml
│   │   │   │   ├── 📄 ic_focus_mode.xml
│   │   │   │   └── 📄 ic_stats.xml
│   │   │   └── 📁 menu/
│   │   │       ├── 📄 bottom_navigation.xml
│   │   │       └── 📄 main_menu.xml
│   │   └── 📄 AndroidManifest.xml
│   └── 📄 build.gradle.kts
├── 📁 build.gradle.kts
└── 📄 settings.gradle.kts
└── 📄 analysis_options.yaml
```

### 4. Docker Structure
```
docker/
├── 📄 nginx.conf              # Nginx Configuration
├── 📄 supervisord.conf        # Process Management
├── 📄 php.ini                 # PHP Configuration
└── 📁 mysql/
    └── 📄 init.sql            # Database Initialization
```

### 5. Documentation Structure
```
docs/
├── 📄 tai_liệu_yeu_cầu_hệ_thống_to_do_app_tich_hợp_ai_ca_nhan_v_1_0_laravel_12_flutter_bản_tiếng_việt.md
├── 📄 tech_stack_to_do_app_ai_integration.md
├── 📄 ui_hi_fi_style_guide_to_do_app_theme_b_jade_electric_blue_v_1.md
├── 📄 wireframe_low_fi_to_do_ai_vn_v_1_0_theo_chuẩn_ux_ui_da_neu.md
├── 📄 要件定義書_ai付きto_doアプリ（個人向け）v_1_0_（laravel_12_flutter）.md
└── 📄 API_Documentation.md    # API Documentation (sẽ tạo)
```

## Kiến trúc hệ thống

### 1. Backend Architecture (Laravel 12)
```
┌─────────────────────────────────────────────────────────────┐
│                    Laravel 12 Backend                      │
├─────────────────────────────────────────────────────────────┤
│  📱 API Layer (Controllers)                                │
│  ├── AuthController (Login, Register, OAuth)               │
│  ├── TaskController (CRUD, AI Breakdown)                   │
│  ├── ProjectController (Project Management)                │
│  ├── SessionController (Focus Mode)                        │
│  ├── AIController (AI Integration)                         │
│  └── StatsController (Analytics)                           │
├─────────────────────────────────────────────────────────────┤
│  🏗️ Business Logic Layer (Services)                        │
│  ├── AIService (OpenAI Integration)                        │
│  ├── TaskBreakdownService (AI Task Breakdown)              │
│  ├── NotificationService (Push Notifications)              │
│  └── StatsService (Analytics & Insights)                   │
├─────────────────────────────────────────────────────────────┤
│  📊 Data Layer (Models & Repositories)                     │
│  ├── User, Task, Project, Session Models                   │
│  ├── AISummary, PushToken Models                           │
│  └── Repository Pattern Implementation                     │
├─────────────────────────────────────────────────────────────┤
│  🔄 Queue System (Jobs & Events)                           │
│  ├── ProcessAIBreakdown (Async AI Processing)              │
│  ├── SendNotification (Push Notifications)                 │
│  └── GenerateDailySummary (AI Summaries)                   │
└─────────────────────────────────────────────────────────────┘
```

### 2. Mobile App Architecture (Android Studio)
```
┌─────────────────────────────────────────────────────────────┐
│                Android Studio Mobile App                   │
├─────────────────────────────────────────────────────────────┤
│  🎨 Presentation Layer (UI/UX)                             │
│  ├── Activities (Screens)                                  │
│  ├── Fragments (Reusable Components)                       │
│  ├── Views (XML Layouts)                                   │
│  └── Material Design 3 (Design System)                     │
├─────────────────────────────────────────────────────────────┤
│  🧠 Business Logic Layer (MVVM)                            │
│  ├── ViewModels (State Management)                         │
│  ├── LiveData (Reactive Data)                              │
│  ├── Repository Pattern (Data Abstraction)                 │
│  └── Use Cases (Business Logic)                            │
├─────────────────────────────────────────────────────────────┤
│  📡 Data Layer (Repository Pattern)                        │
│  ├── Remote Data Sources (Retrofit API)                    │
│  ├── Local Data Sources (Room Database)                    │
│  ├── SharedPreferences (Settings)                          │
│  └── Repository Implementations                            │
├─────────────────────────────────────────────────────────────┤
│  🔧 Core Layer (Utilities)                                 │
│  ├── Dependency Injection (Dagger/Hilt)                    │
│  ├── Networking (OkHttp, Retrofit)                         │
│  ├── Database (Room, SQLite)                               │
│  ├── Notifications (Firebase, Local)                       │
│  └── Utils (Extensions, Helpers)                           │
└─────────────────────────────────────────────────────────────┘
```

### 3. Database Schema
```
┌─────────────────────────────────────────────────────────────┐
│                    Database Schema                          │
├─────────────────────────────────────────────────────────────┤
│  👤 users                                                   │
│  ├── id, name, email, password                              │
│  ├── timezone, locale, avatar_url                           │
│  └── created_at, updated_at                                 │
├─────────────────────────────────────────────────────────────┤
│  📁 projects                                                │
│  ├── id, user_id, name, description                         │
│  ├── color, is_archived                                     │
│  └── created_at, updated_at                                 │
├─────────────────────────────────────────────────────────────┤
│  ✅ tasks                                                   │
│  ├── id, user_id, project_id, title, description            │
│  ├── due_at, completed_at, estimated_minutes                │
│  ├── priority, energy_level, status                         │
│  └── created_at, updated_at                                 │
├─────────────────────────────────────────────────────────────┤
│  📋 subtasks                                                │
│  ├── id, task_id, title, description                        │
│  ├── estimated_minutes, order_index                         │
│  ├── is_completed, completed_at                             │
│  └── created_at, updated_at                                 │
├─────────────────────────────────────────────────────────────┤
│  ⏱️ sessions (Focus Mode)                                   │
│  ├── id, user_id, task_id, start_at, end_at                 │
│  ├── duration_minutes, session_type                         │
│  ├── outcome, notes                                         │
│  └── created_at, updated_at                                 │
├─────────────────────────────────────────────────────────────┤
│  🤖 ai_summaries                                            │
│  ├── id, user_id, summary_date                              │
│  ├── highlights, blockers, plan (JSON)                      │
│  ├── insights                                               │
│  └── created_at, updated_at                                 │
├─────────────────────────────────────────────────────────────┤
│  📱 push_tokens                                             │
│  ├── id, user_id, platform, token                           │
│  ├── is_active                                              │
│  └── created_at, updated_at                                 │
└─────────────────────────────────────────────────────────────┘
```

## Technology Stack

### Backend
- **Framework**: Laravel 12
- **Language**: PHP 8.3
- **Database**: MySQL 8.0
- **Cache**: Redis 7
- **Queue**: Laravel Horizon
- **AI**: OpenAI GPT-4
- **Auth**: Laravel Sanctum
- **Testing**: PHPUnit, Pest

### Mobile App
- **Framework**: Android Studio
- **Language**: Kotlin
- **Architecture**: MVVM + Repository Pattern
- **State Management**: ViewModel + LiveData
- **Navigation**: Navigation Component
- **Local Storage**: Room Database, SharedPreferences
- **Networking**: Retrofit, OkHttp
- **Notifications**: Firebase Messaging
- **Testing**: JUnit, Espresso, Mockito

### DevOps
- **Containerization**: Docker, Docker Compose
- **Web Server**: Nginx
- **Process Management**: Supervisor
- **Monitoring**: Laravel Telescope, Horizon
- **CI/CD**: GitHub Actions (sẽ setup)

## Development Workflow

### 1. Setup Development Environment
```bash
# Clone repository
git clone <repository-url>
cd TodoApp

# Setup Docker environment
cp env.example .env
./docker-start.sh

# Access containers
docker-compose exec app bash
docker-compose exec mysql mysql -u todo_user -p todo_app
```

### 2. Backend Development
```bash
# Install dependencies
composer install

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Start queue worker
php artisan queue:work

# Run tests
php artisan test
```

### 3. Mobile App Development
```bash
# Open in Android Studio
# Install dependencies via Gradle
# Run app on emulator or device

# Run tests
./gradlew test
./gradlew connectedAndroidTest

# Build APK
./gradlew assembleDebug
./gradlew assembleRelease
```

## Deployment Strategy

### Development
- Docker Compose local environment
- Hot reload for both backend and mobile
- Local database with sample data

### Staging
- Docker containers on VPS
- Staging database
- TestFlight/Play Console internal testing

### Production
- Kubernetes cluster
- Production database with backups
- App Store/Play Store release
- Monitoring and logging

## Next Steps

1. **Setup Laravel Backend**: Tạo cấu trúc thư mục và cài đặt dependencies
2. **Setup Android Mobile**: Tạo cấu trúc thư mục và cài đặt dependencies
3. **Database Migration**: Tạo Laravel migrations từ init.sql
4. **API Development**: Implement REST API endpoints
5. **Mobile App Development**: Implement Android Activities/Fragments và ViewModels
6. **AI Integration**: Integrate OpenAI API
7. **Testing**: Write unit và integration tests
8. **Deployment**: Setup CI/CD pipeline
