# Documentation Front-End (SPA Vue 3)

Le front-end de DAZO est une Single Page Application (SPA) bâtie avec Vue 3, intégrée de manière native à notre backend Laravel via Vite.js. Elle est pensée pour être fortement réactive, mobile-first, et ne dépend d'aucun framework CSS utilitaire strict (CSS Vanilla structuré).

## Stack Technique
* **Framework principal** : Vue 3 (Composition API avec `<script setup>`)
* **Bundler** : Vite.js (intégré via `@vitejs/plugin-vue`)
* **Routage** : Vue Router 4 (History API)
* **Store global** : Pinia (État réactif et persistant)
* **Client HTTP** : Axios (Configuré avec Sanctum CSRF / Interceptors)
* **Styling** : CSS Vanilla (Design system importé de `dazo-ui.html`)
* **Édition riche** : Quill 2 chargé côté client pour la rédaction des décisions

## Architecture des Dossiers (`resources/js/`)
```
resources/js/
├── bootstrap.js         # Configuration d'Axios et des headers
├── app.js               # Point d'entrée Vue, Pinia, Router
├── router/
│   └── index.js         # Configuration des routes (Dashboard, Login, Decisions...)
├── stores/
│   ├── auth.js          # Store d'authentification (Sanctum)
│   └── decision.js      # Store du moteur de décision (état central de la SPA)
├── layouts/
│   └── AppLayout.vue    # Coquille globale avec Sidebar / BottomNav mobile
├── views/
│   ├── Login.vue        # Page de connexion SPA
│   ├── Dashboard.vue    # Vue d'ensemble, timeline de l'utilisateur
│   ├── DecisionList.vue # Liste des décisions, explorateur
│   └── DecisionDetail.vue # Vue principale de décision (moteur métier central)
└── components/
    ├── AttachmentPanel.vue # Affichage, upload, suppression et téléchargement des pièces jointes
    ├── ParticipantPhasePanel.vue # Suivi des participants et de leur progression par phase
    ├── RichTextEditor.vue # Wrapper Quill réutilisable pour l'édition HTML
    ├── DecisionThread.vue  # Composant fil de discussion (clarification & réactions)
    └── FeedbackEngine.vue  # Mécanique de soumission d'objections et suggestions
```

## Structure CSS (`resources/css/dazo-theme.css`)
Inspiré par le prototype HTML, le CSS repose sur :
1. **Variables racinaires (`:root`)** : pour les couleurs HSL et les polices.
2. **Design Fluide et Mobile-First** : Media queries `@media (min-width: 768px)` inversant la vue mobile vers la Sidebar Desktop.
3. **Composants structurels** : Classes génériques (`.btn`, `.card`, `.badge`, `.thread-message`) au lieu de longues combinaisons de classes utilitaires.

## Flux de Données et Sécurité (Laravel Sanctum)
- L'authentification repose sur des "Stateful Cookies".
- Lors de l'initialisation, Axios récupère un cookie `XSRF-TOKEN` validé par le backend Laravel.
- Le store `auth.js` maintient la cohérence de connexion. En cas d'erreur `401 Unauthorized` de l'API, un intercepteur Axios déconnecte l'utilisateur et redirige vers `/login`.

## État de la SPA
L'ensemble de l'interface respecte les prérequis métier du projet :
* **Un accès aux actions en "deux clics"** pour les interactions vitales (consentement sans objection, rejoins d'objection).
* **Affichage distinct** des phases du cycle de vie (Clarification, Réaction, Objection) avec chargement conditionnel des composants Vue appropriés (`DecisionThread.vue` ou `FeedbackEngine.vue` selon l'état).
* **Édition complète du brouillon** directement dans `DecisionDetail.vue`, avec éditeur riche, animateur, exclusions, pièces jointes et suppression du brouillon.
* **Actions de pilotage remontées dans le fil d'ariane** pour garder la colonne latérale centrée sur la participation.
* **Panneau participants** dans la colonne de droite, montrant le rôle de chaque personne et son avancement sur les phases Clarification / Réaction / Objection.
* **Pièces jointes visibles sur tous les écrans de consultation**, avec ouverture directe et téléchargement. Un indicateur visuel (trombone 📎) est présent sur le Dashboard pour les décisions concernées.
* **Navigation historique complète** : Un panneau "Versions précédentes" dans `DecisionDetail.vue` permet de naviguer entre les différentes itérations d'une proposition, avec accès aux contenus et échanges archivés.
* **Mode Révision assisté** : Lors de la rédaction d'une nouvelle version, la proposition en vigueur reste visible sous l'éditeur pour faciliter le travail du porteur.
