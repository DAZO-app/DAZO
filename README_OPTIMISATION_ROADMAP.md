# Roadmap d'optimisation et de maintenabilite

Ce document decoupe les recommandations d'audit en chantiers independants. L'objectif est de pouvoir traiter chaque lot dans une branche ou une PR separee, avec des tests associes et un risque maitrise.

## Etat de depart

- Stack : Laravel 13, Sanctum, PostgreSQL, Vue 3, Pinia, Vite.
- Tests : PHPUnit operationnel via `composer test` ou `./vendor/bin/phpunit`.
- Environnement de test : stockage et caches Laravel isoles dans `/tmp` pour eviter les caches Docker et les permissions du dossier `storage`.
- Base de test : `phpunit.xml` utilise PostgreSQL sur `127.0.0.1`, base `dazo_test`, utilisateur `dazo_user`, mot de passe `secret`.
- Corrections deja faites : `PendingItemsController` ne retourne plus les decisions deja traitees, gere correctement la phase `reaction`, et charge `categories` au lieu de `category`.

## Lot 0 - Stabilisation des tests

Objectif : garder une base de regression fiable avant toute optimisation.

Actions :
- Conserver `tests/bootstrap.php` pour creer le stockage temporaire de test.
- Executer `composer test` avant chaque changement backend.
- Ajouter un test de regression pour chaque bug corrige.
- Eviter les tests dependants du dossier local `storage`.

Tests attendus :
- Suite PHPUnit complete verte.
- Tests de permissions fichiers/uploads avec `Storage::fake()`.

## Lot 1 - Indexation base de donnees

Objectif : reduire le cout des requetes frequentes avant augmentation de charge.

Actions proposees :
- Ajouter un index `decisions(status)`.
- Ajouter un index compose `decisions(circle_id, status)`.
- Ajouter un index compose `decisions(visibility, status, created_at)`.
- Ajouter un index compose `decision_versions(decision_id, is_current)`.
- Ajouter un index compose `decision_participants(decision_id, user_id, role)`.
- Ajouter un index compose `circle_members(circle_id, user_id, role)`.
- Ajouter un index compose `feedbacks(decision_version_id, type, status)`.
- Ajouter un index compose `feedback_messages(feedback_id, created_at)`.
- Ajouter un index compose `notifications(user_id, read_at, created_at)`.

Tests attendus :
- Migrations up/down.
- Verification manuelle des plans SQL sur les endpoints `pending-counts`, dashboard, liste publique.

## Lot 2 - Pagination et limites API

Objectif : eviter les reponses volumineuses et les chargements memoire inutiles.

Actions proposees :
- Paginer `GET /api/v1/decisions`.
- Paginer `GET /api/v1/circles/{circle}/decisions`.
- Paginer les listes admin `users`, `circles`, `categories` si elles grossissent.
- Limiter le dashboard a des blocs recents ou pagines.
- Ajouter des parametres `per_page` bornes, par exemple 10 a 100.

Tests attendus :
- Structure paginator Laravel.
- Respect du `per_page` maximum.
- Compatibilite frontend sur listes existantes.

## Lot 3 - Service de participation

Objectif : supprimer les duplications entre `DecisionService`, `DecisionController`, `PendingCountsController`, `PendingItemsController` et `HasUserActionStatus`.

Actions proposees :
- Creer `DecisionParticipationService`.
- Y deplacer :
  - calcul des membres eligibles,
  - calcul des participants par phase,
  - detection des utilisateurs en attente,
  - enregistrement des abstentions automatiques,
  - construction de la carte de participation par phase.
- Faire consommer ce service par les controleurs et traits existants.

Tests attendus :
- Tests unitaires du service.
- Tests feature existants inchanges.
- Tests sur auteur, animateur, observateur, membre exclu, membre standard.

## Lot 4 - Workflow de decision

Objectif : isoler la machine d'etat et rendre les transitions plus lisibles.

Actions proposees :
- Creer `DecisionWorkflowService`.
- Y deplacer les transitions autorisees, droits de transition, deadlines et evenements.
- Supprimer le doublon d'evenement `ADOPTED` dans `DecisionService`.
- Remplacer les tableaux internes par une configuration explicite et testee.

Tests attendus :
- Transition autorisee et interdite par statut.
- Droits auteur, animateur, admin, animateur de cercle, membre en mode reunion.
- Deadlines reaction/objection.
- Evenements dispatches.

