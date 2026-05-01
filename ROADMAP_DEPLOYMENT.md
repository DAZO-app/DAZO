# 🚀 ROADMAP DÉPLOIEMENT — DOCKER & ENVIRONNEMENT

**Objectif:** Simplifier et standardiser le déploiement sur serveur (Docker + config manageable)  
**Horizon:** 2-3 semaines  
**Contexte:** Actuellement compliqué, beaucoup d'ajustements manuels needed

---

## ANALYSE ÉTAT ACTUEL

**Problèmes identifiés:**
- ❌ Déploiement Docker complexe avec beaucoup d'ajustements manuels
- ❌ Configuration serveur non documentée
- ❌ Variables d'environnement dispersées ou hardcodées
- ❌ Processus de migration non automatisé
- ❌ Pas de clear deployment guide

**Objectif:** Créer un déploiement reproductible et simple (dev → staging → prod en même processus).

---

## PHASE 1 — AUDIT ENVIRONNEMENT (Jour 1)

> **Priorité:** 🔴 CRITIQUE  
> **Complexité:** Faible  
> **Dépendances:** Aucune  
> **Timeline:** 1-2 heures  

**Action:** Récupérer la config du serveur actuel.

### Commandes à exécuter sur le serveur:

```bash
# === INFOS SYSTÈME ===

# OS et kernel
uname -a
cat /etc/os-release

# CPU et RAM
nproc
free -h
lscpu | grep "CPU max"

# Disk usage
df -h
du -sh /var/www/dazo-project 2>/dev/null || echo "Project folder not found"

# === PHP ===

php --version
php -i | grep -E "PHP Version|Loaded Configuration File|memory_limit|upload_max_filesize|post_max_size|max_execution_time"

# Extensions PHP
php -m | sort

# === MYSQL/DATABASE ===

mysql --version
# Connect and run:
# mysql -u root -p
# SHOW VARIABLES LIKE 'version';
# SHOW VARIABLES LIKE 'max_connections';
# SHOW VARIABLES LIKE 'character_set%';
# SELECT schema_name FROM information_schema.schemata;
# EXIT;

# Or via command if password in .my.cnf:
mysql -e "SELECT @@version, @@max_connections, @@character_set_database;"
mysql -e "SHOW DATABASES;"

# === NGINX ===

nginx -v
nginx -t  # Test config

# === DOCKER (if running) ===

docker --version
docker-compose --version
docker ps
docker images

# === NODE.js (if frontend build) ===

node --version
npm --version

# === REDIS (if used) ===

redis-cli --version
redis-cli ping

# === FILE PERMISSIONS ===

ls -la /var/www/dazo-project/storage/ 2>/dev/null | head -5
ls -la /var/www/dazo-project/bootstrap/cache/ 2>/dev/null | head -5
ls -la /var/www/dazo-project/.env 2>/dev/null || echo ".env file check"

# === ENVIRONMENT VARIABLES (BE CAREFUL - contains secrets!) ===

# DON'T paste full .env, but check structure:
head -20 /var/www/dazo-project/.env || echo ".env not found"

# === CURRENT RUNNING SERVICES ===

systemctl status | grep running
ps aux | grep -E "php|nginx|mysql|redis" | head -10

# === LARAVEL APP ===

cd /var/www/dazo-project
php artisan --version
php artisan config:cache --show  # or just run it
php artisan migrate:status

# === COMPOSERDEPENDENCIES ===

composer --version
grep "php" composer.json | head -1

# === LOG FILES (check for errors) ===

tail -20 /var/www/dazo-project/storage/logs/laravel.log
tail -10 /var/log/nginx/error.log
tail -10 /var/log/nginx/access.log

# === Network ===

ip addr  # Get server IP
curl -I https://dazo-domain.com 2>/dev/null | head -5  # Check if HTTPS works
```

