#!/bin/bash

# ToDoApp Docker Reset Script
# Usage: ./reset.sh [level]
# Levels: soft, medium, hard, nuclear

set -e

RESET_LEVEL="${1:-soft}"

echo "🔄 ToDoApp Docker Reset Script"
echo "================================"
echo ""

case $RESET_LEVEL in
  soft)
    echo "📦 Level 1: Soft Reset (Code changes only)"
    echo "- Restart containers"
    echo "- Clear Laravel cache"
    echo "- Database: KEEP"
    echo ""
    read -p "Continue? (y/n) " -n 1 -r
    echo ""
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
      echo "❌ Cancelled"
      exit 1
    fi

    echo "🔄 Restarting app container..."
    docker-compose restart app

    echo "🧹 Clearing cache..."
    docker-compose exec app php artisan cache:clear
    docker-compose exec app php artisan config:clear
    docker-compose exec app php artisan route:clear
    docker-compose exec app php artisan view:clear

    echo "⚡ Optimizing..."
    docker-compose exec app php artisan config:cache
    docker-compose exec app php artisan route:cache

    echo "✅ Soft reset complete!"
    echo ""
    echo "📊 Container status:"
    docker-compose ps
    ;;

  medium)
    echo "🏗️  Level 2: Medium Reset (Rebuild containers)"
    echo "- Rebuild app container"
    echo "- Install dependencies"
    echo "- Database: KEEP"
    echo ""
    read -p "Continue? (y/n) " -n 1 -r
    echo ""
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
      echo "❌ Cancelled"
      exit 1
    fi

    echo "🛑 Stopping containers..."
    docker-compose down

    echo "🏗️  Rebuilding app container..."
    docker-compose build --no-cache app

    echo "🚀 Starting containers..."
    docker-compose up -d

    echo "⏳ Waiting for services to start..."
    sleep 10

    echo "🧹 Clearing cache..."
    docker-compose exec app php artisan cache:clear
    docker-compose exec app php artisan config:cache
    docker-compose exec app php artisan route:cache

    echo "✅ Medium reset complete!"
    echo ""
    echo "📊 Container status:"
    docker-compose ps
    ;;

  hard)
    echo "💥 Level 3: Hard Reset (Reset database)"
    echo "- Rebuild all containers"
    echo "- Database: DELETE ALL"
    echo "- Fresh migrations + seed"
    echo ""
    echo "⚠️  WARNING: This will DELETE ALL DATABASE DATA!"
    echo ""
    read -p "Are you sure? (yes/no) " -r
    echo ""
    if [[ ! $REPLY =~ ^[Yy][Ee][Ss]$ ]]; then
      echo "❌ Cancelled"
      exit 1
    fi

    echo "💾 Backup database first? (recommended)"
    read -p "Backup? (y/n) " -n 1 -r
    echo ""
    if [[ $REPLY =~ ^[Yy]$ ]]; then
      BACKUP_FILE="backup_$(date +%Y%m%d_%H%M%S).sql"
      echo "💾 Creating backup: $BACKUP_FILE"
      docker-compose exec mysql mysqldump -u todo_user -p123qwecc todo_app > "$BACKUP_FILE" || echo "⚠️  Backup failed, continuing..."
    fi

    echo "🛑 Stopping containers and removing volumes..."
    docker-compose down -v

    echo "🏗️  Rebuilding containers..."
    docker-compose build --no-cache

    echo "🚀 Starting containers..."
    docker-compose up -d

    echo "⏳ Waiting for MySQL to initialize..."
    sleep 30

    echo "🗄️  Running migrations and seeders..."
    docker-compose exec app php artisan migrate:fresh --seed

    echo "🧹 Caching configuration..."
    docker-compose exec app php artisan config:cache
    docker-compose exec app php artisan route:cache

    echo "✅ Hard reset complete!"
    echo ""
    echo "📊 Container status:"
    docker-compose ps
    ;;

  nuclear)
    echo "☢️  Level 4: Nuclear Reset (Complete wipe)"
    echo "- Remove all containers, volumes, images"
    echo "- Complete fresh start"
    echo "- Database: DELETE ALL"
    echo ""
    echo "🚨 WARNING: This will DELETE EVERYTHING!"
    echo ""
    read -p "Type 'DELETE EVERYTHING' to confirm: " -r
    echo ""
    if [[ ! $REPLY == "DELETE EVERYTHING" ]]; then
      echo "❌ Cancelled"
      exit 1
    fi

    echo "💾 Backup database first? (recommended)"
    read -p "Backup? (y/n) " -n 1 -r
    echo ""
    if [[ $REPLY =~ ^[Yy]$ ]]; then
      BACKUP_FILE="backup_$(date +%Y%m%d_%H%M%S).sql"
      echo "💾 Creating backup: $BACKUP_FILE"
      docker-compose exec mysql mysqldump -u todo_user -p123qwecc todo_app > "$BACKUP_FILE" || echo "⚠️  Backup failed, continuing..."
    fi

    echo "☢️  Nuking everything..."
    docker-compose down -v --remove-orphans --rmi all

    echo "🧹 Removing volumes manually..."
    docker volume rm todoapp_mysql_data todoapp_redis_data 2>/dev/null || true

    echo "🧹 Pruning Docker system..."
    docker system prune -f

    echo "🏗️  Rebuilding from scratch..."
    docker-compose build --no-cache

    echo "🚀 Starting containers..."
    docker-compose up -d

    echo "⏳ Waiting for services..."
    sleep 30

    echo "🗄️  Setting up database..."
    docker-compose exec app php artisan migrate:fresh --seed

    echo "🧹 Caching configuration..."
    docker-compose exec app php artisan config:cache
    docker-compose exec app php artisan route:cache

    echo "✅ Nuclear reset complete!"
    echo ""
    echo "📊 Container status:"
    docker-compose ps
    ;;

  *)
    echo "❌ Invalid reset level: $RESET_LEVEL"
    echo ""
    echo "Usage: ./reset.sh [level]"
    echo ""
    echo "Levels:"
    echo "  soft    - Restart + clear cache (default)"
    echo "  medium  - Rebuild containers"
    echo "  hard    - Reset database"
    echo "  nuclear - Complete wipe"
    echo ""
    echo "Examples:"
    echo "  ./reset.sh soft    # Quick reset for code changes"
    echo "  ./reset.sh medium  # Rebuild after dependency changes"
    echo "  ./reset.sh hard    # Reset database"
    echo "  ./reset.sh nuclear # Start from scratch"
    exit 1
    ;;
esac

echo ""
echo "🔍 Checking services..."
echo ""

# Check app health
echo "📡 Testing API..."
sleep 5
curl -s http://localhost:8080/api/health > /dev/null && echo "✅ API is responding" || echo "❌ API is not responding"

echo ""
echo "🎉 Reset complete!"
echo ""
echo "📚 Next steps:"
echo "  - Check logs: docker-compose logs -f app"
echo "  - Access API: http://localhost:8080"
echo "  - PHPMyAdmin: docker-compose --profile dev up -d phpmyadmin"
echo "               http://localhost:8082"
