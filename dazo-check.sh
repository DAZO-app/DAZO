#!/bin/bash
# DAZO System Check Script

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${YELLOW}🔍 Checking DAZO System Health...${NC}"
echo "-------------------------------------"

# 1. Docker Containers
echo -e "${YELLOW}🐳 Container Status:${NC}"
docker compose ps
echo ""

# 2. Database Connection
echo -e "${YELLOW}🗄️  Database Connection:${NC}"
# Load DB_USERNAME from .env
DB_USER=$(grep DB_USERNAME .env | cut -d '=' -f2)
DB_NAME=$(grep DB_DATABASE .env | cut -d '=' -f2)

if docker compose exec pgsql pg_isready -U ${DB_USER:-dazo_user} -d ${DB_NAME:-dazo} > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Database connection successful${NC}"
else
    echo -e "${RED}❌ Database connection failed${NC}"
fi
echo ""

# 3. Redis Connection
echo -e "${YELLOW}⚡ Redis Status:${NC}"
if docker compose exec redis redis-cli ping | grep -q "PONG"; then
    echo -e "${GREEN}✅ Redis is up${NC}"
else
    echo -e "${RED}❌ Redis is down${NC}"
fi
echo ""

# 4. Storage Permissions
echo -e "${YELLOW}📁 Storage Permissions:${NC}"
if docker compose exec app ls -ld storage | grep -q "www-data"; then
    echo -e "${GREEN}✅ Storage owned by www-data${NC}"
else
    echo -e "${YELLOW}⚠️  Check storage permissions (should be www-data)${NC}"
fi
echo ""

# 5. Logs
echo -e "${YELLOW}📝 Last 5 logs:${NC}"
docker compose exec app tail -n 5 storage/logs/laravel.log
echo ""

# 6. Disk Usage
echo -e "${YELLOW}💾 Disk Usage:${NC}"
df -h | grep "/dev/" | head -n 5
echo ""

echo "-------------------------------------"
echo -e "${GREEN}✅ Check complete${NC}"
