# 📧 STRATÉGIE & RÉPERTOIRE DES EMAILS

Ce document répertorie tous les emails envoyés par la plateforme DAZO. Il sert de guide pour définir la stratégie de communication et configurer les modèles dans l'interface d'administration.

## 🛠 Configuration Globale
Les paramètres d'expédition (Serveur SMTP, Nom de l'expéditeur, Adresse de contact) se configurent dans **Administration > Configuration > Email / SMTP**.

---

## 📋 Répertoire Exhaustif

| Type | Déclencheur (Trigger) | Destinataire | Mailable / Classe | Modèle (Template) | Éditable en Admin ? |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Relance d'échéance** | Tâche planifiée (24h avant) | Participant attendu | `DecisionReminderMail` | `emails.decisions.reminder` | ✅ Oui |
| **Invitation Cercle** | Ajout d'un membre (email) | Nouvel utilisateur | `CircleInvitationMail` | `emails.circle_invitation` | ❌ Non (Blade) |
| **Notification Décision** | Nouvelle décision ou étape | Participants | `DecisionNotificationMail` | `emails.decision_notification` | ❌ Non (Blade) |
| **Contact Admin** | Formulaire de contact | Administrateur | `ContactAdminMail` | `emails.contact.admin` | ❌ Non (Blade) |
| **Notifications Flux** | Divers événements (voir ci-dessous) | Utilisateurs actifs | `SendEmailNotification` | `Mail::raw` (Texte brut) | ❌ Non (Prévu) |
| **Reset Mot de passe** | Demande "Oubli" | Utilisateur | `ResetPassword` (Laravel) | Standard Laravel | ❌ Non |

---

## 🔍 Détails par Catégorie

### ⏰ Relances & Échéances (Reminders)
Ce sont les emails les plus critiques pour le processus de décision. Ils sont entièrement configurables via les clés suivantes dans la configuration :
- **Sujet** : `reminder_email_subject`
- **Corps** : `reminder_email_body`
- **Variables disponibles** :
    - `{name}` : Nom du destinataire
    - `{title}` : Titre de la décision
    - `{phase}` : Phase actuelle (ex: Réaction)
    - `{deadline}` : Date et heure de l'échéance
    - `{url}` : Lien direct vers la décision

### 📢 Notifications d'Événements (Flux)
Gérées par le job `SendEmailNotification`, ces notifications couvrent les événements de l'énumération `NotificationEventType` :
- `NEW_DECISION` : Une nouvelle décision est créée dans un cercle.
- `NEW_VERSION` : Une nouvelle proposition est soumise.
- `PHASE_CHANGE` : Transition vers une nouvelle phase (ex: Objection).
- `DECISION_ADOPTED` : La décision est officiellement adoptée.
- `SUGGESTION_SUBMITTED` : Un nouveau feedback/conseil a été posté.
- `OBJECTION_SUBMITTED` : Une objection a été levée.

> [!NOTE]
> Actuellement, ces notifications sont envoyées en **Texte Brut**. La stratégie cible est de les migrer vers des modèles Blade éditables similaires aux relances.

### ✉️ Invitations
- **Cercle** : Envoyé lorsqu'un administrateur invite une personne externe via son email. Contient un lien de signature temporaire (Magic Link) pour créer le compte ou accepter l'invitation.

---

## 🎯 Stratégie d'Évolution
Pour harmoniser la communication DAZO, les étapes suivantes sont prévues :
1. **Unification des Templates** : Migrer tous les emails `Mail::raw` vers des `Mailable` utilisant des composants Markdown.
2. **Centralisation des Variables** : Utiliser un moteur de rendu de variables (type Mustache/Blade) pour que l'administrateur puisse éditer chaque type d'email sans toucher au code.
3. **Prévisualisation Admin** : Ajouter un bouton "Envoyer un test" pour chaque modèle éditable dans l'interface de configuration.

---
*Lien vers la doc technique : [Architecture de Notification](architecture.md#notifications)*
