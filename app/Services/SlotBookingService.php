<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingType;
use App\Models\Slot;
use Carbon\Carbon;

class SlotBookingService
{
    /**
     * Check if a booking can be made and return affected slots
     *
     * @param Slot $primarySlot The slot the customer is trying to book
     * @param int $bookingTypeId The booking type ID
     * @param int|null $customerId The customer ID (optional, for customer-specific durations)
     * @param int|null $excludeBookingId Exclude this booking ID when checking capacity (for updates)
     * @return array ['can_book' => bool, 'slots' => Collection, 'message' => string]
     */
    public function checkAvailability(Slot $primarySlot, int $bookingTypeId, ?int $customerId = null, ?int $excludeBookingId = null)
    {
        // Use SlotAvailabilityService for comprehensive checks
        if ($customerId) {
            $availabilityService = app(SlotAvailabilityService::class);
            $availabilityCheck = $availabilityService->isSlotAvailable(
                $primarySlot,
                $customerId,
                $bookingTypeId,
                $excludeBookingId
            );

            if (!$availabilityCheck['available']) {
                return [
                    'can_book' => false,
                    'slots' => collect(),
                    'message' => implode(' ', $availabilityCheck['errors'])
                ];
            }

            // Get extended slots from availability check
            $extendedSlotIds = $availabilityCheck['extended_slots'] ?? [];
            $extendedSlots = !empty($extendedSlotIds)
                ? Slot::whereIn('id', $extendedSlotIds)->orderBy('start_at')->get()
                : collect();

            $allSlots = collect([$primarySlot])->merge($extendedSlots);

            return [
                'can_book' => true,
                'slots' => $allSlots,
                'message' => "Available! This booking will occupy " . $allSlots->count() . " slot(s)."
            ];
        }

        // Fallback to legacy logic if no customer ID provided
        $bookingType = BookingType::findOrFail($bookingTypeId);
        $depotId = $primarySlot->depot_id;

        // Get duration for this booking type at this depot (in minutes)
        $durationMinutes = $bookingType->getDurationForDepot($depotId);

        // Calculate how many slots we need
        $slotDurationMinutes = $primarySlot->start_at->diffInMinutes($primarySlot->end_at);
        $slotsNeeded = ceil($durationMinutes / $slotDurationMinutes);

        // Find all consecutive slots needed
        $slots = collect([$primarySlot]);
        $currentSlotEnd = $primarySlot->end_at;

        for ($i = 1; $i < $slotsNeeded; $i++) {
            $nextSlot = Slot::where('depot_id', $depotId)
                ->where('start_at', $currentSlotEnd)
                ->whereNotNull('released_at')
                ->where('released_at', '<=', now())
                ->first();

            if (!$nextSlot) {
                return [
                    'can_book' => false,
                    'slots' => collect(),
                    'message' => "Not enough consecutive slots available. {$bookingType->name} requires {$slotsNeeded} hour(s)."
                ];
            }

            $slots->push($nextSlot);
            $currentSlotEnd = $nextSlot->end_at;
        }

        // Check if all slots have capacity
        foreach ($slots as $slot) {
            // Count occupying bookings, excluding the current booking if this is an update
            $occupiedCount = $slot->occupyingBookings()
                ->when($excludeBookingId, function ($query) use ($excludeBookingId) {
                    return $query->where('bookings.id', '!=', $excludeBookingId);
                })
                ->count();

            $hasCapacity = $occupiedCount < $slot->capacity;

            if (!$hasCapacity) {
                return [
                    'can_book' => false,
                    'slots' => collect(),
                    'message' => "Slot at {$slot->start_at->format('H:i')} is fully booked. Capacity: {$slot->capacity}, Currently occupied: {$occupiedCount}"
                ];
            }
        }

        return [
            'can_book' => true,
            'slots' => $slots,
            'message' => "Available! This booking will occupy {$slotsNeeded} slot(s)."
        ];
    }

