#!/bin/bash
# DAZO Installation Script
set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${YELLOW}🚀 Starting DAZO Installation...${NC}"

# Check System Dependencies
echo -e "${YELLOW}🔍 Checking system dependencies...${NC}"
for cmd in docker zip python3; do
    if ! command -v $cmd &> /dev/null; then
        echo -e "${RED}❌ Error: $cmd is not installed. Please install it before proceeding.${NC}"
        if [ "$cmd" == "zip" ]; then
            echo -e "${YELLOW}💡 Tip: You can install zip using: sudo apt install zip${NC}"
        fi
        if [ "$cmd" == "python3" ]; then
            echo -e "${YELLOW}💡 Tip: You can install python3 using: sudo apt install python3${NC}"
        fi
        exit 1
    fi
done
echo -e "${GREEN}✅ System dependencies ok${NC}"

# Check .env
if [ ! -f .env ]; then
    echo -e "${YELLOW}📝 .env not found, creating from .env.example...${NC}"
    cp .env.example .env
    echo -e "${RED}⚠️  Action Required: Please edit your .env file with production credentials and then run dazo-install.sh again.${NC}"
    exit 1
fi

# Step 1: Start Docker Containers
echo -e "${YELLOW}🐳 Building and starting Docker containers...${NC}"
docker compose up -d --build
echo -e "${GREEN}✅ Containers started${NC}"

# Step 2: Composer Install
echo -e "${YELLOW}🐘 Installing PHP dependencies...${NC}"
docker compose exec app composer install --no-dev --optimize-autoloader
echo -e "${GREEN}✅ PHP dependencies installed${NC}"

# Step 3: NPM Build
echo -e "${YELLOW}📦 Installing NPM dependencies and building assets...${NC}"
docker compose exec app npm install
docker compose exec app npm run build
echo -e "${GREEN}✅ Frontend built${NC}"

# Step 4: App Key
echo -e "${YELLOW}🔑 Checking APP_KEY...${NC}"
if grep -q "APP_KEY=$" .env || grep -q "APP_KEY= " .env; then
    docker compose exec app php artisan key:generate
    echo -e "${GREEN}✅ APP_KEY generated${NC}"
else
    echo -e "${GREEN}✅ APP_KEY already exists${NC}"
fi

# Step 5: Database Migrations
echo -e "${YELLOW}🗄️  Running database migrations and seeding...${NC}"
docker compose exec app php artisan migrate --seed --force
echo -e "${GREEN}✅ Database migrated and seeded${NC}"

# Step 6: Storage Link
echo -e "${YELLOW}🔗 Creating storage symbolic link...${NC}"
docker compose exec -T app rm -rf public/storage
docker compose exec -T app php artisan storage:link --force
# Initialize atomic storage (A/B slots)
docker compose exec -T app sh /var/www/html/bash/DAZO-app-storage.sh init
echo -e "${GREEN}✅ Storage link and atomic slots created${NC}"

# Step 7: Permissions
echo -e "${YELLOW}🔐 Setting storage permissions...${NC}"
docker compose exec app mkdir -p storage/app/private/attachments/seed-pool
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
echo -e "${GREEN}✅ Permissions set${NC}"

# Step 8: Finalize
echo -e "${YELLOW}🧹 Clearing and warming up cache...${NC}"
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
echo -e "${GREEN}✅ Cache warmed up${NC}"

# Step 9: Background Scheduler
echo -e "${YELLOW}⏰ Configuring Background Scheduler...${NC}"
if crontab -l 2>/dev/null | grep -q "php artisan schedule:run"; then
    echo -e "${GREEN}✅ Scheduler already configured in crontab${NC}"
else
    PROJECT_PATH=$(pwd)
    CRON_JOB="* * * * * cd $PROJECT_PATH && docker compose exec -T app php artisan schedule:run >> /dev/null 2>&1"
    (crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -
    echo -e "${GREEN}✅ Scheduler added to crontab${NC}"
fi

echo -e "${GREEN}=====================================${NC}"
echo -e "${GREEN}✨ DAZO INSTALLATION COMPLETE! ✨${NC}"
echo -e "${GREEN}=====================================${NC}"
docker compose ps

echo -e "\n${YELLOW}Appuyez sur Entrée pour revenir au menu...${NC}"
read
exec ./dazo-tool.sh
