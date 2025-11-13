# Cấu trúc dự án To-Do AI App

## Tổng quan kiến trúc

```
TodoApp/
├── 📱 Android Studio Mobile App (Frontend) - Kotlin MVVM
├── 🚀 Laravel 12 Backend (API) - PHP 8.3
├── 🐳 Docker Environment - Multi-container
├── 📊 Database (MySQL 8.0 + Redis 7)
├── 🤖 AI Integration (OpenAI GPT-4)
└── 📚 Documentation - Vietnamese & Japanese
```

**Trạng thái**: Production Ready | **Version**: 1.0.0 | **Last Updated**: 13/11/2025

---

## Cấu trúc thư mục chi tiết

### 1. Root Directory

```
ToDoApp/
├── 📁 backend/                  # Laravel 12 Backend API
├── 📁 mobileandroid/            # Android Studio Mobile App
├── 📁 docker/                   # Docker Configuration Files
├── 📁 scripts/                  # Utility Scripts
├── 🐳 docker-compose.yml        # Docker Services (5 services)
├── 🐳 Dockerfile                # PHP 8.3-FPM Container
├── 🐳 builder.config.json       # Build Configuration
├── 📄 README.md                 # Main Documentation
├── 📄 PROJECT_STRUCTURE.md      # This File - Detailed Structure
├── 📄 PROJECT_SUMMARY.md        # Project Summary (370+ lines)
├── 📄 setup-port-forwarding.bat # Port Forwarding Script (Windows)
├── 📄 .env.example              # Environment Template
├── 📄 .gitignore                # Git Ignore Rules
├── 📄 .dockerignore             # Docker Ignore Rules
└── 📄 .cursorignore             # Cursor AI Ignore Rules
```

---

### 2. Backend Structure (Laravel 12) - 65 PHP Files

```
backend/
├── 📁 app/
│   ├── 📁 Http/Controllers/        # 20 API Controllers
│   │   ├── 🔐 AuthController.php                      # Authentication (Register, Login, Logout)
│   │   ├── 📝 TaskController.php                      # CRUD Tasks + Stats + Filtering
│   │   ├── 📋 SubtaskController.php                   # Subtask Management + Reordering
│   │   ├── ⏱️ FocusSessionController.php              # Pomodoro/Focus Timer
│   │   ├── 🎯 FocusEnhancementController.php          # Environment Check, Distraction, Context Switch
│   │   ├── 🤖 AIController.php (52KB)                 # AI Features Hub
│   │   │   # - Task Breakdown AI
│   │   │   # - Daily Suggestions
│   │   │   # - Chat Conversations with Context
│   │   │   # - Daily Plans & Weekly Insights
│   │   ├── ☀️ DailyCheckinController.php              # Daily Check-in Tracking
│   │   ├── 🌙 DailyReviewController.php               # Daily Review & Analytics
│   │   ├── 📊 StatsController.php                     # User Statistics & Performance
│   │   ├── 🗺️ RoadmapApiController.php                # External Roadmap API Integration
│   │   ├── 📚 StudyScheduleController.php (NEW)       # Mandatory Study Scheduling
│   │   ├── 🎓 LearningPathController.php              # Learning Path Management
│   │   ├── 📑 LearningPathTemplateController.php      # Learning Path Templates
│   │   ├── 📅 TimetableController.php                 # School/Class Timetable
│   │   ├── 💻 CheatCodeController.php                 # Cheat Code Library (13 Languages)
│   │   ├── 🧠 KnowledgeController.php                 # Knowledge Base Management
│   │   ├── ⚙️ SettingsController.php                  # User Settings Management
│   │   ├── 🔑 PasswordResetController.php             # Password Reset Flow
│   │   ├── ✉️ EmailVerificationController.php         # Email Verification
│   │   └── 🏗️ Controller.php                          # Base Controller
│   │
│   ├── 📁 Models/                   # 39+ Eloquent Models
│   │   ├── 👤 Core Models
│   │   │   ├── User.php                               # User with Multiple Relationships
│   │   │   ├── Task.php                               # Main Task Model with Focus Features
│   │   │   ├── Subtask.php                            # Task Breakdown
│   │   │   ├── Project.php                            # Project Grouping
│   │   │   └── Tag.php / TaskTag.php                  # Task Tagging System
│   │   │
│   │   ├── 🎓 Learning & Study Models
│   │   │   ├── LearningPath.php                       # Learning Paths with Milestones
│   │   │   ├── LearningPathTemplate.php               # Template Learning Paths
│   │   │   ├── LearningMilestone.php                  # Milestone Tracking
│   │   │   ├── LearningMilestoneTemplate.php          # Milestone Templates
│   │   │   ├── StudySchedule.php (NEW)                # Mandatory Study Schedule
│   │   │   ├── TimetableClass.php                     # School/University Classes
│   │   │   ├── TimetableStudy.php                     # Homework/Review Tracking
│   │   │   ├── TimetableClassWeeklyContent.php        # Weekly Class Content
│   │   │   ├── KnowledgeItem.php                      # Knowledge Base Items
│   │   │   └── KnowledgeCategory.php                  # Knowledge Categories
│   │   │
│   │   ├── ⏱️ Focus & Session Models
│   │   │   ├── FocusSession.php                       # Pomodoro/Focus Sessions
│   │   │   ├── FocusEnvironment.php (NEW)             # Environment Checklist Tracking
│   │   │   ├── DistractionLog.php (NEW)               # Distraction Tracking
│   │   │   └── ContextSwitch.php (NEW)                # Context Switching Analysis
│   │   │
│   │   ├── 🤖 AI & Analytics Models
│   │   │   ├── AISummary.php                          # AI-Generated Summaries
│   │   │   ├── AISuggestion.php                       # AI Suggestions
│   │   │   ├── AIInteraction.php                      # AI Conversation History
│   │   │   ├── DailyCheckin.php                       # Daily Check-in Records
│   │   │   ├── DailyReview.php                        # Daily Review Records
│   │   │   ├── UserStats.php                          # User Statistics
│   │   │   └── PerformanceMetric.php                  # Performance Tracking
│   │   │
│   │   ├── 💬 Social & Notifications
│   │   │   ├── ChatConversation.php                   # Chat Conversations (AI)
│   │   │   ├── ChatMessage.php                        # Chat Messages
│   │   │   ├── Notification.php                       # User Notifications
│   │   │   └── ActivityLog.php                        # Activity Logs
│   │   │
│   │   ├── 💻 Code & Learning Resources
│   │   │   ├── CheatCodeLanguage.php                  # Programming Languages
│   │   │   ├── CheatCodeSection.php                   # Code Sections
│   │   │   ├── CodeExample.php                        # Code Examples
│   │   │   ├── Exercise.php                           # Code Exercises
│   │   │   └── ExerciseTestCase.php                   # Test Cases for Exercises
│   │   │
│   │   └── ⚙️ Settings & Configuration
│   │       ├── UserProfile.php                        # User Profile Details
│   │       └── UserSettings.php                       # User Preferences
│   │
│   ├── 📁 Services/                 # Business Logic Services
│   │   ├── AIService.php            # OpenAI Integration Service
│   │   └── RoadmapApiService.php    # External Roadmap API Integration
│   │
│   ├── 📁 Console/
│   │   └── Commands/                # Custom Artisan Commands
│   │
│   └── 📁 Providers/                # Service Providers
│
├── 📁 database/
│   ├── 📁 migrations/               # 46 Migration Files
│   │   ├── Core Tables
│   │   │   ├── 0001_01_01_000000_create_users_table.php
│   │   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   │   ├── 2025_10_02_042043_create_user_profiles_table.php
│   │   │   ├── 2025_10_02_042206_create_user_settings_table.php
│   │   │   ├── 2025_10_02_042300_create_projects_table.php
│   │   │   ├── 2025_10_02_042313_create_tasks_table.php
│   │   │   └── 2025_10_02_042341_create_subtasks_table.php
│   │   │
│   │   ├── Focus & Session Tables
│   │   │   ├── 2025_10_02_044304_create_focus_sessions_table.php
│   │   │   ├── 2025_11_07_100002_create_focus_environments_table.php
│   │   │   ├── 2025_11_07_100003_create_distraction_logs_table.php
│   │   │   └── 2025_11_07_100004_create_context_switches_table.php
│   │   │
│   │   ├── Daily Rituals Tables
│   │   │   ├── 2025_10_02_044338_create_daily_checkins_table.php
│   │   │   └── 2025_10_02_044410_create_daily_reviews_table.php
│   │   │
│   │   ├── AI Tables
│   │   │   ├── 2025_10_02_044436_create_ai_suggestions_table.php
│   │   │   ├── 2025_11_07_000001_create_chat_conversations_table.php
│   │   │   └── 2025_11_07_000002_create_chat_messages_table.php
│   │   │
│   │   ├── Learning Path Tables
│   │   │   ├── 2025_10_02_042200_create_learning_paths_table.php
│   │   │   ├── 2025_11_01_100000_create_learning_path_templates_table.php
│   │   │   ├── 2025_11_13_120000_create_study_schedules_table.php (NEW)
│   │   │   └── ... (milestones, templates)
│   │   │
│   │   ├── Timetable Tables
│   │   │   ├── 2025_10_31_000000_create_timetable_classes_table.php
│   │   │   ├── 2025_10_31_000001_create_timetable_studies_table.php
│   │   │   └── 2025_11_01_000000_create_timetable_class_weekly_contents_table.php
│   │   │
│   │   ├── Knowledge & Code Tables
│   │   │   ├── 2025_10_02_100002_create_knowledge_categories_table.php
│   │   │   ├── 2025_10_02_100003_create_knowledge_items_table.php
│   │   │   ├── 2025_12_01_000000_create_cheat_code_languages_table.php
│   │   │   ├── 2025_12_01_000002_create_code_examples_table.php
│   │   │   └── 2025_12_01_000003_create_exercises_table.php
│   │   │
│   │   └── ... (Total: 46 migrations, 40+ tables)
│   │
│   ├── 📁 seeders/                  # 21 Database Seeders
│   │   ├── DatabaseSeeder.php       # Main Seeder
│   │   ├── UserSeeder.php           # Test Users
│   │   │
│   │   ├── Learning Path Seeders
│   │   │   ├── LearningPathTemplateSeeder.php
│   │   │   ├── LaravelCourseSeeder.php
│   │   │   ├── JavaBasicCourseSeeder.php
│   │   │   ├── JavaDesignCourseSeeder.php
│   │   │   └── PhpBasicCourseSeeder.php
│   │   │
│   │   └── Cheat Code Seeders (13 Languages)
│   │       ├── CheatCodeLaravelSeeder.php
│   │       ├── CheatCodePythonSeeder.php
│   │       ├── CheatCodeJavaSeeder.php
│   │       ├── CheatCodePhpSeeder.php
│   │       ├── CheatCodeJavaScriptSeeder.php
│   │       ├── CheatCodeKotlinSeeder.php
│   │       ├── CheatCodeBashSeeder.php
│   │       ├── CheatCodeGoSeeder.php
│   │       ├── CheatCodeMysqlSeeder.php
│   │       ├── CheatCodeDockerSeeder.php
│   │       ├── CheatCodeCss3Seeder.php
│   │       ├── CheatCodeHtmlSeeder.php
│   │       ├── CheatCodeYamlSeeder.php
│   │       └── CheatCodeCppSeeder.php
│   │
│   └── 📁 factories/                # Model Factories
│       ├── UserFactory.php
│       └── ...
│
├── 📁 routes/
│   ├── 📄 api.php (287 lines)       # RESTful API Routes (100+ endpoints)
│   ├── 📄 web.php                   # Web Routes
│   └── 📄 console.php               # Artisan Console Routes
│
├── 📁 config/                       # Configuration Files
│   ├── app.php                      # Application Configuration
│   ├── auth.php                     # Authentication Configuration
│   ├── cache.php                    # Cache Configuration (Redis)
│   ├── database.php                 # Database Configuration (MySQL)
│   ├── filesystems.php              # File Storage Configuration
│   ├── logging.php                  # Logging Configuration
│   ├── mail.php                     # Email Configuration
│   ├── queue.php                    # Queue Configuration
│   ├── sanctum.php                  # API Authentication (Token-based)
│   ├── session.php                  # Session Configuration
│   └── services.php                 # Third-party Services Configuration
│
├── 📁 resources/
│   └── 📁 views/                    # Blade Templates
│       ├── 📁 emails/
│       │   ├── verify.blade.php     # Email Verification Template
│       │   └── password-reset.blade.php  # Password Reset Template
│       └── welcome.blade.php
│
├── 📁 storage/                      # Storage Directory
│   ├── 📁 app/                      # Application Files
│   ├── 📁 logs/                     # Log Files
│   └── 📁 framework/                # Framework Cache/Sessions
│
├── 📁 tests/                        # PHPUnit Tests
│   ├── 📁 Feature/                  # Feature Tests
│   └── 📁 Unit/                     # Unit Tests
│
├── 📁 bootstrap/                    # Bootstrap Files
├── 📁 public/                       # Public Web Root
│
├── 📄 composer.json                 # PHP Dependencies
├── 📄 composer.lock                 # Locked Dependencies
├── 📄 artisan                       # Laravel Artisan CLI
├── 📄 .env.example                  # Environment Template
├── 📄 env.example                   # Alternative Environment Template
├── 📄 phpunit.xml                   # PHPUnit Configuration
├── 📄 vite.config.js                # Vite Configuration
├── 📄 package.json                  # NPM Dependencies
└── 📄 README.md                     # Backend Documentation
```

