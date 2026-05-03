# Domain Model

> Ce document est la référence textuelle du modèle de données DAZO.
> Les diagrammes PlantUML font autorité pour la modélisation visuelle :
> - [MCD Core](./mcd/dazo_mcd_core.puml) — entités du cycle de décision
> - [MCD Périphérique](./mcd/dazo_mcd_peripherique.puml) — entités de support

---

## Modèle Core

### 👤 Utilisateurs & Authentification

#### `USER`
| Champ | Type | Notes |
|---|---|---|
| `id` | uuid PK | |
| `name` | string | |
| `email` | string | unique |
| `password_hash` | string | nullable |
| `avatar_url` | string | nullable |
| `role` | enum | `superadmin \| admin \| user` |
| `is_global_animator` | boolean | false — animateur disponible sur toute la plateforme |
| `is_active` | boolean | true |
| `email_verified_at` | timestamp | |
| `remember_token` | string | nullable |
| `created_at` | timestamp | |
| `updated_at` | timestamp | nullable |
| `deleted_at` | timestamp | nullable — soft delete, historique conservé, affiché "compte supprimé #ID" |

#### `OAUTH_PROVIDER` *(hors scope V1 — reporté en V2)*
| Champ | Type | Notes |
|---|---|---|
| `id` | uuid PK | |
| `user_id` | uuid FK | → USER |
| `provider` | enum | `decidim \| google \| github` |
| `provider_uid` | string | |
| `created_at` | timestamp | |

> Entité modélisée pour préparer la migration V2 (Socialite).
> Non implémentée en V1 — authentification uniquement par email/mot de passe (Sanctum).

---

### 👥 Cercles

#### `CIRCLE`
| Champ | Type | Notes |
|---|---|---|
| `id` | uuid PK | |
| `parent_id` | uuid FK | nullable → CIRCLE (sous-cercles) |
| `name` | string | |
| `description` | text | nullable |
| `type` | enum | `open \| closed \| observer_open` — open: rejoindre librement; closed: invitation animateur; observer_open: rejoindre en lecture seule |
| `created_by` | uuid FK | → USER |
| `storage_quota_mb` | integer | 500 |
| `storage_used_mb` | integer | 0 |
| `created_at` | timestamp | |
| `updated_at` | timestamp | nullable |

#### `CIRCLE_MEMBER`
| Champ | Type | Notes |
|---|---|---|
| `id` | uuid PK | |
| `circle_id` | uuid FK | → CIRCLE |
| `user_id` | uuid FK | → USER |
| `role` | enum | `member \| animator \| observer` |
| `joined_at` | timestamp | |

| Rôle | Propose/Participe | Vote/Commente | Gère adhésions |
|---|---|---|---|
| `member` | ✅ | ✅ | ❌ |
| `animator` | ✅ | ✅ | ✅ |
| `observer` | ❌ | ❌ | ❌ (lecture seule) |

> Distinct du rôle dans une décision individuelle (`DECISION_PARTICIPANT.role`).
> Les sous-cercles servent à élargir la participation à des personnes extérieures
> au cercle principal — il n'y a pas d'héritage de décisions parent → enfant.

---

### 🧠 Décisions

#### `DECISION`
| Champ | Type | Notes |
|---|---|---|
| `id` | uuid PK | |
| `circle_id` | uuid FK | → CIRCLE — **obligatoire**, toujours rattachée à un cercle |
| `title` | string | |

> `author_id` et `animator_id` ont été **supprimés** de cette table.
> - Auteur courant : `DECISION_PARTICIPANT WHERE role = 'author'` (unique par décision)
> - Animateur courant : `DECISION_PARTICIPANT WHERE role = 'animator'` (unique, nullable)
> - Historique animateur : `DECISION_ANIMATOR_LOG`
| `status` | enum | voir [enums.md](./enums.md) — `DecisionStatus` |
| `visibility` | enum | `public \| private` |
| `priority` | integer | 1–10 |
| `deadline` | timestamp | nullable |
| `suggestions_enabled` | boolean | true |
| `anonymous_votes` | boolean | false |
| `anonymous_public` | boolean | false |
| `emergency_mode` | boolean | false — priorité dans dashboards/notifs |
| `share_count` | integer | 0 — compteur de partages publics |
| `objection_round_deadline` | timestamp | nullable |
| `revision_content` | text | nullable — brouillon préparé par l'auteur en phase révision |
| `revision_attachment_ids` | json | nullable — IDs des pièces jointes prêtes pour la nouvelle version |
| `model_id` | uuid FK | nullable → DECISION_MODEL |
| `created_at` | timestamp | |
| `updated_at` | timestamp | nullable |

