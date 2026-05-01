# ♿ ROADMAP ACCESSIBILITÉ WEB — WCAG 2.1 AA COMPLIANCE

**Cible:** WCAG 2.1 Niveau AA (conforme aux standards web accessibilité UE)  
**Horizon:** 2-3 semaines pour audit complet + fixes  
**Législation:** EU Accessibility Directive (2016/2102) + France - Loi Handicap

---

## AUDIT ACCESSIBILITÉ INITIAL

### Outils à utiliser (gratuit):
- **axe DevTools** — Chrome extension
- **WAVE** — WebAIM tool
- **Lighthouse** — Chrome DevTools (Accessibility tab)
- **NVDA** — Screen reader test (Windows/free)
- **Keyboard navigation test** — Tab key through entire app

### Issues identifiées à partir du code:

#### 🔴 Critiques (blockers):
1. **Images sans alt text** — PhotoSwipe lightbox, circle avatars, decision attachments
2. **Form labels manquantes** — Login form, Register form, filters UI
3. **Color contrast insuffisant** — Status badges, link colors à vérifier
4. **Missing aria-labels** — Buttons icon-only (favorite, delete, edit)
5. **No focus indicators** — Keyboard navigation breaks

#### 🟠 Importants:
6. **Modal accessibility** — No aria-modal, focus trap not implemented
7. **Error messages not linked** — Form errors not associated with inputs
8. **Heading hierarchy broken** — Some pages skip h1/h2/h3 levels
9. **Table accessibility** — Admin tables missing thead/tbody/scope
10. **Skip link missing** — No "Skip to main content" link

---

## PHASE 1 — AUDIT COMPLET (Semaine 1)

> **Priorité:** 🔴 CRITIQUE  
> **Complexité:** Faible  
> **Dépendances:** Aucune  
> **Timeline:** 3-5 jours  

**Features à implémenter:**

- [ ] **Automated Accessibility Scan** — Setup
  - **Tool:** axe-core npm package + CI integration
  - **Setup:**
    - `npm install --save-dev @axe-core/react` (for Vue, use `axe-core` + test runner)
    - Add to CI/CD pipeline to fail on critical issues
  - **Effort:** XS

- [ ] **Manual Audit — Keyboard Navigation** — All views
  - **Method:** Tab through entire app without mouse
  - **Check:**
    - All interactive elements reachable via Tab
    - Focus visible and clear
    - Logical tab order (left-to-right, top-to-bottom)
    - Can exit modals with Escape
  - **Views to test:**
    - Login.vue, Register.vue
    - Dashboard.vue, DecisionList.vue, DecisionDetail.vue
    - AdminUsers.vue, AdminCircles.vue
    - PublicFront.vue, PublicDecisionDetail.vue
  - **Effort:** M

- [ ] **Manual Audit — Screen Reader** — Core flows
  - **Tool:** NVDA (Windows) or VoiceOver (Mac)
  - **Test:**
    - Login flow (can user read form labels?)
    - Decision creation (can user hear all fields?)
    - List navigation (can user count items?)
  - **Effort:** M

- [ ] **Lighthouse Audit Report** — All pages
  - **Method:** Run Lighthouse on 20+ pages; document baseline scores
  - **Output:** CSV with page, accessibility_score, issues found
  - **Effort:** S

- [ ] **WCAG 2.1 AA Compliance Checklist** — Document
  - **Create:** Reference document listing all 50 WCAG 2.1 criteria
  - **Mark:** Which are met, which need work
  - **Effort:** S

**Success Criteria:**
- ✅ Lighthouse accessibility score baseline documented
- ✅ Keyboard navigation issues identified
- ✅ Screen reader issues logged
- ✅ Compliance checklist prioritized

---

## PHASE 2 — CRITICAL FIXES (Semaine 2)

> **Priorité:** 🔴 CRITIQUE  
> **Complexité:** Moyenne  
> **Dépendances:** Phase 1 ✅  
> **Timeline:** 5-7 jours  

**Features à implémenter:**

