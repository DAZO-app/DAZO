# Étape 15 — Administration, Outils et Monitoring

> Date : 22 avril 2026

## Objectif

Fournir aux administrateurs une suite d'outils robustes pour surveiller l'état de santé du système, gérer les sauvegardes de données et analyser les comportements du serveur directement depuis l'interface DAZO.

## Travaux Réalisés

### 1. Dashboard d'Administration Premium
- **Interface Centralisée** : Création d'un tableau de bord de surveillance regroupant les métriques vitales et les raccourcis de gestion (Cercles, Catégories, BDD, Serveur).
- **Design Cohérent** : Utilisation du design system "Premium Card" avec dégradés et indicateurs circulaires pour correspondre au reste de la plateforme.

### 2. Gestionnaire de Sauvegardes (Database)
- **Sauvegardes à la volée** : Implémentation d'un bouton pour générer des dumps SQL compressés (`.sql.gz`).
- **Cycle de vie DNS** : 
    - Liste des sauvegardes disponibles avec poids et date.
    - Téléchargement sécurisé via URLs signées temporaires.
    - Suppression physique des fichiers de backup avec confirmation.
- **Statistiques Tables** : Affichage de l'occupation disque par table (données vs index).

### 3. Monitoring Serveur & Logs
- **Ressources Système** : Affichage en temps réel de la charge CPU, de l'utilisation RAM et de l'espace disque.
- **Logic de Lecture de Logs** : Intégration d'un visualiseur de logs Laravel (`laravel.log`) affichant les 15 dernières entrées dans un format terminal haute lisibilité.

## Fichiers Modifiés/Créés

| Fichier | Description |
|---|---|
| `app/Http/Controllers/Api/V1/Admin/AdminToolController.php` | Backend pour les stats, backups et logs. |
| `resources/js/views/admin/AdminDashboard.vue` | Vue centrale de monitoring. |
| `resources/js/views/admin/AdminDatabase.vue` | Gestion des sauvegardes et stats SQL. |
| `resources/js/views/admin/AdminServer.vue` | Monitoring système et logs. |

## Vérification
- [x] Sauvegarde SQL générée et listée correctement.
- [x] Téléchargement fonctionnel via URL signée (sécurisé).
- [x] Suppression physique du fichier confirmée sur le serveur.
- [x] Lecture des logs opérationnelle (via `tail`).