---

### 3. Mobile App Structure (Android Studio) - 114 Kotlin Files

```
mobileandroid/
├── 📁 app/
│   ├── 📁 src/main/
│   │   ├── 📁 java/ecccomp/s2240788/mobile_android/
│   │   │   │
│   │   │   ├── 📁 ui/
│   │   │   │   ├── 📁 activities/          # 30+ Activities
│   │   │   │   │   ├── 🏠 MainActivity.kt                   # Main App Screen
│   │   │   │   │   ├── 💦 SplashActivity.kt                 # Splash Screen with Auto-login
│   │   │   │   │   ├── 🔐 LoginActivity.kt                  # User Login
│   │   │   │   │   ├── 📝 RegisterActivity.kt               # User Registration
│   │   │   │   │   ├── 🔑 ForgotPasswordActivity.kt         # Password Recovery
│   │   │   │   │   ├── 🔓 ResetPasswordActivity.kt          # Password Reset
│   │   │   │   │   ├── 📖 onboardingActivity.kt             # App Onboarding
│   │   │   │   │   ├── 📋 TaskDetailActivity.kt             # Task Details View
│   │   │   │   │   ├── ⏱️ FocusSessionActivity.kt           # Pomodoro Timer
│   │   │   │   │   ├── 📅 TimetableActivity.kt              # School/Class Timetable
│   │   │   │   │   ├── 🧠 KnowledgeActivity.kt              # Knowledge Base
│   │   │   │   │   ├── 📆 CalendarActivity.kt               # Calendar View
│   │   │   │   │   ├── 🎓 PathsActivity.kt                  # Learning Paths
│   │   │   │   │   ├── 📚 LearningPathDetailActivity.kt     # Learning Path Details
│   │   │   │   │   ├── 🎯 MilestoneDetailActivity.kt        # Milestone Tracking
│   │   │   │   │   ├── 💻 CheatCodeDetailActivity.kt        # Cheat Code Viewer
│   │   │   │   │   ├── 🔍 TemplateBrowserActivity.kt        # Browse Learning Templates
│   │   │   │   │   ├── 📑 TemplateListActivity.kt           # Template Listings
│   │   │   │   │   ├── ➕ CreateLearningPathActivity.kt     # Create New Learning Path
│   │   │   │   │   └── 🏗️ BaseActivity.kt                  # Base Class for All Activities
│   │   │   │   │   # ... (30+ total activities)
│   │   │   │   │
│   │   │   │   ├── 📁 viewmodels/          # 25+ ViewModels (MVVM Architecture)
│   │   │   │   │   ├── 🔐 LoginViewModel.kt                 # Authentication Logic
│   │   │   │   │   ├── 📝 RegisterViewModel.kt              # Registration Logic
│   │   │   │   │   ├── 💦 SplashViewModel.kt                # Splash Screen Logic
│   │   │   │   │   ├── 📋 TaskViewModel.kt                  # Task List Management
│   │   │   │   │   ├── ➕ AddTaskViewModel.kt               # Create New Task
│   │   │   │   │   ├── ✏️ EditTaskViewModel.kt              # Edit Existing Task
│   │   │   │   │   ├── 📄 TaskDetailViewModel.kt            # Task Details
│   │   │   │   │   ├── ⏱️ FocusSessionViewModel.kt (21KB)   # Focus Timer Management
│   │   │   │   │   ├── 🌙 DailyReviewViewModel.kt (13KB)    # Daily Review Analytics
│   │   │   │   │   ├── 📅 TimetableViewModel.kt (20KB)      # Timetable Management
│   │   │   │   │   ├── 🎓 PathsViewModel.kt                 # Learning Paths Display
│   │   │   │   │   ├── 🧠 KnowledgeViewModel.kt (14KB)      # Knowledge Base
│   │   │   │   │   ├── 🗺️ RoadmapViewModel.kt               # Roadmap Display
│   │   │   │   │   ├── 📑 TemplateViewModel.kt (20KB)       # Learning Templates
│   │   │   │   │   ├── 📆 CalendarViewModel.kt (13KB)       # Calendar View
│   │   │   │   │   ├── 🤖 AICoachViewModel.kt (15KB)        # AI Coaching
│   │   │   │   │   ├── 📊 StatsViewModel.kt                 # Statistics Display
│   │   │   │   │   ├── 🏠 MainViewModel.kt (16KB)           # Main App Logic
│   │   │   │   │   ├── 🔑 ForgotPasswordViewModel.kt        # Password Recovery
│   │   │   │   │   ├── 🔓 ResetPasswordViewModel.kt         # Password Reset
│   │   │   │   │   ├── 💻 CheatCodeDetailViewModel.kt       # Cheat Code Details
│   │   │   │   │   ├── 🔍 CheatCodeViewModel.kt             # Cheat Code Browsing
│   │   │   │   │   ├── ➕ CreateLearningPathViewModel.kt    # Create Path Logic
│   │   │   │   │   ├── 🚪 LogoutViewModel.kt                # Logout Logic
│   │   │   │   │   └── 📖 KnowledgeDetailViewModel.kt       # Knowledge Item Details
│   │   │   │   │   # ... (25 total ViewModels)
│   │   │   │   │
│   │   │   │   ├── 📁 fragments/           # UI Fragments
│   │   │   │   │   ├── 📁 onboarding/
│   │   │   │   │   └── ... (Multiple UI Fragments)
│   │   │   │   │
│   │   │   │   ├── 📁 dialogs/             # 11+ Custom Dialogs
│   │   │   │   │   ├── AddClassDialogFragment.kt           # Add Class Dialog
│   │   │   │   │   ├── EditWeeklyContentDialogFragment.kt  # Edit Class Content
│   │   │   │   │   ├── EnvironmentChecklistDialog.kt       # Environment Setup
│   │   │   │   │   ├── SubtaskPreviewDialog.kt             # Task Breakdown Preview
│   │   │   │   │   ├── ContextSwitchWarningDialog.kt       # Context Switch Warning
│   │   │   │   │   ├── ComplexitySelectorDialog.kt         # Task Complexity Selector
│   │   │   │   │   ├── ConversationHistoryDialog.kt        # Chat History Dialog
│   │   │   │   │   ├── StartTaskDialogFragment.kt          # Start Task Dialog
│   │   │   │   │   └── ... (11+ total dialogs)
│   │   │   │   │
│   │   │   │   └── 📁 adapters/            # RecyclerView Adapters
│   │   │   │       # - Task List Adapters
│   │   │   │       # - Learning Path Adapters
│   │   │   │       # - Timetable Adapters
│   │   │   │       # - Knowledge Base Adapters
│   │   │   │       # ... (Multiple Adapters)
│   │   │   │
│   │   │   ├── 📁 data/
│   │   │   │   ├── 📁 api/
│   │   │   │   │   └── ApiService.kt       # Retrofit API Client (Main API Interface)
│   │   │   │   │
│   │   │   │   ├── 📁 models/              # Data Classes
│   │   │   │   │   ├── User.kt
│   │   │   │   │   ├── Task.kt
│   │   │   │   │   ├── LearningPath.kt
│   │   │   │   │   ├── FocusSession.kt
│   │   │   │   │   ├── AuthResponse.kt
│   │   │   │   │   └── ... (50+ Data Models)
│   │   │   │   │
│   │   │   │   ├── 📁 repository/          # Repository Pattern Implementation
│   │   │   │   │   ├── AuthRepositoryImpl.kt
│   │   │   │   │   ├── TaskRepositoryImpl.kt
│   │   │   │   │   └── ... (Repository Implementations)
│   │   │   │   │
│   │   │   │   └── 📁 result/              # Result Wrapper
│   │   │   │       └── Result.kt           # Sealed Class for API Results
│   │   │   │
│   │   │   ├── 📁 utils/                   # Utilities & Helpers
│   │   │   │   ├── NetworkModule.kt        # Dependency Injection (Hilt/Dagger)
│   │   │   │   ├── TokenManager.kt         # Token Management with Encryption
│   │   │   │   ├── AuthInterceptor.kt      # Add Auth Headers to Requests
│   │   │   │   ├── ResponseInterceptor.kt  # Handle 401/Token Refresh
│   │   │   │   └── ... (Extension Functions, Helpers)
│   │   │   │
│   │   │   ├── 📁 services/
│   │   │   │   └── FocusTimerService.kt    # Background Focus Timer Service
│   │   │   │
│   │   │   └── 📄 TodoApplication.kt       # Application Class (Entry Point)
│   │   │
│   │   ├── 📁 res/
│   │   │   ├── 📁 drawable/                # 100+ Drawable Resources
│   │   │   │   ├── ic_add_task.xml
│   │   │   │   ├── ic_focus_mode.xml
│   │   │   │   ├── ic_stats.xml
│   │   │   │   ├── ic_learning_path.xml
│   │   │   │   ├── ic_timetable.xml
│   │   │   │   └── ... (100+ icons, backgrounds, shapes)
│   │   │   │
│   │   │   ├── 📁 layout/                  # Activity/Fragment Layouts (XML)
│   │   │   │   ├── activity_main.xml
│   │   │   │   ├── activity_login.xml
│   │   │   │   ├── activity_register.xml
│   │   │   │   ├── activity_task_detail.xml
│   │   │   │   ├── activity_focus_session.xml
│   │   │   │   ├── fragment_task_list.xml
│   │   │   │   ├── fragment_focus_timer.xml
│   │   │   │   └── ... (50+ layout files)
│   │   │   │
│   │   │   ├── 📁 values/                  # App Resources
│   │   │   │   ├── colors.xml              # Color Palette (Jade + Electric Blue Theme)
│   │   │   │   ├── strings.xml             # String Resources
│   │   │   │   ├── dimens.xml              # Dimension Resources
│   │   │   │   ├── styles.xml              # Styles
│   │   │   │   └── themes.xml              # Material Design 3 Themes
│   │   │   │
│   │   │   ├── 📁 mipmap-*/                # App Launcher Icons
│   │   │   │   ├── mipmap-hdpi/
│   │   │   │   ├── mipmap-mdpi/
│   │   │   │   ├── mipmap-xhdpi/
│   │   │   │   ├── mipmap-xxhdpi/
│   │   │   │   └── mipmap-xxxhdpi/
│   │   │   │
│   │   │   └── 📁 assets/                  # Asset Files
│   │   │
│   │   └── 📄 AndroidManifest.xml          # App Manifest
│   │
│   ├── 📁 src/test/                        # Unit Tests (JUnit)
│   ├── 📁 src/androidTest/                 # Instrumented Tests (Espresso)
│   ├── 📄 build.gradle.kts                 # App-level Build Configuration
│   └── 📄 proguard-rules.pro               # ProGuard Rules
│
├── 📁 gradle/                              # Gradle Wrapper
│   └── wrapper/
│       ├── gradle-wrapper.jar
│       └── gradle-wrapper.properties
│
├── 📄 build.gradle.kts                     # Project-level Build Configuration
├── 📄 settings.gradle.kts                  # Project Settings
├── 📄 gradle.properties                    # Gradle Properties
├── 📄 gradlew                              # Gradle Wrapper (Unix)
├── 📄 gradlew.bat                          # Gradle Wrapper (Windows)
└── 📄 local.properties                     # Local SDK Path (Git Ignored)
```

