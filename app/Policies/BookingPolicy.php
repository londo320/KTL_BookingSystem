<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Booking $booking): bool
    {
        // Admin roles can view all bookings
        if ($user->hasRole(['admin', 'depot-admin', 'site-admin'])) {
            return true;
        }

        // Customers can only view bookings they have access to via the pivot table
        if ($user->hasRole('customer')) {
            // If booking has no customer_id, deny access (rogue record)
            if ($booking->customer_id === null) {
                return false;
            }

            return $user->canAccessCustomer($booking->customer_id);
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Booking $booking): bool
    {
        // Admin roles can update all bookings
        if ($user->hasRole(['admin', 'depot-admin', 'site-admin'])) {
            return true;
        }

        // Customers can only update bookings they have access to via the pivot table
        if ($user->hasRole('customer')) {
            // If booking has no customer_id, deny access (rogue record)
            if ($booking->customer_id === null) {
                return false;
            }

            return $user->canAccessCustomer($booking->customer_id);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Booking $booking): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Booking $booking): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Booking $booking): bool
    {
        return false;
    }
}
