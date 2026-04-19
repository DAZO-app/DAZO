# Étape 14 — Raffinements UI/UX, Dashboard et Détails

> Date : 19 avril 2026

## Objectif

Améliorer la lisibilité du Dashboard, affiner l'expérience utilisateur dans la vue détaillée d'une décision, et corriger des incohérences visuelles.

## Travaux Réalisés

### 1. Dashboard (Tableau de Bord)
- **Identité Visuelle** :
  - Logo centré et agrandi dans les blocs vides (60% de l'espace).
  - Opacité du logo passée à 60% pour un aspect premium "filigrane" et texte en noir pour une meilleure lisibilité.
- **Organisation des Contenus** :
  - Suppression de l'affichage répétitif du nom du cercle avant chaque titre de décision (l'information étant déjà présente via le cercle coloré).
  - Ajout d'une icône **Trombone** (📎) devant les titres des décisions comportant des pièces jointes.
- **Logique d'Affichage** :
  - Les blocs "Clarifications actives" et "Objections à traiter" restent désormais visibles même s'ils sont vides, avec un message informatif, pour maintenir la structure de la page.
  - Correction du compteur de la carte "En cours" pour ne comptabiliser que les décisions publiées et non finalisées (exclut les brouillons, adoptées, abandonnées, etc.).

### 2. Vue Détail de Décision
- **Indicateur de Rôle** :
  - Remplacement de la carte générique "Participation validée" par une carte de rôle personnalisée (**Porteur** ou **Animateur**) utilisant les codes couleurs et icônes du dashboard.
- **Journal des Échanges** :
  - Intégration des actions de validation directe ("RAS", "C'est clair", "Pas d'objection") dans le thread de discussion.
  - Regroupement des participants ayant validé dans un bloc consolidé avec bordure verte, sans bouton dépliant, pour un gain de place et une lecture immédiate.

### 3. Gestion des Pièces Jointes
- **Composant AttachmentPanel** :
  - Refonte pour supporter l'upload de fichiers sans ID de version immédiat (pour les brouillons de révision).
  - Ajout des événements `@uploaded` et `@removed` pour permettre aux composants parents de suivre l'état des fichiers "flottants".

## Fichiers Modifiés

| Fichier | Modification |
|---|---|
| `resources/js/views/Dashboard.vue` | Logo, compteurs, filtres de liste, visibilité des blocs. |
| `resources/js/views/DecisionDetail.vue` | Cartes de rôle, synthèse des participations dans le thread. |
| `resources/js/components/AttachmentPanel.vue` | Logique d'upload asynchrone et événements. |
| `resources/css/app.css` | (Via DecisionDetail) Styles pour les nouveaux blocs de participation. |

## Vérification
- [x] Dashboard : Logo bien visible et centré.
- [x] Dashboard : Icône trombone affichée sur les décisions concernées.
- [x] Detail : Carte "Porteur" affichée pour l'auteur.
- [x] Detail : Participations "C'est clair" visibles dans le flux de discussion.
