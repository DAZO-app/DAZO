# DAZO V1 — Roadmap Features Détaillée

> Ordre logique d'implémentation : chaque bloc dépend du précédent.
> Statut : ⬜ À faire · 🔵 En cours · ✅ Terminé

---

## Bloc 0 — Fondations techniques

> Prérequis absolus avant toute fonctionnalité métier.

### 0.1 — Setup Laravel
- ⬜ Initialisation projet Laravel 11
- ⬜ Configuration PostgreSQL (UUID comme clé primaire par défaut)
- ⬜ Configuration Redis (cache + queues)
- ⬜ Configuration Docker (docker-compose : app, postgres, redis)
- ⬜ Variables d'environnement (`.env.example` documenté)
- ⬜ Configuration Sanctum (stateful domains, token TTL)

### 0.2 — PHP Enums
- ⬜ `UserRole`
- ⬜ `CircleType`, `CircleMemberRole`
- ⬜ `DecisionStatus`, `DecisionVisibility`, `DecisionParticipantRole`, `DecisionRelationType`
- ⬜ `ConsentSignal`
- ⬜ `FeedbackType`, `FeedbackStatus`
- ⬜ `ThreadTour`
- ⬜ `NotificationCategory`, `NotificationEventType`
- ⬜ `ConfigValueType`, `HelpTextLevel`

### 0.3 — Migrations (dans l'ordre des dépendances)
- ⬜ `users`
- ⬜ `oauth_providers`
- ⬜ `circles`
- ⬜ `circle_members`
- ⬜ `categories`
- ⬜ `labels`
- ⬜ `decision_models`
- ⬜ `help_texts`
- ⬜ `decisions`
- ⬜ `decision_versions`
- ⬜ `decision_participants` (contraintes unicité author/animator)
- ⬜ `decision_relations`
- ⬜ `decision_animator_logs`
- ⬜ `consents` (contrainte UNIQUE decision_version_id + user_id)
- ⬜ `feedbacks`
- ⬜ `feedback_joins` (contrainte UNIQUE feedback_id + user_id)
- ⬜ `feedback_messages`
- ⬜ `thread_messages`
- ⬜ `attachments`
- ⬜ `decision_labels` (pivot)
- ⬜ `notifications`
- ⬜ `notification_preferences`
- ⬜ `invitations`
- ⬜ `instance_config`
- ⬜ `app_logs`

### 0.4 — Modèles Eloquent
- ⬜ Modèles pour chaque table (casts Enum, relations, scopes)
- ⬜ Scope `currentVersion()` sur `Decision`
- ⬜ Scope `currentAnimator()` et `author()` sur `Decision` (via `DecisionParticipant`)
- ⬜ Soft delete sur `User`

### 0.5 — Contrats d'Events (listeners vides)
- ⬜ `DecisionCreated`
- ⬜ `DecisionTransitioned`
- ⬜ `DecisionAdopted`
- ⬜ `DecisionAdoptedWithOverride`
- ⬜ `DecisionRevisionStarted`
- ⬜ `DecisionAbandoned`
- ⬜ `DecisionLapsed`
- ⬜ `DecisionDeserted`
- ⬜ `FeedbackSubmitted`
- ⬜ `FeedbackResolved`
- ⬜ `DecisionAnimatorChanged`
- ⬜ `ParticipationReminderNeeded`

---

## Bloc 1 — Authentification & Utilisateurs

### 1.1 — Inscription / Connexion
- ⬜ `POST /api/v1/auth/register` — inscription email/password
- ⬜ `POST /api/v1/auth/login` — connexion Sanctum token
- ⬜ `POST /api/v1/auth/logout` — révocation token
- ⬜ `GET /api/v1/auth/me` — profil courant
- ⬜ Vérification email (mail + token)
- ⬜ `AuthPolicy` : vérifier `is_active` et `email_verified_at`

### 1.2 — OAuth (Socialite)
- ⬜ `GET /api/v1/auth/{provider}/redirect`
- ⬜ `GET /api/v1/auth/{provider}/callback`
- ⬜ Providers : Google, GitHub (Decidim si nécessaire)
- ⬜ Création `OauthProvider` au premier login OAuth

