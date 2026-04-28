<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Stratégie',  'description' => 'Orientations long terme et vision',      'color_hex' => '#1e40af', 'icon' => 'trending-up'],
            ['name' => 'RH',         'description' => 'Gestion de l\'humain et bien-être',       'color_hex' => '#9d174d', 'icon' => 'users'],
            ['name' => 'Finance',    'description' => 'Budgets, dépenses et investissements',    'color_hex' => '#065f46', 'icon' => 'credit-card'],
            ['name' => 'Tech',       'description' => 'Outils, infrastructure et architecture',  'color_hex' => '#374151', 'icon' => 'code'],
            ['name' => 'Juridique',  'description' => 'Conformité, contrats et réglementation',  'color_hex' => '#92400e', 'icon' => 'shield'],
            ['name' => 'Produit',    'description' => 'Roadmap, UX et fonctionnalités',          'color_hex' => '#5b21b6', 'icon' => 'package'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
