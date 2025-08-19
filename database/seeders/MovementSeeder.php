<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Depot;
use App\Models\Movement;
use App\Models\MovementLoad;
use App\Models\PalletType;
use App\Models\Trailer;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MovementSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = Vehicle::all();
        $trailers = Trailer::all();
        $bookings = Booking::with('slot.depot')->take(5)->get();
        $depots = Depot::all();
        $customers = Customer::all();
        $palletTypes = PalletType::all();

        // Skip if no test data available
        if ($vehicles->isEmpty() || $trailers->isEmpty() || $depots->isEmpty()) {
            $this->command->info('Skipping movement seeder - insufficient test data');

            return;
        }

        // Create some movements for existing bookings
        foreach ($bookings as $index => $booking) {
            if ($index >= $vehicles->count()) {
                break;
            }

            $vehicle = $vehicles[$index];
            $trailer = $trailers[$index % $trailers->count()];

            // Create booked inbound movement
            $movement = Movement::create([
                'movement_type' => 'inbound_booked',
                'reference_number' => $booking->booking_reference,
                'depot_id' => $booking->slot->depot_id,
                'vehicle_id' => $vehicle->id,
                'trailer_id' => $trailer->id,
                'carrier_company' => $vehicle->carrier_company,
                'driver_name' => $vehicle->default_driver_name,
                'driver_phone' => $vehicle->default_driver_phone,
                'estimated_arrival' => $booking->slot->start_at->subHours(1),
                'current_status' => ['scheduled', 'arrived', 'at_bay', 'unloading'][array_rand(['scheduled', 'arrived', 'at_bay', 'unloading'])],
                'booking_id' => $booking->id,
                'load_type' => 'mixed_pallets',
                'hazmat' => false,
            ]);

            // If movement has arrived, set arrival time
            if (in_array($movement->current_status, ['arrived', 'at_bay', 'unloading', 'departed'])) {
                $movement->update([
                    'actual_arrival' => $booking->slot->start_at->addMinutes(rand(-30, 30)),
                ]);
            }

            // Create movement loads for each PO line in the booking
            foreach ($booking->poNumbers as $poNumber) {
                foreach ($poNumber->lines as $line) {
                    MovementLoad::create([
                        'movement_id' => $movement->id,
                        'customer_id' => $booking->customer_id,
                        'operation_type' => 'inbound',
                        'sequence' => 1,
                        'expected_cases' => $line->expected_cases,
                        'expected_pallets' => $line->expected_pallets,
                        'expected_pallet_type_id' => $line->expected_pallet_type_id,
                        'actual_cases' => in_array($movement->current_status, ['unloading', 'departed']) ?
                            $line->expected_cases + rand(-2, 5) : null,
                        'actual_pallets' => in_array($movement->current_status, ['unloading', 'departed']) ?
                            $line->expected_pallets + rand(-1, 2) : null,
                        'actual_pallet_type_id' => in_array($movement->current_status, ['unloading', 'departed']) ?
                            $line->expected_pallet_type_id : null,
                        'customer_reference' => 'REF-'.$booking->customer_id.'-'.$line->line_number,
                        'po_number' => $poNumber->po_number,
                        'booking_po_line_id' => $line->id,
                    ]);
                }
            }
        }

        // Create some unbooked inbound movements (only if we have customers and pallet types)
        if ($customers->isNotEmpty() && $palletTypes->isNotEmpty()) {
            for ($i = 0; $i < 3; $i++) {
                $vehicle = $vehicles[$i % $vehicles->count()];
                $trailer = $trailers[($i + 1) % $trailers->count()];
                $depot = $depots[$i % $depots->count()];
                $customer = $customers[$i % $customers->count()];

                $movement = Movement::create([
                    'movement_type' => 'inbound_unbooked',
                    'reference_number' => 'IB-'.str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'depot_id' => $depot->id,
                    'vehicle_id' => $vehicle->id,
                    'trailer_id' => $trailer->id,
                    'carrier_company' => $vehicle->carrier_company,
                    'driver_name' => $vehicle->default_driver_name,
                    'driver_phone' => $vehicle->default_driver_phone,
                    'estimated_arrival' => Carbon::now()->addHours(rand(1, 6)),
                    'current_status' => ['scheduled', 'en_route', 'arrived'][array_rand(['scheduled', 'en_route', 'arrived'])],
                    'load_type' => ['food_products', 'mixed_pallets', 'boxed_goods'][array_rand(['food_products', 'mixed_pallets', 'boxed_goods'])],
                    'hazmat' => false,
                    'special_instructions' => 'Unbooked arrival - check paperwork at gate',
                ]);

                // Create a movement load for unbooked arrival
                MovementLoad::create([
                    'movement_id' => $movement->id,
                    'customer_id' => $customer->id,
                    'operation_type' => 'inbound',
                    'sequence' => 1,
                    'expected_cases' => rand(50, 200),
                    'expected_pallets' => rand(2, 8),
                    'expected_pallet_type_id' => $palletTypes->random()->id,
                    'customer_reference' => 'UNBOOKED-'.$customer->id.'-'.$i,
                    'po_number' => 'PO-UNBOOKED-'.str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                ]);
            }
        }

        // Create an example of trailer swap scenario
        $swapVehicle1 = $vehicles->first();
        $swapVehicle2 = $vehicles->skip(1)->first();
        $swapTrailer = $trailers->first();
        $depot = $depots->first();

        $swapMovement = Movement::create([
            'movement_type' => 'inbound_booked',
            'reference_number' => 'SWAP-001',
            'depot_id' => $depot->id,
            'vehicle_id' => $swapVehicle1->id,
            'trailer_id' => $swapTrailer->id,
            'carrier_company' => $swapVehicle1->carrier_company,
            'driver_name' => $swapVehicle1->default_driver_name,
            'estimated_arrival' => Carbon::now()->subHours(2),
            'actual_arrival' => Carbon::now()->subHours(2),
            'current_status' => 'trailer_dropped',
            'trailer_dropped_at' => Carbon::now()->subHour(),
            'collecting_vehicle_id' => $swapVehicle2->id,
            'swap_notes' => 'Original vehicle departed, new vehicle will collect trailer after unloading',
            'load_type' => 'urgent_delivery',
        ]);
    }
}
