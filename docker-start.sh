#!/bin/bash

# Docker Start Script for To-Do AI App
# This script starts the Docker containers and sets up the Laravel application

set -e

echo "🚀 Starting To-Do AI App Docker Environment..."

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker first."
    exit 1
fi

# Check if docker-compose is available
if ! command -v docker-compose &> /dev/null; then
    echo "❌ docker-compose is not installed. Please install docker-compose first."
    exit 1
fi

# Create .env file if it doesn't exist
if [ ! -f .env ]; then
    echo "📝 Creating .env file from env.example..."
    cp env.example .env
    echo "⚠️  Please update .env file with your configuration before continuing."
    echo "   Especially: APP_KEY, OPENAI_API_KEY, and other API keys."
    read -p "Press Enter to continue after updating .env file..."
fi

# Build and start containers
echo "🔨 Building Docker containers..."
docker-compose build --no-cache

echo "🚀 Starting containers..."
docker-compose up -d

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
until docker-compose exec mysql mysqladmin ping -h localhost --silent; do
    echo "   Waiting for MySQL..."
    sleep 2
done

# Wait for Redis to be ready
echo "⏳ Waiting for Redis to be ready..."
until docker-compose exec redis redis-cli ping | grep -q PONG; do
    echo "   Waiting for Redis..."
    sleep 2
done

# Install Laravel dependencies
echo "📦 Installing Laravel dependencies..."
docker-compose exec app composer install --no-dev --optimize-autoloader

# Generate application key
echo "🔑 Generating application key..."
docker-compose exec app php artisan key:generate

# Run database migrations
echo "🗄️  Running database migrations..."
docker-compose exec app php artisan migrate --force

# Seed database with sample data
echo "🌱 Seeding database with sample data..."
docker-compose exec app php artisan db:seed --force

# Clear and cache configuration
echo "⚡ Optimizing Laravel..."
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Set permissions
echo "🔐 Setting permissions..."
docker-compose exec app chown -R www-data:www-data /var/www/storage
docker-compose exec app chown -R www-data:www-data /var/www/bootstrap/cache
docker-compose exec app chmod -R 755 /var/www/storage
docker-compose exec app chmod -R 755 /var/www/bootstrap/cache

echo "✅ Docker environment is ready!"
echo ""
echo "🌐 Application URLs:"
echo "   - Laravel App: http://localhost:8000"
echo "   - PHPMyAdmin: http://localhost:8080"
echo "   - Redis Commander: http://localhost:8081"
echo ""
echo "📊 Container Status:"
docker-compose ps
echo ""
echo "📝 Useful Commands:"
echo "   - View logs: docker-compose logs -f"
echo "   - Stop containers: docker-compose down"
echo "   - Restart containers: docker-compose restart"
echo "   - Access app container: docker-compose exec app bash"
echo "   - Access MySQL: docker-compose exec mysql mysql -u todo_user -p todo_app"
echo "   - Access Redis: docker-compose exec redis redis-cli"
echo ""
echo "🎉 Happy coding!"