- [ ] **Form Labels & ARIA** — All forms
  - **Views:** Login.vue, Register.vue, DecisionCreate.vue, AdminUsers.vue, Settings.vue, etc.
  - **Changes:**
    - Add `<label for="id">` to all inputs
    - Add `aria-label` to icon-only buttons (❤️, 🗑️, ✏️)
    - Add `aria-describedby` for error messages (link error to input)
    - Add `aria-required` on required fields
  - **Example:**
    ```html
    <!-- BEFORE -->
    <input type="email" placeholder="Email">
    
    <!-- AFTER -->
    <label for="email-input">Email address</label>
    <input id="email-input" type="email" aria-required="true" aria-describedby="email-error">
    <span id="email-error" role="alert">Email is required</span>
    ```
  - **Effort:** M

- [ ] **Icon Button Accessibility** — All icon buttons
  - **Views:** Everywhere (favorite button, delete button, edit button, etc.)
  - **Changes:**
    - Add `aria-label` to all `<button>` elements with only icons
    - Example: `<button aria-label="Delete decision">🗑️</button>`
  - **Coverage:**
    - Favorite toggle buttons (❤️)
    - Delete buttons (🗑️)
    - Edit buttons (✏️)
    - Menu buttons (≡)
    - Close buttons (✕)
  - **Effort:** S

- [ ] **Focus Indicators** — Global CSS
  - **File:** `resources/css/app.css` or `dazo-theme.css`
  - **Changes:**
    - Add visible `:focus` and `:focus-visible` styles to all interactive elements
    - Min 3px outline, high contrast color (not same as background)
    - Example:
      ```css
      button:focus-visible,
      a:focus-visible,
      input:focus-visible {
        outline: 3px solid #0066cc;
        outline-offset: 2px;
      }
      ```
  - **Test:** Tab through all pages; outline should be visible everywhere
  - **Effort:** S

- [ ] **Modal Accessibility** — CreateDecisionModal, AddMemberModal, etc.
  - **Changes:**
    - Add `role="dialog"` to modal containers
    - Add `aria-labelledby` pointing to modal title
    - Add `aria-modal="true"`
    - Implement focus trap (Tab loops within modal only)
    - Add `aria-hidden="true"` to background content when modal open
    - Handle Escape key to close modal
  - **Example:**
    ```vue
    <div role="dialog" aria-labelledby="modal-title" aria-modal="true">
      <h2 id="modal-title">Create Decision</h2>
      <!-- modal content -->
    </div>
    ```
  - **Effort:** M

- [ ] **Heading Hierarchy** — All views
  - **Changes:**
    - Ensure every page starts with `<h1>`
    - No skipped levels (h1 → h3 is bad; should be h1 → h2 → h3)
    - Only one `<h1>` per page
  - **Views with issues:**
    - DecisionDetail.vue (likely no h1)
    - AdminDashboard.vue (card titles should be h2, not div)
    - PublicFront.vue (section headers need proper levels)
  - **Effort:** M

- [ ] **Skip Link** — AppLayout, PublicLayout
  - **Changes:**
    - Add hidden skip link at top of page: `<a href="#main-content">Skip to main content</a>`
    - Add `id="main-content"` to main content area
    - Show skip link on focus (`:focus` visible)
  - **Files:** `resources/js/layouts/AppLayout.vue`, `PublicLayout.vue`
  - **Effort:** XS

- [ ] **Color Contrast Check** — Global styles
  - **Method:**
    - Use WebAIM contrast checker
    - Check all text on colored backgrounds
    - Min ratio: 4.5:1 for normal text, 3:1 for large text
  - **Problem areas:**
    - Light gray text on white backgrounds
    - Status badges (especially light colors)
    - Links (ensure different from surrounding text)
  - **Effort:** S

**Success Criteria:**
- ✅ All forms have proper labels and error messages
- ✅ All icon buttons have aria-labels
- ✅ Focus indicators visible on all interactive elements
- ✅ Modals trap focus and have proper ARIA
- ✅ Page heading hierarchy correct
- ✅ Skip link functional
- ✅ All text meets color contrast ratio 4.5:1

---

## PHASE 3 — SEMANTIC HTML & ARIA (Semaine 2-3)

