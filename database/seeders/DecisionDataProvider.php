<?php

namespace Database\Seeders;

class DecisionDataProvider
{
    public static function decisions(): array
    {
        return [
            // 1. DRAFT — brouillon simple
            ['title'=>'Mise en place du télétravail 3j/semaine','circle'=>'RH & Culture','status'=>'draft','visibility'=>'private','priority'=>0,'emergency'=>false,'categories'=>['RH'],'labels'=>['Long terme'],'model'=>'Consentement (Standard)',
             'content'=>"# Proposition\n\n## Contexte\nSuite aux retours de l'enquête satisfaction, 78% des collaborateurs souhaitent plus de flexibilité.\n\n## Proposition\nPermettre jusqu'à 3 jours de télétravail par semaine pour tous les postes éligibles.\n\n## Impact attendu\n- Meilleure qualité de vie\n- Réduction des coûts immobiliers\n- Risque d'isolement à monitorer",
             'versions'=>1,'feedbacks'=>[],'consents'=>[],'attachments'=>0],

            // 2. DRAFT — avec pièce jointe
            ['title'=>'Nouveau logo de l\'organisation','circle'=>'Produit','status'=>'draft','visibility'=>'public','priority'=>0,'emergency'=>false,'categories'=>['Stratégie'],'labels'=>['Expérimental'],'model'=>'Avis Sollicité',
             'content'=>"# Consultation\n\n## Question\nQuel logo préférez-vous parmi les 3 propositions du designer ?\n\n## Contexte\nNotre identité visuelle date de 2018 et ne reflète plus nos valeurs actuelles.",
             'versions'=>1,'feedbacks'=>[],'consents'=>[],'attachments'=>2],

            // 3. CLARIFICATION — questions en cours
            ['title'=>'Migration vers PostgreSQL','circle'=>'Technique','status'=>'clarification','visibility'=>'public','priority'=>1,'emergency'=>false,'categories'=>['Tech'],'labels'=>['Bloquant','Long terme'],'model'=>'Consentement (Standard)',
             'content'=>"# Proposition\n\n## Contexte\nMySQL montre ses limites sur les requêtes JSON et le volume de données.\n\n## Détails\nMigrer l'ensemble de la stack vers PostgreSQL 16 avec réplication.\n\n## Plan\n1. Audit des requêtes existantes\n2. Migration schéma\n3. Tests de charge\n4. Bascule progressive",
             'versions'=>1,'feedbacks'=>[
                 ['type'=>'clarification','status'=>'submitted','content'=>'Quel est le coût estimé de la migration en jours-homme ?','author'=>'david@dazo.test'],
                 ['type'=>'clarification','status'=>'treated','content'=>'Est-ce compatible avec notre hébergeur actuel ?','author'=>'franck@dazo.test','messages'=>['Oui, OVH supporte PG16 nativement.']],
             ],'consents'=>[['signal'=>'no_questions','phase'=>'clarification','users'=>['emma@dazo.test']]],'attachments'=>1],

            // 4. CLARIFICATION — tout le monde a répondu sauf un
            ['title'=>'Horaires flexibles pour l\'été','circle'=>'RH & Culture','status'=>'clarification','visibility'=>'public','priority'=>0,'emergency'=>false,'categories'=>['RH'],'labels'=>['Récurrent'],'model'=>'Consentement (Standard)',
             'content'=>"# Proposition\n\n## Contexte\nChaque été, les collaborateurs demandent des aménagements horaires.\n\n## Proposition\nDu 1er juin au 31 août : arrivée libre entre 7h et 10h, départ après 6h de travail effectif.",
             'versions'=>1,'feedbacks'=>[
                 ['type'=>'clarification','status'=>'submitted','content'=>'Cela s\'applique-t-il aussi aux alternants ?','author'=>'emma@dazo.test'],
             ],'consents'=>[['signal'=>'no_questions','phase'=>'clarification','users'=>['hugo@dazo.test','gaelle@dazo.test']]],'attachments'=>0],

            // 5. REACTION — avec réactions variées
            ['title'=>'Adopter Slack comme outil principal','circle'=>'Technique','status'=>'reaction','visibility'=>'public','priority'=>0,'emergency'=>false,'categories'=>['Tech','Stratégie'],'labels'=>['Quick-win'],'model'=>'Consentement (Standard)',
             'content'=>"# Proposition\n\n## Contexte\nNous utilisons actuellement 3 outils de communication (email, Teams, WhatsApp).\n\n## Proposition\nCentraliser sur Slack avec les intégrations GitHub, Jira et Google Calendar.\n\n## Budget\n12€/utilisateur/mois soit ~1 440€/an pour l'équipe.",
             'versions'=>1,'feedbacks'=>[
                 ['type'=>'reaction','status'=>'submitted','content'=>'Très favorable ! On gagne en productivité.','author'=>'david@dazo.test'],
                 ['type'=>'reaction','status'=>'submitted','content'=>'Je préfèrerais Discord, c\'est gratuit et on y est déjà.','author'=>'franck@dazo.test'],
                 ['type'=>'reaction','status'=>'submitted','content'=>'OK pour moi tant qu\'on garde l\'email pour l\'externe.','author'=>'emma@dazo.test'],
             ],'consents'=>[['signal'=>'no_reaction','phase'=>'reaction','users'=>['user@dazo.test']]],'attachments'=>0,'deadline'=>'+3 days'],

            // 6. REACTION — mode urgence
            ['title'=>'Patch de sécurité critique CVE-2026-1234','circle'=>'Technique','status'=>'reaction','visibility'=>'public','priority'=>2,'emergency'=>true,'categories'=>['Tech'],'labels'=>['Urgent','Bloquant'],'model'=>'Consentement (Standard)',
             'content'=>"# Proposition\n\n## Contexte\nUne faille critique (CVSS 9.8) a été identifiée dans notre framework.\n\n## Action requise\nDéployer le patch v3.2.1 en production ce soir.\n\n## Risque si inaction\nExposition des données utilisateurs.",
             'versions'=>1,'feedbacks'=>[
                 ['type'=>'reaction','status'=>'submitted','content'=>'Il faut agir immédiatement, je valide.','author'=>'claire@dazo.test'],
             ],'consents'=>[['signal'=>'no_reaction','phase'=>'reaction','users'=>['david@dazo.test','franck@dazo.test']]],'attachments'=>1,'deadline'=>'+1 day'],

            // 7. OBJECTION — avec objection active
            ['title'=>'Supprimer la pause déjeuner imposée','circle'=>'RH & Culture','status'=>'objection','visibility'=>'public','priority'=>0,'emergency'=>false,'categories'=>['RH'],'labels'=>[],'model'=>'Consentement (Standard)',
             'content'=>"# Proposition\n\n## Contexte\nLa pause de 12h à 13h30 est jugée trop rigide par certains.\n\n## Proposition\nLaisser chacun organiser sa pause librement (min 30 min obligatoire).",
             'versions'=>1,'feedbacks'=>[
                 ['type'=>'objection','status'=>'submitted','content'=>'Cela va fragmenter les réunions d\'équipe. On ne trouvera plus de créneau commun.','author'=>'hugo@dazo.test'],
                 ['type'=>'suggestion','status'=>'submitted','content'=>'On pourrait garder un créneau protégé de 12h à 13h où aucune réunion n\'est planifiée.','author'=>'emma@dazo.test'],
             ],'consents'=>[['signal'=>'no_objection','phase'=>'objection','users'=>['user@dazo.test','david@dazo.test','gaelle@dazo.test']]],'attachments'=>0,'deadline'=>'+5 days'],

            // 8. OBJECTION — tout le monde consent
            ['title'=>'Passer au format OKR trimestriel','circle'=>'Coordination','status'=>'objection','visibility'=>'public','priority'=>1,'emergency'=>false,'categories'=>['Stratégie'],'labels'=>['Long terme'],'model'=>'Consentement (Standard)',
             'content'=>"# Proposition\n\n## Contexte\nNos objectifs annuels manquent de réactivité face aux changements du marché.\n\n## Proposition\nAdopter le framework OKR avec des cycles de 3 mois et des revues mensuelles.",
             'versions'=>1,'feedbacks'=>[],'consents'=>[['signal'=>'no_objection','phase'=>'objection','users'=>['user@dazo.test','claire@dazo.test','hugo@dazo.test']]],'attachments'=>1,'deadline'=>'+2 days'],

            // 9. REVISION — en cours de révision après objection
            ['title'=>'Politique de remboursement des frais','circle'=>'Finance & Budget','status'=>'revision','visibility'=>'public','priority'=>0,'emergency'=>false,'categories'=>['Finance'],'labels'=>['Récurrent'],'model'=>'Consentement (Standard)',
             'content'=>"# Proposition (v2)\n\n## Contexte\nLa v1 a été objectée car le plafond de 50€ était jugé trop bas.\n\n## Nouvelle proposition\nPlafond de 100€/mois sans justificatif, au-delà sur note de frais classique.",
             'versions'=>2,'v1_content'=>"# Proposition\n\n## Détails\nPlafond de 50€/mois pour les frais professionnels sans justificatif.",'change_reason'=>'Relèvement du plafond suite à l\'objection de David',
             'feedbacks'=>[
                 ['type'=>'objection','status'=>'treated','content'=>'50€ est insuffisant pour couvrir les déplacements réguliers.','author'=>'david@dazo.test','version'=>1],
             ],'consents'=>[['signal'=>'no_objection','phase'=>'objection','users'=>['hugo@dazo.test'],'version'=>1]],'attachments'=>0,
             'revision_content'=>"# Proposition (v3 – brouillon)\n\nPlafond de 120€/mois. Justificatif au-delà de 80€."],

            // 10. ADOPTED — décision adoptée récemment
            ['title'=>'Convention de nommage des branches Git','circle'=>'Technique','status'=>'adopted','visibility'=>'public','priority'=>0,'emergency'=>false,'categories'=>['Tech'],'labels'=>['Quick-win','Récurrent'],'model'=>'Consentement (Standard)',
             'content'=>"# Proposition\n\n## Convention\n- `feature/TICKET-description`\n- `fix/TICKET-description`\n- `hotfix/description`\n\n## Merge\nSquash & merge systématique sur main.",
             'versions'=>1,'feedbacks'=>[],'consents'=>[['signal'=>'no_objection','phase'=>'objection','users'=>['claire@dazo.test','david@dazo.test','franck@dazo.test','emma@dazo.test','user@dazo.test']]],'attachments'=>0,'created_ago'=>'-15 days'],

            // 11. ADOPTED — avec 2 versions (révisée puis adoptée)
            ['title'=>'Budget formation annuel par collaborateur','circle'=>'Finance & Budget','status'=>'adopted','visibility'=>'public','priority'=>1,'emergency'=>false,'categories'=>['Finance','RH'],'labels'=>['Long terme'],'model'=>'Consentement (Standard)',
             'content'=>"# Proposition (v2)\n\n## Budget\n1 500€/an/personne (au lieu de 1 000€ en v1).\n\n## Conditions\n- Formation en lien avec le poste ou l'évolution souhaitée\n- Validation par le cercle concerné",
             'versions'=>2,'v1_content'=>"# Proposition\n\n## Budget\n1 000€/an/personne pour la formation continue.",'change_reason'=>'Augmentation du budget suite aux retours',
             'feedbacks'=>[
                 ['type'=>'reaction','status'=>'acknowledged','content'=>'1000€ c\'est trop peu pour des formations certifiantes.','author'=>'emma@dazo.test','version'=>1],
             ],'consents'=>[['signal'=>'no_objection','phase'=>'objection','users'=>['hugo@dazo.test','alice@dazo.test','david@dazo.test']]],'attachments'=>1,'created_ago'=>'-30 days'],

            // 12. ADOPTED_OVERRIDE — adoptée par override admin
            ['title'=>'Fermeture exceptionnelle du 24 décembre','circle'=>'Coordination','status'=>'adopted_override','visibility'=>'public','priority'=>0,'emergency'=>false,'categories'=>['RH'],'labels'=>[],'model'=>'Consentement (Standard)',
             'content'=>"# Décision administrative\n\nFermeture de l'organisation le 24 décembre.\nJournée offerte (non décomptée des congés).",
             'versions'=>1,'feedbacks'=>[],'consents'=>[],'attachments'=>0,'created_ago'=>'-60 days'],

            // 13. ABANDONED — décision abandonnée
            ['title'=>'Passage à la semaine de 4 jours','circle'=>'RH & Culture','status'=>'abandoned','visibility'=>'public','priority'=>0,'emergency'=>false,'categories'=>['RH','Stratégie'],'labels'=>['Expérimental'],'model'=>'Consentement (Standard)',
             'content'=>"# Proposition\n\n## Idée\nTester la semaine de 4 jours sur un trimestre.\n\n## Raison de l'abandon\nAprès analyse, la charge client ne le permet pas à court terme.",
             'versions'=>1,'feedbacks'=>[
                 ['type'=>'objection','status'=>'rejected','content'=>'Nos clients attendent une disponibilité 5j/7.','author'=>'hugo@dazo.test'],
                 ['type'=>'objection','status'=>'submitted','content'=>'Le planning projet ne le permet pas avant 2027.','author'=>'alice@dazo.test'],
             ],'consents'=>[],'attachments'=>0,'created_ago'=>'-45 days'],

            // 14. SUSPENDED — décision suspendue
            ['title'=>'Déménagement des bureaux','circle'=>'Coordination','status'=>'suspended','visibility'=>'private','priority'=>1,'emergency'=>false,'categories'=>['Stratégie','Finance'],'labels'=>['Long terme'],'model'=>'Consentement (Standard)',
             'content'=>"# Proposition\n\n## Contexte\nLe bail actuel expire en septembre 2027.\n\n## Options étudiées\n1. Renouvellement avec négociation\n2. Déménagement vers un espace plus petit\n3. Full remote + coworking\n\n## Statut\nSuspendu en attente des résultats financiers Q2.",
             'versions'=>1,'status_before'=>'reaction','feedbacks'=>[
                 ['type'=>'reaction','status'=>'submitted','content'=>'L\'option 3 serait la plus économique.','author'=>'david@dazo.test'],
             ],'consents'=>[],'attachments'=>2,'created_ago'=>'-20 days'],

            // 15. LAPSED — expirée sans participation
            ['title'=>'Changement de fournisseur café','circle'=>'RH & Culture','status'=>'lapsed','visibility'=>'public','priority'=>0,'emergency'=>false,'categories'=>['RH'],'labels'=>[],'model'=>'Avis Sollicité',
             'content'=>"# Consultation\n\n## Question\nSouhaitez-vous changer pour un café équitable local ?\n\n## Détail\nCoût supplémentaire de 15€/mois.",
             'versions'=>1,'feedbacks'=>[],'consents'=>[],'attachments'=>0,'created_ago'=>'-90 days'],

            // 16. CLARIFICATION avec avis sollicité
            ['title'=>'Choix du prestataire audit RGPD','circle'=>'Technique','status'=>'clarification','visibility'=>'private','priority'=>1,'emergency'=>false,'categories'=>['Tech','Finance'],'labels'=>['Urgent'],'model'=>'Avis Sollicité',
             'content'=>"# Consultation\n\n## Question\nQuel prestataire retenir pour l'audit RGPD annuel ?\n\n## Options\n1. **DataProtect** – 8 000€, délai 3 semaines\n2. **SecureLaw** – 12 000€, délai 2 semaines\n3. **PrivacyFirst** – 6 500€, délai 5 semaines",
             'versions'=>1,'feedbacks'=>[
                 ['type'=>'clarification','status'=>'submitted','content'=>'DataProtect a-t-il des références dans notre secteur ?','author'=>'hugo@dazo.test'],
                 ['type'=>'clarification','status'=>'submitted','content'=>'Le délai de 5 semaines est-il compatible avec notre échéance légale ?','author'=>'claire@dazo.test'],
             ],'consents'=>[],'attachments'=>2],

            // 17. REACTION — produit
            ['title'=>'Refonte de la page d\'accueil','circle'=>'Produit','status'=>'reaction','visibility'=>'public','priority'=>0,'emergency'=>false,'categories'=>['Tech','Stratégie'],'labels'=>['Expérimental'],'model'=>'Consentement (Standard)',
             'content'=>"# Proposition\n\n## Contexte\nLe taux de rebond de la page d'accueil est de 65%.\n\n## Proposition\n- Hero section avec vidéo\n- Témoignages clients\n- CTA unique vers l'essai gratuit\n\n## Maquettes\nVoir les pièces jointes.",
             'versions'=>1,'feedbacks'=>[
                 ['type'=>'reaction','status'=>'submitted','content'=>'La vidéo risque de ralentir le chargement. Pensons au lazy loading.','author'=>'franck@dazo.test'],
                 ['type'=>'reaction','status'=>'submitted','content'=>'J\'adore la direction ! Les maquettes sont très propres.','author'=>'alice@dazo.test'],
             ],'consents'=>[['signal'=>'no_reaction','phase'=>'reaction','users'=>['claire@dazo.test']]],'attachments'=>3,'deadline'=>'+4 days'],

            // 18. OBJECTION — finance
            ['title'=>'Augmentation générale de 3%','circle'=>'Finance & Budget','status'=>'objection','visibility'=>'private','priority'=>2,'emergency'=>false,'categories'=>['Finance','RH'],'labels'=>['Urgent'],'model'=>'Consentement (Standard)',
             'content'=>"# Proposition\n\n## Contexte\nInflation de 4,2% sur les 12 derniers mois.\n\n## Proposition\nAugmentation générale de 3% au 1er juillet pour tous les salariés.",
             'versions'=>1,'feedbacks'=>[
                 ['type'=>'objection','status'=>'submitted','content'=>'3% ne compense pas l\'inflation. Cela revient à une baisse de pouvoir d\'achat.','author'=>'emma@dazo.test'],
                 ['type'=>'suggestion','status'=>'submitted','content'=>'Proposer 3% + prime exceptionnelle de 500€ pour compenser.','author'=>'claire@dazo.test'],
             ],'consents'=>[['signal'=>'no_objection','phase'=>'objection','users'=>['david@dazo.test']],['signal'=>'abstention','phase'=>'objection','users'=>['hugo@dazo.test']]],'attachments'=>0,'deadline'=>'+6 days'],

            // 19. ADOPTED — ancienne avec 3 versions
            ['title'=>'Charte de communication interne','circle'=>'Coordination','status'=>'adopted','visibility'=>'public','priority'=>0,'emergency'=>false,'categories'=>['Stratégie'],'labels'=>['Récurrent'],'model'=>'Consentement (Standard)',
             'content'=>"# Charte v3\n\n## Principes\n1. Transparence par défaut\n2. Communication asynchrone privilégiée\n3. Réponse sous 24h ouvrées\n4. Pas de message professionnel après 19h",
             'versions'=>3,'v1_content'=>"# Charte v1\n\nTransparence et communication bienveillante.",'v2_content'=>"# Charte v2\n\n1. Transparence\n2. Async first\n3. Réponse sous 48h",
             'change_reason'=>'Ajout de la règle des 19h suite aux retours','change_reason_v2'=>'Passage de 48h à 24h pour les réponses',
             'feedbacks'=>[],'consents'=>[['signal'=>'no_objection','phase'=>'objection','users'=>['user@dazo.test','claire@dazo.test','hugo@dazo.test','alice@dazo.test']]],'attachments'=>0,'created_ago'=>'-120 days'],

            // 20. DESERTED — aucun participant
            ['title'=>'Renouvellement abonnement salle de sport','circle'=>'RH & Culture','status'=>'deserted','visibility'=>'public','priority'=>0,'emergency'=>false,'categories'=>['RH'],'labels'=>[],'model'=>'Avis Sollicité',
             'content'=>"# Consultation\n\n## Question\nRenouveler l'abonnement corporate à FitClub (3 600€/an) ?\n\n## Contexte\nSeulement 4 personnes utilisent l'abonnement régulièrement.",
             'versions'=>1,'feedbacks'=>[],'consents'=>[],'attachments'=>0,'created_ago'=>'-75 days'],

            // 21. DRAFT — avec révision en cours
            ['title'=>'Mise en place d\'un comité éthique','circle'=>'Coordination','status'=>'draft','visibility'=>'public','priority'=>1,'emergency'=>false,'categories'=>['Stratégie','RH'],'labels'=>['Long terme','Expérimental'],'model'=>'Consentement (Standard)',
             'content'=>"# Proposition\n\n## Contexte\nAvec la croissance, nous avons besoin d'un cadre éthique formel.\n\n## Proposition\nCréer un comité de 3 personnes renouvelé annuellement par élection sans candidat.",
             'versions'=>1,'feedbacks'=>[],'consents'=>[],'attachments'=>1],

            // 22. REACTION — deadline proche
            ['title'=>'Sponsoring conférence DevFest 2026','circle'=>'Produit','status'=>'reaction','visibility'=>'public','priority'=>0,'emergency'=>false,'categories'=>['Stratégie','Finance'],'labels'=>['Quick-win'],'model'=>'Avis Sollicité',
             'content'=>"# Consultation\n\n## Question\nSponsoriser le DevFest Nantes 2026 (stand Gold à 5 000€) ?\n\n## Arguments pour\n- Visibilité auprès de 2 000 développeurs\n- Recrutement potentiel\n- Networking",
             'versions'=>1,'feedbacks'=>[
                 ['type'=>'reaction','status'=>'submitted','content'=>'Excellente opportunité de recrutement tech.','author'=>'claire@dazo.test'],
                 ['type'=>'reaction','status'=>'submitted','content'=>'5 000€ c\'est beaucoup. Un stand Silver à 2 500€ suffirait.','author'=>'david@dazo.test'],
             ],'consents'=>[],'attachments'=>1,'deadline'=>'+1 day'],
        ];
    }
}
