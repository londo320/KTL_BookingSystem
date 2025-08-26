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
            
            // Note: Depots, TippingLocations, TippingBays, Products, and test data
            // are NOT included for production - create these manually as needed
        ]);
    }
}