---

### 4. Docker Structure

```
docker/
├── 📄 nginx.conf                    # Nginx Reverse Proxy Configuration
├── 📄 supervisord.conf              # Supervisor Process Management
├── 📄 php.ini                       # PHP 8.3 Configuration
└── 📁 mysql/
    └── 📄 mysql.cnf                 # MySQL Configuration
```

---

### 5. Scripts

```
scripts/
└── 📄 optimize-api.sh               # API Optimization Script
```

---

## Kiến trúc hệ thống

### 1. Backend Architecture (Laravel 12)

```
┌────────────────────────────────────────────────────────────────────┐
│                    Laravel 12 Backend (PHP 8.3)                   │
├────────────────────────────────────────────────────────────────────┤
│  📱 API Layer (20 Controllers)                                    │
│  ├── 🔐 AuthController (Login, Register, OAuth, Email Verify)     │
│  ├── 📝 TaskController (CRUD, AI Breakdown, Stats, Filtering)     │
│  ├── 📋 SubtaskController (Subtask Management, Reordering)        │
│  ├── ⏱️ FocusSessionController (Focus Mode, Pomodoro Timer)       │
│  ├── 🎯 FocusEnhancementController (Environment, Distraction)     │
│  ├── 🤖 AIController (AI Integration Hub - 52KB)                  │
│  ├── ☀️ DailyCheckinController (Morning Planning)                 │
│  ├── 🌙 DailyReviewController (Evening Reflection)                │
│  ├── 📊 StatsController (Analytics & Insights)                    │
│  ├── 🗺️ RoadmapApiController (External API Integration)          │
│  ├── 📚 StudyScheduleController (Mandatory Study Scheduling)      │
│  ├── 🎓 LearningPathController (Learning Paths)                   │
│  ├── 📑 LearningPathTemplateController (Templates)                │
│  ├── 📅 TimetableController (Class Timetable)                     │
│  ├── 💻 CheatCodeController (13 Programming Languages)            │
│  ├── 🧠 KnowledgeController (Knowledge Base)                      │
│  ├── ⚙️ SettingsController (User Settings)                        │
│  ├── 🔑 PasswordResetController (Password Reset)                  │
│  └── ✉️ EmailVerificationController (Email Verification)          │
├────────────────────────────────────────────────────────────────────┤
│  🏗️ Business Logic Layer (Services)                               │
│  ├── AIService (OpenAI GPT-4 Integration)                         │
│  │   ├── Task Breakdown AI                                        │
│  │   ├── Daily Suggestions                                        │
│  │   ├── Chat Conversations with Context                          │
│  │   ├── Daily Plans & Weekly Insights                            │
│  │   └── Learning Recommendations                                 │
│  └── RoadmapApiService (External Roadmap API Integration)         │
├────────────────────────────────────────────────────────────────────┤
│  📊 Data Layer (39+ Models & Relationships)                       │
│  ├── 👤 Core: User, Task, Subtask, Project, Tag                   │
│  ├── 🎓 Learning: LearningPath, Milestone, StudySchedule          │
│  ├── ⏱️ Focus: FocusSession, Environment, Distraction             │
│  ├── 🤖 AI: AISummary, Suggestion, Interaction, ChatMessage       │
│  ├── 📅 Timetable: Class, Study, WeeklyContent                    │
│  ├── 💻 Code: CheatCodeLanguage, Section, Example, Exercise       │
│  ├── 🧠 Knowledge: KnowledgeItem, Category                        │
│  └── 📊 Analytics: UserStats, PerformanceMetric, ActivityLog      │
├────────────────────────────────────────────────────────────────────┤
│  🔄 Queue System (Jobs & Events)                                  │
│  ├── ProcessAIBreakdown (Async AI Processing)                     │
│  ├── SendNotification (Push Notifications)                        │
│  └── GenerateDailySummary (AI Summaries)                          │
├────────────────────────────────────────────────────────────────────┤
│  🔐 Authentication & Security                                     │
│  ├── Laravel Sanctum (Token-based API Authentication)             │
│  ├── Rate Limiting (Throttling)                                   │
│  ├── CSRF Protection                                              │
│  └── Password Hashing (bcrypt)                                    │
└────────────────────────────────────────────────────────────────────┘
```