> `current_version_id` **supprimé** (évite la FK circulaire) — la version courante est
> déterminée par `DECISION_VERSION.is_current = true`.

#### `DECISION_VERSION`
| Champ | Type | Notes |
|---|---|---|
| `id` | uuid PK | |
| `decision_id` | uuid FK | → DECISION |
| `author_id` | uuid FK | → USER |
| `previous_version_id` | uuid FK | nullable → DECISION_VERSION |
| `version_number` | integer | |
| `is_current` | boolean | false — index partiel `UNIQUE(decision_id) WHERE is_current = true` |
| `content` | text | |
| `change_reason` | text | nullable |
| `created_at` | timestamp | |

#### `DECISION_PARTICIPANT`
| Champ | Type | Notes |
|---|---|---|
| `id` | uuid PK | |
| `decision_id` | uuid FK | → DECISION |
| `user_id` | uuid FK | → USER |
| `role` | enum | `author \| animator \| participant` |
| `added_at` | timestamp | |

> **Contraintes applicatives :**
> - `role = author` : unique par `decision_id` (1 seul auteur)
> - `role = animator` : unique par `decision_id` (1 seul animateur courant, nullable)
> - L'auteur ne participe pas aux phases réaction/feedback en tant que membre ordinaire
> - Tous les membres participants du cercle sont inscrits ici


#### `DECISION_RELATION`
| Champ | Type | Notes |
|---|---|---|
| `id` | uuid PK | |
| `source_decision_id` | uuid FK | → DECISION |
| `target_decision_id` | uuid FK | → DECISION |
| `relation_type` | enum | `derives_from \| blocks` |
| `created_by` | uuid FK | → USER |
| `created_at` | timestamp | |

> Remplace les anciens champs `related_decision_id` + `relation_type` sur DECISION.
> Supporte plusieurs relations par décision.

#### `DECISION_ANIMATOR_LOG`
| Champ | Type | Notes |
|---|---|---|
| `id` | uuid PK | |
| `decision_id` | uuid FK | → DECISION |
| `old_animator_id` | uuid FK | nullable → USER |
| `new_animator_id` | uuid FK | nullable → USER |
| `changed_by` | uuid FK | → USER |
| `reason` | text | nullable |
| `created_at` | timestamp | |

---

### 🗳 Consentement

#### `CONSENT`
| Champ | Type | Notes |
|---|---|---|
| `id` | uuid PK | |
| `decision_version_id` | uuid FK | → DECISION_VERSION |
| `user_id` | uuid FK | → USER — **obligatoire** |
| `signal` | enum | `no_objection \| abstention` |
| `created_at` | timestamp | |

> **Contrainte :** `UNIQUE(decision_version_id, user_id)`
>
> **⚠️ Règle exclusive et non réversible :** pour une version donnée, un membre
> ne peut faire qu'UNE SEULE action parmi :
> 1. Soumettre un `FEEDBACK` (objection ou suggestion)
> 2. Rejoindre un `FEEDBACK` via `FEEDBACK_JOIN`
> 3. Exprimer un `CONSENT` (`no_objection` ou `abstention`)
>
> L'action posée est définitive. Pas de retrait possible.
> L'absence de participation n'équivaut PAS à un consentement — les membres
> sans action peuvent être relancés. Le bypass auteur/animateur reste possible
> mais est tracé et affiché visiblement.

---

### 💬 Feedbacks

#### `FEEDBACK`
| Champ | Type | Notes |
|---|---|---|
| `id` | uuid PK | |
| `decision_version_id` | uuid FK | → DECISION_VERSION |
| `author_id` | uuid FK | nullable (anonyme possible) |
| `type` | enum | `objection \| suggestion` |
| `content` | text | |
| `status` | enum | voir [enums.md](./enums.md) — `FeedbackStatus` |
| `is_anonymous` | boolean | false |
| `created_at` | timestamp | |
| `updated_at` | timestamp | nullable |

> Seuls les feedbacks `type=objection` dans un statut **non-terminal** bloquent l'adoption.
> Statuts non-terminaux : `submitted`, `clarification_requested`, `in_treatment`.

#### `FEEDBACK_JOIN`
| Champ | Type | Notes |
|---|---|---|
| `id` | uuid PK | |
| `feedback_id` | uuid FK | → FEEDBACK |
| `user_id` | uuid FK | → USER |
| `joined_at` | timestamp | |

