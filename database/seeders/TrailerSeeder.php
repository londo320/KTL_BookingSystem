<?php

namespace Database\Seeders;

use App\Models\Trailer;
use Illuminate\Database\Seeder;

class TrailerSeeder extends Seeder
{
    public function run(): void
    {
        $trailers = [
            [
                'trailer_number' => 'TRL001',
                'trailer_type' => 'container',
                'size' => '40ft',
                'capacity_pallets' => 26,
                'capacity_weight_kg' => 30000,
                'temperature_controlled' => false,
                'owner' => 'Swift Transport Ltd',
            ],
            [
                'trailer_number' => 'TRL002',
                'trailer_type' => 'curtain_sider',
                'size' => '45ft',
                'capacity_pallets' => 30,
                'capacity_weight_kg' => 32000,
                'temperature_controlled' => false,
                'owner' => 'Express Logistics',
            ],
            [
                'trailer_number' => 'CTN003',
                'trailer_type' => 'container',
                'size' => '20ft',
                'capacity_pallets' => 12,
                'capacity_weight_kg' => 20000,
                'temperature_controlled' => true,
                'owner' => 'Cold Chain Ltd',
            ],
            [
                'trailer_number' => 'TRL004',
                'trailer_type' => 'box',
                'size' => '40ft',
                'capacity_pallets' => 24,
                'capacity_weight_kg' => 28000,
                'temperature_controlled' => false,
                'owner' => 'Local Delivery Co',
            ],
            [
                'trailer_number' => 'FLT005',
                'trailer_type' => 'flatbed',
                'size' => '45ft',
                'capacity_pallets' => 0,
                'capacity_weight_kg' => 35000,
                'temperature_controlled' => false,
                'owner' => 'Heavy Haulage Ltd',
            ],
        ];

        foreach ($trailers as $trailer) {
            Trailer::create($trailer);
        }
    }
}
