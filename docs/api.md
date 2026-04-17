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
| `POST` | `/api/v1/decisions` | Créer une décision |
| `GET` | `/api/v1/decisions/{id}` | Détail d'une décision (version courante) |
| `PUT` | `/api/v1/decisions/{id}` | Modifier les métadonnées (draft uniquement) |

### Versions

| Méthode | Endpoint | Description |
|---|---|---|
| `GET` | `/api/v1/decisions/{id}/versions` | Historique des versions |
| `POST` | `/api/v1/decisions/{id}/versions` | Créer une nouvelle version (phase revision) |
| `GET` | `/api/v1/decisions/{id}/versions/{versionId}` | Détail d'une version |

### Cycle de vie

| Méthode | Endpoint | Description |
|---|---|---|
| `POST` | `/api/v1/decisions/{id}/transition` | Déclencher une transition d'état |
| `POST` | `/api/v1/decisions/{id}/abandon` | Abandonner une décision |

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
| `POST` | `/api/v1/invitations` | Envoyer une invitation |
| `POST` | `/api/v1/invitations/{token}/accept` | Accepter une invitation |

---

## ⚙️ Administration

| Méthode | Endpoint | Description |
|---|---|---|
| `GET` | `/api/v1/admin/config` | Lire la configuration instance |
| `PUT` | `/api/v1/admin/config/{key}` | Modifier une clé de configuration |
| `GET` | `/api/v1/admin/categories` | Lire les catégories |
| `POST` | `/api/v1/admin/categories` | Créer une catégorie |
| `GET` | `/api/v1/admin/labels` | Lire les labels |
| `POST` | `/api/v1/admin/labels` | Créer un label |
| `GET` | `/api/v1/admin/models` | Lire les modèles de décision |
| `POST` | `/api/v1/admin/models` | Créer un modèle de décision |

---

## ⚠️ Notes

- Tous les endpoints sont préfixés `/api/v1/` — versionnage dès la V1
- Authentification via token Sanctum dans le header `Authorization: Bearer {token}`
- Conçu pour SPA et usage mobile
- L'API évoluera — les versions majeures breaking seront `/api/v2/`
- Les réponses d'erreur suivent le format standard Laravel (`message`, `errors`)
