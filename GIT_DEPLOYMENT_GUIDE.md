# 🚀 DEPLOYMENT GUIDE — Mettre à jour depuis Git

**Infrastructure:** Docker, PostgreSQL 15, Laravel, Vue.js  
**Repo:** https://github.com/DAZO-app/DAZO.git  
**Server:** vps-dc05dcc3 (51.83.162.146)  

---

## ✅ PRÉ-REQUIS

Avant tout déploiement:

```bash
# 1. SSH access
ssh root@51.83.162.146

# 2. Vérifier que DAZO est en place
docker compose ps | grep dazo

# 3. Avoir un backup récent
docker compose exec -T pgsql pg_dump -U dazo dazo > /tmp/backup-pre-deploy-$(date +%Y%m%d-%H%M%S).sql
```

---

## 📍 LOCALISER LE PROJET

**Première étape: trouver où est le code**

```bash
# Find docker-compose.yml location
docker ps | grep dazo-app
docker inspect dazo-app | grep -i "source\|volume\|workdir"

# Or search for it
find / -name "docker-compose.yml" -path "*dazo*" 2>/dev/null

# Common locations:
# - /root/dazo
# - /home/user/dazo
# - /opt/dazo
# - /var/www/dazo
```

**Une fois trouvé, on appelle ce dossier `$PROJECT_DIR`**

```bash
# Example:
cd /root/dazo
# or
cd /home/user/dazo
```

---

## 🔄 SCÉNARIO 1: PREMIER DÉPLOIEMENT (Clone)

**Si le code n'existe pas encore:**

```bash
# 1. Go to parent directory
cd /root
# or cd /home/user
# or cd /opt

# 2. Clone the repository
git clone https://github.com/DAZO-app/DAZO.git dazo
cd dazo

# 3. Setup environment
cp .env.example .env.docker
# Edit .env.docker with your production values:
nano .env.docker
# Set:
#   APP_ENV=production
#   APP_DEBUG=false
#   DB_PASSWORD=<strong-password>
#   APP_KEY=<generate-with-artisan>
#   MAIL_*=<your-smtp>

# 4. Build and start containers
docker compose up -d --build

# 5. Run migrations
docker compose exec dazo-app php artisan migrate --force

# 6. Seed database (optional)
docker compose exec dazo-app php artisan db:seed

# 7. Clear cache
docker compose exec dazo-app php artisan cache:clear
docker compose exec dazo-app php artisan config:cache

# 8. Verify
docker compose ps
docker compose logs dazo-app | tail -20
```

---

## 🔄 SCÉNARIO 2: MISE À JOUR (Git Pull)

**Si le code existe déjà et tu veux mettre à jour:**

```bash
cd /root/dazo  # Your project directory

# 1. BACKUP FIRST (CRITICAL!)
docker compose exec dazo-db pg_dump -U postgres dazo > backup-$(date +%Y%m%d-%H%M%S).sql
echo "✅ Backup saved"

# 2. Stash local changes (if any)
git stash

# 3. Fetch latest from repository
git fetch origin

# 4. Check what changed
git log HEAD..origin/main --oneline  # Show what's new

# 5. Pull latest code
git pull origin main
echo "✅ Code updated"

# 6. Install/update PHP dependencies
docker compose exec app composer install --no-dev --optimize-autoloader
echo "✅ Composer dependencies updated"

# 7. Build frontend
docker compose exec app npm ci --production
docker compose exec app npm run build
echo "✅ Frontend built"

# 8. Run migrations
docker compose exec app php artisan migrate --force
echo "✅ Database migrated"

# 9. Clear cache
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
echo "✅ Cache cleared"

# 10. Restart services
docker compose restart app queue scheduler reverb
echo "✅ Services restarted"

# 11. Verify
docker compose ps
docker compose logs app | tail -20
curl https://beta.dazo.fr/api/v1/init  # Test API
echo "✅ Deployment complete!"
```

---

## 🔧 DETAILED DEPLOYMENT SCRIPT

**Crée un fichier `deploy.sh`:**

