#!/bin/bash
# DAZO Rollback Script
set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${YELLOW}⏪ Starting DAZO Rollback...${NC}"

# Step 1: Revert Git
echo -e "${YELLOW}📥 Reverting code to previous state (HEAD@{1})...${NC}"
git reset --hard HEAD@{1}
echo -e "${GREEN}✅ Code reverted${NC}"

# Step 2: Re-install Dependencies
echo -e "${YELLOW}🐘 Restoring dependencies...${NC}"
docker compose exec app composer install --no-dev --optimize-autoloader
docker compose exec app npm install
docker compose exec app npm run build
echo -e "${GREEN}✅ Dependencies and assets restored${NC}"

# Step 3: Clear Cache
echo -e "${YELLOW}🧹 Clearing cache...${NC}"
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
echo -e "${GREEN}✅ Cache cleared${NC}"

echo -e "${GREEN}=====================================${NC}"
echo -e "${GREEN}✅ ROLLBACK COMPLETE!${NC}"
echo -e "${YELLOW}Note: If database migrations were destructive, you may need to restore a DB backup manually.${NC}"
echo -e "${GREEN}=====================================${NC}"
docker compose ps

echo -e "\n${YELLOW}Appuyez sur Entrée pour revenir au menu...${NC}"
read
exec ./dazo-tool.sh