> Rejoindre un feedback = l'approuver ("like") ET passer son propre tour de soumission.
> **Contrainte :** `UNIQUE(feedback_id, user_id)`

#### `FEEDBACK_MESSAGE`
| Champ | Type | Notes |
|---|---|---|
| `id` | uuid PK | |
| `feedback_id` | uuid FK | → FEEDBACK |
| `author_id` | uuid FK | nullable |
| `content` | text | |
| `created_at` | timestamp | |

> Fil de clarification sur un feedback.
> **Accès restreint :** auteur du feedback, auteur de la décision, animateur.

#### `THREAD_MESSAGE`
| Champ | Type | Notes |
|---|---|---|
| `id` | uuid PK | |
| `decision_id` | uuid FK | → DECISION |
| `author_id` | uuid FK | nullable |
| `tour` | enum | `clarification \| reaction` |
| `content` | text | |
| `is_moderator_note` | boolean | false |
| `created_at` | timestamp | |

---

## Modèle Périphérique

### 🏷 Organisation thématique

#### `CATEGORY`
| Champ | Type | Notes |
|---|---|---|
| `id` | uuid PK | |
| `parent_id` | uuid FK | nullable → CATEGORY (sous-catégories) |
| `name` | string | |
| `description` | text | nullable |
| `created_by` | uuid FK | → USER |
| `created_at` | timestamp | |

> **Scope : global à l'instance** — indépendant des cercles. Gérées par les admins.

#### `LABEL`
| Champ | Type | Notes |
|---|---|---|
| `id` | uuid PK | |
| `name` | string | |
| `color_hex` | string | |
| `created_by` | uuid FK | → USER |
| `created_at` | timestamp | |

> **Scope : global à l'instance** — indépendant des cercles. Gérés par les admins.

#### `DECISION_LABEL` (pivot)
| Champ | Type | Notes |
|---|---|---|
| `decision_id` | uuid FK | → DECISION |
| `label_id` | uuid FK | → LABEL |

#### `DECISION_CATEGORY` (pivot)
| Champ | Type | Notes |
|---|---|---|
| `decision_id` | uuid FK | → DECISION |
| `category_id` | uuid FK | → CATEGORY |

---

### 📎 Pièces jointes

#### `ATTACHMENT`
| Champ | Type | Notes |
|---|---|---|
| `id` | uuid PK | |
| `decision_version_id` | uuid FK | → DECISION_VERSION — **obligatoire** |
| `feedback_id` | uuid FK | nullable → FEEDBACK |
| `filename` | string | |
| `original_name` | string | |
| `mime_type` | string | |
| `file_size_mb` | float | |
| `storage_path` | string | |
| `uploaded_by` | uuid FK | nullable → USER |
| `created_at` | timestamp | |

> `decision_version_id` est toujours renseigné — une pièce jointe est toujours liée à une version.
> `feedback_id` est optionnel : la PJ peut être liée à la version seule, ou à la version ET un feedback.

---

### 🔔 Notifications

#### `NOTIFICATION`
| Champ | Type | Notes |
|---|---|---|
| `id` | uuid PK | |
| `user_id` | uuid FK | nullable |
| `category` | enum | `lifecycle \| feedback \| vote \| admin \| system` |
| `event_type` | enum | voir [enums.md](./enums.md) — `NotificationEventType` |
| `related_decision_id` | uuid FK | nullable |
| `related_feedback_id` | uuid FK | nullable |
| `is_read` | boolean | false |
| `sent_at` | timestamp | nullable |
| `created_at` | timestamp | |

#### `NOTIFICATION_PREFERENCE`
| Champ | Type | Notes |
|---|---|---|
| `id` | uuid PK | |
| `user_id` | uuid FK | → USER |
| `decision_id` | uuid FK | nullable → DECISION |
| `circle_id` | uuid FK | nullable → CIRCLE |
| `category` | enum | même enum que `NOTIFICATION.category` (source : PHP Enum `NotificationCategory`) |
| `email_enabled` | boolean | true |
| `push_enabled` | boolean | false |

> **Priorité de résolution :** décision > cercle > global (null = global)

---

### 📐 Modèles de décision

#### `DECISION_MODEL`
| Champ | Type | Notes |
|---|---|---|
| `id` | uuid PK | |
| `name` | string | |
| `description` | text | nullable |
| `content_template` | text | nullable |
| `default_priority` | integer | nullable |
| `default_visibility` | enum | `public \| private` |
| `suggestions_enabled` | boolean | true |
| `requires_distinct_animator` | boolean | false — si true : notifie les membres du cercle pour se porter volontaires si aucun animateur n'est défini |
| `created_by` | uuid FK | → USER (admin uniquement) |
| `created_at` | timestamp | |
| `updated_at` | timestamp | nullable |

