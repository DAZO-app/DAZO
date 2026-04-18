# Étape 11 — Impersonation (Se faire passer pour)

> Date : 17 avril 2026

## Objectif

Permettre aux administrateurs de se faire passer pour n'importe quel autre utilisateur du système afin de faciliter les tests de l'interface utilisateur, tout en conservant la capacité de revenir facilement au mode administrateur. (Impersonation)

## Travaux réalisés

### 1. Implémentation Backend (Sanctum)

Afin d'éviter les problématiques complexes liées à la gestion des sessions lors de l'impersonation via APIs, une approche basée sur des tokens a été utilisée.

- **Nouveau Contrôleur** : `App\Http\Controllers\Api\V1\Admin\ImpersonationController`.
- **Méthode** : `impersonate(User $user)`.
- **Sécurité** : Protégé par le middleware `admin`. Vérifie également activement si l'utilisateur demandeur a le rôle `admin` ou `superadmin`. Vérifie que l'utilisateur cible est "actif".
- **Génération de Token** : Crée un *Personal Access Token* Sanctum spécifique (`impersonation_token`) pour l'utilisateur ciblé, qui sera envoyé au frontend.

### 2. Implémentation Frontend (Pinia Store)

Le store d'authentification (`stores/auth.js`) a été modifié pour gérer un état original (le "vrai" utilisateur).

- **Nouvelles Propriétés** :
  - `originalToken` : Stocke le token d'authentification originel (celui de l'administrateur).
  - Getter `isImpersonating` : Renvoie TRUE si `originalToken` est défini.
- **Action `impersonate(userId)`** :
  1.  Sauvegarde le token actuel dans le `localStorage` sous la clé `dazo_original_token`.
  2.  Appelle le backend pour obtenir le token de l'utilisateur ciblé.
  3.  Écrase temporairement l'entête HTTP d'Autorisation (`Bearer`) et le `dazo_token` avec le nouveau token.
  4.  Recharge l'utilisateur ciblé.
- **Action `stopImpersonating()`** :
  1.  Restaure le token d'origine stocké dans le `localStorage`.
  2.  Supprime les clés relatives à l'impersonation et purge la variable d'état.
  3.  Recharge l'utilisateur (le profil administrateur est restitué).

### 3. Interface Utilisateur (Vue Components)

- **Composant `ImpersonationBanner.vue`** : Un bandeau d'alerte global ajouté à `AppLayout.vue`.
  - Fixé en haut de l'écran (`position: sticky`).
  - Arrière-plan hachuré distinctif (orange/ambre) pour certifier visuellement que l'utilisateur n'agit pas vraiment sous son nom.
  - Contient le nom de l'utilisateur usurpé et un bouton explicite "Revenir à l'administration" (déclenchant `stopImpersonating()`).
- **Composant `AppLayout.vue`** : 
  - Masquage conditionnel des entrées du menu d'Administration (`v-if="isAdmin && !isImpersonating"`) pour être certain de tester fidèlement l'interface comme un utilisateur normal (par défaut un utilisateur normal ne voit pas l'option admin).
- **Composant `AdminUsers.vue`** : 
  - Ajout d'une colonne "Actions".
  - Ajout du bouton "🎭 Simuler" pour chaque ligne correspondant à un utilisateur (sauf pour l'administrateur connecté).

## Vérification globale

Le cycle complet peut être testé :
1.  Connexion en administrateur.
2.  Aller sur "Administration" > "Utilisateurs".
3.  Cliquer sur "Simuler" un autre utilisateur. L'interface se recharge avec ses permissions, et le bandeau d'alerte est bien visible.
4.  Cliquer "Revenir à l'administration" dans le bandeau restitue entièrement l'accès administrateur sans perte de session.

## Fichiers Modifiés

- `routes/api.php`
- `app/Http/Controllers/Api/V1/Admin/ImpersonationController.php` (Nouveau)
- `resources/js/stores/auth.js`
- `resources/js/layouts/AppLayout.vue`
- `resources/js/views/admin/AdminUsers.vue`
- `resources/js/components/ImpersonationBanner.vue` (Nouveau)
- `README.md` (Mise à jour du journal d'implémentation)
