# 📋 ROADMAP BÊTA — FEATURES CRITIQUES PRÉ-LANCEMENT

**Statut:** Features à compléter AVANT beta utilisateurs  
**Timeline:** 2-3 semaines maximum  
**Objectif:** Minimum viable product pour feedback utilisateur réel

---

## TABLE DES FEATURES CRITIQUES

| # | Priorité | Feature | Vue | Statut Actuel | Effort | Dépend | Timeline |
|---|----------|---------|-----|---------------|--------|---------|----------|
| **CORE FUNCTIONALITY** | | | | | | | |
| 1 | 🔴 CRITIQUE | Authentication (Login/Register/OAuth) | Login, Register | ✅ Complet | XS | Aucune | Day 1 |
| 2 | 🔴 CRITIQUE | Dashboard utilisateur | Dashboard | ✅ Complet | XS | Aucune | Day 1 |
| 3 | 🔴 CRITIQUE | Créer décision | DecisionCreate | ✅ Complet | XS | Aucune | Day 1 |
| 4 | 🔴 CRITIQUE | Voir décision + workflow | DecisionDetail | ✅ Complet | XS | Aucune | Day 1 |
| 5 | 🔴 CRITIQUE | Gestion cercles | CircleList, CircleDetail | ✅ Complet | XS | Aucune | Day 1 |
| 6 | 🔴 CRITIQUE | Feedback & consentement | DecisionDetail | ✅ Complet | XS | Aucune | Day 1 |
| **CONFORMITÉ LÉGALE** | | | | | | | |
| 7 | 🔴 CRITIQUE | Privacy Policy | Public | ❌ Absent | S | Aucune | Day 2-3 |
| 8 | 🔴 CRITIQUE | Terms of Service | Register view | ❌ Absent | S | Privacy Policy | Day 2-3 |
| 9 | 🔴 CRITIQUE | Cookie Consent Banner | PublicLayout, AppLayout | ❌ Absent | M | Privacy Policy | Day 3-4 |
| 10 | 🔴 CRITIQUE | GDPR Data Export | Settings | ❌ Absent | M | Aucune | Day 4-5 |
| 11 | 🔴 CRITIQUE | GDPR Delete Account | Settings | ❌ Absent | M | Aucune | Day 4-5 |
| **INFRASTRUCTURE & STABILITÉ** | | | | | | | |
| 12 | 🟠 HAUTE | Load Test 500 users | Backend | ⚠️ Not tested | M | Aucune | Week 1-2 |
| 13 | 🟠 HAUTE | Email delivery validation | Auth | ⚠️ Untested | S | Aucune | Week 1 |
| 14 | 🟠 HAUTE | Mobile responsive (375px) | All views | ⚠️ Partial | M | Aucune | Week 1 |
| 15 | 🟠 HAUTE | Backup/Restore DR Test | AdminDatabase | ✅ Functional | S | Aucune | Week 1 |
| 16 | 🟠 HAUTE | Database migration tested | Backend | ⚠️ Partial | S | Aucune | Week 1 |
| 17 | 🟠 HAUTE | Error logging (Sentry) | Backend | ❌ Missing | S | Aucune | Day 5 |
| **API PUBLIQUE (IF NEEDED)** | | | | | | | |
| 18 | 🟡 MOYENNE | Public API key generation | AdminPublication | ⚠️ Client-side only | S | Aucune | Week 1 (optional) |
| 19 | 🟡 MOYENNE | Public API filtering validation | Backend | ⚠️ Not verified | M | Key generation | Week 1 (optional) |
| **ACCESSIBILITY BASELINE** | | | | | | | |
| 20 | 🟡 MOYENNE | Keyboard navigation (core flows) | All | ⚠️ Partial | M | Aucune | Week 2 |
| 21 | 🟡 MOYENNE | Screen reader test (core flows) | All | ❌ Not tested | M | Aucune | Week 2 |
| 22 | 🟡 MOYENNE | Focus indicators visible | CSS | ❌ Missing | S | Aucune | Week 2 |
| **KNOWN BLOCKERS TO FIX** | | | | | | | |
| 23 | 🟠 HAUTE | AdminPublication API key security | AdminPublication | ❌ Not secure | S | Aucune | Week 1 |
| 24 | 🟡 MOYENNE | Share functionality stub | PublicFront, PublicDecisionDetail | ❌ Alert only | S | Aucune | Week 1 |

