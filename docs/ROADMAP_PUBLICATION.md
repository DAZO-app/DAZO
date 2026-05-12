# 🌐 Roadmap Détaillée : Publication & Widgets

Ce document détaille l'implémentation du **Snippet Generator** et l'ouverture de DAZO vers l'extérieur.

---

## 🧩 8. Snippet Generator (Widget JS)

L'objectif est de permettre l'intégration dynamique des décisions publiques sur n'importe quel site tiers via un script léger.

### Phase 8.1 : API Public Widgets
- [ ] Création d'un contrôleur dédié `Api/V1/Public/WidgetController.php`.
- [ ] Endpoint `/api/v1/public/widgets/decisions` : retourne une liste filtrée formatée pour le widget.
- [ ] Endpoint `/api/v1/public/widgets/decision/{id}` : retourne le détail d'une décision spécifique.
- [ ] Gestion des paramètres de filtrage : `circle_id`, `category_id`, `limit`, `sort`.
- [ ] Sécurisation : Uniquement les décisions ayant `is_public = true`.

### Phase 8.2 : Le Widget Frontend (DAZO-Loader.js)
- [ ] Création d'un script Vanilla JS minimaliste (sans dépendances lourdes).
- [ ] Système de rendu via Web Components ou injection de DOM.
- [ ] Styles isolés (Shadow DOM) pour éviter les conflits CSS avec le site hôte.
- [ ] Support des thèmes (Light/Dark) via attributs data (ex: `data-theme="dark"`).

### Phase 8.3 : Interface Admin "Snippet Generator"
- [ ] Développement de l'onglet "Snippet Generator" dans `AdminPublication.vue`.
- [ ] **Formulaire de configuration** :
    - Type de widget (Décision unique vs Liste).
    - Filtres dynamiques.
    - Options visuelles (couleurs, bordures, ombre).
- [ ] **Live Preview** : Zone d'aperçu affichant le widget tel qu'il apparaîtra.
- [ ] **Code Exporter** : Bouton pour copier le bloc de code `<script>` prêt à l'emploi.

### Phase 8.4 : Analytique & Tracking
- [ ] Enregistrement des "Vues" de widgets (compteur de consultations externes).
- [ ] Tracking des clics de redirection vers l'instance DAZO principale.

---

## Synthèse Technique

| Composant | Technologie | Statut |
| :--- | :--- | :--- |
| **API Widget** | Laravel API | ⏳ En attente |
| **JS Loader** | Vanilla JS / Vite | ⏳ En attente |
| **Admin UI** | Vue 3 | ⏳ En attente |
| **Shadow DOM CSS** | Scoped CSS | ⏳ En attente |
