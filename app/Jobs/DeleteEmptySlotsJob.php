<?php

namespace App\Jobs;

use App\Models\Slot;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class DeleteEmptySlotsJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 600; // 10 minutes max

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $depotId,
        public string $date
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting deletion of empty slots for depot {$this->depotId} on date {$this->date}");

        $deletedCount = 0;

        // Delete in chunks to avoid memory issues
        do {
            $deleted = Slot::where('depot_id', $this->depotId)
                ->whereDate('start_at', $this->date)
                ->whereDoesntHave('occupyingBookings')
                ->limit(100)
                ->delete();

            $deletedCount += $deleted;

            // Small pause between chunks
            usleep(10000); // 10ms

        } while ($deleted > 0);

        Log::info("Completed deletion of {$deletedCount} empty slots for depot {$this->depotId} on date {$this->date}");
    }
}
