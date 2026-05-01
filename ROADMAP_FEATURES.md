# 🗺️ ROADMAP FEATURES — DÉCOUPAGE POUR CONCEPTION

**Format:** Phases priorisées avec features groupées  
**Cible:** Guide pour sessions de développement itératives  
**Horizon:** Pre-bêta → Post-bêta

---

## PRINCIPES DE PRIORISATION

1. **Critique** — Bloque d'autres features ou risque l'expérience utilisateur
2. **Haute** — Visible par utilisateur final, attendu pour bêta
3. **Moyenne** — Amélioration UX/admin, peut attendre post-bêta
4. **Basse** — Nice-to-have, post-bêta + longterm

---

## PHASE 1 — STABILITÉ PRÉ-BÊTA (Semaine 1-2)

> **Priorité:** 🔴 CRITIQUE  
> **Complexité:** Moyenne  
> **Dépendances:** Aucune  
> **Timeline:** 5-7 jours  

**Objectif:** Valider et hardener les fonctionnalités core avant bêta externe.

**Features à concevoir/valider:**

- [ ] **Load Testing & Performance Optimization** — Dashboard user
  - **Vue:** Dashboard.vue
  - **Description:** Benchmark app sous 500 utilisateurs simultanés; identifier bottlenecks; implémenter caching si needed
  - **Effort:** M

- [ ] **Email Delivery Validation** — Notifications + Auth
  - **Vue:** AuthController, NotificationController
  - **Description:** Test real SMTP delivery (reset password, invitations, notifications); validate templates rendering
  - **Effort:** S

- [ ] **Mobile Responsiveness Testing** — All public/auth views
  - **Vue:** PublicLayout, Login, Register, PublicFront, PublicDecisionDetail
  - **Description:** Test all pages on iOS Safari + Android Chrome; fix layout breaks if any
  - **Effort:** M

- [ ] **File Upload Real-world Testing** — Attachments
  - **Vue:** DecisionCreate, DecisionDetail
  - **Description:** Test 100MB file edge cases; validate S3 path handling; test download/preview on mobile
  - **Effort:** S

- [ ] **Backup/Restore DR Test** — AdminDatabase
  - **Vue:** AdminDatabase
  - **Description:** Full database backup creation → restore to test DB; verify data integrity
  - **Effort:** M

- [ ] **Admin Impersonation Edge Cases** — AuthStore, AppLayout
  - **Vue:** Dashboard, AppLayout (impersonation banner)
  - **Description:** Test impersonation state persistence; logout while impersonating; permission layering
  - **Effort:** S

**Success Criteria:**
- ✅ App stable under 500 concurrent users (response time < 500ms)
- ✅ All auth emails delivered in < 2min
- ✅ Mobile layout no layout breaks on 375px width
- ✅ File uploads/downloads work at 100MB+
- ✅ DR test passes (backup restore verified)

---

## PHASE 2 — PUBLIC API (Week 2)

> **Priorité:** 🟠 HAUTE  
> **Complexité:** Moyenne  
> **Dépendances:** Phase 1 ✅  
> **Timeline:** 3-4 jours  

**Objectif:** Implement secure public API for external integrations.

**Features à concevoir:**

- [ ] **API Key Generation (Server-side Secure)** — AdminPublication
  - **Vue:** AdminPublication.vue
  - **Controller:** AdminConfigController
  - **Description:** Replace client-side random generation with server-side secure token generation; store in DB; show once only
  - **API Endpoints:**
    - `POST /api/v1/admin/config/api-key/generate` — Generate new key
    - `DELETE /api/v1/admin/config/api-key` — Revoke key
  - **Effort:** S

- [ ] **Public API Filtering Validation** — PublicDecisionController
  - **Vue:** Backend only (but AdminPublication UI depends)
  - **Description:** Verify `/api/v1/public/decisions` respects scope (circles, categories, statuses) from AdminPublication config
  - **Tests:**
    - Scope enforcement (should not return decisions outside selected circles)
    - Category filtering (should respect configured categories)
    - Status filtering (should respect configured statuses)
  - **Effort:** M

