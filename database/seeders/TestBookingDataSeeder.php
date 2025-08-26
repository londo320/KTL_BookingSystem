<?php

namespace Database\Seeders;

use App\Models\Depot;
use App\Models\Customer;
use App\Models\Slot;
use App\Models\Booking;
use App\Models\BookingType;
use App\Models\TippingBay;
use App\Models\TippingLocation;
use App\Models\User;
use App\Models\Product;
use App\Models\TrailerType;
use App\Models\BookingHistory;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TestBookingDataSeeder extends Seeder
{
    public function run(): void
    {
        // PRODUCTION: Skip test booking data creation
        $this->command->info('Test booking data seeder skipped for production.');
        return;
        
        $depot = Depot::first();
        $customers = Customer::all();
        $bookingTypes = BookingType::all();
        $products = Product::take(5)->get();
        $trailerTypes = TrailerType::all();
        $tippingBays = TippingBay::all();
        $tippingLocations = TippingLocation::all();
        
        if ($customers->isEmpty() || $bookingTypes->isEmpty()) {
            $this->command->error('Need customers and booking types to create test data');
            return;
        }

        $this->command->info('Creating comprehensive test booking data...');

        // Generate data for 3 weeks ago to 1 week ahead
        $startDate = Carbon::now()->subWeeks(3)->startOfWeek();
        $endDate = Carbon::now()->addWeeks(1)->endOfWeek();

        $bookingCount = 0;

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            // Skip Sundays (no bookings)
            if ($date->isSunday()) continue;

            $slotsForDay = Slot::where('depot_id', $depot->id)
                ->whereDate('start_at', $date)
                ->get();

            foreach ($slotsForDay as $slot) {
                // Vary booking density - more bookings on weekdays
                $bookingChance = $date->isWeekend() ? 0.3 : 0.7;
                
                if (rand(1, 100) <= ($bookingChance * 100)) {
                    $booking = $this->createRealisticBooking(
                        $slot, 
                        $depot, 
                        $customers->random(), 
                        $bookingTypes->random(),
                        $products->random(),
                        $trailerTypes->random(),
                        $tippingBays->random(),
                        $tippingLocations->random(),
                        $date
                    );
                    
                    if ($booking) {
                        $bookingCount++;
                        
                        // Add realistic workflow events and history
                        $this->addBookingWorkflow($booking, $date);
                    }
                }
            }
        }

        $this->command->info("Created {$bookingCount} test bookings with realistic workflows");
    }

    private function createRealisticBooking($slot, $depot, $customer, $bookingType, $product, $trailerType, $tippingBay, $tippingLocation, $date)
    {
        $isPastBooking = $date->isPast();
        $isToday = $date->isToday();
        $isFuture = $date->isFuture();

        // Generate realistic vehicle details
        $carrierNames = [
            'Express Logistics Ltd', 'Swift Transport', 'Reliable Haulage', 
            'Premier Freight', 'Direct Line Transport', 'Speedy Deliveries',
            'National Carriers', 'Regional Express', 'Fast Track Logistics'
        ];

        $vehicleRegs = [
            'BX21 ABC', 'CY22 DEF', 'DZ20 GHI', 'EX23 JKL', 'FY21 MNO',
            'GX22 PQR', 'HY20 STU', 'JZ23 VWX', 'KX21 YZA', 'LY22 BCD'
        ];

        $trailerNumbers = [
            'TR12345', 'TR67890', 'TR11111', 'TR22222', 'TR33333',
            'TR44444', 'TR55555', 'TR66666', 'TR77777', 'TR88888'
        ];

        $driverNames = [
            'John Smith', 'Mike Johnson', 'Dave Wilson', 'Tom Brown', 'Steve Davis',
            'Paul Miller', 'Chris Taylor', 'Mark Anderson', 'Andy Thompson', 'Gary White'
        ];

        // Determine booking status based on date
        $status = 'confirmed';
        if ($isPastBooking) {
            $statusOptions = ['completed', 'completed', 'completed', 'cancelled', 'no-show'];
            $status = $statusOptions[array_rand($statusOptions)];
        } elseif ($isToday) {
            $statusOptions = ['confirmed', 'in-progress', 'completed', 'cancelled'];
            $status = $statusOptions[array_rand($statusOptions)];
        }

        // Get a user for this customer
        $user = $customer->users()->first() ?? User::first();
        
        // Check if this slot is already booked by this user
        $existingBooking = Booking::where('slot_id', $slot->id)
            ->where('user_id', $user->id)
            ->first();
            
        if ($existingBooking) {
            return null; // Skip this booking if slot already taken by this user
        }
        
        // Create the booking
        $booking = Booking::create([
            'slot_id' => $slot->id,
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'booking_type_id' => $bookingType->id,
            'trailer_type_id' => $trailerType->id,
            'tipping_location_id' => $tippingLocation->id,
            'tipping_bay_id' => rand(0, 1) ? $tippingBay->id : null, // Some bookings not assigned to bay yet
            'status' => $status,
            
            // Vehicle details JSON
            'vehicle_details' => [
                'carrier_name' => $carrierNames[array_rand($carrierNames)],
                'driver_name' => $driverNames[array_rand($driverNames)],
                'vehicle_registration' => $vehicleRegs[array_rand($vehicleRegs)],
                'trailer_number' => $trailerNumbers[array_rand($trailerNumbers)],
                'contact_phone' => '+44' . rand(1000000000, 9999999999),
            ],
            
            // Tipping workflow status
            'tipping_status' => $this->getTippingStatus($status, $isPastBooking),
            
            // Timing fields - set based on whether it's past/present/future
            'arrived_at' => $isPastBooking || $isToday ? $this->getArrivalTime($slot, $isPastBooking) : null,
            'departed_at' => $isPastBooking ? $this->getDepartureTime($slot, $isPastBooking) : null,
            
            'notes' => 'Test booking with realistic scenario data',
            'created_at' => $date->copy()->subDays(rand(1, 14)), // Created 1-14 days before slot
            'updated_at' => now(),
        ]);

        // Add products to the booking
        $booking->products()->attach($product->id, [
            'cases' => rand(10, 50),
            'pallets' => rand(1, 5),
            'po_reference' => 'PO' . str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT),
        ]);

        return $booking;
    }

    private function getTippingStatus($bookingStatus, $isPastBooking)
    {
        if ($bookingStatus === 'cancelled' || $bookingStatus === 'no-show') {
            return 'not_started';
        }

        if ($isPastBooking) {
            $statuses = [
                'trailer_departed', 'trailer_departed', 'trailer_departed', // Most completed
                'tipping_completed', 'moved_to_bay' // Some incomplete workflows
            ];
            return $statuses[array_rand($statuses)];
        }

        return 'not_started';
    }

    private function getArrivalTime($slot, $isPastBooking)
    {
        if (!$isPastBooking && !$slot->start_at->isToday()) {
            return null;
        }

        $scheduledTime = $slot->start_at;
        
        // Create realistic arrival patterns
        $scenarios = [
            'early' => rand(-30, -5), // 5-30 minutes early
            'ontime' => rand(-5, 5),  // Within 5 minutes
            'late' => rand(5, 60),    // 5-60 minutes late
            'very_late' => rand(60, 180), // 1-3 hours late
        ];

        $scenarioWeights = [
            'early' => 20,
            'ontime' => 50,
            'late' => 25,
            'very_late' => 5
        ];

        $scenario = $this->weightedRandom($scenarioWeights);
        $minutesOffset = $scenarios[$scenario];

        return $scheduledTime->copy()->addMinutes($minutesOffset);
    }

    private function getDepartureTime($slot, $isPastBooking)
    {
        if (!$isPastBooking) {
            return null;
        }

        // Departure typically 1-4 hours after arrival
        $arrivalTime = $this->getArrivalTime($slot, true);
        $processingTime = rand(60, 240); // 1-4 hours processing
        
        return $arrivalTime->copy()->addMinutes($processingTime);
    }

    private function addBookingWorkflow($booking, $date)
    {
        $isPastBooking = $date->isPast();
        $isToday = $date->isToday();

        // Add booking history for past bookings
        if ($isPastBooking || $isToday) {
            $this->addBookingHistory($booking);
        }

        // Add rebooking scenarios for some bookings
        if (rand(1, 100) <= 15) { // 15% chance of rebooking
            $this->addRebookingHistory($booking);
        }

        // Add cancellation scenarios
        if (rand(1, 100) <= 10) { // 10% chance of cancellation
            $this->addCancellationHistory($booking);
        }

        // Add tipping workflow details for past completed bookings
        if ($isPastBooking && in_array($booking->tipping_status, ['tipping_completed', 'trailer_departed'])) {
            $this->addTippingWorkflowTimes($booking);
        }

        // Add trailer drop/collection scenarios - disabled as fields were moved to movements table
        // if (rand(1, 100) <= 30) { // 30% chance of trailer drop scenario
        //     $this->addTrailerDropCollection($booking);
        // }
    }

    private function addBookingHistory($booking)
    {
        $histories = [
            [
                'action' => 'created',
                'reason' => 'Booking created via customer portal',
                'created_at' => $booking->created_at
            ],
            [
                'action' => 'confirmed',
                'reason' => 'Booking confirmed by depot staff',
                'created_at' => $booking->created_at->copy()->addHours(2)
            ]
        ];

        if ($booking->arrived_at) {
            $histories[] = [
                'action' => 'arrived',
                'reason' => 'Vehicle arrived at depot',
                'created_at' => $booking->arrived_at
            ];
        }

        if ($booking->tipping_bay_id) {
            $histories[] = [
                'action' => 'moved_to_bay',
                'description' => "Assigned to tipping bay",
                'created_at' => $booking->arrived_at ? $booking->arrived_at->copy()->addMinutes(30) : now()
            ];
        }

        if ($booking->departed_at) {
            $histories[] = [
                'action' => 'departed',
                'reason' => 'Vehicle departed depot',
                'created_at' => $booking->departed_at
            ];
        }

        foreach ($histories as $history) {
            BookingHistory::create([
                'booking_id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'user_id' => $booking->user_id,
                'action' => $history['action'],
                'reason' => $history['reason'],
                'created_at' => $history['created_at'],
                'updated_at' => $history['created_at'],
            ]);
        }
    }

    private function addRebookingHistory($booking)
    {
        // Simulate a rebooking scenario
        $originalSlot = $booking->slot->start_at->copy()->subDays(rand(1, 3));
        
        BookingHistory::create([
            'booking_id' => $booking->id,
            'customer_id' => $booking->customer_id,
            'user_id' => $booking->user_id,
            'action' => 'rebooked',
            'reason' => "Rebooked from {$originalSlot->format('Y-m-d H:i')} to {$booking->slot->start_at->format('Y-m-d H:i')}",
            'changes' => json_encode(['old_start_time' => $originalSlot->toISOString(), 'new_start_time' => $booking->slot->start_at->toISOString()]),
            'created_at' => $booking->created_at->copy()->addHours(rand(1, 24)),
        ]);

        $booking->update([
            'is_rebooked' => true,
            'rebook_count' => rand(1, 2),
        ]);
    }

    private function addCancellationHistory($booking)
    {
        if (rand(1, 100) <= 70) { // 70% chance it gets rebooked instead of staying cancelled
            BookingHistory::create([
                'booking_id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'user_id' => $booking->user_id,
                'action' => 'cancelled',
                'reason' => 'Booking cancelled by customer',
                'created_at' => $booking->slot->start_at->copy()->subHours(rand(2, 48)),
            ]);

            BookingHistory::create([
                'booking_id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'user_id' => $booking->user_id,
                'action' => 'rebooked',
                'reason' => 'Booking reinstated and rebooked',
                'created_at' => $booking->slot->start_at->copy()->subHours(rand(1, 24)),
            ]);
        } else {
            $booking->update(['status' => 'cancelled']);
            
            BookingHistory::create([
                'booking_id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'user_id' => $booking->user_id,
                'action' => 'cancelled',
                'reason' => 'Booking cancelled - no alternative slot available',
                'created_at' => $booking->slot->start_at->copy()->subHours(rand(2, 48)),
            ]);
        }
    }

    private function addTippingWorkflowTimes($booking)
    {
        if (!$booking->arrived_at) return;

        $currentTime = $booking->arrived_at->copy();

        // Trailer dropped at location (if applicable)
        if (rand(1, 100) <= 40) { // 40% chance of trailer drop
            $booking->update([
                'trailer_dropped_at' => $currentTime->copy()->addMinutes(rand(15, 45)),
                'tipping_notes' => 'Trailer dropped for tipping - driver departed with tractor unit'
            ]);
            $currentTime->addMinutes(rand(15, 45));
        }

        // Moved to bay
        if ($booking->tipping_bay_id) {
            $movedToBayTime = $currentTime->copy()->addMinutes(rand(10, 60));
            $booking->update(['moved_to_bay_at' => $movedToBayTime]);
            $currentTime = $movedToBayTime;
        }

        // Tipping started
        $tippingStarted = $currentTime->copy()->addMinutes(rand(5, 30));
        $booking->update(['tipping_started_at' => $tippingStarted]);

        // Tipping completed
        $tippingDuration = rand(30, 180); // 30 minutes to 3 hours
        $tippingCompleted = $tippingStarted->copy()->addMinutes($tippingDuration);
        $booking->update([
            'tipping_completed_at' => $tippingCompleted,
            'actual_tipping_duration' => $tippingDuration
        ]);

        // Trailer departed (if not a drop scenario)
        if (!$booking->trailer_dropped_at) {
            $booking->update([
                'trailer_departed_at' => $tippingCompleted->copy()->addMinutes(rand(5, 30))
            ]);
        }
    }

    private function addTrailerDropCollection($booking)
    {
        $vehicleDetails = $booking->vehicle_details;
        
        // Add trailer collection scenarios
        if (rand(1, 100) <= 50) { // 50% chance of separate collection
            // Solo unit arrived to collect
            $collectionTime = $booking->tipping_completed_at ? 
                $booking->tipping_completed_at->copy()->addHours(rand(2, 24)) : 
                $booking->slot->start_at->copy()->addHours(rand(4, 8));

            $collectionDrivers = ['Solo Driver 1', 'Solo Driver 2', 'Collection Specialist'];
            $collectionVehicles = ['SL21 ABC', 'SL22 DEF', 'SL23 GHI'];

            $booking->update([
                'trailer_left_on_site' => true,
                'trailer_collected_at' => $collectionTime,
                'collected_by_vehicle' => $collectionVehicles[array_rand($collectionVehicles)],
            ]);

            // Add history for collection
            BookingHistory::create([
                'booking_id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'user_id' => $booking->user_id,
                'action' => 'modified',
                'reason' => "Empty trailer collected by solo unit {$booking->collected_by_vehicle}",
                'changes' => json_encode([
                    'collection_driver' => $collectionDrivers[array_rand($collectionDrivers)],
                    'collection_vehicle' => $booking->collected_by_vehicle,
                    'collection_time' => $collectionTime->toISOString()
                ]),
                'created_at' => $collectionTime,
            ]);
        }
    }

    private function weightedRandom($weights)
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);
        
        $currentWeight = 0;
        foreach ($weights as $key => $weight) {
            $currentWeight += $weight;
            if ($random <= $currentWeight) {
                return $key;
            }
        }
        
        return array_key_first($weights);
    }
}