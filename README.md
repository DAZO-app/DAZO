# DAZO — Moteur de Décision Sociocratique

DAZO est une plateforme collaborative conçue pour faciliter la prise de décision par consentement, inspirée des principes de la sociocratie. Elle permet aux organisations de structurer leurs cercles, de rédiger des propositions claires et de suivre un cycle de vie décisionnel rigoureux et transparent.

![DAZO Logo](DAZO_logo.png)

## 🎯 Mission

L'objectif de DAZO est de transformer la manière dont les groupes collaborent en remplaçant le vote majoritaire traditionnel par un moteur de **gestion des objections** et de **recherche de consentement**.

- **Gouvernance partagée** : Structure en cercles imbriqués.
- **Transparence totale** : Historique complet des versions et des débats.
- **Efficacité opérationnelle** : Détection automatique des impasses et outils de facilitation.

## 🚀 Fonctionnalités Clés

- **Gouvernance partagée** : Structure en cercles imbriqués (Parents/Sous-cercles).
- **Cycle de Décision** : Étapes claires (Brouillon, Clarification, Réaction, Objection, Adoption, Révision).
- **Échéances & Relances** : Délais automatiques par phase et relances par mail pour garantir la fluidité des processus.
- **Moteur de Feedback** : Système structuré d'objections, suggestions et clarifications intégré au thread de discussion.
- **Accès Simplifié** : Connexion par **Magic Link**, Authentification Sociale (Google, Microsoft...) et **Impersonation**.
- **RGPD & Vie Privée** : Droit à l'oubli (anonymisation complète) et **Export de portabilité** (JSON par e-mail).
- **Sitemap & SEO** : Architecture hybride SSR/SPA avec sitemaps dynamiques par cercles et JSON-LD (Schema.org).
- **Identité Visuelle** : Personnalisation dynamique du logo et du nom de l'instance.
- **Centre de Contrôle** : Dashboard d'administration avec monitoring serveur, **Sauvegardes Auto** (SQL compressé) et lecture de logs.
- **Temps Réel** : Notifications et compteurs d'actions en attente via **Laravel Reverb** (WebSockets).
- **Widgets & Snippets** : Intégration dynamique des décisions sur des sites tiers via un **Loader JS** léger et personnalisable (15 thèmes).

## 🛠 Stack Technique

- **Backend** : Laravel 11 (PHP 8.3+), API REST, Sanctum Auth, **Laravel Reverb** (WebSocket).
- **Frontend** : Vue 3 (SPA), Pinia, Vue Router, Vite.js, **Laravel Echo**.
- **Base de données** : PostgreSQL / MySQL.
- **Design** : CSS Vanilla (Design System premium, responsive & mobile-first).

## 🚀 Installation & Déploiement

Le projet utilise Docker pour garantir une parité parfaite entre le développement local et la production. Des scripts automatisés sont fournis pour simplifier toutes les opérations.

### Développement Local
1. **Pré-requis** : Docker, Docker Compose, **zip** (pour les sauvegardes).
2. **Initialisation** : `./dazo-install.sh`
3. **Lancer le développement** : `./dazo-dev.sh`
   * Accès : `http://localhost:3003`
   * Vite (HMR) : Actif sur le port 5173

### Production (VPS)
1. **Première installation** : `./dazo-install.sh`
2. **Mise à jour** : `./dazo-update.sh`

## 🛠️ Scripts de Maintenance
Une suite de scripts est disponible à la racine pour faciliter la gestion :
- `./dazo-check.sh` : Vérifie la santé de tous les services (BDD, Redis, Containers).
- `./dazo-cleancache.sh` : Vide tous les caches Laravel proprement.
- `./dazo-cleanDBbackup.sh` : Gère le nettoyage des anciennes sauvegardes SQL.
- `./dazo-refresh.sh` : (Local) Rafraîchit les droits et les caches après une modif.

Pour plus de détails, consultez la [Documentation des Scripts](docs/SCRIPTS.md). pour découvrir toutes les commandes disponibles (`update`, `cleancache`, `rollback`).

## 🐳 Architecture Docker Standard
DAZO tourne sur une architecture multi-containers optimisée pour la production :
- **dazo-app** : Backend Laravel (PHP 8.3 FPM)
- **dazo-nginx** : Serveur Web
- **dazo-db** : PostgreSQL 15
- **dazo-redis** : Cache & Sessions
- **dazo-queue** : Worker de files d'attente
- **dazo-reverb** : Serveur WebSocket (Real-time)


### 🖥 Développement local (3 terminaux)

```bash
# Terminal 1 — Serveur Laravel
php artisan serve

# Terminal 2 — Assets frontend avec Hot Module Replacement
npm run dev

# Terminal 3 — Serveur WebSocket temps réel (optionnel)
php artisan reverb:start
```

### ⚙️ Automatisation (Production)

Pour activer les relances automatiques, les notifications et la gestion des échéances,
configurez le **Scheduler**, le **Worker** et le **Serveur WebSocket** :

**1. Scheduler (Cron)** (Sur la machine hôte) :
```bash
* * * * * cd /chemin/vers/votre/projet && docker compose exec -T app php artisan schedule:run >> /dev/null 2>&1
```

**2. File d'attente (Queues)** (emails asynchrones) :
```bash
php artisan queue:work
```

**3. WebSocket Reverb** (temps réel) — via Supervisor :
```bash
php artisan reverb:start --host=0.0.0.0 --port=8080
```

## 📚 Documentation

L'ensemble de la documentation détaillée se trouve dans le dossier [`docs/`](docs/) :

| Document | Description |
|---|---|
| [ROADMAP.md](ROADMAP.md) | Roadmap V1 → V3 et backlog |
| [docs/api.md](docs/api.md) | Référence complète de l'API REST v1 |
| [docs/architecture.md](docs/architecture.md) | Architecture Laravel en couches |
| [docs/frontend.md](docs/frontend.md) | Guide SPA Vue 3, stores Pinia, Echo |
| [docs/decision-lifecycle.md](docs/decision-lifecycle.md) | Cycle de vie des décisions |
| [docs/state-machine.md](docs/state-machine.md) | Machine d'états & transitions |
| [docs/enums.md](docs/enums.md) | Référence de tous les Enums PHP |
| [docs/domain-model.md](docs/domain-model.md) | Modèle de données |
| [docs/SCRIPTS.md](docs/SCRIPTS.md) | Guide des scripts bash et automatisation |
| [docs/ROADMAP_OPTIMIZATIONS.md](docs/ROADMAP_OPTIMIZATIONS.md) | Roadmap d'optimisations techniques & priorité |
| [docs/TODO_TECHNIQUE.md](docs/TODO_TECHNIQUE.md) | Backlog technique détaillé |
| [docs/WIKI_HELP_STRUCTURE.md](docs/WIKI_HELP_STRUCTURE.md) | Structure du Wiki d'aide initial |

## ⚖️ Licence

DAZO est un logiciel en **source-available** (code source accessible), mais n'est pas "Open Source" au sens de l'OSI.

- **Usage Non-Commercial** : Libre et encouragé sous les termes de la [PolyForm Noncommercial License 1.0.0](LICENSE).
- **Usage Commercial** : Strictement **interdit** sans autorisation écrite préalable. Cela inclut le SaaS, l'hébergement payant, la revente, le white-labeling et l'intégration dans des produits payants.

Pour plus de détails, consultez le fichier [LICENSE](LICENSE) et le document [COMMERCIAL_LICENSE.md](COMMERCIAL_LICENSE.md).

---

Une initiative pour une gouvernance plus fluide et humaine.
