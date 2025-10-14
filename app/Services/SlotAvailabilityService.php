<?php

namespace App\Services;

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
     * considering duration, time windows, and equipment requirements
     */
    public function isSlotAvailable(
        Slot $slot,
        int $customerId,
        int $bookingTypeId,
        ?int $excludeBookingId = null
    ): array {
        $errors = [];

        // Get booking type
        $bookingType = BookingType::find($bookingTypeId);
        if (!$bookingType) {
            $errors[] = 'Invalid booking type';
            return ['available' => false, 'errors' => $errors];
        }

        // Get duration for this customer at this depot
        $durationMinutes = $bookingType->getDurationForCustomer($slot->depot_id, $customerId);

        // Check capacity for the primary slot
        if (!$this->checkSlotCapacity($slot, $excludeBookingId)) {
            $errors[] = 'Slot is at full capacity';
        }

        // Check time window restrictions
        if (!CustomerDepotTimeWindow::isTimeAllowed($customerId, $slot->depot_id, $slot->start_at)) {
            $errors[] = 'Booking time is outside allowed time window for this customer';
        }

        // Check if slot is blocked or locked
        if ($slot->is_blocked) {
            $errors[] = 'Slot is blocked';
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

        // Find all slots between primary slot end and booking end
        return Slot::where('depot_id', $primarySlot->depot_id)
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
    public function getBookingDuration(int $bookingTypeId, int $depotId, int $customerId): int
    {
        $bookingType = BookingType::find($bookingTypeId);
        if (!$bookingType) {
            return 60; // Default 1 hour
        }

        return $bookingType->getDurationForCustomer($depotId, $customerId);
    }
}
