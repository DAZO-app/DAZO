#!/bin/bash
# DAZO Update and Deployment Script
set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

BACKUP_DIR="./backups"
mkdir -p $BACKUP_DIR
TIMESTAMP=$(date +%Y%m%d-%H%M%S)

echo -e "${YELLOW}🔄 Starting DAZO Update Process...${NC}"

# Load environment variables from .env
if [ -f .env ]; then
    export $(grep -v '^#' .env | xargs)
fi

DB_USER=${DB_USERNAME:-dazo_user}
DB_NAME=${DB_DATABASE:-dazo}

# Step 1: Database Backup
echo -e "${YELLOW}💾 Backing up database...${NC}"
docker compose exec pgsql pg_dump -U $DB_USER $DB_NAME > "$BACKUP_DIR/db_backup_$TIMESTAMP.sql"
echo -e "${GREEN}✅ Backup saved to $BACKUP_DIR/db_backup_$TIMESTAMP.sql${NC}"

# Step 2: Git Pull
echo -e "${YELLOW}📥 Pulling latest code from GitHub...${NC}"
git fetch origin
git log HEAD..origin/main --oneline
git pull origin main
echo -e "${GREEN}✅ Code updated${NC}"

# Step 3: Composer Install
echo -e "${YELLOW}🐘 Updating PHP dependencies...${NC}"
docker compose exec app composer install --no-dev --optimize-autoloader
echo -e "${GREEN}✅ PHP dependencies updated${NC}"

# Step 4: NPM Install & Build
echo -e "${YELLOW}📦 Building assets...${NC}"
docker compose exec app npm install
docker compose exec app npm run build
echo -e "${GREEN}✅ Frontend built${NC}"

# Step 5: Database Migrations
echo -e "${YELLOW}🗄️  Running database migrations...${NC}"
docker compose exec app php artisan migrate --force
echo -e "${GREEN}✅ Database migrated${NC}"

# Step 6: Permissions
echo -e "${YELLOW}🔐 Setting storage permissions...${NC}"
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
echo -e "${GREEN}✅ Permissions set${NC}"

# Step 7: Finalize
echo -e "${YELLOW}🧹 Clearing and warming up cache...${NC}"
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
echo -e "${GREEN}✅ Cache warmed up${NC}"

# Step 7: Restart relevant services
echo -e "${YELLOW}♻️  Restarting background workers...${NC}"
docker compose restart queue scheduler reverb
echo -e "${GREEN}✅ Services restarted${NC}"

echo -e "${GREEN}=====================================${NC}"
echo -e "${GREEN}✅ DAZO UPDATED SUCCESSFULLY! ✨${NC}"
echo -e "${GREEN}=====================================${NC}"
docker compose ps
