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
- **Échéances & Relances** : Délais automatiques par phase et relances par mail pour garantir la fluidité des processus.
- **Moteur de Feedback** : Système structuré d'objections, suggestions et clarifications intégré au thread de discussion.
- **Accès Simplifié** : Connexion par **Magic Link** et prise de contrôle admin (**Impersonation**).
- **Identité Visuelle** : Personnalisation dynamique du logo et du nom de l'instance.
- **Centre de Contrôle** : Dashboard d'administration avec monitoring serveur, gestion des sauvegardes SQL et lecture de logs.

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

### ⚙️ Automatisation (Production)

Pour activer les relances automatiques et la gestion des échéances, configurez le **Scheduler** et le **Worker** :

**1. Scheduler (Cron)** :
Ajoutez cette ligne à la crontab de votre serveur (`crontab -e`) :
```bash
* * * * * cd /chemin/vers/votre/projet && php artisan schedule:run >> /dev/null 2>&1
```

**2. File d'attente (Queues)** :
Pour l'envoi asynchrone des emails, lancez le worker (idéalement via Supervisor) :
```bash
php artisan queue:work
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
