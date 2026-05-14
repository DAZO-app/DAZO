# 🚀 Roadmap d'Optimisations Techniques

Ce document répertorie les axes d'amélioration identifiés pour faire passer DAZO d'une phase Beta à une solution de niveau Entreprise/Gouvernance Critique.

---

## 🔝 Priorité 1 : Sécurité & Observabilité (Indispensable)

### 1. Audit Logs (Journal d'activité structuré) — TERMINÉ ✅
- **Description** : Création d'une table `activity_logs` enregistrant qui a fait quoi, quand et sur quelle ressource (Décisions, Cercles, Config).
- **Valeur** : Traçabilité totale, indispensable pour la responsabilité en gouvernance.
- **Détails** : Affichage plein bloc avec pagination, recherche par utilisateur et type d'événement.
- **Complexité** : 4/10
- **Priorité** : Urgente

### 2. Monitoring & Exception Tracking (Sentry)
- **Description** : Intégration de Sentry ou Flare pour capturer les erreurs 500 et les bugs JS en temps réel.
- **Valeur** : Détection proactive des bugs avant que les utilisateurs ne les signalent.
- **Complexité** : 2/10
- **Priorité** : Urgente

---

## 🏗️ Priorité 2 : Fiabilité & Robustesse
> [Voir le détail de cette priorité](ROADMAP_ROBUSTNESS.md)

### 3. Tests Automatisés & CI/CD
- **Description** : Mise en place d'une suite de tests (Pest/PHPUnit) couvrant le cycle de vie des décisions et les permissions. Pipeline GitHub Actions pour bloquer les déploiements cassés.
- **Valeur** : Zéro régression lors des mises à jour.
- **Complexité** : 6/10
- **Priorité** : Haute

### 4. Double Authentification (2FA)
- **Description** : Ajout de la validation par code (TOTP/Email) pour les comptes à privilèges (Admin/Super-Admin).
- **Valeur** : Protection contre le vol de session/identifiants.
- **Complexité** : 5/10
- **Priorité** : Haute

---

## 📈 Priorité 3 : Scalabilité & UX Avancée

### 5. Recherche plein texte (Meilisearch)
- **Description** : Indexation des décisions, feedbacks et wiki dans un moteur de recherche ultra-rapide gérant les fautes de frappe.
- **Valeur** : Expérience utilisateur "Premium" pour naviguer dans une base de connaissances riche.
- **Complexité** : 7/10
- **Priorité** : Moyenne

### 6. Stockage Objet (Compatible S3)
- **Description** : Migration du stockage local (filesystem) vers un service type AWS S3 ou MinIO.
- **Valeur** : Scalabilité infinie du stockage et facilité de backup/récupération.
- **Complexité** : 4/10
- **Priorité** : Moyenne

### 7. Système de Délégation / Proxy Voting
- **Description** : Permettre à un utilisateur de déléguer son "pouvoir" de feedback ou de vote à un autre membre pour une période donnée.
- **Valeur** : Répond aux besoins réels des organisations sociocratiques.
- **Complexité** : 5/10
- **Priorité** : Moyenne

### 8. Snippet Generator (Widget JS)
- **Description** : Générateur de widgets interactifs et légers (JS) pour intégrer les décisions publiques sur des sites tiers. Supporte le filtrage dynamique (cercle, catégorie, auteur) et le rendu statique avec lien de retour.
- **Valeur** : Transparence accrue et rayonnement des décisions hors de la plateforme DAZO.
- **Complexité** : 5/10
- **Priorité** : Moyenne

---

## 📦 Priorité 4 : Exports & Reporting

### 9. Export PDF Global (Le "Grand Livre")
- **Description** : Génération d'un document PDF structuré regroupant toutes les décisions validées de l'année/cercle.
- **Valeur** : Archivage légal et lecture hors-ligne.
- **Complexité** : 6/10
- **Priorité** : Faible (Confort)

### 10. DAZO comme Fournisseur OAuth2 (SSO)
- **Description** : Transformer DAZO en serveur d'authentification (via Laravel Passport) pour permettre à d'autres outils internes de l'organisation d'utiliser les comptes DAZO pour se connecter.
- **Valeur** : Centralisation de l'identité, DAZO devient le "hub" de confiance de l'organisation.
- **Complexité** : 8/10
- **Priorité** : Faible (Écosystème)

---

## 🟢 Améliorations Terminées ✅

### 11. Refonte Admin Server
- **Description** : Modernisation des tuiles statistiques avec dégradés et icônes spécifiques pour CPU, RAM et Disque.
- **Valeur** : Meilleure lisibilité et esthétique premium.

### 12. Dashboard Quick Actions
- **Description** : Redesign du bloc d'actions rapides en grille de cartes centrées ("Bento Grid").
- **Valeur** : Navigation plus intuitive et moderne.

### 13. Personnalisation Dashboard Utilisateur
- **Description** : Support du redimensionnement (drag-to-resize) et gestion des couleurs de catégories personnalisées.
- **Valeur** : Expérience utilisateur personnalisée et cohérente avec le branding.

---

## Synthèse des efforts

| Sujet | Priorité | Complexité | Estimation temps |
| :--- | :---: | :---: | :---: |
| **Audit Logs** | ✅ | 4/10 | TERMINÉ |
| **Monitoring (Sentry)** | 🔴 | 2/10 | 0.5 jour |
| **Tests / CI/CD** | 🟠 | 6/10 | 5 jours |
| **2FA** | 🟠 | 5/10 | 3 jours |
| **Meilisearch** | 🔵 | 7/10 | 4 jours |
| **S3 Storage** | 🔵 | 4/10 | 2 jours |
| **Délégation** | 🔵 | 5/10 | 3 jours |
| **Snippet Generator** | 🔵 | 5/10 | 2 jours |
| **Export PDF** | 🟢 | 6/10 | 3 jours |
| **OAuth Provider** | 🟢 | 8/10 | 5 jours |
