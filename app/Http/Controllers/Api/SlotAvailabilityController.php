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
use App\Services\SlotAvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SlotAvailabilityController extends Controller
{
    protected SlotAvailabilityService $slotService;

    public function __construct(SlotAvailabilityService $slotService)
    {
        $this->slotService = $slotService;
    }

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
        $daysAhead = (int) ($validated['days_ahead'] ?? 7); // Reduced default from 14 to 7 days for performance

        // Get booking type to check time restrictions
        $bookingType = BookingType::find($bookingTypeId);
        if (!$bookingType) {
            return response()->json([
                'success' => false,
                'message' => 'Booking type not found',
            ], 404);
        }

        // Get customer's allowed bays
        $customerBayAssignments = CustomerBayAssignment::where('customer_id', $customerId)
            ->where('is_active', true)
            ->pluck('tipping_bay_id')
            ->toArray();

        // Build base query with optimizations
        $query = Slot::with(['depot', 'tippingBay'])
            ->where('start_at', '>=', now())
            ->where('start_at', '<=', now()->addDays($daysAhead))
            ->where('is_blocked', false);

        if ($depotId) {
            $query->where('depot_id', $depotId);
        }

        if (!empty($customerBayAssignments)) {
            // Get bays that allow public bookings for released slots
            $publicBayIds = TippingBay::where('allow_public_bookings', true)
                ->where('is_active', true)
                ->pluck('id')
                ->toArray();

            // Filter slots: either customer's assigned bays OR public bays with released slots
            $query->where(function ($q) use ($customerBayAssignments, $publicBayIds) {
                // Always include customer's assigned bays
                $q->whereIn('tipping_bay_id', $customerBayAssignments);

                // Also include public bays if slots are already released
                if (!empty($publicBayIds)) {
                    $q->orWhere(function ($subQ) use ($publicBayIds) {
                        $subQ->whereIn('tipping_bay_id', $publicBayIds)
                            ->where(function ($releaseQ) {
                                $releaseQ->whereNull('released_at')
                                    ->orWhere('released_at', '<=', now());
                            });
                    });
                }
            });
        }
        // If customer has no bay assignments, show all slots (backwards compatibility)
        // This allows customers without bay restrictions to book any slot

        // Order and limit for performance (max 500 slots per request)
        $slots = $query->orderBy('start_at')
            ->limit(500)
            ->get();

        // Group slots by date and time (since multiple bays can have same start time)
        $groupedSlots = [];
        $debugInfo = [
            'total_slots_fetched' => $slots->count(),
            'slots_skipped_capacity' => 0,
            'slots_skipped_customer_release' => 0,
            'slots_skipped_time_restriction' => 0,
            'slots_skipped_equipment' => 0,
            'slots_included' => 0,
        ];

        foreach ($slots as $slot) {
            // Use the service to check full availability including equipment requirements
            $availability = $this->slotService->isSlotAvailable(
                $slot,
                $customerId,
                $bookingTypeId
            );

            if (!$availability['available']) {
                // Track which checks failed
                foreach ($availability['errors'] as $error) {
                    if (str_contains($error, 'capacity')) {
                        $debugInfo['slots_skipped_capacity']++;
                    } elseif (str_contains($error, 'time window')) {
                        $debugInfo['slots_skipped_time_restriction']++;
                    } elseif (str_contains($error, 'equipment')) {
                        $debugInfo['slots_skipped_equipment']++;
                    } elseif (str_contains($error, 'blocked')) {
                        $debugInfo['slots_skipped_capacity']++;
                    }
                }
                continue; // Skip unavailable slots
            }

            $debugInfo['slots_included']++;

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

            // Add bay to this time slot (avoid duplicates)
            $bayId = $slot->tipping_bay_id;
            $bayExists = false;

            foreach ($groupedSlots[$compositeKey]['available_bays'] as $existingBay) {
                if ($existingBay['bay_id'] === $bayId) {
                    $bayExists = true;
                    break;
                }
            }

            if (!$bayExists) {
                $groupedSlots[$compositeKey]['available_bays'][] = [
                    'bay_id' => $bayId,
                    'bay_name' => $slot->tippingBay ? $slot->tippingBay->name : 'No Bay',
                    'bay_code' => $slot->tippingBay ? $slot->tippingBay->code : null,
                ];
            }

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
            'debug' => array_merge($debugInfo, [
                'customer_allowed_bays' => $allowedBayIds,
                'has_bay_restrictions' => !empty($allowedBayIds),
            ]),
            'booking_type_time_restrictions' => [
                'global_start' => $bookingType->booking_start_time,
                'global_end' => $bookingType->booking_end_time,
            ],
        ]);
    }

    /**
     * Get available dates with slot counts (for date picker sidebar)
     */
    public function getAvailableDates(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'booking_type_id' => 'required|exists:booking_types,id',
            'depot_id' => 'nullable|exists:depots,id',
            'days_ahead' => 'nullable|integer|min:1|max:30',
        ]);

        $customerId = $validated['customer_id'];
        $bookingTypeId = $validated['booking_type_id'];
        $depotId = $validated['depot_id'] ?? null;
        $daysAhead = (int) ($validated['days_ahead'] ?? 14);

        // Get customer's allowed bays
        $allowedBayIds = CustomerBayAssignment::where('customer_id', $customerId)
            ->where('is_active', true)
            ->pluck('tipping_bay_id')
            ->toArray();

        // Build query
        $query = Slot::select('start_at')
            ->where('start_at', '>=', now())
            ->where('start_at', '<=', now()->addDays($daysAhead))
            ->where('is_blocked', false)
            ->whereNull('deleted_at');

        if ($depotId) {
            $query->where('depot_id', $depotId);
        }

        if (!empty($allowedBayIds)) {
            $query->whereIn('tipping_bay_id', $allowedBayIds);
        }

        $slots = $query->get();

        // Group by date and count available slots per date
        $dateGroups = [];
        foreach ($slots as $slot) {
            // Check availability using service
            $availability = $this->slotService->isSlotAvailable(
                $slot,
                $customerId,
                $bookingTypeId
            );

            if ($availability['available']) {
                $dateKey = $slot->start_at->format('Y-m-d');
                if (!isset($dateGroups[$dateKey])) {
                    $dateGroups[$dateKey] = [
                        'date' => $dateKey,
                        'day_name' => $slot->start_at->format('l'), // Monday, Tuesday, etc.
                        'formatted_date' => $slot->start_at->format('D M j'), // Mon Feb 26
                        'count' => 0,
                    ];
                }
                $dateGroups[$dateKey]['count']++;
            }
        }

        // Convert to array and sort
        $result = array_values($dateGroups);
        usort($result, function ($a, $b) {
            return strcmp($a['date'], $b['date']);
        });

        return response()->json([
            'success' => true,
            'dates' => $result,
            'total_dates' => count($result),
        ]);
    }

    /**
     * Get available slots for a specific date
     */
    public function getSlotsByDate(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'booking_type_id' => 'required|exists:booking_types,id',
            'depot_id' => 'nullable|exists:depots,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $customerId = $validated['customer_id'];
        $bookingTypeId = $validated['booking_type_id'];
        $depotId = $validated['depot_id'] ?? null;
        $date = Carbon::parse($validated['date']);

        // Get customer's allowed bays
        $allowedBayIds = CustomerBayAssignment::where('customer_id', $customerId)
            ->where('is_active', true)
            ->pluck('tipping_bay_id')
            ->toArray();

        // Build query for specific date
        $query = Slot::with(['depot', 'tippingBay'])
            ->whereDate('start_at', $date->toDateString())
            ->where('start_at', '>=', now())
            ->where('is_blocked', false)
            ->whereNull('deleted_at');

        if ($depotId) {
            $query->where('depot_id', $depotId);
        }

        if (!empty($allowedBayIds)) {
            $query->whereIn('tipping_bay_id', $allowedBayIds);
        }

        $slots = $query->orderBy('start_at')->get();

        // Group by time, showing only customer's allowed bays
        $groupedSlots = [];

        foreach ($slots as $slot) {
            // Check availability
            $availability = $this->slotService->isSlotAvailable(
                $slot,
                $customerId,
                $bookingTypeId
            );

            if (!$availability['available']) {
                continue;
            }

            $timeKey = $slot->start_at->format('H:i');

            if (!isset($groupedSlots[$timeKey])) {
                $groupedSlots[$timeKey] = [
                    'time' => $timeKey,
                    'start_at' => $slot->start_at->toIso8601String(),
                    'available_bays' => [],
                    'slot_ids' => [],
                ];
            }

            // Add bay (avoid duplicates)
            $bayId = $slot->tipping_bay_id;
            $bayExists = false;

            foreach ($groupedSlots[$timeKey]['available_bays'] as $existingBay) {
                if ($existingBay['bay_id'] === $bayId) {
                    $bayExists = true;
                    break;
                }
            }

            if (!$bayExists) {
                $groupedSlots[$timeKey]['available_bays'][] = [
                    'bay_id' => $bayId,
                    'bay_name' => $slot->tippingBay ? $slot->tippingBay->name : 'No Bay',
                    'bay_code' => $slot->tippingBay ? $slot->tippingBay->code : null,
                ];
            }

            $groupedSlots[$timeKey]['slot_ids'][] = $slot->id;
        }

        // Convert to array and sort by time
        $result = array_values($groupedSlots);
        usort($result, function ($a, $b) {
            return strcmp($a['time'], $b['time']);
        });

        return response()->json([
            'success' => true,
            'date' => $date->format('Y-m-d'),
            'slots' => $result,
            'total_slots' => count($result),
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
            ->where('is_active', true)
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
