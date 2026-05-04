#!/bin/bash
# DAZO Quick Development Update Script
# Builds assets and clears caches without doing a Git Pull
set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}🔄 Starting DAZO Dev Update (Local Build Only)...${NC}"

# 1. NPM Build via Docker
echo -e "${YELLOW}📦 Building frontend assets...${NC}"
docker compose exec app npm run build

# 2. Reset Permissions (fixes EACCES for next builds)
echo -e "${YELLOW}🔐 Fixing build directory permissions...${NC}"
sudo chown -R $USER:$USER public/build

# 3. Clear Laravel caches
echo -e "${YELLOW}🧹 Clearing Laravel caches...${NC}"
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan view:clear

echo -e "${GREEN}=====================================${NC}"
echo -e "${GREEN}✅ DEV UPDATE COMPLETED! ✨${NC}"
echo -e "${GREEN}=====================================${NC}"
echo -e "${YELLOW}💡 N'oubliez pas de faire Ctrl+F5 dans votre navigateur.${NC}"

echo -e "\n${YELLOW}Appuyez sur Entrée pour revenir au menu...${NC}"
read
exec ./dazo-tool.sh
