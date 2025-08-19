<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Core system data
            RoleSeeder::class,
            DepotSeeder::class,
            UserSeeder::class,
            BookingTypeSeeder::class,
            
            // Essential data
            TrailerTypeSeeder::class,
            ArrivalTimeSettingsSeeder::class,
            ProductSeeder::class,
            PalletTypeSeeder::class,
            
            // Demo data (facilities, slots, customers)
            DemoDataSeeder::class,
            
            // Test booking data with realistic scenarios
            TestBookingDataSeeder::class,
        ]);
    }
}