```bash
#!/bin/bash

set -e  # Exit on error

PROJECT_DIR="/root/dazo"  # Change if needed
BACKUP_DIR="/tmp/backups"
LOG_FILE="$BACKUP_DIR/deploy-$(date +%Y%m%d-%H%M%S).log"

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}=== DAZO Deployment Started ===${NC}"
echo "Project: $PROJECT_DIR"
echo "Log: $LOG_FILE"

# Create backup directory
mkdir -p $BACKUP_DIR

cd $PROJECT_DIR

# Step 1: Backup database
echo -e "${YELLOW}[1/11] Backing up database...${NC}"
docker compose exec dazo-db pg_dump -U postgres dazo > "$BACKUP_DIR/db-$(date +%Y%m%d-%H%M%S).sql" 2>&1 | tee -a $LOG_FILE
echo -e "${GREEN}✅ Database backed up${NC}"

# Step 2: Stash changes
echo -e "${YELLOW}[2/11] Stashing local changes...${NC}"
git stash 2>&1 | tee -a $LOG_FILE
echo -e "${GREEN}✅ Local changes stashed${NC}"

# Step 3: Fetch
echo -e "${YELLOW}[3/11] Fetching from origin...${NC}"
git fetch origin 2>&1 | tee -a $LOG_FILE
echo -e "${GREEN}✅ Fetched${NC}"

# Step 4: Show changes
echo -e "${YELLOW}[4/11] Changes to deploy:${NC}"
git log HEAD..origin/main --oneline 2>&1 | tee -a $LOG_FILE

# Step 5: Pull
echo -e "${YELLOW}[5/11] Pulling code...${NC}"
git pull origin main 2>&1 | tee -a $LOG_FILE
echo -e "${GREEN}✅ Code updated${NC}"

# Step 6: Composer
echo -e "${YELLOW}[6/11] Installing PHP dependencies...${NC}"
docker compose exec dazo-app composer install --no-dev --optimize-autoloader 2>&1 | tee -a $LOG_FILE
echo -e "${GREEN}✅ Dependencies installed${NC}"

# Step 7: NPM
echo -e "${YELLOW}[7/11] Building frontend...${NC}"
docker compose exec dazo-app npm ci --production 2>&1 | tee -a $LOG_FILE
docker compose exec dazo-app npm run build 2>&1 | tee -a $LOG_FILE
echo -e "${GREEN}✅ Frontend built${NC}"

# Step 8: Migrations
echo -e "${YELLOW}[8/11] Running migrations...${NC}"
docker compose exec dazo-app php artisan migrate --force 2>&1 | tee -a $LOG_FILE
echo -e "${GREEN}✅ Migrations complete${NC}"

# Step 9: Cache
echo -e "${YELLOW}[9/11] Clearing cache...${NC}"
docker compose exec dazo-app php artisan cache:clear 2>&1 | tee -a $LOG_FILE
docker compose exec dazo-app php artisan config:cache 2>&1 | tee -a $LOG_FILE
docker compose exec dazo-app php artisan route:cache 2>&1 | tee -a $LOG_FILE
echo -e "${GREEN}✅ Cache cleared${NC}"

# Step 10: Restart
echo -e "${YELLOW}[10/11] Restarting services...${NC}"
docker compose restart dazo-app dazo-queue dazo-scheduler dazo-reverb 2>&1 | tee -a $LOG_FILE
echo -e "${GREEN}✅ Services restarted${NC}"

# Step 11: Verify
echo -e "${YELLOW}[11/11] Verifying deployment...${NC}"
echo "Container status:"
docker compose ps 2>&1 | tee -a $LOG_FILE

echo "App logs (last 5 lines):"
docker compose logs dazo-app | tail -5 2>&1 | tee -a $LOG_FILE

echo "Testing API..."
curl -s https://beta.dazo.fr/api/v1/init | head -c 100 2>&1 | tee -a $LOG_FILE
echo ""
echo -e "${GREEN}✅ Deployment verified${NC}"

echo -e "${GREEN}=== ✅ DEPLOYMENT COMPLETE ===${NC}"
echo "Backup saved: $BACKUP_DIR"
echo "Log saved: $LOG_FILE"
```

**Utilisation:**

```bash
chmod +x deploy.sh
./deploy.sh
```

---

## 🔍 VÉRIFIER LE DÉPLOIEMENT

**Après la mise à jour:**

```bash
# 1. Check container status
docker compose ps
# All should be "Up"

# 2. Check app logs
docker compose logs dazo-app | tail -20
# No errors? ✅

# 3. Test API
curl https://beta.dazo.fr/api/v1/init
# Should return JSON with app config ✅

# 4. Test database
docker compose exec dazo-app php artisan tinker
# > DB::connection()->getPdo()->query('SELECT 1')
# > exit

# 5. Check queue
docker compose exec dazo-app php artisan queue:failed
# Should be empty or show previous failures only

# 6. Full health check
docker compose exec dazo-app php artisan health:check
```

---

## 🔄 ROLLBACK (En cas de problème)

**Si le déploiement échoue:**

