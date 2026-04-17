# API (Draft)

## 🔐 Auth

* POST /login
* POST /register

---

## 👥 Circles

* GET /circles
* POST /circles
* POST /circles/{id}/join

---

## 🧠 Decisions

* GET /decisions
* POST /decisions
* GET /decisions/{id}

---

## 🔄 Lifecycle

* POST /decisions/{id}/transition

---

## 💬 Feedback

* POST /decisions/{id}/feedback
* GET /decisions/{id}/feedback

---

## 🗳 Votes

* POST /decisions/{id}/vote
* GET /decisions/{id}/votes

---

## ⚠️ Notes

* API will evolve
* Designed for SPA / mobile usage
