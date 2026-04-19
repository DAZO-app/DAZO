# Étape 6 : Adoption et Nouvelle Version (Révision)

> **Statut** : Complété ✅ (Couvre les Blocs 8 et 9 de la Roadmap originale). Clôture officielle de l'intégration Backend V1.
> **Objectif** : Mécanismes d'Adoption (Bloc 8) et gestion des itérations correctives d'une proposition (Bloc 9).

---

## 🛠 Ce qui a été implémenté

### 1. Automate d'Adoption (Bloc 8)
- **Vérification Intelligente** : La méthode `FeedbackService::checkAndAdoptIfNoBlockingObjections()` (déjà détaillée à l'étape 5) se trouve au cœur du Bloc 8. Elle surveille le pool d'objections d'une décision à l'instant `T`. Si **absolument plus aucune objection** ne bloque, la décision est *de facto* validée.
- **Le Forçage Modérateur** (`override`) : Une option de modération spéciale via le paramètre JSON `"to": "adopted_override"` lancé sur `POST /api/v1/decisions/{id}/transition`. Elle gère les cas particuliers où un Animateur coupe court au débat infini et force l'adoption malgré la présence de blocages. Les interfaces front-end afficheront distinctement ce `adopted_override`.

### 2. Dépôt de Nouvelle Version / Révision (Bloc 9)
La mise au propre et le raffinement constant de textes nécessitent des réécritures :
- **Autorisation Stricte** : La création d'une nouvelle version n'est autorisée par `CreateDecisionVersionRequest` **que** lorsque le statut actuel de la décision est officiellement sur `REVISION` (suite logique après la phase `OBJECTION`). L'acteur doit être le rédacteur d'origine.
- **Persistance des Brouillons de Révision** : L'auteur peut désormais enregistrer son travail en cours via les champs `revision_content` et `revision_attachment_ids` sur la table `decisions`. Cela permet de préparer la nouvelle proposition et ses pièces jointes sur plusieurs sessions sans affecter la version actuellement consultable.
- **Désactivation Automatique** : Lors du POST final (`DecisionVersionController::store`), le `DecisionService::createNewVersion()` cherche statiquement l'ancienne version active (`is_current = true`), la passe sur `false`, et injecte la toute nouvelle dans la table en attribuant `is_current = true` et `version_number = N+1`.
- **Lien des Archives & Pièces Jointes** : La nouvelle version retient dynamiquement un pointeur `previous_version_id` vers l'archive. Les fichiers préparés dans le brouillon de révision sont automatiquement liés à la nouvelle version lors de sa création.
- **Relance Automatique du Cycle** : À l'issue de cet ajout, la décision est automatiquement redescendue au statut `CLARIFICATION`. Un tout nouveau round de débat peut alors s'ouvrir. Le Back-End DAZO est une boucle continue.

---

> 🎉 **FINALE BACKEND V1** : L'entièreté des spécifications Backend DAZO pour la V1 sont officiellement intégrées et couvertes. L'API est fonctionnelle.
