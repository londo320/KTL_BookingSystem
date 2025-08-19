<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrailerTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $trailerTypes = [
            ['name' => '20ft Container', 'description' => '20-foot shipping container'],
            ['name' => '40ft Container', 'description' => '40-foot shipping container'],
            ['name' => '45ft Container', 'description' => '45-foot shipping container'],
            ['name' => '53ft Trailer', 'description' => '53-foot dry van trailer'],
            ['name' => 'Box Trailer', 'description' => 'Standard box trailer'],
            ['name' => 'Curtain Side', 'description' => 'Curtain side trailer'],
            ['name' => 'Flatbed', 'description' => 'Flatbed trailer'],
            ['name' => 'Articulated', 'description' => 'Articulated lorry'],
            ['name' => 'Rigid', 'description' => 'Rigid truck'],
        ];

        foreach ($trailerTypes as $type) {
            \App\Models\TrailerType::firstOrCreate(
                ['name' => $type['name']],
                [
                    'description' => $type['description'],
                    'is_active' => true
                ]
            );
        }
    }
}
