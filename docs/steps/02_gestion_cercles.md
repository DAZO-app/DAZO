# Étape 2 : Gestion des Cercles

> **Statut** : Complété ✅
> **Objectif** : Implémentation du système de cercles, de la gestion des membres, et du système d'invitations sécurisées par Token.

---

## 🛠 Ce qui a été implémenté

### 1. CRUD Cercles et Services dédiés
- **`CircleController`** : Exposé via une `apiResource` (index, store, show, update) dans `routes/api.php` sous le préfixe `/v1/circles`.
- **`CircleService`** : Logique de création de cercle isolée dans ce service. À la création par `store()`, une transaction est levée et le créateur originel est automatiquement inscrit à la table pivot `CircleMember` avec le rôle `ANIMATOR`.
- **Politiques (`CirclePolicy`)** : 
  - La route d'indexation ne retourne que les cercles dont le visiteur est membre.
  - La route de détail (`show`) ainsi que la route de modification (`update`) sont bloquées par Policy selon la présence de l'utilisateur dans le cercle et sa possession ou non du grade `ANIMATOR`.

### 2. Système d'Adhésion (Join, Leave, Exclude)
- Contrôleur **`CircleMemberController`** :
  - **`POST /circles/{id}/join`** : Utilisateur libre de rejoindre uniquement si le cercle n'est pas `CLOSED`. Par défaut typé `MEMBER`, et `OBSERVER` si le cercle est de type `OBSERVER_OPEN`.
  - **`POST /circles/{id}/leave`** : Désinscription simple via le `CircleService`. *(Note: En bloc 3, sera implémentée la règle restreignant un utilisateur de quitter un cercle s'il gère une décision active dedans).*
  - **`GET /circles/{id}/members`** : Liste publique des membres et de leurs rôles pour l'interface UI.
  - **`DELETE /circles/{id}/members/{user}`** : Exclusion stricte, défendue en Policy aux seuls animateurs.

### 3. Invitation Système par Tokens
- Le modèle de base de données d'invitation (précédemment instancié en Étape 0) a été consolidé sous le **`InvitationController`**.
  - **Create (`POST /invitations`)** : Validation de l'utilisateur animateur pour générer un Token Hexadécimal unique de 60 caractères, valable 7 jours. On y attache le rôle pour la future acceptation.
  - **Accept (`POST /invitations/{token}/accept`)** : Validation d'expiration (`expires_at`), d'usage précédent (`used_by` vs null), et d'équivalence Email entre le receveur du mail et l'utilisateur courant. Insère l'utilisateur dans `CircleMember` lors d'un succès.

---

> **Suite naturelle** : [Bloc 3 - Moteur de Décision (Core)](./03_moteur_decision_core.md) *(Prochainement...)*
