# To-Do AI App

A modern To-Do application with AI integration built with Laravel 12 backend and Next.js frontend.

## 🚀 Features

- **AI-Powered Task Breakdown**: Automatically break down complex tasks into manageable subtasks
- **Focus Mode**: Pomodoro timer with AI-powered nudges and hints
- **Smart Planning**: AI suggests Top 3 tasks for the day based on energy level and schedule
- **Progress Tracking**: Visual progress tracking with streaks and analytics
- **Multi-language Support**: Vietnamese, Japanese, and English

## 🏗️ Architecture

```
┌───────────────────────────────────────┐   ┌───────────────────────────────────────┐
│           Next.js Frontend            │   │             Android App               │
│                                       │   │                                       │
│ [React] [TypeScript] [Tailwind CSS]   │   │     [Kotlin] [MVVM] [Retrofit]        │
│                                       │   │                                       │
└───────────────────┬───────────────────┘   └───────────────────┬───────────────────┘
                    │                                           │
                    │                                           │
                    └─────────────────────┬─────────────────────┘
                                          │ REST API
                                          ▼
┌─────────────────────────────────────────────────────────────┐
│                  Laravel 12 Backend                         │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐            │
│  │   API Layer │ │  Business   │ │   AI        │            │
│  │             │ │   Logic     │ │ Integration │            │
│  └─────────────┘ └─────────────┘ └─────────────┘            │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    Data Layer                               │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐            │
│  │   MySQL 8   │ │   Redis     │ │   Ollama    │            │
│  │  (Primary)  │ │ (Cache/Queue)│ │  (Local LLM)│           │
│  └─────────────┘ └─────────────┘ └─────────────┘            │
└─────────────────────────────────────────────────────────────┘
```

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 12
- **Language**: PHP 8.3
- **Database**: MySQL 8.0
- **Cache**: Redis 7
- **AI**: Ollama (Local LLM) - gemma2:2b

### Frontend
- **Framework**: Next.js 16
- **Language**: TypeScript
- **UI**: Tailwind CSS, Radix UI
- **State Management**: Zustand

### Mobile (Android)
- **Language**: Kotlin 2.0
- **Architecture**: MVVM (Model-View-ViewModel)
- **Network**: Retrofit + OkHttp
- **Local Database**: Room (Offline support)
- **Async**: Coroutines & Flow
- **UI**: XML / ViewBinding / Material Design
- **Notifications**: Firebase Cloud Messaging (FCM)


### DevOps
- **Containerization**: Docker, Docker Compose
- **Web Server**: Nginx

## 📋 Prerequisites

- Docker & Docker Compose
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
cp backend/env.example backend/.env

# Update backend/.env with your configuration
```

### 3. Start with Docker
```bash
docker-compose up -d
```

### 4. Setup Ollama Model
```bash
# Enter Ollama container
docker-compose exec ollama bash

# Download recommended model (default: gemma2:2b)
ollama pull gemma2:2b

# Verify model
ollama list
```

**Recommended Model:** `gemma2:2b` (Lightweight/Fast, ~1.5GB Memory)

### 5. Setup Laravel Backend
```bash
# Enter backend container
docker-compose exec app bash

# Install dependencies
composer install

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate
```

### 6. Access the application
- **Frontend**: http://localhost:8088
- **Backend API**: http://localhost:8080/api

## 📱 API Documentation

For detailed API documentation, please refer to `backend/routes/api.php`.

## 🔧 Configuration

For environment variable configuration, please refer to `backend/env.example`.


## 📄 License

This project is licensed under the MIT License.

---

Made with ❤️ by [NGUYEN MINH TRI](https://github.com/tringuyenminh209)
