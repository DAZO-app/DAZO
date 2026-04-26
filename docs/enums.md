# Enums — Référence centrale

> Toutes les valeurs d'énumération du projet DAZO.
> Implémentées en PHP 8.1+ (`enum NomEnum: string`).
> Ce fichier fait autorité — tout ajout de valeur doit être fait ici en premier.

---

## 👤 USER

### `UserRole`
| Valeur | Description |
|---|---|
| `superadmin` | Accès technique complet (config, infra, migrations) |
| `admin` | Accès fonctionnel complet : gestion des cercles, utilisateurs, modèles, catégories, labels |
| `user` | Utilisateur standard |

> `is_global_animator` (boolean, indépendant du rôle) : peut être appelé en renfort
> sur n'importe quelle décision/cercle, même sans en être membre.

### `OAuthProvider`
| Valeur | Description |
|---|---|
| `decidim` | Authentification via Decidim |
| `google` | Google OAuth |
| `github` | GitHub OAuth |

---

## 👥 CIRCLES

### `CircleType`
| Valeur | Description |
|---|---|
| `open` | Tout utilisateur peut rejoindre librement comme membre |
| `closed` | Accès uniquement sur invitation de l'animateur du cercle |
| `observer_open` | Tout utilisateur peut rejoindre comme observateur (lecture seule) |

