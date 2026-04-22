# Étape 16 — Configuration Globale et Échéances

> Date : 22 avril 2026

## Objectif

Centraliser la gestion des paramètres de l'instance (identité visuelle, règles de gouvernance) et automatiser le suivi temporel des processus décisionnels pour garantir l'efficacité de la sociocratie.

## Travaux Réalisés

### 1. Identité & Branding
- **Instance Name** : Possibilité de modifier le nom de l'organisation.
- **Logo Dynamique** : Système d'upload de logo (stocké dans `storage/app/public/branding/`).
- **Nouveau Centre d'Upload** : Interface avec zone de dépôt interactive (Dropzone style) et prévisualisation large.
- **UI Premium** : Affichage des labels fonctionnels en priorité, avec les clés techniques (variables système) affichées discrètement en dessous pour les administrateurs.

### 2. Moteur de Délais (Deadlines) & Gouvernance
- **Calcul Automatique** : Lors de la transition vers les phases de **Réaction** ou d'**Objection**, une date limite est fixée dynamiquement.
- **Paramètres de Gouvernance** : Réglage du nombre de jours par défaut pour chaque phase critique.
- **Période de Révision** : Définition de l'intervalle de ré-examen suggéré pour les décisions adoptées.

### 3. Système de Relance & Notifications
- **Relances Automatiques** : Moteur identifiant les participants n'ayant pas encore réagi 24h avant l'échéance.
- **Catégorie Emails (Nouveau)** : Création d'une section dédiée à la personnalisation des textes de templates mails (en cours de développement).
- **Artisan Command** : `php artisan dazo:send-reminders`.

### 4. Mode Maintenance
- **Activation Rapide** : Toggle dédié permettant de couper l'accès aux non-administrateurs lors de travaux techniques.
- **UI Dédiée** : Section "Zone de Danger" renommée en "Mode Maintenance" avec confirmation visuelle.

### 5. Indicateurs d'Urgence UI
- **Badges de Sidebar** : Compteur rouge "Urgentes / Expirées" pour guider l'utilisateur.
- **Dashboard Alert** : Bloc de priorité s'affichant en haut du tableau de bord pour les échéances imminentes.
- **Style Visuel** : Code couleur rouge et icônes d'horloge intégrés dans les listes de décisions.

## Fichiers Modifiés/Créés

| Fichier | Description |
|---|---|
| `app/Services/ConfigService.php` | Cœur de la configuration (Bulk update, Upload). |
| `app/Console/Commands/SendDecisionReminders.php` | Moteur de relance automatique. |
| `resources/js/stores/config.js` | Store Pinia pour la réactivité globale du branding. |
| `resources/js/views/admin/AdminConfig.vue` | Interface d'administration segmentée avec design Premium Card. |

## Vérification
- [x] Changement de nom répercuté instantanément (Pinia).
- [x] Logo uploadable via la nouvelle interface Dropzone.
- [x] Deadline calculée lors du passage en phase "Reaction".
- [x] Badge d'urgence visible si une décision finit dans < 24h.
- [x] Navigation par sections (Identité, Gouvernance, Sécurité, Alertes, Emails, Maintenance).
