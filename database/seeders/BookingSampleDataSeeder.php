<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingType;
use App\Models\Customer;
use App\Models\Depot;
use App\Models\Slot;
use App\Models\TippingBay;
use App\Models\TippingLocation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingSampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing booking data
        echo "Clearing existing booking data...\n";
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('booking_product')->truncate();
        DB::table('booking_history')->truncate();
        Booking::withTrashed()->forceDelete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get required data
        $users = User::all();
        $customers = Customer::all();
        $depots = Depot::all();
        $bookingTypes = BookingType::all();
        $slots = Slot::with('depot')->orderBy('start_at')->get();
        $tippingLocations = TippingLocation::all();
        $tippingBays = TippingBay::all();

        echo "Creating comprehensive sample booking data...\n";

        $statuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];
        $tippingStatuses = ['not_started', 'trailer_dropped', 'moved_to_bay', 'tipping_in_progress', 'tipping_completed', 'trailer_departed'];
        $vehicleTypes = ['Artic', 'Rigid', 'Van', 'Container'];
        $carriers = ['DHL Express', 'UPS Logistics', 'FedEx Ground', 'Royal Mail', 'TNT Express', 'DPD Group', 'Hermes', 'Yodel'];
        $loadTypes = ['Palletized', 'Loose Load', 'Containerized', 'Bulk', 'Mixed', 'Refrigerated', 'Hazmat'];

        $bookingCount = 0;
        $slotsUsed = 0;

        foreach ($slots->take(150) as $slot) {
            // Skip some slots to create variety
            if (rand(1, 100) <= 25) {
                continue;
            } // 25% chance to skip

            $user = $users->random();
            $customer = $customers->random();
            $bookingType = $bookingTypes->random();

            // Determine status based on slot timing
            $now = now();
            $slotStart = Carbon::parse($slot->start_at);
            $slotEnd = Carbon::parse($slot->end_at);

            if ($slotEnd < $now->subDays(2)) {
                // Past slots - higher chance of completion
                $status = collect(['completed', 'completed', 'completed', 'cancelled'])->random();
                $tippingStatus = $status === 'completed' ? 'trailer_departed' : 'not_started';
            } elseif ($slotStart < $now && $slotEnd > $now) {
                // Current slots - in progress
                $status = collect(['in_progress', 'confirmed'])->random();
                $tippingStatus = collect(['moved_to_bay', 'tipping_in_progress'])->random();
            } elseif ($slotStart > $now && $slotStart < $now->addDays(7)) {
                // Future slots within week - mostly confirmed
                $status = collect(['confirmed', 'confirmed', 'pending'])->random();
                $tippingStatus = 'not_started';
            } else {
                // Far future - pending or confirmed
                $status = collect(['pending', 'confirmed'])->random();
                $tippingStatus = 'not_started';
            }

            // Create booking reference
            $reference = 'WM-'.$slotStart->format('Ymd').'-'.strtoupper(substr(uniqid(), -4));

            // Generate realistic vehicle data
            $vehicleReg = $this->generateVehicleRegistration();
            $containerNum = $this->generateContainerNumber();
            $driverName = $this->generateDriverName();
            $driverPhone = $this->generatePhoneNumber();
            $carrier = $carriers[array_rand($carriers)];
            $loadType = $loadTypes[array_rand($loadTypes)];

            // Create the booking
            $booking = Booking::create([
                'slot_id' => $slot->id,
                'booking_type_id' => $bookingType->id,
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'booking_reference' => $reference,
                'reference' => 'REF-'.rand(1000, 9999),
                'status' => $status,
                'vehicle_registration' => $vehicleReg,
                'container_number' => $containerNum,
                'carrier_company' => $carrier,
                'carrier_contact' => $driverName,
                'gate_number' => 'G'.rand(1, 8),
                'bay_number' => 'B'.rand(1, 12),
                'manifest_number' => 'MAN-'.rand(10000, 99999),
                'load_type' => $loadType,
                'hazmat' => rand(1, 100) <= 15, // 15% chance
                'temperature_requirements' => rand(1, 100) <= 20 ? 'Ambient' : null,
                'estimated_arrival' => $slotStart->copy()->subMinutes(rand(15, 45)),
                'expected_cases' => rand(50, 500),
                'actual_cases' => $status === 'completed' ? rand(45, 520) : null,
                'expected_pallets' => rand(5, 25),
                'actual_pallets' => $status === 'completed' ? rand(4, 27) : null,
                'container_size' => rand(15000, 45000), // Weight in kg
                'notes' => $this->generateNotes(),
                'special_instructions' => rand(1, 100) <= 30 ? $this->generateSpecialInstructions() : null,
                'tipping_status' => $tippingStatus,
                'tipping_location_id' => $tippingStatuses !== 'not_started' && $tippingLocations->count() > 0 ? $tippingLocations->random()->id : null,
                'tipping_bay_id' => in_array($tippingStatus, ['moved_to_bay', 'tipping_in_progress', 'tipping_completed']) && $tippingBays->count() > 0 ? $tippingBays->random()->id : null,
                'tipping_operator_id' => in_array($tippingStatus, ['tipping_in_progress', 'tipping_completed']) ? $users->random()->id : null,
                'bay_assigned_by' => in_array($tippingStatus, ['moved_to_bay', 'tipping_in_progress', 'tipping_completed']) ? $users->random()->id : null,
                'created_at' => $slotStart->copy()->subDays(rand(1, 14)),
                'updated_at' => now(),
            ]);

            // Set tipping timestamps based on status
            if ($tippingStatus !== 'not_started') {
                $baseTime = $slotStart->copy();
                $updates = [];

                if (in_array($tippingStatus, ['trailer_dropped', 'moved_to_bay', 'tipping_in_progress', 'tipping_completed', 'trailer_departed'])) {
                    $updates['trailer_dropped_at'] = $baseTime->copy()->subMinutes(rand(30, 90));
                }

                if (in_array($tippingStatus, ['moved_to_bay', 'tipping_in_progress', 'tipping_completed', 'trailer_departed'])) {
                    $updates['moved_to_bay_at'] = $baseTime->copy()->subMinutes(rand(15, 60));
                }

                if (in_array($tippingStatus, ['tipping_in_progress', 'tipping_completed', 'trailer_departed'])) {
                    $updates['tipping_started_at'] = $baseTime->copy()->addMinutes(rand(5, 30));
                }

                if (in_array($tippingStatus, ['tipping_completed', 'trailer_departed'])) {
                    $startedAt = $baseTime->copy()->addMinutes(rand(5, 30));
                    $duration = rand(45, 180); // 45-180 minutes
                    $updates['tipping_completed_at'] = $startedAt->copy()->addMinutes($duration);
                    $updates['actual_tipping_duration'] = $duration;
                }

                if ($tippingStatus === 'trailer_departed') {
                    $updates['trailer_departed_at'] = $baseTime->copy()->addMinutes(rand(200, 300));
                }

                $booking->update($updates);
            }

            // Handle cancelled bookings
            if ($status === 'cancelled') {
                $booking->update([
                    'cancelled_at' => $slotStart->copy()->subDays(rand(1, 5)),
                    'cancellation_reason' => collect([
                        'Vehicle breakdown',
                        'Driver unavailable',
                        'Load cancelled by customer',
                        'Weather conditions',
                        'Route change required',
                        'Delayed at previous stop',
                    ])->random(),
                    'cancelled_by' => $users->random()->id,
                ]);
            }

            $bookingCount++;
            $slotsUsed++;
        }

        // Create some rebooked bookings for testing
        echo "Creating rebooked bookings...\n";
        $originalBookings = Booking::where('status', '!=', 'cancelled')
            ->whereNull('is_rebooked')
            ->inRandomOrder()
            ->take(8)
            ->get();

        foreach ($originalBookings as $originalBooking) {
            // Find a future slot for rebooking
            $futureSlot = Slot::where('start_at', '>', now())
                ->whereDoesntHave('bookings')
                ->inRandomOrder()
                ->first();

            if ($futureSlot) {
                $originalBooking->rebook($futureSlot, collect([
                    'Driver schedule change',
                    'Load priority updated',
                    'Customer request',
                    'Vehicle maintenance required',
                    'Route optimization',
                ])->random());
            }
        }

        echo "Sample data creation completed!\n";
        echo "Created {$bookingCount} bookings across {$slotsUsed} slots\n";
        echo "Booking statuses distribution:\n";

        foreach ($statuses as $status) {
            $count = Booking::where('status', $status)->count();
            echo "  {$status}: {$count}\n";
        }

        echo "Tipping statuses distribution:\n";
        foreach ($tippingStatuses as $tStatus) {
            $count = Booking::where('tipping_status', $tStatus)->count();
            echo "  {$tStatus}: {$count}\n";
        }
    }

    private function generateVehicleRegistration(): string
    {
        $letters1 = chr(rand(65, 90)).chr(rand(65, 90));
        $numbers = rand(10, 99);
        $letters2 = chr(rand(65, 90)).chr(rand(65, 90)).chr(rand(65, 90));

        return $letters1.$numbers.' '.$letters2;
    }

    private function generateContainerNumber(): string
    {
        $prefix = collect(['TEMU', 'MSCU', 'GESU', 'CSQU', 'FCIU'])->random();

        return $prefix.rand(1000000, 9999999);
    }

    private function generateDriverName(): string
    {
        $firstNames = ['John', 'Mike', 'David', 'Steve', 'Paul', 'Mark', 'James', 'Robert', 'Gary', 'Kevin', 'Simon', 'Lee', 'Craig', 'Wayne', 'Tony'];
        $lastNames = ['Smith', 'Jones', 'Taylor', 'Brown', 'Wilson', 'Evans', 'Thomas', 'Roberts', 'Johnson', 'Davies', 'Robinson', 'Wright', 'Thompson', 'White', 'Hughes'];

        return $firstNames[array_rand($firstNames)].' '.$lastNames[array_rand($lastNames)];
    }

    private function generatePhoneNumber(): string
    {
        return '07'.rand(100000000, 999999999);
    }

    private function generateNotes(): ?string
    {
        $notes = [
            'Standard delivery - no special requirements',
            'Fragile goods - handle with care',
            'Time-sensitive delivery',
            'Customer collection preferred',
            'Additional paperwork required',
            'Multi-drop delivery',
            'Return packaging required',
            'POD signature essential',
            null, null, null, // 30% chance of no notes
        ];

        return $notes[array_rand($notes)];
    }

    private function generateSpecialInstructions(): string
    {
        $instructions = [
            'Call customer 30 minutes before arrival',
            'Use rear entrance only',
            'Driver must wear high-vis vest',
            'Tailgate required for unloading',
            'Customer to provide forklift operator',
            'Check temperature log on arrival',
            'Obtain signature from goods-in supervisor only',
            'Take photos of any damage before unloading',
        ];

        return $instructions[array_rand($instructions)];
    }
}
