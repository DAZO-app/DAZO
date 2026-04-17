# Étape 0 : Fondations Techniques

> **Statut** : Complété ✅
> **Objectif** : Initialisation du socle technique Laravel, modélisation de la base de données, infrastructure Docker et configuration initiale, selon le [MCD Core](../mcd/dazo_mcd_core.puml) et [Périphérique](../mcd/dazo_mcd_peripherique.puml).

---

## 🛠 Ce qui a été implémenté

### 1. Initialisation du projet Laravel
- Installation d'un projet **Laravel 11** "from scratch".
- Fichiers d'environnement (`.env` et `.env.example`) documentés et paramétrés.
- Préparation de l'architecture **Docker** via *Laravel Sail* (fichiers `docker-compose.yml`) avec les conteneurs :
  - `laravel.test` (App PHP 8.3)
  - `pgsql` (Base de données PostgreSQL 15)
  - `redis` (Cache et file d'attente système)

### 2. Configuration Authentification / API
- Installation de **Laravel Sanctum**.
- Ajout du trait `HasApiTokens` au modèle utilisateur courant.
- Préparation des domaines "stateful" (via la clé `SANCTUM_STATEFUL_DOMAINS` dans l'`.env`).

### 3. Enums PHP (Typage strict)
Implémentation de tous les types `enum` garantissant un contrôle strict des valeurs de l'application selon la documentation MCD (dans `app/Enums/*`) :
- `UserRole`, `CircleType`, `CircleMemberRole`, `DecisionStatus`, `DecisionVisibility`
- `DecisionParticipantRole`, `DecisionRelationType`, `ConsentSignal`
- `FeedbackType`, `FeedbackStatus`, `ThreadTour`, `NotificationCategory`
- `NotificationEventType`, `ConfigValueType`, `HelpTextLevel`, `InvitationRole`

### 4. Base de Données (Migrations)
Les **23 tables du projet** ont été générées dans des fichiers de migration individuels.
**Points d'attention mis en place :**
- L'utilisation formelle des **UUIDs** comme clés primaires partout (`$table->uuid('id')->primary()`, `$table->foreignUuid(...)`).
- Ajout du support de soft dététion (`SoftDeletes`) sur les entités majeures nécessitant de la traçabilité sans destruction de données (`users`, `circles`, `categories`, `decision_models`).
- Création exhaustive des tables périphériques (invitations, logs d'application, configurations d'instance, notifications).
- Définition stricte des index d'unicité (ex. `FeedbackJoins`, `Consents`).

### 5. Modèles Eloquent (Relations fondamentales)
Génération de **22 Modèles** dans `app/Models/` qui incluent par défaut :
- L'utilisation du trait `HasUuids` et `SoftDeletes` là où ils ont été demandés.
- La protection des attributs massifs et le casting précis (`casts()`) des propriétés vers les enums propres générés.
- Les relations relationnelles Eloquent (`belongsTo`, `hasMany`, `belongsToMany`, etc.) parfaitement alignées avec la cardinalité du PlantUML de conception.
- La création de cas très précis (ex: `currentVersion`, `currentAnimator` sur `Decision`).

### 6. Contrats d'Événements (Event Dispatcher)
Pour intégrer la logique de détection des notifications, les `Events` du système de prise de décision DAZO ont été générés dans `app/Events/` :
- Création des décisions, transitions, adoptions, ou abandons de cycle.
- Traitement ou soumission des réactions (feedback) et invocation matérielle d'animateurs ou de rappels de participation.

---

> **Prochaine étape correspondante** : [Bloc 1 - Authentification & Utilisateurs](./01_authentification_utilisateurs.md) *(Prévue)*
