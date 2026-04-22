# Étape 17 — Aide & Wiki Intégré

L'objectif de cette étape était de fournir une source de vérité documentaire au sein même de la plateforme, permettant aux utilisateurs d'accéder aux guides de gouvernance (Élection sans candidat, Consentement, etc.) et aux administrateurs de gérer cette base de connaissance en toute autonomie.

## 🧱 Composants du Système

### 1. Backend (Laravel)
- **Modèle `WikiPage`** : Persistance UUID avec support de catégories et de "slugs" pour des URLs propres.
- **Migration** : Table `wiki_pages` dédiée.
- **Contrôleurs API** : 
  - `WikiController` : Lecture publique avec moteur de recherche textuel (`LIKE %search%`).
  - `Admin\WikiController` : CRUD complet sécurisé par le middleware `admin`.

### 2. Frontend (Vue 3)
- **`WikiIndex.vue`** : Portail d'accueil avec barre de recherche et navigation thématique.
- **`WikiDetail.vue`** : Affichage premium à deux colonnes (Contenu / Barre latérale contextuelle).
- **`WikiEditor.vue`** : Interface de rédaction utilisant le `RichTextEditor` (Quill), avec prévisualisation et gestion des paramètres (slug, catégorie, publication).
- **`AdminWiki.vue`** : Vue de gestion tabulaire pour les administrateurs.

## 🎨 Design System "Wiki"
Le Wiki suit la charte "Premium" de DAZO :
- **Hero Headers** à dégradés Indigo/Blue.
- **Premium Cards** pour le contenu et les paramètres.
- **Typographie soignée** utilisant des styles `:deep` pour garantir un rendu optimal du HTML généré par l'éditeur riche.

## 🚀 Utilisation
- **Utilisateurs** : Accès direct via l'icône "Aide" dans la barre latérale.
- **Administrateurs** : Accès au bouton "Gérer le Wiki" pour modifier ou créer de nouveaux articles.
