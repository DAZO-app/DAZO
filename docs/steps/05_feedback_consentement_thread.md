# Étape 5 : Feedback, Consentements et Débat (Thread)

> **Statut** : Complété ✅ (Couvre les Blocs 5, 6 et 7 de la Roadmap originale)
> **Objectif** : Implémentation du cœur participatif d'une décision : Fils de discussion informels (Clarification/Réaction) et Dépôt de Feedbacks et Consentements formels exclusifs (Phase d'Objection).

---

## 🛠 Ce qui a été implémenté

### 1. Fil de discussion Global (`ThreadMessageController`)
- **Isolation d'Accès** : Les messages du Thread classique sont validés par un `CreateThreadMessageRequest` qui vérifie que la Décision est bien dans une étape dédiée au débat oral (`CLARIFICATION` ou `REACTION`).
- **Permissions** : Les membres "Observer" de cercle ne peuvent **pas** poster ici, ils ne font qu'historiser en lecture.
- **Modération** : L'animateur (et le `global_animator`) a le droit de tagger son message avec `"is_moderator_note": true`, affiché distinctement sur l'UI avec une coloration spéciale.

### 2. Dépôt de Feedback et Logique Exclusive
- **`FeedbackController`** gère le dépôt d'un feedback de type `objection` ou `suggestion`. Ce droit est limité à la phase officielle `OBJECTION`.
- **`FeedbackService`** (Règles Métier) : Implémente la règle de sécurité centrale des modèles d'objection DAZO : un même utilisateur **ne peut pas cumuler** un Feedback unique, un Consentement, et un Soutien (`FeedbackJoin`) pendant un tour donné (`DecisionVersion`). S'il utilise déjà son jeton sur l'un, utiliser l'autre renvoie une erreur `422 ValidationException`.
- **Création Automatique de Participant** : Le tout premier acte (Poster feedback ou consentir ou rejoindre un projet) crée instantanément un `DecisionParticipant` attribuant le Grade de base (valeur enum `participant`).

### 3. Cycle de Vie Interne d'une Objection
- Les statuts de l'Enum `FeedbackStatus` peuvent être modifiés (`updateStatus`) par les modérateurs, passant un point de "Soumis" (Submitted) vers "Traité" (Treated) ou "Ignoré".
- **Interaction Locale (Mini-thread)** : `FeedbackMessageController` permet une discussion compartimentée sous le coup d'une demande de clarification spécifique à une objection précise (les auteurs respectifs y débattant).
- ⚙️ **Automate d'Adoption** : Dès qu'une modification d'état advient, `FeedbackService::checkAndAdoptIfNoBlockingObjections()` est invoquée. Elle scrute si tout le pool de statuts d'une décision s'avère "terminal ou non bloquant". Si c'est le cas, elle passe par-dessus la Machine d'États et force la décision globale au statut terminal `ADOPTED` !

### 4. Consentement formel (Abstention et No Objection)
- **`ConsentController`** : L'utilisateur n'ayant ni objection ni intention de soutenir publiquement, cède la parole via `no_objection` ou s'inhibe via `abstention`. Les mêmes règles de non-cumul que le `FeedbackService` s'appliquent sur sa version.

---

> **Suite naturelle** : Bloc 8 & 9 - Nouvelles itérations de Version / Interface Utilisateur Front-end *(Prochainement...)*
