<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingType;
use App\Models\Customer;
use App\Models\CustomerBayAssignment;
use App\Models\Depot;
use App\Models\Slot;
use App\Models\TippingBay;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SlotAvailabilityController extends Controller
{
    /**
     * Get available slots filtered by customer and booking type
     */
    public function getAvailableSlots(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'booking_type_id' => 'required|exists:booking_types,id',
            'depot_id' => 'nullable|exists:depots,id',
            'days_ahead' => 'nullable|integer|min:1|max:90',
        ]);

        $customerId = $validated['customer_id'];
        $bookingTypeId = $validated['booking_type_id'];
        $depotId = $validated['depot_id'] ?? null;
        $daysAhead = $validated['days_ahead'] ?? 14;

        // Get customer's allowed bays
        $allowedBayIds = CustomerBayAssignment::where('customer_id', $customerId)
            ->where('is_allowed', true)
            ->pluck('tipping_bay_id')
            ->toArray();

        if (empty($allowedBayIds)) {
            // Customer has no bay assignments - show all slots (backwards compatibility)
            $query = Slot::with(['depot', 'tippingBay'])
                ->whereDate('start_at', '>=', now()->toDateString())
                ->whereDate('start_at', '<=', now()->addDays($daysAhead)->toDateString());

            if ($depotId) {
                $query->where('depot_id', $depotId);
            }

            $slots = $query->orderBy('start_at')->get();
        } else {
            // Filter slots by customer's allowed bays
            $query = Slot::with(['depot', 'tippingBay'])
                ->whereIn('tipping_bay_id', $allowedBayIds)
                ->whereDate('start_at', '>=', now()->toDateString())
                ->whereDate('start_at', '<=', now()->addDays($daysAhead)->toDateString());

            if ($depotId) {
                $query->where('depot_id', $depotId);
            }

            $slots = $query->orderBy('start_at')->get();
        }

        // Group slots by date and time (since multiple bays can have same start time)
        $groupedSlots = [];

        foreach ($slots as $slot) {
            // Check if slot has capacity
            $bookingCount = $slot->bookings()->count();
            if ($bookingCount >= $slot->capacity) {
                continue; // Skip full slots
            }

            // Check slot release rules
            if ($slot->allowed_customers->isNotEmpty() && !$slot->allowed_customers->contains('id', $customerId)) {
                continue; // Customer not allowed
            }

            $dateKey = $slot->start_at->format('Y-m-d');
            $timeKey = $slot->start_at->format('H:i');
            $compositeKey = $dateKey . '_' . $timeKey;

            if (!isset($groupedSlots[$compositeKey])) {
                $groupedSlots[$compositeKey] = [
                    'date' => $dateKey,
                    'time' => $timeKey,
                    'start_at' => $slot->start_at->toIso8601String(),
                    'depot_id' => $slot->depot_id,
                    'depot_name' => $slot->depot->name,
                    'available_bays' => [],
                    'slot_ids' => [],
                ];
            }

            // Add bay to this time slot
            $groupedSlots[$compositeKey]['available_bays'][] = [
                'bay_id' => $slot->tipping_bay_id,
                'bay_name' => $slot->tippingBay ? $slot->tippingBay->name : 'No Bay',
                'bay_code' => $slot->tippingBay ? $slot->tippingBay->code : null,
            ];

            $groupedSlots[$compositeKey]['slot_ids'][] = $slot->id;
        }

        // Convert to array and sort
        $result = array_values($groupedSlots);

        usort($result, function ($a, $b) {
            return strcmp($a['start_at'], $b['start_at']);
        });

        // Note: Bay assignment happens automatically on arrival, not during booking
        // User only needs to know that capacity exists at this time

        return response()->json([
            'success' => true,
            'slots' => $result,
            'total' => count($result),
        ]);
    }

    /**
     * Get customer's priority bay for a given depot
     */
    public function getCustomerPriorityBay(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'depot_id' => 'required|exists:depots,id',
        ]);

        $priorityAssignment = CustomerBayAssignment::with('tippingBay')
            ->where('customer_id', $validated['customer_id'])
            ->whereHas('tippingBay', function ($query) use ($validated) {
                $query->where('depot_id', $validated['depot_id']);
            })
            ->where('is_allowed', true)
            ->orderBy('priority', 'asc')
            ->first();

        if (!$priorityAssignment) {
            return response()->json([
                'success' => false,
                'message' => 'No bay assignments found for this customer at this depot',
            ]);
        }

        return response()->json([
            'success' => true,
            'bay' => [
                'id' => $priorityAssignment->tippingBay->id,
                'name' => $priorityAssignment->tippingBay->name,
                'code' => $priorityAssignment->tippingBay->code,
                'priority' => $priorityAssignment->priority,
            ],
        ]);
    }
}
