#!/bin/bash
# DAZO Quick Refresh Script (Local Dev)
set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}🔄 Refreshing DAZO Local Environment...${NC}"

# 1. Clear caches
echo -e "${YELLOW}🧹 Clearing Laravel caches...${NC}"
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan view:clear
docker compose exec app php artisan route:clear

# 2. Reset permissions
echo -e "${YELLOW}🔐 Resetting permissions...${NC}"
docker compose exec app chown -R www-data:www-data storage bootstrap/cache

# 3. Storage link
echo -e "${YELLOW}🔗 Ensuring storage link...${NC}"
docker compose exec app php artisan storage:link --force

echo -e "${GREEN}✅ Local environment refreshed!${NC}"
echo -e "${YELLOW}💡 Tip: If you changed JS/Vue files, make sure dazo-dev.sh is running.${NC}"