### 1.3 — Profil utilisateur
- ⬜ `GET /api/v1/auth/me` — lecture profil complet
- ⬜ `PUT /api/v1/auth/me` — modification nom, avatar

---

## Bloc 2 — Gestion des Cercles

### 2.1 — CRUD Cercles
- ⬜ `POST /api/v1/circles` — créer un cercle (admin ou user selon config)
- ⬜ `GET /api/v1/circles` — liste des cercles accessibles
- ⬜ `GET /api/v1/circles/{id}` — détail
- ⬜ `PUT /api/v1/circles/{id}` — modifier (animateur du cercle)
- ⬜ `CircleService` : logique de création, type open/closed/observer_open
- ⬜ `CirclePolicy` : droits par rôle

### 2.2 — Adhésion
- ⬜ `POST /api/v1/circles/{id}/join` — rejoindre un cercle open
- ⬜ `POST /api/v1/circles/{id}/leave` — quitter (interdit si auteur d'une décision active)
- ⬜ `GET /api/v1/circles/{id}/members` — liste des membres
- ⬜ `DELETE /api/v1/circles/{id}/members/{userId}` — exclure (animateur uniquement)

### 2.3 — Invitations vers un cercle
- ⬜ `POST /api/v1/invitations` — envoyer une invitation (`circle_id`, rôle cible)
- ⬜ `POST /api/v1/invitations/{token}/accept` — accepter l'invitation
- ⬜ Création automatique de `CircleMember` à l'acceptation
- ⬜ TTL sur invitation (`expires_at`)

---

## Bloc 3 — Création & Gestion d'une Décision

### 3.1 — Création
- ⬜ `POST /api/v1/decisions` — créer une décision (status = draft)
- ⬜ `DecisionService::create()` : crée `Decision` + première `DecisionVersion` (is_current = true) + `DecisionParticipant` (role = author)
- ⬜ `GET /api/v1/decisions` — liste (filtrée par cercle, visibilité, status)
- ⬜ `GET /api/v1/decisions/{id}` — détail avec version courante

### 3.2 — Métadonnées
- ⬜ `PUT /api/v1/decisions/{id}` — modifier titre, priorité, deadline, visibility (draft uniquement)
- ⬜ Labels : `POST /api/v1/decisions/{id}/labels`, `DELETE /api/v1/decisions/{id}/labels/{labelId}`
- ⬜ Catégorie : champ sur `PUT /api/v1/decisions/{id}`

### 3.3 — Gestion de l'animateur
- ⬜ `PUT /api/v1/decisions/{id}/animator` — désigner/changer l'animateur
- ⬜ `DecisionService::changeAnimator()` : met à jour `DecisionParticipant` (role=animator) + ajoute une entrée dans `DecisionAnimatorLog`
- ⬜ `GET /api/v1/decisions/{id}/animator/history` — historique
- ⬜ Notification `DecisionAnimatorChanged` → animateur entrant

### 3.4 — Participants
- ⬜ `GET /api/v1/decisions/{id}/participants` — liste des participants
- ⬜ Tous les membres du cercle peuvent être inscrits comme participant
- ⬜ `DecisionParticipant` créé automatiquement au premier acte de participation

---

## Bloc 4 — Machine d'États & Cycle de Vie

### 4.1 — Installation et configuration spatie
- ⬜ `composer require spatie/laravel-model-states`
- ⬜ Définition des 10 états (`DraftState`, `ClarificationState`, …)
- ⬜ Définition des transitions autorisées
- ⬜ Dispatch des Events à chaque transition

### 4.2 — Transitions manuelles
- ⬜ `POST /api/v1/decisions/{id}/transition` — body : `{ "to": "clarification" }`
- ⬜ `DecisionService::transition()` : vérifie l'acteur, délègue à spatie
- ⬜ `POST /api/v1/decisions/{id}/abandon` — abandon explicite (auteur ou animateur)

### 4.3 — Transitions automatiques (Jobs)
- ⬜ `CheckDecisionDeadlines` — cron horaire : détecte `lapsed`
- ⬜ `CheckDecisionInactivity` — cron quotidien : détecte `deserted`
- ⬜ Enregistrement dans `AppLog` à chaque transition automatique

---

## Bloc 5 — Thread (Clarification & Réaction)

### 5.1 — Messages de thread
- ⬜ `GET /api/v1/decisions/{id}/thread` — liste (filtrable par `tour`)
- ⬜ `POST /api/v1/decisions/{id}/thread` — poster un message
- ⬜ Autorisation : phase correspondante active (`clarification` ou `reaction`)
- ⬜ `is_moderator_note` : réservé à l'animateur
- ⬜ Observer : lecture seule — ne peut pas poster

---

## Bloc 6 — Feedback (Phase Objection)

### 6.1 — Soumission de feedback
- ⬜ `POST /api/v1/decisions/{id}/feedback` — soumettre un feedback
- ⬜ Autorisation : phase `objection` active, membre du cercle (pas observer, pas auteur)
- ⬜ Règle exclusive : vérifier qu'aucun `Consent` ni `FeedbackJoin` n'existe pour cet user sur cette version — et vice versa
- ⬜ `FeedbackService::submit()` : crée le feedback + `DecisionParticipant` si premier acte

### 6.2 — Consultation
- ⬜ `GET /api/v1/decisions/{id}/feedback` — liste (filtrée par type, status, version)
- ⬜ `GET /api/v1/decisions/{id}/feedback/{feedbackId}` — détail

### 6.3 — Gestion du statut d'un feedback
- ⬜ `PUT /api/v1/decisions/{id}/feedback/{feedbackId}/status` — changer le statut
- ⬜ Acteurs selon les règles enums (objection → auteur feedback ; suggestion → auteur décision / animateur)
- ⬜ `FeedbackService::changeStatus()` : vérifie la machine d'états feedback

### 6.4 — Fil de discussion sur un feedback
- ⬜ `GET /api/v1/feedback/{feedbackId}/messages` — liste des messages
- ⬜ `POST /api/v1/feedback/{feedbackId}/messages` — poster un message
- ⬜ Autorisation : auteur feedback, auteur décision, animateur uniquement

### 6.5 — Soutien (FEEDBACK_JOIN)
- ⬜ `POST /api/v1/feedback/{feedbackId}/join` — rejoindre un feedback
- ⬜ Règle exclusive : vérifier qu'aucun `Feedback` ni `Consent` n'existe pour cet user sur cette version
- ⬜ Action non réversible

---

## Bloc 7 — Consentement (Phase Objection)

### 7.1 — Signal de consentement
- ⬜ `POST /api/v1/decisions/{id}/versions/{versionId}/consent` — exprimer `no_objection` ou `abstention`
- ⬜ Règle exclusive : vérifier qu'aucun `Feedback` ni `FeedbackJoin` n'existe
- ⬜ Action non réversible
- ⬜ `ConsentService::express()` : crée l'entrée `Consent`

### 7.2 — Consultation
- ⬜ `GET /api/v1/decisions/{id}/versions/{versionId}/consent` — résumé (no_objection, abstention, non exprimé)

---

## Bloc 8 — Adoption

### 8.1 — Vérification de la condition d'adoption
- ⬜ `FeedbackService::hasNoBlockingObjections(DecisionVersion $version): bool`
- ⬜ Déclenchement automatique si résultat `true` (transition → `adopted` + event `DecisionAdopted`)
- ⬜ Déclenché à chaque changement de statut d'un feedback

### 8.2 — Adoption manuelle / override
- ⬜ `POST /api/v1/decisions/{id}/transition` avec `{ "to": "adopted" }` (si 0 objection)
- ⬜ `POST /api/v1/decisions/{id}/transition` avec `{ "to": "adopted_override" }` (avec objections — auteur ou animateur)
- ⬜ Affichage distinct de `adopted_override` dans les réponses API (flag `is_override: true`)

---

## Bloc 9 — Révision (Nouvelle Version)

### 9.1 — Création d'une nouvelle version
- ⬜ `POST /api/v1/decisions/{id}/versions` — corps + raison du changement
- ⬜ `DecisionService::createNewVersion()` :
  - Crée `DecisionVersion` (version_number++, is_current = true)
  - Met l'ancienne version `is_current = false`
  - Transition `revision → clarification`
  - Dispatch `DecisionRevisionStarted`

### 9.2 — Consultation des versions
- ⬜ `GET /api/v1/decisions/{id}/versions` — historique complet
- ⬜ `GET /api/v1/decisions/{id}/versions/{versionId}` — détail d'une version + feedbacks liés

---

## Bloc 10 — Notifications

### 10.1 — Stockage en base
- ⬜ `NotificationService::notify(User $user, NotificationEventType $event, array $payload)`
- ⬜ Listeners branchés sur chaque Event V1 → appel `NotificationService`
- ⬜ `GET /api/v1/notifications` — liste des notifications
- ⬜ `POST /api/v1/notifications/{id}/read`, `POST /api/v1/notifications/read-all`

### 10.2 — Envoi email (basique)
- ⬜ `SendEmailNotification` Job (queue) — email simple sans template sophistiqué
- ⬜ Respect des `NotificationPreference` (email_enabled par catégorie)

### 10.3 — Relances de participation
- ⬜ `ParticipationReminderNeeded` event → notify les membres sans action sur version courante
- ⬜ Déclenchée par auteur/animateur (`POST /api/v1/decisions/{id}/remind`) ou automatiquement

---

## Bloc 11 — Audit Log

- ⬜ Middleware `LogRequest` : log automatique des actions significatives dans `AppLog`
- ⬜ Actions loggées : transitions, feedback soumis/résolu, changement animateur, adoption
- ⬜ Purge automatique après 1 an (Job quotidien `PurgeOldLogs`)

---

## Bloc 12 — Administration

### 12.1 — Configuration instance
- ⬜ `ConfigService` : lecture avec cache Redis, invalidation à chaque écriture
- ⬜ `GET /api/v1/admin/config` — lire toutes les clés
- ⬜ `PUT /api/v1/admin/config/{key}` — modifier (admin uniquement)
- ⬜ Seed des valeurs par défaut `INSTANCE_CONFIG`

### 12.2 — Catégories & Labels
- ⬜ CRUD `/api/v1/admin/categories` (admin)
- ⬜ CRUD `/api/v1/admin/labels` (admin)

### 12.3 — Modèles de décision
- ⬜ CRUD `/api/v1/admin/models` (admin)
- ⬜ Logique `requires_distinct_animator` → notification aux membres du cercle

---

## Bloc 13 — Tests

### 13.1 — Tests unitaires
- ⬜ `FeedbackService::hasNoBlockingObjections()` — cas nominaux + cas limites
- ⬜ Machine d'états : toutes les transitions autorisées et refusées
- ⬜ Règle exclusive CONSENT/FEEDBACK/FEEDBACK_JOIN
- ⬜ `ConfigService` : cache, invalidation

### 13.2 — Tests de feature (HTTP)
- ⬜ Auth : register, login, logout, OAuth
- ⬜ Cercles : CRUD, adhésion, rôles
- ⬜ Cycle décision : draft → clarification → reaction → objection → adopted
- ⬜ Cycle avec révision : objection → revision → clarification → ... → adopted
- ⬜ Adoption override
- ⬜ Lapsed / deserted (Jobs)
- ⬜ Permissions (observer ne peut pas poster, auteur ne participe pas en objection)

---

## Estimations (informatives)

| Bloc | Complexité | Estimation |
|---|---|---|
| 0 — Fondations | Moyenne | 2–3 jours |
| 1 — Auth | Faible | 1–2 jours |
| 2 — Cercles | Faible | 1–2 jours |
| 3 — Décision CRUD | Faible | 1 jour |
| 4 — Machine d'états | **Élevée** | 2–3 jours |
| 5 — Thread | Faible | 0,5 jour |
| 6 — Feedback | Moyenne | 2 jours |
| 7 — Consent | Faible | 0,5 jour |
| 8 — Adoption | Moyenne | 1 jour |
| 9 — Révision | Moyenne | 1 jour |
| 10 — Notifications | Moyenne | 2 jours |
| 11 — Audit log | Faible | 0,5 jour |
| 12 — Admin | Faible | 1 jour |
| 13 — Tests | **Élevée** | 3–4 jours |
| **Total V1** | | **~18–23 jours** |