---

### 2. Mobile App Architecture (Android Studio - Kotlin MVVM)

```
┌────────────────────────────────────────────────────────────────────┐
│                Android Studio Mobile App (Kotlin)                 │
├────────────────────────────────────────────────────────────────────┤
│  🎨 Presentation Layer (UI/UX)                                    │
│  ├── Activities (30+ Screens)                                     │
│  │   ├── MainActivity, SplashActivity, LoginActivity              │
│  │   ├── TaskDetailActivity, FocusSessionActivity                 │
│  │   ├── TimetableActivity, KnowledgeActivity                     │
│  │   ├── LearningPathDetailActivity, PathsActivity                │
│  │   └── ... (30+ total activities)                               │
│  ├── Fragments (Reusable Components)                              │
│  │   └── Onboarding, Task List, Focus Timer, etc.                 │
│  ├── Dialogs (11+ Custom Dialogs)                                 │
│  │   ├── Environment Checklist, Subtask Preview                   │
│  │   ├── Context Switch Warning, Add Class Dialog                 │
│  │   └── ... (11+ total dialogs)                                  │
│  ├── XML Layouts (Material Design 3)                              │
│  │   └── 50+ layout files with responsive design                  │
│  └── Resources (100+ Drawables, Colors, Strings, Themes)          │
├────────────────────────────────────────────────────────────────────┤
│  🧠 Business Logic Layer (MVVM Architecture)                      │
│  ├── ViewModels (25+ ViewModels - State Management)               │
│  │   ├── LoginViewModel, RegisterViewModel                        │
│  │   ├── TaskViewModel, AddTaskViewModel, EditTaskViewModel       │
│  │   ├── FocusSessionViewModel (21KB - Complex Logic)             │
│  │   ├── DailyReviewViewModel (13KB - Analytics)                  │
│  │   ├── TimetableViewModel (20KB - Scheduling)                   │
│  │   ├── KnowledgeViewModel (14KB - Knowledge Base)               │
│  │   ├── TemplateViewModel (20KB - Learning Templates)            │
│  │   ├── CalendarViewModel (13KB - Calendar Logic)                │
│  │   ├── AICoachViewModel (15KB - AI Coaching)                    │
│  │   ├── MainViewModel (16KB - Main App Logic)                    │
│  │   └── ... (25+ total ViewModels)                               │
│  ├── LiveData (Reactive Data)                                     │
│  ├── Repository Pattern (Data Abstraction)                        │
│  └── Use Cases (Business Rules)                                   │
├────────────────────────────────────────────────────────────────────┤
│  📡 Data Layer (Repository Pattern)                               │
│  ├── Remote Data Sources                                          │
│  │   ├── ApiService.kt (Retrofit API Client)                      │
│  │   ├── AuthInterceptor (Add Token Headers)                      │
│  │   └── ResponseInterceptor (Handle 401/Auto Token Refresh)      │
│  ├── Local Data Sources                                           │
│  │   ├── EncryptedSharedPreferences (Secure Token Storage)        │
│  │   └── SharedPreferences (Settings)                             │
│  └── Repository Implementations (Data Abstraction)                │
│      └── AuthRepositoryImpl, TaskRepositoryImpl, etc.             │
├────────────────────────────────────────────────────────────────────┤
│  🔧 Core Layer (Utilities & Services)                             │
│  ├── Dependency Injection (NetworkModule)                         │
│  ├── Networking (OkHttp, Retrofit)                                │
│  ├── TokenManager (Encrypted Token Management)                    │
│  ├── FocusTimerService (Background Service)                       │
│  └── Utils (Extensions, Helpers)                                  │
└────────────────────────────────────────────────────────────────────┘
```