## Lot 5 - Optimisation des pending counts/items

Objectif : rendre les compteurs rapides sous forte charge.

Actions proposees :
- Remplacer les chargements `get()->filter()` par des `exists`, `count`, sous-requetes ou agrégations SQL.
- Eviter de charger tous les messages d'un feedback pour trouver le dernier.
- Ajouter une relation ou une sous-requete pour le dernier message.
- Mutualiser la logique avec `DecisionParticipationService`.

Tests attendus :
- Meme resultat que les tests actuels.
- Tests avec plusieurs decisions et plusieurs phases.
- Mesure du nombre de requetes avec un dataset moyen.

## Lot 6 - Front public

Objectif : separer les usages public front, XML API, meta et suggestions.

Actions proposees :
- Decouper `PublicDecisionController` en :
  - `PublicFrontDecisionController`,
  - `PublicXmlDecisionController`,
  - `PublicDecisionMetaController`,
  - eventuellement `PublicDecisionSuggestionController`.
- Limiter `showFront` a la version courante par defaut.
- Charger l'historique complet uniquement sur demande.
- Cache court sur `meta`, categories, cercles et statuts publics.

Tests attendus :
- Publication uniquement si cercle, categorie, statut et visibilite sont autorises.
- JSON public front.
- XML public.
- Suggestions bornees.

## Lot 7 - Admin tools

Objectif : reduire le controleur admin multi-responsabilites.

Actions proposees :
- Scinder `AdminToolController` en :
  - `AdminDatabaseController`,
  - `AdminBackupController`,
  - `AdminServerController`,
  - `AdminLogController`.
- Encapsuler `pg_dump`, `psql`, `tail` dans des services.
- Journaliser les actions sensibles backup/restore/delete.
- Ajouter une confirmation forte pour restore.

Tests attendus :
- Autorisation admin.
- Validation du nom de fichier.
- URL signee de telechargement.
- Cas fichier introuvable.

## Lot 8 - Composants Vue volumineux

Objectif : faciliter les evolutions frontend sans casser les flux decision/reunion/admin.

Actions proposees :
- Decouper `DecisionDetail.vue` avec des composables :
  - `useDecisionDraft`,
  - `useDecisionTransitions`,
  - `useDecisionVersions`,
  - `useDecisionParticipation`.
- Decouper `MeetingModeOverlay.vue` :
  - panneau participants,
  - panneau feedback,
  - actions secretaire,
  - historique/undo,
  - pieces jointes.
- Decouper `AdminConfig.vue` :
  - branding,
  - mail,
  - OAuth,
  - publication,
  - pages legales.
- Remplacer les `alert()` par un systeme de notifications UI.

Tests attendus :
- Build Vite.
- Tests manuels des parcours critiques.
- Captures ou tests e2e a ajouter ensuite si le projet adopte Playwright/Cypress.

## Lot 9 - Observabilite et logs

Objectif : conserver l'audit sans penaliser les requetes a forte charge.

Actions proposees :
- Mettre `LogRequest` derriere une queue ou un mode sampling configurable.
- Filtrer davantage les payloads sensibles.
- Ajouter une retention configurable plus courte que 1 an si necessaire.
- Ajouter des index sur `app_logs(created_at)` et `app_logs(user_id, created_at)`.

Tests attendus :
- Les requetes mutatives creent un log.
- Les routes ignorees ne creent pas de log.
- Les champs sensibles sont absents.

## Lot 10 - Nettoyage technique continu

Objectif : reduire le bruit et prevenir les regressions.

Actions proposees :
- Supprimer les imports inutilises.
- Supprimer ou documenter les methodes obsoletes.
- Retirer les `console.log` de production.
- Ajouter Laravel Pint en verification CI.
- Ajouter une commande CI minimale : `composer test`, `npm run build`.

Tests attendus :
- PHPUnit vert.
- Build frontend vert.
- Format Pint sans changement inattendu.

## Ordre conseille

1. Lot 0 : tests.
2. Lot 1 : index DB.
3. Lot 2 : pagination API.
4. Lot 5 : pending counts/items.
5. Lot 3 : service de participation.
6. Lot 4 : workflow decision.
7. Lot 6 : front public.
8. Lot 7 : admin tools.
9. Lot 8 : gros composants Vue.
10. Lots 9 et 10 en continu.

## Commandes utiles

```bash
composer install
composer test
./vendor/bin/phpunit --filter PendingItemsTest
npm run build
```
