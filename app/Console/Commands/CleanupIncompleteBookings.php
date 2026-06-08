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

        // Find bookings that:
        // 1. Were created more than X minutes ago
        // 2. Have no PO numbers attached
        // 3. Have not arrived yet (don't delete active bookings)
        $incompleteBookings = Booking::whereDoesntHave('poNumbers')
            ->where('created_at', '<', $cutoffTime)
            ->whereNull('arrived_at')
            ->get();

        if ($incompleteBookings->isEmpty()) {
            return 0;
        }

        foreach ($incompleteBookings as $booking) {
            try {
                // Release occupied slots before deleting
                $booking->occupiedSlots()->detach();
                $booking->delete();
            } catch (\Exception $e) {
                // Continue even if one booking fails to delete
                continue;
            }
        }

        $this->info("Deleted {$incompleteBookings->count()} incomplete bookings");

        return 0;
    }
}
