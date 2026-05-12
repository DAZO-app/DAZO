# 🏗️ Roadmap Détaillée : Fiabilité & Robustesse

Ce document détaille les étapes d'implémentation pour renforcer la stabilité et la sécurité de DAZO.

---

## 🧪 3. Tests Automatisés & CI/CD (Complexité : 6/10)

L'objectif est d'atteindre une couverture de test permettant de déployer en toute confiance.

### Phase 3.1 : Infrastructure de Test
- [ ] Installation et configuration de **Pest PHP**.
- [ ] Mise en place d'une base de données de test (SQLite in-memory ou PostgreSQL dédié).
- [ ] Création des **Factories** pour toutes les entités (User, Decision, Circle, Feedback).

### Phase 3.2 : Tests de Logique Métier (Unit)
- [ ] **State Machine** : Valider toutes les transitions autorisées/interdites des décisions.
- [ ] **Permissions** : Tester les Policies (qui peut voir/éditer quoi selon le cercle et le rôle).
- [ ] **Calculs de délais** : Vérifier que les deadlines sont correctement calculées selon la config.

### Phase 3.3 : Tests d'Intégration API (Feature)
- [ ] **Flux de décision** : Scénario complet de "Brouillon" à "Adoptée".
- [ ] **Auth & Sécurité** : Vérifier que les routes admin sont protégées et que les Magic Links fonctionnent.
- [ ] **Exports & Backups** : Tester la génération sans erreur des fichiers.

### Phase 3.4 : Pipeline CI/CD (GitHub Actions)
- [ ] Script de Linting (PHP Stan / Laravel Pint).
- [ ] Exécution automatique des tests à chaque Pull Request.
- [ ] Notification d'échec sur Slack/Discord.

---

## 🔐 4. Double Authentification - 2FA (Complexité : 5/10)

Sécurisation des accès sensibles pour les administrateurs et porteurs de projets.

### Phase 4.1 : Fondations 2FA
- [ ] Intégration de `pragmarx/google2fa` ou utilisation de **Laravel Fortify**.
- [ ] Ajout des colonnes `two_factor_secret` et `two_factor_recovery_codes` aux utilisateurs.

### Phase 4.2 : Interface Utilisateur (Paramètres)
- [ ] Vue de configuration : Affichage du QR Code pour scan (Google Authenticator / Authy).
- [ ] Génération et affichage des codes de secours (Recovery codes).
- [ ] Désactivation sécurisée (nécessite le mot de passe actuel).

### Phase 4.3 : Challenge au Login
- [ ] Interruption du flux de connexion si 2FA activé.
- [ ] Page de saisie du code TOTP à 6 chiffres.
- [ ] Gestion du "Se souvenir de cet appareil" (Optionnel).

### Phase 4.4 : Politique d'Administration
- [ ] Option dans le centre de contrôle pour **obliger** le 2FA pour les rôles Admin/Super-Admin.
- [ ] Dashboard de suivi pour voir quels utilisateurs ont activé le 2FA.

---

## Calendrier Prévisionnel

| Phase | Durée Estmée | Dépendances |
| :--- | :--- | :--- |
| **Infrastructure Tests** | 1 jour | - |
| **Tests Critiques (Logic)** | 2 jours | Infrastructure Tests |
| **Pipeline CI** | 1 jour | Tests Critiques |
| **Core 2FA** | 2 jours | - |
| **UI 2FA & Challenge** | 2 jours | Core 2FA |
