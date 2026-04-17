# Architecture

## 🧠 Vue d'ensemble

DAZO suit une **architecture Laravel en couches** :

```
Request → Controller → Service → Model → Response
               ↓
            Policy (authorization)
               ↓
            Event → Listener → Job (async)
```

---

## 📂 Structure `/app`

```
app/
├── Enums/                    # PHP 8.1+ Enums (source de vérité des valeurs)
│   ├── DecisionStatus.php
│   ├── FeedbackType.php
│   ├── FeedbackStatus.php
│   ├── ConsentSignal.php
│   ├── NotificationCategory.php
│   ├── NotificationEventType.php
│   └── ...
│
├── Models/                   # Eloquent Models — accès données uniquement
│   ├── User.php
│   ├── Circle.php
│   ├── CircleMember.php
│   ├── Decision.php
│   ├── DecisionVersion.php
│   ├── DecisionParticipant.php
│   ├── DecisionRelation.php
│   ├── DecisionAnimatorLog.php
│   ├── Consent.php
│   ├── Feedback.php
│   ├── FeedbackJoin.php
│   ├── FeedbackMessage.php
│   ├── ThreadMessage.php
│   └── ...
│
├── States/                   # Machine d'états (spatie/laravel-model-states)
│   └── Decision/
│       ├── DecisionState.php
│       ├── DraftState.php
│       ├── ClarificationState.php
│       └── ...
│
├── Transitions/              # Transitions de la machine d'états
│   └── Decision/
│       ├── TransitionToAdopted.php
│       └── ...
│
├── Services/                 # Logique métier — toutes les règles ici
│   ├── DecisionService.php   # Création, transitions, versionnage
│   ├── FeedbackService.php   # Soumission, résolution, adoption check
│   ├── ConsentService.php    # Gestion des signaux de consentement
│   ├── CircleService.php     # Adhésion, rôles, sous-cercles
│   ├── NotificationService.php # Dispatch des notifications en base + email
│   └── ConfigService.php     # Lecture de INSTANCE_CONFIG (cache Redis)
│
├── Http/
│   └── Controllers/Api/V1/   # Controllers HTTP — thin, délèguent aux Services
│       ├── AuthController.php
│       ├── CircleController.php
│       ├── DecisionController.php
│       ├── FeedbackController.php
│       ├── ConsentController.php
│       └── ...
│
├── Http/
│   └── Requests/             # Form Requests — validation des entrées
│       ├── StoreDecisionRequest.php
│       ├── StoreFeedbackRequest.php
│       └── ...
│
├── Policies/                 # Autorisation (Gates)
│   ├── DecisionPolicy.php
│   ├── FeedbackPolicy.php
│   ├── CirclePolicy.php
│   └── ...
│
├── Events/                   # Contrats d'événements (définis en V1, listeners en V1.5)
│   ├── DecisionCreated.php
│   ├── DecisionTransitioned.php
│   ├── DecisionAdopted.php
│   ├── DecisionAdoptedWithOverride.php
│   ├── DecisionRevisionStarted.php
│   ├── DecisionAbandoned.php
│   ├── DecisionLapsed.php
│   ├── DecisionDeserted.php
│   ├── FeedbackSubmitted.php
│   ├── FeedbackResolved.php
│   └── DecisionAnimatorChanged.php
│
├── Listeners/                # Listeners (branchés sur les Events)
│   └── SendNotification.php  # Dispatch vers NotificationService
│
└── Jobs/                     # Tâches asynchrones
    ├── CheckDecisionDeadlines.php  # Cron — détecte lapsed/deserted
    └── SendEmailNotification.php
```

---

## ⚙️ Principes

- **Thin controllers** : aucune logique métier dans les controllers
- **Services** : toutes les règles métier, toujours injectés par le constructeur
- **Models** : accès données uniquement — scopes Eloquent autorisés, pas de règles métier
- **Policies** : toute autorisation passe par une Policy, jamais dans les controllers
- **Enums** : source de vérité unique — jamais de chaînes littérales dans le code

---

## 🔐 Authentification

- **Laravel Sanctum** — tokens API stateless pour SPA et mobile
- Email + mot de passe — inscription classique + vérification email
- Expiration des tokens configurable via `INSTANCE_CONFIG.session_lifetime_days`
- **OAuth (Socialite)** — reporté en V2 (Google, GitHub, Decidim)

---

## 🧩 Services clés

### `DecisionService`
Création de décisions, gestion des transitions d'états (via `spatie/laravel-model-states`),
création de nouvelles versions, association d'animateur.

### `FeedbackService`
Soumission de feedbacks, résolution, vérification de la condition d'adoption
(`hasNoBlockingObjections()`), gestion des `FEEDBACK_JOIN`.

### `ConsentService`
Enregistrement des signaux `CONSENT` (`no_objection`, `abstention`) par version.

### `CircleService`
Gestion des adhésions, invitations, rôles, sous-cercles.

### `NotificationService`
Enregistrement des notifications en base, dispatch email (Jobs), résolution des préférences
(`decision > circle > global`).

### `ConfigService`
Lecture des clés `INSTANCE_CONFIG` avec cache Redis.
**Aucune lecture directe en base depuis les autres services.**

```php
// Usage :
$days = ConfigService::get('reminder_no_deadline_days', default: 30);
```

---

## 🔄 Machine d'états

- Bibliothèque : **`spatie/laravel-model-states`**
- Voir [state-machine.md](./state-machine.md) pour la table de transitions complète

---

## 📡 Events — Contrats V1

Les events sont définis en V1 **même si les listeners sont vides**.
Cela garantit un couplage faible entre les services dès le début.
Les listeners `NotificationService` sont branchés en V1.5.

Voir la liste complète dans `app/Events/`.

---

## 🚀 Scalabilité

- La couche Service garantit l'extensibilité sans toucher aux controllers
- Les Jobs Redis permettent l'async (emails, cron deadlines)
- `ConfigService` + Redis : pas de requête SQL à chaque accès config
- La séparation Events/Listeners prépare une future architecture event-driven

---

## 🌍 Évolution SaaS (V3)

- Multi-tenancy : isolation par `tenant_id` à ajouter sur les entités principales
- La couche Service facilite cette migration (pas de logique dispersée)
