# Étape 3 : Moteur de Décision (Core)

> **Statut** : Complété ✅
> **Objectif** : Implémentation de la logique de création formelle des décisions (DRAFT), des templates (Models), et du versioning des textes.

---

## 🛠 Ce qui a été implémenté

### 1. Templates de décision (`DecisionModel`)
- Contrôleur **`DecisionModelController`** : Gère la création, la lecture et la mise à jour des "templates" qui cadreront les futures décisions.
- **`DecisionModelPolicy`** : La lecture est ouverte à tous (pour choisir un modèle), mais l'update/création est vérifiée formellement via `$user->is_global_animator` en attendant des rôles plus complexes pour les administrateurs d'instances.

### 2. Service d'Initialisation (DecisionService)
- L'initialisation du statut **DRAFT** d'une décision requiert la constitution complexe de plusieurs données qui justifie le `DecisionService::createDecision()`.
- **Mécanisme interne** :
  - Création de l'entité commune `Decision` attachée à son Cercle (`circle_id`).
  - Création instantanée et en chaîne de la **version 1** de la décision par le créateur via la relation statique `.versions()`. Le champ est marqué `is_current = true`.
  - Intégration à la table stricte des comportements `DecisionParticipant` : le créateur récupère **automatiquement** le rôle d' `AUTHOR`. S'il assigne un autre utilisateur comme animateur, la seconde assignation est effectuée en tant qu'`ANIMATOR`.
  - Levée d'un événement `DecisionCreated`. L'arborescence est couverte par une transaction de base de données.

### 3. Consultation Sécurisée et Historique (Versions)
- **`DecisionController`** (`index`, `show`, `store`) contrôlé par **`DecisionPolicy`** : L'accès en lecture à une décision exige de pouvoir accéder au cercle parent de cette même décision.
- Contrôleur Historique **`DecisionVersionController`** : L'URL RESTful `/api/v1/decisions/{id}/versions` permet de lister l'historique complet, tandis que la sélection exacte expose ses auteurs respectifs et la hiérarchie différentielle (`previous_version_id`).

---

> **Suite naturelle** : Bloc 4 - Phase de Consentement Primaire *(Prochainement...)*
