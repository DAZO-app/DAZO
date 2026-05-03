# ROADMAP V1 — DAZO (Statut : TERMINÉ ✅)

Toutes les briques fondamentales du moteur de décision sociocratique, de l'interface admin et des outils de maintenance ont été implémentées, testées et harmonisées.

## ✅ Bloc 0 — Fondations & Structure
- [x] Initialisation Laravel 11.
- [x] Architecture API UUID-based.
- [x] Système d'audit log (`app_logs`).

## ✅ Bloc 1 — Authentification & Accès
- [x] Sanctum integration (Login/Register/Logout).
- [x] Rôles utilisateurs (SuperAdmin, Admin, User).
- [x] **Magic Link Login** : Connexion simplifiée par email.
- [x] **Impersonation** : Prise de contrôle par les administrateurs.

## ✅ Bloc 2 — Cercles
- [x] CRUD Cercles & Types (Open, Closed).
- [x] Gestion des membres (Animateur, Membre, Observateur).
- [x] Invitations groupées par email.

## ✅ Bloc 3 — Décision CRUD
- [x] Brouillon & Édition riche (Quill).
- [x] Gestion des participants explicites et par rôle.

## ✅ Bloc 4 — Machine d'États (Standard)
- [x] Cycle de vie complet (Draft, Clarification, Reaction, Objection, Adoption, Revision).
- [x] Transitions manuelles et automatiques.

## ✅ Bloc 5 — Threads & Discussion
- [x] Échanges de messages par phase.
- [x] Notes de modérateur (Animateur).
- [x] Intégration des participations directes dans le flux (RAS, OK).

## ✅ Bloc 6 & 7 — Feedback & Consentement
- [x] Moteur d'objections & suggestions.
- [x] Signaux de consentement (`no_objection`, `abstention`).
- [x] Règles d'exclusivité (un seul signal par version).

## ✅ Bloc 8 & 9 — Adoption, Révision & Fichiers
- [x] Adoption automatique sans objection.
- [x] Création de révision avec report sélectif des pièces jointes.
- [x] Gestionnaire asynchrone d'upload de fichiers.

## ✅ Bloc 10 — Notifications & Relances
- [x] Système de notification in-app.
- [x] Emails automatiques via queue (Invitation, Transitions).
- [x] **Relances Automatiques** : Notifications 24h avant échéance de phase.

## ✅ Bloc 12 — Administration & Monitoring
- [x] **Dashboard de surveillance** : État CPU, RAM, Disque et Logs.
- [x] **Gestion BDD** : Liste des tables et système de backups compressés.
- [x] **Configuration Globale** : Gestion de l'identité (Instance Name, Logo) et des délais de phase.
- [x] Répertoire utilisateurs & gestion des invitations.

## ✅ Bloc 14 — Onboarding & UX
- [x] Parcours complet d'invitation (Public bridge).
- [x] Refonte Dashboard Premium (Urgences, Logos filigrane).
- [x] Navigation transverse (Urgentes / Expirées).

## ✅ Bloc 17 — Wiki & Aide
- [x] **Centre d'Aide Intégré** : Wiki thématique avec moteur de recherche.
- [x] **Administration du contenu** : Éditeur riche pour la rédaction de guides par les admins.
- [x] **Design Premium** : Layout bi-colonne pour une lecture fluide des ressources.

## ✅ Bloc 18 — Mode Réunion & Expérience Avancée
- [x] **Interface plein écran épurée** : Immersion totale pour les réunions de décision.
- [x] **Panneau Secrétaire intelligent** : Pilotage centralisé des feedbacks et transitions.
- [x] **Pièces jointes flottantes** : Gestion multi-fenêtres (déplacement, redimensionnement, maximisation) intégrée au plein écran.
- [x] **Saisie par procuration** : Possibilité pour le secrétaire de saisir les retours au nom des participants.
- [x] **Impression PDF avancée** : Filtrage par version, séparation claire objections/suggestions, et affichage des soutiens ("rejoint par").
- [x] **Design Harmonisé** : En-têtes à dégradés dynamiques selon la phase et bordures de rôles stylisées.

## ✅ Bloc 19 — Authentification & Accès (Évolutions)
- [x] **Social Login** : Connexion via fournisseurs tiers (Google, Microsoft, Github).
- [x] Maintien du support de mot de passe par défaut.

## ✅ Bloc 20 — Interface Publique & Partage Social
- [x] **Consultation Publique** : Interface dédiée pour les décisions ouvertes au grand public.
- [x] **Filtrage Avancé** : Recherche cumulative par thématique, cercle et phase.
- [x] **Partage Social Premium** : Popin de partage avec prévisualisation dynamique et tracking des intentions.
- [x] **Intégration Admin** : Visualisation du taux de partage dans le back-office.

## 🚀 Prochaines Étapes (V1.5 / V2)
- [ ] **Snippet Generator** : Générateur d'extraits HTML/JS pour l'intégration web facilitée.
- [ ] **Notification Push** : Intégration Firebase pour alertes mobiles en temps réel.
- [ ] **Recherche multi-critères avancée** sur l'interface publique.
- [ ] **API Publique (V2)** : Support des webhooks pour notifier les CMS tiers.