- [ ] **Public API Authentication Middleware** — Middleware
  - **Description:** Create middleware to check API key header; fail with 403 if invalid/missing
  - **Logic:**
    - Accept header: `X-API-Key: {key}`
    - Validate against stored DB key
    - Log access attempts
  - **Effort:** S

- [ ] **Public API XML/JSON Format Support** — PublicDecisionController
  - **View:** responses/public-decisions.xml, responses/public-decisions.json
  - **Description:** Support both XML and JSON output for public API (based on Accept header)
  - **Effort:** S

**Success Criteria:**
- ✅ API key generated server-side; never transmitted in code
- ✅ `/api/v1/public/decisions?api_key={key}` returns only decisions in scope
- ✅ Invalid keys rejected with 403
- ✅ Both XML and JSON formats work

---

## PHASE 3 — UX POLISH (Week 3)

> **Priorité:** 🟡 MOYENNE  
> **Complexité:** Faible  
> **Dépendances:** Phase 1 ✅  
> **Timeline:** 2-3 jours  

**Objectif:** Fix cosmetic issues and improve UX polish before beta feedback.

**Features à concevoir:**

- [ ] **Share Functionality** — PublicFront, PublicDecisionDetail
  - **Vue:** PublicFront.vue, PublicDecisionDetail.vue
  - **Description:** Implement share modal with:
    - Twitter share button (pre-filled with title + decision URL)
    - LinkedIn share button
    - Facebook share button
    - Copy URL to clipboard button
    - Email share option (generate mailto: link)
  - **Component:** New `ShareModal.vue`
  - **Effort:** S

- [ ] **Toast Notifications** — App-wide
  - **Vue:** All views with alerts/errors
  - **Description:** Replace browser `alert()` with toast notification system
  - **Candidates to replace:**
    - WikiEditor.vue (line 155: `alert('Succès...')`)
    - AdminWiki.vue (various error alerts)
    - AdminPublication (form save feedback)
  - **Component:** Use existing notification pattern or add `useToast()` composable
  - **Effort:** S

- [ ] **Empty State Illustrations** — All list views
  - **Vue:** DecisionList, CircleList, WikiIndex
  - **Description:** Add SVG illustrations for empty states (no decisions, no circles, etc.)
  - **Effort:** XS

- [ ] **Loading Skeleton States** — Dashboard, DecisionList
  - **Vue:** Dashboard.vue, DecisionList.vue
  - **Description:** Add skeleton placeholders while data loads (instead of blank space)
  - **Effort:** S

- [ ] **Breadcrumb Navigation** — Nested views
  - **Vue:** CircleDetail, DecisionDetail, WikiDetail
  - **Description:** Add breadcrumb trails showing: Home > Circle > Decision (for mobile orientation)
  - **Effort:** XS

**Success Criteria:**
- ✅ Share buttons visible on public pages
- ✅ No more browser alerts (all replaced with toasts)
- ✅ Loading states clear (users see progress, not blank screen)

---

## PHASE 4 — ADMIN CONVENIENCE TOOLS (Week 4)

> **Priorité:** 🟡 MOYENNE  
> **Complexité:** Faible  
> **Dépendances:** Phase 1 ✅  
> **Timeline:** 2-3 jours  

**Objectif:** Complete admin tooling for database management and automation.

**Features à concevoir:**

- [ ] **SQL Import Endpoint** — AdminDatabase
  - **Vue:** AdminDatabase.vue
  - **Controller:** AdminDatabaseToolController
  - **Description:** Allow admins to upload and execute SQL files for data migration
  - **API Endpoint:**
    - `POST /api/v1/admin/tools/database/import` — Upload SQL file; execute; return results
  - **Validation:**
    - File must be .sql
    - Max 10MB
    - Execute in transaction (rollback on error)
    - Log all executions
  - **UI:** Dropzone already exists in AdminDatabase.vue (lines 129-135); just connect handler
  - **Effort:** S

