<?php

namespace Database\Seeders;

use App\Models\TippingLocation;
use App\Models\TippingBay;
use App\Models\SlotTemplate;
use App\Models\Slot;
use App\Models\Customer;
use App\Models\User;
use App\Models\Depot;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $depot = Depot::first();
        
        if (!$depot) {
            $this->command->error('No depot found. Please ensure depots are seeded first.');
            return;
        }

        $this->command->info("Setting up demo data for depot: {$depot->name}");

        // Create tipping locations
        $this->createTippingLocations($depot);
        
        // Create tipping bays
        $this->createTippingBays($depot);
        
        // Create slot templates  
        $this->createSlotTemplates($depot);
        
        // Generate slots for next 4 weeks
        $this->generateSlots($depot);
        
        // Create sample customers
        $this->createSampleCustomers($depot);

        $this->command->info('Demo data seeded successfully!');
    }

    private function createTippingLocations($depot)
    {
        $locations = [
            ['name' => 'Warehouse A - General', 'code' => 'WHA', 'description' => 'General cargo warehouse', 'capacity' => 50],
            ['name' => 'Warehouse B - Bulk', 'code' => 'WHB', 'description' => 'Bulk materials warehouse', 'capacity' => 30],
            ['name' => 'Cold Storage', 'code' => 'COLD', 'description' => 'Temperature controlled storage', 'capacity' => 20],
            ['name' => 'Outdoor Yard', 'code' => 'YARD', 'description' => 'Open yard for construction materials', 'capacity' => 100],
            ['name' => 'Hazmat Storage', 'code' => 'HAZ', 'description' => 'Hazardous materials storage', 'capacity' => 10],
        ];

        foreach ($locations as $location) {
            TippingLocation::firstOrCreate(
                ['code' => $location['code'], 'depot_id' => $depot->id],
                array_merge($location, ['depot_id' => $depot->id, 'is_active' => true])
            );
        }

        $this->command->info("Created " . count($locations) . " tipping locations");
    }

    private function createTippingBays($depot)
    {
        $locations = TippingLocation::where('depot_id', $depot->id)->get();
        $bayCount = 0;

        foreach ($locations as $location) {
            $bays = match($location->code) {
                'WHA' => 6,   // Warehouse A - 6 bays
                'WHB' => 4,   // Warehouse B - 4 bays  
                'COLD' => 3,  // Cold Storage - 3 bays
                'YARD' => 8,  // Outdoor Yard - 8 positions
                'HAZ' => 2,   // Hazmat - 2 bays
                default => 2
            };

            for ($i = 1; $i <= $bays; $i++) {
                $bayCode = $location->code . $i;
                
                TippingBay::firstOrCreate(
                    ['code' => $bayCode, 'depot_id' => $depot->id],
                    [
                        'name' => "Bay {$bayCode}",
                        'description' => "Loading bay {$i} at {$location->name}",
                        'is_active' => true,
                        'is_occupied' => false,
                        'equipment' => ['forklift', 'loading_dock'],
                    ]
                );
                $bayCount++;
            }
        }

        $this->command->info("Created {$bayCount} tipping bays");
    }

    private function createSlotTemplates($depot)
    {
        $templates = [];
        $weekdays = [1, 2, 3, 4, 5]; // Monday to Friday

        foreach ($weekdays as $dayOfWeek) {
            // 3-hour slots throughout the day
            $timeSlots = [
                ['06:00:00', '09:00:00'],   // Morning A
                ['09:00:00', '12:00:00'],  // Morning B
                ['12:00:00', '15:00:00'],  // Afternoon A
                ['15:00:00', '18:00:00'],  // Afternoon B
                ['18:00:00', '21:00:00'],  // Evening
            ];

            foreach ($timeSlots as $slot) {
                $templates[] = [
                    'depot_id' => $depot->id,
                    'day_of_week' => $dayOfWeek,
                    'start_time' => $slot[0],
                    'end_time' => $slot[1],
                    'duration_minutes' => 180, // 3 hours
                ];
            }
        }

        // Saturday (reduced hours)
        $templates[] = [
            'depot_id' => $depot->id,
            'day_of_week' => 6,
            'start_time' => '08:00:00',
            'end_time' => '11:00:00',
            'duration_minutes' => 180,
        ];

        $templates[] = [
            'depot_id' => $depot->id,
            'day_of_week' => 6,
            'start_time' => '11:00:00',
            'end_time' => '14:00:00',
            'duration_minutes' => 180,
        ];

        foreach ($templates as $template) {
            SlotTemplate::firstOrCreate([
                'depot_id' => $template['depot_id'],
                'day_of_week' => $template['day_of_week'],
                'start_time' => $template['start_time'],
                'end_time' => $template['end_time']
            ], $template);
        }

        $this->command->info("Created " . count($templates) . " slot templates");
    }

    private function generateSlots($depot)
    {
        $templates = SlotTemplate::where('depot_id', $depot->id)->get();
        $startDate = Carbon::now()->startOfWeek();
        $endDate = $startDate->copy()->addWeeks(4);
        $slotsCreated = 0;

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dayOfWeek = $date->dayOfWeek === 0 ? 7 : $date->dayOfWeek;
            $dayTemplates = $templates->where('day_of_week', $dayOfWeek);

            foreach ($dayTemplates as $template) {
                $startTime = $date->copy()->setTimeFromTimeString($template->start_time);
                $endTime = $date->copy()->setTimeFromTimeString($template->end_time);

                if ($startTime->isPast()) continue;

                $existingSlot = Slot::where('depot_id', $depot->id)
                    ->where('start_at', $startTime)
                    ->where('end_at', $endTime)
                    ->first();

                if (!$existingSlot) {
                    Slot::create([
                        'depot_id' => $depot->id,
                        'start_at' => $startTime,
                        'end_at' => $endTime,
                        'capacity' => 10, // Default capacity
                        'is_blocked' => false,
                    ]);
                    $slotsCreated++;
                }
            }
        }

        $this->command->info("Generated {$slotsCreated} slots for next 4 weeks");
    }

    private function createSampleCustomers($depot)
    {
        $customers = [
            [
                'name' => 'BuildCorp Construction Ltd',
                'user' => [
                    'name' => 'John Builder',
                    'email' => 'john@buildcorp.com',
                    'password' => 'password',
                ]
            ],
            [
                'name' => 'FreshFood Distributors',
                'user' => [
                    'name' => 'Emma Fresh',
                    'email' => 'emma@freshfood.co.uk',
                    'password' => 'password',
                ]
            ],
            [
                'name' => 'AutoParts Express Ltd',
                'user' => [
                    'name' => 'Tom Auto',
                    'email' => 'tom@autoparts.com',
                    'password' => 'password',
                ]
            ],
        ];

        foreach ($customers as $customerData) {
            $userData = $customerData['user'];
            unset($customerData['user']);

            $customer = Customer::firstOrCreate(
                ['name' => $customerData['name']],
                $customerData
            );

            $existingUser = User::where('email', $userData['email'])->first();
            
            if (!$existingUser) {
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password']),
                    'depot_id' => $depot->id,
                ]);

                $user->assignRole('customer');
                $customer->users()->syncWithoutDetaching([$user->id]);
            }
        }

        $this->command->info("Created " . count($customers) . " sample customers");
    }
}