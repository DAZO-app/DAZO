<?php

namespace Database\Seeders;

use App\Models\WikiCategory;
use App\Models\WikiPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WikiPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nettoyage des anciennes données
        Schema::disableForeignKeyConstraints();
        WikiPage::truncate();
        WikiCategory::truncate();
        Schema::enableForeignKeyConstraints();

        $structure = [
            'Méthode DAZO' => [
                'Introduction à la Sociocratie et au Consentement' => '<h1>La Sociocratie & Le Consentement</h1><p>La sociocratie est un mode de gouvernance qui permet à une organisation, quelle que soit sa taille, de fonctionner efficacement selon un mode auto-organisé caractérisé par une prise de décision distribuée.</p><h2>Le Consentement</h2><p>Contrairement au consensus (où tout le monde doit être d\'accord) ou au vote (où la majorité l\'emporte), le <strong>consentement</strong> repose sur l\'absence d\'objection. On ne cherche pas la perfection, mais une décision "suffisamment bonne pour l\'instant et assez sûre pour être essayée".</p>',
                'Le processus de décision par le cercle' => '<h1>Le Cercle : Cœur de DAZO</h1><p>Dans DAZO, une organisation est découpée en <strong>Cercles</strong>. Chaque cercle est souverain dans son périmètre et suit un cycle de décision structuré pour faire évoluer ses règles et projets.</p><h2>Cycle de vie</h2><ol><li>Brouillon</li><li>Clarification</li><li>Réaction</li><li>Objection</li><li>Adoption / Révision</li></ol>',
                'Qu\'est-ce qu\'une Objection ?' => '<h1>Comprendre l\'Objection</h1><p>Une objection n\'est pas une préférence personnelle ou un avis divergent. C\'est une information précieuse qui indique qu\'une proposition risque d\'entraver la mission du cercle ou de l\'organisation.</p><blockquote>"Est-ce que cette proposition limite ma capacité à travailler ?"</blockquote><p>Si la réponse est oui, c\'est une objection raisonnable qui doit être intégrée pour améliorer la proposition.</p>',
                'Rôles et responsabilités' => '<h1>Les Rôles dans DAZO</h1><ul><li><strong>Porteur</strong> : Rédige et porte la proposition initiale.</li><li><strong>Animateur</strong> : Facilite le processus, garantit que chacun est entendu et gère les tours de parole.</li><li><strong>Participant</strong> : Membre du cercle qui apporte ses clarifications, réactions et objections.</li><li><strong>Observateur</strong> : Peut consulter la décision sans intervenir dans le processus de consentement.</li></ul>',
            ],
            'Gestion des Décisions' => [
                'Créer une nouvelle proposition' => '<h1>Lancer une proposition</h1><p>Pour créer une décision, cliquez sur le bouton <strong>"Nouvelle décision"</strong>. Choisissez le cercle concerné et rédigez un titre clair ainsi qu\'un contenu détaillé. Vous pouvez joindre des documents pour étayer votre demande.</p>',
                'Phase 1 : Tour de Clarification' => '<h1>Tour de Clarification</h1><p>L\'objectif est de s\'assurer que tout le monde comprend la proposition. On ne donne pas encore son avis, on pose des questions pour lever les ambiguïtés.</p>',
                'Phase 2 : Tour de Réaction' => '<h1>Tour de Réaction</h1><p>Chaque participant exprime son ressenti, ses encouragements ou ses craintes. C\'est le moment de la parole libre et de l\'intelligence collective.</p>',
                'Phase 3 : Tour d\'Objection' => '<h1>Tour d\'Objection</h1><p>On cherche à savoir si quelqu\'un voit un risque. L\'animateur demande à chaque membre : "As-tu une objection ?".</p>',
                'Phase 4 : Adoption ou Révision' => '<h1>Finalisation</h1><p>Si aucune objection n\'est soulevée, la décision est <strong>Adoptée</strong>. Si des objections sont présentes, la décision passe en phase de <strong>Révision</strong> pour que le porteur puisse ajuster son texte.</p>',
                'Historique et traçabilité' => '<h1>Versions & Historique</h1><p>Chaque modification majeure crée une nouvelle version. Vous pouvez consulter l\'historique complet pour comprendre comment une décision a évolué au fil des objections et des révisions.</p>',
                'Gestion des pièces jointes' => '<h1>Documents liés</h1><p>Vous pouvez téléverser des PDF, images ou documents Office. Les pièces jointes sont liées à une version spécifique de la décision pour garantir la cohérence des archives.</p>',
            ],
            'Mode Réunion' => [
                'Utiliser DAZO en réunion' => '<h1>Le Meeting Mode</h1><p>Le mode réunion transforme l\'interface pour faciliter la projection sur écran. Il affiche les éléments essentiels de manière simplifiée pour que le cercle puisse se concentrer sur l\'échange oral.</p>',
                'Animation d\'un tour de table' => '<h1>Facilitation numérique</h1><p>L\'animateur peut voir en un coup d\'œil qui a déjà parlé et qui reste à entendre, garantissant une équité de parole parfaite.</p>',
            ],
            'Éléments de Navigation' => [
                'Utilisation de la Sidebar' => '<h1>La Barre Latérale</h1><p>C\'est votre centre de commande. Elle contient vos raccourcis vers le Tableau de bord, la liste globale des décisions, vos favoris et vos cercles.</p>',
                'Navigation mobile' => '<h1>DAZO en mobilité</h1><p>Sur mobile, les menus sont condensés dans la Topbar. Vous pouvez accéder à toutes les fonctionnalités essentielles via le menu hamburger.</p>',
                'Recherche et filtres' => '<h1>Trouver une information</h1><p>La recherche globale scanne les titres et contenus. Les filtres permettent de cibler par état (ex: "Adoptée"), par cercle ou par catégorie.</p>',
            ],
            'Tableau de Bord et Vues' => [
                'Personnaliser son Dashboard' => '<h1>Votre Cockpit</h1><p>Dans vos <strong>Paramètres</strong>, vous pouvez choisir quels widgets afficher sur votre accueil (Statistiques, Urgences, Mes propositions, etc.).</p>',
                'Gérer "Mes Vues"' => '<h1>Raccourcis personnalisés</h1><p>Créez des "Vues" basées sur vos filtres préférés pour les retrouver instantanément dans votre barre latérale.</p>',
                'Actions en attente' => '<h1>Réactions attendues</h1><p>Ce bloc liste toutes les décisions où votre action est requise (clarification, réaction ou objection) avant l\'échéance.</p>',
            ],
            'Paramètres Utilisateur' => [
                'Profil et Sécurité' => '<h1>Votre Compte</h1><p>Gérez votre avatar, vos informations personnelles et changez votre mot de passe régulièrement pour garantir la sécurité de votre accès.</p>',
                'Notifications' => '<h1>Alertes</h1><p>Configurez si vous souhaitez recevoir des emails pour chaque nouvelle décision ou uniquement des rappels 24h avant les échéances.</p>',
                'Confidentialité et RGPD' => '<h1>Vos Données</h1><p>Vous disposez d\'un droit d\'export complet de vos données au format JSON et de la possibilité de supprimer votre compte de manière anonymisée.</p>',
            ],
            'Administration du Site' => [
                'Configuration générale' => '<h1>Réglages Instance</h1><p>Les administrateurs peuvent changer le nom de l\'organisation et le logo affiché sur l\'ensemble du site.</p>',
                'Catégories et Labels' => '<h1>Taxonomie</h1><p>Organisez vos décisions par thématiques (RH, Finance, Technique...) via le système de catégories administrables.</p>',
                'Widgets externes' => '<h1>Snippets</h1><p>Générez des codes d\'intégration pour afficher vos décisions publiques sur d\'autres sites web.</p>',
            ],
            'Administration Système' => [
                'Sauvegardes et Maintenance' => '<h1>Gestion Technique</h1><p>Le Super-Admin peut gérer les sauvegardes de la base de données, monitorer les ressources serveur et consulter les journaux d\'audit pour la sécurité.</p>',
            ],
        ];

        $catOrder = 0;
        foreach ($structure as $catName => $pages) {
            $category = WikiCategory::create([
                'name' => $catName,
                'slug' => Str::slug($catName),
                'order' => $catOrder++,
            ]);

            $pageOrder = 0;
            foreach ($pages as $title => $content) {
                WikiPage::create([
                    'wiki_category_id' => $category->id,
                    'title' => $title,
                    'slug' => Str::slug($title),
                    'content' => $content,
                    'is_published' => true,
                    'order' => $pageOrder++,
                ]);
            }
        }
    }
}

