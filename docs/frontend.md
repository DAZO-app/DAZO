# Documentation Front-End (SPA Vue 3)

Le front-end de DAZO est une Single Page Application (SPA) bâtie avec Vue 3, intégrée de manière native à notre backend Laravel via Vite.js. Elle est pensée pour être fortement réactive, mobile-first, et ne dépend d'aucun framework CSS utilitaire strict (CSS Vanilla structuré).

## Stack Technique

| Outil | Version | Rôle |
|---|---|---|
| **Vue 3** | ^3.5 | Framework principal (Composition API + `<script setup>`) |
| **Vite.js** | ^8.0 | Bundler + HMR (plugin `@vitejs/plugin-vue`) |
| **Vue Router** | ^5.0 | Routage SPA (History API) |
| **Pinia** | ^3.0 | Store global réactif et persistant |
| **Axios** | ^1.15 | Client HTTP (avec Sanctum CSRF + Interceptors) |
| **Laravel Echo** | ^2.3 | Client WebSocket (temps réel via Reverb) |
| **Pusher-js** | ^8.5 | Adaptateur WebSocket pour Echo |
| **Quill 2** | CDN | Éditeur de texte riche (décisions) |
| **PhotoSwipe** | ^5.4 | Galerie d'images (pièces jointes) |
| **Font Awesome** | ^7.2 | Icônes |

## Architecture des Dossiers (`resources/js/`)

```
resources/js/
├── bootstrap.js         # Configuration Axios + initialisation Echo/Reverb
├── app.js               # Point d'entrée Vue, Pinia, Router
├── router/
│   └── index.js         # Configuration des routes (guards d'authentification)
├── stores/
│   ├── auth.js          # Store d'authentification (Sanctum, impersonation)
│   ├── decision.js      # Store du moteur de décision (getters: isAuthor, isAnimator,
│   │                    #   participationPercent, isFavorite, isActivePhase, isTerminal...)
│   ├── pending.js       # Compteurs d'actions en attente (Echo WebSocket + fallback polling)
│   └── config.js        # Configuration de l'instance (logo, nom, etc.)
├── layouts/
│   └── AppLayout.vue    # Coquille globale avec Sidebar / BottomNav mobile
├── views/
│   ├── Dashboard.vue        # Indicateurs, timeline, blocs "en attente"
│   ├── DecisionList.vue     # Liste filtrée + recherche des décisions
│   ├── DecisionDetail.vue   # Moteur de décision interactif (vue principale)
│   ├── DecisionCreate.vue   # Formulaire de création
│   ├── Settings.vue         # Paramètres utilisateur
│   ├── admin/
│   │   ├── AdminDashboard.vue   # Tableau de bord admin
│   │   ├── AdminUsers.vue       # Gestion des utilisateurs
│   │   ├── AdminCircles.vue     # Gestion des cercles
│   │   ├── AdminConfig.vue      # Configuration de l'instance
│   │   └── AdminDatabase.vue    # Outils base de données + monitoring
│   └── wiki/
│       ├── WikiIndex.vue    # Portail de recherche aide
│       ├── WikiDetail.vue   # Article aide détaillé
│       ├── AdminWiki.vue    # Gestion CRUD wiki
│       └── WikiEditor.vue   # Éditeur riche d'articles
└── components/
    ├── AttachmentPanel.vue      # Upload + aperçu des pièces jointes
    ├── FeedbackEngine.vue       # Threads d'objections, clarifications, suggestions
    ├── ParticipantPhasePanel.vue # Progression des phases + liste participants
    ├── ImpersonationBanner.vue  # Bandeau mode impersonation
    └── EmptyState.vue           # États vides génériques
```

## Structure CSS (`resources/css/dazo-theme.css`)

1. **Variables racinaires (`:root`)** : Couleurs HSL, polices, espacements, rayons
2. **Design Fluide et Mobile-First** : Media queries pour adaptation Desktop
3. **Composants structurels** : Classes génériques (`.btn`, `.card`, `.badge`, `.hero-card`)
4. **Codes couleur par rôle** :
   - 🔵 Bleu — Porteur (auteur de la décision)
   - 🟠 Orange — Animateur
   - 🟢 Vert — Membres participants
   - 🔴 Indicateur clignotant — Action requise (voyant d'urgence)

## Flux de Données

```
AppLayout.vue (onMounted)
  ├── authStore.fetchUser()       → GET /api/v1/auth/me
  ├── decisionStore.fetchDecisions() → GET /api/v1/decisions
  ├── configStore.fetchConfig()   → GET /api/v1/public/config
  └── pendingStore.startEcho(userId)  → Echo WebSocket (fallback: polling 60s)
```

## Temps Réel (Laravel Echo + Reverb)

Le store `pending.js` s'abonne au canal privé de l'utilisateur dès la connexion :

```js
// bootstrap.js — initialisation Echo
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    // ...
});

// pending.js — écoute des événements
const startEcho = (userId) => {
    window.Echo.private(`App.Models.User.${userId}`)
        .listen('.decision.transitioned', () => fetch())
        .listen('.feedback.submitted', () => fetch())
        .listen('.consent.submitted', () => fetch());
};
```

**Fallback** : si Reverb n'est pas disponible (env dev sans `php artisan reverb:start`),
le store bascule automatiquement sur un polling HTTP toutes les 60 secondes.

## Sécurité & Authentification (Laravel Sanctum)

- Axios est configuré avec `withCredentials: true` pour transmettre les cookies Sanctum
- CSRF token récupéré automatiquement via `GET /sanctum/csrf-cookie` avant chaque mutation
- Les routes privées Vue Router vérifient `authStore.isAuthenticated` via un guard `beforeEach`
- La réponse 401 de l'API est interceptée par Axios pour rediriger vers `/login`

## Stores Pinia — Référence

### `auth.js`
- `user`, `isAuthenticated`, `isImpersonating`
- `login()`, `logout()`, `fetchUser()`, `impersonate(userId)`, `stopImpersonating()`

### `decision.js`
Getters disponibles :
| Getter | Description |
|---|---|
| `isAuthor(userId)` | L'utilisateur est porteur de la décision courante |
| `isAnimator(userId)` | L'utilisateur est animateur de la décision courante |
| `isAuthorOrAnimator(userId)` | Porteur ou animateur |
| `currentStatus` | Statut courant (`draft`, `clarification`, etc.) |
| `isActivePhase` | Phase active (clarification/réaction/objection) |
| `isTerminal` | Phase terminale (adopté/abandonné/etc.) |
| `isFavorite` | Décision marquée en favori |
| `notificationLevel` | Niveau de notification (`relevant`, `all`, `none`) |
| `eligibleCount` | Nombre de participants éligibles |
| `participatedCount` | Nombre ayant participé |
| `participationPercent` | Taux de participation (0-100) |

Actions : `fetchDecisions()`, `fetchDecisionById(id)`, `clearCurrent()`, `toggleFavorite(id)` (avec optimistic UI).

### `pending.js`
- `counts` : `{ clarifications, reactions, objections }`
- `startEcho(userId)` : écoute WebSocket (mode principal)
- `startPolling(intervalMs)` : polling HTTP (fallback)
- `stop()` : nettoyage (écoute + polling)

### `config.js`
- `appName`, `appLogo`, `maintenanceMode`
- `fetchConfig()` depuis `/api/v1/public/config`

## Commandes de développement

```bash
# Frontend (HMR) + Backend + Reverb
npm run dev                # Assets Vite avec HMR
php artisan serve          # Serveur Laravel
php artisan reverb:start   # WebSocket temps réel (optionnel)
php artisan queue:work     # Worker pour les emails async
```
