# Documentation Front-End (SPA Vue 3)

Le front-end de DAZO est une Single Page Application (SPA) bâtie avec Vue 3, intégrée de manière native à notre backend Laravel via Vite.js. Elle est pensée pour être fortement réactive, mobile-first, et ne dépend d'aucun framework CSS utilitaire strict (CSS Vanilla structuré).

## Stack Technique
* **Framework principal** : Vue 3 (Composition API avec `<script setup>`)
* **Bundler** : Vite.js (intégré via `@vitejs/plugin-vue`)
* **Routage** : Vue Router 4 (History API)
* **Store global** : Pinia (État réactif et persistant)
* **Client HTTP** : Axios (Configuré avec Sanctum CSRF / Interceptors)
* **Styling** : CSS Vanilla (Design system importé de `dazo-theme.css`)
* **Édition riche** : Quill 2 chargé côté client pour la rédaction des décisions

## Architecture des Dossiers (`resources/js/`)
```
resources/js/
├── bootstrap.js         # Configuration d'Axios et des headers
├── app.js               # Point d'entrée Vue, Pinia, Router
├── router/
│   └── index.js         # Configuration des routes
├── stores/
│   ├── auth.js          # Store d'authentification (Sanctum)
│   └── decision.js      # Store du moteur de décision
├── layouts/
│   └── AppLayout.vue    # Coquille globale avec Sidebar / BottomNav mobile
├── views/
│   ├── ...
│   ├── Dashboard.vue    # Indicateurs et timeline
│   ├── DecisionList.vue # Liste des décisions
│   ├── DecisionDetail.vue # Moteur de décision interactif
│   └── wiki/
│       ├── WikiIndex.vue    # Portail de recherche help
│       ├── WikiDetail.vue   # Article help détaillé (2 colonnes)
│       ├── AdminWiki.vue    # Table de gestion CRUD wiki
│       └── WikiEditor.vue   # Édition riche d'articles
└── components/
    ├── AttachmentPanel.vue # Gestion des fichiers
    ├── ParticipantPhasePanel.vue # Progression des phases
    ├── FeedbackEngine.vue  # Objections & Suggestions
    └── EmptyState.vue      # Gestion des états vides (v2)
```

## Structure CSS (`resources/css/dazo-theme.css`)
Inspiré par le prototype HTML, le CSS repose sur :
1. **Variables racinaires (`:root`)** : pour les couleurs HSL et les polices.
2. **Design Fluide et Mobile-First** : Media queries pour adaptation Desktop.
3. **Composants structurels** : Classes génériques (`.btn`, `.card`, `.badge`).
4. **Hero Cards** : Bandeaux bleus premium pour l'identité de page.

## Flux de Données et Sécurité (Laravel Sanctum)
- L'authentification repose sur des "Stateful Cookies".
- Le store `auth.js` maintient la cohérence de connexion.
- **Sécurité** : Lors de l'inscription, un indicateur de force (score 0-4) et une check-list dynamique imposent un mot de passe complexe (Majuscule, Chiffre, Caractère spécial).

## État de la SPA
L'ensemble de l'interface respecte les prérequis métier du projet :
* **Un accès aux actions en "deux clics"**.
* **Affichage distinct** des phases du cycle de vie.
* **Onboarding** : Les invités non connectés arrivent sur une page d'attente qui valide leur token avant de les rediriger vers l'inscription pré-remplie.
