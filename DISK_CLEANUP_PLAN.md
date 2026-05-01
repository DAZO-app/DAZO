# 🧹 DISK CLEANUP PLAN — 53Gi à optimiser

**Situation:** 72Gi total, 53Gi utilisé (73%), 20Gi libre  
**Target:** Réduire à 50% utilisation (36Gi utilisé)  
**Action:** Nettoyer 17Gi minimum  

---

## 🚨 POURQUOI C'EST CRITIQUE

À 73%, tu n'as que 20Gi libres. Si:
- Docker logs gonflent → Out of disk
- PostgreSQL data croît → Out of disk
- Backup créé → Out of disk
- App crash immédiatement

**Action immédiate: Nettoyer avant TOUT.**

---

## 🔍 QU'EST-CE QUI PREND DE LA PLACE?

### Docker Images & Layers (~25Gi)
```
dazo-app:latest                2.09GB
decidim/decidim:latest         4.04GB
onlyoffice/documentserver:     5.73GB  ← ÉNORME!
nextcloud:29                   1.89GB
mattermost:                    1.03GB
postgres images:               ~1.3GB
redis images:                  ~500MB
nginx alpine:                  ~100MB
mariadb:                       ~553MB
```

### PostgreSQL Data (~5Gi)
```
/var/lib/postgresql/data/
  └─ dazo database
  └─ mattermost database
  └─ decidim database
  └─ nextcloud database
```

### Docker Volumes (~3-5Gi)
```
Unused volumes
Old container state
Temp data
```

---

## ✅ CLEANUP PLAN (SAFE)

### STEP 1: Docker System Cleanup (2-3Gi gain)
```bash
# SSH to server
ssh root@51.83.162.146

# Check what will be removed
docker system df

# Remove unused images
docker image prune -a --filter "until=720h"  # Images older than 30 days
# ✅ Saves: ~5-10Gi (but careful — OnlyOffice large!)

# Remove unused volumes
docker volume prune
# ✅ Saves: ~1-2Gi

# Remove unused networks
docker network prune
# ✅ Saves: Minimal, but clean

# Remove build cache
docker builder prune
# ✅ Saves: ~500MB

# Total expected: 3-5Gi freed
```

### STEP 2: Log Rotation (1-2Gi gain)
```bash
# Clear old logs
find /var/lib/docker/containers -type f -name "*.log" -exec truncate -s 0 {} \;
# ✅ Saves: ~1-2Gi

# Or be surgical:
docker inspect dazo-app | grep -i "log" | grep file
# Then:
truncate -s 0 /path/to/logfile
```

### STEP 3: PostgreSQL Cleanup (2-3Gi potential)
```bash
# Connect to PostgreSQL
docker compose exec dazo-db psql -U postgres -d dazo

# Inside psql:
-- Get database sizes
\l+

-- Vacuum (reclaim space)
VACUUM FULL;
ANALYZE;

-- Exit
\q
```

### STEP 4: Identify Unused Services (~5Gi possible)
```bash
# If Decidim, OnlyOffice, Nextcloud, Mattermost NOT needed:
docker compose down

# Then remove their images:
docker rmi decidim/decidim:latest
docker rmi onlyoffice/documentserver:latest
docker rmi nextcloud:29
docker rmi mattermost/mattermost-team-edition:latest

# ✅ Saves: ~13Gi (if all removed)
```

---

## 🎯 RECOMMENDED SEQUENCE

### Phase 1: Quick Wins (NO RISK) — 30 min
```bash
# Step 1: Docker cleanup
docker system df
docker image prune -a --filter "until=720h"
docker volume prune

# Result: 3-5Gi freed
# Time: 5-10 min
```

### Phase 2: Log Rotation (LOW RISK) — 20 min
```bash
# Step 2: Clear old logs
find /var/lib/docker/containers -type f -name "*.log" -exec truncate -s 0 {} \;

# Result: 1-2Gi freed
# Time: 5 min
# Impact: None (logs regenerate)
```

### Phase 3: Database Vacuum (LOW RISK) — 15 min
```bash
# Step 3: PostgreSQL optimization
docker compose exec dazo-db psql -U postgres -d dazo -c "VACUUM FULL; ANALYZE;"

# Result: 1-2Gi freed (if DB bloated)
# Time: 5-10 min
# Impact: None (operation is safe)
```

### Phase 4: Archive/Remove Old Services (DECISION NEEDED)
```bash
# Step 4: Remove unused services
# ONLY IF NOT NEEDED:
# - Decidim (5Gi)
# - OnlyOffice (6Gi)
# - Nextcloud (2Gi)
# - Mattermost (1Gi)

# Result: 5-14Gi freed
# Time: 5 min
# Impact: Those services stop working
```

---

## 📊 EXPECTED RESULTS

### Before Cleanup
```
Total: 72Gi
Used:  53Gi (73%)
Free:  20Gi
```

