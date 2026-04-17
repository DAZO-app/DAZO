# Étape 9 — Finalisation de l'Infrastructure Backend (V1)

Cette étape marque la fin du développement de la V1 au niveau Backend, en implémentant les blocs 10, 11, 12 et 13 prévus par la `ROADMAP_V1.md`.

## 1. Bloc 10 : Notifications
Un système d'alerte multicanal a été introduit pour fluidifier l'expérience des décideurs et des cercles :
- **`NotificationService`** : Fournit une méthode commune `notify()` acceptant un utilisateur, le type d'évènement et un payload optionnel. Le service détermine automatiquement le canal en vérifiant les préférences (`NotificationPreference`) utilisateur.
- **Canaux locaux** : Stockage asynchrone des notifications (`notification_table`) interrogeable via l'API REST `NotificationController` (`GET` pour la liste, `POST /read` pour l'acquittement).
- **Envoi Mail** : Un Dispatchable Job `SendEmailNotification` a été créé pour assurer l'envoi d'alerte en arrière-plan sans bloquer la requête entrante. Utilisé majoritairement pour les relances ou transitions de vie (Adoption, Abandon).

## 2. Bloc 11 : Traçabilité (Audit Log)
L'application doit répondre aux besoins d'audit des processus de décision sans sacrifier la performance :
- **MiddleWare `LogRequest`** : Enregistré globalement sur la stack de l'API. Ce middleware est responsable d'intercepter chaque requête HTTP de mutation d'état (Méthodes `POST`, `PUT`, `DELETE` ou `PATCH`).
- Un log crypté et silencieux est conservé dans `app_logs` comprenant l'IP, le contexte, le payload expurgé des mots de passe.
- **`app:purge-old-logs` command** : Une tâche cron tournant en local pour garantir que l'historique d'exploitation (+ d'1 an) est purgé chaque minuit, garantissant un contrôle légal strict en RGPD.

## 3. Bloc 12 : Administration Globale
Configuration système injectée en runtime par les rôles d'instance superieurs.
- **Cache Redis `ConfigService`** : Afin de réduire la pression base de données sur la table `instance_config`, le Service d'administration lit une fois la structure avant de mémoriser `Cache::rememberForever`. Seule l'action d'édition asynchrone (`POST`) entraine un `Cache::forget`.
- Les contrôleurs administratifs (comme `Api/V1/Admin/ConfigController`) sont verrouillés par le Custom Middleware **`AdminMiddleware`**, qui s'assure que le `UserRole` assigné en session est `UserRole::ADMIN` ou `SUPERADMIN`.

## 4. Bloc 13 : Tests de Validation (Scaffolding)
- `tests/Unit/FeedbackServiceTest.php` a été mis en œuvre pour tester explicitement l'algorithme critique : `hasNoBlockingObjections()`.
- Un test système `DecisionFlowTest.php` sert de fondation HTTP pour orchestrer dynamiquement les end-to-ends.

> **Statut Global : Terminé !** 
La V1 Backend est à 100% couverte par des middlewares sécurisés, un routage REST documenté, un ORM typé UUIDs et Enum Casts, et interopérable avec sa propre SPA incluse.
