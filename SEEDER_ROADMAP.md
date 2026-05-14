# Roadmap pour le Seeder "Full Simulation"

## Objectif

Créer un seeder de démonstration réaliste, massif et cohérent avec les règles métier DAZO :

- environ 50 utilisateurs avec statuts, rôles, préférences et appartenance aux cercles ;
- 10 cercles avec types équilibrés et hiérarchie partielle ;
- 200+ décisions couvrant toutes les phases du workflow ;
- interactions riches : clarifications, réactions, objections, suggestions, consentements, réponses et soutiens ;
- pièces jointes variées avec visibilité indépendante de la décision ;
- données déterministes ou semi-déterministes pour pouvoir rejouer les tests.

Le seeder doit permettre de tester les vues publiques, privées, admin, cercle, décision, notifications, filtres, accès, et états limites.

---

## Modifications selon les spécifications

### Utilisateurs

- Environ 50 utilisateurs.
- Répartition :
  - superadmin : 1
  - admins : 3 à 5
  - animateurs globaux : 6 à 8
  - utilisateurs actifs : 35 à 40
  - utilisateurs inactifs : 3 à 5
- Tous les utilisateurs doivent avoir :
  - email prévisible en `@dazo.test`
  - mot de passe commun `password`
  - `email_verified_at`
  - préférences de notification variées
  - quelques `custom_views`
  - éventuellement un `dashboard_widgets` ou `UserLayout` si utile pour les tests UI
- Les utilisateurs sont répartis aléatoirement mais de façon contrôlée dans les cercles.

### Visibilité des décisions

- 90% publiques.
- 10% privées.
- Répartition aléatoire mais cohérente :
  - les brouillons sont plus souvent privés ;
  - les décisions de cercles fermés peuvent être publiques ou privées ;
  - les décisions privées doivent garder des participants et auteurs permettant les tests d'accès.

### Pièces jointes

- 5 à 10 pièces jointes par décision.
- 90% publiques / 10% privées via `attachments.is_private`.
- La visibilité d'une pièce jointe n'est pas forcément liée à la visibilité de la décision parente.
- Types variés :
  - PDF
  - images
  - tableurs
  - documents
  - SVG
  - archives légères
- Les chemins peuvent rester fictifs mais doivent être plausibles : `seed-attachments/{uuid}/{filename}`.

### Types de cercles

- Répartition équilibrée :
  - Open : 50%
  - Closed : 30%
  - Observer open : 20%
- Pour 10 cercles :
  - 5 open
  - 3 closed
  - 2 observer_open

### Hiérarchie des cercles

- 30% des cercles avec sous-cercles.
- Logique attendue :
  - sous-cercles plus larges ou plus restreints que le parent ;
  - héritage partiel des membres ;
  - visibilité indépendante ;
  - au moins un cas parent fermé -> enfant ouvert ;
  - au moins un cas parent ouvert -> enfant fermé ;
  - au moins un second niveau de hiérarchie.

---

## Structure cible des données

### Décisions

Créer au minimum 200 décisions.

Répartition par visibilité :

| Visibilité | Volume cible |
|------------|--------------|
| Publique | 180 |
| Privée | 20 |

Répartition par phase :

| Statut | Ratio cible | Volume approx. | Notes |
|--------|-------------|----------------|-------|
| `draft` | 15% | 30 | majorité privée |
| `clarification` | 20% | 40 | questions ouvertes et traitées |
| `reaction` | 30% | 60 | réactions positives, neutres, critiques |
| `objection` | 20% | 40 | objections, suggestions, soutiens |
| terminal | 15% | 30 | `adopted`, `abandoned`, `lapsed`, `deserted`, `adopted_override` |

Ajouter aussi quelques cas spécifiques :

- 5 décisions en `revision`.
- 3 décisions en `suspended` avec `status_before_suspension`.
- 5 décisions en `emergency_mode`.
- 10 décisions avec relations :
  - `derives_from`
  - `blocks`
- 10 décisions avec plusieurs versions.

### Interactions

Chaque décision active doit générer des interactions adaptées à sa phase :

