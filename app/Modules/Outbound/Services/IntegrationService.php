<?php

namespace App\Modules\Outbound\Services;

use App\Modules\Outbound\Models\OutboundLoad;
use App\Modules\Outbound\Models\OutboundOrder;
use App\Modules\Outbound\Models\CustomerAddress;
use App\Models\Booking;
use App\Models\FactoryBooking;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class IntegrationService
{
    /**
     * Create outbound load from completed inbound booking
     */
    public static function createFromBooking(Booking $booking): ?OutboundLoad
    {
        // Check if outbound integration is enabled
        if (!Setting::get('outbound_auto_create_from_booking', false)) {
            return null;
        }

        // Only create outbound for completed bookings
        if (!$booking->isCompleted()) {
            Log::info("Skipping outbound creation for booking {$booking->id} - not completed");
            return null;
        }

        // Check if customer has delivery addresses
        $customerAddress = CustomerAddress::forCustomer($booking->customer_id)
            ->active()
            ->default()
            ->first();

        if (!$customerAddress) {
            Log::warning("No default delivery address found for customer {$booking->customer_id}");
            return null;
        }

        try {
            return self::createOutboundLoad([
                'source_type' => 'booking',
                'source_id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'collection_depot_id' => $booking->slot->depot_id,
                'customer_address_id' => $customerAddress->id,
                'order_reference' => $booking->booking_reference ?? "BK{$booking->id}",
                'collection_reference' => $booking->booking_reference,
                'expected_pallets' => self::calculateExpectedPallets($booking),
                'expected_cases' => self::calculateExpectedCases($booking),
                'expected_units' => self::calculateExpectedUnits($booking),
                'notes' => "Auto-created from booking {$booking->booking_reference}",
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to create outbound load from booking {$booking->id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create outbound load from completed factory booking
     */
    public static function createFromFactoryBooking(FactoryBooking $factoryBooking): ?OutboundLoad
    {
        // Check if outbound integration is enabled
        if (!Setting::get('outbound_auto_create_from_factory', false)) {
            return null;
        }

        // Only create outbound for completed factory bookings
        if ($factoryBooking->status !== 'completed') {
            return null;
        }

        // Check if customer has delivery addresses
        $customerAddress = CustomerAddress::forCustomer($factoryBooking->customer_id)
            ->active()
            ->default()
            ->first();

        if (!$customerAddress) {
            Log::warning("No default delivery address found for customer {$factoryBooking->customer_id}");
            return null;
        }

        try {
            return self::createOutboundLoad([
                'source_type' => 'factory_booking',
                'source_id' => $factoryBooking->id,
                'customer_id' => $factoryBooking->customer_id,
                'collection_depot_id' => $factoryBooking->depot_id,
                'customer_address_id' => $customerAddress->id,
                'order_reference' => $factoryBooking->reference,
                'collection_reference' => $factoryBooking->reference,
                'expected_pallets' => self::calculateFactoryPallets($factoryBooking),
                'expected_cases' => self::calculateFactoryCases($factoryBooking),
                'expected_units' => self::calculateFactoryUnits($factoryBooking),
                'notes' => "Auto-created from factory booking {$factoryBooking->reference}",
                'delivery_priority' => $factoryBooking->priority >= 8 ? 'urgent' : 'standard',
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to create outbound load from factory booking {$factoryBooking->id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create outbound load manually with order data
     */
    public static function createManualLoad(array $loadData, array $ordersData): OutboundLoad
    {
        $load = OutboundLoad::create([
            'load_name' => $loadData['load_name'] ?? null,
            'planned_vehicle_id' => $loadData['planned_vehicle_id'] ?? null,
            'assigned_driver_id' => $loadData['assigned_driver_id'] ?? null,
            'notes' => $loadData['notes'] ?? null,
            'created_by' => auth()->id(),
            'created_from' => 'manual',
        ]);

        foreach ($ordersData as $orderData) {
            OutboundOrder::create([
                'outbound_load_id' => $load->id,
                'customer_id' => $orderData['customer_id'],
                'customer_address_id' => $orderData['customer_address_id'],
                'collection_depot_id' => $orderData['collection_depot_id'],
                'order_reference' => $orderData['order_reference'],
                'po_number' => $orderData['po_number'] ?? null,
                'expected_pallets' => $orderData['expected_pallets'] ?? 0,
                'expected_cases' => $orderData['expected_cases'] ?? 0,
                'expected_units' => $orderData['expected_units'] ?? 0,
                'expected_weight_kg' => $orderData['expected_weight_kg'] ?? null,
                'delivery_notes' => $orderData['delivery_notes'] ?? null,
                'planned_delivery_date' => $orderData['planned_delivery_date'] ?? null,
                'delivery_priority' => $orderData['delivery_priority'] ?? 'standard',
            ]);
        }

        // Create collection schedules
        self::createCollectionSchedule($load);

        return $load->fresh();
    }

    /**
     * Consolidate multiple orders into a single load
     */
    public static function consolidateOrders(array $orderIds): OutboundLoad
    {
        $orders = OutboundOrder::whereIn('id', $orderIds)
            ->with(['customer', 'customerAddress', 'collectionDepot'])
            ->get();

        if ($orders->isEmpty()) {
            throw new \InvalidArgumentException('No orders found to consolidate');
        }

        // Create new consolidated load
        $load = OutboundLoad::create([
            'load_name' => "Consolidated Load - " . now()->format('Y-m-d H:i'),
            'created_by' => auth()->id(),
            'created_from' => 'consolidation',
        ]);

        // Move orders to new load
        foreach ($orders as $order) {
            $order->update(['outbound_load_id' => $load->id]);
        }

        // Create collection schedules for new load
        self::createCollectionSchedule($load);

        return $load->fresh();
    }

    /**
     * Create outbound load with single order (helper method)
     */
    protected static function createOutboundLoad(array $data): OutboundLoad
    {
        $load = OutboundLoad::create([
            'load_name' => $data['source_type'] === 'factory_booking' ? 'Factory Load' : 'Standard Load',
            'created_by' => auth()->id() ?? 1, // Default to admin if no auth
            'created_from' => $data['source_type'] === 'booking' ? 'booking_completion' : 'factory_completion',
            'notes' => $data['notes'],
        ]);

        OutboundOrder::create([
            'outbound_load_id' => $load->id,
            'customer_id' => $data['customer_id'],
            'customer_address_id' => $data['customer_address_id'],
            'collection_depot_id' => $data['collection_depot_id'],
            'order_reference' => $data['order_reference'],
            'collection_reference' => $data['collection_reference'],
            'expected_pallets' => $data['expected_pallets'],
            'expected_cases' => $data['expected_cases'],
            'expected_units' => $data['expected_units'],
            'delivery_priority' => $data['delivery_priority'] ?? 'standard',
        ]);

        // Create collection schedule
        self::createCollectionSchedule($load);

        Log::info("Created outbound load {$load->load_reference} from {$data['source_type']} {$data['source_id']}");

        return $load->fresh();
    }

    /**
     * Create collection schedule for a load
     */
    protected static function createCollectionSchedule(OutboundLoad $load): void
    {
        $depotIds = $load->orders()->distinct('collection_depot_id')->pluck('collection_depot_id');
        $sequence = 1;

        foreach ($depotIds as $depotId) {
            $collection = $load->collections()->create([
                'depot_id' => $depotId,
                'collection_sequence' => $sequence++,
                'planned_collection_time' => now()->addDay(), // Default to tomorrow
                'estimated_duration_minutes' => 30,
                'status' => 'pending',
            ]);

            // Calculate depot totals
            $collection->updateDepotTotals();
        }
    }

    /**
     * Calculate expected quantities from booking data
     */
    protected static function calculateExpectedPallets(Booking $booking): int
    {
        // Try to get from PO lines first
        $poLinesTotals = $booking->poNumbers()
            ->with('poLines')
            ->get()
            ->reduce(function ($total, $poNumber) {
                return $total + $poNumber->poLines->sum('expected_pallets');
            }, 0);

        if ($poLinesTotals > 0) {
            return $poLinesTotals;
        }

        // Fallback to movement loads
        $movementTotals = $booking->movements()
            ->with('loads')
            ->get()
            ->reduce(function ($total, $movement) {
                return $total + $movement->loads->where('operation_type', 'inbound')->sum('expected_pallets');
            }, 0);

        return $movementTotals ?: 1; // Default to 1 if no data
    }

    protected static function calculateExpectedCases(Booking $booking): int
    {
        $poLinesTotals = $booking->poNumbers()
            ->with('poLines')
            ->get()
            ->reduce(function ($total, $poNumber) {
                return $total + $poNumber->poLines->sum('expected_cases');
            }, 0);

        if ($poLinesTotals > 0) {
            return $poLinesTotals;
        }

        $movementTotals = $booking->movements()
            ->with('loads')
            ->get()
            ->reduce(function ($total, $movement) {
                return $total + $movement->loads->where('operation_type', 'inbound')->sum('expected_cases');
            }, 0);

        return $movementTotals ?: 0;
    }

    protected static function calculateExpectedUnits(Booking $booking): int
    {
        // Calculate from cases if available
        $cases = self::calculateExpectedCases($booking);
        
        // Assume average 12 units per case if no specific data
        return $cases * 12;
    }

    protected static function calculateFactoryPallets(FactoryBooking $factoryBooking): int
    {
        // Factory bookings might have different data structure
        // This would depend on your factory booking data model
        return 1; // Default for now
    }

    protected static function calculateFactoryCases(FactoryBooking $factoryBooking): int
    {
        return 0; // Default for now
    }

    protected static function calculateFactoryUnits(FactoryBooking $factoryBooking): int
    {
        return 0; // Default for now
    }
}