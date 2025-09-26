# Cấu trúc dự án To-Do AI App

## Tổng quan kiến trúc

```
TodoApp/
├── 📱 Flutter Mobile App (Frontend)
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
├── 📁 mobile/                  # Flutter Mobile App (sẽ tạo)
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

### 3. Mobile App Structure (Flutter)
```
mobile/
├── 📁 lib/
│   ├── 📁 core/
│   │   ├── 📁 constants/
│   │   │   ├── 📄 app_constants.dart
│   │   │   ├── 📄 api_constants.dart
│   │   │   └── 📄 theme_constants.dart
│   │   ├── 📁 errors/
│   │   │   ├── 📄 exceptions.dart
│   │   │   └── 📄 failures.dart
│   │   ├── 📁 network/
│   │   │   ├── 📄 api_client.dart
│   │   │   └── 📄 network_info.dart
│   │   └── 📁 utils/
│   │       ├── 📄 validators.dart
│   │       └── 📄 formatters.dart
│   ├── 📁 features/
│   │   ├── 📁 auth/
│   │   │   ├── 📁 data/
│   │   │   │   ├── 📁 datasources/
│   │   │   │   │   └── 📄 auth_remote_datasource.dart
│   │   │   │   ├── 📁 models/
│   │   │   │   │   └── 📄 user_model.dart
│   │   │   │   └── 📁 repositories/
│   │   │   │       └── 📄 auth_repository_impl.dart
│   │   │   ├── 📁 domain/
│   │   │   │   ├── 📁 entities/
│   │   │   │   │   └── 📄 user.dart
│   │   │   │   ├── 📁 repositories/
│   │   │   │   │   └── 📄 auth_repository.dart
│   │   │   │   └── 📁 usecases/
│   │   │   │       ├── 📄 login_usecase.dart
│   │   │   │       └── 📄 register_usecase.dart
│   │   │   └── 📁 presentation/
│   │   │       ├── 📁 pages/
│   │   │       │   ├── 📄 login_page.dart
│   │   │       │   ├── 📄 register_page.dart
│   │   │       │   └── 📄 forgot_password_page.dart
│   │   │       ├── 📁 widgets/
│   │   │       │   ├── 📄 login_form.dart
│   │   │       │   └── 📄 auth_button.dart
│   │   │       └── 📁 bloc/
│   │   │           ├── 📄 auth_bloc.dart
│   │   │           ├── 📄 auth_event.dart
│   │   │           └── 📄 auth_state.dart
│   │   ├── 📁 tasks/
│   │   │   ├── 📁 data/
│   │   │   │   ├── 📁 datasources/
│   │   │   │   │   └── 📄 task_remote_datasource.dart
│   │   │   │   ├── 📁 models/
│   │   │   │   │   ├── 📄 task_model.dart
│   │   │   │   │   └── 📄 subtask_model.dart
│   │   │   │   └── 📁 repositories/
│   │   │   │       └── 📄 task_repository_impl.dart
│   │   │   ├── 📁 domain/
│   │   │   │   ├── 📁 entities/
│   │   │   │   │   ├── 📄 task.dart
│   │   │   │   │   └── 📄 subtask.dart
│   │   │   │   ├── 📁 repositories/
│   │   │   │   │   └── 📄 task_repository.dart
│   │   │   │   └── 📁 usecases/
│   │   │   │       ├── 📄 create_task_usecase.dart
│   │   │   │       ├── 📄 update_task_usecase.dart
│   │   │   │       └── 📄 delete_task_usecase.dart
│   │   │   └── 📁 presentation/
│   │   │       ├── 📁 pages/
│   │   │       │   ├── 📄 home_page.dart
│   │   │       │   ├── 📄 add_task_page.dart
│   │   │       │   └── 📄 edit_task_page.dart
│   │   │       ├── 📁 widgets/
│   │   │       │   ├── 📄 task_card.dart
│   │   │       │   ├── 📄 task_list.dart
│   │   │       │   └── 📄 priority_selector.dart
│   │   │       └── 📁 bloc/
│   │   │           ├── 📄 task_bloc.dart
│   │   │           ├── 📄 task_event.dart
│   │   │           └── 📄 task_state.dart
│   │   ├── 📁 focus/
│   │   │   ├── 📁 data/
│   │   │   ├── 📁 domain/
│   │   │   └── 📁 presentation/
│   │   │       ├── 📁 pages/
│   │   │       │   └── 📄 focus_page.dart
│   │   │       ├── 📁 widgets/
│   │   │       │   ├── 📄 pomodoro_timer.dart
│   │   │       │   └── 📄 focus_stats.dart
│   │   │       └── 📁 bloc/
│   │   │           ├── 📄 focus_bloc.dart
│   │   │           ├── 📄 focus_event.dart
│   │   │           └── 📄 focus_state.dart
│   │   ├── 📁 calendar/
│   │   │   ├── 📁 data/
│   │   │   ├── 📁 domain/
│   │   │   └── 📁 presentation/
│   │   │       ├── 📁 pages/
│   │   │       │   └── 📄 calendar_page.dart
│   │   │       ├── 📁 widgets/
│   │   │       │   ├── 📄 calendar_widget.dart
│   │   │       │   └── 📄 schedule_item.dart
│   │   │       └── 📁 bloc/
│   │   │           ├── 📄 calendar_bloc.dart
│   │   │           ├── 📄 calendar_event.dart
│   │   │           └── 📄 calendar_state.dart
│   │   ├── 📁 stats/
│   │   │   ├── 📁 data/
│   │   │   ├── 📁 domain/
│   │   │   └── 📁 presentation/
│   │   │       ├── 📁 pages/
│   │   │       │   └── 📄 stats_page.dart
│   │   │       ├── 📁 widgets/
│   │   │       │   ├── 📄 stats_chart.dart
│   │   │       │   └── 📄 streak_widget.dart
│   │   │       └── 📁 bloc/
│   │   │           ├── 📄 stats_bloc.dart
│   │   │           ├── 📄 stats_event.dart
│   │   │           └── 📄 stats_state.dart
│   │   └── 📁 settings/
│   │       ├── 📁 data/
│   │       ├── 📁 domain/
│   │       └── 📁 presentation/
│   │           ├── 📁 pages/
│   │           │   └── 📄 settings_page.dart
│   │           ├── 📁 widgets/
│   │           │   ├── 📄 theme_selector.dart
│   │           │   └── 📄 notification_settings.dart
│   │           └── 📁 bloc/
│   │               ├── 📄 settings_bloc.dart
│   │               ├── 📄 settings_event.dart
│   │               └── 📄 settings_state.dart
│   ├── 📁 shared/
│   │   ├── 📁 widgets/
│   │   │   ├── 📄 custom_button.dart
│   │   │   ├── 📄 custom_text_field.dart
│   │   │   ├── 📄 loading_widget.dart
│   │   │   └── 📄 error_widget.dart
│   │   ├── 📁 themes/
│   │   │   ├── 📄 app_theme.dart
│   │   │   ├── 📄 colors.dart
│   │   │   └── 📄 text_styles.dart
│   │   └── 📁 utils/
│   │       ├── 📄 date_utils.dart
│   │       ├── 📄 string_utils.dart
│   │       └── 📄 validation_utils.dart
│   └── 📄 main.dart
├── 📁 assets/
│   ├── 📁 images/
│   │   ├── 📄 logo.png
│   │   ├── 📄 splash_screen.png
│   │   └── 📄 icons/
│   ├── 📁 fonts/
│   │   ├── 📄 NotoSansJP-Regular.ttf
│   │   └── 📄 Inter-Regular.ttf
│   └── 📄 pubspec.yaml
├── 📁 test/
│   ├── 📁 unit/
│   │   ├── 📄 auth_test.dart
│   │   └── 📄 task_test.dart
│   └── 📁 widget/
│       ├── 📄 login_page_test.dart
│       └── 📄 task_card_test.dart
├── 📄 pubspec.yaml
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