| Phase | Feedbacks | Consentements |
|-------|-----------|---------------|
| `clarification` | `clarification` | `no_questions`, `abstention` |
| `reaction` | `reaction` | `no_reaction`, `abstention` |
| `objection` | `objection`, `suggestion` | `no_objection`, `abstention` |

Cas à couvrir :

- aucune interaction ;
- interaction unique ;
- discussion longue avec `feedback_messages` ;
- feedback traité ;
- feedback rejeté ;
- objection soutenue via `feedback_joins` ;
- plusieurs utilisateurs ayant déjà consenti ;
- utilisateurs restants n'ayant pas encore agi ;
- commentaires produits par un auteur, un animateur, un participant et un membre non participant.

### Pièces jointes

> Statut temporaire : génération des pièces jointes mise de côté jusqu'à l'ajout des PJ de test.

- 5 à 10 par décision.
- Volume total attendu pour 200 décisions : 1 000 à 2 000 pièces jointes.
- Répartition :
  - 90% `is_private = false`
  - 10% `is_private = true`
- L'uploader doit être un utilisateur existant et cohérent avec la décision.

### Cercles

Créer 10 cercles :

| Type | Volume |
|------|--------|
| `open` | 5 |
| `closed` | 3 |
| `observer_open` | 2 |

Prévoir :

- 2 ou 3 cercles racines.
- 3 cercles avec enfants.
- 1 sous-cercle de niveau 2.
- 8 à 20 membres par cercle.
- 1 à 3 animateurs par cercle.
- quelques observateurs dans les cercles `observer_open`.
- au moins un cercle avec peu de membres pour tester les états vides ou restreints.

---

## Liste d'implémentation des features du seeder

### 1. Créer un seeder dédié full simulation

- [x] Ajouter `database/seeders/FullSimulationSeeder.php`.
- [x] Garder les seeders actuels pour la démo légère.
- [x] Appeler le full seeder uniquement sur commande explicite :
  - `php artisan db:seed --class=FullSimulationSeeder`
- [x] Ne pas l'appeler par défaut dans `DatabaseSeeder`.
- [x] Encapsuler l'exécution dans une transaction quand possible.
- [x] Afficher un résumé final :
  - utilisateurs créés ;
  - cercles créés ;
  - décisions créées ;
  - feedbacks créés ;
  - consentements créés ;
  - pièces jointes créées ou différées ;
  - ratio public/privé.

Résumé point 1 :

- `FullSimulationSeeder` existe et peut être lancé explicitement.
- `DatabaseSeeder` n'est pas modifié : le seed léger reste le comportement par défaut.
- Le seeder prépare une transaction, affiche un résumé final, et indique que les pièces jointes sont différées.
- Aucune génération métier massive n'est encore active : elle commence au point 2.

### 2. Ajouter une configuration de génération

- [x] Créer une configuration interne dans `FullSimulationSeeder` :
  - `user_count = 50`
  - `circle_count = 10`
  - `decision_count = 200`
  - `attachments_per_decision_min = 5`
  - `attachments_per_decision_max = 10`
  - `public_decision_ratio = 0.90`
  - `public_attachment_ratio = 0.90`
- [x] Prévoir un seed aléatoire fixe :
  - `mt_srand(20260513)`
  - `fake()->seed(20260513)` si Faker est utilisé
- [x] Centraliser les helpers de ratio :
  - `weightedPick(array $weights)`
  - `chance(float $ratio)`
  - `pickMany(Collection $items, int $min, int $max)`

Résumé point 2 :

- La configuration cible est centralisée dans `FullSimulationSeeder::CONFIG`.
- Le seed aléatoire fixe `20260513` rend les futures générations rejouables.
- Les helpers `weightedPick`, `chance`, `pickMany` et `randomFloat` sont prêts pour les prochains points.
- Les paramètres de pièces jointes restent présents comme cible, mais la génération demeure différée.

### 3. Générer les utilisateurs

