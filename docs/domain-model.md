# Domain Model

## 👤 Users & Roles

### users

* id (uuid)
* name
* email

### roles

* id
* name

### role_user

* user_id
* role_id

---

## 👥 Circles

### circles

* id
* name
* parent_id

### circle_user

* circle_id
* user_id
* role_in_circle

---

## 🧠 Decisions

### decisions

* id
* title
* content
* status
* circle_id
* author_id

---

## 👥 Participation

### decision_user (optional but recommended)

* decision_id
* user_id
* role

---

## 💬 Feedback

### feedbacks

* id
* type (objection | suggestion)
* content
* status
* decision_id
* author_id

---

## 🗳 Votes

### votes

* id
* value
* decision_id
* user_id

---

## 🔗 Relationships

* User ↔ Role (many-to-many)
* User ↔ Circle (many-to-many)
* Circle → Decisions (1-N)
* Decision → Feedbacks (1-N)
* Decision → Votes (1-N)
