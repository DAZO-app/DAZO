# ROADMAP

## ✅ V1 — MVP (Completed)

### Core Features
- [x] Users & authentication (Sanctum)
- [x] Circles & memberships (Animator, Member, Observer)
- [x] Decision creation & rich editing (Quill)
- [x] Full Decision lifecycle (Spatie State Machine)
- [x] Feedback system (Clarifications, Objections, Suggestions)
- [x] Consent system (No objection, Abstention)
- [x] Decision versioning & historical navigation
- [x] Participation tracking & progress bar
- [x] Email invitations & Secure Onboarding (v2)

### Technical
- [x] Database schema (Stable)
- [x] Service layer architecture
- [x] RESTful API (v1)
- [x] Real-time Dashboard with participation stats

---

## 🚀 V2 — Expansion & Administration (In Progress)

### Administration & Surveillance
- [x] High-fidelity Admin Dashboard
- [x] Server Monitoring (CPU, RAM, Logs peak)
- [x] Database Management (Tables, Backup history)
- [ ] Automated Rolling Backups (Engine implementation)
- [ ] Table Repair & Optimization tools (CLI & UI)

### Intégration & API Publique
- [x] API XML publique pour intégration CMS tiers (WP, Drupal)
- [x] Sécurisation par Clé d'API (générée via interface Admin)
- [x] Configuration fine de publication (Cercles, Catégories, Statuts)
- [x] Toggle public/privé sur les décisions individuelles

### Social Features & Onboarding
- [x] Re-designed Invitation Bridge
- [x] Batch invitations (CSV/Separator upload)
- [x] Invitation management (Resend, Cancel, Expire)
- [x] Impersonation for debugging (Admin only)
- [x] Real-time notifications (WebSockets — Laravel Reverb)

### Decision Governance UX
- [x] Feedback thread visual redesign (role colors, avatar icons)
- [x] Blinking indicator for threads requiring action
- [x] Post-discussion consensus display ("Validé après clarification")
- [x] Decision header toolbar (Meeting mode, Remind, Print)
- [x] Participant reminder modal (manual email send)

### Performance & Security
- [x] Fix N+1 queries in PendingCountsController
- [x] Fix N+1 queries in HasUserActionStatus trait
- [x] Fix Decision::$appends serialization queries
- [x] Rate limiting on sensitive endpoints
- [x] Attachment security hardening (extension/MIME blocking, no public direct access)
- [x] Configurable allowed file types (admin config)

### Code Quality
- [x] DecisionStatus::getPhaseConfig() — single source of truth for phase logic
- [x] DecisionService centralized (getPhaseParticipationMap, getPendingUsers)
- [x] Pinia store getters (isAuthor, isAnimator, participationPercent, etc.)
- [x] Feature test suite: PendingCounts, AttachmentSecurity, DecisionFlow, FeedbackService
- [x] Unit test suite: DecisionStatus enum helpers

---

## 📋 Backlog V2 — À implémenter

### Interface & UX
- [ ] **Mode Réunion** — Vue "secrétaire de séance" simplifiée pour projection
- [ ] **Export PDF** — Génération serveur via `spatie/browsershot` (remplace `window.print()`)
- [ ] **Optimistic UI** — Mise à jour locale immédiate sur votes et consents
- [ ] **Vue Calendrier** — Afficher les deadlines sur un calendrier (porteurs/animateurs)
- [ ] **Recherche globale** — Fulltext search sur décisions, feedbacks

### Backend
- [ ] **API Resources Laravel** — `DecisionResource`, `FeedbackResource`, `UserResource`
- [ ] **Broadcasting Events** — Brancher events existants sur canaux Reverb (pour mise à jour auto des décisions en temps réel)
- [ ] **Préférences de notification** — Audit des Listeners pour vérifier le respect des préférences
- [ ] **Relations entre décisions** — Exposer `DecisionRelation` en front
- [ ] **Labels** — Utiliser `Label.php` et `decision_labels` pivot
- [ ] **HelpText** — Exposer en front (aide contextuelle)
- [ ] **Backups automatiques** — Scheduler Laravel

### Administration
- [ ] **Types de fichiers admin** — UI dans AdminConfig.vue pour gérer `allowed_file_types` et `max_file_size_mb`
- [ ] **Vue tests admin** — Afficher/lancer la suite de tests depuis l'interface admin

---

## 🌍 V3 — Advanced Intelligence
- [ ] AI assistant (Decision summarizer & Conflict detection)
- [ ] Advanced permissions (ACL / Role Builder)
- [ ] Multi-tenancy (SaaS architecture)
- [ ] Billing & Subscription management
- [ ] Decidim & GitHub OAuth integrations
- [ ] Table Repair & Optimization tools (CLI & UI)