- [x] Étendre ou remplacer la logique de `UserSeeder` dans le full seeder.
- [x] Créer un jeu de noms réalistes.
- [x] Assigner les rôles `superadmin`, `admin`, `user`.
- [x] Assigner `is_global_animator` à une minorité d'utilisateurs.
- [x] Créer des utilisateurs inactifs.
- [x] Créer des préférences de notifications par profils :
  - très notifié ;
  - web seulement ;
  - email seulement ;
  - silence presque total ;
  - deadlines uniquement.
- [x] Ajouter quelques vues personnalisées.

Résumé point 3 :

- `FullSimulationSeeder` crée 50 utilisateurs `simulation01@dazo.test` à `simulation50@dazo.test`.
- Les rôles sont répartis avec 1 superadmin, 4 admins et 45 utilisateurs simples.
- Les 8 premiers utilisateurs sont animateurs globaux, et les 4 derniers sont inactifs.
- Chaque utilisateur reçoit des préférences de notification selon un des 5 profils prévus.
- Des `custom_views` et `dashboard_widgets` sont générés pour couvrir plusieurs états UI.
- Le mot de passe commun reste `password`.

### 4. Générer les cercles et la hiérarchie

- [x] Ajouter un générateur de cercles avec noms métier.
- [x] Respecter les ratios de `CircleType`.
- [x] Créer les parents avant les enfants.
- [x] Affecter `parent_id` après création ou dans un second passage.
- [x] Créer les memberships via `circle_members`.
- [x] Garantir au moins un animateur par cercle.
- [ ] Garantir que l'auteur ou l'animateur d'une décision appartient au cercle quand c'est requis par l'UI.
- [x] Tester les cas d'héritage partiel :
  - 40 à 70% des membres du parent dans l'enfant ;
  - quelques membres uniquement dans le sous-cercle.

Résumé point 4 :

- `FullSimulationSeeder` crée 10 cercles préfixés `Simulation -`.
- La répartition cible est respectée : 5 `open`, 3 `closed`, 2 `observer_open`.
- La hiérarchie couvre 3 racines, plusieurs sous-cercles et un niveau 2.
- Les cas parent fermé -> enfant ouvert, parent ouvert -> enfant fermé et parent ouvert -> enfant ouvert sont couverts.
- Chaque cercle reçoit 6 à 24 membres selon son profil, avec 1 à 3 animateurs.
- Les sous-cercles héritent d'environ 40 à 70% des membres de leur parent puis ajoutent des membres propres.
- Les cercles `observer_open` reçoivent aussi des observateurs.
- La cohérence auteur/animateur avec le cercle reste à finaliser au moment du point décisions/participants.

### 5. Générer les taxonomies

- [x] Réutiliser `CategorySeeder`, `LabelSeeder`, `DecisionModelSeeder`.
- [x] Ajouter si besoin quelques catégories supplémentaires :
  - Opérations
  - Partenariats
  - Communication
  - Sécurité
- [x] Ajouter des labels utiles aux tests :
  - Urgent
  - Bloquant
  - Quick-win
  - Expérimental
  - Récurrent
  - Long terme
  - Confidentiel
  - À arbitrer

Résumé point 5 :

- `FullSimulationSeeder` appelle les seeders existants `CategorySeeder`, `LabelSeeder` et `DecisionModelSeeder`.
- Les catégories supplémentaires `Opérations`, `Partenariats`, `Communication` et `Sécurité` sont créées ou réactivées.
- Les labels complémentaires `Confidentiel`, `À arbitrer`, `Risque élevé`, `Dépendance externe` et `À communiquer` sont ajoutés.
- Un modèle `Décision Urgente` est ajouté pour les futurs scénarios en `emergency_mode`.
- La génération reste idempotente via `updateOrCreate`.

### 6. Générer les décisions par script

- [x] Ajouter une méthode `seedDecisions()` dans `FullSimulationSeeder`.
- [x] Ne pas écrire 200 décisions à la main dans `DecisionDataProvider`.
- [x] Créer des templates de titres par catégorie.
- [x] Créer des templates de contenu Markdown :
  - contexte ;
  - proposition ;
  - impacts ;
  - risques ;
  - budget ;
  - prochaines étapes.
