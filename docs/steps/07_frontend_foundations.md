# Étape 7 : Fondations Front-End (SPA Vue 3)

> **Statut** : Complété ✅ (Couvre les Blocs 10 et 11 de la nouvelle Roadmap Front)
> **Objectif** : Brancher un client riche SPA interactif (Vue 3) pour remplacer les mock-ups statiques, configurer le routage sécurisé et déployer l'architecture CSS unifiée.

---

## 🛠 Ce qui a été implémenté

### 1. Architecture SPA & Outils de Build
- **Initialisation Vue / Vite** : Le front est embarqué directement à la racine du projet Laravel pour maximiser la sécurité (pas de problème CORS, tokens sécurisés gérés via Sanctum par cookies first-party). Le `@vitejs/plugin-vue` a été branché sur `vite.config.js`.
- **Axios & Sanctum** : Configuration d'un bootstrap centralisé (`bootstrap.js`) signalant via `X-Requested-With` que toutes les communications de Vue se protègent du CSRF via les mécanismes de session par défaut de l'API.
- **Pinia State Management** : Ajout d'une gestion locale d'état via `stores/auth.js`. Ce store capte l'authentification (login), et stocke les informations de `"Me"` (le user actuellement logué) pour alimenter tous les autres composants.

### 2. Design System & CSS
- **Abstraction Vanilla CSS** : Extraction de l'intégralité du design du mock `dazo-ui.html` vers `resources/css/dazo-theme.css`. Nous n'utilisons **pas** Tailwind pour garantir une indépendance et un contrôle absolu du DOM.
- **Le Mobile-First** : La navigation globale (`AppLayout.vue`) a été repensée pour être adaptative :
  - Sur PC : Maintien de l'imposante Sidebar traditionnelle sur le côté gauche.
  - Sur Mobile : Bascule automatique vers une **Bottom Navigation Bar** ancrée en bas d'écran façon "Application Native iOS/Android" limitant l'encombrement horizontal.

### 3. Vues et Routage (`vue-router`)
- `routes/web.php` pointe désormais n'importe quelle URL (Catch-All) sur `welcome.blade.php`.
- **`Login.vue`** : Écran d'accueil de l'application qui attaque l'endpoint REST `/api/v1/auth/login`.
- **`AppLayout.vue`** : Composant central (shell applicatif) de sécurisation. Seuls les utilisateurs dont le state `authStore.isAuthenticated` est validé peuvent y accéder.
- **`Dashboard.vue`** : Base d'accueil reprenant les métriques statiques prêtes à accueillir les requêtes d'activité.

> **Suite naturelle** : Migration des vues "Mockups complexes" (Liste des Décisions, Focus de phase, Feedbacks) vers des SFC robustes.
