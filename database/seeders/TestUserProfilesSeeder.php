<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Depot;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserProfilesSeeder extends Seeder
{
    /**
     * Seed comprehensive test user profiles for different roles
     */
    public function run(): void
    {
        $this->command->info('Creating test user profiles...');

        // Get or create depots
        $depot1 = Depot::firstOrCreate(['name' => 'Main Depot'], [
            'location' => 'Wimblington',
            'map_file' => 'Wimblington.svg',
        ]);

        $depot2 = Depot::firstOrCreate(['name' => 'North Depot'], [
            'location' => 'Leeds',
            'map_file' => null,
        ]);

        // Get or create test customers
        $customerABC = Customer::firstOrCreate(['name' => 'ABC Logistics Ltd'], [
            'email' => 'bookings@abclogistics.com',
            'phone' => '01234 567890',
            'address' => '123 Business Park, Industrial Estate',
        ]);

        $customerXYZ = Customer::firstOrCreate(['name' => 'XYZ Transport'], [
            'email' => 'office@xyztransport.com',
            'phone' => '01234 567891',
            'address' => '456 Freight Lane, Distribution Centre',
        ]);

        // Password for all test users
        $password = Hash::make('password');

        // =====================================
        // ADMIN ROLES
        // =====================================

        $this->createUser([
            'name' => 'System Administrator',
            'email' => 'admin@ktl.com',
            'password' => $password,
            'role' => 'admin',
            'depot_id' => $depot1->id,
        ], '✅ Super Admin - Full system access');

        $this->createUser([
            'name' => 'Sarah Johnson',
            'email' => 'depot.admin@ktl.com',
            'password' => $password,
            'role' => 'depot-admin',
            'depot_id' => $depot1->id,
        ], '🏢 Depot Admin - Manages Main Depot');

        $this->createUser([
            'name' => 'Mike Williams',
            'email' => 'site.admin@ktl.com',
            'password' => $password,
            'role' => 'site-admin',
            'depot_id' => $depot2->id,
        ], '🔧 Site Admin - Manages North Depot');

        // =====================================
        // WAREHOUSE ROLES
        // =====================================

        $this->createUser([
            'name' => 'David Thompson',
            'email' => 'warehouse.manager@ktl.com',
            'password' => $password,
            'role' => 'warehouse-manager',
            'depot_id' => $depot1->id,
        ], '👔 Warehouse Manager - Oversees all warehouse operations');

        $this->createUser([
            'name' => 'Emma Davis',
            'email' => 'warehouse.op1@ktl.com',
            'password' => $password,
            'role' => 'warehouse-operative',
            'depot_id' => $depot1->id,
        ], '📦 Warehouse Operative #1 - Day shift');

        $this->createUser([
            'name' => 'James Wilson',
            'email' => 'warehouse.op2@ktl.com',
            'password' => $password,
            'role' => 'warehouse-operative',
            'depot_id' => $depot1->id,
        ], '📦 Warehouse Operative #2 - Night shift');

        // =====================================
        // FORKLIFT DRIVERS
        // =====================================

        $this->createUser([
            'name' => 'Tom Brown',
            'email' => 'forklift1@ktl.com',
            'password' => $password,
            'role' => 'forklift-driver',
            'depot_id' => $depot1->id,
        ], '🚜 Forklift Driver #1 - Main Depot');

        $this->createUser([
            'name' => 'Lucy Martinez',
            'email' => 'forklift2@ktl.com',
            'password' => $password,
            'role' => 'forklift-driver',
            'depot_id' => $depot1->id,
        ], '🚜 Forklift Driver #2 - Main Depot');

        $this->createUser([
            'name' => 'Chris Anderson',
            'email' => 'forklift3@ktl.com',
            'password' => $password,
            'role' => 'forklift-driver',
            'depot_id' => $depot2->id,
        ], '🚜 Forklift Driver - North Depot');

        // =====================================
        // YARD CONTROLLER
        // =====================================

        $this->createUser([
            'name' => 'Kevin Moore',
            'email' => 'yard.controller@ktl.com',
            'password' => $password,
            'role' => 'yard-controller',
            'depot_id' => $depot1->id,
        ], '🚛 Yard Controller - Manages vehicle movements');

        // =====================================
        // GATE SECURITY
        // =====================================

        $this->createUser([
            'name' => 'Robert Taylor',
            'email' => 'security1@ktl.com',
            'password' => $password,
            'role' => 'gate-security',
            'depot_id' => $depot1->id,
        ], '🛡️ Security Officer #1 - Main Gate');

        $this->createUser([
            'name' => 'Michelle Garcia',
            'email' => 'security2@ktl.com',
            'password' => $password,
            'role' => 'gate-security',
            'depot_id' => $depot2->id,
        ], '🛡️ Security Officer #2 - North Gate');

        // =====================================
        // CUSTOMER ROLES
        // =====================================

        $customerUser1 = $this->createUser([
            'name' => 'John Smith',
            'email' => 'john.smith@abclogistics.com',
            'password' => $password,
            'role' => 'customer-admin',
            'depot_id' => $depot1->id,
            'customer_id' => $customerABC->id,
        ], '🏢 Customer Admin - ABC Logistics');

        $customerUser2 = $this->createUser([
            'name' => 'Jane Cooper',
            'email' => 'jane.cooper@abclogistics.com',
            'password' => $password,
            'role' => 'customer',
            'depot_id' => $depot1->id,
            'customer_id' => $customerABC->id,
        ], '👤 Customer User - ABC Logistics');

        $customerUser3 = $this->createUser([
            'name' => 'Peter Walsh',
            'email' => 'peter@xyztransport.com',
            'password' => $password,
            'role' => 'customer-admin',
            'depot_id' => $depot1->id,
            'customer_id' => $customerXYZ->id,
        ], '🏢 Customer Admin - XYZ Transport');

        $customerUser4 = $this->createUser([
            'name' => 'Lisa White',
            'email' => 'lisa@xyztransport.com',
            'password' => $password,
            'role' => 'customer',
            'depot_id' => $depot1->id,
            'customer_id' => $customerXYZ->id,
        ], '👤 Customer User - XYZ Transport');

        // =====================================
        // VIEWER ROLE (Read-only)
        // =====================================

        $this->createUser([
            'name' => 'Manager Review',
            'email' => 'viewer@ktl.com',
            'password' => $password,
            'role' => 'viewer',
            'depot_id' => $depot1->id,
        ], '👁️ Viewer - Read-only access for management');

        $this->command->info("\n✅ Test user profiles created successfully!");
        $this->command->info("Default password for all users: password");
        $this->command->info("\nTo login, use email + 'password'");
    }

    /**
     * Create a user and assign role
     */
    private function createUser(array $data, string $description): User
    {
        $role = $data['role'];
        unset($data['role']);

        $user = User::firstOrCreate(
            ['email' => $data['email']],
            array_merge($data, ['email_verified_at' => now(), 'is_active' => true])
        );

        if (!$user->hasRole($role)) {
            $user->assignRole($role);
        }

        $this->command->info("  {$description} ({$data['email']})");

        return $user;
    }
}
