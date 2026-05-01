# 🛠️ Scripts d'Automatisation DAZO

Ces scripts sont conçus pour simplifier la gestion, le déploiement et la maintenance de DAZO sur votre serveur.

## 📋 Liste des Scripts

| Script | Description | Quand l'utiliser |
|--------|-------------|------------------|
| `dazo-install.sh` | Installation initiale complète | Première mise en route du projet |
| `dazo-update.sh` | Mise à jour complète (Git + Deps + DB + Cache) | Déploiement d'une nouvelle version |
| `dazo-gitpull.sh` | Récupération simple du code GitHub | Vérifier les changements sans les appliquer |
| `dazo-check.sh` | Vérification de l'état du système | En cas de doute sur la santé des containers |
| `dazo-cleancache.sh` | Nettoyage et reconstruction des caches | Après un changement de config ou de routes |
| `dazo-rollback.sh` | Retour à la version précédente (Git) | En cas d'erreur critique après mise à jour |

---

## 🚀 Utilisation Détaillée

### 1. Installation Initiale (`dazo-install.sh`)
Ce script automatise tout le processus de démarrage :
- Création du `.env` (si manquant).
- Build et démarrage des containers Docker.
- Installation des dépendances PHP et JS.
- Génération de la clé d'application.
- Exécution des migrations.
- Mise en cache.

**Commande :**
```bash
./dazo-install.sh
```

### 2. Mise à jour et Déploiement (`dazo-update.sh`)
C'est le script principal pour vos déploiements réguliers. Il effectue :
1. Une sauvegarde préventive de la base de données dans `./backups/`.
2. Un `git pull` de la branche main.
3. La mise à jour des dépendances.
4. Le build des assets Vite.
5. Les migrations de base de données.
6. Le redémarrage des workers (queue, reverb, etc.).

**Commande :**
```bash
./dazo-update.sh
```

### 3. Nettoyage du Cache (`dazo-cleancache.sh`)
À utiliser en cas de comportement étrange ou après avoir modifié manuellement le fichier `.env`. Il nettoie TOUS les caches Laravel et les reconstruit proprement pour la production.

**Commande :**
```bash
./dazo-cleancache.sh
```

### 4. Vérification du Système (`dazo-check.sh`)
Affiche un rapport rapide sur :
- L'état des containers Docker.
- La connectivité PostgreSQL et Redis.
- Les permissions du dossier storage.
- Les 5 dernières lignes de logs d'erreur.
- L'espace disque restant.

**Commande :**
```bash
./dazo-check.sh
```

### 5. Rollback (`dazo-rollback.sh`)
En cas de problème après un `git pull`, ce script revient à l'état juste avant la mise à jour (via `git reset --hard HEAD@{1}`).
*Note : Il ne restaure pas automatiquement la base de données, utilisez les backups dans `./backups/` si nécessaire.*

---

## ⚙️ Configuration
Tous les scripts sont situés à la racine du projet. Assurez-vous qu'ils possèdent les droits d'exécution :
```bash
chmod +x dazo-*.sh
```
