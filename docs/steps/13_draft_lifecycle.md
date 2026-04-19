# Étape 13 — Cycle de Vie des Brouillons (Initial & Révision) et Animations

> Date : 17 avril 2026

## Objectif

Permettre la modification des propositions non-publiées (Brouillon/Draft), l'affectation d'animateurs de session, le changement des statuts hiérarchiques dans un cercle, et l'intégration des conflits d'intérêt via l'exclusion de membres du processus.

## Travaux Réalisés

### 1. Structure de données — Role d'Exclusion
- La classe enumération `App\Enums\DecisionParticipantRole` a été enrichie avec un cas `EXCLUDED = 'excluded'`.
- Cet ajustement permet désormais d'enregistrer techniquement qui n'a pas voix au chapitre sur une décision spécifique en utilisant la table standard de liaison pivot `decision_participants`.

### 2. Édition de "Drafts"
#### API Backend
- Ajout d'une route `PUT /api/v1/decisions/{id}` dirigée vers `DecisionController@update`.
- Cette route contrôle que la décision cible est bien en statut `draft` avant toute altération.
- Modifie directement le "title" principal et le "content" rattaché à la "current_version" (qui correspond obligatoirement à la V1 dans un repère temporel Brouillon).
- Purge et synchronise la définition éventuelle d'un nouveau participant au rôle `ANIMATOR` ainsi que des cibles `EXCLUDED`.
- **Nouveau (19 avril)** : La route `PUT` autorise également la modification en statut `revision`. Dans ce cas, les données sont stockées dans `revision_content` et `revision_attachment_ids` sur la table `decisions` pour préserver la version actuelle.

#### Vue Frontend (DecisionDetail.vue)
- En détectant le repère `status === 'draft'`, le module se désagrège de l'affichage conventionnel pour instancier un "Mode Édition".
- **Composants ajoutés :** 
  - Champs modifiables (Textarea, champs texts).
  - Listage asynchrone des membres du cercle sur la base de la décision, retranscrit sous forme :
    - D'un selecteur unique "Animateur".
    - D'une liste de checkboxes empilée pour cocher les Exclus.
- Implémentation du bouton `Publier la décision` appelant le flux de transition déjà existant `POST transition {to: clarification}` de l'`API`.
- **Nouveau (19 avril) : Espace de Révision**
  - Ajout d'un bloc de révision conditionnel en phase `revision`.
  - Boutons **Enregistrer** et **Publier** intégrés dans l'en-tête pour une gestion fluide du brouillon de révision.

### 3. Nomination de l'Animateur à l'initiation
- Dans le module `CreateDecisionModal.vue` figurant sur le Dashboard, la sélection d'un Cercle déclenche dynamiquement un `Watch()` asynchrone pour importer la nomenclature complète de ses membres `GET /api/v1/circles/{id}/members`.
- En découle la désignation paramétrique du responsable "Animateur" rattachée directement lors de la routine `POST`.

### 4. Gestions Administratives de Rôles
- **Backend** : Création conjointe de la méthode `CircleMemberController@update` gérant les flux `PUT /api/v1/circles/{c}/members/{u}` sous validation `['animator', 'member', 'observer']`.
- **Frontend** : Modification radicale du composant graphique `AdminCircles.vue`. Les badges passifs ont été remplacés par des composants balises `<select>` pilotant l'API en temps réel.
