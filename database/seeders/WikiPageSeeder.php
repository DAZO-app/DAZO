<?php

namespace Database\Seeders;

use App\Models\WikiPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WikiPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Bienvenue sur DAZO',
                'category' => 'Introduction',
                'content' => '<h1>Bienvenue !</h1><p>DAZO est une plateforme de gouvernance partagée conçue pour faciliter la prise de décision collective et transparente.</p><blockquote>L\'objectif de DAZO est de redonner du pouvoir d\'agir aux membres d\'une organisation en structurant le chaos créatif par des processus clairs.</blockquote><h2>Comment commencer ?</h2><ul><li>Explorez les <strong>Cercles</strong> pour comprendre la structure de l\'organisation.</li><li>Consultez les <strong>Décisions</strong> en cours pour voir sur quoi travaille la communauté.</li><li>Participez en réagissant aux propositions !</li></ul>',
            ],
            [
                'title' => 'L\'Élection Sans Candidat',
                'category' => 'Gouvernance',
                'content' => '<h1>L\'Élection Sans Candidat (ESC)</h1><p>Contrairement au vote majoritaire classique, l\'ESC est un processus de recherche du candidat le plus apte pour une mission donnée, sans que personne n\'ait besoin de se porter volontaire au départ.</p><h2>Les étapes clés :</h2><ol><li><strong>Définition du rôle</strong> : On commence par définir clairement les responsabilités et les compétences nécessaires.</li><li><strong>Nominations</strong> : Chaque membre nomme la personne qu\'il juge la plus apte (il peut se nommer lui-même).</li><li><strong>Arguments</strong> : On expose pourquoi on a choisi cette personne.</li><li><strong>Renforcement</strong> : Possibilité de changer son vote après avoir entendu les arguments.</li><li><strong>Proposition de l\'animateur</strong> : L\'animateur propose un candidat sur la base des échanges.</li><li><strong>Objections</strong> : On cherche le consentement de tous sur la proposition.</li></ol>',
            ],
            [
                'title' => 'Comprendre le Consentement',
                'category' => 'Gouvernance',
                'content' => '<h1>Le Consentement vs Le Compromis</h1><p>Dans DAZO, nous visons le <strong>consentement</strong> : "Personne n\'a d\'objection raisonnable". Ce n\'est pas la moyenne des opinions (compromis) ni l\'accord enthousiaste de tous (consensus).</p><h2>Qu\'est-ce qu\'une objection ?</h2><p>Une objection n\'est pas une préférence personnelle ("Je préférerais une autre couleur"). C\'est une alerte sur un risque pour l\'organisation : <strong>"Cette décision va nuire à notre capacité à remplir notre mission."</strong></p><blockquote>"C\'est suffisamment bon pour l\'instant, et assez sûr pour essayer."</blockquote>',
            ],
            [
                'title' => 'Gérer ses notifications',
                'category' => 'Utilisation',
                'content' => '<h1>Paramètres et Notifications</h1><p>Pour rester informé sans être submergé, DAZO vous permet de configurer vos préférences.</p><ul><li><strong>Réactions attendues</strong> : Vous recevez un mail lorsqu\'une décision nécessite votre attention immédiate.</li><li><strong>Suivi de cercle</strong> : Abonnez-vous à un cercle pour voir toutes ses actualités.</li></ul><p>Vous pouvez modifier ces réglages dans l\'onglet <strong>Paramètres</strong> de votre profil.</p>',
            ]
        ];
        $categories = [];

        foreach ($pages as $p) {
            $catName = $p['category'];
            if (!isset($categories[$catName])) {
                $categories[$catName] = \App\Models\WikiCategory::create([
                    'name' => $catName,
                    'slug' => Str::slug($catName),
                    'order' => count($categories),
                ]);
            }

            WikiPage::create([
                'title' => $p['title'],
                'slug' => Str::slug($p['title']),
                'wiki_category_id' => $categories[$catName]->id,
                'content' => $p['content'],
                'is_published' => true,
            ]);
        }
    }
}
