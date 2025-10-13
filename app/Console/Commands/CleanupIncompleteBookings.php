<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CleanupIncompleteBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:cleanup-incomplete {--minutes=30 : Minutes after creation to delete bookings without PO details}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete bookings created more than X minutes ago that have no PO number details';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $minutes = $this->option('minutes');
        $cutoffTime = Carbon::now()->subMinutes($minutes);

        $this->info("Finding bookings created before {$cutoffTime->toDateTimeString()} with no PO details...");

        // Find bookings that:
        // 1. Were created more than X minutes ago
        // 2. Have no PO numbers attached
        // 3. Have not arrived yet (don't delete active bookings)
        $incompleteBookings = Booking::whereDoesntHave('poNumbers')
            ->where('created_at', '<', $cutoffTime)
            ->whereNull('arrived_at')
            ->get();

        if ($incompleteBookings->isEmpty()) {
            $this->info('No incomplete bookings found to delete.');
            return 0;
        }

        $this->info("Found {$incompleteBookings->count()} incomplete bookings to delete:");

        $deletedCount = 0;

        foreach ($incompleteBookings as $booking) {
            $this->line("  - Booking #{$booking->id} (Customer: {$booking->customer->name}, Created: {$booking->created_at->diffForHumans()})");

            // Release occupied slots before deleting
            $booking->occupiedSlots()->detach();

            // Delete the booking
            $booking->delete();
            $deletedCount++;
        }

        $this->info("Successfully deleted {$deletedCount} incomplete bookings.");

        return 0;
    }
}
