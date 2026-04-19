# DAZO — Plateforme de Prise de Décision Intelligente

Bienvenue sur le dépôt principal de **DAZO**, la plateforme moderne permettant aux communautés et organisations de gérer de manière fluide leurs cycles de décision, du brouillon jusqu'à l'adoption finale, en intégrant des mécanismes structurés de débat et de consentement.

---

## 🕊️ Philosophie & Concepts : La Décision à Zéro Objection

DAZO n'est pas qu'un outil de gestion de tâches, c'est une implémentation numérique des principes de la **Sociocratie** et de la **Gouvernance Partagée**. 

### 1. Le Consentement plutôt que le Consensus
Contrairement au consensus — où tout le monde doit dire "oui" — DAZO repose sur le **consentement** : personne n'a d'objection qui pourrait nuire à l'organisation ou au cercle. Cela évite les blocages interminables et permet d'avancer dès qu'une proposition est "suffisamment bonne pour maintenant, et assez sûre pour être essayée".

### 2. L'Objection est un Cadeau
Dans DAZO, une objection n'est pas un "non" définitif ou un droit de veto. C'est un signal précieux indiquant que la proposition actuelle comporte un risque ou peut être améliorée. Le processus DAZO transforme l'objection en moteur d'itération : on ne rejette pas, on amende la proposition jusqu'à ce que l'objection soit levée.

### 3. Structure en Cercles
L'application organise les membres en **Cercles** (équipes, départements, projets). Chaque cercle est souverain sur son périmètre. Cette décentralisation de l'autorité permet de garder de l'agilité tout en maintenant une transparence totale sur les décisions prises.

### 4. Rôles et Workflow
Pour garantir la fluidité, DAZO définit des rôles clairs lors d'une décision :
- **📣 Porteur** : Initie la proposition et la "pilote" jusqu'à sa validation.
- **🎭 Animateur** : Facilite le débat, s'assure que chacun est entendu et que le processus est respecté.
- **👥 Participant** : Membre du cercle qui apporte sa sagesse, clarifie et exprime ses éventuelles objections.

---

## 📚 Documentation & Références

Le projet est documenté en profondeur :

* **[Architecture Système](./docs/architecture.md)** — Vue globale des services métiers (Sanctum, Vue, Redis...).
* **[Modèle de Domaine](./docs/domain-model.md)** — Entités et règles conceptuelles.
* **[Cycle de Vie décisionnel](./docs/decision-lifecycle.md)** — Flux des états (`Draft` -> `Adopted`).
* **[Machine à États (State Machine)](./docs/state-machine.md)** — Documentation spatie/laravel-model-states.
* **[Design Front-End SPA](./docs/frontend.md)** — Structure de l'interface Vue + Vite.js.
* **[Référence API](./docs/api.md)** — Documentation complète des Endpoints REST.
* **[Référence des Enums PHP](./docs/enums.md)** — Valeurs fixes utilisées en base.

