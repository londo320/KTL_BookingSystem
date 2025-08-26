<?php

namespace Database\Seeders;

use App\Models\Depot;
use Illuminate\Database\Seeder;

class DepotSeeder extends Seeder
{
    public function run(): void
    {
        // PRODUCTION: Skip automatic depot creation
        // Create your specific depots manually through the admin interface
        $this->command->info('Depot seeder skipped for production. Create your specific depots manually through admin interface.');
        return;
        
        // Previous demo depots commented out:
        // Depot::firstOrCreate(['name' => 'Wimblington'], ['location' => 'March']);
        // Depot::firstOrCreate(['name' => 'Cromwell Road'], ['location' => 'Wisbech']);
        // Depot::firstOrCreate(['name' => 'Salters Yard'], ['location' => 'Wisbech']);
        // Depot::firstOrCreate(['name' => 'Lynn Road'], ['location' => 'Wisbech']);
    }
}