### 2. Mobile App Architecture (Flutter)
```
┌─────────────────────────────────────────────────────────────┐
│                    Flutter Mobile App                      │
├─────────────────────────────────────────────────────────────┤
│  🎨 Presentation Layer (UI/UX)                             │
│  ├── Pages (Screens)                                       │
│  ├── Widgets (Reusable Components)                         │
│  └── Themes (Design System)                                │
├─────────────────────────────────────────────────────────────┤
│  🧠 Business Logic Layer (BLoC)                            │
│  ├── AuthBloc (Authentication State)                       │
│  ├── TaskBloc (Task Management)                            │
│  ├── FocusBloc (Focus Mode)                                │
│  ├── CalendarBloc (Scheduling)                             │
│  ├── StatsBloc (Analytics)                                 │
│  └── SettingsBloc (App Settings)                           │
├─────────────────────────────────────────────────────────────┤
│  📡 Data Layer (Repository Pattern)                        │
│  ├── Remote Data Sources (API Calls)                       │
│  ├── Local Data Sources (Hive, SharedPreferences)          │
│  └── Repository Implementations                            │
├─────────────────────────────────────────────────────────────┤
│  🔧 Core Layer (Utilities)                                 │
│  ├── Network (API Client, Connectivity)                    │
│  ├── Storage (Local Database, Cache)                       │
│  ├── Notifications (Push, Local)                           │
│  └── Utils (Validators, Formatters)                        │
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
- **Framework**: Flutter 3.24
- **Language**: Dart 3.5
- **State Management**: BLoC
- **Navigation**: GoRouter
- **Local Storage**: Hive, SharedPreferences
- **Networking**: Dio, Retrofit
- **Notifications**: Firebase Messaging
- **Testing**: Flutter Test, Mockito

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
# Install dependencies
flutter pub get

# Run app
flutter run

# Run tests
flutter test

# Build APK
flutter build apk
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
2. **Setup Flutter Mobile**: Tạo cấu trúc thư mục và cài đặt dependencies
3. **Database Migration**: Tạo Laravel migrations từ init.sql
4. **API Development**: Implement REST API endpoints
5. **Mobile App Development**: Implement Flutter screens và BLoC
6. **AI Integration**: Integrate OpenAI API
7. **Testing**: Write unit và integration tests
8. **Deployment**: Setup CI/CD pipeline
