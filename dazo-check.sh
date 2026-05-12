#!/bin/bash
# DAZO System Check Script

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${YELLOW}🔍 Checking DAZO System Health...${NC}"
echo "-------------------------------------"

# 0. System Dependencies
echo -e "${YELLOW}🔍 System Requirements:${NC}"
for cmd in docker zip; do
    if command -v $cmd &> /dev/null; then
        echo -e "${GREEN}✅ $cmd is installed${NC}"
    else
        echo -e "${RED}❌ $cmd is MISSING${NC}"
    fi
done
if [ ! -f .env ]; then
    echo -e "${RED}❌ .env file is MISSING${NC}"
fi
echo ""

# 1. Docker Containers
echo -e "${YELLOW}🐳 Container Status:${NC}"
if ! docker compose ps | grep -q "Up"; then
    echo -e "${RED}❌ No containers are running.${NC}"
else
    docker compose ps
fi
echo ""

# 2. Database Connection
echo -e "${YELLOW}🗄️  Database Connection:${NC}"
if [ -f .env ]; then
    DB_USER=$(grep DB_USERNAME .env | cut -d '=' -f2 | tr -d '\r')
    DB_NAME=$(grep DB_DATABASE .env | cut -d '=' -f2 | tr -d '\r')
    
    if docker compose exec pgsql pg_isready -U ${DB_USER:-dazo_user} -d ${DB_NAME:-dazo} > /dev/null 2>&1; then
        echo -e "${GREEN}✅ Database connection successful${NC}"
    else
        echo -e "${RED}❌ Database connection failed${NC}"
    fi
else
    echo -e "${RED}⚠️  Skipping DB check (no .env)${NC}"
fi
echo ""

# 3. Redis Connection
echo -e "${YELLOW}⚡ Redis Status:${NC}"
if docker compose exec redis redis-cli ping 2>/dev/null | grep -q "PONG"; then
    echo -e "${GREEN}✅ Redis is up${NC}"
else
    echo -e "${RED}❌ Redis is down${NC}"
fi
echo ""

# 4. Storage & Backups
echo -e "${YELLOW}📁 Storage & Permissions:${NC}"
# Root storage
if docker compose exec app ls -ld storage | grep -q "www-data"; then
    echo -e "${GREEN}✅ Root storage owned by www-data${NC}"
else
    echo -e "${RED}❌ Storage permissions mismatch (should be www-data)${NC}"
fi

# Backup folders
for dir in "backups/database" "backups/files" "public/attachments"; do
    if docker compose exec app [ -d "storage/app/$dir" ]; then
        if docker compose exec app [ -w "storage/app/$dir" ]; then
            echo -e "${GREEN}✅ storage/app/$dir is writable${NC}"
        else
            echo -e "${RED}❌ storage/app/$dir is NOT writable${NC}"
        fi
    else
        echo -e "${YELLOW}⚠️  storage/app/$dir is missing (will be auto-created)${NC}"
    fi
done
echo ""

# 5. Logs
echo -e "${YELLOW}📝 Last 5 logs:${NC}"
if docker compose exec app [ -f storage/logs/laravel.log ]; then
    docker compose exec app tail -n 5 storage/logs/laravel.log
else
    echo -e "${GREEN}✅ No logs yet (clean)${NC}"
fi
echo ""

# 6. Disk Usage
echo -e "${YELLOW}💾 Disk Usage:${NC}"
df -h | grep -E '^/dev/' | head -n 5
echo ""

echo "-------------------------------------"
echo -e "${GREEN}✅ Check complete${NC}"

echo -e "\n${YELLOW}Appuyez sur Entrée pour revenir au menu...${NC}"
read
exec ./dazo-tool.sh
