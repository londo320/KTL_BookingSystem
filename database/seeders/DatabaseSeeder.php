<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Core system data only for production
            RoleSeeder::class,
            UserSeeder::class,
            BookingTypeSeeder::class,
            
            // Essential configuration data
            TrailerTypeSeeder::class,
            PalletTypeSeeder::class,
            
            // Basic depot setup for functional depot map
            BasicDepotSetupSeeder::class,
            
            // Note: Additional depots, locations, bays, products, and test data
            // are NOT included for production - create these manually as needed
        ]);
    }
}
