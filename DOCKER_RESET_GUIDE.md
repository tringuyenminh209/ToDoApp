# Docker Reset Guide - ToDoApp

Hướng dẫn reset code và services bằng Docker cho ToDoApp backend.

---

## 🔄 CÁC CẤP ĐỘ RESET

### **Level 1: Soft Reset** (Áp dụng code mới, giữ database)
✅ Dùng khi: Code backend thay đổi (như fix timetable intent)
✅ Database: Giữ nguyên
✅ Cache: Clear
✅ Thời gian: ~30 giây

```bash
# 1. Pull code mới từ git (nếu cần)
git pull origin claude/review-timeba-backend-01SWJQCs1fxCHpgxgm2PuSEM

# 2. Restart backend container
docker-compose restart app

# 3. Clear Laravel cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# 4. Optimize
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
```

---

### **Level 2: Medium Reset** (Rebuild container, giữ database)
✅ Dùng khi: Thay đổi Dockerfile, composer dependencies
✅ Database: Giữ nguyên
✅ Cache: Clear
✅ Thời gian: ~2-3 phút

```bash
# 1. Pull code mới
git pull origin claude/review-timeba-backend-01SWJQCs1fxCHpgxgm2PuSEM

# 2. Stop và rebuild container
docker-compose down
docker-compose build --no-cache app
docker-compose up -d

# 3. Clear cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache

# 4. Kiểm tra logs
docker-compose logs -f app
```

---

### **Level 3: Hard Reset** (Reset toàn bộ, bao gồm database)
⚠️ Dùng khi: Muốn bắt đầu từ đầu
❌ Database: **XÓA TẤT CẢ** (mất data)
✅ Thời gian: ~3-5 phút

```bash
# 1. Stop và xóa containers + volumes
docker-compose down -v

# 2. Xóa Docker images cũ (optional)
docker-compose build --no-cache

# 3. Start lại từ đầu
docker-compose up -d

# 4. Chờ MySQL khởi động (15-30s)
sleep 30

# 5. Migrate database
docker-compose exec app php artisan migrate:fresh --seed

# 6. Cache
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache

# 7. Kiểm tra
docker-compose ps
docker-compose logs -f app
```

---

### **Level 4: Nuclear Reset** (Xóa mọi thứ, bao gồm volumes)
🚨 Dùng khi: Docker bị lỗi hoàn toàn
❌ **MẤT TẤT CẢ DATA**
✅ Thời gian: ~5-10 phút

```bash
# 1. Stop và xóa mọi thứ
docker-compose down -v --remove-orphans

# 2. Xóa volumes thủ công
docker volume rm todoapp_mysql_data todoapp_redis_data 2>/dev/null || true

# 3. Xóa images
docker-compose down --rmi all

# 4. Rebuild từ đầu
docker-compose build --no-cache

# 5. Start
docker-compose up -d

# 6. Chờ services khởi động
sleep 30

# 7. Setup database
docker-compose exec app php artisan migrate:fresh --seed

# 8. Cache
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
```

---

## 🎯 TRƯỜNG HỢP CỤ THỂ

### **Trường hợp 1: Vừa fix code timetable intent**
→ Dùng **Level 1: Soft Reset**

```bash
# Quick reset cho code mới
docker-compose restart app
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:cache
```

---

### **Trường hợp 2: Cài thêm package mới (composer)**
→ Dùng **Level 2: Medium Reset**

```bash
# Rebuild để install dependencies mới
docker-compose down
docker-compose build --no-cache app
docker-compose up -d
```

---

### **Trường hợp 3: Migration thay đổi cấu trúc database**
→ Dùng **Level 3: Hard Reset**

```bash
# Reset database
docker-compose down -v
docker-compose up -d
sleep 30
docker-compose exec app php artisan migrate:fresh --seed
```

---

### **Trường hợp 4: Docker bị lỗi, container không start**
→ Dùng **Level 4: Nuclear Reset**

```bash
# Reset hoàn toàn
docker-compose down -v --remove-orphans
docker volume prune -f
docker-compose build --no-cache
docker-compose up -d
```

---

## 📋 COMMANDS REFERENCE

### Kiểm tra trạng thái
```bash
# Xem containers đang chạy
docker-compose ps

# Xem logs
docker-compose logs app
docker-compose logs mysql
docker-compose logs redis

# Follow logs (real-time)
docker-compose logs -f app

# Xem resource usage
docker stats
```

### Laravel Commands trong Docker
```bash
# Artisan commands
docker-compose exec app php artisan list
docker-compose exec app php artisan migrate
docker-compose exec app php artisan migrate:fresh --seed
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan queue:work

# Composer
docker-compose exec app composer install
docker-compose exec app composer update

# Bash shell vào container
docker-compose exec app bash
```

