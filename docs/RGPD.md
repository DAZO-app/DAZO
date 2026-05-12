# Roadmap : Implémentation du Droit à la Portabilité (RGPD)

Ce document détaille la stratégie technique et fonctionnelle pour permettre aux utilisateurs de récupérer l'intégralité de leurs données personnelles sur DAZO.

## 1. Objectifs
- Fournir un export machine-readable (JSON) complet.
- Assurer la sécurité du transfert de données.
- Minimiser l'impact sur les performances du serveur.

---

## 2. Roadmap d'Implémentation

### Phase 1 : Architecture Backend (Service d'Extraction)
- **Création d'un `GdprService`** : Un service dédié à la collecte récursive des données.
- **Périmètre des données** :
    - Profil : `name`, `email`, `role`, `created_at`.
    - Sécurité : Historique des sessions et comptes sociaux liés.
    - Cercles : Liste des cercles rejoints et rôles occupés.
    - Décisions : Liste des décisions portées (authored) et participations.
    - Échanges : Tous les commentaires et feedbacks postés.
    - Paramètres : Vues personnalisées et configuration du tableau de bord.

### Phase 2 : Système de Génération (Job Asynchrone)
- **Queue Worker** : Utilisation de Laravel Queues pour générer l'export en arrière-plan (évite les timeouts).
- **Formatage** : Génération d'un fichier `.zip` contenant un fichier `data.json` et éventuellement les pièces jointes uploadées par l'utilisateur.
- **Stockage temporaire** : Sauvegarde dans un dossier sécurisé (non-public) avec une durée de vie limitée (ex: 24h).

### Phase 3 : Flux Utilisateur & Notifications
- **Trigger** : L'utilisateur clique sur "Demander mes données" dans les paramètres.
- **Notification** : Une fois l'export prêt, l'utilisateur reçoit :
    - Une notification in-app.
    - Un email contenant un lien de téléchargement signé (`URL::signedRoute`).
- **Audit Log** : Enregistrement de la demande d'export pour conformité administrative.

### Phase 4 : Interface Utilisateur (UI)
- Mise à jour de la section RGPD dans `/settings` (Déjà initialisée).
- Ajout d'un historique des demandes d'export (optionnel).
- Affichage du statut de la demande (En cours / Prêt / Expiré).

---

## 3. Considérations Techniques
- **Sécurité** : Le lien de téléchargement doit être temporaire et lié à la session de l'utilisateur.
- **Performance** : Limiter à une demande d'export par utilisateur tous les 7 jours pour éviter les abus de ressources.
- **Conformité** : Le format JSON est idéal car il respecte l'obligation de "format structuré, couramment utilisé et lisible par machine".
