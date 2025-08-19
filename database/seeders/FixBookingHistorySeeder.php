<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingHistory;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FixBookingHistorySeeder extends Seeder
{
    public function run(): void
    {
        echo "Fixing missing booking history entries...\n";

        // Find cancelled bookings that are missing cancellation history
        $cancelledBookings = Booking::where('status', 'cancelled')
            ->whereNotNull('cancelled_at')
            ->get();

        echo "Found {$cancelledBookings->count()} cancelled bookings to check\n";

        $fixed = 0;
        foreach ($cancelledBookings as $booking) {
            // Check if this booking already has a cancellation history entry
            $hasCancel = BookingHistory::where('booking_id', $booking->id)
                ->where('action', 'cancelled')
                ->exists();

            if (! $hasCancel) {
                echo "  Adding cancellation history for booking {$booking->id}\n";

                // Calculate timing information
                $slotStart = Carbon::parse($booking->slot->start_at);
                $cancelledAt = Carbon::parse($booking->cancelled_at);
                $hoursBeforeSlot = $cancelledAt->diffInHours($slotStart, false);

                // Get customer behavior data
                $recentRebooks = BookingHistory::where('customer_id', $booking->customer_id)
                    ->where('action', 'rebooked')
                    ->where('created_at', '>=', $cancelledAt->copy()->subDays(30))
                    ->count();

                $recentCancels = BookingHistory::where('customer_id', $booking->customer_id)
                    ->where('action', 'cancelled')
                    ->where('created_at', '>=', $cancelledAt->copy()->subDays(30))
                    ->count();

                BookingHistory::create([
                    'booking_id' => $booking->id,
                    'customer_id' => $booking->customer_id,
                    'user_id' => $booking->cancelled_by ?: $booking->user_id,
                    'original_slot_id' => $booking->slot->id,
                    'original_start_time' => $booking->slot->start_at,
                    'original_end_time' => $booking->slot->end_at,
                    'action' => 'cancelled',
                    'reason' => $booking->cancellation_reason ?: 'No reason provided',
                    'hours_before_slot' => $hoursBeforeSlot,
                    'is_last_minute' => abs($hoursBeforeSlot) < 24,
                    'customer_rebook_count_30days' => $recentRebooks,
                    'customer_cancel_count_30days' => $recentCancels,
                    'created_at' => $booking->cancelled_at,
                    'updated_at' => $booking->cancelled_at,
                ]);

                $fixed++;
            }
        }

        // Also check for bookings that arrived/departed but have no history
        echo "\nChecking for missing arrival/departure history...\n";

        $arrivedBookings = Booking::whereNotNull('arrived_at')
            ->get();

        foreach ($arrivedBookings as $booking) {
            $hasArrival = BookingHistory::where('booking_id', $booking->id)
                ->where('action', 'modified')
                ->where('reason', 'LIKE', '%arrived%')
                ->exists();

            if (! $hasArrival) {
                echo "  Adding arrival history for booking {$booking->id}\n";

                BookingHistory::create([
                    'booking_id' => $booking->id,
                    'customer_id' => $booking->customer_id,
                    'user_id' => $booking->user_id,
                    'action' => 'modified',
                    'reason' => 'Vehicle arrived on site - status changed to in_progress',
                    'created_at' => $booking->arrived_at,
                    'updated_at' => $booking->arrived_at,
                ]);

                $fixed++;
            }
        }

        $departedBookings = Booking::whereNotNull('departed_at')
            ->get();

        foreach ($departedBookings as $booking) {
            $hasDeparture = BookingHistory::where('booking_id', $booking->id)
                ->where('action', 'completed')
                ->exists();

            if (! $hasDeparture) {
                echo "  Adding departure/completion history for booking {$booking->id}\n";

                BookingHistory::create([
                    'booking_id' => $booking->id,
                    'customer_id' => $booking->customer_id,
                    'user_id' => $booking->user_id,
                    'action' => 'completed',
                    'reason' => 'Vehicle departed - booking completed',
                    'created_at' => $booking->departed_at,
                    'updated_at' => $booking->departed_at,
                ]);

                $fixed++;
            }
        }

        echo "\n✅ Fixed {$fixed} missing history entries\n";

        // Verification
        echo "\nVerification:\n";
        $totalBookings = Booking::count();
        $bookingsWithHistory = Booking::whereHas('history')->count();
        $cancelledWithHistory = Booking::where('status', 'cancelled')
            ->whereHas('history', function ($q) {
                $q->where('action', 'cancelled');
            })->count();

        echo "  Total bookings: {$totalBookings}\n";
        echo "  Bookings with history: {$bookingsWithHistory}\n";
        echo "  Cancelled bookings with cancellation history: {$cancelledWithHistory}\n";

        if ($bookingsWithHistory == $totalBookings) {
            echo "✅ All bookings now have history entries!\n";
        }
    }
}