#### `HELP_TEXT`
| Champ | Type | Notes |
|---|---|---|
| `id` | uuid PK | |
| `model_id` | uuid FK | nullable → DECISION_MODEL |
| `field_key` | string | |
| `content` | text | |
| `level` | enum | `instance \| model` — priorité : model > instance |
| `created_by` | uuid FK | → USER |
| `created_at` | timestamp | |

---

### ✉️ Invitations

#### `INVITATION`
| Champ | Type | Notes |
|---|---|---|
| `id` | uuid PK | |
| `email` | string | |
| `token` | string | unique |
| `invited_by` | uuid FK | → USER |
| `circle_id` | uuid FK | nullable → CIRCLE |
| `decision_id` | uuid FK | nullable → DECISION |
| `role` | enum | `member \| animator \| participant` |
| `expires_at` | timestamp | |
| `accepted_at` | timestamp | nullable |
| `created_at` | timestamp | |

---

### ⚙️ Configuration instance

#### `INSTANCE_CONFIG`
Table clé/valeur pour le paramétrage runtime. **Accès via `ConfigService` uniquement (cache Redis).**

| Champ | Type | Notes |
|---|---|---|
| `id` | uuid PK | |
| `key` | string | unique |
| `value` | text | |
| `type` | enum | `boolean \| integer \| float \| string \| json` |
| `description` | text | nullable |
| `updated_at` | timestamp | nullable |

Clés gérées : `use_circles`, `use_categories`, `use_labels`, `default_visibility`,
`session_lifetime_days`, `animator_deadline_days`, `objection_round_ratio`,
`reminder_mid_delay`, `reminder_last_hours`, `reminder_no_deadline_days`,
`max_file_size_mb`, `default_circle_quota_mb`, `mandatory_animator_priority`,
`mandatory_animator_participants`, `dashboard_deadline_warning_days`,
`ai_enabled`, `ai_endpoint`, `ai_model`

---

### 📋 Audit log

#### `APP_LOG`
| Champ | Type | Notes |
|---|---|---|
| `id` | uuid PK | |
| `user_id` | uuid FK | nullable |
| `action` | string | |
| `entity_type` | string | nullable |
| `entity_id` | uuid | nullable |
| `ip_address` | string | nullable |
| `user_agent` | string | nullable |
| `payload` | json | nullable |
| `created_at` | timestamp | |

> Purge automatique après 1 an (conformité LCEN)

---

## 🔗 Résumé des relations

### Core

| Relation | Cardinalité |
|---|---|
| USER → OAUTH_PROVIDER | 1-N |
| USER ↔ CIRCLE via CIRCLE_MEMBER | N-N |
| CIRCLE → DECISION | 1-N (obligatoire) |
| DECISION → DECISION_VERSION | 1-N |
| DECISION ↔ USER via DECISION_PARTICIPANT | N-N |
| DECISION_VERSION → CONSENT | 1-N |
| DECISION_VERSION → FEEDBACK | 1-N |
| FEEDBACK → FEEDBACK_MESSAGE | 1-N |
| FEEDBACK ↔ USER via FEEDBACK_JOIN | N-N |
| DECISION → THREAD_MESSAGE | 1-N |
| DECISION → DECISION_ANIMATOR_LOG | 1-N |
| DECISION ↔ DECISION via DECISION_RELATION | N-N |
| CIRCLE → CIRCLE (parent_id) | auto-ref |
| DECISION_VERSION → DECISION_VERSION (previous) | auto-ref |

### Périphérique

| Relation | Cardinalité |
|---|---|
| DECISION ↔ LABEL via DECISION_LABEL | N-N |
| DECISION ↔ CATEGORY via DECISION_CATEGORY | N-N |
| CATEGORY → CATEGORY (parent_id) | auto-ref |
| DECISION_VERSION → ATTACHMENT | 1-N |
| FEEDBACK → ATTACHMENT | 1-N (optionnel) |
| USER → NOTIFICATION | 1-N |
| USER → NOTIFICATION_PREFERENCE | 1-N |
| DECISION_MODEL → DECISION | 1-N |
| DECISION_MODEL → HELP_TEXT | 1-N |
| USER → INVITATION | 1-N |
