<?php

namespace Database\Seeders;

use App\Models\TippingLocation;
use App\Models\Depot;
use Illuminate\Database\Seeder;

class TippingLocationSeeder extends Seeder
{
    public function run(): void
    {
        // PRODUCTION: Skip automatic tipping location creation
        // Create these manually through the admin interface as needed
        $this->command->info('Tipping location seeder skipped for production. Create locations manually through admin interface.');
        return;

        $mainDepot = Depot::first(); // Use first available depot
        
        if (!$mainDepot) {
            $this->command->error('Main Depot not found. Please run DepotSeeder first.');
            return;
        }

        $tippingLocations = [
            [
                'name' => 'Warehouse A - General',
                'description' => 'Main warehouse for general cargo and palletised goods',
                'location_code' => 'WHA',
                'depot_id' => $mainDepot->id,
                'is_active' => true,
                'capacity' => 50,
                'location_type' => 'warehouse',
                'special_requirements' => null,
            ],
            [
                'name' => 'Warehouse B - Bulk',
                'description' => 'Dedicated warehouse for bulk materials and aggregates',
                'location_code' => 'WHB',
                'depot_id' => $mainDepot->id,
                'is_active' => true,
                'capacity' => 30,
                'location_type' => 'warehouse',
                'special_requirements' => 'bulk_materials',
            ],
            [
                'name' => 'Cold Storage',
                'description' => 'Temperature controlled storage for perishable goods',
                'location_code' => 'COLD',
                'depot_id' => $mainDepot->id,
                'is_active' => true,
                'capacity' => 20,
                'location_type' => 'cold_storage',
                'special_requirements' => 'temperature_controlled',
            ],
            [
                'name' => 'Outdoor Yard - Construction',
                'description' => 'Open yard area for construction materials and machinery',
                'location_code' => 'YARD',
                'depot_id' => $mainDepot->id,
                'is_active' => true,
                'capacity' => 100,
                'location_type' => 'yard',
                'special_requirements' => 'heavy_machinery',
            ],
            [
                'name' => 'Hazmat Storage',
                'description' => 'Secure storage for hazardous materials',
                'location_code' => 'HAZ',
                'depot_id' => $mainDepot->id,
                'is_active' => true,
                'capacity' => 10,
                'location_type' => 'hazmat',
                'special_requirements' => 'hazmat_certified',
            ],
        ];

        foreach ($tippingLocations as $location) {
            TippingLocation::firstOrCreate(
                ['location_code' => $location['location_code'], 'depot_id' => $location['depot_id']],
                $location
            );
        }

        $this->command->info('Tipping locations seeded successfully.');
    }
}