---

### 3. Database Schema (MySQL 8.0 + Redis 7)

#### MySQL Database Tables (40+ Tables)

```
┌────────────────────────────────────────────────────────────────────┐
│                    MySQL Database Schema                          │
├────────────────────────────────────────────────────────────────────┤
│  👤 Users & Profiles                                              │
│  ├── users (id, name, email, password, timezone, locale)          │
│  ├── user_profiles (bio, avatar_url, date_of_birth)               │
│  ├── user_settings (preferences, notifications)                   │
│  └── user_stats (tasks_completed, focus_minutes, streaks)         │
├────────────────────────────────────────────────────────────────────┤
│  📝 Tasks & Projects                                              │
│  ├── tasks (id, user_id, project_id, title, description)          │
│  │   └── Fields: due_at, completed_at, estimated_minutes          │
│  │              priority, energy_level, status                    │
│  ├── subtasks (id, task_id, title, order_index, is_completed)     │
│  ├── projects (id, user_id, name, description, color)             │
│  └── task_tags (task_id, tag_id) + tags (id, name)                │
├────────────────────────────────────────────────────────────────────┤
│  ⏱️ Focus & Sessions                                              │
│  ├── focus_sessions (id, user_id, task_id, start_at, end_at)      │
│  │   └── Fields: duration_minutes, session_type, outcome          │
│  ├── focus_environments (id, user_id, session_id, checklist_data) │
│  ├── distraction_logs (id, session_id, distraction_type, notes)   │
│  └── context_switches (id, user_id, from_task_id, to_task_id)     │
├────────────────────────────────────────────────────────────────────┤
│  🤖 AI Features                                                   │
│  ├── ai_summaries (id, user_id, summary_date, highlights)         │
│  │   └── Fields: blockers, plan (JSON), insights                  │
│  ├── ai_suggestions (id, user_id, suggestion_type, content)       │
│  ├── ai_interactions (id, user_id, interaction_type, request)     │
│  ├── chat_conversations (id, user_id, title, created_at)          │
│  └── chat_messages (id, conversation_id, role, content)           │
├────────────────────────────────────────────────────────────────────┤
│  🎓 Learning Paths & Education                                    │
│  ├── learning_paths (id, user_id, title, description, status)     │
│  ├── learning_path_templates (id, title, description, difficulty) │
│  ├── learning_milestones (id, learning_path_id, title, order)     │
│  ├── learning_milestone_templates (template_id, order)            │
│  ├── study_schedules (NEW - Mandatory Study Scheduling)           │
│  │   └── Fields: learning_path_id, day_of_week, study_time        │
│  │              duration_minutes, reminder_enabled                │
│  ├── timetable_classes (id, user_id, class_name, day_of_week)     │
│  ├── timetable_studies (id, class_id, homework_title, due_date)   │
│  └── timetable_class_weekly_contents (class_id, week_number)      │
├────────────────────────────────────────────────────────────────────┤
│  💻 Code & Knowledge Base                                         │
│  ├── cheat_code_languages (id, name, description, icon)           │
│  │   └── Supports: Laravel, Python, Java, PHP, JavaScript,        │
│  │                 Kotlin, Bash, Go, MySQL, Docker,               │
│  │                 CSS3, HTML, YAML, C++                           │
│  ├── cheat_code_sections (id, language_id, title, order)          │
│  ├── code_examples (id, section_id, title, code, explanation)     │
│  ├── exercises (id, language_id, title, difficulty, description)  │
│  ├── exercise_test_cases (id, exercise_id, input, expected)       │
│  ├── knowledge_categories (id, name, description, parent_id)      │
│  └── knowledge_items (id, category_id, title, content)            │
├────────────────────────────────────────────────────────────────────┤
│  📊 Analytics & Tracking                                          │
│  ├── daily_checkins (id, user_id, checkin_date, mood, goals)      │
│  ├── daily_reviews (id, user_id, review_date, accomplishments)    │
│  ├── performance_metrics (id, user_id, metric_type, value)        │
│  ├── activity_logs (id, user_id, activity_type, description)      │
│  └── notifications (id, user_id, type, title, body, read_at)      │
├────────────────────────────────────────────────────────────────────┤
│  🔐 Authentication & Security                                     │
│  ├── password_resets (email, token, created_at)                   │
│  ├── personal_access_tokens (Laravel Sanctum Tokens)              │
│  └── cache (Redis-backed cache table)                             │
└────────────────────────────────────────────────────────────────────┘
```

#### Redis Cache & Queue

```
Redis 7 (Cache & Queue)
├── Session Storage
├── API Cache (15-minute self-cleaning cache)
├── Queue Jobs (AI Processing, Notifications)
└── Real-time Data (Focus Session Stats)
```

---

## Technology Stack

### Backend (Laravel 12)

```json
{
  "framework": "Laravel 12",
  "language": "PHP 8.3",
  "database": "MySQL 8.0",
  "cache": "Redis 7",
  "queue": "Laravel Queue (Redis Driver)",
  "ai": "OpenAI GPT-4 (openai-php/client ^0.8)",
  "auth": "Laravel Sanctum ^4.2 (Token-based)",
  "testing": "PHPUnit / Pest",
  "dependencies": {
    "laravel/framework": "^11.0",
    "laravel/sanctum": "^4.2",
    "openai-php/client": "^0.8",
    "predis/predis": "^2.2",
    "pusher/pusher-php-server": "^7.2"
  }
}
```

### Mobile App (Android)

```json
{
  "framework": "Android Studio",
  "language": "Kotlin",
  "architecture": "MVVM + Repository Pattern",
  "ui": "Material Design 3 (Jade + Electric Blue Theme)",
  "state_management": "ViewModel + LiveData",
  "navigation": "Navigation Component",
  "local_storage": {
    "secure": "EncryptedSharedPreferences (Token Storage)",
    "regular": "SharedPreferences (Settings)"
  },
  "networking": {
    "client": "Retrofit 2",
    "http": "OkHttp 4",
    "interceptors": ["AuthInterceptor", "ResponseInterceptor"]
  },
  "di": "Dependency Injection (NetworkModule)",
  "background_services": "FocusTimerService",
  "testing": "JUnit + Espresso + Mockito"
}
```

### DevOps (Docker)

```json
{
  "containerization": "Docker + Docker Compose",
  "services": {
    "app": "PHP 8.3-FPM (Laravel)",
    "mysql": "MySQL 8.0",
    "redis": "Redis 7-Alpine",
    "phpmyadmin": "Database UI (Dev only)",
    "redis-commander": "Redis UI (Dev only)"
  },
  "web_server": "Nginx (Reverse Proxy)",
  "process_management": "Supervisor",
  "resource_limits": {
    "cpu": "2.0 cores",
    "memory": "2GB RAM"
  }
}
```