### Database Commands
```bash
# MySQL shell
docker-compose exec mysql mysql -u todo_user -p123qwecc todo_app

# Backup database
docker-compose exec mysql mysqldump -u todo_user -p123qwecc todo_app > backup.sql

# Restore database
docker-compose exec -T mysql mysql -u todo_user -p123qwecc todo_app < backup.sql

# PHPMyAdmin (dev mode)
docker-compose --profile dev up -d phpmyadmin
# Access: http://localhost:8082
```

### Redis Commands
```bash
# Redis CLI
docker-compose exec redis redis-cli

# Clear Redis cache
docker-compose exec redis redis-cli FLUSHALL

# Redis Commander (dev mode)
docker-compose --profile dev up -d redis-commander
# Access: http://localhost:8081
```

---

## 🚀 QUICK START (Sau khi pull code mới)

### Nếu đang chạy Docker:
```bash
# Option A: Soft reset (nhanh nhất)
docker-compose restart app && \
docker-compose exec app php artisan cache:clear && \
docker-compose exec app php artisan config:cache

# Option B: Rebuild (chắc chắn hơn)
docker-compose down && \
docker-compose build --no-cache app && \
docker-compose up -d
```

### Nếu chưa chạy Docker:
```bash
# Start từ đầu
docker-compose up -d

# Chờ MySQL khởi động
sleep 30

# Migrate database
docker-compose exec app php artisan migrate:fresh --seed

# Cache
docker-compose exec app php artisan config:cache

# Kiểm tra
docker-compose ps
curl http://localhost:8080/api/health
```

---

## ⚠️ LƯU Ý

### Trước khi reset:
- ✅ **Backup database** nếu có data quan trọng
- ✅ **Commit code** local changes
- ✅ **Pull code mới** từ git
- ✅ **Check .env file** có đúng config không

### Sau khi reset:
- ✅ Kiểm tra containers: `docker-compose ps` (tất cả phải "Up")
- ✅ Kiểm tra logs: `docker-compose logs app` (không có error)
- ✅ Test API: `curl http://localhost:8080/api/health`
- ✅ Test timetable creation với chatbot

### Nếu gặp lỗi:
```bash
# Xem logs chi tiết
docker-compose logs -f app

# Vào container để debug
docker-compose exec app bash
php artisan tinker

# Check MySQL connection
docker-compose exec app php artisan migrate:status

# Check Redis connection
docker-compose exec redis redis-cli ping
```

---

## 📊 SERVICES & PORTS

| Service | Container Name | Port | Access |
|---------|----------------|------|--------|
| Laravel Backend | todo-app-backend | 8080 | http://localhost:8080 |
| MySQL Database | todo-mysql | 3308 | localhost:3308 |
| Redis Cache | todo-redis | 6379 | localhost:6379 |
| PHPMyAdmin (dev) | todo-phpmyadmin | 8082 | http://localhost:8082 |
| Redis Commander (dev) | todo-redis-commander | 8081 | http://localhost:8081 |

---

## 🔍 TROUBLESHOOTING

### Container không start:
```bash
# Xem logs
docker-compose logs app

# Rebuild
docker-compose build --no-cache app
docker-compose up -d
```

### Database connection error:
```bash
# Check MySQL container
docker-compose ps mysql

# Wait for MySQL
sleep 30

# Test connection
docker-compose exec app php artisan migrate:status
```

### Cache issues:
```bash
# Clear all cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# Clear Redis
docker-compose exec redis redis-cli FLUSHALL
```

### Port already in use:
```bash
# Kill process using port 8080
sudo lsof -ti:8080 | xargs kill -9

# Or change port in docker-compose.yml
# ports:
#   - "8081:80"  # Change 8080 to 8081
```

---

## 🎯 RECOMMENDED WORKFLOW

### Development (Code changes thường xuyên):
```bash
# 1. Pull code
git pull

# 2. Soft reset
docker-compose restart app
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:cache

# 3. Test
curl http://localhost:8080/api/health
```

### After migrations:
```bash
# 1. Pull code
git pull

# 2. Run migrations
docker-compose exec app php artisan migrate

# Or fresh (reset data)
docker-compose exec app php artisan migrate:fresh --seed
```

### Production deployment:
```bash
# 1. Backup database
docker-compose exec mysql mysqldump -u todo_user -p123qwecc todo_app > backup_$(date +%Y%m%d).sql

# 2. Pull code
git pull

# 3. Rebuild
docker-compose down
docker-compose build --no-cache
docker-compose up -d

# 4. Migrate
docker-compose exec app php artisan migrate --force

# 5. Cache
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache

# 6. Verify
docker-compose ps
docker-compose logs -f app
```

---

**Created**: 2025-11-17
**Last Updated**: 2025-11-17
**Version**: 1.0
