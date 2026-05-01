# 🔒 ROADMAP RGPD — CONFORMITÉ LÉGALE FRANCE/UE

**Cible:** Conformité RGPD + CNIL + Lois France (Loi Informatique et Libertés, loi RGPD)  
**Horizon:** 3-4 semaines pour audit + implementation  
**Législation:** RGPD (UE 2016/679), CNIL France, Loi Informatique & Libertés, LCEN

---

## ANALYSE RGPD INITIAL

### Risques identifiés:

#### 🔴 Critiques (non-conformités majeures):
1. **Pas de Privacy Policy publique** — Obligatoire (Article 13/14 RGPD)
2. **Pas de Terms of Service** — Obligatoire pour légalité des CGU
3. **Pas de Cookie consent** — Obligatoire en France/UE (CNIL, LCEN)
4. **Pas de Data Processing Agreement (DPA)** — Si données hébergées chez tiers
5. **Pas de user consent tracking** — Impossible de prouver consentement (Article 7 RGPD)
6. **Pas de droit à l'oubli UI** — Article 17 RGPD (Droit à l'oubli)
7. **Pas de data export/portabilité** — Article 20 RGPD (Droit à la portabilité)
8. **Pas de retention policy** — Données doivent être supprimées après X jours

#### 🟠 Importants (non-conformités modérées):
9. **Pas de DPO (Data Protection Officer) contact** — Recommandé pour org publique
10. **Pas de data breach notification process** — Article 33 RGPD
11. **Pas de DPIA (Data Protection Impact Assessment)** — Si données sensibles
12. **No OAuth consent screen** — Should state what data is collected
13. **No encryption for sensitive data** — Passwords, tokens, personal data
14. **No logging of data access** — For audit trail
15. **No third-party data sharing consent** — Article 6 RGPD (if sharing data)

---

## PHASE 1 — DOCUMENTATION LÉGALE (Semaine 1)

> **Priorité:** 🔴 CRITIQUE  
> **Complexité:** Faible (writing, not coding)  
> **Dépendances:** Aucune  
> **Timeline:** 3-5 jours  

**Features à implémenter:**

- [ ] **Privacy Policy** — Legal document
  - **File:** `resources/views/privacy-policy.blade.php` (or public/privacy.html)
  - **Content required (RGPD Article 13):**
    - Who is the data controller (your company/org)
    - What data is collected (name, email, decisions, etc.)
    - Why data is collected (legal basis: consent, contract, legitimate interest)
    - How long data is stored (retention policy)
    - What rights users have (access, delete, port, object)
    - How to exercise rights (contact email)
    - Who to contact for privacy issues (DPO if applicable)
    - Third-party data sharing (Google OAuth, etc.)
    - Cookies/tracking used
    - Data transfers outside EU (if any)
  - **Template:** Use CNIL template or hire legal consultant
  - **Must be:**
    - Linked from footer (public pages)
    - Accessible to all users
    - Written in French (if targeting France)
  - **Effort:** S

- [ ] **Terms of Service (ToS) / Conditions d'Utilisation** — Legal document
  - **File:** `resources/views/terms-of-service.blade.php`
  - **Content required:**
    - Service description
    - User responsibilities
    - Content ownership (who owns decisions? circles?)
    - Liability limitations
    - Termination conditions
    - Dispute resolution
    - Changes to ToS
  - **Must be:**
    - Accepted during registration (checkbox)
    - Versioned (show ToS version in profile)
    - Linked from login/register pages
  - **Effort:** S

- [ ] **Cookie Policy** — Legal disclosure
  - **File:** `resources/views/cookie-policy.blade.php`
  - **Content required:**
    - What cookies are used
    - Purpose of each cookie (session, analytics, tracking)
    - Third-party cookies (if any)
    - User consent options
    - How to manage/delete cookies
  - **Linked from:** Cookie banner + footer
  - **Effort:** S

