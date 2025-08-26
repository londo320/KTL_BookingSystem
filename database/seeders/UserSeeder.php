<?php

namespace Database\Seeders;

use App\Models\Depot;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Check if admin user already exists
        if (User::where('email', 'admin@example.com')->exists()) {
            $this->command->info('Admin user already exists, skipping user creation.');
            return;
        }

        // Ensure at least one depot exists with map file
        $depot = Depot::first() ?? Depot::create([
            'name' => 'Main Depot',
            'location' => 'Default Location',
            'map_file' => 'Wimblington.svg',
        ]);
        
        // Set map file if depot exists but doesn't have one
        if (!$depot->map_file) {
            $depot->update(['map_file' => 'Wimblington.svg']);
        }

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'depot_id' => $depot->id,
        ]);
        $admin->assignRole('admin');

        $depotAdmin = User::create([
            'name' => 'Depot Admin',
            'email' => 'depotadmin@example.com',
            'password' => Hash::make('password'),
            'depot_id' => $depot->id,
        ]);
        $depotAdmin->assignRole('depot-admin');

        $siteAdmin = User::create([
            'name' => 'Site Admin',
            'email' => 'siteadmin@example.com',
            'password' => Hash::make('password'),
            'depot_id' => $depot->id,
        ]);
        $siteAdmin->assignRole('site-admin');

        $customer = User::create([
            'name' => 'Customer One',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'depot_id' => $depot->id,
        ]);
        $customer->assignRole('customer');
    }
}
