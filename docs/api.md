# API — Draft v1

> Base URL : `/api/v1`
> Authentification : Laravel Sanctum (Bearer token)
> Format : JSON

---

## 🔐 Auth

| Méthode | Endpoint | Description |
|---|---|---|
| `POST` | `/api/v1/auth/register` | Inscription email + mot de passe |
| `POST` | `/api/v1/auth/login` | Connexion — retourne un token Sanctum |
| `POST` | `/api/v1/auth/logout` | Révocation du token courant |
| `GET` | `/api/v1/auth/me` | Profil utilisateur courant |
| `POST` | `/api/v1/auth/email/verify/{id}/{hash}` | Vérification email |
| `POST` | `/api/v1/auth/email/resend` | Renvoyer l'email de vérification |
| `GET` | `/api/v1/magic-login/{userId}` | Connexion via Magic Link (Public/Admin) |
| `POST` | `/api/v1/admin/impersonate/{userId}` | Prendre le contrôle d'un utilisateur |
| `POST` | `/api/v1/admin/stop-impersonation` | Arrêter l'impersonation |

> **OAuth (Google, GitHub, Decidim) — reporté en V2.** L'entité `OAUTH_PROVIDER`
> est modélisée dans le MCD mais n'est pas implémentée en V1.

---

## 👥 Cercles

| Méthode | Endpoint | Description |
|---|---|---|
| `GET` | `/api/v1/circles` | Liste des cercles accessibles |
| `POST` | `/api/v1/circles` | Créer un cercle |
| `GET` | `/api/v1/circles/{id}` | Détail d'un cercle |
| `PUT` | `/api/v1/circles/{id}` | Modifier un cercle |
| `POST` | `/api/v1/circles/{id}/join` | Rejoindre un cercle open |
| `POST` | `/api/v1/circles/{id}/leave` | Quitter un cercle |
| `GET` | `/api/v1/circles/{id}/members` | Liste des membres |
| `DELETE` | `/api/v1/circles/{id}/members/{userId}` | Retirer un membre (animateur) |

---

## 🧠 Décisions

| Méthode | Endpoint | Description |
|---|---|---|
| `GET` | `/api/v1/decisions` | Liste des décisions accessibles |
| `GET` | `/api/v1/decisions/{id}` | Détail d'une décision (version courante) |
| `PUT` | `/api/v1/decisions/{id}` | Modifier un brouillon (titre, contenu, animateur, exclusions) OU enregistrer un brouillon de révision en phase `revision` (`content`, `revision_attachment_ids`). Le contenu du brouillon est stocké dans `revision_content` sur le modèle Decision. |
| `DELETE` | `/api/v1/decisions/{id}` | Supprimer un brouillon |
| `POST` | `/api/v1/circles/{circleId}/decisions` | Créer une décision dans un cercle |

### Versions

| Méthode | Endpoint | Description |
|---|---|---|
| `GET` | `/api/v1/decisions/{id}/versions` | Historique des versions |
| `POST` | `/api/v1/decisions/{id}/versions` | Créer une nouvelle version (phase revision). Les colonnes `revision_content` et `revision_attachment_ids` du modèle Decision sont utilisées pour initialiser la version, puis réinitialisées. |
| `GET` | `/api/v1/decisions/{id}/versions/{versionId}` | Détail d'une version (inclut `attachments`, `feedbacks`, `consents` et `participation_stats`). |

### Cycle de vie

| Méthode | Endpoint | Description |
|---|---|---|
| `POST` | `/api/v1/decisions/{id}/transition` | Déclencher une transition d'état |
| `POST` | `/api/v1/decisions/{id}/abandon` | Abandonner une décision |

### Participants en attente & Relances

| Méthode | Endpoint | Description |
|---|---|---|
| `GET` | `/api/v1/decisions/{id}/pending-participants` | Liste des participants qui n'ont pas encore répondu dans la phase courante |
| `POST` | `/api/v1/decisions/{id}/remind` | Envoyer un email de relance aux participants en attente (porteur ou animateur uniquement) |