> **Priorité:** 🟠 HAUTE  
> **Complexité:** Moyenne  
> **Dépendances:** Phase 2 ✅  
> **Timeline:** 4-5 jours  

**Features à implémenter:**

- [ ] **Image Alt Text** — All images
  - **Views:** DecisionDetail (attachments), PublicFront (decision cards), CircleList (avatars)
  - **Changes:**
    - Add meaningful `alt` text to all `<img>` tags
    - Alt should describe image content or function
    - Decorative images: `alt=""` (empty)
    - Example:
      ```html
      <!-- GOOD -->
      <img src="avatar.jpg" alt="User profile picture for John Doe">
      
      <!-- BAD -->
      <img src="avatar.jpg" alt="avatar">
      ```
  - **Effort:** S

- [ ] **Table Accessibility** — Admin tables
  - **Views:** AdminUsers.vue, AdminCircles.vue, AdminDatabase.vue
  - **Changes:**
    - Wrap headers in `<thead>`, data in `<tbody>`
    - Add `scope="col"` to header cells (`<th>`)
    - Add `scope="row"` to row headers if applicable
    - Example:
      ```html
      <table>
        <thead>
          <tr>
            <th scope="col">Name</th>
            <th scope="col">Email</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>John</td>
            <td>john@example.com</td>
          </tr>
        </tbody>
      </table>
      ```
  - **Effort:** S

- [ ] **List Semantics** — Decision lists, circle lists
  - **Changes:**
    - Use `<ul>` or `<ol>` for lists, not `<div>`
    - Each list item should be `<li>`
    - Example:
      ```html
      <!-- BEFORE (bad accessibility) -->
      <div class="list">
        <div class="item">Decision 1</div>
        <div class="item">Decision 2</div>
      </div>
      
      <!-- AFTER (good) -->
      <ul>
        <li>Decision 1</li>
        <li>Decision 2</li>
      </ul>
      ```
  - **Views:** DecisionList.vue, CircleList.vue, WikiIndex.vue
  - **Effort:** M

- [ ] **Landmark Regions** — All layouts
  - **Changes:**
    - Use semantic HTML: `<header>`, `<nav>`, `<main>`, `<section>`, `<footer>`
    - Or add `role="banner"`, `role="navigation"`, `role="main"`, etc.
  - **Files:** `AppLayout.vue`, `PublicLayout.vue`
  - **Example:**
    ```vue
    <template>
      <header role="banner"><!-- navbar --></header>
      <nav role="navigation"><!-- sidebar --></nav>
      <main role="main"><!-- page content --></main>
      <footer role="contentinfo"><!-- footer --></footer>
    </template>
    ```
  - **Effort:** M

- [ ] **ARIA Live Regions** — Dynamic content
  - **Changes:**
    - Add `aria-live="polite"` to areas that update without page reload
    - Examples: notification alerts, pending counts, form validation messages
    - Example:
      ```html
      <div aria-live="polite" aria-atomic="true">
        <!-- updated dynamically; screen reader announces changes -->
      </div>
      ```
  - **Views:** Dashboard pending counts, DecisionDetail feedback updates
  - **Effort:** S

- [ ] **Form Validation Accessibility** — All forms
  - **Changes:**
    - On form error, focus should move to error message
    - Error message should be announced by screen reader
    - Use `role="alert"` on error messages
    - Example:
      ```html
      <span id="email-error" role="alert" aria-live="assertive">
        Email is invalid
      </span>
      ```
  - **Effort:** S

**Success Criteria:**
- ✅ All images have meaningful alt text
- ✅ Tables properly structured with thead/tbody
- ✅ Lists use semantic HTML
- ✅ Page uses landmark regions
- ✅ Dynamic content updates announced to screen readers
- ✅ Form errors properly linked and announced

---

## PHASE 4 — TESTING & DOCUMENTATION (Semaine 3)

> **Priorité:** 🟠 HAUTE  
> **Complexité:** Moyenne  
> **Dépendances:** Phase 2-3 ✅  
> **Timeline:** 2-3 jours  

