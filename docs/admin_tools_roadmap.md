# Roadmap : Outils d'Administration DAZO (BDD & Serveur)

Ce document définit les étapes logiques pour fournir aux administrateurs de l'instance DAZO une interface de contrôle complète du système.

## 🗄️ Bloc A : Gestion de la Base de Données

### A.1 — Surveillance & État
- [ ] **Indicateur de connexion** : Vérification en temps réel de la réponse SQL.
- [ ] **Inventaire des tables** : Liste exhaustive des tables, nombre de lignes, index et poids des données (Data vs Index).
- [ ] **État de santé** : Détection des tables corrompues ou nécessitant une optimisation.

### A.2 — Maintenance Opérationnelle
- [ ] **Outils de réparation** : Interface pour lancer `REPAIR TABLE` ou `OPTIMIZE TABLE`.
- [ ] **Gestion des clés** : Visualisation des contraintes API et index orphelins.

### A.3 — Sauvegarde & Restauration
- [ ] **Dumps compressés** : Bouton pour générer un `.sql.gz` à la volée.
- [ ] **Historique des backups** : Liste des fichiers présents dans `storage/app/backups`.
- [ ] **Import assisté** : Upload de dump avec vérification de structure de base avant exécution.

---

## 🖥️ Bloc B : Monitoring Serveur

### B.1 — Ressources Système
- [ ] **Métriques de base** : Charge CPU (Load Average), Utilisation RAM, Espace disque (Total / Libre).
- [ ] **Uptime & OS** : Informations sur le noyau et le temps de fonctionnement.

### B.2 — Environnement & Services
- [ ] **Checklist PHP** : Vérification de la présence des extensions critiques (PDO, GD, BCMath, Redis, EXIF, Intl).
- [ ] **Statut des services** : Vérification de l'état de Redis, du serveur de mail (SMTP/Mailpit) et des workers (Supervisor).
- [ ] **Configuration PHP** : Affichage des limites vitales (`memory_limit`, `upload_max_filesize`, `max_execution_time`).

### B.3 — Analyse & Logs
- [ ] **Log Viewer** : Affichage temps réel des dernières lignes du `laravel.log`.
- [ ] **Purge des logs** : Outil pour vider les fichiers de logs volumineux ou les archives.

---

## 🛡️ Bloc C : Automatisation & Sécurité (V2)
- [ ] **Sauvegardes automatiques** : Planification de dumps quotidiens vers un stockage externe (S3/FTP).
- [ ] **Alerting** : Notification par email si la partition disque est saturée (> 90%).
- [ ] **Audit de sécurité** : Scan des permissions sur les dossiers sensibles (`storage`, `bootstrap/cache`).