- [ ] **Auto-backup Scheduling** — AdminDatabase
  - **Controller:** AdminDatabaseToolController
  - **Description:** Allow admins to configure automatic daily backups at specific time
  - **API Endpoint:**
    - `PUT /api/v1/admin/tools/database/auto-backup` — Save scheduling config
    - `GET /api/v1/admin/tools/database/auto-backup/status` — Check last run
  - **Backend:** Use Laravel scheduler (app/Console/Kernel.php) to trigger backups daily
  - **UI:** Form already in AdminDatabase.vue (lines 149-169); just connect save handler
  - **Effort:** M

- [ ] **Table Optimization** — AdminDatabase
  - **View:** AdminDatabase.vue
  - **Controller:** AdminDatabaseToolController
  - **Description:** Add button handler to optimize tables (OPTIMIZE TABLE via MySQL)
  - **API Endpoint:**
    - `POST /api/v1/admin/tools/database/optimize` — Trigger optimization for all tables
  - **UI:** Button exists (line 56); just add @click handler
  - **Effort:** XS

**Success Criteria:**
- ✅ SQL imports execute without error
- ✅ Auto-backup runs daily at configured time
- ✅ Table optimization completes in < 30 seconds

---

## PHASE 5 — DECISION WORKFLOW ENHANCEMENTS (Week 5+)

> **Priorité:** 🔵 BASSE  
> **Complexité:** Élevée  
> **Dépendances:** Phase 1 ✅  
> **Timeline:** 1-2 semaines  

**Objectif:** Advanced decision workflow features (post-bêta).

**Features à concevoir:**

- [ ] **Scheduled Transitions** — DecisionDetail
  - **Description:** Schedule automatic phase transitions (e.g., "move to reaction phase on 2026-05-15")
  - **Controllers:** DecisionController (new endpoint)
  - **Effort:** M

- [ ] **Decision Templates** — DecisionCreate
  - **Description:** Admins can create decision templates (pre-filled title, content, categories) for reuse
  - **Controllers:** DecisionTemplateController (new)
  - **Effort:** M

- [ ] **Bulk Invite to Decision** — DecisionDetail, CircleDetail
  - **Description:** Quickly invite entire circle to a decision with pre-configured role
  - **Effort:** S

- [ ] **Decision Duplicate** — DecisionList
  - **Description:** Clone existing decision (copy title/content/categories) to new circle
  - **Effort:** S

- [ ] **Feedback Analytics** — DecisionDetail
  - **Description:** Show aggregate stats: % agreed, % disagreed, avg participation by role
  - **Effort:** M

**Success Criteria:**
- ✅ Transitions can be scheduled
- ✅ Templates work and save time
- ✅ Analytics show clear participation picture

---

## PHASE 6 — REPORTING & EXPORT (Week 6+)

> **Priorité:** 🔵 BASSE  
> **Complexité:** Élevée  
> **Dépendances:** Phase 1 ✅  
> **Timeline:** 1-2 semaines  

**Objectif:** Export and reporting features for admins and users.

**Features à concevoir:**

- [ ] **Decision Export (PDF)** — DecisionDetail
  - **Description:** Export full decision with all feedback as PDF
  - **Library:** Use `laravel-dompdf` or similar
  - **Effort:** M

- [ ] **Decisions Export (CSV)** — DecisionList
  - **Description:** Export filtered decision list to CSV (title, status, author, dates, participation count)
  - **Effort:** S

- [ ] **Circle Report (PDF)** — CircleDetail
  - **Description:** Generate PDF report: circle info, members, decision summary, participation stats
  - **Effort:** M

- [ ] **Admin Analytics Dashboard** — AdminDashboard
  - **Description:** Add charts: decisions over time, participation trends, most active users, slowest decisions (time to adoption)
  - **Effort:** L

**Success Criteria:**
- ✅ PDF exports format correctly
- ✅ CSV opens in Excel without encoding issues
- ✅ Charts update in real-time

---

## PHASE 7 — INTEGRATIONS (Week 7+)

> **Priorité:** 🔵 BASSE  
> **Complexité:** Élevée  
> **Dépendances:** Phase 2 (Public API) ✅  
> **Timeline:** 2-3 semaines  