- [x] Générer le statut selon les ratios cibles.
- [x] Générer la visibilité selon le ratio 90/10, avec pondération spéciale pour les brouillons.
- [x] Générer `priority` :
  - 0 normal ;
  - 1 important ;
  - 2 urgent.
- [x] Générer `emergency_mode` pour un petit nombre de décisions.
- [x] Générer `current_deadline` selon la phase :
  - passé pour certains terminaux ;
  - proche pour quelques urgences ;
  - futur pour phases actives.
- [x] Générer `revision_content` pour les décisions en `revision`.
- [x] Générer `status_before_suspension` pour les décisions suspendues.
- [x] Lier catégories et labels avec `sync`.

Résumé point 6 :

- `FullSimulationSeeder` génère 200 décisions préfixées `Simulation NNN`.
- La répartition est déterministe : 30 brouillons, 40 clarifications, 60 réactions, 40 objections, 5 révisions, 3 suspendues et 22 terminales.
- La visibilité cible est respectée avec 180 décisions publiques et 20 privées, dont une part importante de brouillons privés.
- 5 décisions sont en `emergency_mode` avec modèle `Décision Urgente`, priorité élevée et deadline proche.
- Les deadlines sont adaptées aux phases : futures pour les phases actives, passées pour certains états terminaux, nulles pour les brouillons.
- Les catégories et labels sont synchronisés via les pivots.
- Le contenu Markdown est préparé par template, mais sera écrit dans `DecisionVersion` au point 7.

### 7. Générer les versions de décisions par script

- [x] Créer au moins une `DecisionVersion` par décision.
- [x] Créer 2 à 4 versions pour les décisions en révision ou terminales.
- [x] Marquer une seule version `is_current = true`.
- [x] Chaîner `previous_version_id`.
- [x] Varier `change_reason` :
  - clarification intégrée ;
  - objection traitée ;
  - budget ajusté ;
  - périmètre réduit ;
  - délai modifié.
- [x] Répartir les dates de versions de façon chronologique.

Résumé point 7 :

- Chaque décision de simulation reçoit au moins une `DecisionVersion`.
- Les décisions en révision, suspendues ou terminales reçoivent 2 à 4 versions selon leur statut.
- Une seule version est marquée `is_current = true` par décision.
- Les versions sont chaînées via `previous_version_id`.
- Les contenus Markdown générés au point 6 sont maintenant écrits dans les versions.
- Les `change_reason` couvrent clarifications, objections, budget, périmètre et délais.
- Les auteurs de versions sont choisis parmi les utilisateurs actifs ; les participants correspondants seront alignés au point 8.

### 8. Générer les participants par script

- [x] Ajouter auteur et animateur via `DecisionParticipant`.
- [x] Ajouter quelques participants simples.
- [x] Ajouter quelques exclus pour les tests d'accès si le produit les exploite.
- [x] Créer `DecisionAnimatorLog` quand un animateur est assigné.
- [x] Éviter auteur = animateur quand le modèle exige `requires_distinct_animator`.
- [x] Garantir que les décisions privées ont suffisamment de participants pour être testables.

Résumé point 8 :

- Chaque décision reçoit un auteur aligné avec l'auteur de sa version courante.
- Les décisions non brouillon reçoivent un animateur, choisi en priorité parmi les animateurs du cercle.
- Les modèles qui exigent un animateur distinct évitent auteur = animateur.
- Des participants simples sont ajoutés depuis les membres actifs du cercle, avec un volume plus élevé pour les décisions privées.
- Quelques participants `excluded` sont générés pour tester les règles d'accès.
- Un `DecisionAnimatorLog` est créé pour chaque assignation d'animateur.
- Les participants/logs de chaque décision sont recréés proprement à chaque passage du seeder.

### 9. Générer les interactions par script

- [x] Ajouter `seedInteractionsForDecision(Decision $decision, DecisionVersion $version)`.
- [x] Adapter les feedbacks au statut courant :
  - `clarification` -> questions ;
  - `reaction` -> réactions ;
  - `objection` -> objections et suggestions ;
  - `revision` -> traces de l'ancienne objection ;
  - terminal -> historique d'interactions passées.
