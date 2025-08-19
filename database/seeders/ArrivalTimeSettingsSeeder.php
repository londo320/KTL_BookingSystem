<?php

namespace Database\Seeders;

use App\Models\ArrivalTimeSetting;
use Illuminate\Database\Seeder;

class ArrivalTimeSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // Create default global settings (0 minutes tolerance - exact timing)
        ArrivalTimeSetting::firstOrCreate(
            [
                'level' => 'global',
                'depot_id' => null,
                'customer_id' => null,
            ],
            [
                'early_threshold_minutes' => 0,
                'late_threshold_minutes' => 0,
                'description' => 'Default global arrival time rules: exact time only (no tolerance)',
                'is_active' => true,
            ]
        );

        $this->command->info('Default arrival time settings seeded successfully.');
    }
}