> Les sous-cercles servent à élargir la participation sur un périmètre de décisions
> (pas d'héritage automatique de décisions parent → enfant).

### `CircleMemberRole`
| Valeur | Peut proposer/participer | Peut voter/commenter | Gère les adhésions |
|---|---|---|---|
| `member` | ✅ | ✅ | ❌ |
| `animator` | ✅ | ✅ | ✅ |
| `observer` | ❌ | ❌ | ❌ (lecture seule) |

> Distinct du rôle dans une décision individuelle (`DECISION_PARTICIPANT.role`).

---

## 🧠 DECISIONS

### `DecisionStatus`
| Valeur | Type | Description |
|---|---|---|
| `draft` | Pré-actif | Brouillon — visible auteur + animateur uniquement |
| `suspended` | Pré-actif | Suspendu temporairement |
| `clarification` | Phase active | Phase de questions/clarifications |
| `reaction` | Phase active | Phase de ressenti personnel (CNV) |
| `objection` | Phase active | Phase d'objections, suggestions, consentements |
| `revision` | Actif | Rédaction d'une nouvelle version |
| `adopted` | Terminal ✅ | Adopté proprement — 0 objection bloquante |
| `adopted_override` | Terminal ✅⚠️ | Adopté avec objections restantes — affiché de façon distincte |
| `abandoned` | Terminal ❌ | Annulé volontairement |
| `lapsed` | Terminal ❌ | Délai dépassé sans complétion |
| `deserted` | Terminal ❌ | Aucune activité pendant la période configurée |

#### Méthodes helper (source de vérité)

| Méthode | Retour | Description |
|---|---|---|
| `getPhaseConfig()` | `array\|null` | Retourne `['feedback_types' => [], 'consent_signals' => []]` pour les phases actives, `null` sinon. **Seule source de vérité** pour mapper phase → types. |
| `isActivePhase()` | `bool` | `true` pour clarification, réaction, objection |
| `isTerminal()` | `bool` | `true` pour adopted, adopted\_override, abandoned, lapsed, deserted |

```php
// Usage type dans les services et controllers :
$config = $decision->status->getPhaseConfig();
if ($config) {
    $feedbackTypes = $config['feedback_types'];   // ex: ['objection', 'suggestion']
    $consentSignals = $config['consent_signals']; // ex: ['no_objection', 'abstention']
}
```

### `DecisionVisibility`
| Valeur | Description |
|---|---|
| `public` | Visible par tous les membres de l'instance |
| `private` | Visible uniquement par les membres du cercle et participants désignés |

### `DecisionRelationType`
| Valeur | Description |
|---|---|
| `derives_from` | Cette décision est dérivée d'une autre |
| `blocks` | Cette décision bloque une autre |

### `DecisionParticipantRole`
| Valeur | Unique par décision | Description |
|---|---|---|
| `author` | ✅ Oui | Auteur de la décision — ne participe pas aux phases réaction/feedback |
| `animator` | ✅ Oui (nullable) | Animateur courant — peut évoluer (cf. DECISION_ANIMATOR_LOG) |
| `participant` | ❌ Multiple | Membre du cercle participant normalement à toutes les phases |
| `excluded` | ❌ Multiple | Membre explicitement exclu de cette décision (ne vote pas, non compté) |

---

## 🗳 CONSENT

### `ConsentSignal`
| Valeur | Description |
|---|---|
| `no_questions` | Le membre n'a plus de questions (phase clarification) |
| `no_reaction` | Le membre a exprimé son avis (phase réaction) |
| `no_objection` | Le membre consent — pas d'objection sur cette version |
| `abstention` | Le membre s'abstient |

> **Règle exclusive et non réversible :** pour une version donnée, un membre ne peut
> faire qu'UNE SEULE action parmi : soumettre un FEEDBACK, rejoindre un FEEDBACK (JOIN),
> ou exprimer un CONSENT. L'action posée est définitive.
>
> Les objections sont exprimées via `FEEDBACK (type=objection)`, pas via `CONSENT`.

---

## 💬 FEEDBACKS

### `FeedbackType`
| Valeur | Phase | Bloque l'adoption | Description |
|---|---|---|---|
| `clarification` | Clarification | ❌ Non | Question ou demande de précision |
| `reaction` | Réaction | ❌ Non | Ressenti / avis personnel (CNV) |
| `objection` | Objection | ✅ Oui (si non-terminal) | Objection formelle dans l'intérêt du groupe |
| `suggestion` | Objection | ❌ Non | Proposition d'amélioration non bloquante |

### `FeedbackStatus`
| Valeur | Terminal | Acteur(s) autorisé(s) | Description |
|---|---|---|---|
| `submitted` | Non | — | Soumis, en attente |
| `clarification_requested` | Non | Auteur décision, Animateur | Précision demandée |
| `in_treatment` | Non | Auteur décision, Animateur | Prise en charge |
| `treated` | ✅ Oui | Auteur feedback (obj.), Auteur décision / Animateur (sugg.) | Traitement terminé |
| `rejected` | ✅ Oui | Auteur décision, Animateur | Rejeté (non recevable) |
| `acknowledged` | ✅ Oui | Auteur décision, Animateur | Pris en compte sans traitement |
| `withdrawn` | ✅ Oui | Auteur du feedback uniquement | Retiré volontairement |
| `ignored` | ✅ Oui | Auteur décision, Animateur | Ignoré |

> Statuts **bloquants pour l'adoption** (non-terminaux sur un `type=objection`) :
> `submitted`, `clarification_requested`, `in_treatment`

---

## 💬 THREAD

### `ThreadTour`
| Valeur | Phase | Auteurs autorisés |
|---|---|---|
| `clarification` | Phase clarification | Tous les membres (sauf observer) |
| `reaction` | Phase réaction | Tous les membres (sauf observer) |

---

## 🔔 NOTIFICATIONS

### `NotificationCategory`
| Valeur | Description |
|---|---|
| `lifecycle` | Transitions de la décision |
| `feedback` | Objections, suggestions, réponses, soutiens |
| `vote` | Consentements en attente, relances |
| `admin` | Invitations, validation email, animateur sollicité |
| `system` | Messages système |

> Source de vérité PHP partagée entre `NOTIFICATION.category`
> et `NOTIFICATION_PREFERENCE.category`.

### `NotificationEventType`
| Valeur | Catégorie | Description |
|---|---|---|
| `new_decision` | `lifecycle` | Nouvelle décision dans le cercle |
| `new_version` | `lifecycle` | Nouvelle version d'une décision |
| `decision_adopted` | `lifecycle` | Décision adoptée (proprement) |
| `decision_adopted_override` | `lifecycle` | Décision adoptée avec forçage |
| `decision_abandoned` | `lifecycle` | Décision abandonnée |
| `decision_lapsed` | `lifecycle` | Décision expirée (délai) |
| `decision_deserted` | `lifecycle` | Décision désertée (inactivité) |
| `objection_submitted` | `feedback` | Nouvelle objection soumise |
| `suggestion_submitted` | `feedback` | Nouvelle suggestion soumise |
| `feedback_replied` | `feedback` | Réponse sur un feedback |
| `feedback_joined` | `feedback` | Quelqu'un a rejoint un feedback |
| `participation_reminder` | `vote` | Relance de participation (consentement en attente) |
| `animator_invoked` | `admin` | Animateur sollicité (requires_distinct_animator) |
| `email_validation` | `admin` | Validation d'email |
| `invitation` | `admin` | Invitation reçue |

---

## ⚙️ INSTANCE CONFIG

### `ConfigValueType`
| Valeur | Description |
|---|---|
| `boolean` | `"true"` / `"false"` |
| `integer` | Entier |
| `float` | Nombre décimal |
| `string` | Chaîne de caractères |
| `json` | Structure JSON sérialisée |

---

## 📐 DECISION MODELS

### `HelpTextLevel`
| Valeur | Priorité | Description |
|---|---|---|
| `instance` | Fallback | Texte d'aide global à l'instance |
| `model` | Prioritaire | Texte d'aide spécifique à un modèle — prime sur `instance` |

---

## ✉️ INVITATIONS

### `InvitationRole`
| Valeur | Description |
|---|---|
| `member` | Invité comme membre du cercle |
| `animator` | Invité comme animateur du cercle |
| `observer` | Invité comme observateur du cercle |
| `participant` | Invité comme participant à une décision spécifique |
