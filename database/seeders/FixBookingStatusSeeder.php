<?php

namespace Database\Seeders;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FixBookingStatusSeeder extends Seeder
{
    public function run(): void
    {
        echo "Fixing booking status inconsistencies...\n";

        // Get all bookings that are marked as completed but missing arrival/departure data
        $inconsistentBookings = Booking::where('status', 'completed')
            ->where(function ($q) {
                $q->whereNull('arrived_at')
                    ->orWhereNull('departed_at');
            })
            ->get();

        echo "Found {$inconsistentBookings->count()} bookings with inconsistent status\n";

        foreach ($inconsistentBookings as $booking) {
            $slotStart = Carbon::parse($booking->slot->start_at);
            $now = Carbon::now();

            // Determine appropriate status based on slot timing and current status
            if ($slotStart->isFuture()) {
                // Future slots should be confirmed or pending
                $booking->status = 'confirmed';
                $booking->arrived_at = null;
                $booking->departed_at = null;
            } elseif ($slotStart->isPast()) {
                // Past slots - create realistic workflow progression
                $minutesFromSlot = $slotStart->diffInMinutes($now);

                if ($minutesFromSlot > 2880) { // More than 2 days ago
                    // Old slots - mark as fully completed with realistic times
                    $arrivalTime = $slotStart->copy()->addMinutes(rand(-30, 60));
                    $departureTime = $arrivalTime->copy()->addMinutes(rand(90, 240));

                    $booking->update([
                        'status' => 'completed',
                        'arrived_at' => $arrivalTime,
                        'departed_at' => $departureTime,
                        'actual_cases' => $booking->expected_cases ? rand($booking->expected_cases - 10, $booking->expected_cases + 10) : rand(50, 200),
                        'actual_pallets' => $booking->expected_pallets ? rand($booking->expected_pallets - 2, $booking->expected_pallets + 2) : rand(5, 15),
                        'tipping_status' => 'trailer_departed',
                    ]);

                } elseif ($minutesFromSlot > 1440) { // 1-2 days ago
                    // Recent past - some completed, some in progress
                    if (rand(1, 100) <= 70) { // 70% completed
                        $arrivalTime = $slotStart->copy()->addMinutes(rand(-30, 60));
                        $departureTime = $arrivalTime->copy()->addMinutes(rand(90, 240));

                        $booking->update([
                            'status' => 'completed',
                            'arrived_at' => $arrivalTime,
                            'departed_at' => $departureTime,
                            'actual_cases' => $booking->expected_cases ? rand($booking->expected_cases - 10, $booking->expected_cases + 10) : rand(50, 200),
                            'actual_pallets' => $booking->expected_pallets ? rand($booking->expected_pallets - 2, $booking->expected_pallets + 2) : rand(5, 15),
                            'tipping_status' => 'trailer_departed',
                        ]);
                    } else { // 30% still in progress
                        $arrivalTime = $slotStart->copy()->addMinutes(rand(-30, 60));

                        $booking->update([
                            'status' => 'in_progress',
                            'arrived_at' => $arrivalTime,
                            'departed_at' => null,
                            'tipping_status' => collect(['moved_to_bay', 'tipping_in_progress', 'tipping_completed'])->random(),
                        ]);
                    }
                } else { // Less than 24 hours ago
                    // Very recent - mostly in progress or confirmed
                    if (rand(1, 100) <= 40) { // 40% arrived and in progress
                        $arrivalTime = $slotStart->copy()->addMinutes(rand(-30, 60));

                        $booking->update([
                            'status' => 'in_progress',
                            'arrived_at' => $arrivalTime,
                            'departed_at' => null,
                            'tipping_status' => collect(['trailer_dropped', 'moved_to_bay', 'tipping_in_progress'])->random(),
                        ]);
                    } else { // 60% still confirmed (not arrived)
                        $booking->update([
                            'status' => 'confirmed',
                            'arrived_at' => null,
                            'departed_at' => null,
                            'tipping_status' => 'not_started',
                        ]);
                    }
                }
            } else { // Current time slots
                $booking->update([
                    'status' => 'confirmed',
                    'arrived_at' => null,
                    'departed_at' => null,
                    'tipping_status' => 'not_started',
                ]);
            }
        }

        echo "Status fixes completed!\n";
        echo "Updated booking status distribution:\n";

        $statuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];
        foreach ($statuses as $status) {
            $count = Booking::where('status', $status)->count();
            $arrivedCount = Booking::where('status', $status)->whereNotNull('arrived_at')->count();
            $departedCount = Booking::where('status', $status)->whereNotNull('departed_at')->count();

            echo "  {$status}: {$count} total";
            if ($arrivedCount > 0) {
                echo ", {$arrivedCount} arrived";
            }
            if ($departedCount > 0) {
                echo ", {$departedCount} departed";
            }
            echo "\n";
        }

        // Validate the fixes
        echo "\nValidation:\n";
        $invalidCompleted = Booking::where('status', 'completed')
            ->where(function ($q) {
                $q->whereNull('arrived_at')
                    ->orWhereNull('departed_at');
            })
            ->count();

        echo "Bookings marked completed without full workflow: {$invalidCompleted}\n";

        if ($invalidCompleted == 0) {
            echo "✅ All completed bookings now have proper arrival and departure times!\n";
        } else {
            echo "⚠️  Some bookings still need manual review\n";
        }
    }
}
