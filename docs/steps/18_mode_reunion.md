# Étape 18 — Mode Réunion High-Performance

## Objectif
Implémenter une interface de pilotage haute performance pour les réunions de décision sociocratique, permettant une immersion totale et une gestion centralisée par le secrétaire de séance.

## Architecture UI/UX
Le Mode Réunion est conçu comme une surcouche (Overlay) plein écran activable depuis la vue détail d'une décision.

### 1. MeetingModeOverlay.vue
- **Plein écran (Simulé)** : Masquage de la navigation standard pour une immersion maximale.
- **Header Dynamique** : Changement de couleur (dégradé) selon la phase active de la décision.
- **Affichage Épuré** : Contenu de la décision et pièces jointes (via PhotoSwipe) accessibles sans distraction.
- **Système d'Échanges** : Refonte des threads de discussion sous forme d'onglets ("Segmented Controls") avec bordures colorées par rôle.

### 2. MeetingSecretaryPanel.vue (Le "Cerveau")
- **Panel Flottant Draggable** : Permet au secrétaire de piloter la séance tout en gardant un œil sur le contenu.
- **Tour de Table Dynamique** : Liste filtrée en temps réel des participants n'ayant pas encore réagi dans la phase en cours.
- **Saisie par Procuration** : Fonctionnalité permettant au secrétaire de saisir un retour (Clarification, Réaction, Objection) au nom d'un participant sélectionné.
- **Actions Rapides** : Bouton unique de validation du signal de consentement (ex: "Valider RAS", "Aucune objection") adapté à la phase.
- **Historique & Annulation** : Bouton "Annuler la dernière action" (avec support du raccourci `Ctrl+Z`) pour corriger les erreurs de saisie en séance.

## Choix Techniques
- **CSS Vanilla (Dazo-Theme)** : Refus d'introduire Tailwind CSS pour maintenir la cohérence avec le design system existant et éviter les régressions visuelles. Utilisation de variables CSS et de dégradés natifs.
- **Synchronisation d'État** : Rafraîchissement forcé (`window.location.reload()`) à la fermeture du mode réunion pour garantir que l'interface standard reflète exactement les changements effectués pendant la réunion.
- **Gestion des Événements** : Utilisation intensive de `@refresh-data` pour remonter les changements d'état (transitions, nouveaux messages) entre les composants imbriqués.

## Évolution Future
- Intégration plus poussée des WebSockets pour refléter les saisies du secrétaire instantanément sur les écrans de tous les participants connectés.
- Mode "Présentateur" dédié à la projection sur grand écran.
