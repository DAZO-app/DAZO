#!/bin/bash
# DAZO Quick Refresh Script (Local Dev)
set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}🔄 Refreshing DAZO Local Environment...${NC}"

# 0. Optional Restart
if [[ "$1" == "--restart" ]]; then
    echo -e "${YELLOW}🐳 Restarting containers...${NC}"
    docker compose restart app web
fi

# 1. Clear caches
echo -e "${YELLOW}🧹 Clearing Laravel caches...${NC}"
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan view:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan event:clear

# 2. Reset permissions
echo -e "${YELLOW}🔐 Resetting permissions...${NC}"
docker compose exec app mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views storage/app/public/branding bootstrap/cache
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
docker compose exec app chmod -R 775 storage bootstrap/cache

# 3. Storage link
echo -e "${YELLOW}🔗 Ensuring storage link...${NC}"
docker compose exec app rm -rf public/storage
docker compose exec app php artisan storage:link --force

# 4. Vite Build (Optional if HMR fails)
if [[ "$1" == "--build" ]]; then
    echo -e "${YELLOW}📦 Running production build...${NC}"
    docker compose exec app npm run build
fi

echo -e "${GREEN}✅ Local environment refreshed!${NC}"
echo -e "${YELLOW}💡 Tip: If you still don't see changes, try:${NC}"
echo -e "   ./dazo-refresh.sh --restart"
echo -e "   ./dazo-refresh.sh --build"
echo -e "   Puis faites un Ctrl+F5 dans votre navigateur."

echo -e "\n${YELLOW}Appuyez sur Entrée pour revenir au menu...${NC}"
read
exec ./dazo-tool.sh