### After Phase 1-3 (Quick Wins)
```
Total: 72Gi
Used:  47Gi (65%)
Free:  25Gi
```

### After Phase 4 (Remove OnlyOffice + Decidim)
```
Total: 72Gi
Used:  36Gi (50%)
Free:  36Gi  ✅ Ideal!
```

---

## 🔧 DETAILED COMMANDS

### Option A: Quick Interactive Cleanup
```bash
ssh root@51.83.162.146

# Check current state
df -h
docker system df

# Clean (with confirmation)
docker system prune --all --volumes --force

# Verify
df -h
docker system df

# Result: 5-8Gi freed
```

### Option B: Surgical Approach (Safer)
```bash
# 1. List all images and sizes
docker image ls --format "table {{.Repository}}\t{{.Size}}\t{{.ID}}"

# 2. Remove specific large images (if not needed)
docker rmi onlyoffice/documentserver:latest          # 5.73GB
docker rmi decidim/decidim:latest                     # 4.04GB

# 3. Remove dangling images
docker image prune -a

# 4. Check volumes
docker volume ls
docker volume prune

# 5. Verify
df -h
```

### Option C: Automated Script
```bash
#!/bin/bash
# save as cleanup-docker.sh

set -e

echo "=== Docker Cleanup Started ==="
echo "Before:"
df -h / | tail -1

# Step 1: Remove dangling images
echo "Removing dangling images..."
docker image prune -af --filter "label!=keep"

# Step 2: Remove unused volumes
echo "Removing unused volumes..."
docker volume prune -f

# Step 3: Remove build cache
echo "Removing build cache..."
docker builder prune -af

# Step 4: Truncate logs
echo "Truncating container logs..."
find /var/lib/docker/containers -type f -name "*.log" -exec truncate -s 0 {} \;

echo "After:"
df -h / | tail -1
echo "=== Cleanup Complete ==="
```

---

## ⚠️ THINGS TO AVOID

### ❌ DON'T DO THIS
```bash
# Don't force remove running containers
docker rm -f dazo-app                 # Will lose data!

# Don't remove postgres data directory
rm -rf /var/lib/postgresql/           # Data loss!

# Don't truncate active logs
truncate -s 0 /var/log/nginx/access.log  # Breaks logging

# Don't run on production without backup first
# Always: docker compose exec dazo-db pg_dump > backup.sql
```

### ✅ DO THIS INSTEAD
```bash
# Backup first
docker compose exec dazo-db pg_dump -U postgres dazo > dazo-backup-$(date +%Y%m%d).sql

# Then cleanup safely
docker image prune -a --filter "until=720h"

# Verify nothing broke
docker compose ps
docker compose logs dazo-app | head -20
```

---

## 📋 CHECKLIST BEFORE CLEANUP

- [ ] Have you taken a PostgreSQL backup?
  ```bash
  docker compose exec dazo-db pg_dump -U postgres dazo > backup.sql
  ```

- [ ] Do you know what services are running?
  ```bash
  docker compose ps
  ```

- [ ] Have you documented what you're removing?
  ```bash
  docker image ls > images-before.txt
  ```

- [ ] Is there someone available if cleanup fails?

- [ ] You have shell access and can SSH back?

---

## 🚀 QUICK CLEANUP COMMAND (Safe)

**Just run this:**
```bash
ssh root@51.83.162.146

# Backup first (ALWAYS)
docker compose exec dazo-db pg_dump -U postgres dazo > /tmp/dazo-backup-$(date +%Y%m%d-%H%M%S).sql

# Clean (safe operations only)
docker system df                                    # Show what will be removed
docker image prune -a --filter "until=720h" -f     # Remove old images
docker volume prune -f                              # Remove unused volumes
docker builder prune -af                            # Clear build cache
find /var/lib/docker/containers -type f -name "*.log" -exec truncate -s 0 {} \;  # Clear logs

# Verify
df -h
docker system df

# Check app still works
docker compose ps
docker compose logs dazo-app | tail -5
```

**Expected result:** 5-10Gi freed in 5 minutes!

---

## 📞 TROUBLESHOOTING

### "Out of disk" during cleanup?
```bash
# Emergency: Remove largest unused image
docker rmi onlyoffice/documentserver:latest   # Frees 5Gi immediately
```

### Services won't restart after cleanup?
```bash
# Rebuild everything
docker compose down
docker compose build --no-cache
docker compose up -d
```

### PostgreSQL slow after cleanup?
```bash
# Run maintenance
docker compose exec dazo-db psql -U postgres -d dazo -c "VACUUM FULL; REINDEX DATABASE dazo;"
```

---

## 🎯 FINAL STATE (After Cleanup)

```
Disk:  72Gi total
Used:  36Gi (50%)
Free:  36Gi

All services running
DAZO healthy ✅
Backups taken ✅
Ready for bêta ✅
```

---

**Status after cleanup:** Ready for beta launch! 🚀

Now we have breathing room for logs, backups, and scaling.
