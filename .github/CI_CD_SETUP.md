# CI/CD Pipeline Setup Guide

This document explains the GitHub Actions CI/CD pipeline for the ToDoApp project.

## 📋 Table of Contents

- [Overview](#overview)
- [Workflows](#workflows)
- [How to Use](#how-to-use)
- [Build Artifacts](#build-artifacts)
- [Troubleshooting](#troubleshooting)

## 🎯 Overview

The project uses GitHub Actions for automated:
- **Android builds** (Debug & Release APK)
- **Code quality checks** (Lint, Unit Tests)
- **Backend tests** (Laravel PHPUnit, Code Style)

## 🔄 Workflows

### 1. Android CI/CD (`android-build.yml`)

**Triggers:**
- Push to `main`, `master`, `develop`, or any `claude/**` branch
- Pull requests to `main`, `master`, `develop`

**Jobs:**

#### 📦 Build Job
- Builds Debug APK for all branches
- Builds Release APK only for `main`/`master`
- Uploads APK as downloadable artifacts

#### 🔍 Lint Job
- Runs Android Lint checks
- Uploads HTML lint report

#### ✅ Test Job
- Runs unit tests
- Uploads test reports
- Publishes test results summary

**Artifacts:**
- `app-debug.apk` (7 days retention)
- `app-release.apk` (30 days retention)
- Lint reports
- Test reports

### 2. Backend CI (`backend-ci.yml`)

**Triggers:**
- Push/PR when `backend/**` files change
- Runs on multiple PHP versions (8.1, 8.2)

**Jobs:**

#### 🧪 Laravel Tests
- Installs Composer dependencies
- Runs database migrations (SQLite)
- Executes PHPUnit tests
- Checks code style with Laravel Pint

#### 📊 Code Quality
- Syntax checking for all PHP files
- Static analysis with PHPStan (level 5)

## 🚀 How to Use

### Viewing Workflow Runs

1. Go to your repository on GitHub
2. Click **Actions** tab
3. See all workflow runs and their status

### Downloading Build Artifacts

1. Navigate to a completed workflow run
2. Scroll to **Artifacts** section at the bottom
3. Download:
   - `app-debug` - Debug APK for testing
   - `app-release` - Release APK for production
   - `lint-report` - Code quality report
   - `test-report` - Test results

### Adding Status Badges to README

Add these badges to your main `README.md`:

```markdown
![Android Build](https://github.com/YOUR_USERNAME/ToDoApp/actions/workflows/android-build.yml/badge.svg)
![Backend CI](https://github.com/YOUR_USERNAME/ToDoApp/actions/workflows/backend-ci.yml/badge.svg)
```

Replace `YOUR_USERNAME` with your GitHub username.

## 🔧 Configuration

### Android Build Configuration

The workflow uses:
- **JDK 17** (Temurin distribution)
- **Gradle caching** for faster builds
- **Ubuntu latest** runner

### Backend Test Configuration

The workflow uses:
- **PHP 8.1 & 8.2** (matrix strategy)
- **SQLite** for testing database
- **Composer caching**

## 📊 Build Time Estimates

| Workflow | Average Duration |
|----------|-----------------|
| Android Build | 5-8 minutes |
| Android Lint | 2-3 minutes |
| Android Tests | 1-2 minutes |
| Backend Tests | 2-4 minutes |

## 🐛 Troubleshooting

### Build Fails with "Permission Denied"

**Problem:** `./gradlew: Permission denied`

**Solution:** Already fixed - workflow includes:
```yaml
- name: Grant execute permission for gradlew
  run: chmod +x ./gradlew
```

### Build Fails with "OutOfMemory"

**Problem:** Gradle runs out of memory

**Solution:** Add to `gradle.properties`:
```properties
org.gradle.jvmargs=-Xmx2048m -XX:MaxPermSize=512m
```

### Backend Tests Fail

**Problem:** Database connection errors

**Solution:** Workflow uses SQLite by default. Check `.env.example` has correct defaults.

### Lint Warnings

**Problem:** Lint job passes but shows warnings

**Solution:** Download lint report artifact to see details:
1. Click workflow run
2. Download `lint-report` artifact
3. Open `lint-results-debug.html` in browser

## 🎯 Best Practices

### Branch Strategy

- **`main`/`master`**: Production - builds Release APK
- **`develop`**: Development - builds Debug APK
- **`claude/**`**: Feature branches - builds Debug APK

### Commit Messages

Use conventional commits for better changelog:
```
feat: add new study schedule feature
fix: resolve crash on roadmap import
chore: update dependencies
docs: update API documentation
```

### Testing Before Push

Always test locally before pushing:

**Android:**
```bash
./gradlew assembleDebug
./gradlew lint
./gradlew test
```

**Backend:**
```bash
cd backend
composer install
php artisan test
./vendor/bin/pint
```

## 📈 Monitoring

### Slack/Discord Notifications (Optional)

Add notification step to workflows:

```yaml
- name: Notify Slack
  if: always()
  uses: 8398a7/action-slack@v3
  with:
    status: ${{ job.status }}
    webhook_url: ${{ secrets.SLACK_WEBHOOK }}
```

### Email Notifications

GitHub automatically sends email notifications for:
- Failed workflow runs
- First successful run after failure

Configure in: **Settings → Notifications → Actions**

## 🔐 Secrets Management

For signing Android APKs, add secrets:

1. Go to **Settings → Secrets and variables → Actions**
2. Add these secrets:
   - `KEYSTORE_FILE` (Base64 encoded)
   - `KEYSTORE_PASSWORD`
   - `KEY_ALIAS`
   - `KEY_PASSWORD`

Then update workflow:
```yaml
- name: Sign APK
  run: |
    echo "${{ secrets.KEYSTORE_FILE }}" | base64 -d > keystore.jks
    ./gradlew assembleRelease \
      -Pandroid.injected.signing.store.file=keystore.jks \
      -Pandroid.injected.signing.store.password="${{ secrets.KEYSTORE_PASSWORD }}" \
      -Pandroid.injected.signing.key.alias="${{ secrets.KEY_ALIAS }}" \
      -Pandroid.injected.signing.key.password="${{ secrets.KEY_PASSWORD }}"
```

## 🎓 Learn More

- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [Android CI/CD Best Practices](https://developer.android.com/studio/build/building-cmdline)
- [Laravel Testing Guide](https://laravel.com/docs/testing)

## 📞 Support

If you encounter issues:
1. Check **Actions** tab for error logs
2. Review this documentation
3. Open an issue in the repository

---

**Last Updated:** 2025-11-13
**Version:** 1.0.0
