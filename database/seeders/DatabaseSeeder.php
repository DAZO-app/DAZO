<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            LabelSeeder::class,
            DecisionModelSeeder::class,
            CircleSeeder::class,
            DecisionSeeder::class,
            WikiPageSeeder::class,
        ]);
    }
}
