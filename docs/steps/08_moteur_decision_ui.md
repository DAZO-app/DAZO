# Étape 8 : Moteur de Décision (Composants UI)

> **Statut** : Complété ✅ (Couvre le Bloc 12 de la Roadmap Front)
> **Objectif** : Implémenter les vues réelles de l'application chargées de requêter l'API DAZO, en reproduisant dynamiquement le contenu du mockup.

---

## 🛠 Ce qui a été implémenté

### 1. Store Vue (Pinia) des Décisions
- **`stores/decision.js`** : Service d'état détenant `decisions` (liste globale) et `currentDecision` (Focus unique). 
- **Actions** : `fetchDecisions()` charge l'index, tandis que `fetchDecisionById(id)` popule les détails profonds depuis l'API, avec une gestion intégrée des erreurs d'authentification (`Axios`).

### 2. Liste Globale (`DecisionList.vue`)
- Convertit le **Mockup VUE 2** en composant asynchrone dynamique.
- **Rendu** : Affiche les métadonnées de base (Cercle affilié, priorité, tags).
- **Badges de Statuts Intelligents** : Le helper `statusClass()` applique les couleurs sémantiques précises dictées par la charte (ex: `objection` = badge-amber, `adopted` = badge-teal, `draft` = badge-gray) et les estampilles.
- Route correspondante : `/decisions`

### 3. Le Tunnel de Décision (`DecisionDetail.vue`)
- Vue maîtresse décortiquant une proposition. Convertit le **Mockup VUE 3 et 4**.
- **La Timeline Dynamique** : Une barre de progression des 5 étapes structurelles (`clarification -> reaction -> objection -> !! adopté !!`). Une fonction `getStepClass()` résout temporellement l'emplacement actuel de la proposition par rapport à tous les autres statuts (validé vs en-attente vs actif).
- **Cartes D'actions Intelligentes** : 
  - *Affichage conditionnel* : Si le serveur renvoie l'état `objection`, l'interface déverrouille magiquement le pupitre de vote (Aktion `"Soumettre un consentement"` OU `"Soumettre une objection restreinte"`).
- **Intégration d'Adoption** : Le bouton "Sans objection" (Consentement pur) déclenche instantanément la requête `POST /api/v1/decisions/{id}/versions/{version_id}/consent` via Axios HTTP, alerte l'utilisateur et recharge le state propulsé par Laravel.
- Route correspondante : `/decisions/:id`

> **Prochaine étape** : L'Étape 9 "Interactions Poussées", visant à implémenter dans cette même `DecisionDetail` le fil de discussion (Composant Thread ping-pong) et le formulaire/modale d'envoi de Feedback.
