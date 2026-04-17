<?php

namespace Database\Seeders;

use App\Enums\CircleType;
use App\Models\Circle;
use Illuminate\Database\Seeder;

class CircleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coordination = Circle::updateOrCreate(
            ['name' => 'Coordination'],
            [
                'description' => 'Cercle de pilotage global',
                'type' => CircleType::CLOSED,
            ]
        );

        Circle::updateOrCreate(
            ['name' => 'Technique'],
            [
                'description' => 'Cercle dédié à l\'infrastructure et au développement',
                'type' => CircleType::OPEN,
                'parent_id' => $coordination->id,
            ]
        );

        Circle::updateOrCreate(
            ['name' => 'RH & Culture'],
            [
                'description' => 'Bien-être et recrutement',
                'type' => CircleType::OBSERVER_OPEN,
                'parent_id' => $coordination->id,
            ]
        );
    }
}
