# Roadmap d'optimisation et de maintenabilite

Ce document decoupe les recommandations d'audit en chantiers independants. L'objectif est de pouvoir traiter chaque lot dans une branche ou une PR separee, avec des tests associes et un risque maitrise.

## Etat de depart

- Stack : Laravel 13, Sanctum, PostgreSQL, Vue 3, Pinia, Vite.
- Tests : PHPUnit operationnel via `composer test` ou `./vendor/bin/phpunit`.
- Environnement de test : stockage et caches Laravel isoles dans `/tmp` pour eviter les caches Docker et les permissions du dossier `storage`.
- Base de test : `phpunit.xml` utilise PostgreSQL sur `127.0.0.1`, base `dazo_test`, utilisateur `dazo_user`, mot de passe `secret`.
- Corrections deja faites : `PendingItemsController` ne retourne plus les decisions deja traitees, gere correctement la phase `reaction`, et charge `categories` au lieu de `category`.

## Lot ✅ 0 - Stabilisation des tests (Réalisé)

Objectif : garder une base de regression fiable avant toute optimisation.

Statut : en place. A maintenir a chaque lot.

Actions :
- Conserver `tests/bootstrap.php` pour creer le stockage temporaire de test.
- Executer `composer test` avant chaque changement backend.
- Ajouter un test de regression pour chaque bug corrige.
- Eviter les tests dependants du dossier local `storage`.
- Conserver les caches PHPUnit/Laravel de test dans `/tmp` pour eviter les caches Docker presents dans `bootstrap/cache`.

Tests attendus :
- Suite PHPUnit complete verte.
- Tests de permissions fichiers/uploads avec `Storage::fake()`.

## ✅ Lot 1 - Indexation base de donnees (Réalisé)

Objectif : reduire le cout des requetes frequentes avant augmentation de charge.

Statut : realise via `2026_05_03_000000_add_performance_indexes.php`.

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
- Verifier, apres analyse des plans SQL, si un index simple `decisions(visibility)` reste utile en plus de l'index compose `decisions(visibility, status, created_at)`.
- Verifier l'indexation de toutes les cles etrangeres (FK) non couvertes par les index composes.

Tests attendus :
- Migrations up/down.
- Verification manuelle des plans SQL sur les endpoints `pending-counts`, dashboard, liste publique.

## ✅ Lot 2 - Pagination et limites API (Réalisé)

Objectif : eviter les reponses volumineuses et les chargements memoire inutiles.

Actions réalisées :
- [x] Création des **Eloquent API Resources** pour tous les modèles (`Decision`, `User`, `Circle`, `Category`, `Notification`, etc.).
- [x] Standardisation de toutes les réponses JSON des contrôleurs (Admin et Front).
- [x] Implémentation de la **pagination côté serveur** systématique avec paramètre `per_page` dynamique et borné.
- [x] Refactorisation des stores Pinia pour supporter la structure `data` / `meta` de Laravel.
- [x] Mise à jour des vues Frontend (`DecisionList`, `AdminUsers`, `AdminCircles`, `AdminCategories`) pour utiliser le filtrage et la pagination serveur.
- [x] Suppression de la logique de filtrage/tri/pagination côté client devenue redondante.

## Lot 3 - Service de participation [TERMINE]

Objectif : supprimer les duplications entre `DecisionService`, `DecisionController`, `PendingCountsController`, `PendingItemsController` et `HasUserActionStatus`.

Actions réalisées :
- [x] Création de `DecisionParticipationService` pour isoler la logique métier (quorum, membres éligibles, statistiques).
- [x] Refactorisation de `DecisionService` pour déléguer les responsabilités de participation.
- [x] Unification des requêtes optimisées pour `PendingCountsController` et `PendingItemsController` dans le service via des Query Builders dédiés.
- [x] Migration de la logique du trait `HasUserActionStatus` dans le service et suppression du trait obsolète.
- [x] Mise à jour de `DecisionController` et `DashboardController` pour utiliser la source de vérité unique.
- [x] Intégration dans `DecisionPolicy` pour les contrôles d'accès (`participate`).

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
- Faire consommer ce service par la `DecisionPolicy` pour les regles de transition d'etat.

Tests attendus :
- Transition autorisee et interdite par statut.
- Droits auteur, animateur, admin, animateur de cercle, membre en mode reunion.
- Deadlines reaction/objection.
- Evenements dispatches.

## ✅ Lot 5 - Optimisation des pending counts/items (Réalisé)

Objectif : rendre les compteurs rapides sous forte charge.

Actions réalisées :
- [x] Ajout de la relation `latestMessage` via sous-requête (compatible PostgreSQL/UUID) pour un accès direct au dernier message.
- [x] Refactorisation de `PendingCountsController` : remplacement des `get()->filter()` par des requêtes SQL pures (`whereDoesntHave`, `whereHas`).
- [x] Refactorisation de `PendingItemsController` : suppression des boucles N+1 et de la logique de filtrage en mémoire.
- [x] Optimisation du trait `HasUserActionStatus` pour utiliser le chargement immédiat du dernier message uniquement.
- [x] Gain de performance significatif (réduction drastique de la consommation mémoire et du nombre de requêtes SQL).

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
- Utiliser le pattern **Provide/Inject** de Vue 3 pour partager l'etat de la decision et les permissions entre le composant racine et les sous-composants sans prop-drilling.

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
- Creer un `AuditLogService` pour standardiser l'enregistrement des actions metiers sensibles avec des payloads structures.

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
