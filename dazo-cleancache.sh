#!/bin/bash
# DAZO Cache Cleaning Script
set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}🧹 Cleaning all DAZO caches...${NC}"

docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear
docker compose exec app php artisan event:clear
docker compose exec app php artisan optimize:clear

echo -e "${GREEN}✅ All caches cleared${NC}"

echo -e "${YELLOW}🔥 Warming up production caches...${NC}"
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

echo -e "${GREEN}✅ Cache warmed up for production${NC}"

echo -e "\n${YELLOW}Appuyez sur Entrée pour revenir au menu...${NC}"
read
exec ./dazo-tool.sh
