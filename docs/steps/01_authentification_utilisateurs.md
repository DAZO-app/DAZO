# Étape 1 : Authentification & Utilisateurs

> **Statut** : Complété ✅
> **Objectif** : Implémentation du système d'inscription, connexion via tokens API et gestion du profil utilisateur en suivant le fichier API Docs.

---

## 🛠 Ce qui a été implémenté

### 1. Inscription et Connexion API (Sanctum)
Création complète du contrôleur `AuthController` (dans `app/Http/Controllers/Api/V1`) intégrant :
- **Register (`POST /api/v1/auth/register`)** : Valide les paramètres via `RegisterRequest`, gère le mot de passe (hachage par défaut via Laravel `Hash`), déploie l'enum utilisateur (`role = user`, `is_active = true`) et émet automatiquement la notification d'email de vérification. Il retourne le `user` et le **Bearer Token**.
- **Login (`POST /api/v1/auth/login`)** : Valide le mail/password, vérifie que le compte est bien actif (sinon status `403`), et retourne un token Sanctum.
- **Logout (`POST /api/v1/auth/logout`)** : Révoque spécifiquement le "current access token".

### 2. Validation par Email
- Une notification native est envoyée via l'interface `MustVerifyEmail` qui a été simulée en appel direct `sendEmailVerificationNotification()`.
- Endpoint de retour `POST /api/v1/auth/email/verify/{id}/{hash}` (qui valide la signature SHA1 native).
- Endpoint pour renvoyer le mail : `POST /api/v1/auth/email/resend`.

### 3. Middleware `ActiveUser`
Création et enregistrement de `active` alias vers le middleware `App\Http\Middleware\ActiveUser` injecté globalement sur l'ensemble des routes sécurisées (`routes/api.php` protégé par `['auth:sanctum', 'active']`).
Ce middleware refuse `403` si `$request->user()->is_active === false`.

### 4. Profil Utilisateur
Routage complet via `ProfileController` :
- **Me (`GET /api/v1/auth/me`)** : Renvoie simplement l'utilisateur courant instancié par la demande du Token Sanctum.
- **Update (`PUT /api/v1/auth/me`)** : Valide `name` ou `avatar_url` (via `UpdateProfileRequest`) et met à jour uniquement ces champs, de maniètre sécurisée.

---

> **Suite naturelle** : [Bloc 2 - Gestion des Cercles](./02_gestion_cercles.md) *(Prochainement...)*
