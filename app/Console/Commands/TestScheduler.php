<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestScheduler extends Command
{
    protected $signature = 'test:scheduler';

    protected $description = 'Test that the scheduler is working';

    public function handle()
    {
        $this->info('✅ Scheduler test command executed successfully at '.now()->format('Y-m-d H:i:s'));

        // Log to file as well
        \Log::info('Test scheduler command executed at '.now()->format('Y-m-d H:i:s'));

        return 0;
    }
}