---

## BREAKDOWN PAR CATÉGORIE

### 🔴 CRITICAL PATH — Do FIRST (Day 1-5)

Must be complete before any beta testing.

#### Conformité légale (Blockers légaux absolus)

| Feature | Vue | Effort | By Day |
|---------|-----|--------|--------|
| Privacy Policy (écrire + publier) | Public footer | S (2-3h) | Day 2 |
| Terms of Service (écrire + publier) | Register | S (2-3h) | Day 3 |
| Cookie Consent Banner (implémenter) | PublicLayout, AppLayout | M (4-6h) | Day 4 |
| GDPR Data Export endpoint | Settings | M (2-3h) | Day 4 |
| GDPR Delete Account endpoint | Settings | M (2-3h) | Day 5 |

**Why critical?** Légalement obligatoire en France/EU. Beta users will expect (and require) these.

---

### 🟠 HIGH PRIORITY — Week 1 (Testing)

#### Stabilité & performance

| Feature | Check | Status | Effort | By |
|---------|-------|--------|--------|-----|
| Load test 500 concurrent users | Dashboard load time < 500ms | ⚠️ Unknown | M (2 days) | Day 5 |
| Email delivery test | Forgot password → inbox check | ⚠️ Manual only | S (4h) | Day 3 |
| Mobile responsive test | Test 375px width iPhone | ⚠️ Partial | M (1 day) | Day 3 |
| Backup/restore test | Create backup → restore to test DB | ✅ Works | S (2h) | Day 2 |
| Error tracking setup | Sentry integration on backend | ❌ Missing | S (2h) | Day 5 |

---

### 🟡 MEDIUM PRIORITY — Week 1-2 (Polish)

#### Nice-to-have before beta

| Feature | Vue | Effort | By |
|---------|-----|--------|-----|
| Fix API key generation (server-side) | AdminPublication | S (2h) | Week 1 |
| Implement Share functionality | PublicFront, Detail | S (4h) | Week 1 |
| Keyboard navigation testing | All core views | M (1 day) | Week 2 |
| Screen reader test | Dashboard, Decisions | M (1 day) | Week 2 |
| Focus indicators CSS | Global styles | S (2h) | Week 2 |

---

## DEFINITION OF DONE — BETA READY CHECKLIST

**Avant d'appuyer sur "Launch Beta":**

### ✅ Functional Requirements
- [ ] All 6 core features working (auth, dashboard, decisions, circles, feedback, export)
- [ ] No console errors on main flows
- [ ] All form validations working
- [ ] File attachments upload/download working
- [ ] Notification system working (email + in-app if configured)
- [ ] Admin panel accessible and functional

### ✅ Legal Compliance
- [ ] Privacy Policy visible from public pages (footer link)
- [ ] Terms of Service shown during registration (checkbox + link)
- [ ] Cookie consent banner appears on first visit
- [ ] Users can export their data (API working)
- [ ] Users can delete their account (API working)
- [ ] Data retention policy documented internally

### ✅ Performance
- [ ] Page load time < 2 seconds (homepage, login, dashboard)
- [ ] API response time < 500ms (dashboard, decisions list)
- [ ] Database queries optimized (no N+1 queries)
- [ ] Tested under 500 concurrent users with acceptable performance

