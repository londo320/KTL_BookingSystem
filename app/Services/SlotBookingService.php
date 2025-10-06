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
     * @return array ['can_book' => bool, 'slots' => Collection, 'message' => string]
     */
    public function checkAvailability(Slot $primarySlot, int $bookingTypeId)
    {
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
            if (!$slot->hasCapacity()) {
                return [
                    'can_book' => false,
                    'slots' => collect(),
                    'message' => "Slot at {$slot->start_at->format('H:i')} is fully booked. Capacity: {$slot->capacity}, Currently occupied: " . $slot->occupyingBookings()->count()
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
        // Check availability first
        $availability = $this->checkAvailability($primarySlot, $bookingTypeId);

        if (!$availability['can_book']) {
            throw new \Exception($availability['message']);
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

            // Check new slot availability
            $availability = $this->checkAvailability($targetSlot, $targetTypeId);

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
