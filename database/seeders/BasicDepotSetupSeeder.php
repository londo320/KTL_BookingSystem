<?php

namespace Database\Seeders;

use App\Models\Depot;
use App\Models\TippingLocation;
use App\Models\TippingBay;
use Illuminate\Database\Seeder;

class BasicDepotSetupSeeder extends Seeder
{
    public function run(): void
    {
        $depot = Depot::first();
        
        if (!$depot) {
            $this->command->error('No depot found. Please ensure UserSeeder runs first.');
            return;
        }

        $this->command->info("Setting up basic depot configuration for: {$depot->name}");

        // Create essential tipping locations (all parking areas now)
        $locations = [
            [
                'name' => 'Warehouse A - General (Parking Area)',
                'code' => 'WH-A',
                'description' => 'Main warehouse for general cargo parking',
                'location_type' => 'parking',
                'capacity' => 20,
                'is_active' => true,
                'map_x' => 25.0,
                'map_y' => 30.0,
                'show_on_map' => true,
                'map_width' => 120,
                'map_height' => 80,
            ],
            [
                'name' => 'Loading Dock (Parking Area)',
                'code' => 'DOCK-1',
                'description' => 'Primary loading dock parking area',
                'location_type' => 'parking',
                'capacity' => 15,
                'is_active' => true,
                'map_x' => 65.0,
                'map_y' => 45.0,
                'show_on_map' => true,
                'map_width' => 100,
                'map_height' => 70,
            ],
            [
                'name' => 'Collection Zone (Parking Area)',
                'code' => 'COLLECT',
                'description' => 'Area for trailer collection parking',
                'location_type' => 'parking',
                'capacity' => 10,
                'is_active' => true,
                'map_x' => 75.0,
                'map_y' => 20.0,
                'show_on_map' => true,
                'map_width' => 90,
                'map_height' => 60,
            ],
        ];

        foreach ($locations as $locationData) {
            TippingLocation::firstOrCreate(
                ['code' => $locationData['code'], 'depot_id' => $depot->id],
                array_merge($locationData, ['depot_id' => $depot->id])
            );
        }

        // Create essential tipping bays
        $bays = [
            [
                'name' => 'Bay 1A',
                'code' => 'B1A',
                'description' => 'Primary tipping bay - general use',
                'is_active' => true,
                'map_x' => 15.0,
                'map_y' => 40.0,
                'show_on_map' => true,
                'map_width' => 60,
                'map_height' => 40,
                'equipment' => ['forklift', 'loading_dock'],
            ],
            [
                'name' => 'Bay 1B',
                'code' => 'B1B',
                'description' => 'Secondary tipping bay - general use',
                'is_active' => true,
                'map_x' => 15.0,
                'map_y' => 55.0,
                'show_on_map' => true,
                'map_width' => 60,
                'map_height' => 40,
                'equipment' => ['forklift', 'loading_dock'],
            ],
            [
                'name' => 'Bay 2A',
                'code' => 'B2A',
                'description' => 'Express bay for priority loads',
                'is_active' => true,
                'map_x' => 35.0,
                'map_y' => 60.0,
                'show_on_map' => true,
                'map_width' => 70,
                'map_height' => 35,
                'equipment' => ['forklift', 'loading_dock', 'crane'],
            ],
            [
                'name' => 'Bay 3',
                'code' => 'B3',
                'description' => 'Bulk materials bay',
                'is_active' => true,
                'map_x' => 55.0,
                'map_y' => 65.0,
                'show_on_map' => true,
                'map_width' => 80,
                'map_height' => 45,
                'equipment' => ['conveyor', 'bulk_loader'],
            ],
        ];

        foreach ($bays as $bayData) {
            TippingBay::firstOrCreate(
                ['code' => $bayData['code'], 'depot_id' => $depot->id],
                array_merge($bayData, ['depot_id' => $depot->id])
            );
        }

        $this->command->info('Created ' . count($locations) . ' tipping locations and ' . count($bays) . ' tipping bays');
        $this->command->info('Depot map is now ready with positioned items');
    }
}