<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Stratégie', 'description' => 'Orientations long terme', 'color_hex' => '#1e40af', 'icon' => 'trending-up'],
            ['name' => 'RH', 'description' => 'Gestion de l\'humain', 'color_hex' => '#9d174d', 'icon' => 'users'],
            ['name' => 'Finance', 'description' => 'Budgets et dépenses', 'color_hex' => '#065f46', 'icon' => 'credit-card'],
            ['name' => 'Tech', 'description' => 'Outils et infrastructure', 'color_hex' => '#374151', 'icon' => 'code'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