---

## API Endpoints Overview (100+ Endpoints)

### 🔐 Authentication (9 Endpoints)

```
POST   /api/register                    # User Registration
POST   /api/login                       # User Login (Token Generation)
POST   /api/logout                      # User Logout (Token Revocation)
GET    /api/user                        # Get Current User
POST   /api/refresh-token               # Refresh Access Token
POST   /api/forgot-password             # Request Password Reset Email
POST   /api/reset-password              # Reset Password with Token
POST   /api/email/verification-notification  # Resend Verification Email
GET    /api/email/verify/{id}/{hash}   # Verify Email Address
```

### 📝 Tasks (8+ Endpoints)

```
GET    /api/tasks                       # Get All Tasks (with Filters)
POST   /api/tasks                       # Create New Task
GET    /api/tasks/{id}                  # Get Task Details
PUT    /api/tasks/{id}                  # Update Task
DELETE /api/tasks/{id}                  # Delete Task
GET    /api/tasks/stats                 # Task Statistics
PUT    /api/tasks/{id}/complete         # Mark Task as Completed
PUT    /api/tasks/{id}/start            # Start Task (Focus Mode)
```

### 📋 Subtasks (5 Endpoints)

```
GET    /api/tasks/{taskId}/subtasks     # Get All Subtasks
POST   /api/tasks/{taskId}/subtasks     # Create Subtask
PUT    /api/subtasks/{id}               # Update Subtask
DELETE /api/subtasks/{id}               # Delete Subtask
PUT    /api/subtasks/{id}/reorder       # Reorder Subtasks
```

### ⏱️ Focus Sessions (8 Endpoints)

```
POST   /api/sessions/start              # Start Focus Session
GET    /api/sessions/current            # Get Current Session
PUT    /api/sessions/{id}/pause         # Pause Session
PUT    /api/sessions/{id}/resume        # Resume Session
PUT    /api/sessions/{id}/stop          # Stop Session
GET    /api/sessions                    # Get All Sessions
GET    /api/sessions/stats              # Session Statistics
GET    /api/sessions/by-date            # Sessions by Date
```

### 🎯 Focus Enhancement (6+ Endpoints - NEW)

```
POST   /api/focus/environment           # Log Environment Checklist
GET    /api/focus/environment/{sessionId}  # Get Environment Data
POST   /api/focus/distraction           # Log Distraction
GET    /api/focus/distraction/stats     # Distraction Analytics
POST   /api/focus/context-switch        # Log Context Switch
GET    /api/focus/context-switch/stats  # Context Switch Analytics
```

### 🤖 AI Features (15+ Endpoints)

```
POST   /api/ai/breakdown-task           # AI Task Breakdown
GET    /api/ai/daily-suggestions        # Daily AI Suggestions
POST   /api/ai/daily-summary            # Generate Daily Summary
POST   /api/ai/insights                 # AI Insights & Analysis
POST   /api/ai/learning-recommendations # Learning Recommendations
POST   /api/ai/focus-analysis           # Focus Session Analysis
GET    /api/ai/daily-plan               # Get Daily AI Plan
GET    /api/ai/weekly-insights          # Get Weekly Insights
GET    /api/ai/chat/conversations       # Get Chat Conversations
POST   /api/ai/chat/conversations       # Create Chat Conversation
GET    /api/ai/chat/conversations/{id}  # Get Conversation Details
POST   /api/ai/chat/conversations/{id}/messages  # Send Message
DELETE /api/ai/chat/conversations/{id}  # Delete Conversation
```

### 🎓 Learning Paths (12+ Endpoints)

```
GET    /api/learning-paths              # Get All Learning Paths
POST   /api/learning-paths              # Create Learning Path
GET    /api/learning-paths/{id}         # Get Path Details
PUT    /api/learning-paths/{id}         # Update Learning Path
DELETE /api/learning-paths/{id}         # Delete Learning Path
PUT    /api/learning-paths/{id}/complete # Complete Path
GET    /api/learning-paths/{id}/milestones  # Get Milestones
POST   /api/learning-paths/{id}/milestones  # Create Milestone
PUT    /api/milestones/{id}/complete    # Complete Milestone
```

### 📚 Study Schedules (6+ Endpoints - NEW)

```
GET    /api/learning-paths/{id}/study-schedules  # Get Study Schedules
POST   /api/learning-paths/{id}/study-schedules  # Create Study Schedule
GET    /api/study-schedules/today       # Get Today's Study Schedules
GET    /api/study-schedules/stats       # Study Schedule Statistics
PUT    /api/study-schedules/{id}        # Update Study Schedule
POST   /api/study-schedules/{id}/complete  # Mark Schedule as Completed
DELETE /api/study-schedules/{id}        # Delete Study Schedule
```

### 📑 Learning Path Templates (5+ Endpoints)

```
GET    /api/learning-path-templates     # Get All Templates
GET    /api/learning-path-templates/{id}  # Get Template Details
POST   /api/learning-path-templates/{id}/clone  # Clone Template to Learning Path
```

### 📅 Timetable (10+ Endpoints)

```
GET    /api/timetable                   # Get Full Timetable
GET    /api/timetable/classes           # Get All Classes
POST   /api/timetable/classes           # Create Class
GET    /api/timetable/classes/{id}      # Get Class Details
PUT    /api/timetable/classes/{id}      # Update Class
DELETE /api/timetable/classes/{id}      # Delete Class
GET    /api/timetable/studies           # Get All Studies (Homework)
POST   /api/timetable/studies           # Create Study/Homework
PUT    /api/timetable/studies/{id}/toggle  # Toggle Study Completion
DELETE /api/timetable/studies/{id}      # Delete Study
POST   /api/timetable/classes/{id}/weekly-content  # Add Weekly Content
```

### 💻 Cheat Codes (10+ Endpoints - 13 Languages)

```
GET    /api/cheat-code/languages        # Get All Languages (13)
GET    /api/cheat-code/languages/{id}   # Get Language Details
GET    /api/cheat-code/languages/{id}/sections  # Get Sections
GET    /api/cheat-code/sections/{id}/examples   # Get Code Examples
GET    /api/cheat-code/exercises        # Get All Exercises
POST   /api/cheat-code/exercises/{id}/submit  # Submit Exercise Solution
GET    /api/cheat-code/favorites        # Get Favorite Code Examples
POST   /api/cheat-code/favorites        # Add to Favorites
```

**Supported Languages**: Laravel, Python, Java, PHP, JavaScript, Kotlin, Bash, Go, MySQL, Docker, CSS3, HTML, YAML, C++

### 🧠 Knowledge Base (8+ Endpoints)

```
GET    /api/knowledge/categories        # Get Categories
GET    /api/knowledge/items             # Get All Items
POST   /api/knowledge/items             # Create Knowledge Item
GET    /api/knowledge/items/{id}        # Get Item Details
PUT    /api/knowledge/items/{id}        # Update Item
DELETE /api/knowledge/items/{id}        # Delete Item
GET    /api/knowledge/search            # Search Knowledge Base
```

### ☀️ Daily Check-in (5+ Endpoints)

```
POST   /api/daily-checkin               # Submit Daily Check-in
GET    /api/daily-checkin/today         # Get Today's Check-in
GET    /api/daily-checkin/stats         # Check-in Statistics
GET    /api/daily-checkin/history       # Check-in History
```

### 🌙 Daily Review (5+ Endpoints)

```
POST   /api/daily-review                # Submit Daily Review
GET    /api/daily-review/today          # Get Today's Review
GET    /api/daily-review/stats          # Review Statistics
GET    /api/daily-review/insights       # Review Insights
```

### 📊 Statistics & Analytics (10+ Endpoints)

