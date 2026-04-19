# Decision Lifecycle

## 🧠 Vue d'ensemble

Le cycle de vie d'une décision DAZO suit le principe du **consentement** (inspiré de la sociocratie) :
une décision est adoptée quand **aucune objection valide ne subsiste**, et non par vote majoritaire.

Chaque cycle est lié à une **version de décision** (`DECISION_VERSION`). Une révision crée
une nouvelle version et relance le cycle depuis la phase `clarification`.

---

## 📊 États

| État | Type | Description |
|---|---|---|
| `draft` | Actif | Brouillon, visible uniquement de l'auteur et de l'animateur |
| `clarification` | Actif | Questions / clarifications sur la proposition (THREAD_MESSAGE) |
| `reaction` | Actif | Ressenti personnel des membres, style CNV (THREAD_MESSAGE) |
| `objection` | Actif | Expression des objections et suggestions (FEEDBACK) |
| `revision` | Actif | L'auteur rédige une nouvelle version en réponse aux objections |
| `adopted` | **Terminal ✅** | Adopté — aucune objection non résolue sur la version courante |
| `adopted_override` | **Terminal ✅** | Adopté malgré des objections restantes (décision explicite auteur/animateur) |
| `abandoned` | **Terminal ❌** | Annulé explicitement par l'auteur ou l'animateur |
| `lapsed` | **Terminal ❌** | Délai de cycle dépassé sans complétion |
| `deserted` | **Terminal ❌** | Aucune activité pendant la période configurée (`reminder_no_deadline_days`) |

---

## 🔄 Transitions autorisées

| De | Vers | Condition | Déclencheur |
|---|---|---|---|
| `draft` | `clarification` | Manuel | Auteur ou Animateur |
| `clarification` | `reaction` | Manuel | Auteur ou Animateur |
| `reaction` | `objection` | Manuel | Auteur ou Animateur |
| `objection` | `adopted` | 0 objection non résolue sur version courante | Automatique OU Auteur/Animateur |
| `objection` | `adopted_override` | Objections restantes — décision forcée | Auteur ou Animateur (explicite) |
| `objection` | `revision` | Objections non résolues — nouvelle version nécessaire | Auteur ou Animateur |
| `revision` | `clarification` | Nouvelle `DECISION_VERSION` créée par l'auteur | Auteur |
| `*` | `abandoned` | Annulation manuelle | Auteur ou Animateur |
| `*` | `lapsed` | `deadline` dépassée | Système (automatique) |
| `*` | `deserted` | Inactivité > `reminder_no_deadline_days` | Système (automatique) |

> **Règle absolue :** pas de retour en arrière dans les phases. Si un nouveau cycle est
> nécessaire, il passe par une `revision` qui repart de `clarification`.

---

## 📋 Règles par phase

### 🟡 Draft
- Visible uniquement de l'auteur et de l'animateur (si désigné)
- L'auteur peut modifier le contenu librement (pas de versionnage en draft)
- L'auteur peut désigner un animateur

### 🔵 Clarification
- Les membres du cercle posent leurs questions via `THREAD_MESSAGE (tour=clarification)`
- Aucun feedback (objection/suggestion) n'est possible à ce stade
- L'auteur clarifie, mais ne peut pas publier une nouvelle version sans passer par la phase `objection`
- Le `THREAD_MESSAGE.is_moderator_note` permet à l'animateur de poser des repères

### 🟢 Réaction
- Les membres expriment leur **ressenti personnel** via `THREAD_MESSAGE (tour=reaction)`
- Inspiré de la Communication Non-Violente (CNV) : observations, sentiments, besoins
- Non bloquant — informatif uniquement
- Aucun feedback (objection/suggestion) n'est possible à ce stade

### 🔴 Objection
- Les membres peuvent faire **UNE SEULE** de ces trois actions (choix exclusif, **non réversible**) :
  1. Soumettre un `FEEDBACK` (`type=objection` ou `type=suggestion`)
  2. Rejoindre un feedback existant via `FEEDBACK_JOIN` (soutien — passe son tour de soumission)
  3. Exprimer un `CONSENT` (`signal=no_objection` ou `signal=abstention`) sur la version courante
- Les fils de discussion sur chaque feedback (`FEEDBACK_MESSAGE`) sont **restreints** à :
  l'auteur du feedback, l'auteur de la décision, et l'animateur
