<?php

namespace Database\Seeders;

use App\Models\PalletType;
use Illuminate\Database\Seeder;

class PalletTypeSeeder extends Seeder
{
    public function run(): void
    {
        $palletTypes = [
            [
                'name' => 'Euro Pallet (EPAL)',
                'code' => 'EUR',
                'description' => 'Standard European pallet 1200x800x144mm',
                'is_active' => true,
            ],
            [
                'name' => 'UK Standard Pallet',
                'code' => 'UK',
                'description' => 'UK standard pallet 1200x1000x144mm',
                'is_active' => true,
            ],
            [
                'name' => 'Half Pallet',
                'code' => 'HALF',
                'description' => 'Half size pallet 600x800x144mm',
                'is_active' => true,
            ],
            [
                'name' => 'Quarter Pallet',
                'code' => 'QTR',
                'description' => 'Quarter size pallet 600x400x144mm',
                'is_active' => true,
            ],
            [
                'name' => 'Plastic Pallet - Standard',
                'code' => 'PLAS-STD',
                'description' => 'Plastic pallet 1200x800x150mm - hygienic',
                'is_active' => true,
            ],
            [
                'name' => 'Plastic Pallet - Heavy Duty',
                'code' => 'PLAS-HD',
                'description' => 'Heavy duty plastic pallet 1200x1000x160mm',
                'is_active' => true,
            ],
            [
                'name' => 'Metal Pallet',
                'code' => 'METAL',
                'description' => 'Steel pallet 1200x800x150mm - industrial use',
                'is_active' => true,
            ],
            [
                'name' => 'Display Pallet',
                'code' => 'DISP',
                'description' => 'Display pallet 800x600x144mm - retail',
                'is_active' => true,
            ],
            [
                'name' => 'Export Pallet - Heat Treated',
                'code' => 'EXP-HT',
                'description' => 'Heat treated export pallet 1200x800x144mm (ISPM 15)',
                'is_active' => true,
            ],
            [
                'name' => 'Custom Size Pallet',
                'code' => 'CUSTOM',
                'description' => 'Custom manufactured pallet - various sizes',
                'is_active' => true,
            ],
        ];

        foreach ($palletTypes as $palletType) {
            PalletType::firstOrCreate(
                ['code' => $palletType['code']],
                $palletType
            );
        }

        $this->command->info('Pallet types seeded successfully (' . count($palletTypes) . ' types created).');
    }
}