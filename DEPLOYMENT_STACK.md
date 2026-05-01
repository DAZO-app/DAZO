# 🔧 DEPLOYMENT STACK — INFRASTRUCTURE DAZO ACTUELLE

**Date:** 2026-05-01  
**Serveur:** vps-dc05dcc3  
**Audit exécuté:** 2026-05-01 11:03 UTC  

---

## 📊 INFRASTRUCTURE ACTUELLE

### Hardware
```
OS:          Ubuntu 25.04 (Plucky Puffin)
Kernel:      6.14.0-37-generic #37-Ubuntu SMP x86_64
CPU:         4 cores
RAM:         7.6 Gi (3.7 Gi used, 3.9 Gi available)
Disk:        72 Gi (53 Gi used = 73%, 20 Gi free)
IP Public:   51.83.162.146
Hostname:    vps-dc05dcc3
```

### Docker Setup
```
Docker:              29.2.1, build a5c7197
Docker Compose:      2.x (via services)
Containers:          13 running
  - 7 for DAZO
  - 3 for Mattermost
  - 1 for Decidim
  - 1 for OnlyOffice
  - 1 for Nextcloud
```

---

## 🎯 DAZO CONTAINERS

### 1. **dazo-app** (PHP-FPM)
```
Image:       dazo-app:latest
Status:      Up 2 days
Port:        9000/tcp (internal, via Nginx)
Memory:      ~50-80 MB
Role:        Laravel application server
```

### 2. **dazo-nginx** (Reverse Proxy)
```
Image:       nginx:alpine
Status:      Up 2 days
Port:        80 → Docker 80
             443 → Nginx (external)
Role:        Web server + reverse proxy for dazo-app
```

### 3. **dazo-queue** (Laravel Queue Worker)
```
Image:       dazo-queue:latest
Status:      Up 2 days
Port:        9000/tcp (internal)
Memory:      ~50 MB
Role:        Background jobs (emails, notifications, etc.)
Command:     php artisan queue:work --sleep=3 --tries=3
```

### 4. **dazo-scheduler** (Laravel Scheduler)
```
Image:       dazo-queue:latest (same as queue)
Status:      Up 2 days
Port:        9000/tcp (internal)
Memory:      ~50 MB
Role:        Scheduled tasks (cleanup, reminders, etc.)
Command:     php artisan schedule:work
```

### 5. **dazo-reverb** (WebSocket Server)
```
Image:       dazo-app:latest (same as app)
Status:      Up 2 days
Port:        8080 → 8081 (external, for WebSocket)
Memory:      ~40 MB
Role:        Real-time notifications (Echo/WebSocket)
Command:     php artisan reverb:start --host=0.0.0.0 --port=8080
```

### 6. **dazo-db** (PostgreSQL)
```
Image:       postgres:15
Status:      Up 2 days
Port:        5432 → 5432 (internal only)
Volume:      /var/lib/postgresql/data
Role:        Primary database
Database:    dazo
User:        postgres (or dazo_user)
Version:     15.x
```

### 7. **dazo-redis** (Cache/Session)
```
Image:       redis:alpine
Status:      Up 2 days
Port:        6379 → 6379 (internal only)
Role:        Cache, session store, queue backend
Memory:      ~35 MB
```

### 8. **dazo-mailpit** (Email Testing)
```
Image:       axllent/mailpit:latest
Status:      Up 2 days (healthy)
Port:        1025 → 1025 (SMTP)
Port:        8025 → 8025 (Web UI at http://localhost:8025)
Role:        Catch all emails for testing/inspection
```

---

## 🌐 NETWORK CONFIGURATION

### Ports Exposed
```
External Internet:
  51.83.162.146:80    → Nginx HTTP (redirects to HTTPS)
  51.83.162.146:443   → Nginx HTTPS (production)
  51.83.162.146:8081  → Reverb WebSocket

Internal Docker Network:
  172.18.0.0/16       → Main Docker network
  dazo-app:9000       → PHP-FPM (via Nginx)
  dazo-db:5432        → PostgreSQL
  dazo-redis:6379     → Redis
  dazo-mailpit:1025   → SMTP
  dazo-mailpit:8025   → Mail Web UI (localhost only)
```

### Domain
```
Domain:     beta.dazo.fr
SSL:        Via Nginx reverse proxy
HTTPS:      ✅ Active (port 443)
HTTP:       ✅ Redirects to HTTPS
```

---

## 💾 DATABASE

### PostgreSQL 15
```
Host:       dazo-db (container), localhost:5432 (from host)
Database:   dazo
Version:    PostgreSQL 15
User:       postgres (superuser)
User:       dazo_user (app user)
Location:   /var/lib/postgresql/data (Docker volume)
Backup:     Via docker exec pg_dump
```

**Connection strings:**
```
From Docker:     postgres://dazo_user:password@dazo-db:5432/dazo
From Host:       postgres://dazo_user:password@localhost:5432/dazo
```

---

## 🗂️ FILE LOCATIONS

### In Container
```
/var/www/html/                    → Laravel project root
/var/www/html/storage/            → User uploads, cache, logs
/var/www/html/.env                → Environment config
/var/www/html/storage/logs/       → Application logs
```

### On Host (Docker volumes)
```
Not directly accessible (files are in container)
Use: docker compose exec dazo-app ls -la /path
```

