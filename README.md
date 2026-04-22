# DAZO — Moteur de Décision Sociocratique

DAZO est une plateforme collaborative conçue pour faciliter la prise de décision par consentement, inspirée des principes de la sociocratie. Elle permet aux organisations de structurer leurs cercles, de rédiger des propositions claires et de suivre un cycle de vie décisionnel rigoureux et transparent.

![DAZO Logo](DAZO_logo.png)

## 🎯 Mission

L'objectif de DAZO est de transformer la manière dont les groupes collaborent en remplaçant le vote majoritaire traditionnel par un moteur de **gestion des objections** et de **recherche de consentement**.

- **Gouvernance partagée** : Structure en cercles imbriqués.
- **Transparence totale** : Historique complet des versions et des débats.
- **Efficacité opérationnelle** : Détection automatique des impasses et outils de facilitation.

## 🚀 Fonctionnalités Clés

- **Gestion des Cercles** : Définition des rôles (Animateur, Membre, Observateur) et types de cercles (Ouvert/Fermé).
- **Cycle de Décision** : Étapes claires (Brouillon, Clarification, Réaction, Objection, Adoption, Révision).
- **Moteur de Feedback** : Système structuré d'objections, suggestions et clarifications.
- **Édition Riche** : Rédaction des propositions avec l'éditeur Quill (support rich-text et images).
- **Tableau de Bord** : Visualisation en temps réel de la participation et de l'état des décisions en cours.
- **Outils Admin** : Monitoring serveur et base de données intégré.

## 🛠 Stack Technique

- **Backend** : Laravel 11 (PHP 8.2+), API REST, Sanctum Auth.
- **Frontend** : Vue 3 (SPA), Pinia, Vue Router, Vite.js.
- **Base de données** : PostgreSQL / MySQL.
- **Design** : CSS Vanilla (Design System premium, responsive & mobile-first).

## 📥 Installation

```bash
# Cloner le projet
git clone <repository-url>
cd DAZO

# Installation Backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# Installation Frontend
npm install
npm run dev
```

## 📚 Documentation

L'ensemble de la documentation détaillée se trouve dans le dossier [`docs/`](docs/) :

- [Roadmap de Développement](ROADMAP.md)
- [Spécifications de l'API](docs/api.md)
- [Architecture & MCD](docs/architecture.md)
- [Guide Front-End](docs/frontend.md)
- [Cycle de vie des Décisions](docs/decision-lifecycle.md)

---

Une initiative pour une gouvernance plus fluide et humaine.