> ⚠️ **Rate limit** : `POST /remind` est limité à **5 req/min** par IP.

### Pièces jointes

| Méthode | Endpoint | Description |
|---|---|---|
| `POST` | `/api/v1/attachments` | Upload d'une pièce jointe. Liée directement si `decision_version_id` fourni, sinon retourne l'ID pour liaison ultérieure (ex: phase révision). |
| `GET` | `/api/v1/attachments/{id}/download` | Téléchargement sécurisé — vérifie les droits sur la décision parente avant de servir le fichier |
| `POST` | `/api/v1/decisions/versions/{versionId}/attachments/link` | Lier des uploads existants à une version |
| `DELETE` | `/api/v1/attachments/{attachmentId}` | Supprimer une pièce jointe |

> ⚠️ Les fichiers sont stockés en **disque local privé** (jamais accessibles directement via URL publique).
> Les extensions dangereuses (`.php`, `.sh`, `.exe`...) et les MIME types exécutables sont refusés.
> La taille maximale est configurable via `ConfigService` (clé `max_file_size_mb`, défaut : 10 Mo).

### Animateur

| Méthode | Endpoint | Description |
|---|---|---|
| `PUT` | `/api/v1/decisions/{id}/animator` | Désigner / changer l'animateur |
| `GET` | `/api/v1/decisions/{id}/animator/history` | Historique des animateurs |

### Participants

| Méthode | Endpoint | Description |
|---|---|---|
| `GET` | `/api/v1/decisions/{id}/participants` | Liste des participants explicites |
| `POST` | `/api/v1/decisions/{id}/participants` | Ajouter un participant |

---

## 💬 Feedback

| Méthode | Endpoint | Description |
|---|---|---|
| `GET` | `/api/v1/decisions/{id}/feedback` | Liste des feedbacks (version courante) |
| `POST` | `/api/v1/decisions/{id}/feedback` | Soumettre un feedback |
| `GET` | `/api/v1/decisions/{id}/feedback/{feedbackId}` | Détail d'un feedback |
| `PUT` | `/api/v1/decisions/{id}/feedback/{feedbackId}/status` | Changer le statut d'un feedback |

### Fil de discussion (Feedback Messages)

| Méthode | Endpoint | Description |
|---|---|---|
| `GET` | `/api/v1/feedback/{feedbackId}/messages` | Messages du fil |
| `POST` | `/api/v1/feedback/{feedbackId}/messages` | Poster un message |

### Soutien (Join)

| Méthode | Endpoint | Description |
|---|---|---|
| `POST` | `/api/v1/feedback/{feedbackId}/join` | Rejoindre / soutenir un feedback |
| `DELETE` | `/api/v1/feedback/{feedbackId}/join` | Retirer son soutien |

---

## 🗳 Consentement

| Méthode | Endpoint | Description |
|---|---|---|
| `POST` | `/api/v1/decisions/{id}/versions/{versionId}/consent` | Exprimer un signal (`no_objection` / `abstention`) |
| `GET` | `/api/v1/decisions/{id}/versions/{versionId}/consent` | Résumé des consentements |
| `DELETE` | `/api/v1/decisions/{id}/versions/{versionId}/consent` | Retirer son signal |

---

## 💬 Thread (Clarification / Réaction)

| Méthode | Endpoint | Description |
|---|---|---|
| `GET` | `/api/v1/decisions/{id}/thread` | Messages du thread (toutes phases) |
| `POST` | `/api/v1/decisions/{id}/thread` | Poster un message (`tour` = clarification ou reaction) |

---

## 🔔 Notifications

| Méthode | Endpoint | Description |
|---|---|---|
| `GET` | `/api/v1/notifications` | Liste des notifications |
| `POST` | `/api/v1/notifications/{id}/read` | Marquer comme lue |
| `POST` | `/api/v1/notifications/read-all` | Marquer toutes comme lues |
| `GET` | `/api/v1/notifications/preferences` | Préférences de notification |
| `PUT` | `/api/v1/notifications/preferences` | Mettre à jour les préférences |

---

## ✉️ Invitations

