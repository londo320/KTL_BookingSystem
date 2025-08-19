<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * Note: In Laravel 12, scheduling is now handled in routes/console.php
     */
    protected function schedule(Schedule $schedule): void
    {
        // Scheduling has been moved to routes/console.php in Laravel 12
        // This method is kept for compatibility but should remain empty
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