```
GET    /api/stats/dashboard             # Dashboard Overview
GET    /api/stats/tasks                 # Task Statistics
GET    /api/stats/sessions              # Session Statistics
GET    /api/stats/trends                # Trend Analysis
GET    /api/stats/performance           # Performance Metrics
GET    /api/stats/distraction           # Distraction Analytics
GET    /api/stats/context-switch        # Context Switch Analysis
GET    /api/stats/learning-progress     # Learning Progress
```

### 🗺️ Roadmaps (3+ Endpoints)

```
GET    /api/roadmaps/popular            # Get Popular IT Roadmaps
POST   /api/roadmaps/generate           # Generate Roadmap with AI
POST   /api/roadmaps/import             # Import Roadmap as Learning Path
```

### ⚙️ Settings (5+ Endpoints)

```
GET    /api/settings                    # Get User Settings
PUT    /api/settings                    # Update Settings
GET    /api/settings/profile            # Get User Profile
PUT    /api/settings/profile            # Update Profile
POST   /api/settings/avatar             # Upload Avatar
```

---

## Các Features Đã Được Implement

### ✅ Core Features

#### 🔐 Authentication & Authorization
- ✅ User registration with email validation
- ✅ User login with JWT token generation (Sanctum)
- ✅ Password reset flow with email
- ✅ Email verification system
- ✅ Auto-logout on 401 errors (Mobile)
- ✅ Token refresh mechanism (Mobile)
- ✅ Encrypted token storage (Android EncryptedSharedPreferences)
- ✅ Rate limiting on auth endpoints (Laravel Throttle)

#### 📝 Task Management
- ✅ Create, Read, Update, Delete tasks (Full CRUD)
- ✅ Task priorities (1-5 levels)
- ✅ Task status tracking (pending, in_progress, completed)
- ✅ Task subtasks/breakdown with AI
- ✅ Task tags & categorization
- ✅ Task search & filtering (by priority, status, date)
- ✅ Task statistics (completed, overdue, due soon)
- ✅ Task categories/projects

#### ⏱️ Focus & Productivity
- ✅ **Pomodoro Timer / Focus Sessions**
  - Start, Pause, Resume, Stop functionality
  - Session duration tracking
  - Session types (focus, break, long break)
  - Session outcome logging (completed, abandoned, interrupted)

- ✅ **Environment Checklist** (NEW)
  - Pre-focus environment setup checklist
  - Checklist items (clean desk, water, notifications off, etc.)
  - Environment data logging per session

- ✅ **Distraction Logging** (NEW)
  - Log distractions during focus sessions
  - Distraction types (phone, social media, people, noise, etc.)
  - Distraction analytics & insights

- ✅ **Context Switching Detection** (NEW)
  - Track task switches during work
  - Context switch warnings (Mobile dialog)
  - Context switch analytics
  - Productivity impact analysis

- ✅ **Session Statistics**
  - Focus time by date
  - Session trends & patterns
  - Difficulty level tracking
  - Warmup/cooldown/recovery time tracking

#### 🎓 Learning Paths & Study Schedules

- ✅ **Learning Paths**
  - Create custom learning paths with milestones
  - Track learning path progress
  - Learning path templates/cloning
  - Learning path statistics

- ✅ **Mandatory Study Schedules** (NEW)
  - Enforce discipline for learning path users
  - Scheduled study times per week (day of week + time)
  - Duration tracking (study minutes)
  - Reminder settings (enable/disable)
  - Completion/missed session tracking
  - Study schedule statistics
  - Daily study schedule view ("Today's Study")

- ✅ **Learning Milestones**
  - Milestone creation & tracking
  - Milestone ordering
  - Milestone completion

- ✅ **Knowledge Base**
  - Knowledge items & categories
  - Knowledge search
  - Knowledge item tagging

#### 🤖 AI Features (OpenAI GPT-4)

- ✅ **AI Task Breakdown**
  - Break down complex tasks into subtasks
  - AI-generated subtask titles & descriptions
  - Estimated time for each subtask

- ✅ **Daily AI Suggestions**
  - AI-powered task recommendations
  - Priority suggestions
  - Daily focus recommendations

- ✅ **Daily AI Summaries**
  - End-of-day summary generation
  - Highlights, blockers, and action plan
  - AI insights & analysis

- ✅ **AI Chat Conversations**
  - Context-aware AI coaching
  - Conversation history
  - Multiple conversation threads
  - Task context awareness

- ✅ **AI Insights**
  - Learning recommendations
  - Focus session analysis
  - Performance insights
  - Motivational messages

- ✅ **Daily & Weekly Planning**
  - AI-generated daily plans
  - Weekly insights & trends
  - Goal recommendations

#### 💻 Educational Resources (Cheat Code Library)

- ✅ **13 Programming Languages Supported**
  - Laravel, Python, Java, PHP, JavaScript, Kotlin
  - Bash, Go, MySQL, Docker
  - CSS3, HTML, YAML, C++

- ✅ **Code Examples**
  - Organized by sections
  - Code syntax highlighting
  - Explanations & best practices

- ✅ **Code Exercises**
  - Practice exercises with test cases
  - Exercise submission system
  - Progress tracking
  - Difficulty levels

- ✅ **Code Favorites**
  - Bookmark favorite code examples
  - Quick access to bookmarks

#### 📊 Analytics & Statistics

- ✅ **User Statistics Dashboard**
  - Tasks completed, focus minutes, streaks
  - Performance metrics
  - Trend analysis

- ✅ **Task Completion Trends**
  - Daily, weekly, monthly trends
  - Completion rate tracking

- ✅ **Focus Session Analytics**
  - Total focus time
  - Session success rate
  - Focus patterns & insights

- ✅ **Distraction Analytics** (NEW)
  - Distraction frequency
  - Distraction types breakdown
  - Distraction impact on productivity

- ✅ **Context Switch Analytics** (NEW)
  - Context switch frequency
  - Average switch time
  - Productivity loss estimation

- ✅ **Daily Check-in & Review Stats**
  - Check-in streak tracking
  - Review insights
  - Weekly trends

#### 📅 School/Timetable Management

- ✅ **Class Timetable**
  - Weekly class schedule
  - Class details (name, instructor, room, time)
  - Day of week scheduling

- ✅ **Weekly Class Content**
  - Track weekly class topics
  - Content notes per week

- ✅ **Homework/Study Tracking**
  - Create homework tasks
  - Due date tracking
  - Toggle completion status

#### 🗺️ External Integrations

- ✅ **Roadmap API Integration**
  - Get popular IT roadmaps
  - Generate custom roadmaps with AI
  - Import roadmaps as learning paths

#### ⚙️ User Settings & Preferences

- ✅ **User Profile Management**
  - Profile details (bio, avatar, date of birth)
  - Avatar upload

- ✅ **Settings/Preferences**
  - Notification preferences
  - Language selection
  - Timezone configuration

#### ☀️🌙 Daily Rituals

- ✅ **Daily Check-in** (Morning Planning)
  - Mood tracking
  - Daily goals setting
  - Check-in statistics

- ✅ **Daily Review** (Evening Reflection)
  - Accomplishments logging
  - Blockers identification
  - Review insights & trends

---

## Development Workflow

### 1. Setup Development Environment (Docker)

```bash
# Clone repository
git clone <repository-url>
cd ToDoApp

# Setup environment
cp .env.example .env

# Start Docker services (5 containers)
docker-compose up -d

# Access Laravel container
docker-compose exec app bash

# Inside container:
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
```

### 2. Backend Development (Laravel)

```bash
# Run migrations
php artisan migrate

# Seed database (21 seeders)
php artisan db:seed

# Start queue worker (for AI processing)
php artisan queue:work

# Run tests
php artisan test

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### 3. Mobile App Development (Android Studio)

```bash
# Open project in Android Studio
# File > Open > mobileandroid/

# Install dependencies via Gradle
./gradlew build

# Run app on emulator or device
./gradlew installDebug

