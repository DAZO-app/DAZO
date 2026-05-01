#!/bin/bash
# DAZO Installation Script
set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${YELLOW}🚀 Starting DAZO Installation...${NC}"

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
echo -e "${YELLOW}🗄️  Running database migrations...${NC}"
docker compose exec app php artisan migrate --force
echo -e "${GREEN}✅ Database migrated${NC}"

# Step 6: Finalize
echo -e "${YELLOW}🧹 Clearing and warming up cache...${NC}"
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
echo -e "${GREEN}✅ Cache warmed up${NC}"

echo -e "${GREEN}=====================================${NC}"
echo -e "${GREEN}✨ DAZO INSTALLATION COMPLETE! ✨${NC}"
echo -e "${GREEN}=====================================${NC}"
docker compose ps