### 🚀 Journal d'Implémentation (V1)
Notre progression étape par étape :
* [Étape 0 — Fondations Techniques](./docs/steps/00_fondations_techniques.md)
* [Étape 1 — Authentification & Utilisateurs](./docs/steps/01_authentification_utilisateurs.md)
* [Étape 2 — Gestion des Cercles](./docs/steps/02_gestion_cercles.md)
* [Étape 3 — Moteur de Décision (Core)](./docs/steps/03_moteur_decision_core.md)
* [Étape 4 — Machine d'États & Cycle de Vie](./docs/steps/04_machine_etats.md)
* [Étape 5 — Feedback, Consentements et Débat](./docs/steps/05_feedback_consentement_thread.md)
* [Étape 6 — Adoption et Nouvelle Version](./docs/steps/06_adoption_et_revision.md)
* [Étape 7 — Fondations Front-End (SPA Vue 3)](./docs/steps/07_frontend_foundations.md)
* [Étape 8 — Moteur de Décision (Composants UI)](./docs/steps/08_moteur_decision_ui.md)
* [Étape 9 — Finalisation Infrastructure Backend](./docs/steps/09_infrastructure_backend.md)
* [Étape 10 — Frontend Fonctionnel & Panel Admin](./docs/steps/10_frontend_features.md)
* [Étape 11 — Impersonation (Se faire passer pour)](./docs/steps/11_impersonation.md)

---

## 🛠 Guide d'Installation

DAZO V1 est architecturé sur :
* **Backend** : Laravel 11 (PHP 8.3+)
* **Base de données** : PostgreSQL (avec support des UUID)
* **Cache & Files d'attente** : Redis
* **Frontend** : Vue 3 + Vite.js (SPA compilée via `@vitejs/plugin-vue`)

### 1. Pré-requis
- PHP 8.3 ou supérieur (avec extensions `pdo_pgsql`, `mbstring`, `xml`, `curl`)
- Composer 2.x
- Node.js 18+ et NPM
- PostgreSQL 14+
- Redis

### 2. Configuration Initiale

Clonez le dépôt puis installez les dépendances :
```bash
git clone <url-du-repo> dazo-app
cd dazo-app

# Installer les packages PHP
composer install

# Installer les packages Node
npm install
```

Configurez les variables d'environnement :
```bash
cp .env.example .env
php artisan key:generate
```

Configurez PostgreSQL dans `.env` :
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=dazo
DB_USERNAME=dazo_user
DB_PASSWORD=secret

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

### 3. Migrations & Seed de la Base

Lancez les migrations pour créer la structure et injecter les données de démonstration :
```bash
php artisan migrate:fresh --seed
```

### 4. Build du Frontend

Compilez les assets Vue :
```bash
npm run build
```

### 5. Démarrage du Serveur

```bash
php artisan serve
```

L'application est maintenant accessible sur `http://127.0.0.1:8000`.

---

## 🔐 Accès & Administration

### Comptes de démonstration

| Rôle | Email | Mot de passe |
|---|---|---|
| **Administrateur** | `admin@dazo.test` | `password` |
| **Utilisateur** | `user@dazo.test` | `password` |

### Panel d'administration

Le panel admin est accessible via l'icône ⚙ dans la sidebar (visible uniquement pour les admins). Il permet de :
- **Gérer les cercles** : création, édition, gestion des membres
- **Voir les utilisateurs** : liste des comptes et rôles
- **Gérer les catégories** : organiser les décisions par thème
- **Configurer l'instance** : paramètres globaux via les endpoints `/api/v1/admin/*`

## 📖 Utilisation Rapide (Scénario Métier)

1. **Connectez-vous** avec les identifiants de démonstration
2. **Explorez vos cercles** via la navigation latérale (⊛ Cercles)
3. **Créez une décision** via le bouton `+ Nouvelle décision` sur le Dashboard ou la liste des décisions
4. **Sélectionnez un cercle**, renseignez un titre et une proposition
5. **Suivez le cycle de vie** : Brouillon → Clarification → Réaction → Objection → Adoption
6. **Administrez** la plateforme via le panel admin (⚙)

---

## 🧪 Exécuter la Suite de Tests

DAZO utilise PostgreSQL pour les tests (base `dazo_test`). Créez cette base avant de lancer les tests :
```bash
# Créer la base de test
PGPASSWORD=secret psql -h 127.0.0.1 -U dazo_user -d postgres -c "CREATE DATABASE dazo_test"

# Lancer toute la suite (Feature et Unit)
vendor/bin/phpunit
```

---

## 📁 Structure du Projet

```
app/
├── Console/Commands/     # Commandes Artisan (PurgeOldLogs)
├── Enums/                # Enums PHP typés (DecisionStatus, FeedbackType...)
├── Events/               # Événements métier (DecisionCreated, FeedbackSubmitted...)
├── Http/
│   ├── Controllers/Api/V1/  # Contrôleurs API REST
│   ├── Middleware/           # Middleware (Active, Admin)
│   └── Requests/            # FormRequests de validation
├── Listeners/            # Listeners de notification
├── Models/               # Modèles Eloquent avec UUID
├── Policies/             # Politiques d'autorisation
└── Services/             # Services métier (DecisionService, FeedbackService...)

resources/js/
├── components/           # Composants Vue réutilisables
├── layouts/              # Layout principal (AppLayout)
├── router/               # Configuration Vue Router
├── stores/               # Stores Pinia (auth, decision, circle)
└── views/                # Pages de l'application
    └── admin/            # Pages du panel d'administration

database/
├── factories/            # Factories pour les tests
├── migrations/           # Migrations PostgreSQL
└── seeders/              # Données de démonstration
```
