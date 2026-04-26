# DAZO — Backlog Technique

Documentation des axes d'amélioration technique, de performance et de qualité du code.
Complément au ROADMAP.md orienté fonctionnalités.

---

## 🔴 Critique (prod-ready)

### API Resources Laravel
Actuellement, les modèles Eloquent sont sérialisés directement (risque de sur-exposition).

**À créer :**
```bash
php artisan make:resource DecisionResource
php artisan make:resource FeedbackResource
php artisan make:resource UserResource
php artisan make:resource AttachmentResource
```

**Avantages :**
- Contrôle précis des champs exposés (cacher `is_admin`, etc.)
- Transformation des données (dates formatées, relations incluses/exclues)
- Standardisation des réponses API

### Broadcasting sur événements existants
Les events (`FeedbackSubmitted`, `DecisionTransitioned`, etc.) existent mais ne sont pas broadcastés.

**Étapes :**
1. Implémenter `ShouldBroadcast` sur chaque event
2. Créer des canaux privés par décision : `PrivateChannel("decision.{id}")`
3. Brancher les listeners Echo dans `DecisionDetail.vue` pour auto-refresh des feedbacks

### Politique de sécurité MIME — Validation supplémentaire
Pour les fichiers Office (`.docx`, `.xlsx`), le MIME type peut être mal détecté.
Ajouter une validation par magic bytes (premiers octets du fichier) pour les types ambigus.

---

## 🟠 Important (optimisation)

### Découpage `DecisionDetail.vue` (1885 → ~4 fichiers)

| Composant | Responsabilité |
|---|---|
| `DecisionHero.vue` | En-tête, statut, fil d'Ariane, barre d'actions (étoile, cloche, relance, imprimer) |
| `DecisionEditForm.vue` | Formulaire d'édition (brouillon + révision), Quill, pièces jointes |
| `DecisionPhaseActions.vue` | Boutons de transition de phase, vote consentement |
| `DecisionDetail.vue` | Orchestrateur léger qui compose les 3 ci-dessus |

### Découpage `FeedbackEngine.vue` (553 → ~3 fichiers + 1 composable)

| Fichier | Responsabilité |
|---|---|
| `FeedbackThread.vue` | Un thread complet (messages + formulaire de réponse) |
| `FeedbackSummaryBlock.vue` | Blocs "C'est clair" / "Pas d'objection" + "Validé après échanges" |
| `useFeedbackRole.js` | Composable : `getRoleIcon()`, `getMsgStyle()`, `isMyTurn()` |
| `FeedbackEngine.vue` | Orchestrateur des threads par type |

### Mise en cache `PendingCounts`
Le contrôleur recalcule tout à chaque appel (toutes les 60s).
Ajouter un cache par utilisateur de 30s avec invalidation à chaque event pertinent.

```php
Cache::remember("pending_counts_{$userId}", 30, fn() => $this->calculate());
// Invalider dans FeedbackSubmitted, ConsentSubmitted, DecisionTransitioned Listeners
```

### Pagination sur `/api/v1/decisions`
Actuellement `get()` sans limite. À remplacer par `paginate(20)` avec curseur.

---

## 🟡 Amélioration (qualité)

### Optimistic UI — Checklist composants concernés

| Composant | Action | Status |
|---|---|---|
| `DecisionDetail.vue` | Toggle favori | ✅ Fait (store) |
| `FeedbackEngine.vue` | "C'est clair" / "Plus d'objection" | ⬜ À faire |
| `ParticipationPanel` | Vote consentement | ⬜ À faire |
| `FeedbackEngine.vue` | Envoi message dans thread | ⬜ À faire |

### Tests à ajouter

| Fichier | Tests manquants |
|---|---|
| `DecisionService` | `transition()` — transitions invalides, droits manquants |
| `NotificationService` | Respect des préférences par décision |
| `PendingItemsController` | Tests de cohérence items/compteurs |
| `HasUserActionStatus` | Trait avec données en mémoire vs requêtes |
| `AttachmentController` | Type MIME conforme vs déclaré (magic bytes) |

### Gestion d'erreurs cohérente
Créer un `Handler` API uniforme qui formate tous les erreurs Laravel (validation, auth, 404...) en JSON standardisé :
```json
{ "message": "...", "errors": { "field": ["msg"] }, "code": "VALIDATION_ERROR" }
```

---

## 🟢 Nice to have

### Logging des actions sensibles
Ajouter un audit log pour :
- Changements de statut de décisions
- Envoi de relances par email
- Impersonnation
- Suppression d'utilisateurs

### Commande artisan de diagnostic
```bash
php artisan dazo:health-check
```
Vérifie : connexion DB, queue, mail, Reverb, espace disque, dernière migration.

### Dark mode
Variable CSS `--color-scheme` déjà présente dans la charte.
Ajouter un toggle dans les préférences utilisateur.