```bash
cd /root/dazo

# 1. Stop services
docker compose down

# 2. Restore database from backup
docker compose exec dazo-db psql -U postgres -d dazo < /tmp/backups/db-YYYYMMDD-HHMMSS.sql

# 3. Revert code to previous commit
git reset --hard HEAD~1

# 4. Start services
docker compose up -d

# 5. Verify
docker compose ps
docker compose logs dazo-app
```

---

## 🚨 TROUBLESHOOTING

### Migration Fails
```bash
# See what went wrong
docker compose logs dazo-app | grep -i "migration\|error"

# Rollback migration
docker compose exec dazo-app php artisan migrate:rollback

# Fix code, then retry
git pull origin main
docker compose exec dazo-app php artisan migrate --force
```

### App won't start
```bash
# Check logs
docker compose logs dazo-app

# Rebuild container
docker compose build --no-cache dazo-app
docker compose up -d

# Check again
docker compose logs dazo-app
```

### Database connection error
```bash
# Verify database is running
docker compose ps dazo-db

# Check credentials in .env
grep DB_ /root/dazo/.env.docker

# Test connection manually
docker compose exec dazo-db psql -U postgres -c "SELECT 1"
```

### Out of disk space during build
```bash
# Stop deployment
docker compose down

# Clean up
docker system prune -a --volumes

# Try again
docker compose up -d --build
```

---

## 📋 GIT WORKFLOW

### Voir les branches disponibles
```bash
git branch -a
git branch -r
```

### Déployer une branche spécifique
```bash
git checkout main
git pull origin main
# vs
git checkout develop
git pull origin develop
```

### Voir l'historique
```bash
git log --oneline -10
git log --oneline main..origin/main  # What's new
```

### Tags (releases)
```bash
git tag -l                           # List tags
git checkout tags/v1.0.0             # Deploy specific version
git pull origin tag v1.0.0
```

---

## ✅ DEPLOYMENT CHECKLIST

Avant de déployer:
- [ ] Backup database pris
- [ ] Logs consultés pour erreurs existantes
- [ ] Code vérifié sur GitHub (regarder les changements)
- [ ] Fenêtre de maintenance planifiée (si needed)
- [ ] Team notifiée
- [ ] Quelqu'un disponible pour monitoring

Après déploiement:
- [ ] Tous les containers UP
- [ ] Logs sans erreurs
- [ ] API accessible
- [ ] Database accessible
- [ ] Queue processing
- [ ] Email sending works
- [ ] Users can login
- [ ] Basic functionality tested

---

## 🔐 GIT CREDENTIALS

### Si tu as des erreurs "Permission denied"

```bash
# Method 1: SSH Key (Recommended)
git remote set-url origin git@github.com:DAZO-app/DAZO.git
# Requires SSH key setup

# Method 2: GitHub Token
git remote set-url origin https://TOKEN@github.com/DAZO-app/DAZO.git
# Replace TOKEN with your personal access token

# Method 3: Store credentials
git config credential.helper store
git pull  # Will ask for password once, then save it
```

---

## 📊 DEPLOYMENT SCHEDULE

**Recommended:**
- Deploy updates during low-traffic hours
- Avoid Friday evenings
- Schedule maintenance windows
- Notify users in advance

**Monitoring:**
- Watch logs for 30min after deployment
- Monitor error rate
- Monitor response time
- Monitor database load

---

## 🎯 COMPLETE WORKFLOW EXAMPLE

**Déployer une mise à jour complète:**

```bash
# 1. SSH to server
ssh root@51.83.162.146

# 2. Navigate to project
cd /root/dazo

# 3. Backup database
docker compose exec dazo-db pg_dump -U postgres dazo > backup-$(date +%Y%m%d).sql

# 4. Get latest code
git fetch origin
git log HEAD..origin/main --oneline  # Check what's new
git pull origin main

# 5. Update dependencies
docker compose exec dazo-app composer install --no-dev --optimize-autoloader
docker compose exec dazo-app npm ci --production
docker compose exec dazo-app npm run build

# 6. Run migrations
docker compose exec dazo-app php artisan migrate --force

# 7. Clear cache and restart
docker compose exec dazo-app php artisan cache:clear
docker compose restart dazo-app dazo-queue dazo-scheduler

# 8. Verify
docker compose ps
docker compose logs dazo-app | tail -10
curl https://beta.dazo.fr/api/v1/init

# 9. Monitor for 30min
watch -n 10 'docker compose logs dazo-app | tail -5'
```

---

**Questions?**
- Check `git log` to see deployment history
- Check `docker compose logs` for runtime errors
- Keep backups (they're in `/tmp/backups/`)
- Test in staging first if possible

Happy deploying! 🚀
