<?php

namespace App\Services;

use App\Models\BayCapacityRule;
use App\Models\Booking;
use App\Models\BookingType;
use App\Models\BookingTypeEquipmentRequirement;
use App\Models\CustomerBayAssignment;
use App\Models\CustomerDepotTimeWindow;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SlotAvailabilityService
{
    /**
     * Check if a slot is available for a customer and booking type
     * considering duration, time windows, equipment requirements, and bay capacity rules
     */
    public function isSlotAvailable(
        Slot $slot,
        int $customerId,
        int $bookingTypeId,
        ?int $excludeBookingId = null,
        ?int $caseCount = null
    ): array {
        $errors = [];

        // Get booking type
        $bookingType = BookingType::find($bookingTypeId);
        if (!$bookingType) {
            $errors[] = 'Invalid booking type';
            return ['available' => false, 'errors' => $errors];
        }

        // Get duration for this customer at this depot (with case count if provided)
        $durationMinutes = $caseCount !== null
            ? $bookingType->getDurationWithCaseCount($caseCount, $slot->depot_id, $customerId)
            : $bookingType->getDurationForCustomer($slot->depot_id, $customerId);

        // Check capacity for the primary slot
        if (!$this->checkSlotCapacity($slot, $excludeBookingId)) {
            $errors[] = 'Slot is at full capacity';
        }

        // Check booking type time restrictions (with customer/depot overrides)
        if (!$bookingType->isAvailableAtTime($slot->start_at, $customerId, $slot->depot_id)) {
            $errors[] = 'Booking time is outside allowed time window for this booking type';
        }

        // Check time window restrictions
        if (!CustomerDepotTimeWindow::isTimeAllowed($customerId, $slot->depot_id, $slot->start_at)) {
            $errors[] = 'Booking time is outside allowed time window for this customer';
        }

        // Check if slot is blocked or locked
        if ($slot->is_blocked) {
            $errors[] = 'Slot is blocked';
        }

        // Check if bay is active
        if ($slot->tippingBay && !$slot->tippingBay->is_active) {
            $errors[] = 'This bay is no longer available';
        }

        // Check bay capacity rules (e.g., max 3 handball bookings at once)
        $capacityRuleCheck = $this->checkBayCapacityRules(
            $slot->depot_id,
            $bookingTypeId,
            $slot->start_at,
            $durationMinutes,
            $slot->tipping_bay_id,
            $excludeBookingId
        );
        if (!$capacityRuleCheck['available']) {
            $errors = array_merge($errors, $capacityRuleCheck['errors']);
        }

        // Check extended slots (if booking extends beyond this slot)
        $extendedSlotsNeeded = $this->getExtendedSlotsNeeded($slot, $durationMinutes);
        if (!empty($extendedSlotsNeeded)) {
            foreach ($extendedSlotsNeeded as $extendedSlot) {
                if (!$this->checkSlotCapacity($extendedSlot, $excludeBookingId)) {
                    $errors[] = sprintf(
                        'Extended slot at %s is at full capacity',
                        $extendedSlot->start_at->format('H:i')
                    );
                }
            }
        }

        // Check equipment requirements and bay availability
        $requiredEquipment = BookingTypeEquipmentRequirement::getRequiredEquipment($bookingTypeId);
        if (!empty($requiredEquipment)) {
            $availableBays = CustomerBayAssignment::getAvailableBaysForCustomer(
                $customerId,
                $slot->depot_id,
                $requiredEquipment
            );

            if ($availableBays->isEmpty()) {
                $errors[] = sprintf(
                    'No bays available with required equipment: %s',
                    implode(', ', $requiredEquipment)
                );
            } else {
                // Check if THIS slot's bay is in the list of available bays
                $availableBayIds = $availableBays->pluck('id')->toArray();
                if (!in_array($slot->tipping_bay_id, $availableBayIds)) {
                    $errors[] = sprintf(
                        'This bay does not have required equipment: %s',
                        implode(', ', $requiredEquipment)
                    );
                }
            }
        }

        return [
            'available' => empty($errors),
            'errors' => $errors,
            'duration_minutes' => $durationMinutes,
            'extended_slots' => $extendedSlotsNeeded->pluck('id')->toArray() ?? [],
        ];
    }

    /**
     * Get all available slots for a customer and booking type
     */
    public function getAvailableSlots(
        int $depotId,
        int $customerId,
        int $bookingTypeId,
        ?Carbon $startDate = null,
        ?Carbon $endDate = null
    ): Collection {
        $query = Slot::where('depot_id', $depotId)
            ->where('is_blocked', false)
            ->orderBy('start_at');

        if ($startDate) {
            $query->where('start_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('start_at', '<=', $endDate);
        }

        $slots = $query->get();

        // Filter slots based on availability rules
        return $slots->filter(function ($slot) use ($customerId, $bookingTypeId) {
            $check = $this->isSlotAvailable($slot, $customerId, $bookingTypeId);
            return $check['available'];
        });
    }

    /**
     * Get extended slots needed if booking spans multiple slots
     */
    protected function getExtendedSlotsNeeded(Slot $primarySlot, int $durationMinutes): Collection
    {
        $endTime = $primarySlot->start_at->copy()->addMinutes($durationMinutes);

        // If booking ends within the primary slot, no extended slots needed
        if ($endTime->lessThanOrEqualTo($primarySlot->end_at)) {
            return collect([]);
        }

        // Find all slots between primary slot end and booking end ON THE SAME BAY
        return Slot::where('depot_id', $primarySlot->depot_id)
            ->where('tipping_bay_id', $primarySlot->tipping_bay_id) // CRITICAL: Same bay only!
            ->where('start_at', '>', $primarySlot->start_at)
            ->where('start_at', '<', $endTime)
            ->orderBy('start_at')
            ->get();
    }

    /**
     * Check if a slot has available capacity
     */
    protected function checkSlotCapacity(Slot $slot, ?int $excludeBookingId = null): bool
    {
        $occupiedCount = $slot->occupyingBookings()
            ->when($excludeBookingId, function ($q) use ($excludeBookingId) {
                $q->where('bookings.id', '!=', $excludeBookingId);
            })
            ->count();

        return $occupiedCount < ($slot->capacity ?? 1);
    }

    /**
     * Reserve extended slots for a booking
     */
    public function reserveExtendedSlots(Booking $booking, Slot $primarySlot, int $durationMinutes): void
    {
        $extendedSlots = $this->getExtendedSlotsNeeded($primarySlot, $durationMinutes);

        foreach ($extendedSlots as $slot) {
            $booking->occupiedSlots()->attach($slot->id, [
                'is_primary' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Also attach primary slot
        $booking->occupiedSlots()->attach($primarySlot->id, [
            'is_primary' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Get booking duration for customer and booking type
     */
    public function getBookingDuration(int $bookingTypeId, int $depotId, int $customerId, ?int $caseCount = null): int
    {
        $bookingType = BookingType::find($bookingTypeId);
        if (!$bookingType) {
            return 60; // Default 1 hour
        }

        if ($caseCount !== null) {
            return $bookingType->getDurationWithCaseCount($caseCount, $depotId, $customerId);
        }

        return $bookingType->getDurationForCustomer($depotId, $customerId);
    }

    /**
     * Check bay capacity rules to ensure depot can handle this booking type at this time
     * Example: "Max 3 handball bookings between 08:00-15:00"
     */
    protected function checkBayCapacityRules(
        int $depotId,
        int $bookingTypeId,
        Carbon $startTime,
        int $durationMinutes,
        ?int $bayId = null,
        ?int $excludeBookingId = null
    ): array {
        $errors = [];

        // Get applicable capacity rules for this depot/booking type/time
        $rules = BayCapacityRule::getApplicableRules($depotId, $bookingTypeId, $startTime, $bayId);

        if ($rules->isEmpty()) {
            return ['available' => true, 'errors' => []];
        }

        // Check each rule
        foreach ($rules as $rule) {
            // Calculate end time of this booking
            $endTime = $startTime->copy()->addMinutes($durationMinutes);

            // Count how many bookings of this type will be concurrent during any point of this booking
            $maxConcurrent = $this->getMaxConcurrentBookings(
                $rule,
                $startTime,
                $endTime,
                $excludeBookingId
            );

            // Check if adding this booking would exceed the limit
            $capacityUsed = $maxConcurrent + ($rule->capacity_weight ?? 1.0);

            if ($capacityUsed > $rule->max_concurrent_bookings) {
                $errors[] = sprintf(
                    'Maximum %d concurrent %s bookings allowed between %s-%s (currently %d)',
                    $rule->max_concurrent_bookings,
                    $rule->bookingType->name ?? 'bookings',
                    $rule->time_start,
                    $rule->time_end,
                    $maxConcurrent
                );
            }
        }

        return [
            'available' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Get maximum concurrent bookings for a capacity rule during a time range
     */
    protected function getMaxConcurrentBookings(
        BayCapacityRule $rule,
        Carbon $startTime,
        Carbon $endTime,
        ?int $excludeBookingId = null
    ): int {
        $applicableBayIds = $rule->applicable_bay_ids ?? \App\Models\TippingBay::where('depot_id', $rule->depot_id)
            ->pluck('id')
            ->toArray();

        // Find all bookings that overlap with this time range
        $query = Booking::whereHas('slot', function ($q) use ($applicableBayIds, $rule) {
                $q->where('depot_id', $rule->depot_id)
                  ->whereIn('tipping_bay_id', $applicableBayIds);
            })
            ->when($rule->booking_type_id, function ($q) use ($rule) {
                $q->where('booking_type_id', $rule->booking_type_id);
            })
            ->when($excludeBookingId, function ($q) use ($excludeBookingId) {
                $q->where('id', '!=', $excludeBookingId);
            });

        // Get bookings that overlap with our time range
        $bookings = $query->get()->filter(function ($booking) use ($startTime, $endTime) {
            if (!$booking->slot) {
                return false;
            }

            $bookingStart = $booking->slot->start_at;
            $bookingEnd = $booking->slot->start_at->copy()->addMinutes($booking->duration_minutes ?? 60);

            // Check if time ranges overlap
            return $bookingStart < $endTime && $bookingEnd > $startTime;
        });

        // Apply capacity weight
        $totalCapacity = 0;
        foreach ($bookings as $booking) {
            $totalCapacity += $rule->capacity_weight ?? 1.0;
        }

        return (int) ceil($totalCapacity);
    }
}