### ✅ Security
- [ ] HTTPS enforced (no HTTP)
- [ ] reCAPTCHA working (login, register, forgot password)
- [ ] Database passwords never logged
- [ ] API keys server-generated (not client-side)
- [ ] No SQL injection vulnerabilities (input validation)
- [ ] CORS properly configured

### ✅ Mobile Responsive
- [ ] Works on 375px width (iPhone SE)
- [ ] Touch-friendly buttons (min 44px)
- [ ] No horizontal scroll
- [ ] Navigation accessible on mobile

### ✅ Data Integrity
- [ ] Backup created successfully
- [ ] Backup restored to test database successfully
- [ ] Data integrity verified post-restore
- [ ] Restoration took < 5 minutes

### ✅ Monitoring & Logging
- [ ] Error tracking working (Sentry or similar)
- [ ] Application logs being written
- [ ] Admin can view recent errors
- [ ] Server monitoring configured (CPU, RAM, disk)

### ✅ Documentation
- [ ] Deployment guide completed
- [ ] Troubleshooting guide for common issues
- [ ] Admin onboarding guide
- [ ] User FAQ or help page
- [ ] Known issues documented

### ✅ Team Readiness
- [ ] Support email configured
- [ ] Issue tracking ready (Jira, Linear, GitHub Issues)
- [ ] On-call rotation assigned
- [ ] Incident response plan documented
- [ ] Feedback collection process ready (form, email, etc.)

---

## FEATURES NOT IN BETA (Post-launch)

**These are planned but NOT for beta v1:**

- ❌ Share functionality (planned but stub only)
- ❌ Advanced reporting/analytics
- ❌ Webhooks/API integrations
- ❌ SQL import for admins
- ❌ Auto-backup scheduling
- ❌ Full WCAG 2.1 AA compliance (Phase 1 only)
- ❌ Advanced decision workflows (scheduled transitions, templates)
- ❌ Mobile app
- ❌ Internationalization (i18n)

**Message to beta users:** "We're rolling out features iteratively based on your feedback."

---

## SPRINT BREAKDOWN — 2-3 WEEKS

### Week 1: Days 1-5 (Conformité + Core fixes)

**Mon-Wed (Days 1-3):**
- [ ] Write + publish Privacy Policy (6h)
- [ ] Write + publish Terms of Service (6h)
- [ ] Setup error tracking (Sentry) (2h)
- [ ] Manual email delivery test (1h)
- [ ] Database backup test (1h)

**Thu-Fri (Days 4-5):**
- [ ] Implement Cookie Consent Banner (6h)
- [ ] Implement Data Export API (3h)
- [ ] Implement Delete Account API (3h)
- [ ] Load test on staging (4h)
- [ ] Fix any critical issues found (4h)

**Effort Week 1:** ~40 hours (1 full-time developer)

---

### Week 2: Days 6-10 (Stabilité + Polish)

**Mon-Tue (Days 6-7):**
- [ ] Mobile responsive fixes (8h)
- [ ] Fix API key generation (2h)
- [ ] Implement Share functionality (4h)

**Wed-Thu (Days 8-9):**
- [ ] Keyboard navigation audit (4h)
- [ ] Focus indicators CSS (2h)
- [ ] Screen reader basic test (4h)

**Fri (Day 10):**
- [ ] Final testing pass
- [ ] Documentation review
- [ ] Deploy to staging
- [ ] Final go/no-go decision

**Effort Week 2:** ~30 hours (0.75 FTE + QA)

---

### Week 3: Day 11-15 (Launch)

**Mon-Wed (Days 11-13):**
- [ ] Production deployment
- [ ] Beta user onboarding
- [ ] 24/7 monitoring
- [ ] Bug fixes as reported

**Thu-Fri (Days 14-15):**
- [ ] Feedback collection
- [ ] Triage issues
- [ ] Start sprint 1 of post-beta improvements

**Effort Week 3:** On-call rotation (standby mode)

---

## SUCCESS METRICS FOR BETA

