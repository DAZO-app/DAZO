# 🔍 SERVER AUDIT SCRIPT — Récupérer l'environnement actuel

**Objectif:** Récupérer complètement la configuration du serveur pour documentation et standardisation

**À exécuter:** Sur ton serveur de production/staging

---

## SCRIPT COMPLET À COPIER-COLLER

Crée un fichier `audit-server.sh` et exécute-le:

```bash
#!/bin/bash

# ======================================
# DAZO SERVER AUDIT SCRIPT
# ======================================
# Collect all server configuration info
# Usage: bash audit-server.sh > server-audit-$(date +%Y%m%d-%H%M%S).txt
# ======================================

echo "========== DAZO SERVER AUDIT =========="
echo "Date: $(date)"
echo "Hostname: $(hostname)"
echo ""

# === SYSTEM INFO ===
echo "### SYSTEM INFORMATION ###"
echo "Kernel: $(uname -a)"
echo "OS: $(cat /etc/os-release | grep -E '^NAME=|^VERSION=')"
echo "Cores: $(nproc)"
echo "RAM: $(free -h | head -2)"
echo "Disk: $(df -h / | tail -1)"
echo ""

# === PHP ===
echo "### PHP CONFIGURATION ###"
php --version
echo ""
echo "PHP Extensions:"
php -m | sort
echo ""
echo "PHP Config Values:"
php -i | grep -E "memory_limit|upload_max_filesize|post_max_size|max_execution_time|default_socket_timeout|date.timezone"
echo ""

# === MYSQL ===
echo "### MYSQL CONFIGURATION ###"
if command -v mysql &> /dev/null; then
    mysql --version
    echo ""
    echo "MySQL Variables (if accessible):"
    mysql -u root -e "SELECT VERSION();" 2>/dev/null || echo "MySQL root access check failed (normal if password protected)"
else
    echo "MySQL not found in PATH"
fi
echo ""

# === NGINX ===
echo "### NGINX CONFIGURATION ###"
if command -v nginx &> /dev/null; then
    nginx -v
    echo ""
    echo "Nginx Config Test:"
    nginx -t
    echo ""
    echo "Nginx Config Location:"
    which nginx
    echo "Config dir: $(nginx -T 2>&1 | grep -m1 'Configuration file')"
else
    echo "Nginx not installed or not in PATH"
fi
echo ""

# === DOCKER ===
echo "### DOCKER CONFIGURATION ###"
if command -v docker &> /dev/null; then
    docker --version
    docker-compose --version
    echo ""
    echo "Running Containers:"
    docker ps --format "table {{.Names}}\t{{.Image}}\t{{.Status}}\t{{.Ports}}"
    echo ""
    echo "Docker Images:"
    docker images
else
    echo "Docker not installed"
fi
echo ""

# === NODE.js ===
echo "### NODE.JS CONFIGURATION ###"
if command -v node &> /dev/null; then
    node --version
    npm --version
else
    echo "Node.js not installed"
fi
echo ""

# === REDIS ===
echo "### REDIS CONFIGURATION ###"
if command -v redis-cli &> /dev/null; then
    redis-cli --version
    echo "Redis Connection Test:"
    redis-cli ping 2>/dev/null || echo "Redis not accessible (or not running on default port)"
else
    echo "Redis not installed"
fi
echo ""

# === PROJECT STRUCTURE ===
echo "### DAZO PROJECT STRUCTURE ###"
if [ -d "/var/www/dazo-project" ]; then
    echo "Project Location: /var/www/dazo-project"
    echo "Project Size: $(du -sh /var/www/dazo-project 2>/dev/null)"
    echo ""
    echo "Laravel Version: $(grep '"laravel/framework"' /var/www/dazo-project/composer.lock | head -1 | grep -oP '"\K[^"]*' || echo 'Not found')"
    echo "PHP Requirement: $(grep '"php"' /var/www/dazo-project/composer.json | head -1)"
    echo ""
    echo "Key Directories:"
    ls -ld /var/www/dazo-project/{app,routes,resources,storage,bootstrap} 2>/dev/null
    echo ""
    echo "Storage Permissions:"
    ls -la /var/www/dazo-project/storage/ | head -5
    echo ""
    echo "Bootstrap Permissions:"
    ls -la /var/www/dazo-project/bootstrap/cache/ 2>/dev/null | head -3
else
    echo "Project not found at /var/www/dazo-project"
fi
echo ""

# === ENVIRONMENT ===
echo "### ENVIRONMENT CONFIGURATION ###"
if [ -f "/var/www/dazo-project/.env" ]; then
    echo ".env exists: Yes"
    echo ".env size: $(wc -c < /var/www/dazo-project/.env) bytes"
    echo ""
    echo ".env Configuration (non-sensitive keys):"
    grep -E '^(APP_NAME=|APP_ENV=|APP_DEBUG=|DB_CONNECTION=|CACHE_DRIVER=|SESSION_DRIVER=|MAIL_MAILER=)' /var/www/dazo-project/.env
    echo ""
    echo "⚠️  Sensitive values (passwords, keys) not shown for security"
else
    echo ".env not found"
fi
echo ""

# === NETWORK ===
echo "### NETWORK CONFIGURATION ###"
echo "Server IPs:"
ip addr show | grep "inet " | grep -v "127.0.0.1"
echo ""
echo "Listening Ports:"
ss -ltnp 2>/dev/null | grep -E 'LISTEN|:80|:443|:3306|:6379|:8000|:8080'
echo ""

# === SERVICES ===
echo "### RUNNING SERVICES ###"
ps aux | grep -E 'php|nginx|mysql|redis' | grep -v grep
echo ""

# === LOG FILES ===
echo "### LOG FILES STATUS ###"
echo "Laravel Log (last 10 lines):"
if [ -f "/var/www/dazo-project/storage/logs/laravel.log" ]; then
    tail -10 /var/www/dazo-project/storage/logs/laravel.log
else
    echo "Log file not found"
fi
echo ""
echo "Nginx Error Log (last 5 lines):"
tail -5 /var/log/nginx/error.log 2>/dev/null || echo "Not accessible"
echo ""

# === ARTISAN ===
echo "### LARAVEL ARTISAN ====="
if [ -f "/var/www/dazo-project/artisan" ]; then
    cd /var/www/dazo-project
    echo "Laravel Version: $(php artisan --version)"
    echo "Environment: $(php artisan tinker --execute="echo env('APP_ENV');"  2>/dev/null | tail -1)"
    echo "Debug Mode: $(php artisan tinker --execute="echo env('APP_DEBUG');"  2>/dev/null | tail -1)"
else
    echo "Artisan not found"
fi
echo ""

# === DATABASE STATUS ===
echo "### DATABASE STATUS ====="
if command -v mysql &> /dev/null; then
    echo "Database Connection Test:"
    # Try to connect (this will fail if password needed, which is normal)
    mysql -u root -e "SELECT database();" 2>/dev/null || echo "Note: MySQL connection requires credentials (normal)"
else
    echo "MySQL client not available"
fi
echo ""

# === DISK I/O ===
echo "### DISK USAGE ====="
echo "Overall:"
df -h
echo ""
echo "Project directories:"
du -sh /var/www/dazo-project/* 2>/dev/null | sort -h
echo ""

# === SSL/HTTPS ===
echo "### SSL/HTTPS STATUS ====="
if command -v openssl &> /dev/null; then
    echo "OpenSSL Version: $(openssl version)"
    # Check if cert file exists
    if [ -f "/etc/letsencrypt/live/dazo.app/fullchain.pem" ]; then
        echo "Let's Encrypt Cert Found: Yes"
        echo "Expiration: $(openssl x509 -in /etc/letsencrypt/live/dazo.app/fullchain.pem -noout -enddate 2>/dev/null)"
    else
        echo "Let's Encrypt cert not found at default location"
    fi
else
    echo "OpenSSL not installed"
fi
echo ""

# === SUMMARY ===
echo "========== END OF AUDIT =========="
echo "Generated: $(date)"
echo "Hostname: $(hostname)"
```