- [x] Générer 0 à 12 feedbacks par décision selon la phase.
- [x] Générer des statuts de feedback :
  - `submitted`
  - `treated`
  - `rejected`
  - `acknowledged`
  - `withdrawn`
  - `ignored`
- [x] Générer des réponses via `FeedbackMessage`.
- [x] Générer des soutiens via `FeedbackJoin` pour une partie des réactions et objections.
- [x] Éviter les doublons incohérents :
  - un utilisateur ne doit pas soutenir son propre feedback ;
  - un feedback de type non compatible avec la phase ne doit exister que comme historique.
- [x] Prévoir des textes variés :
  - question courte ;
  - objection argumentée ;
  - réaction positive ;
  - réaction réservée ;
  - suggestion de compromis ;
  - réponse d'animateur.

Résumé point 9 :

- `FullSimulationSeeder` génère les interactions après les participants, pour choisir des auteurs cohérents.
- Les brouillons restent presque silencieux ; les phases actives reçoivent des feedbacks adaptés.
- Les révisions et états terminaux conservent des traces historiques de réactions, objections et suggestions.
- Les feedbacks couvrent les statuts `submitted`, `treated`, `rejected`, `acknowledged`, `withdrawn` et `ignored`.
- Des `FeedbackMessage` sont ajoutés sur une partie des feedbacks pour simuler des discussions.
- Des `FeedbackJoin` sont ajoutés sur certaines réactions et objections sans auto-soutien.
- Les feedbacks existants des décisions de simulation sont recréés proprement à chaque passage.

### 10. Générer les consentements par script

- [x] Ajouter `seedConsentsForDecision(Decision $decision, DecisionVersion $version)`.
- [x] Choisir les signaux selon la phase :
  - clarification : `no_questions`, `abstention`
  - reaction : `no_reaction`, `abstention`
  - objection : `no_objection`, `abstention`
- [x] Créer un consentement par utilisateur et par phase maximum.
- [x] Respecter la contrainte unique existante.
- [x] Ne pas créer de consentement pour tous les membres sur toutes les décisions : garder des actions attendues.
- [x] Pour certains terminaux adoptés, simuler un consentement large.
- [x] Pour certains abandons, simuler peu de consentements et beaucoup d'objections.

Résumé point 10 :

- `FullSimulationSeeder` génère les consentements après les interactions.
- Les phases actives utilisent les signaux adaptés : `no_questions`, `no_reaction`, `no_objection` et `abstention`.
- Les décisions adoptées simulent un consentement large.
- Les décisions abandonnées, échues ou désertées gardent peu de consentements pour conserver une histoire d'échec.
- Le seeder respecte l'unicité `decision_version_id + user_id + phase`.
- Tous les membres ne consentent pas automatiquement, afin de garder des actions attendues dans l'interface.
- Les consentements des décisions de simulation sont recréés proprement à chaque passage.

### 11. Générer les pièces jointes par script

Statut : actif via le pool texte généré par `dazo-generate-fake-attachments.sh`.

- [x] Ajouter `seedAttachmentsForDecisionVersion(DecisionVersion $version)`.
- [x] Générer 0 à 5 pièces par décision.
- [x] Assigner `is_private` selon ratio 10%.
- [x] Varier les fichiers avec un catalogue texte :
  - `.txt`
  - `.md`
  - `.csv`
  - `.json`
- [x] Utiliser un uploader cohérent :
  - auteur ;
  - animateur ;
  - participant ;
  - membre du cercle.
- [x] Utiliser des fichiers physiques pour permettre les téléchargements.
- [x] Si les téléchargements doivent fonctionner, ajouter une option séparée pour créer des fichiers factices dans `storage/app/private/attachments/seed-pool`.

Résumé point 11 :

- `dazo-generate-fake-attachments.sh` génère un pool de PJ texte compatible avec `Storage::disk('local')`.
- `FullSimulationSeeder` lit ce pool et crée les lignes `attachments` reliées aux versions courantes.
- Les fichiers physiques sont réutilisés depuis `storage/app/private/attachments/seed-pool`.
- Les uploaders sont choisis parmi les participants non exclus de la décision.
- Le ratio privé/public est déterministe, environ 10% privé.
- `dazo-seed-full.sh` peut générer le pool automatiquement via `--attachments`.
- `dazo-tool.sh` expose la génération de PJ et le flux PJ + fresh + seed.

