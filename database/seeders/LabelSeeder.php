<?php

namespace Database\Seeders;

use App\Models\Label;
use Illuminate\Database\Seeder;

class LabelSeeder extends Seeder
{
    public function run(): void
    {
        $labels = [
            ['name' => 'Urgent',       'color_hex' => '#dc2626'],
            ['name' => 'Quick-win',    'color_hex' => '#16a34a'],
            ['name' => 'Long terme',   'color_hex' => '#2563eb'],
            ['name' => 'Expérimental', 'color_hex' => '#9333ea'],
            ['name' => 'Récurrent',    'color_hex' => '#ca8a04'],
            ['name' => 'Bloquant',     'color_hex' => '#e11d48'],
        ];

        foreach ($labels as $label) {
            Label::updateOrCreate(['name' => $label['name']], $label);
        }
    }
}
