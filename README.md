# DAZO — Plateforme de Prise de Décision Intelligente

Bienvenue sur le dépôt principal de **DAZO**, la plateforme moderne permettant aux communautés et organisations de gérer de manière fluide leurs cycles de décision, du brouillon jusqu'à l'adoption finale, en intégrant des mécanismes structurés de débat et de consentement.

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

---

## 🛠 Guide d'Installation Exhaustif

DAZO V1 est architecturé sur :
* **Backend** : Laravel 13 (PHP 8.3+)
* **Base de données** : PostgreSQL (avec support des UUID)
* **Cache & Files d'attente** : Redis
* **Frontend** : Vue 3 + Vite.js (Compilation native via `@vitejs/plugin-vue`)

### 1. Pré-requis
- PHP 8.3 ou supérieur (avec extensions PDO, mbstring, xml, curl, sqlite).
- Composer 2.x
- Node.js 18+ et NPM
- PostgreSQL 14+ ou Docker
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
touch database/database.sqlite
```

Le profil par défaut permet un démarrage local rapide en SQLite. Si vous souhaitez utiliser PostgreSQL et Redis, adaptez votre `.env` comme suit :
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
php artisan migrate --seed
```

### 4. Démarrage des Services de Développement

Pour faire tourner le projet en mode développement :
```bash
composer dev
```

L'application est maintenant accessible sur `http://localhost:8000`.

---

## 🔐 Accès & Administration

Dans la version V1, un compte administrateur de démonstration est injecté par le seeder :
```text
admin@dazo.test / password
```

**Accès au panel:**
Les endpoints API d'administration (`/api/v1/admin/*`) seront immédiatement débloqués pour votre compte, vous permettant de configurer l'instance (via `ConfigService`). Les composants côté Vue afficheront des contrôles étendus si les permissions correspondent à ce rôle de session.

## 📖 Utilisation Rapide (Scénario Métier)

1. **Création d'un Cercle :** En tant qu'utilisateur vous formez un "Cercle" (Espace de la communauté) et vous invitez d'autres utilisateurs via des liens d'invitation.
2. **Rédaction :** N'importe quel membre lance une `Decision` dans ce contexte avec le modèle souhaité. Elle démarre en phase de brouillon (`Draft`).
3. **Clarification :** L'auteur pousse la décision en clarification. Le composant *Thread* s'ouvre, autorisant les réactions.
4. **Objections :** L'animateur déclare la phase d'Objection. Le front-end présente l'interface `FeedbackEngine` où chaque utilisateur déclare s'il a une objection bloquante.
5. **Adoption :** Si les objections sont résolues (*withdraw*) et que personne ne soutient des blocages stricts, l'adoption passe au vert.

---

## 🧪 Exécuter la Suite de Tests Automatisés

DAZO comprend des tests critiques pour auditer les règles de validation du modèle. La suite utilise SQLite en mémoire pour rester autonome en local.
```bash
# Lancer toute la suite (Feature et Unit)
php artisan test
```