### 12. Générer les relations entre décisions

- [x] Créer 10 à 20 relations.
- [x] Éviter les cycles.
- [x] Mélanger :
  - `derives_from`
  - `blocks`
- [x] Créer des cas lisibles :
  - une décision sécurité bloque une décision produit ;
  - une décision budget dérive d'une décision stratégie ;
  - une décision RH dépend d'une décision juridique.

Résumé point 12 :

- Le point 11 est désormais actif via un pool texte local ; les relations restent indépendantes des pièces jointes.
- `FullSimulationSeeder` génère 16 relations déterministes entre décisions de simulation.
- Les relations mélangent `derives_from` et `blocks`.
- Les relations existantes impliquant les décisions de simulation sont supprimées puis recréées pour rester idempotentes.
- Les paires source/cible sont orientées de façon à éviter les auto-relations et les cycles.
- Les cas métier lisibles sont couverts par les catégories déjà réparties entre sécurité, produit, finance, stratégie, RH et juridique.

### 13. Générer les réglages utilisateur par décision

- [x] Créer des favoris avec `DecisionUserSetting`.
- [x] Varier `notification_level` :
  - `all`
  - `relevant`
  - `phase_change`
  - `none`
- [x] Faire suivre les décisions urgentes par plusieurs utilisateurs.
- [x] Faire ignorer certaines décisions par des utilisateurs peu actifs.

Résumé point 13 :

- `FullSimulationSeeder` génère des `DecisionUserSetting` après les relations.
- Chaque décision reçoit quelques réglages depuis ses participants.
- Les décisions en `emergency_mode` sont favorites pour plusieurs utilisateurs et suivies avec `all` ou `relevant`.
- Les niveaux de notification couvrent `all`, `relevant`, `phase_change` et `none`.
- Quelques utilisateurs inactifs reçoivent des réglages `none` pour tester les décisions ignorées.
- Les réglages des décisions de simulation sont supprimés puis recréés à chaque passage.

### 14. Ajouter un script shell d'exécution

- [x] Créer `dazo-seed-full.sh`.
- [x] Ajouter une entrée dans `docs/SCRIPTS.md`.
- [x] Script proposé :
  - confirmation interactive ;
  - option `--fresh` pour `migrate:fresh`;
  - option `--full` pour `db:seed --class=FullSimulationSeeder`;
  - option `--summary` pour afficher les volumes après seed ;
  - option `--no-confirm` pour CI/local rapide.
- [x] Ne pas effacer la base sans confirmation explicite.

Résumé point 14 :

- `dazo-seed-full.sh` permet de lancer le full seeder, de faire un `migrate:fresh`, et d'afficher un résumé.
- Le reset de base demande confirmation sauf avec `--no-confirm`.
- Le script propose aussi un menu interactif quand il est lancé sans option.
- `docs/SCRIPTS.md` documente les commandes disponibles.
- `dazo-tool.sh` expose les options seed full, fresh+seed, summary et menu avancé.

### 15. Ajouter des validations automatisées

- [x] Ajouter une commande Artisan ou une méthode finale `printAndValidateSummary()`.
- [x] Vérifier :
  - 50 utilisateurs environ ;
  - 10 cercles ;
  - 200+ décisions ;
  - 90% décisions publiques avec tolérance de 2% ;
  - 90% pièces jointes publiques avec tolérance de 2% ;
  - toutes les décisions ont une version courante ;
  - toutes les décisions ont un auteur ;
  - toutes les décisions non draft ont un animateur ;
  - toutes les pièces jointes ont uploader et version ;
  - aucune relation ne pointe vers la même décision ;
  - aucun cercle enfant ne pointe vers lui-même ;
  - aucun feedback sans auteur ;
  - aucun consentement avec phase invalide.
- [x] Afficher les anomalies en warning sans bloquer au début.
- [ ] Passer en exceptions une fois le seeder stabilisé.

Résumé point 15 :

