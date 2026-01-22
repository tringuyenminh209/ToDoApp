# To-Do AI App

A modern To-Do application with AI integration built with Laravel 12 backend and Android Studio mobile app.

## 🚀 Features

- **AI-Powered Task Breakdown**: Automatically break down complex tasks into manageable subtasks
- **Focus Mode**: Pomodoro timer with AI-powered nudges and hints
- **Smart Planning**: AI suggests Top 3 tasks for the day based on energy level and schedule
- **Progress Tracking**: Visual progress tracking with streaks and analytics
- **Multi-language Support**: Vietnamese, Japanese, and English
- **Offline Support**: Works offline with local data synchronization

## 🏗️ Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                  Android Studio Mobile App                  │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐          │
│  │   Android   │ │   Kotlin    │ │ Material 3  │          │
│  │     App     │ │   Native    │ │    Design   │          │
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

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 12
- **Language**: PHP 8.3
- **Database**: MySQL 8.0
- **Cache**: Redis 7
- **Queue**: Laravel Horizon
- **AI**: Ollama (Local LLM) - phi3:latest
- **Auth**: Laravel Sanctum

### Mobile App
- **Framework**: Android Studio
- **Language**: Kotlin
- **Architecture**: MVVM + Repository Pattern
- **UI**: Material Design 3, Jetpack Compose
- **Local Storage**: Room Database, SharedPreferences
- **Networking**: Retrofit, OkHttp

### DevOps
- **Containerization**: Docker, Docker Compose
- **Web Server**: Nginx
- **Process Management**: Supervisor

## 📋 Prerequisites

- Docker & Docker Compose
- PHP 8.3+
- Composer
- Android Studio
- Kotlin
- Node.js 18+

## 🚀 Quick Start

### 1. Clone the repository
```bash
git clone https://github.com/tringuyenminh209/ToDoApp.git
cd ToDoApp
```

### 2. Setup environment
```bash
# Copy environment files
cp env.example .env
cp backend/env.example backend/.env

# Update .env files with your configuration
```

### 3. Start with Docker
```bash
# Start all services
./docker-start.sh

# Or manually
docker-compose up -d
```

### 3.1. Setup Ollama Model

**推奨モデル（速度・メモリ重視）:**
- `gemma2:2b` - 最も軽量で高速（推奨・デフォルト、約1.5GBメモリ）
- `qwen2.5:1.5b` - 軽量で日本語対応良好（約3GBメモリ）
- `phi3:mini` - 軽量（約2GBメモリ、ただし`phi3:latest`と同じファイルを参照する可能性あり）
- `phi3:latest` - バランス型（約8GBメモリ必要、メモリが十分な場合のみ）

```bash
# Ollamaコンテナに入る
docker-compose exec ollama bash

# 軽量モデルをダウンロード（速度向上・メモリ節約のため推奨・デフォルト）
ollama pull gemma2:2b

# 注意: phi3:latestは約8GBのメモリが必要です。メモリが不足する場合はgemma2:2bを使用してください
# ollama pull phi3:latest

# モデルが正しくダウンロードされたか確認
ollama list
```

**Ollama応答速度の最適化:**

1. **軽量モデルの使用**: `gemma2:2b`は`phi3:latest`より約3-4倍高速で、メモリ使用量も約1/5
2. **Keep-alive設定**: すべてのAPIリクエストに`keep_alive=30m`を追加（モデルをメモリに30分保持）
3. **コンテキストサイズ**: 512に削減済み（速度向上、load_duration削減）
4. **ストリーミング**: 既に実装済み（リアルタイム表示で体感速度向上）
5. **モデルプリロード**: コンテナ起動時にモデルをメモリに読み込み（初回リクエストのload_duration削減）

**応答速度改善のポイント:**
- `load_duration`（モデル読み込み時間）: `keep_alive`パラメータにより60秒→0秒に削減
- `prompt_eval_duration`（プロンプト評価時間）: コンテキストサイズ削減により短縮
- `eval_duration`（生成時間）: 軽量モデル使用により短縮

**モデル切り替え方法:**
```bash
# backend/.env を編集
OPENAI_MODEL=gemma2:2b  # または qwen2.5:1.5b, phi3:mini

# コンテナを再起動
docker-compose restart app
```

### 4. Setup Laravel Backend
```bash
# Enter the backend container
docker-compose exec app bash

# Install dependencies
composer install

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed
```

### 5. Setup Android Mobile App
```bash
cd mobile-android

# Open in Android Studio
# Install dependencies via Gradle
# Run the app on emulator or device
```

## 📱 API Documentation

### Authentication
- `POST /api/v1/auth/register` - User registration
- `POST /api/v1/auth/login` - User login
- `POST /api/v1/auth/logout` - User logout

### Tasks
- `GET /api/v1/tasks` - Get user tasks
- `POST /api/v1/tasks` - Create new task
- `GET /api/v1/tasks/{id}` - Get specific task
- `PUT /api/v1/tasks/{id}` - Update task
- `DELETE /api/v1/tasks/{id}` - Delete task
- `POST /api/v1/tasks/{id}/breakdown` - AI task breakdown

### Projects
- `GET /api/v1/projects` - Get user projects
- `POST /api/v1/projects` - Create new project
- `GET /api/v1/projects/{id}` - Get specific project
- `PUT /api/v1/projects/{id}` - Update project
- `DELETE /api/v1/projects/{id}` - Delete project

### AI Features
- `POST /api/v1/ai/plan-today` - Get daily AI plan
- `POST /api/v1/ai/nudge` - Get AI nudge
- `POST /api/v1/ai/review` - Generate daily review

## 🧪 Testing

### Backend Tests
```bash
cd backend
php artisan test
```

### Android Tests
```bash
cd mobile-android
./gradlew test
./gradlew connectedAndroidTest
```

## 📊 Database Schema

### Core Tables
- `users` - User accounts
- `projects` - Project management
- `tasks` - Task management
- `subtasks` - Task breakdown
- `sessions` - Focus mode sessions
- `ai_summaries` - AI-generated insights
- `push_tokens` - Push notification tokens

## 🔧 Configuration

### Environment Variables

#### Backend (.env)
```env
APP_NAME="To-Do AI App"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=todo_app
DB_USERNAME=todo_user
DB_PASSWORD=123qwecc

REDIS_HOST=redis
REDIS_PORT=6379

# Ollama Local AI Configuration
OPENAI_API_KEY=sk-dummy-key
OPENAI_BASE_URL=http://ollama:11434/v1
OPENAI_MODEL=phi3:latest
OPENAI_TIMEOUT=120
FCM_SERVER_KEY=your-fcm-server-key
```

## 🚀 Deployment

### Production Setup
1. Update environment variables
2. Build Docker images
3. Deploy to your preferred platform
4. Configure SSL certificates
5. Setup monitoring and logging

## 📈 Performance

- API response time: < 300ms (p95)
- Database queries optimized with proper indexing
- Redis caching for frequently accessed data
- Background job processing with Laravel Horizon

## 🔒 Security

- API authentication with Laravel Sanctum
- Input validation and sanitization
- Rate limiting
- HTTPS enforcement
- Data encryption at rest

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 📞 Support

For support, email support@todoapp.com or create an issue in this repository.

## 🎯 Roadmap

- [ ] iOS version (Swift)
- [ ] Team collaboration features
- [ ] Advanced AI coaching
- [ ] Integration with calendar apps
- [ ] Voice commands
- [ ] Smart notifications

---

Made with ❤️ by [Trinh Nguyen Minh](https://github.com/tringuyenminh209)
