# Étape 4 : Machine d'États & Cycle de Vie

> **Statut** : Complété ✅
> **Modifications Exceptionnelles** : Refus de Spatie `laravel-model-states` dû à des soucis de dépendances directes avec l'implémentation Laravel 13 (PHP 8.3). La Machine à États a été réécrite proprement via `DecisionService`.

---

## 🛠 Ce qui a été implémenté

### 1. Contrôleur de Transitions
- Un `DecisionTransitionController` a été ajouté pour prendre en charge les flux séquentiels d'une instance de décision de manière REST API via `POST /api/v1/decisions/{id}/transition`.
- Endpoint spécial d'abandon direct ajouté (`POST /api/v1/decisions/{id}/abandon`), utile pour les UIs pour un acte terminal de création.
- Les requêtes sont filtrées par un `TransitionDecisionRequest` qui n'accepte que les Enums correspondants aux `DecisionStatus`.

### 2. Le Moteur Custom (DecisionService)
- Implémentation de la fonction maîtresse `transition($decision, $status, $actor, $isSystem)`. 
- **Array of Allowed Rules** : Un dictionnaire strict garantit par exemple qu'il est impossible de passer en `REACTION` depuis `REVISION` ni `DRAFT` depuis `OBJECTION`.
- **État Suspendu** : Nouvel état global permettant de mettre en pause n'importe quelle phase active (`CLARIFICATION`, `REACTION`, `OBJECTION`, `REVISION`).
- **Gestion de la Reprise** : Lors de la suspension, le statut d'origine est sauvegardé dans `status_before_suspension` pour permettre une reprise fidèle au cycle précédent.
- L'algorithme valide que c'est un AUTEUR ou ANIMATOR qui initie la demande (Sauf pour l'adoption globale par un `GLOBAL_ANIMATOR` ou un évènement Cron système).
- Dispatch dynamique des **12 App\Events** de cycle (Ex: `DecisionAdopted`, `DecisionLapsed`) en fonction du statut terminal atterri. L'avantage d'une solution Custom : aucune surcharge mémoire et pas d'installation de composants tiers compliqués à migrer dans le temps.

### 3. Gestion Limitée dans le Temps (Cron)
- Création de la Job Queue Laravel : **`CheckDecisionDeadlines`**.
- Cette Job requête l'ensemble de la base où la `objection_round_deadline` est inférieure à l'heure PHP `now()` courante, et passe la décision au statut terminal passif `LAPSED`.
- **Suspension des Délais** : Lorsqu'une décision passe à l'état `SUSPENDED`, son échéance active est gelée (mise à `null`).
- **Période de Révision** : Définition de l'intervalle de ré-examen suggéré pour les décisions adoptées.
- **Actions de Suspension** : Le Porteur ou l'Animateur peuvent manuellement "Mettre en Pause" le processus depuis le menu d'actions rapides (icône engrenage) pour geler les délais.
- **Réactivation** : Lors de la sortie de l'état suspendu, l'échéance est recalculée pour la phase en cours à partir de l'heure de réactivation.
- Configuration locale validée par un `Schedule::job(...)` horaire directement imbriqué dans `routes/console.php`.

---

> **Suite naturelle** : Bloc 5 - Feedback (Consentements et Objections) *(Prochainement...)*