---

## COMMENT EXÉCUTER

### Option 1: Exécuter directement (plus simple)

```bash
# SSH to your server
ssh user@your-server.com

# Run the commands one by one:

# System info
uname -a
cat /etc/os-release
nproc
free -h
df -h

# PHP
php --version
php -m
php -i | grep -E "memory_limit|upload_max_filesize|post_max_size|max_execution_time"

# MySQL
mysql --version

# Nginx
nginx -v
nginx -t

# Docker
docker --version
docker-compose --version
docker ps
docker images

# Node
node --version
npm --version

# Redis
redis-cli --version
redis-cli ping

# Project structure
ls -la /var/www/dazo-project/
du -sh /var/www/dazo-project/

# Laravel
cd /var/www/dazo-project
php artisan --version
php artisan config:show

# Logs
tail -20 storage/logs/laravel.log
```

### Option 2: Sauvegarder dans un fichier

```bash
# Save script locally first
# Then upload to server
scp audit-server.sh user@server:/tmp/

# SSH and run
ssh user@server
bash /tmp/audit-server.sh > ~/server-audit-$(date +%Y%m%d).txt

# Download the file back
scp user@server:~/server-audit-20260501.txt ./

# View it
cat server-audit-20260501.txt
```

---

## INFORMATIONS CLÉS À NOTER