# Run unit tests
./gradlew test

# Run instrumented tests
./gradlew connectedAndroidTest

# Build APK
./gradlew assembleDebug
./gradlew assembleRelease
```

### 4. Access Services

```
Laravel Backend:     http://localhost:8080
MySQL Database:      localhost:3308
Redis Cache:         localhost:6379
phpMyAdmin:          http://localhost:8082
Redis Commander:     http://localhost:8081
```

---

## Project Statistics

| Metric | Count |
|--------|-------|
| **Backend Controllers** | 20 |
| **Backend Models** | 39+ |
| **Database Migrations** | 46 |
| **Database Tables** | 40+ |
| **Database Seeders** | 21 |
| **API Endpoints** | 100+ |
| **PHP Files (Backend)** | 65 |
| **Android Activities** | 30+ |
| **Android ViewModels** | 25+ |
| **Kotlin Files** | 114 |
| **Docker Services** | 5 |
| **Programming Languages (Cheat Codes)** | 13 |
| **Total Lines of Code** | 10,000+ |

---

## Docker Services Configuration

### 1. App Service (Laravel Backend)

```yaml
Image: Custom PHP 8.3-FPM (Multi-stage Dockerfile)
Port: 8080
CPU Limit: 2.0 cores
Memory Limit: 2GB RAM
Volumes:
  - ./backend:/var/www/html (Code mounting)
  - ./docker/nginx.conf:/etc/nginx/conf.d/default.conf
  - ./docker/supervisord.conf:/etc/supervisor/conf.d/supervisord.conf
```

### 2. MySQL Service

```yaml
Image: mysql:8.0
Port: 3308
Database: todo_app
User: todo_user
Password: todo_password
Volumes:
  - mysql_data:/var/lib/mysql (Persistent storage)
  - ./docker/mysql/mysql.cnf:/etc/mysql/conf.d/mysql.cnf
```

### 3. Redis Service

```yaml
Image: redis:7-alpine
Port: 6379
Configuration:
  - AOF persistence enabled
  - Max memory: 512MB
  - Eviction policy: allkeys-lru
```

### 4. phpMyAdmin Service (Dev Only)

```yaml
Image: phpmyadmin/phpmyadmin
Port: 8082
Purpose: Database UI for development
```

### 5. Redis Commander Service (Dev Only)

```yaml
Image: rediscommander/redis-commander
Port: 8081
Purpose: Redis UI for development
```

---

## Deployment Strategy

### Development Environment
- ✅ Docker Compose local environment (5 services)
- ✅ Hot reload for both backend and mobile
- ✅ Local MySQL database with sample data
- ✅ Redis cache & queue
- ✅ phpMyAdmin & Redis Commander for debugging

### Staging Environment (Future)
- Docker containers on VPS
- Staging database with anonymized data
- TestFlight/Play Console internal testing
- SSL/TLS certificates

### Production Environment (Future)
- Kubernetes cluster for scalability
- Production MySQL database with automated backups
- Redis cluster for high availability
- App Store/Play Store release
- Monitoring (Laravel Telescope, Horizon)
- Logging (ELK stack)
- CDN for static assets

---

## Security Features

### Backend Security
- ✅ Laravel Sanctum token-based authentication
- ✅ CSRF protection
- ✅ Rate limiting (throttling) on API routes
- ✅ Password hashing (bcrypt)
- ✅ SQL injection protection (Eloquent ORM)
- ✅ XSS protection (blade templating)
- ✅ Environment variable encryption (.env)

### Mobile Security
- ✅ EncryptedSharedPreferences for token storage
- ✅ HTTPS-only API communication
- ✅ Certificate pinning (future enhancement)
- ✅ Auth interceptor for secure token transmission
- ✅ Auto token refresh on 401 errors
- ✅ Auto-logout on authentication failure

---

## Testing Strategy

### Backend Testing (PHPUnit/Pest)
- Unit Tests (app/Tests/Unit/)
- Feature Tests (app/Tests/Feature/)
- Coverage: Authentication, Tasks, AI Services

### Mobile Testing (JUnit/Espresso)
- Unit Tests (src/test/)
- Instrumented Tests (src/androidTest/)
- UI Tests with Espresso
- Mock API responses with Mockito

---

## Documentation Files

```
📚 Documentation
├── README.md (Main project documentation)
├── PROJECT_STRUCTURE.md (This file - 1000+ lines)
├── PROJECT_SUMMARY.md (370+ lines project summary)
├── backend/README.md (Backend-specific documentation)
└── .env.example (Environment configuration template)
```

---

## Next Steps & Recommendations

### ✅ Completed
1. ✅ Laravel backend with 20 controllers
2. ✅ Android mobile app with 30+ activities
3. ✅ Database schema with 46 migrations
4. ✅ AI integration with OpenAI GPT-4
5. ✅ Docker containerization (5 services)
6. ✅ Authentication & security (Sanctum + EncryptedSharedPreferences)
7. ✅ Focus enhancement features (Environment, Distraction, Context Switch)
8. ✅ Mandatory study schedule system
9. ✅ Cheat code library (13 programming languages)

### 📋 Areas for Improvement
1. ❌ **Testing** - Implement comprehensive test suite
   - Backend: Unit + Feature tests (PHPUnit/Pest)
   - Mobile: Unit + UI tests (JUnit/Espresso)

2. ❌ **API Documentation** - Generate API docs
   - Swagger/OpenAPI documentation
   - Postman collection

3. ❌ **Code Comments** - Add PHPDoc/KDoc comments
   - Document complex business logic
   - Add inline comments for clarity

4. ❌ **Error Handling** - Improve error handling
   - Consistent error responses
   - User-friendly error messages

5. ❌ **Logging** - Implement comprehensive logging
   - API request/response logging
   - Error logging with context

6. ❌ **Performance Optimization**
   - Database query optimization (N+1 queries)
   - Caching strategy (Redis)
   - Image optimization (mobile)

### 🚀 Future Enhancements
1. Push Notifications (Firebase Cloud Messaging)
2. Social Features (leaderboards, achievements, sharing)
3. Team Collaboration (shared tasks, projects)
4. Calendar Integration (Google Calendar, Outlook)
5. iOS Version (Swift/SwiftUI)
6. Web Version (Vue.js/React)
7. Advanced Analytics (custom reports, export)
8. Offline Mode (local database sync)
9. Gamification (points, badges, levels)
10. Third-party Integrations (Trello, Notion, GitHub)

---

## Conclusion

ToDoApp là một ứng dụng quản lý nhiệm vụ cao cấp với tích hợp AI, được xây dựng với kiến trúc hiện đại và công nghệ tiên tiến:

- **Backend**: Laravel 12 (PHP 8.3) với 20 controllers, 39+ models, 100+ API endpoints
- **Frontend**: Android Studio (Kotlin MVVM) với 30+ activities, 25+ ViewModels
- **Database**: MySQL 8.0 với 40+ tables, Redis 7 cho cache & queue
- **AI**: OpenAI GPT-4 integration cho task breakdown, suggestions, chat
- **DevOps**: Docker containerization với 5 services

**Trạng thái**: Production Ready (với một số cải tiến được khuyến nghị)

**Tính năng nổi bật**:
- Focus enhancement (Environment checklist, Distraction logging, Context switch detection)
- Mandatory study schedules (Enforce discipline for learning paths)
- Cheat code library (13 programming languages)
- AI coaching & insights
- Comprehensive analytics & statistics

---

**Phiên bản**: 1.0.0
**Ngày cập nhật**: 13/11/2025
**Tác giả**: ToDoApp Development Team
**License**: Proprietary

---

## Contact & Support

- **Repository**: <https://github.com/tringuyenminh209/ToDoApp>
- **Issues**: <https://github.com/tringuyenminh209/ToDoApp/issues>
- **Email**: support@todoapp.com

---

*End of PROJECT_STRUCTURE.md*