**Features à implémenter:**

- [ ] **Accessibility Testing Checklist** — Create formal test plan
  - **Document:** `ACCESSIBILITY_TESTING.md`
  - **Sections:**
    - Keyboard navigation test script
    - Screen reader test script (NVDA/VoiceOver steps)
    - Mobile accessibility test (mobile screen reader)
    - Color contrast verification
  - **Effort:** S

- [ ] **Automated Testing Setup** — CI/CD integration
  - **Tool:** `@axe-core/react` or Vue adapter
  - **Setup:**
    - Add axe-core to test suite
    - Run on all component tests
    - Fail CI if critical issues found
  - **Effort:** S

- [ ] **Accessibility Documentation** — User-facing
  - **Document:** `PUBLIC/ACCESSIBILITY.md`
  - **Content:**
    - Keyboard shortcuts (Tab, Enter, Escape, etc.)
    - Screen reader compatibility statement
    - Known limitations
    - How to report accessibility issues
  - **Effort:** S

- [ ] **WCAG 2.1 Compliance Report** — Formal audit
  - **Document:** `WCAG_2.1_COMPLIANCE.md`
  - **Format:** Checklist of all 50 WCAG criteria with status (Met/Partial/Not Met)
  - **Effort:** M

**Success Criteria:**
- ✅ Lighthouse accessibility score ≥ 90/100
- ✅ NVDA/VoiceOver can navigate all core flows
- ✅ All keyboard shortcuts documented
- ✅ WCAG 2.1 AA compliance report generated
- ✅ Accessibility statement published on site

---

## PHASE 5 — ONGOING MAINTENANCE

> **Priorité:** 🟡 MOYENNE  
> **Complexité:** Faible  
> **Dépendances:** Phase 1-4 ✅  
> **Timeline:** Ongoing  

**Features à implémenter:**

- [ ] **Accessibility Code Review Process** — Team standard
  - **Checklist:** Before merge, PR reviewer checks:
    - New form fields have labels
    - New buttons have aria-labels if icon-only
    - New images have alt text
    - No new contrast issues
  - **Effort:** XS (process only)

- [ ] **Dependency Updates** — Vue, Bootstrap, etc.
  - **Task:** Monitor accessibility fixes in library updates
  - **Frequency:** Monthly
  - **Effort:** S

- [ ] **User Feedback Loop** — Accessibility issues from users
  - **Process:** Triage accessibility issues separately
  - **SLA:** High priority, fix within 2 weeks
  - **Effort:** Ongoing

**Success Criteria:**
- ✅ Zero critical accessibility issues in production
- ✅ New features always accessible before merge
- ✅ Accessibility score maintained ≥ 90/100

---

## TIMELINE

```
Week 1:    Phase 1 (Audit)              ████████
Week 2:    Phase 2 (Critical Fixes)     ████████████
Week 2-3:  Phase 3 (Semantic HTML)      ████████████
Week 3:    Phase 4 (Testing & Docs)     ████████
Ongoing:   Phase 5 (Maintenance)        ████...
```

---

## COMPLIANCE TARGETS

| Criteria | Target | Status |
|----------|--------|--------|
| WCAG 2.1 Level AA | 100% | ⚠️ In progress |
| Lighthouse Accessibility | ≥ 90/100 | ⚠️ Baseline needed |
| Keyboard Navigation | Full support | ⚠️ Partial |
| Screen Reader Support | Full | ⚠️ Partial |
| Color Contrast | 4.5:1 min | ⚠️ Review needed |
| Form Labels | 100% | ❌ Missing |

---

## KEY RESOURCES

- **WCAG 2.1 Guidelines:** https://www.w3.org/WAI/WCAG21/quickref/
- **WebAIM:** https://webaim.org/
- **Inclusive Components:** https://inclusive-components.design/
- **ARIA Authoring Practices:** https://www.w3.org/WAI/ARIA/apg/
- **Vue Accessibility:** https://vuejs.org/guide/best-practices/accessibility.html

---

**Effort total estimé:** 15-20 jours  
**Post-audit status:** Viser WCAG 2.1 AA complet
