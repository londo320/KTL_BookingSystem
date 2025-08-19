<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingPoNumber;
use App\Models\BookingType;
use App\Models\Customer;
use App\Models\Depot;
use App\Models\PalletType;
use App\Models\PoLine;
use App\Models\Slot;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Creating comprehensive test data...');

        // Create additional customers
        $this->createCustomers();

        // Create additional slots for the next 30 days
        $this->createSlots();

        // Create diverse bookings with various scenarios
        $this->createTestBookings();

        $this->command->info('✅ Test data generation complete!');
        $this->printSummary();
    }

    private function createCustomers()
    {
        $this->command->info('Creating additional customers...');

        $customers = [
            ['name' => 'Tesco Distribution'],
            ['name' => 'ASDA Logistics'],
            ['name' => 'Sainsbury\'s Supply Chain'],
            ['name' => 'Morrisons Warehouse'],
            ['name' => 'Amazon Fulfillment'],
            ['name' => 'DHL Supply Chain'],
            ['name' => 'XPO Logistics'],
            ['name' => 'Kuehne+Nagel'],
            ['name' => 'CEVA Logistics'],
            ['name' => 'Geodis Wilson'],
            ['name' => 'Pallet Networks Ltd'],
            ['name' => 'Europa Worldwide'],
            ['name' => 'TPN Distribution'],
            ['name' => 'Palletforce Network'],
        ];

        foreach ($customers as $customerData) {
            Customer::firstOrCreate(['name' => $customerData['name']], $customerData);
        }
    }

    private function createSlots()
    {
        $this->command->info('Creating slots for next 30 days...');

        $depots = Depot::all();
        $startDate = now()->startOfDay();

        foreach ($depots as $depot) {
            for ($day = 0; $day < 30; $day++) {
                $date = $startDate->copy()->addDays($day);

                // Skip weekends for some variety
                if ($date->isWeekend() && rand(1, 3) === 1) {
                    continue;
                }

                // Create 6-8 slots per day
                $slotsPerDay = rand(6, 8);

                for ($slot = 0; $slot < $slotsPerDay; $slot++) {
                    $startHour = 8 + ($slot * 2); // 8am, 10am, 12pm, 2pm, 4pm, 6pm, 8pm, 10pm
                    $startTime = $date->copy()->setHour($startHour)->setMinute(0);
                    $endTime = $startTime->copy()->addHours(2);

                    // Skip if slot already exists
                    if (Slot::where('depot_id', $depot->id)
                        ->where('start_at', $startTime)
                        ->exists()) {
                        continue;
                    }

                    Slot::create([
                        'depot_id' => $depot->id,
                        'start_at' => $startTime,
                        'end_at' => $endTime,
                        'capacity' => rand(3, 8),
                    ]);
                }
            }
        }
    }

    private function createTestBookings()
    {
        $this->command->info('Creating diverse test bookings...');

        $admin = User::where('email', 'admin@example.com')->first();
        $customers = Customer::all();
        $slots = Slot::where('start_at', '>=', now())->take(50)->get();
        $bookingTypes = BookingType::all();
        $palletTypes = PalletType::all();

        $scenarios = [
            'simple_single_line' => 15,
            'multi_line_same_type' => 10,
            'multi_line_mixed_types' => 8,
            'large_volume' => 5,
            'small_volume' => 7,
            'completed_with_variances' => 8,
            'completed_perfect_match' => 5,
        ];

        $bookingCounter = 1;

        foreach ($scenarios as $scenario => $count) {
            $this->command->info("Creating {$count} '{$scenario}' bookings...");

            for ($i = 0; $i < $count; $i++) {
                $customer = $customers->random();
                $slot = $slots->random();
                $bookingType = $bookingTypes->random();

                // Skip if slot is already taken
                if (Booking::where('slot_id', $slot->id)->exists()) {
                    continue;
                }

                $booking = $this->createBookingForScenario(
                    $scenario,
                    $admin,
                    $customer,
                    $slot,
                    $bookingType,
                    $palletTypes,
                    $bookingCounter++
                );

                if ($booking) {
                    // Some bookings should be marked as arrived/completed
                    if (in_array($scenario, ['completed_with_variances', 'completed_perfect_match'])) {
                        $this->completeBooking($booking, $scenario === 'completed_perfect_match');
                    }
                }
            }
        }
    }

    private function createBookingForScenario($scenario, $admin, $customer, $slot, $bookingType, $palletTypes, $counter)
    {
        $carriers = [
            'DPD Group', 'Hermes', 'Royal Mail', 'UPS', 'FedEx',
            'TNT Express', 'GLS', 'Yodel', 'APC', 'Palletforce',
            'Pallet-Track', 'TPN', 'Pallex', 'Scottish Pallet Network',
        ];

        $containerSizes = [15000, 20000, 25000, 30000, 35000, 40000];

        // Create booking
        $booking = Booking::create([
            'slot_id' => $slot->id,
            'booking_type_id' => $bookingType->id,
            'customer_id' => $customer->id,
            'user_id' => $admin->id,
            'carrier_company' => $carriers[array_rand($carriers)],
            'vehicle_registration' => $this->generateVehicleReg(),
            'container_number' => 'CONT'.str_pad($counter, 6, '0', STR_PAD_LEFT),
            'container_size' => $containerSizes[array_rand($containerSizes)],
            'notes' => "Test booking scenario: {$scenario}",
            'special_instructions' => $this->generateSpecialInstructions($scenario),
        ]);

        // Create PO based on scenario
        $po = BookingPoNumber::create([
            'booking_id' => $booking->id,
            'po_number' => $this->generatePoNumber($customer->name, $counter),
        ]);

        // Create PO lines based on scenario
        $this->createPoLinesForScenario($scenario, $po, $palletTypes);

        return $booking;
    }

    private function createPoLinesForScenario($scenario, $po, $palletTypes)
    {
        switch ($scenario) {
            case 'simple_single_line':
                PoLine::create([
                    'booking_po_number_id' => $po->id,
                    'line_number' => 1,
                    'expected_cases' => rand(50, 200),
                    'expected_pallets' => rand(2, 8),
                    'expected_pallet_type_id' => $palletTypes->random()->id,
                ]);
                break;

            case 'multi_line_same_type':
                $palletType = $palletTypes->random();
                for ($line = 1; $line <= rand(2, 4); $line++) {
                    PoLine::create([
                        'booking_po_number_id' => $po->id,
                        'line_number' => $line,
                        'expected_cases' => rand(30, 150),
                        'expected_pallets' => rand(1, 6),
                        'expected_pallet_type_id' => $palletType->id,
                    ]);
                }
                break;

            case 'multi_line_mixed_types':
                for ($line = 1; $line <= rand(3, 6); $line++) {
                    PoLine::create([
                        'booking_po_number_id' => $po->id,
                        'line_number' => $line,
                        'expected_cases' => rand(25, 100),
                        'expected_pallets' => rand(1, 4),
                        'expected_pallet_type_id' => $palletTypes->random()->id,
                    ]);
                }
                break;

            case 'large_volume':
                for ($line = 1; $line <= rand(2, 3); $line++) {
                    PoLine::create([
                        'booking_po_number_id' => $po->id,
                        'line_number' => $line,
                        'expected_cases' => rand(500, 1500),
                        'expected_pallets' => rand(20, 60),
                        'expected_pallet_type_id' => $palletTypes->random()->id,
                    ]);
                }
                break;

            case 'small_volume':
                PoLine::create([
                    'booking_po_number_id' => $po->id,
                    'line_number' => 1,
                    'expected_cases' => rand(5, 25),
                    'expected_pallets' => 1,
                    'expected_pallet_type_id' => $palletTypes->random()->id,
                ]);
                break;

            case 'completed_with_variances':
            case 'completed_perfect_match':
                for ($line = 1; $line <= rand(2, 4); $line++) {
                    PoLine::create([
                        'booking_po_number_id' => $po->id,
                        'line_number' => $line,
                        'expected_cases' => rand(50, 200),
                        'expected_pallets' => rand(2, 8),
                        'expected_pallet_type_id' => $palletTypes->random()->id,
                    ]);
                }
                break;
        }
    }

    private function completeBooking($booking, $perfectMatch = false)
    {
        // Mark as arrived
        $booking->update([
            'arrived_at' => now()->subHours(rand(1, 48)),
            'departed_at' => now()->subHours(rand(0, 24)),
        ]);

        // Add actual quantities to PO lines
        foreach ($booking->poNumbers as $po) {
            foreach ($po->lines as $line) {
                if ($perfectMatch) {
                    // Perfect match scenario
                    $line->update([
                        'actual_cases' => $line->expected_cases,
                        'actual_pallets' => $line->expected_pallets,
                        'actual_pallet_type_id' => $line->expected_pallet_type_id,
                    ]);
                } else {
                    // Variance scenario
                    $casesVariance = rand(-20, 20);
                    $palletsVariance = rand(-2, 3);
                    $typeChange = rand(1, 4) === 1; // 25% chance of type change

                    $line->update([
                        'actual_cases' => max(0, $line->expected_cases + $casesVariance),
                        'actual_pallets' => max(0, $line->expected_pallets + $palletsVariance),
                        'actual_pallet_type_id' => $typeChange
                            ? PalletType::where('id', '!=', $line->expected_pallet_type_id)->inRandomOrder()->first()->id
                            : $line->expected_pallet_type_id,
                    ]);
                }
            }
        }
    }

    private function generateVehicleReg()
    {
        $letters1 = chr(rand(65, 90)).chr(rand(65, 90));
        $numbers = str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT);
        $letters2 = chr(rand(65, 90)).chr(rand(65, 90)).chr(rand(65, 90));

        return $letters1.$numbers.' '.$letters2;
    }

    private function generatePoNumber($customerName, $counter)
    {
        $prefix = strtoupper(substr(str_replace(' ', '', $customerName), 0, 3));
        $date = now()->format('ymd');
        $suffix = str_pad($counter, 3, '0', STR_PAD_LEFT);

        return "PO-{$prefix}-{$date}-{$suffix}";
    }

    private function generateSpecialInstructions($scenario)
    {
        $instructions = [
            'simple_single_line' => 'Standard delivery - no special requirements',
            'multi_line_same_type' => 'All pallets are same type - handle with care',
            'multi_line_mixed_types' => 'Mixed pallet types - check each line carefully',
            'large_volume' => 'LARGE DELIVERY - Multiple vehicle drops may be required',
            'small_volume' => 'Small delivery - single pallet only',
            'completed_with_variances' => 'Completed delivery with some quantity differences',
            'completed_perfect_match' => 'Completed delivery - perfect match to expectations',
        ];

        return $instructions[$scenario] ?? 'Standard delivery instructions';
    }

    private function printSummary()
    {
        $totalBookings = Booking::count();
        $totalPoNumbers = BookingPoNumber::count();
        $totalPoLines = PoLine::count();
        $totalCustomers = Customer::count();
        $totalSlots = Slot::count();
        $completedBookings = Booking::whereNotNull('arrived_at')->count();

        $this->command->info('');
        $this->command->info('📊 Test Data Summary:');
        $this->command->info("   🏢 Customers: {$totalCustomers}");
        $this->command->info("   📅 Slots: {$totalSlots}");
        $this->command->info("   📦 Bookings: {$totalBookings}");
        $this->command->info("   📋 PO Numbers: {$totalPoNumbers}");
        $this->command->info("   📝 PO Lines: {$totalPoLines}");
        $this->command->info("   ✅ Completed: {$completedBookings}");
        $this->command->info('');
        $this->command->info('🌐 You can now test the system with realistic data!');
    }
}
