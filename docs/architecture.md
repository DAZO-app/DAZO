# Architecture

## 🧠 Overview

DAZO follows a **clean Laravel architecture**:

* Controllers → HTTP layer
* Services → business logic
* Models → data access
* Policies → authorization
* Jobs → async tasks

---

## 📂 Structure

/app

* Models
* Services
* Http/Controllers
* Policies
* Jobs

---

## ⚙️ Principles

* Thin controllers
* No business logic in models
* Services handle all rules
* Explicit dependencies

---

## 🔄 Flow Example

Request → Controller → Service → Model → Response

---

## 🧩 Key Components

### Services

* DecisionService
* FeedbackService
* VoteService

### Events (future)

* DecisionCreated
* ObjectionSubmitted

---

## 🚀 Scalability

* Service layer ensures extensibility
* Modular domain logic
* Ready for SaaS evolution