---

## 📊 CURRENT ISSUES IDENTIFIED

### 🔴 CRITICAL
1. **Disk at 73%** — Only 20Gi free remaining
   - Clean up old Docker images/volumes
   - Archive old logs
   - Remove unused containers

2. **Reverb Error** — "Connection reset by peer"
   - Indicates WebSocket connection drops
   - May need Reverb restart or debugging
   - See logs: `docker compose logs dazo-reverb`

### 🟠 MEDIUM
3. **Multiple Services on Same Server**
   - Mattermost, Decidim, OnlyOffice, Nextcloud taking space/resources
   - May cause conflicts if all scale up

4. **No Dedicated Backups Visible**
   - PostgreSQL backups not documented
   - Should setup automated pg_dump schedule

### 🟡 LOW
5. **Email via Mailpit** (for testing)
   - Production should use real SMTP service
   - Currently configured for testing only

---

## 📈 RESOURCE USAGE

### RAM Usage
```
Total:       7.6 Gi
Used:        3.7 Gi (49%)
Available:   3.9 Gi (51%)

Breakdown:
  - dazo-app, queue, scheduler: ~150 MB
  - PostgreSQL: ~500 MB
  - Redis: ~35 MB
  - Nginx: ~10 MB
  - Other services: ~3 Gi
```

### CPU Usage
```
4 Cores available
Current load: Low (mostly idle)
Peak: During queue processing or reporting
```

### Disk Usage
```
Total:       72 Gi
Used:        53 Gi (73%)
Free:        20 Gi (27%)

Breakdown:
  - Docker images: ~20 Gi
  - Container layers: ~25 Gi
  - PostgreSQL data: ~5 Gi
  - Other services: ~3 Gi
```

**⚠️ Recommendation:** Clean up when < 25% free disk

---

## 🔐 SECURITY NOTES

### ✅ Good
- SSL/TLS via Nginx (port 443)
- PostgreSQL on internal network only
- Redis on internal network only
- Mailpit not exposed externally

### ⚠️ To Review
- PostgreSQL credentials (check .env inside container)
- Redis password (if any)
- Nginx config for security headers
- CORS configuration for Reverb

---

## 🚀 KEY COMMANDS (For Operations)

### View Status
```bash
docker compose ps                        # All containers
docker compose logs dazo-app -f         # App logs
docker compose logs dazo-queue -f       # Queue logs
docker compose logs dazo-reverb -f      # WebSocket logs
```

### Restart Services
```bash
docker compose restart dazo-app         # Restart app only
docker compose restart                  # Restart all
docker compose down && docker compose up -d  # Full restart
```

### Database Access
```bash
docker compose exec dazo-db psql -U postgres -d dazo
# Inside psql:
\dt                    # List tables
\l                     # List databases
SELECT version();      # Check version
```

### Check Logs
```bash
docker compose exec dazo-app tail -f storage/logs/laravel.log
```

### Queue Status
```bash
docker compose exec dazo-app php artisan queue:failed    # Failed jobs
docker compose exec dazo-app php artisan queue:retry     # Retry failed
docker compose exec dazo-app php artisan queue:flush     # Flush all
```

---

## 📋 COMPARISON: CURRENT vs. RECOMMENDED

| Aspect | Current | Recommended | Priority |
|--------|---------|-------------|----------|
| Database | PostgreSQL 15 | ✅ PostgreSQL 15 | ✅ Perfect |
| Cache | Redis Alpine | ✅ Redis Alpine | ✅ Good |
| WebSocket | Reverb ✅ | ✅ Reverb | ✅ Working |
| Email | Mailpit (test) | SMTP provider | 🟡 Medium |
| Backups | Manual? | Automated schedule | 🟠 High |
| Monitoring | None visible | Sentry + Prometheus | 🟠 High |
| Disk | 73% full | < 50% | 🔴 Critical |
| SSL | ✅ Via Nginx | ✅ Keep current | ✅ Good |
| Scaling | Monolithic | Can horizontally scale | 🟡 Future |

---

## 🔧 NEXT STEPS

### Week 1 (Immediate)
- [ ] Cleanup disk (reduce from 73% to 50%)
- [ ] Document PostgreSQL backup schedule
- [ ] Setup automated backups
- [ ] Test disaster recovery

### Week 2 (High Priority)
- [ ] Setup monitoring (errors, performance)
- [ ] Configure proper SMTP for production emails
- [ ] Increase automated alerting

### Week 3+ (Infrastructure)
- [ ] Implement load balancing if needed
- [ ] Separate database server (if scaling)
- [ ] CDN for static assets
- [ ] Dedicated backup server

---

## 📞 ACCESSING PRODUCTION

### SSH Access
```bash
ssh root@51.83.162.146
# or
ssh -i /path/to/key user@51.83.162.146
```

### Docker Compose Location
```bash
# Likely in:
/root/dazo  or  /home/user/dazo  or  /opt/dazo

# Find it:
docker ps | grep dazo
# Then:
docker inspect dazo-app | grep -i "working\|exec"
```

---

**Last Updated:** 2026-05-01  
**Hostname:** vps-dc05dcc3  
**Status:** ✅ Running stable, minor issues (disk, reverb errors)  