**Objectif:** Extend DAZO via external integrations.

**Features à concevoir:**

- [ ] **Webhook Support** — Core
  - **Description:** Send webhooks on decision state changes, feedback added, phase transitions
  - **Controllers:** WebhookController
  - **UI:** Admin page to configure webhook URLs + test delivery
  - **Effort:** L

- [ ] **Slack Integration** — Notifications
  - **Description:** Send decision notifications to Slack channels
  - **Effort:** M

- [ ] **Microsoft Teams Integration** — Notifications
  - **Description:** Send notifications to Teams channels
  - **Effort:** M

- [ ] **RSS Feeds** — Public
  - **Description:** Public RSS feed for decisions (by circle, category, or global)
  - **Effort:** S

- [ ] **IFTTT/Zapier Support** — Via webhooks
  - **Description:** Document and support IFTTT/Zapier automation
  - **Effort:** S

**Success Criteria:**
- ✅ Webhooks fire on all key events
- ✅ Slack channel receives notifications
- ✅ RSS feed valid and parseable

---

## TIMELINE VISUELLE

```
Week 1-2:  PHASE 1 (Stabilité)       ████████
Week 2:    PHASE 2 (Public API)       ██████
Week 3:    PHASE 3 (UX Polish)        ███████
Week 4:    PHASE 4 (Admin Tools)      ███████
Week 5+:   PHASE 5+ (Enhancements)    ████████████...
```

**Path to Beta:** PHASE 1 + PHASE 2 + PHASE 3 (1-3 semaines)

**Path to v1.1:** PHASE 1-7 (4-8 semaines)

---

## MATRICE PRIORISATION

| Phase | Feature | Criticité | Complexité | Timeline | Bloquant? |
|-------|---------|-----------|-----------|----------|-----------|
| 1 | Load Testing | 🔴 Critique | M | 5-7j | OUI |
| 1 | Email Validation | 🔴 Critique | S | 3-5j | OUI |
| 1 | Mobile Testing | 🔴 Critique | M | 3-5j | OUI |
| 2 | API Key Generation | 🟠 Haute | S | 1-2j | OUI (pour API) |
| 2 | Public API Filtering | 🟠 Haute | M | 2-3j | OUI (pour API) |
| 3 | Share Feature | 🟡 Moyenne | S | 4h | NON |
| 3 | Toast Notifications | 🟡 Moyenne | S | 2h | NON |
| 4 | SQL Import | 🟡 Moyenne | S | 2h | NON |
| 4 | Auto-backup | 🟡 Moyenne | M | 1-2j | NON |
| 5 | Scheduled Transitions | 🔵 Basse | M | 3-5j | NON |
| 6 | PDF Export | 🔵 Basse | M | 2-3j | NON |
| 7 | Webhooks | 🔵 Basse | L | 1 semaine | NON |

---

## DÉPENDANCES CRITIQUES

```
PHASE 1 (Stabilité)
    ↓
PHASE 2 (Public API) — depends on Phase 1
PHASE 3 (UX Polish) — depends on Phase 1
    ↓
PHASE 4 (Admin Tools) — can parallel with Phase 3
    ↓
PHASE 5+ (Enhancements) — depends on Phase 1 + 2
```

**Chemin critique:** Phase 1 → Phase 2 (if public API needed) → Phase 3  
**Parallélizable:** Phase 3 et Phase 4 peuvent être simultanées

---

## NOTES DE CONCEPTION

### Pour chaque feature:
1. **Wireframe ou mockup** — Avant de coder
2. **API contract** — Endpoint, params, response format
3. **Data model changes** — Si migrations DB needed
4. **Test plan** — Unit + integration + e2e si applicable
5. **Documentation** — User-facing + admin-facing if needed

### Exemple workflow:
```
1. Design (wireframe in Figma)
2. Backend API contract (Postman collection)
3. Database migrations (if needed)
4. Backend implementation + tests
5. Frontend component + tests
6. E2E testing
7. Documentation
```

---

**Prochaine étape:** Voir `ROADMAP_BETA.md` pour sprint beta immédiate (2-3 semaines).
