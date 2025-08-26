<?php

namespace Database\Seeders;

use App\Models\TippingBay;
use App\Models\TippingLocation;
use App\Models\Depot;
use Illuminate\Database\Seeder;

class TippingBaySeeder extends Seeder
{
    public function run(): void
    {
        // PRODUCTION: Skip automatic tipping bay creation
        // Create these manually through the admin interface as needed
        $this->command->info('Tipping bay seeder skipped for production. Create bays manually through admin interface.');
        return;

        $mainDepot = Depot::first(); // Use first available depot
        
        if (!$mainDepot) {
            $this->command->error('Main Depot not found. Please run DepotSeeder first.');
            return;
        }

        $locations = TippingLocation::where('depot_id', $mainDepot->id)->get();

        if ($locations->isEmpty()) {
            $this->command->error('No tipping locations found. Please run TippingLocationSeeder first.');
            return;
        }

        $bays = [];

        // Warehouse A - General (6 bays)
        $warehouseA = $locations->where('location_code', 'WHA')->first();
        if ($warehouseA) {
            for ($i = 1; $i <= 6; $i++) {
                $bays[] = [
                    'bay_number' => "A{$i}",
                    'name' => "Bay A{$i} - General",
                    'description' => "General purpose loading bay {$i}",
                    'tipping_location_id' => $warehouseA->id,
                    'is_active' => true,
                    'bay_type' => 'dock_door',
                    'equipment_available' => json_encode(['forklift', 'pallet_jack', 'loading_dock']),
                    'restrictions' => null,
                    'max_vehicle_length' => 16.5,
                    'max_vehicle_height' => 4.5,
                ];
            }
        }

        // Warehouse B - Bulk (4 bays)
        $warehouseB = $locations->where('location_code', 'WHB')->first();
        if ($warehouseB) {
            for ($i = 1; $i <= 4; $i++) {
                $bays[] = [
                    'bay_number' => "B{$i}",
                    'name' => "Bay B{$i} - Bulk",
                    'description' => "Bulk materials loading bay {$i}",
                    'tipping_location_id' => $warehouseB->id,
                    'is_active' => true,
                    'bay_type' => 'tipping_bay',
                    'equipment_available' => json_encode(['crane', 'conveyor', 'weighbridge']),
                    'restrictions' => 'bulk_materials_only',
                    'max_vehicle_length' => 20.0,
                    'max_vehicle_height' => 5.0,
                ];
            }
        }

        // Cold Storage (3 bays)
        $coldStorage = $locations->where('location_code', 'COLD')->first();
        if ($coldStorage) {
            for ($i = 1; $i <= 3; $i++) {
                $bays[] = [
                    'bay_number' => "C{$i}",
                    'name' => "Bay C{$i} - Cold",
                    'description' => "Temperature controlled loading bay {$i}",
                    'tipping_location_id' => $coldStorage->id,
                    'is_active' => true,
                    'bay_type' => 'cold_dock',
                    'equipment_available' => json_encode(['forklift', 'temperature_control', 'insulated_dock']),
                    'restrictions' => 'temperature_controlled_vehicles_only',
                    'max_vehicle_length' => 16.5,
                    'max_vehicle_height' => 4.2,
                ];
            }
        }

        // Outdoor Yard (8 positions)
        $yard = $locations->where('location_code', 'YARD')->first();
        if ($yard) {
            for ($i = 1; $i <= 8; $i++) {
                $bays[] = [
                    'bay_number' => "Y{$i}",
                    'name' => "Yard Position Y{$i}",
                    'description' => "Outdoor yard position {$i} for construction materials",
                    'tipping_location_id' => $yard->id,
                    'is_active' => true,
                    'bay_type' => 'yard_position',
                    'equipment_available' => json_encode(['crane', 'telehandler', 'weighbridge']),
                    'restrictions' => 'heavy_machinery_access_required',
                    'max_vehicle_length' => 25.0,
                    'max_vehicle_height' => 6.0,
                ];
            }
        }

        // Hazmat Storage (2 bays)
        $hazmat = $locations->where('location_code', 'HAZ')->first();
        if ($hazmat) {
            for ($i = 1; $i <= 2; $i++) {
                $bays[] = [
                    'bay_number' => "H{$i}",
                    'name' => "Bay H{$i} - Hazmat",
                    'description' => "Hazardous materials handling bay {$i}",
                    'tipping_location_id' => $hazmat->id,
                    'is_active' => true,
                    'bay_type' => 'hazmat_bay',
                    'equipment_available' => json_encode(['specialized_handling', 'safety_equipment', 'containment']),
                    'restrictions' => 'hazmat_certified_drivers_only',
                    'max_vehicle_length' => 16.5,
                    'max_vehicle_height' => 4.5,
                ];
            }
        }

        foreach ($bays as $bay) {
            TippingBay::firstOrCreate(
                ['bay_number' => $bay['bay_number'], 'tipping_location_id' => $bay['tipping_location_id']],
                $bay
            );
        }

        $this->command->info('Tipping bays seeded successfully (' . count($bays) . ' bays created).');
    }
}