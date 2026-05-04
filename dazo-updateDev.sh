#!/bin/bash
# DAZO BOURRIN Update Script
set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}🔥 Starting DAZO BOURRIN Update...${NC}"

# 1. Mise à jour automatique de la date dans le fichier source (pour forcer le build)
TIMESTAMP=$(date +'%d/%m/%Y %H:%M:%S')
echo -e "${YELLOW}🏷️  Updating timestamp to $TIMESTAMP...${NC}"
# On utilise | comme séparateur pour sed au lieu de / pour éviter les conflits avec les dates
sed -i "s|BETA \[.*\]|BETA [$TIMESTAMP]|g" resources/js/layouts/AppLayout.vue

# 2. Nettoyage radical
echo -e "${YELLOW}🧹 Purging public/build...${NC}"
docker compose exec app rm -rf public/build

# 3. Build Docker
echo -e "${YELLOW}📦 Building assets via Docker...${NC}"
docker compose exec app npm run build

# 4. Permissions
echo -e "${YELLOW}🔐 Setting permissions...${NC}"
sudo chown -R $USER:$USER public/build

# 5. Laravel optimize
echo -e "${YELLOW}🧹 Clearing Laravel caches...${NC}"
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan cache:clear

echo -e "${GREEN}=====================================${NC}"
echo -e "${GREEN}✅ BOURRIN UPDATE FINISHED! ✨${NC}"
echo -e "${GREEN}=====================================${NC}"
echo -e "${YELLOW}TIMESTAMP: $TIMESTAMP${NC}"

echo -e "\n${YELLOW}Appuyez sur Entrée pour revenir au menu...${NC}"
read
exec ./dazo-tool.sh
