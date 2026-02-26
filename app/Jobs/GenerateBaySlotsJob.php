<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class GenerateBaySlotsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes max

    protected $depotId;
    protected $days;

    /**
     * Create a new job instance.
     */
    public function __construct(int $depotId, int $days)
    {
        $this->depotId = $depotId;
        $this->days = $days;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting bay slot generation job", [
            'depot_id' => $this->depotId,
            'days' => $this->days,
        ]);

        try {
            Artisan::call('slots:generate-bay', [
                '--depot' => $this->depotId,
                '--days' => $this->days,
            ]);

            $output = Artisan::output();

            Log::info("Bay slot generation completed", [
                'depot_id' => $this->depotId,
                'output' => $output,
            ]);
        } catch (\Exception $e) {
            Log::error("Bay slot generation failed", [
                'depot_id' => $this->depotId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Bay slot generation job failed permanently", [
            'depot_id' => $this->depotId,
            'days' => $this->days,
            'error' => $exception->getMessage(),
        ]);
    }
}