- `FullSimulationSeeder` utilise maintenant `printAndValidateSummary()` en fin d'exécution.
- Le résumé porte sur le périmètre simulation pour ne pas être faussé par les données locales ou les seeders légers.
- Les validations contrôlent volumes, ratio public/privé, versions courantes, auteurs, animateurs, relations, hiérarchie, feedbacks et consentements.
- Les validations pièces jointes contrôlent le ratio public/privé et les liens version/uploader ; elles préviennent si aucun fichier n'a été généré.
- Les anomalies sont affichées en warnings non bloquants.
- Le passage en exceptions strictes reste volontairement différé jusqu'après un premier seed complet validé.

---

## Découpage technique recommandé

### Option A : un seul seeder structuré

Fichier principal :

- `database/seeders/FullSimulationSeeder.php`

Méthodes internes :

- `seedBaseTaxonomies()`
- `seedUsers()`
- `seedCircles()`
- `seedCircleMemberships()`
- `seedDecisions()`
- `seedDecisionVersions()`
- `seedParticipants()`
- `seedInteractionsForDecision()`
- `seedConsentsForDecision()`
- `seedAttachmentsForDecisionVersion()`
- `seedDecisionRelations()`
- `seedDecisionUserSettings()`
- `printAndValidateSummary()`

Avantage : plus simple à lancer et maintenir au début.

### Option B : seeders spécialisés

Fichiers :

- `FullSimulationSeeder.php`
- `FullUserSeeder.php`
- `FullCircleSeeder.php`
- `FullDecisionSeeder.php`
- `FullInteractionSeeder.php`
- `FullAttachmentSeeder.php`

Avantage : plus lisible si le seeder devient très volumineux.

Recommandation actuelle : commencer par l'option A, puis extraire quand le fichier dépasse un volume difficile à lire.

---

## Commandes cibles

### Lancement manuel sans reset

```bash
php artisan db:seed --class=FullSimulationSeeder
```

### Reset complet local

```bash
php artisan migrate:fresh
php artisan db:seed --class=FullSimulationSeeder
```

### Script shell cible

```bash
./dazo-seed-full.sh --fresh --full --summary
```

### Variante Docker

```bash
docker compose exec app php artisan db:seed --class=FullSimulationSeeder
```

---

## Ordre d'implémentation

1. Créer `FullSimulationSeeder` avec config, helpers et résumé.
2. Générer utilisateurs + préférences.
3. Générer cercles + memberships + hiérarchie.
4. Réutiliser catégories, labels et modèles.
5. Générer 200 décisions avec statuts, visibilité, priorité et deadline.
6. Générer versions et participants.
7. Générer interactions par phase.
8. Générer consentements par phase.
9. Générer pièces jointes avec `is_private`.
10. Générer relations et réglages utilisateur.
11. Ajouter validations et résumé.
12. Ajouter `dazo-seed-full.sh`.
13. Documenter dans `docs/SCRIPTS.md`.
14. Tester sur base fraîche.

---

## Validation supplémentaire

- Vérifier la cohérence visibilité décisions/pièces jointes.
- Tester l'héritage des membres dans les sous-cercles.
- Contrôler les règles d'accès par type de cercle.
- Tester les listes publiques avec décisions privées absentes.
- Tester les pièces jointes privées sur décisions publiques.
- Tester les décisions privées avec utilisateur participant et non participant.
- Tester les filtres par statut, cercle, catégorie, label, favori et notification.
- Tester la charge avec 200 décisions et 1 000+ pièces jointes.

---

## Definition of Done

- [ ] `php artisan db:seed --class=FullSimulationSeeder` s'exécute sans erreur.
- [ ] Le seeder est rejouable sur une base fraîche.
- [ ] Les volumes cibles sont atteints.
- [ ] Les ratios public/privé sont respectés.
- [ ] Les interactions sont cohérentes avec les phases.
- [ ] Les pièces jointes ont une visibilité indépendante.
- [ ] Les vues principales restent utilisables avec ce volume.
- [ ] Le script `dazo-seed-full.sh` existe et est documenté.
- [ ] Un résumé final permet de vérifier rapidement la qualité du seed.
