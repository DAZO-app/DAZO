<?php

namespace Database\Seeders;

use App\Models\DecisionModel;
use Illuminate\Database\Seeder;

class DecisionModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $models = [
            [
                'name' => 'Consentement (Standard)',
                'description' => 'Décision par absence d\'objection majeure.',
                'template_content' => "# Proposition\n\n## Contexte\n...\n\n## Détails\n...",
                'requires_distinct_animator' => true,
                'default_objection_days' => 5,
            ],
            [
                'name' => 'Avis Sollicité',
                'description' => 'Le porteur prend la décision après avoir écouté les avis.',
                'template_content' => "# Consultation\n\n## Question\n...\n\n## Public cible\n...",
                'requires_distinct_animator' => false,
                'default_objection_days' => 3,
            ],
        ];

        foreach ($models as $model) {
            DecisionModel::updateOrCreate(['name' => $model['name']], $model);
        }
    }
}