    /**
     * Create a booking and occupy all required slots
     *
     * @param array $bookingData The booking data
     * @param Slot $primarySlot The primary slot
     * @param int $bookingTypeId The booking type ID
     * @return Booking
     */
    public function createBooking(array $bookingData, Slot $primarySlot, int $bookingTypeId)
    {
        // Check availability first (pass customer_id if available in booking data)
        $customerId = $bookingData['customer_id'] ?? null;
        $availability = $this->checkAvailability($primarySlot, $bookingTypeId, $customerId);

        if (!$availability['can_book']) {
            throw new \Exception($availability['message']);
        }

        // Auto-assign bay if needed (when slot doesn't have a specific bay)
        if (!isset($bookingData['tipping_bay_id']) || $bookingData['tipping_bay_id'] === null) {
            $assignedBay = $this->autoAssignBay($primarySlot, $bookingTypeId, $customerId, $availability['slots']);
            if ($assignedBay) {
                $bookingData['tipping_bay_id'] = $assignedBay->id;
            }
        }

        // Create the booking
        $booking = Booking::create($bookingData);

        // Occupy all required slots
        foreach ($availability['slots'] as $index => $slot) {
            $booking->occupiedSlots()->attach($slot->id, [
                'is_primary' => ($index === 0), // First slot is primary
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $booking;
    }

    /**
     * Auto-assign a bay for a booking based on equipment requirements and availability
     *
     * @param Slot $primarySlot
     * @param int $bookingTypeId
     * @param int|null $customerId
     * @param Collection $slotsToOccupy All slots that will be occupied by this booking
     * @return \App\Models\TippingBay|null
     */
    protected function autoAssignBay(Slot $primarySlot, int $bookingTypeId, ?int $customerId, $slotsToOccupy)
    {
        // Get required equipment for this booking type
        $requiredEquipment = \App\Models\BookingTypeEquipmentRequirement::getRequiredEquipment($bookingTypeId);

        // Get available bays for this customer with required equipment
        $availableBays = \App\Models\CustomerBayAssignment::getAvailableBaysForCustomer(
            $customerId,
            $primarySlot->depot_id,
            $requiredEquipment
        );

        if ($availableBays->isEmpty()) {
            return null;
        }

        // Find a bay that is available for ALL the slots we need to occupy
        foreach ($availableBays as $bay) {
            $bayAvailableForAllSlots = true;

            foreach ($slotsToOccupy as $slot) {
                // Check if this bay has capacity during this slot time
                $conflictingBookings = \App\Models\Booking::where('tipping_bay_id', $bay->id)
                    ->whereHas('occupiedSlots', function ($q) use ($slot) {
                        $q->where('slots.id', $slot->id);
                    })
                    ->count();

                if ($conflictingBookings > 0) {
                    $bayAvailableForAllSlots = false;
                    break;
                }
            }

            if ($bayAvailableForAllSlots) {
                return $bay;
            }
        }

        return null;
    }

    /**
     * Update a booking and re-occupy slots if slot changed
     *
     * @param Booking $booking
     * @param array $updateData
     * @param Slot|null $newSlot
     * @param int|null $newBookingTypeId
     * @return Booking
     */
    public function updateBooking(Booking $booking, array $updateData, ?Slot $newSlot = null, ?int $newBookingTypeId = null)
    {
        $slotChanged = $newSlot && $newSlot->id !== $booking->slot_id;
        $typeChanged = $newBookingTypeId && $newBookingTypeId !== $booking->booking_type_id;

        if ($slotChanged || $typeChanged) {
            $targetSlot = $newSlot ?? $booking->slot;
            $targetTypeId = $newBookingTypeId ?? $booking->booking_type_id;

            // Check new slot availability (use customer_id from booking)
            // Exclude current booking from capacity check since we're updating it
            $customerId = $updateData['customer_id'] ?? $booking->customer_id;
            $availability = $this->checkAvailability($targetSlot, $targetTypeId, $customerId, $booking->id);

            if (!$availability['can_book']) {
                throw new \Exception($availability['message']);
            }

            // Release old slots
            $booking->occupiedSlots()->detach();

            // Occupy new slots
            foreach ($availability['slots'] as $index => $slot) {
                $booking->occupiedSlots()->attach($slot->id, [
                    'is_primary' => ($index === 0),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Update booking
        $booking->update($updateData);

        return $booking->fresh();
    }

    /**
     * Cancel/delete a booking and release all occupied slots
     *
     * @param Booking $booking
     * @return bool
     */
    public function cancelBooking(Booking $booking)
    {
        // Release all occupied slots
        $booking->occupiedSlots()->detach();

        // Soft delete the booking
        return $booking->delete();
    }
}
