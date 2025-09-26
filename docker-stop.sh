#!/bin/bash

# Docker Stop Script for To-Do AI App
# This script stops the Docker containers and cleans up

set -e

echo "🛑 Stopping To-Do AI App Docker Environment..."

# Stop containers
echo "⏹️  Stopping containers..."
docker-compose down

# Remove volumes (optional - uncomment if you want to reset database)
# echo "🗑️  Removing volumes..."
# docker-compose down -v

# Remove images (optional - uncomment if you want to rebuild from scratch)
# echo "🗑️  Removing images..."
# docker-compose down --rmi all

# Clean up unused Docker resources
echo "🧹 Cleaning up unused Docker resources..."
docker system prune -f

echo "✅ Docker environment stopped and cleaned up!"
echo ""
echo "📝 To start again, run: ./docker-start.sh"
