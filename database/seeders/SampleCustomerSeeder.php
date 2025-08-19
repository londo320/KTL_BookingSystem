<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use App\Models\Depot;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleCustomerSeeder extends Seeder
{
    public function run(): void
    {
        $mainDepot = Depot::first(); // Use first available depot
        
        if (!$mainDepot) {
            $this->command->error('Main Depot not found. Please run DepotSeeder first.');
            return;
        }

        $customers = [
            [
                'name' => 'BuildCorp Construction Ltd',
                'company_registration' => 'BC123456',
                'contact_email' => 'orders@buildcorp.com',
                'contact_phone' => '+44 1234 567890',
                'address' => '123 Construction Way, BuildCity, BC1 2CD',
                'billing_address' => '123 Construction Way, BuildCity, BC1 2CD',
                'credit_limit' => 50000.00,
                'payment_terms' => 30,
                'is_active' => true,
                'risk_level' => 'low',
                'notes' => 'Large construction company, reliable payment history',
                'users' => [
                    [
                        'name' => 'John Builder',
                        'email' => 'john@buildcorp.com',
                        'password' => 'password',
                        'phone' => '+44 1234 567891',
                        'job_title' => 'Logistics Manager',
                    ],
                    [
                        'name' => 'Sarah Construction',
                        'email' => 'sarah@buildcorp.com', 
                        'password' => 'password',
                        'phone' => '+44 1234 567892',
                        'job_title' => 'Site Supervisor',
                    ],
                ]
            ],
            [
                'name' => 'QuickMart Retail Chain',
                'company_registration' => 'QM789012',
                'contact_email' => 'logistics@quickmart.co.uk',
                'contact_phone' => '+44 2345 678901',
                'address' => '456 Retail Park, ShopTown, ST3 4EF',
                'billing_address' => '456 Retail Park, ShopTown, ST3 4EF',
                'credit_limit' => 25000.00,
                'payment_terms' => 14,
                'is_active' => true,
                'risk_level' => 'medium',
                'notes' => 'Fast-growing retail chain, sometimes tight on payment terms',
                'users' => [
                    [
                        'name' => 'Mike Logistics',
                        'email' => 'mike@quickmart.co.uk',
                        'password' => 'password',
                        'phone' => '+44 2345 678902',
                        'job_title' => 'Distribution Manager',
                    ],
                ]
            ],
            [
                'name' => 'FreshFood Distributors',
                'company_registration' => 'FF345678',
                'contact_email' => 'bookings@freshfood.co.uk',
                'contact_phone' => '+44 3456 789012',
                'address' => '789 Food Central, FreshTown, FT5 6GH',
                'billing_address' => '789 Food Central, FreshTown, FT5 6GH',
                'credit_limit' => 35000.00,
                'payment_terms' => 7,
                'is_active' => true,
                'risk_level' => 'low',
                'notes' => 'Temperature-sensitive goods, requires cold storage',
                'users' => [
                    [
                        'name' => 'Emma Fresh',
                        'email' => 'emma@freshfood.co.uk',
                        'password' => 'password',
                        'phone' => '+44 3456 789013',
                        'job_title' => 'Cold Chain Manager',
                    ],
                    [
                        'name' => 'David Delivery',
                        'email' => 'david@freshfood.co.uk',
                        'password' => 'password',
                        'phone' => '+44 3456 789014',
                        'job_title' => 'Transport Coordinator',
                    ],
                ]
            ],
            [
                'name' => 'AutoParts Express Ltd',
                'company_registration' => 'AP901234',
                'contact_email' => 'dispatch@autoparts.com',
                'contact_phone' => '+44 4567 890123',
                'address' => '321 Motor Way, CarCity, CC7 8IJ',
                'billing_address' => '321 Motor Way, CarCity, CC7 8IJ',
                'credit_limit' => 20000.00,
                'payment_terms' => 30,
                'is_active' => true,
                'risk_level' => 'low',
                'notes' => 'Automotive parts supplier, regular weekly deliveries',
                'users' => [
                    [
                        'name' => 'Tom Auto',
                        'email' => 'tom@autoparts.com',
                        'password' => 'password',
                        'phone' => '+44 4567 890124',
                        'job_title' => 'Parts Manager',
                    ],
                ]
            ],
            [
                'name' => 'ChemSafe Solutions',
                'company_registration' => 'CS567890',
                'contact_email' => 'safety@chemsafe.co.uk',
                'contact_phone' => '+44 5678 901234',
                'address' => '654 Chemical Drive, SafeTown, ST9 0KL',
                'billing_address' => '654 Chemical Drive, SafeTown, ST9 0KL',
                'credit_limit' => 15000.00,
                'payment_terms' => 30,
                'is_active' => true,
                'risk_level' => 'high',
                'notes' => 'Hazardous materials - requires certified drivers and special handling',
                'users' => [
                    [
                        'name' => 'Lisa Safety',
                        'email' => 'lisa@chemsafe.co.uk',
                        'password' => 'password',
                        'phone' => '+44 5678 901235',
                        'job_title' => 'Safety Coordinator',
                    ],
                ]
            ],
            [
                'name' => 'Fashion Forward Ltd',
                'company_registration' => 'FF123789',
                'contact_email' => 'warehouse@fashionforward.com',
                'contact_phone' => '+44 6789 012345',
                'address' => '987 Fashion Street, StyleCity, SC1 2MN',
                'billing_address' => '987 Fashion Street, StyleCity, SC1 2MN',
                'credit_limit' => 30000.00,
                'payment_terms' => 21,
                'is_active' => true,
                'risk_level' => 'medium',
                'notes' => 'Seasonal business, high volume during fashion seasons',
                'users' => [
                    [
                        'name' => 'Sophie Style',
                        'email' => 'sophie@fashionforward.com',
                        'password' => 'password',
                        'phone' => '+44 6789 012346',
                        'job_title' => 'Warehouse Manager',
                    ],
                    [
                        'name' => 'Alex Fashion',
                        'email' => 'alex@fashionforward.com',
                        'password' => 'password',
                        'phone' => '+44 6789 012347',
                        'job_title' => 'Inventory Coordinator',
                    ],
                ]
            ],
        ];

        foreach ($customers as $customerData) {
            $userData = $customerData['users'];
            unset($customerData['users']);

            // Create customer
            $customer = Customer::firstOrCreate(
                ['company_registration' => $customerData['company_registration']],
                $customerData
            );

            // Create users for this customer
            foreach ($userData as $user) {
                $existingUser = User::where('email', $user['email'])->first();
                
                if (!$existingUser) {
                    $newUser = User::create([
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'password' => Hash::make($user['password']),
                        'phone' => $user['phone'] ?? null,
                        'job_title' => $user['job_title'] ?? null,
                        'depot_id' => $mainDepot->id,
                        'customer_id' => $customer->id,
                    ]);

                    // Assign customer role
                    $newUser->assignRole('customer');

                    // Link user to customer via pivot table
                    $customer->users()->syncWithoutDetaching([$newUser->id]);
                }
            }

            // Link customer to depot
            $customer->depots()->syncWithoutDetaching([$mainDepot->id]);
        }

        $this->command->info('Sample customers and users seeded successfully (' . count($customers) . ' customers created).');
    }
}