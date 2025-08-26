<?php

namespace App\Modules\Outbound\Services;

use App\Modules\Outbound\Models\PhysicalLoadRegistration;
use App\Modules\Outbound\Models\WmsStagingOrder;
use App\Modules\Outbound\Models\OutboundLoad;
use App\Modules\Outbound\Models\OutboundOrder;
use App\Modules\Outbound\Models\CustomerAddress;
use App\Models\Customer;
use App\Models\Depot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderMatchingService
{
    /**
     * Process pending WMS orders and match them to physical loads
     */
    public function processPendingOrders(): array
    {
        $results = [
            'processed' => 0,
            'matched' => 0,
            'failed' => 0,
            'errors' => []
        ];

        $pendingOrders = WmsStagingOrder::pending()
            ->orderBy('uploaded_at')
            ->get();

        foreach ($pendingOrders as $stagingOrder) {
            try {
                if ($this->matchStagingOrder($stagingOrder)) {
                    $results['matched']++;
                } else {
                    $results['failed']++;
                }
                $results['processed']++;
            } catch (\Exception $e) {
                $results['errors'][] = "Order {$stagingOrder->order_reference}: " . $e->getMessage();
                $stagingOrder->markAsFailed("System error: " . $e->getMessage());
                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * Match a specific staging order to the outbound system
     */
    public function matchStagingOrder(WmsStagingOrder $stagingOrder): bool
    {
        // Find physical load registration
        $physicalLoad = PhysicalLoadRegistration::where('load_reference', $stagingOrder->load_reference)
            ->first();

        if (!$physicalLoad) {
            $stagingOrder->markAsFailed("No physical load found for reference: {$stagingOrder->load_reference}");
            return false;
        }

        // Find or create outbound load
        $outboundLoad = $this->findOrCreateOutboundLoad($physicalLoad, $stagingOrder);

        if (!$outboundLoad) {
            $stagingOrder->markAsFailed("Could not create outbound load");
            return false;
        }

        // Find customer
        $customer = $stagingOrder->findMatchingCustomer();
        if (!$customer) {
            $stagingOrder->markAsFailed("Customer not found: {$stagingOrder->customer_code} / {$stagingOrder->customer_name}");
            return false;
        }

        // Find collection depot
        $collectionDepot = $stagingOrder->findMatchingDepot();
        if (!$collectionDepot) {
            $stagingOrder->markAsFailed("Collection depot not found: {$stagingOrder->collection_depot_code}");
            return false;
        }

        // Find or create customer address
        $customerAddress = $this->findOrCreateCustomerAddress($customer, $stagingOrder);
        if (!$customerAddress) {
            $stagingOrder->markAsFailed("Could not create customer address");
            return false;
        }

        try {
            DB::transaction(function () use ($stagingOrder, $outboundLoad, $customer, $collectionDepot, $customerAddress) {
                // Create outbound order
                $outboundOrder = OutboundOrder::create([
                    'outbound_load_id' => $outboundLoad->id,
                    'customer_id' => $customer->id,
                    'customer_address_id' => $customerAddress->id,
                    'order_reference' => $stagingOrder->order_reference,
                    'po_number' => $stagingOrder->po_number,
                    'collection_depot_id' => $collectionDepot->id,
                    'collection_reference' => $stagingOrder->collection_reference,
                    'planned_delivery_date' => $stagingOrder->planned_delivery_date,
                    'planned_delivery_time_start' => $stagingOrder->delivery_time_start,
                    'planned_delivery_time_end' => $stagingOrder->delivery_time_end,
                    'expected_pallets' => $stagingOrder->pallets,
                    'expected_cases' => $stagingOrder->cases,
                    'expected_units' => $stagingOrder->units,
                    'expected_weight_kg' => $stagingOrder->weight_kg,
                    'temperature_controlled' => $stagingOrder->temperature_controlled,
                    'fragile' => $stagingOrder->fragile,
                    'hazardous' => $stagingOrder->hazardous,
                    'handling_instructions' => $stagingOrder->special_instructions,
                    'status' => 'ready_for_collection',
                ]);

                // Mark staging order as matched
                $stagingOrder->markAsMatched($outboundLoad, $outboundOrder);

                // Update load totals
                $this->updateLoadTotals($outboundLoad);

                // Update physical load matching progress
                $physicalLoad = $stagingOrder->physicalLoad;
                if ($physicalLoad) {
                    $physicalLoad->updateMatchedCount();
                }
            });

            Log::info("Successfully matched staging order", [
                'staging_order_id' => $stagingOrder->id,
                'order_reference' => $stagingOrder->order_reference,
                'load_reference' => $stagingOrder->load_reference
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error("Failed to match staging order", [
                'staging_order_id' => $stagingOrder->id,
                'error' => $e->getMessage()
            ]);
            
            $stagingOrder->markAsFailed("Database error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find or create outbound load for physical load
     */
    private function findOrCreateOutboundLoad(PhysicalLoadRegistration $physicalLoad, WmsStagingOrder $stagingOrder): ?OutboundLoad
    {
        // Check if outbound load already exists
        if ($physicalLoad->outbound_load_id) {
            return $physicalLoad->outboundLoad;
        }

        try {
            $outboundLoad = OutboundLoad::create([
                'load_reference' => $physicalLoad->load_reference,
                'load_name' => "Auto-created from physical load arrival",
                'status' => 'collecting',
                'created_from' => 'physical_arrival',
                'planned_vehicle_id' => null, // Will be updated when vehicle info is available
                'notes' => "Driver: {$physicalLoad->driver_name}, Vehicle: {$physicalLoad->vehicle_registration}",
            ]);

            // Link back to physical load
            $physicalLoad->update(['outbound_load_id' => $outboundLoad->id]);

            return $outboundLoad;

        } catch (\Exception $e) {
            Log::error("Failed to create outbound load", [
                'physical_load_id' => $physicalLoad->id,
                'load_reference' => $physicalLoad->load_reference,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Find or create customer address from staging data
     */
    private function findOrCreateCustomerAddress(Customer $customer, WmsStagingOrder $stagingOrder): ?CustomerAddress
    {
        // Try to find existing address by postcode
        $existingAddress = CustomerAddress::where('customer_id', $customer->id)
            ->where('postcode', $stagingOrder->delivery_postcode)
            ->first();

        if ($existingAddress) {
            return $existingAddress;
        }

        try {
            // Parse delivery address (this is simplified - you may want more sophisticated parsing)
            $addressLines = explode("\n", $stagingOrder->delivery_address_raw);
            $addressLine1 = $addressLines[0] ?? $stagingOrder->delivery_address_raw;
            $addressLine2 = $addressLines[1] ?? null;
            
            // Extract city (assumes it's before postcode)
            $city = $this->extractCityFromAddress($stagingOrder->delivery_address_raw, $stagingOrder->delivery_postcode);

            return CustomerAddress::create([
                'customer_id' => $customer->id,
                'address_name' => "Auto-created from WMS",
                'address_line_1' => $addressLine1,
                'address_line_2' => $addressLine2,
                'city' => $city,
                'postcode' => $stagingOrder->delivery_postcode,
                'is_active' => true,
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to create customer address", [
                'customer_id' => $customer->id,
                'staging_order_id' => $stagingOrder->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Update outbound load totals from its orders
     */
    private function updateLoadTotals(OutboundLoad $load): void
    {
        $orders = $load->orders;
        
        $load->update([
            'total_orders' => $orders->count(),
            'total_customers' => $orders->unique('customer_id')->count(),
            'total_pallets' => $orders->sum('expected_pallets'),
            'total_cases' => $orders->sum('expected_cases'),
            'total_units' => $orders->sum('expected_units'),
            'total_weight_kg' => $orders->sum('expected_weight_kg'),
        ]);
    }

    /**
     * Simple city extraction from address string
     */
    private function extractCityFromAddress(string $address, string $postcode): string
    {
        // Remove postcode and take the last meaningful part
        $withoutPostcode = str_replace($postcode, '', $address);
        $lines = array_map('trim', explode("\n", $withoutPostcode));
        $lines = array_filter($lines); // Remove empty lines
        
        // Take the last non-empty line as city
        return end($lines) ?: 'Unknown City';
    }
}