**Store output in:**
- Create file: `/home/daha/DEV/DAZO/PROJECT/SERVER_INFO.md`
- Paste results from above commands
- Keep locally (don't commit sensitive data!)

---

## PHASE 2 — DOCUMENT CURRENT STACK (Jour 1)

> **Priorité:** 🔴 CRITIQUE  
> **Complexité:** Faible  
> **Dépendances:** Phase 1 ✅  
> **Timeline:** 2-3 heures  

**Create file:** `DEPLOYMENT_STACK.md`

**Content structure:**

```markdown
# Deployment Stack — Current Environment

## Server Info
- **OS:** [from uname -a]
- **CPU:** [from nproc]
- **RAM:** [from free -h]
- **Disk:** [from df -h]

## PHP
- **Version:** [from php --version]
- **Memory Limit:** [from php -i]
- **Upload Max:** [from php -i]
- **Extensions:** [from php -m]

## Database
- **Type:** MySQL/MariaDB
- **Version:** [from mysql --version]
- **Character Set:** [from SHOW VARIABLES]
- **Max Connections:** [from SHOW VARIABLES]

## Webserver
- **Type:** Nginx
- **Version:** [from nginx -v]
- **Config Location:** [from nginx -t output]

## Cache/Session
- **Type:** Redis/File/Array
- **Version:** [from redis-cli --version]

## Current Issues
- [List issues found during Phase 1]
- [Docker complexity noted]
- [Manual configs needed]

## Dependencies
- Laravel version: [from composer.json]
- PHP requirement: [from composer.json]
- Node: [from node --version]
```

---

## PHASE 3 — CREATE DOCKER STANDARDIZATION (Jours 2-3)

> **Priorité:** 🟠 HAUTE  
> **Complexité:** Moyenne  
> **Dépendances:** Phase 1-2 ✅  
> **Timeline:** 2-3 jours  

**Create new Docker setup files:**

- [ ] **Dockerfile** — Base image standardized
  - File: `docker/Dockerfile`
  - Requirements:
    - PHP 8.2+ with all extensions (mysql, redis, gd, bcmath, etc.)
    - Composer pre-installed
    - Node.js pre-installed (for frontend build)
    - Non-root user (dazo)
    - Working directory: `/app`
  - Template provided below

- [ ] **docker-compose.yml** — Multi-container orchestration
  - File: `docker-compose.yml` (root)
  - Containers:
    - `app` (Laravel backend)
    - `web` (Nginx reverse proxy)
    - `db` (MySQL 8.0)
    - `cache` (Redis)
  - Network: internal `dazo-network`
  - Volumes: persist data across restarts
  - Environment: load from `.env.docker`

- [ ] **.env.docker** — Docker-specific config
  - File: `.env.docker` (don't commit, but template provided)
  - Template:
    ```env
    APP_NAME=DAZO
    APP_ENV=production
    APP_DEBUG=false
    APP_URL=https://dazo-domain.com
    
    DB_CONNECTION=mysql
    DB_HOST=db
    DB_PORT=3306
    DB_DATABASE=dazo_production
    DB_USERNAME=dazo_user
    DB_PASSWORD=[strong-password-here]
    
    REDIS_HOST=cache
    REDIS_PORT=6379
    
    CACHE_DRIVER=redis
    SESSION_DRIVER=cookie
    
    MAIL_MAILER=smtp
    MAIL_HOST=mail.smtp-provider.com
    MAIL_PORT=587
    MAIL_USERNAME=your-email@example.com
    MAIL_PASSWORD=[app-password-here]
    MAIL_ENCRYPTION=tls
    MAIL_FROM_ADDRESS=noreply@dazo.com
    MAIL_FROM_NAME="DAZO"
    ```

- [ ] **docker-entrypoint.sh** — Startup script
  - File: `docker/entrypoint.sh`
  - Actions:
    ```bash
    #!/bin/bash
    set -e
    
    # Wait for database to be ready
    until MYSQL_PWD=$DB_PASSWORD mysql -h $DB_HOST -u $DB_USERNAME -e "SELECT 1" > /dev/null 2>&1; do
      echo "Waiting for MySQL..."
      sleep 2
    done
    
    # Install dependencies
    composer install --no-dev --optimize-autoloader
    
    # Run migrations
    php artisan migrate --force
    
    # Cache config
    php artisan config:cache
    php artisan route:cache
    
    # Build frontend if needed
    npm ci --production
    npm run build
    
    # Start services
    php artisan queue:work &
    php artisan serve --host=0.0.0.0 --port=8000
    
    exec "$@"
    ```

- [ ] **Nginx config** — Web server
  - File: `docker/nginx.conf`
  - Proxy to Laravel backend
  - SSL termination (if external)
  - Gzip compression
  - Cache headers for static assets

- [ ] **Makefile** — Simple deployment commands
  - File: `Makefile` (root)
  - Targets:
    ```makefile
    .PHONY: up down logs build rebuild migrate fresh test

    up:
    	docker-compose up -d

    down:
    	docker-compose down

    build:
    	docker-compose build

    rebuild:
    	docker-compose build --no-cache

    logs:
    	docker-compose logs -f app

    shell:
    	docker-compose exec app bash

    migrate:
    	docker-compose exec app php artisan migrate

    fresh:
    	docker-compose exec app php artisan migrate:fresh --seed

    test:
    	docker-compose exec app php artisan test
    ```

**Success Criteria:**
- ✅ `docker-compose up` works in one command
- ✅ All services start automatically
- ✅ Database initialized on first run
- ✅ Migrations run automatically
- ✅ Frontend built automatically
- ✅ No manual config steps needed

---

## PHASE 4 — CI/CD AUTOMATION (Jours 3-4)

> **Priorité:** 🟡 MOYENNE  
> **Complexité:** Moyenne  
> **Dépendances:** Phase 3 ✅  
> **Timeline:** 2-3 jours  

**Create files:**

- [ ] **GitHub Actions Workflow** — Auto-deploy on push
  - File: `.github/workflows/deploy.yml`
  - Triggers: On push to `main` branch
  - Steps:
    1. Checkout code
    2. Build Docker image
    3. Push to registry (Docker Hub or private)
    4. Deploy to server (SSH + docker pull + docker-compose up)
  - Secrets needed:
    - `DOCKER_USERNAME`, `DOCKER_PASSWORD`
    - `SERVER_SSH_KEY`, `SERVER_HOST`, `SERVER_USER`
    - `.env` vars (APP_KEY, DB_PASSWORD, etc.)

- [ ] **Deploy script** — SSH to server and deploy
  - File: `scripts/deploy.sh`
  - Actions:
    ```bash
    #!/bin/bash
    # 1. SSH to server
    # 2. cd /var/www/dazo-production
    # 3. git pull origin main
    # 4. docker-compose pull
    # 5. docker-compose up -d
    # 6. docker-compose exec app php artisan migrate --force
    # 7. docker-compose exec app php artisan cache:clear
    # 8. Verify services running
    # 9. Health check curl
    ```

- [ ] **Staging Environment** — Separate from production
  - File: `docker-compose.staging.yml`
  - Same as prod but different DB/domain
  - Used to test before pushing to production

**Success Criteria:**
- ✅ Push to main → auto-deploys to staging
- ✅ Manual trigger → deploys to production
- ✅ Migrations run automatically
- ✅ Zero downtime deployments
- ✅ Rollback capability if needed

---

## PHASE 5 — DEPLOYMENT GUIDE (Jour 4)

> **Priorité:** 🟠 HAUTE  
> **Complexité:** Faible  
> **Dépendances:** Phase 3-4 ✅  
> **Timeline:** 1-2 heures  

**Create file:** `docs/DEPLOYMENT_GUIDE.md`

**Content:**

```markdown
# 🚀 DAZO Deployment Guide

## Prerequisites
- Docker + Docker Compose installed on server
- Git repository access
- Domain name configured (DNS pointing to server IP)
- SSL certificate (Let's Encrypt recommended)

## Local Development

### First time setup:
```bash
# 1. Clone repo
git clone https://github.com/DAZO-app/DAZO.git
cd DAZO

# 2. Setup environment
cp .env.example .env
cp .env.docker .env.local  # for local testing

# 3. Start containers
docker-compose up -d

# 4. Initialize database
docker-compose exec app php artisan migrate --seed

# 5. Build frontend
docker-compose exec app npm run build

# 6. Access app
# Frontend: http://localhost:3000
# Backend: http://localhost:8000
# Admin: http://localhost:3000/admin
```

### Daily workflow:
```bash
# Start
make up

# Stop
make down

# View logs
make logs

# Run migrations
make migrate

# Run tests
make test

# Access shell
make shell
```

## Staging Deployment

```bash
# 1. SSH to server
ssh user@staging-server.com

# 2. Clone/pull repo
cd /var/www/dazo-staging
git pull origin main

# 3. Build and deploy
docker-compose -f docker-compose.staging.yml up -d

# 4. Run migrations
docker-compose -f docker-compose.staging.yml exec app php artisan migrate

# 5. Test
curl https://staging.dazo.com/api/v1/init
```

## Production Deployment

```bash
# 1. Create production directory
ssh user@prod-server.com
mkdir -p /var/www/dazo-production
cd /var/www/dazo-production

# 2. Clone repo
git clone https://github.com/DAZO-app/DAZO.git .

# 3. Setup .env.docker with production values
cp .env.docker.example .env.docker
# Edit .env.docker with:
# - DB credentials
# - APP_KEY (generate with: php artisan key:generate)
# - Mail credentials
# - Domain names
# - SSL paths

# 4. Deploy
docker-compose up -d

# 5. Verify
docker-compose ps  # All services should be 'Up'
docker-compose logs -f app  # Check no errors

# 6. Health check
curl https://dazo.com/api/v1/init
```

## Troubleshooting

### Services won't start
```bash
# Check logs
docker-compose logs app
docker-compose logs db

# Rebuild
docker-compose build --no-cache
docker-compose up -d
```

### Database connection error
```bash
# Wait for MySQL to be ready
docker-compose exec db mysql -u root -p -e "SELECT 1"

# Check DB password matches
grep DB_PASSWORD .env.docker
```

### Frontend not loading
```bash
# Rebuild frontend
docker-compose exec app npm run build

# Clear cache
docker-compose exec app php artisan cache:clear
```

## Backup & Recovery

### Backup database:
```bash
docker-compose exec db mysqldump -u dazo_user -p$DB_PASSWORD dazo_production > backup-$(date +%Y%m%d).sql
```

### Restore database:
```bash
docker-compose exec db mysql -u dazo_user -p$DB_PASSWORD dazo_production < backup-20260501.sql
```
```

**Success Criteria:**
- ✅ Guide is clear and step-by-step
- ✅ All commands tested and work
- ✅ Troubleshooting section covers common issues
- ✅ New team member can deploy using this guide

---

## PHASE 6 — MONITORING & LOGGING (Ongoing)

> **Priorité:** 🟡 MOYENNE  
> **Complexité:** Média  
> **Dépendances:** Phase 3-5 ✅  
> **Timeline:** 1 semaine post-launch  

**Implement:**

- [ ] **Container logging** — All services log to stdout
  - Use `docker logs app` to see logs
  - Redirect to external logging (ELK, DataDog, etc.) if needed

- [ ] **Health checks** — Docker health status
  - Containers include `HEALTHCHECK` instruction
  - Automatic restart if unhealthy

- [ ] **Monitoring alerts** — CPU, memory, disk
  - Setup alerts if:
    - CPU > 80%
    - Memory > 90%
    - Disk > 85%
  - Use external tool (Uptime Robot, New Relic, Datadog)

- [ ] **Error tracking** — Sentry integration
  - Laravel Sentry SDK
  - Track all exceptions in production
  - Alert on new errors

**Success Criteria:**
- ✅ Can view logs with `docker logs`
- ✅ Containers auto-restart on crash
- ✅ Alerts configured for resource issues
- ✅ Exceptions tracked in Sentry

---

## TIMELINE

```
Day 1:   Phase 1 (Audit)              ████
Day 1:   Phase 2 (Document)           ████
Day 2-3: Phase 3 (Docker)             ████████
Day 3-4: Phase 4 (CI/CD)              ████████
Day 4:   Phase 5 (Guide)              ████
Day 5+:  Phase 6 (Monitoring)         ████...
```

**Path to simple deployment:** Phase 1-3 (1.5 days) → `make up` works

**Full production ready:** Phase 1-5 (4 days) → CI/CD automated

---

## COMMANDS SUMMARY

**Local development:**
```bash
make up          # Start all services
make down        # Stop services
make logs        # View logs
make migrate     # Run migrations
make fresh       # Wipe & re-seed DB
make test        # Run tests
make shell       # Access container shell
```

**Server deployment:**
```bash
# Staging
docker-compose -f docker-compose.staging.yml up -d

# Production
docker-compose up -d
docker-compose exec app php artisan migrate --force
```

---

**Effort total:** 4-5 jours pour simplifier complètement  
**Result:** Déploiement reproductible, simple, 1-click deployment possible
