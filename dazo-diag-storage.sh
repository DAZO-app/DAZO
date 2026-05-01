#!/bin/bash
# DAZO Storage & Branding Diagnostic Tool
set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${YELLOW}🔍 Running DAZO Storage Diagnostic...${NC}"
echo "--------------------------------------"

# 1. Check if containers are running
if ! docker compose ps | grep -q "Up"; then
    echo -e "${RED}❌ Docker containers are not running!${NC}"
    exit 1
fi

# 2. Check Storage Permissions
echo -e "${YELLOW}📁 Checking internal storage permissions...${NC}"
docker compose exec app ls -ld storage storage/app/private storage/app/public bootstrap/cache
echo -e "${GREEN}✅ Base directories exist.${NC}"

# 3. Check for the public/storage symbolic link
echo -e "${YELLOW}🔗 Checking public/storage symbolic link...${NC}"
if docker compose exec app ls -lad public/storage | grep -q "\->"; then
    echo -e "${GREEN}✅ public/storage is a valid symbolic link.${NC}"
    docker compose exec app ls -lad public/storage
else
    echo -e "${RED}❌ public/storage is MISSING or is NOT a symbolic link!${NC}"
    echo -e "${YELLOW}💡 Action: Run ./dazo-refresh.sh to recreate it.${NC}"
fi

# 4. Check for attachments directory
echo -e "${YELLOW}📎 Checking attachments directory...${NC}"
if docker compose exec app ls -d storage/app/private/attachments > /dev/null 2>&1; then
    COUNT=$(docker compose exec app ls storage/app/private/attachments | wc -l)
    echo -e "${GREEN}✅ Attachments directory found ($COUNT files).${NC}"
else
    echo -e "${RED}❌ Attachments directory is missing!${NC}"
fi

# 5. Check Branding Logo
echo -e "${YELLOW}🖼️  Checking Branding Logo...${NC}"
LOGO_PATH=$(docker compose exec app php artisan tinker --execute="echo App\Services\ConfigService::class ? app(App\Services\ConfigService::class)->get('app_logo') : ''" | tr -d '\r\n')
if [ -n "$LOGO_PATH" ] && [ "$LOGO_PATH" != "null" ]; then
    echo -e "Configured logo path: $LOGO_PATH"
    if docker compose exec app ls "storage/app/public/$LOGO_PATH" > /dev/null 2>&1; then
        echo -e "${GREEN}✅ Logo file exists physically in public storage.${NC}"
        echo -e "${GREEN}✅ Logo should be visible at: /storage/$LOGO_PATH${NC}"
    else
        echo -e "${RED}❌ Logo file NOT FOUND in storage/app/public/$LOGO_PATH!${NC}"
    fi
else
    echo -e "${YELLOW}ℹ️  No custom logo configured.${NC}"
fi

echo "--------------------------------------"
echo -e "${GREEN}Diagnostic complete.${NC}"