- L'auteur de la décision **ne participe pas** comme membre ordinaire à cette phase
- L'**observer** peut lire tous les feedbacks et threads — il ne peut pas agir
- **L'absence de participation ≠ consentement** : les membres sans action peuvent
  être relancés (par l'auteur, l'animateur, ou automatiquement). Un bypass
  est possible mais tracé et affiché visiblement.

### 🔧 Révision
- L'auteur peut rédiger une nouvelle proposition tout en restant en phase de révision. 
- Le mécanisme de **brouillon de révision** permet de sauvegarder le contenu (`revision_content`) et les pièces jointes (`revision_attachment_ids`) sans créer de version officielle ni écraser la version actuelle. Ce brouillon est accessible via la route `PUT /decisions/{id}` par l'auteur (avec le champ `content` mappé sur `revision_content`).
- Les feedbacks de la version précédente restent liés à leur version — ils servent de base de travail pour la révision.
- **Accès Historique** : Dans l'interface SPA, un panneau latéral "Versions précédentes" permet de naviguer dans l'historique complet de la décision, affichant le contenu, les pièces jointes et les échanges pour chaque version archivée.
- La publication effective crée une nouvelle `DECISION_VERSION`, nettoie le brouillon, et fait repartir la décision en `clarification` (nouveau cycle complet).
- Seuls les feedbacks de la version courante comptent pour la condition d'adoption

---

## ✅ Condition d'adoption

Une décision peut passer à l'état `adopted` si :

**Cas automatique :** aucun `FEEDBACK` de `type=objection` avec un statut non terminal
(`submitted`, `clarification_requested`, `in_treatment`) ne subsiste sur la `DECISION_VERSION` courante.

**Cas manuel :** l'auteur ou l'animateur valide l'adoption malgré des feedbacks non résolus
→ statut `adopted_override` (distinct de `adopted` — traçabilité de la décision forcée).

---

## 👥 Règles de participation

| Acteur | Draft | Clarif. | Réaction | Objection | Révision |
|---|---|---|---|---|---|
| Auteur | ✅ édite | ✅ clarifie | 👀 observe | 👀 observe, répond aux feedbacks | ✅ rédige nouvelle version |
| Animateur | ✅ co-gère | ✅ modère | ✅ modère | ✅ modère, répond aux feedbacks | ✅ co-gère |
| Membre cercle | — | ✅ questions | ✅ réactions | ✅ 1 action exclusive (feedback/consent/join) | — |
| Observer | 👀 | 👀 lecture | 👀 lecture | 👀 lecture | 👀 lecture |

---

## ⚠️ Contraintes

- L'auteur **ne peut pas quitter** son cercle pendant un cycle actif (bloqué au niveau API)
- Si l'auteur perd son accès, l'animateur suspend ou transfère la décision
- Un cercle **doit avoir des membres** autres que l'auteur pour qu'un cycle soit pertinent
- L'animateur peut changer en cours de cycle (historisé dans `DECISION_ANIMATOR_LOG`)
- **Règle exclusive de participation** : un membre ne peut exprimer qu'UN SEUL acte
  par version (feedback, join, ou consent) — action non réversible
- **L'absence de participation n'est pas un consentement implicite** — relance possible
- `emergency_mode` : activable par auteur ou animateur — mise en priorité dans les tableaux de bord et notifications
- **Sous-cercles** : servent à élargir la participation à des personnes extérieures
  au cercle principal — pas d'héritage de décisions entre cercles

---

## ⏱ Délais

- `DECISION.deadline` : délai global de la décision → déclenche `lapsed` si dépassé
- `DECISION.objection_round_deadline` : délai spécifique à la phase `objection`
- `reminder_no_deadline_days` (`INSTANCE_CONFIG`) : inactivité → déclenche `deserted`
- `animator_deadline_days` (`INSTANCE_CONFIG`) : délai pour désigner un animateur si `requires_distinct_animator = true`

---

## 📌 Exemple de cycle complet

```
[draft] → [clarification] → [reaction] → [objection]
              ↑                                |
              |                    objections résolues ?
              |                        /         \
              |                      oui          non
              |                       ↓            ↓
              |                  [adopted]     [revision]
              |                              (nouvelle version)
              └──────────────────────────────────┘
```
