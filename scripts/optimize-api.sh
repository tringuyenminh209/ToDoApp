@env ('staging') 
    
@endenv#!/bin/bash

# API Performance Optimization Script
# This script rebuilds Docker containers with optimizations and clears caches

echo "🚀 Starting API Performance Optimization..."

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Step 1: Stop containers
echo -e "${YELLOW}Step 1: Stopping containers...${NC}"
docker-compose down

# Step 2: Rebuild containers
echo -e "${YELLOW}Step 2: Rebuilding containers with optimizations...${NC}"
docker-compose build --no-cache

# Step 3: Start containers
echo -e "${YELLOW}Step 3: Starting containers...${NC}"
docker-compose up -d

# Wait for containers to be ready
echo -e "${YELLOW}Waiting for containers to be ready...${NC}"
sleep 10

# Step 4: Clear Laravel caches
echo -e "${YELLOW}Step 4: Clearing Laravel caches...${NC}"
docker exec -it todo-app-backend php artisan config:clear
docker exec -it todo-app-backend php artisan cache:clear
docker exec -it todo-app-backend php artisan route:clear
docker exec -it todo-app-backend php artisan view:clear

# Step 5: Rebuild caches
echo -e "${YELLOW}Step 5: Rebuilding optimized caches...${NC}"
docker exec -it todo-app-backend php artisan config:cache
docker exec -it todo-app-backend php artisan route:cache
docker exec -it todo-app-backend php artisan view:cache

# Step 6: Optimize autoloader
echo -e "${YELLOW}Step 6: Optimizing Composer autoloader...${NC}"
docker exec -it todo-app-backend composer dump-autoload --optimize --classmap-authoritative

# Step 7: Verify Redis connection
echo -e "${YELLOW}Step 7: Verifying Redis connection...${NC}"
REDIS_CHECK=$(docker exec -it todo-redis redis-cli ping 2>/dev/null | tr -d '\r\n')
if [ "$REDIS_CHECK" = "PONG" ]; then
    echo -e "${GREEN}✅ Redis is connected${NC}"
else
    echo -e "${RED}❌ Redis connection failed${NC}"
fi

# Step 8: Verify MySQL connection
echo -e "${YELLOW}Step 8: Verifying MySQL connection...${NC}"
MYSQL_CHECK=$(docker exec -it todo-mysql mysql -u todo_user -p123qwecc -e "SELECT 1" 2>/dev/null)
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ MySQL is connected${NC}"
else
    echo -e "${RED}❌ MySQL connection failed${NC}"
fi

# Step 9: Check container status
echo -e "${YELLOW}Step 9: Checking container status...${NC}"
docker-compose ps

# Step 10: Display resource usage
echo -e "${YELLOW}Step 10: Current resource usage:${NC}"
docker stats --no-stream

echo -e "${GREEN}✅ Optimization complete!${NC}"
echo ""
echo "📊 Next steps:"
echo "1. Test API endpoints to verify performance improvements"
echo "2. Monitor resource usage: docker stats"
echo "3. Check logs if issues occur: docker logs todo-app-backend"
echo ""
echo "🔍 Performance monitoring:"
echo "- API Response Time: Monitor via logs"
echo "- Cache Hit Rate: docker exec -it todo-redis redis-cli INFO stats"
echo "- Database Performance: Check MySQL slow query log"
echo "- Memory Usage: docker stats"

