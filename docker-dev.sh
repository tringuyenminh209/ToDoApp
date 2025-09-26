#!/bin/bash

# Docker Development Script for To-Do AI App
# This script starts the development environment with dev tools

set -e

echo "🛠️  Starting To-Do AI App Development Environment..."

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker first."
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

# Start containers with dev profiles
echo "🚀 Starting containers with development tools..."
docker-compose --profile dev up -d

# Wait for services to be ready
echo "⏳ Waiting for services to be ready..."
sleep 10

# Install Laravel dependencies
echo "📦 Installing Laravel dependencies..."
docker-compose exec app composer install

# Generate application key
echo "🔑 Generating application key..."
docker-compose exec app php artisan key:generate

# Run database migrations
echo "🗄️  Running database migrations..."
docker-compose exec app php artisan migrate --force

# Seed database with sample data
echo "🌱 Seeding database with sample data..."
docker-compose exec app php artisan db:seed --force

# Clear caches for development
echo "⚡ Clearing caches for development..."
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan cache:clear

# Set permissions
echo "🔐 Setting permissions..."
docker-compose exec app chown -R www-data:www-data /var/www/storage
docker-compose exec app chown -R www-data:www-data /var/www/bootstrap/cache
docker-compose exec app chmod -R 755 /var/www/storage
docker-compose exec app chmod -R 755 /var/www/bootstrap/cache

echo "✅ Development environment is ready!"
echo ""
echo "🌐 Application URLs:"
echo "   - Laravel App: http://localhost:8000"
echo "   - PHPMyAdmin: http://localhost:8080"
echo "   - Redis Commander: http://localhost:8081"
echo ""
echo "📊 Container Status:"
docker-compose ps
echo ""
echo "🛠️  Development Commands:"
echo "   - View logs: ./docker-logs.sh"
echo "   - Access app container: docker-compose exec app bash"
echo "   - Run tests: docker-compose exec app php artisan test"
echo "   - Run PHPStan: docker-compose exec app ./vendor/bin/phpstan analyse"
echo "   - Run Pint: docker-compose exec app ./vendor/bin/pint"
echo "   - Generate API docs: docker-compose exec app php artisan l5-swagger:generate"
echo ""
echo "🎉 Happy developing!"
