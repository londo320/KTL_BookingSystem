<?php

namespace App\Observers;

use App\Models\Booking;

class BookingObserver
{
    /**
     * Handle the Booking "creating" event.
     */
    public function creating(Booking $booking): void
    {
        // Variance calculations are now handled through PO lines
        // No longer need to calculate booking-level variances
    }

    /**
     * Handle the Booking "updating" event.
     */
    public function updating(Booking $booking): void
    {
        // Variance calculations are now handled through PO lines
        // No longer need to calculate booking-level variances
    }
}
