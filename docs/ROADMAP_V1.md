# ROADMAP V1 — DAZO (Statut : TERMINÉ ✅)

Toutes les briques fondamentales du moteur de décision sociocratique et de l'interface ont été implémentées, testées et harmonisées.

## ✅ Bloc 0 — Fondations & Structure
- [x] Initialisation Laravel 11.
- [x] Architecture API UUID-based.
- [x] Système d'audit log (`app_logs`).

## ✅ Bloc 1 — Authentification
- [x] Sanctum integration (Login/Register/Logout).
- [x] Rôles utilisateurs (SuperAdmin, Admin, User).
- [x] Sécurisation des mots de passe (Visual Strength Meter + Backend Rules).

## ✅ Bloc 2 — Cercles
- [x] CRUD Cercles & Types (Open, Closed).
- [x] Gestion des membres (Animateur, Membre, Observateur).
- [x] Invitations groupées par email (v2).

## ✅ Bloc 3 — Décision CRUD
- [x] Brouillon & Édition riche (Quill).
- [x] Gestion des participants explicites et par rôle.

## ✅ Bloc 4 — Machine d'États
- [x] Cycle de vie complet (Draft, Clarification, Reaction, Objection, Adoption, Revision).
- [x] Transitions manuelles et automatiques.

## ✅ Bloc 5 — Threads (Clarification/Réaction)
- [x] Échanges de messages par phase.
- [x] Notes de modérateur (Animateur).

## ✅ Bloc 6 & 7 — Feedback & Consentement
- [x] Moteur d'objections & suggestions.
- [x] Signaux de consentement (`no_objection`, `abstention`).
- [x] Règles d'exclusivité (un seul signal par version).

## ✅ Bloc 8 & 9 — Adoption & Révision
- [x] Adoption automatique sans objection.
- [x] Création de révision avec report sélectif des pièces jointes.

## ✅ Bloc 10 — Notifications
- [x] Système de notification in-app.
- [x] Emails automatiques via queue (Invitation, Transitions).

## ✅ Bloc 12 — Administration
- [x] Configuration dynamique de l'instance.
- [x] Dashboard de surveillance (Database & Server status).
- [x] Gestion des catégories et répertoires utilisateurs.

## ✅ Bloc 14 — Onboarding (Nouveau)
- [x] Pont public de validation d'invitation.
- [x] Inscription pré-remplie depuis une invitation.
- [x] Gestion avancée des invitations (Relancer, Annuler).
