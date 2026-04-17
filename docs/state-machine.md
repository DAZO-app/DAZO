# State Machine — DECISION

> Document de référence des transitions d'états pour les développeurs.
> Toutes les transitions sont implémentées via `spatie/laravel-model-states`.

---

## Transitions DECISION.status

| De | Vers | Condition | Acteur | Automatique |
|---|---|---|---|---|
| `draft` | `clarification` | Aucune — décision manuelle | Auteur, Animateur | Non |
| `clarification` | `reaction` | Aucune — décision manuelle | Auteur, Animateur | Non |
| `reaction` | `objection` | Aucune — décision manuelle | Auteur, Animateur | Non |
| `objection` | `adopted` | `FeedbackService::hasNoBlockingObjections(version)` → true | Auteur, Animateur, Système | Oui (si 0 objection) |
| `objection` | `adopted_override` | Objections restantes — décision explicite | Auteur, Animateur | Non |
| `objection` | `revision` | Objections restantes — révision nécessaire | Auteur, Animateur | Non |
| `revision` | `clarification` | Nouvelle `DECISION_VERSION` créée | Auteur | Non |
| `*` | `abandoned` | Décision manuelle d'annulation | Auteur, Animateur | Non |
| `*` | `lapsed` | `DECISION.deadline` dépassée | Système | Oui |
| `*` | `deserted` | Inactivité > `INSTANCE_CONFIG.reminder_no_deadline_days` | Système | Oui |

---

## Règle d'adoption automatique

```
FeedbackService::hasNoBlockingObjections(DecisionVersion $version): bool
{
    // true si aucun FEEDBACK de type 'objection' sur cette version
    // n'est dans un statut non-terminal :
    // non-terminaux : submitted, clarification_requested, in_treatment
    // terminaux     : treated, rejected, acknowledged, ignored
}
```

---

## Transitions FEEDBACK.status

| De | Vers | Acteur autorisé | Notes |
|---|---|---|---|
| `submitted` | `clarification_requested` | Auteur décision, Animateur | Demander une précision |
| `submitted` | `in_treatment` | Auteur décision, Animateur | Prise en charge |
| `submitted` | `rejected` | Auteur décision, Animateur | Rejet (non recevable) |
| `submitted` | `acknowledged` | Auteur décision, Animateur | Pris en compte sans traitement immédiat |
| `submitted` | `ignored` | Auteur décision, Animateur | Ignoré (à utiliser avec parcimonie) |
| `clarification_requested` | `submitted` | Auteur du feedback | Après avoir fourni la clarification |
| `in_treatment` | `treated` | Auteur du feedback (objection), Auteur décision / Animateur (suggestion) | Traitement terminé |
| `in_treatment` | `rejected` | Auteur décision, Animateur | |
| `*` | `withdrawn` | Auteur du feedback uniquement | Retrait volontaire |

> **Règle clé :** seuls les feedbacks de `type=objection` dans un état non-terminal
> bloquent l'adoption. Les suggestions n'ont pas d'effet bloquant.

---

## Implémentation Laravel recommandée

```
// composer require spatie/laravel-model-states

// App/States/Decision/
// ├── DecisionState.php         (abstract)
// ├── DraftState.php
// ├── ClarificationState.php
// ├── ReactionState.php
// ├── ObjectionState.php
// ├── RevisionState.php
// ├── AdoptedState.php
// ├── AdoptedOverrideState.php
// ├── AbandonedState.php
// ├── LapsedState.php
// └── DesertedState.php

// App/Transitions/Decision/
// ├── TransitionToClarification.php
// ├── TransitionToReaction.php
// ├── TransitionToObjection.php
// ├── TransitionToAdopted.php
// ├── TransitionToAdoptedOverride.php
// ├── TransitionToRevision.php
// └── ... (abandon, lapsed, deserted)
```

---

## Événements à déclencher lors des transitions

| Transition | Événement à dispatcher |
|---|---|
| `* → clarification` | `DecisionTransitioned` |
| `* → objection` | `DecisionEnteredObjectionPhase` |
| `objection → adopted` | `DecisionAdopted` |
| `objection → adopted_override` | `DecisionAdoptedWithOverride` |
| `* → revision` | `DecisionRevisionStarted` |
| `* → abandoned` | `DecisionAbandoned` |
| `* → lapsed` | `DecisionLapsed` |
| `* → deserted` | `DecisionDeserted` |
| Feedback soumis | `FeedbackSubmitted` |
| Feedback résolu | `FeedbackResolved` |
| Animateur changé | `DecisionAnimatorChanged` |