**How do we know if beta is successful?**

| Metric | Target | How to measure |
|--------|--------|-----------------|
| Uptime | ≥ 99.5% | Monitoring dashboard |
| Page Load Time | < 2 sec | Lighthouse / real user data |
| Error Rate | < 0.1% | Sentry dashboard |
| User Engagement | ≥ 80% try core feature | Analytics |
| Support Requests | < 10 per day | Support email tracking |
| Critical Bugs | 0 | Bug tracking system |

---

## RISK MITIGATION

### Risk: Privacy Policy not ready on time

**Mitigation:**
- Use CNIL template (free, publicly available)
- Hire legal consultant for 2-3 hours consultation
- Start writing TODAY
- Have lawyer review Draft by Day 1

**Fallback:** Delay launch by 2 days (not critical path blocker if draft ready)

---

### Risk: Email delivery broken

**Mitigation:**
- Test SMTP configuration before beta
- Have fallback SMTP provider ready
- Monitor email queue
- Setup alerting if deliverability drops

**Fallback:** Use SendGrid instead of current provider

---

### Risk: Performance degrades under load

**Mitigation:**
- Load test on staging before production
- Implement caching if needed
- Use CDN for static assets
- Database query optimization

**Fallback:** Limit beta to 100 users initially, scale up later

---

### Risk: Security vulnerability discovered during beta

**Mitigation:**
- Have security incident response plan ready
- Monitor error logs constantly
- Implement rate-limiting on auth endpoints
- Keep dependencies updated

**Fallback:** If critical: take down beta temporarily, fix, redeploy

---

## GO/NO-GO DECISION CRITERIA

**Launch beta if ALL green:**

- ✅ Privacy Policy + ToS + Cookie banner live
- ✅ GDPR data rights (export + delete) working
- ✅ Load test passed (500 users, < 500ms response)
- ✅ Mobile responsive on 375px
- ✅ Zero critical bugs in staging
- ✅ Backup/restore validated
- ✅ Support email configured
- ✅ Monitoring alerts configured

**Delay beta if ANY red:**

- ❌ Legal documents not published
- ❌ Load test fails (response time > 1 second)
- ❌ Critical security issue discovered
- ❌ Data loss risk identified
- ❌ Mobile completely broken

---

## POST-BETA ROADMAP

**Week 1 after launch:** Monitor, triage, fix critical bugs

**Week 2+:** Start Sprint 2 (Post-Beta improvements):
- [ ] Implement full accessibility (WCAG 2.1 AA)
- [ ] Complete RGPD compliance (Phase 3-5)
- [ ] Add Share feature (full implementation)
- [ ] Performance optimization
- [ ] More admin tools

---

## COMMUNICATION TEMPLATE

**To send to beta users:**

```
Subject: DAZO Beta Program — You're In! 🎉

Hi [Name],

Thank you for joining the DAZO beta! We're excited to have you test our collaborative 
decision-making platform with us.

**What to expect:**
- Full feature access to create decisions, manage circles, and participate in feedback
- Some features may still be rough (UI not perfect yet)
- Occasional bugs or performance hiccups
- New updates 2-3 times per week based on your feedback

**How to report issues:**
- Found a bug? Email support@dazo.com with:
  - What you were doing
  - What happened (screenshot appreciated)
  - What you expected to happen

**We need your feedback on:**
- Is the decision workflow clear?
- Are the UI/UX intuitive?
- What features are missing?
- Any performance issues?

**Important notes:**
- Data is not guaranteed to be persistent (may wipe DB during beta)
- Use test/dummy data, not production data yet
- Beta ends [DATE]. Full launch: [DATE]

Thanks for being early adopters!

DAZO Team
```

---

**Status:** Ready to build  
**Next action:** Exécuter SERVER_AUDIT_SCRIPT.md + Start Week 1 Day 1  
**Questions?** Check ROADMAP_INDEX.md for guidance
