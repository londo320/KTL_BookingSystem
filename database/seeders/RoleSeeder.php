<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // System Roles
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'depot-admin']);
        Role::firstOrCreate(['name' => 'site-admin']);

        // Warehouse Roles
        Role::firstOrCreate(['name' => 'warehouse-manager']);
        Role::firstOrCreate(['name' => 'warehouse-operative']);
        Role::firstOrCreate(['name' => 'forklift-driver']);
        Role::firstOrCreate(['name' => 'yard-controller']);
        Role::firstOrCreate(['name' => 'gate-security']);

        // Customer Roles
        Role::firstOrCreate(['name' => 'customer']);
        Role::firstOrCreate(['name' => 'customer-admin']);

        // View Only Roles
        Role::firstOrCreate(['name' => 'viewer']);
    }
}
