# 🌐 Roadmap Détaillée : Publication & Widgets

Ce document détaille l'implémentation du **Snippet Generator** et l'ouverture de DAZO vers l'extérieur.

---

## 🧩 8. Snippet Generator (Widget JS)

L'objectif est de permettre l'intégration dynamique des décisions publiques sur n'importe quel site tiers via un script léger.

### Phase 8.1 : API Public Widgets
- [x] Création d'un contrôleur dédié `Api/V1/PublicDecisionController.php`.
- [x] Endpoint `/api/v1/public/decisions` : retourne une liste filtrée formatée pour le widget.
- [x] Endpoint `/api/v1/public/decisions/{id}` : retourne le détail d'une décision spécifique.
- [x] Gestion des paramètres de filtrage : `circle_id`, `category_id`, `status`.
- [x] Sécurisation : Uniquement les décisions ayant `visibility = public` et validation de la `X-API-Key`.

### Phase 8.2 : Le Widget Frontend (loader.js)
- [x] Création d'un script Vanilla JS autonome (`/public/widgets/loader.js`).
- [x] Système de rendu dynamique sans dépendances (DOM Injection).
- [x] Support complet des **15 thèmes visuels** prédéfinis.
- [x] Rendu conditionnel des détails (clarifications, réactions, objections).

### Phase 8.3 : Interface Admin "Snippet Generator"
- [x] Développement de l'onglet "Mes Snippets" et du générateur dans `AdminPublication.vue`.
- [x] **Formulaire de configuration** :
    - Type de widget (Décision unique vs Liste).
    - Filtres dynamiques unifiés.
    - Sélection du thème avec prévisualisation en temps réel.
- [x] **Live Preview** : Zone d'aperçu dynamique (mockup) et rendu réel via popin.
- [x] **Code Exporter** : Bouton pour copier le script et le bloc `div` avec attributs `data-*`.

### Phase 8.4 : Analytique & Tracking
- [ ] Enregistrement des "Vues" de widgets (compteur de consultations externes).
- [ ] Tracking des clics de redirection vers l'instance DAZO principale.

---

## Synthèse Technique

| Composant | Technologie | Statut |
| :--- | :--- | :--- |
| **API Widget** | Laravel API | ✅ Terminé |
| **JS Loader** | Vanilla JS | ✅ Terminé |
| **Admin UI** | Vue 3 | ✅ Terminé |
| **Thématisation** | CSS Dynamique | ✅ Terminé |