**À récupérer absolument:**

| Info | Commande | Pourquoi |
|------|----------|---------|
| OS Version | `cat /etc/os-release` | Compatibilité Docker |
| PHP Version | `php --version` | Dépendances Laravel |
| PHP Extensions | `php -m` | Packages requis |
| MySQL Version | `mysql --version` | DB compatibility |
| Memory Limit | `php -i \| grep memory_limit` | Upload size |
| Nginx Version | `nginx -v` | Load balancing |
| Docker Installed | `docker --version` | Déploiement |
| Project Size | `du -sh /var/www/dazo-project` | Storage needs |
| PHP Processes | `ps aux \| grep php` | Current load |
| Nginx Config | `nginx -t` | Web server status |
| Laravel Env | `grep APP_ENV /var/www/dazo-project/.env` | Environment mode |
| DB Credentials | Check `.env` | Connection info |
| SSL Certificate | `openssl x509 -in /path/to/cert` | HTTPS status |
| Disk Available | `df -h` | Space for backups |

---

## APRÈS AVOIR COLLECTÉ LES INFOS

1. **Crée le fichier** `SERVER_INFO.md` dans le project root
2. **Copie les outputs** dans ce fichier
3. **Ne commit pas les secrets** (DB password, API keys)
4. **Utilise comme base** pour créer `.env.docker`

---

## EXEMPLE DE OUTPUT ATTENDU

```
========== DAZO SERVER AUDIT ==========
Date: Mon May 1 14:32:45 UTC 2026
Hostname: dazo-production-01

### SYSTEM INFORMATION ###
Kernel: Linux dazo-prod 5.10.0-21-generic x86_64
OS: NAME="Ubuntu" VERSION="22.04.1 LTS"
Cores: 8
RAM:               total        used        free      shared
                   15Gi       12Gi        3Gi        500Mi
Disk: Filesystem     Size  Used Avail Use% Mounted on
      /dev/sda1      100G   45G   55G  45% /

### PHP CONFIGURATION ###
PHP 8.2.1 (cli) ...
memory_limit = 256M
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
default_socket_timeout = 60

### MYSQL CONFIGURATION ###
mysql  Ver 8.0.32-0ubuntu0.22.04.1

### NGINX CONFIGURATION ###
nginx version: nginx/1.24.0

### DOCKER CONFIGURATION ###
Docker version 24.0.0
docker-compose version 2.17.0

### PROJECT STRUCTURE ###
Project Location: /var/www/dazo-project
Project Size: 850M
Laravel Version: "^11.0"
```

---

## PROCHAINES ÉTAPES

1. ✅ Exécuter le script d'audit
2. ✅ Créer `SERVER_INFO.md` avec les résultats
3. ✅ Utiliser les infos pour remplir `DEPLOYMENT_STACK.md`
4. ✅ Créer `.env.docker` basé sur config actuelle
5. ✅ Tester Docker Compose localement
6. ✅ Déployer sur staging
7. ✅ Migrer production progressivement

---

**Une fois l'audit fait, envoie-moi le fichier `server-audit-*.txt` et je peux:**
- Créer `DEPLOYMENT_STACK.md` complet
- Configurer `.env.docker` correct
- Adapter les Dockerfiles à ta stack
- Préparer les scripts de déploiement