- [ ] **Data Processing Agreement (DPA)** — If using third parties
  - **Applies if:**
    - Using AWS/Azure/hosting provider (they're data processors)
    - Using email service (SendGrid, Mailgun)
    - Using analytics (Google Analytics if collecting PII)
  - **Must document:**
    - What data is processed
    - Where is it stored
    - Security measures
    - Data subprocessors
  - **File:** Keep internally, reference in Privacy Policy
  - **Effort:** S

- [ ] **Data Retention Policy** — Internal document
  - **Define:**
    - User account data: delete after X months of inactivity? Or indefinitely?
    - Decision data: delete after X years?
    - Logs: delete after 90 days?
    - Backups: how long retained?
  - **Legal requirement:** RGPD Article 5 (storage limitation)
  - **File:** `docs/DATA_RETENTION_POLICY.md`
  - **Effort:** XS

- [ ] **DPIA (Data Protection Impact Assessment)** — If processing sensitive data
  - **Triggers:**
    - Large-scale processing of personal data
    - Automated decision-making
    - Systematic monitoring
    - Processing of special categories (race, religion, health, etc.)
  - **DAZO consideration:** Likely triggers if decisions contain personal opinions/data
  - **Template:** Use CNIL template
  - **File:** `docs/DPIA.md`
  - **Effort:** M

**Success Criteria:**
- ✅ Privacy Policy published and accessible
- ✅ ToS published and agreed during signup
- ✅ Cookie Policy published
- ✅ DPA signed with all data processors
- ✅ Retention policy defined
- ✅ DPIA completed (if applicable)

---

## PHASE 2 — USER CONSENT & COOKIE BANNER (Semaine 1-2)

> **Priorité:** 🔴 CRITIQUE  
> **Complexité:** Moyenne  
> **Dépendances:** Phase 1 ✅  
> **Timeline:** 4-5 jours  

**Features à implémenter:**

- [ ] **Cookie Consent Banner** — Frontend
  - **Views:** PublicLayout.vue, AppLayout.vue
  - **Requirements (CNIL, LCEN):**
    - Banner appears on first visit
    - Shows what cookies are used (essential, analytics, marketing)
    - "Accept All" and "Reject All" buttons (equal prominence)
    - "Customize" option to select granular consent
    - Consent must be stored (Article 7 RGPD)
    - Can withdraw consent anytime
  - **Categories:**
    - Essential (session cookies) — NO CONSENT NEEDED
    - Analytics (Google Analytics) — CONSENT REQUIRED
    - Marketing/Tracking (if any) — CONSENT REQUIRED
  - **Implementation:**
    - Use library: `vue-gtag` (for Google Analytics) + `cookie-consent` component
    - Store consent in localStorage or cookie
    - Respect DNT (Do Not Track) header
  - **Example flow:**
    ```
    1. User visits
    2. Banner appears: "We use cookies. Accept? [Accept All] [Reject] [Customize]"
    3. User clicks "Customize"
    4. Modal shows: Essential (ON, disabled), Analytics (toggle), Marketing (toggle)
    5. User clicks "Save Preferences"
    6. Consent stored + banner hidden
    7. User can manage consent in Settings
    ```
  - **Effort:** M

- [ ] **ToS Acceptance During Signup** — Register.vue
  - **Changes:**
    - Add checkbox: "I agree to Terms of Service" (mandatory)
    - Add link to ToS (modal or new tab)
    - Track acceptance in DB (users table, field: `accepted_terms_at` timestamp)
    - Add acceptance version (in case ToS changes)
  - **Validation:**
    - Checkbox must be checked to submit form
    - Backend validates acceptance on POST `/api/v1/auth/register`
  - **Effort:** S

- [ ] **Privacy & Cookie Consent During Signup** — Register.vue
  - **Changes:**
    - Add checkbox: "I agree to Privacy Policy" (mandatory)
    - Track in DB (users table, field: `accepted_privacy_at` timestamp)
    - Add checkbox: "I consent to cookies" (optional, but recommend accepting)
  - **Data model:**
    ```
    users table:
    - accepted_terms_at (timestamp, nullable)
    - accepted_privacy_at (timestamp, nullable)
    - accepted_cookies_at (timestamp, nullable)
    ```
  - **Effort:** S

- [ ] **Consent Management in Settings** — Settings.vue
  - **New section:** "Privacy & Consent"
  - **Options:**
    - View & download Privacy Policy (link)
    - View & download ToS (link)
    - Change cookie preferences (modal to customize)
    - Withdraw all consents (warning: some services may not work)
    - View what data is stored about user (data export preview)
  - **Effort:** M

- [ ] **Cookie Consent Persistence** — Frontend
  - **Changes:**
    - Store consent choices in localStorage
    - Key: `consent_preferences` = `{essential: true, analytics: false, marketing: false}`
    - Check on every page load
    - Update when user changes preferences
  - **Effort:** S

**Success Criteria:**
- ✅ Cookie banner appears on first visit
- ✅ User can accept/reject/customize
- ✅ Consent stored and respected
- ✅ ToS checkbox during signup
- ✅ Settings page shows consent history
- ✅ Users can withdraw consent anytime

---

## PHASE 3 — USER DATA RIGHTS (Semaine 2-3)

> **Priorité:** 🟠 HAUTE  
> **Complexité:** Moyenne  
> **Dépendances:** Phase 1 ✅  
> **Timeline:** 5-7 jours  

**Features à implémenter:**

- [ ] **Right to Access (Article 15)** — Data export
  - **Endpoint:** `POST /api/v1/auth/export-data`
  - **Action:** User clicks "Download my data" in Settings
  - **Output:** JSON or CSV file containing:
    - User profile (name, email, phone if stored)
    - All decisions authored/participated
    - All feedback submitted
    - All consent timestamps
    - Login history (if tracked)
  - **Format:** Machine-readable (JSON), plus human-readable PDF summary
  - **Timeline:** Deliver within 30 days (RGPD Article 15)
  - **Code changes:**
    - Controller: `ProfileController@exportData`
    - Generate JSON with all user data
    - Return as downloadable file
  - **Effort:** M

- [ ] **Right to Erasure (Article 17) — "Right to be Forgotten"** — Account deletion
  - **UI:** Settings.vue → "Delete my account" button (bottom, red button)
  - **Flow:**
    1. User clicks "Delete account"
    2. Confirmation modal: "Are you sure? This is permanent."
    3. User enters password to confirm
    4. Account and associated data deleted
  - **What gets deleted:**
    - User profile
    - All decisions authored (anonymize content? or delete?)
    - All feedback (anonymize or delete?)
    - All consents/tokens
    - All logs containing user data
  - **What stays (legal obligation):**
    - Audit logs (for security)
    - Anonymized decision data (community benefit)
    - Backup data (until next backup cycle)
  - **Endpoint:** `DELETE /api/v1/auth/me`
  - **Backend logic:**
    - Soft-delete user (set `deleted_at`)
    - Anonymize decisions (replace name with "Deleted User")
    - Clear personal data
    - Log deletion for compliance
  - **Effort:** M

- [ ] **Right to Rectification (Article 16)** — Already implemented
  - **Status:** ✅ Settings.vue already allows editing profile
  - **Verify:**
    - User can update name, email
    - Changes reflected immediately
    - Change logged for audit trail
  - **Effort:** XS (verification only)

- [ ] **Right to Restrict Processing (Article 18)** — Account restriction
  - **UI:** Settings → "Restrict processing" (advanced option)
  - **Effect:**
    - User data not used for analytics
    - No recommendations/personalization
    - Data not shared with third parties
    - Communications paused
  - **Implementation:** Add `processing_restricted` flag on user
  - **Effort:** S

- [ ] **Right to Data Portability (Article 20)** — Export in standard format
  - **Action:** Similar to data export, but in portable format
  - **Formats:** JSON (standard), CSV, XML
  - **Include:**
    - User profile
    - Decisions with metadata
    - Feedback with timestamps
    - Circle memberships
  - **Endpoint:** `POST /api/v1/auth/export-data?format=json`
  - **Effort:** S

- [ ] **Right to Object (Article 21)** — Opt-out of processing
  - **UI:** Settings → "Opt out of..."
  - **Options:**
    - Analytics processing
    - Marketing communications
    - Recommendations
  - **Implementation:** Add consent flags on user table
  - **Effort:** S

- [ ] **Pseudonymization Option** — For analytics
  - **UI:** Settings → "Use anonymous identifier for analytics"
  - **Effect:**
    - Analytics track activity without linking to real identity
    - Use hash/pseudonym instead of email
  - **Effort:** S

**Success Criteria:**
- ✅ User can export all their data
- ✅ User can delete their account
- ✅ User can restrict processing
- ✅ Data portability format valid
- ✅ Opt-out options respected by backend
- ✅ All operations logged for compliance

---

## PHASE 4 — DATA PROTECTION & SECURITY (Semaine 3-4)

> **Priorité:** 🟠 HAUTE  
> **Complexité:** Élevée  
> **Dépendances:** Phase 1-3 ✅  
> **Timeline:** 5-7 jours  

**Features à implémenter:**

- [ ] **Encryption at Rest** — Sensitive data
  - **Data to encrypt:**
    - User passwords (already hashed via bcrypt ✅)
    - OAuth tokens (store encrypted)
    - Personal info (email, name — optional but recommended)
    - Sensitive decision content (if marked confidential)
  - **Method:**
    - Use Laravel `Crypt` facade (uses APP_KEY)
    - Encrypt on save, decrypt on retrieve
    - Example:
      ```php
      // Save
      $user->oauth_token = Crypt::encryptString($token);
      
      // Retrieve
      $token = Crypt::decryptString($user->oauth_token);
      ```
  - **Effort:** M

- [ ] **Encryption in Transit** — HTTPS enforcement
  - **Status:** Likely ✅ if using HTTPS
  - **Verify:**
    - All endpoints use HTTPS (not HTTP)
    - HSTS header set (Force-HTTPS)
    - Certificate valid
  - **Check command:** `curl -I https://dazo.app` → look for `Strict-Transport-Security`
  - **Effort:** XS (configuration only)

- [ ] **Data Breach Notification Process** — Compliance
  - **Document:** `docs/DATA_BREACH_RESPONSE.md`
  - **Process:**
    1. Detect breach (logging, alerts, user report)
    2. Investigate scope (what data leaked?)
    3. Notify CNIL within 72 hours (Article 33 RGPD)
    4. Notify affected users (Article 34 RGPD)
    5. Public communication if high-risk
    6. Implement remediation
  - **Template:**
    ```
    CNIL Notification (template):
    - Date of breach
    - Nature of data
    - Number of affected persons
    - Likely consequences
    - Measures taken
    
    User Notification (template):
    - What happened
    - What data affected
    - What they should do
    - Contact info for questions
    ```
  - **Effort:** S

- [ ] **Access Control & Audit Logging** — Data access tracking
  - **Log all:**
    - Who accessed what data (user_id, data_type, timestamp)
    - Admin impersonations
    - Data exports
    - Account deletions
    - Consent changes
  - **Table:** `audit_logs` (user_id, action, resource, timestamp, ip)
  - **Retention:** Keep for 1 year (legal requirement)
  - **Backend:**
    - Use Laravel event listeners to log sensitive actions
    - Example: `UserDeleted`, `DataExported`, `ImpersonationStarted`
  - **Effort:** M

- [ ] **IP Address Privacy** — GDPR-compliant analytics
  - **Changes:**
    - If using Google Analytics: enable IP anonymization
    - If logging IP addresses: anonymize after 90 days
    - Don't store full IP (store last octet only for geo-blocking)
  - **Config:**
    - Google Analytics: `anonymizeIp: true`
    - Custom logs: `ip = substr(ip, 0, strrpos(ip, '.')) . '.0'`
  - **Effort:** S

- [ ] **Database Backup Encryption** — Backups secure
  - **Changes:**
    - Encrypt backup files
    - Use separate encryption key (not APP_KEY)
    - Test decryption regularly
  - **Effort:** M

- [ ] **Third-Party Service Audit** — Vendors compliance
  - **Audit checklist:**
    - Does OAuth provider have DPA? ✅ (Google, GitHub do)
    - Is email service GDPR-compliant? (Verify with SendGrid/Mailgun)
    - Is hosting provider GDPR-compliant? (AWS, Azure, DigitalOcean — yes)
    - Are backups hosted GDPR-compliant?
  - **Document:** `docs/THIRD_PARTY_COMPLIANCE.md`
  - **Effort:** S

**Success Criteria:**
- ✅ Sensitive data encrypted at rest
- ✅ HTTPS enforced
- ✅ Breach notification process documented
- ✅ All sensitive actions logged
- ✅ IP addresses anonymized
- ✅ Backups encrypted
- ✅ Third-party vendors audited

---

## PHASE 5 — COMPLIANCE MONITORING & ONGOING (Semaine 4+)

> **Priorité:** 🟡 MOYENNE  
> **Complexité:** Faible  
> **Dépendances:** Phase 1-4 ✅  
> **Timeline:** Ongoing  

**Features à implémenter:**

- [ ] **CNIL Compliance Checklist** — Annual review
  - **Document:** `docs/CNIL_COMPLIANCE_CHECKLIST.md`
  - **Review every 6 months:**
    - Privacy Policy up-to-date?
    - ToS up-to-date?
    - All user rights implemented?
    - Data retention policy enforced?
    - No data breaches?
    - All third-parties still compliant?
  - **Effort:** S

- [ ] **User Data Requests Process** — CNIL requests
  - **Implement system:**
    - Form: `support/gdpr-request`
    - User can request:
      - Data access
      - Data deletion
      - Data export
      - Complaint to CNIL
    - Team receives notification
    - Respond within 30 days
  - **Tracking:** Database table `gdpr_requests` (user_id, request_type, status, created_at, resolved_at)
  - **Effort:** S

- [ ] **Consent Re-confirmation** — Update consent if Policy changes
  - **Process:**
    - When Privacy Policy or ToS changes
    - Ask existing users to re-confirm consent
    - Track new acceptance
  - **Effort:** S

- [ ] **Annual GDPR Audit** — External verification
  - **Frequency:** Yearly (or when significant changes)
  - **Scope:**
    - Privacy Policy current?
    - All user rights working?
    - Data handling secure?
    - Third-parties compliant?
    - No unaddressed breaches?
  - **Effort:** M (hire external auditor)

- [ ] **Employee/Team GDPR Training** — Compliance awareness
  - **Annual training:**
    - What is GDPR?
    - User rights
    - Data handling best practices
    - Incident reporting
  - **Effort:** S (once per year)

**Success Criteria:**
- ✅ Compliance checklist passed
- ✅ GDPR requests processed on-time
- ✅ Annual audit passed
- ✅ Team trained on GDPR

---

## TIMELINE

```
Week 1:    Phase 1 (Documentation)     ████████
Week 1-2:  Phase 2 (Cookie Banner)     ████████████
Week 2-3:  Phase 3 (User Rights)       ████████████████
Week 3-4:  Phase 4 (Security)          ████████████
Week 4+:   Phase 5 (Monitoring)        ████...
```

---

## COMPLIANCE CHECKLIST

| RGPD Article | Requirement | Status | Phase |
|--------------|-------------|--------|-------|
| 5 | Lawful basis, transparency | ⚠️ | Phase 1 |
| 6 | Lawful basis for processing | ⚠️ | Phase 1-2 |
| 7 | Consent (freely given, specific) | ❌ | Phase 2 |
| 13 | Privacy Policy at collection | ❌ | Phase 1 |
| 14 | Privacy Policy if not directly collected | ❌ | Phase 1 |
| 15 | Right to access | ❌ | Phase 3 |
| 16 | Right to rectification | ✅ | (existing) |
| 17 | Right to erasure | ❌ | Phase 3 |
| 18 | Right to restrict processing | ❌ | Phase 3 |
| 19 | Notification of rectification/erasure | ⚠️ | Phase 3 |
| 20 | Right to portability | ❌ | Phase 3 |
| 21 | Right to object | ❌ | Phase 3 |
| 32 | Security measures | ⚠️ | Phase 4 |
| 33 | Breach notification (CNIL) | ❌ | Phase 4 |
| 34 | Breach notification (Users) | ❌ | Phase 4 |

---

## LEGAL RESOURCES (France/EU)

- **CNIL:** https://www.cnil.fr/ (French data protection authority)
- **RGPD Officiel:** https://gdpr-info.eu/
- **CNIL Templates:** https://www.cnil.fr/fr/professionnels
- **Data Protection Officer Resources:** https://www.cnil.fr/en/roles

---

## ESTIMATED EFFORT

| Phase | Days | Priority |
|-------|------|----------|
| Phase 1 (Docs) | 3-5 | 🔴 CRITICAL |
| Phase 2 (Consent) | 4-5 | 🔴 CRITICAL |
| Phase 3 (Rights) | 5-7 | 🟠 HIGH |
| Phase 4 (Security) | 5-7 | 🟠 HIGH |
| Phase 5 (Monitoring) | Ongoing | 🟡 MEDIUM |
| **TOTAL** | **22-24 days** | - |

---

**Critical path for beta:** Phase 1 + Phase 2 (Privacy Policy + Cookie Banner) = 1 week MINIMUM

**Full compliance before production:** All phases = 4 weeks

**Annual maintenance:** 2-3 days per year for audits + updates