| Méthode | Endpoint | Description |
|---|---|---|
| `POST` | `/api/v1/invitations` | Envoyer une invitation (un seul email) |
| `POST` | `/api/v1/invitations/batch` | Envoyer des invitations groupées (emails séparés par `;`) |
| `PUT` | `/api/v1/invitations/{token}/resend` | Renvoyer un email d'invitation |
| `DELETE` | `/api/v1/invitations/{token}` | Supprimer/Annuler une invitation pendante |
| `GET` | `/api/v1/invitations/{token}` | Valider un token et obtenir les détails (Public) |
| `POST` | `/api/v1/invitations/{token}/accept` | Accepter une invitation (Nécessite authentification) |

---

## ⚙️ Administration

| Méthode | Endpoint | Description |
|---|---|---|
| `GET` | `/api/v1/admin/config` | Lire la configuration instance |
| `PUT` | `/api/v1/admin/config` | Mise à jour groupée de la configuration |
| `POST` | `/api/v1/admin/config/logo` | Upload du logo de l'instance |
| `GET` | `/api/v1/admin/categories` | Lire les catégories |
| `POST` | `/api/v1/admin/categories` | Créer une catégorie |
| `GET` | `/api/v1/admin/labels` | Lire les labels |
| `POST` | `/api/v1/admin/labels` | Créer un label |
| `GET` | `/api/v1/admin/models` | Lire les modèles de décision |
| `POST` | `/api/v1/admin/models` | Créer un modèle de décision |

---

## 🛠 Outils Admin

| Méthode | Endpoint | Description |
|---|---|---|
| `GET` | `/api/v1/admin/tools/database` | Statistiques des tables SQL |
| `GET` | `/api/v1/admin/tools/database/backups` | Liste des sauvegardes SQL |
| `POST` | `/api/v1/admin/tools/database/backups` | Créer une sauvegarde à la volée |
| `GET` | `/api/v1/admin/tools/database/backups/{filename}/download` | Télécharger un backup (URL signée) |
| `DELETE` | `/api/v1/admin/tools/database/backups/{filename}` | Supprimer un backup |
| `GET` | `/api/v1/admin/tools/server` | Monitoring CPU/RAM/Disque |
| `GET` | `/api/v1/admin/tools/server/logs` | Lire les logs Laravel |

---

## ⚡ Rate Limiting

| Endpoint | Limite |
|---|---|
| `POST /remind` | 5 req/min |
| `POST /feedback`, `POST /consent`, `POST /join` | 10 req/min |
| `POST /transition`, `POST /abandon`, `PUT /feedback/{id}/status` | 20 req/min |
| `POST /attachments` | 20 req/min |
| `POST /feedback/{id}/messages` | 30 req/min |

---

## 📡 Temps réel (WebSockets — Laravel Reverb)

DAZO utilise **Laravel Reverb** comme serveur WebSocket et **Laravel Echo** côté frontend.

### Canal privé utilisateur
```
App.Models.User.{userId}
```

### Événements broadcastés sur ce canal (work in progress)

| Event | Payload | Description |
|---|---|---|
| `.decision.transitioned` | `{ decision_id, new_status }` | Une décision a changé de phase |
| `.feedback.submitted` | `{ decision_id, feedback_id }` | Un nouveau feedback soumis |
| `.feedback.status.updated` | `{ feedback_id, status }` | Statut d'un feedback mis à jour |
| `.consent.submitted` | `{ decision_id, user_id }` | Un signal de consentement exprimé |

> Lancer le serveur WebSocket : `php artisan reverb:start`

---

## ⚠️ Notes

- Tous les endpoints sont préfixés `/api/v1/` — versionnage dès la V1
- Authentification via token Sanctum dans le header `Authorization: Bearer {token}`
- Conçu pour SPA et usage mobile
- L'API évoluera — les versions majeures breaking seront `/api/v2/`
- Les réponses d'erreur suivent le format standard Laravel (`message`, `errors`)
- Le détail d'une décision retourne aussi les pièces jointes de la version courante, les consentements, les feedbacks de la version et une matrice de participation par phase pour alimenter l'interface de suivi
