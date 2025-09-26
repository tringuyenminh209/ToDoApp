# Thiết lập Docker cho To-Do AI App

Hướng dẫn này sẽ giúp bạn thiết lập môi trường Docker cho To-Do AI App với backend Laravel 12.

## Yêu cầu hệ thống

- Docker Desktop đã cài đặt và đang chạy
- Docker Compose v2.0+
- Git

## Bắt đầu nhanh

### 1. Clone và Thiết lập

```bash
# Clone repository
git clone <repository-url>
cd TodoApp

# Sao chép file environment
cp env.example .env

# Chỉnh sửa file .env với cấu hình của bạn
# Quan trọng: Cập nhật APP_KEY, OPENAI_API_KEY và các API keys khác
```

### 2. Khởi động Docker Environment

```bash
# Cấp quyền thực thi cho scripts (Linux/Mac)
chmod +x docker-*.sh

# Khởi động ứng dụng
./docker-start.sh
```

### 3. Truy cập Ứng dụng

- **Laravel App**: http://localhost:8000
- **PHPMyAdmin**: http://localhost:8080
- **Redis Commander**: http://localhost:8081

## Scripts có sẵn

### Môi trường Production

```bash
# Khởi động môi trường production
./docker-start.sh

# Dừng và dọn dẹp
./docker-stop.sh

# Xem logs
./docker-logs.sh
```

### Môi trường Development

```bash
# Khởi động với công cụ development (PHPMyAdmin, Redis Commander)
./docker-dev.sh

# Xem logs cho service cụ thể
./docker-logs.sh app
./docker-logs.sh mysql
./docker-logs.sh redis
```

## Lệnh Docker thủ công

### Khởi động Containers

```bash
# Build và khởi động tất cả containers
docker-compose up -d

# Khởi động với công cụ development
docker-compose --profile dev up -d

# Build không sử dụng cache
docker-compose build --no-cache
```

### Dừng Containers

```bash
# Dừng containers
docker-compose down

# Dừng và xóa volumes (reset database)
docker-compose down -v

# Dừng và xóa images
docker-compose down --rmi all
```

### Truy cập Containers

```bash
# Truy cập Laravel app container
docker-compose exec app bash

# Truy cập MySQL
docker-compose exec mysql mysql -u todo_user -p todo_app

# Truy cập Redis
docker-compose exec redis redis-cli
```

### Lệnh Laravel

```bash
# Chạy migrations
docker-compose exec app php artisan migrate

# Seed database
docker-compose exec app php artisan db:seed

# Xóa caches
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan cache:clear

# Tạo application key
docker-compose exec app php artisan key:generate

# Chạy tests
docker-compose exec app php artisan test

# Chạy PHPStan
docker-compose exec app ./vendor/bin/phpstan analyse

# Chạy Pint (code style)
docker-compose exec app ./vendor/bin/pint
```

## Cấu hình Environment

### Biến môi trường bắt buộc

```bash
# Application
APP_NAME="To-Do AI App"
APP_ENV=local
APP_KEY=base64:your-app-key-here
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=todo_app
DB_USERNAME=todo_user
DB_PASSWORD=todo_password

# Redis
REDIS_HOST=redis
REDIS_PORT=6379

# OpenAI (Bắt buộc cho tính năng AI)
OPENAI_API_KEY=your-openai-api-key
OPENAI_MODEL=gpt-4
```

### Biến môi trường tùy chọn

```bash
# OAuth Providers
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret

APPLE_CLIENT_ID=your-apple-client-id
APPLE_CLIENT_SECRET=your-apple-client-secret

# Firebase Cloud Messaging
FCM_SERVER_KEY=your-fcm-server-key

# Cấu hình Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
```

## Services

### Core Services

- **app**: Laravel 12 backend với PHP 8.3, Nginx, PHP-FPM
- **mysql**: MySQL 8.0 database
- **redis**: Redis 7 cho caching và queues

### Công cụ Development (Profile: dev)

- **phpmyadmin**: Giao diện web cho MySQL
- **redis-commander**: Giao diện web cho Redis

## Khắc phục sự cố

### Các vấn đề thường gặp

1. **Xung đột cổng**: Đảm bảo các cổng 8000, 3306, 6379, 8080, 8081 đang trống
2. **Vấn đề quyền**: Chạy `chmod +x docker-*.sh` để cấp quyền thực thi cho scripts
3. **Docker không chạy**: Khởi động Docker Desktop trước khi chạy scripts
4. **Kết nối database**: Đợi MySQL sẵn sàng trước khi chạy migrations

### Reset Environment

```bash
# Dừng containers và xóa volumes
docker-compose down -v

# Xóa images
docker-compose down --rmi all

# Dọn dẹp Docker system
docker system prune -f

# Khởi động lại từ đầu
./docker-start.sh
```

### Xem Logs

```bash
# Tất cả services
docker-compose logs -f

# Service cụ thể
docker-compose logs -f app
docker-compose logs -f mysql
docker-compose logs -f redis
```

### Vấn đề Database

```bash
# Reset database
docker-compose exec mysql mysql -u root -p -e "DROP DATABASE IF EXISTS todo_app; CREATE DATABASE todo_app;"

# Chạy migrations
docker-compose exec app php artisan migrate --force

# Seed database
docker-compose exec app php artisan db:seed --force
```

## Tối ưu hiệu suất

### Cài đặt Production

```bash
# Tối ưu Laravel
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Bật OPcache
# Đã được cấu hình trong docker/php.ini
```

### Giám sát

```bash
# Kiểm tra trạng thái containers
docker-compose ps

# Kiểm tra sử dụng tài nguyên
docker stats

# Kiểm tra logs để tìm lỗi
docker-compose logs --tail=100 app
```

## Lưu ý bảo mật

- Thay đổi mật khẩu mặc định trong production
- Sử dụng biến môi trường cho dữ liệu nhạy cảm
- Bật HTTPS trong production
- Cập nhật Docker images thường xuyên
- Sử dụng quản lý secrets cho API keys

## Hỗ trợ

Nếu bạn gặp vấn đề:

1. Kiểm tra logs: `./docker-logs.sh`
2. Xác minh cấu hình environment
3. Đảm bảo tất cả yêu cầu hệ thống được đáp ứng
4. Thử reset environment
5. Kiểm tra Docker Desktop đang chạy

Để được hỗ trợ thêm, tham khảo tài liệu dự án chính.
