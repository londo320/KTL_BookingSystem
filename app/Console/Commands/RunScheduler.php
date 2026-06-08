<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scheduler:run
                            {--daemon : Run as a daemon process}
                            {--interval=60 : Interval in seconds between checks (default 60)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the Laravel scheduler continuously or once';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $daemon = $this->option('daemon');
        $interval = (int) $this->option('interval');

        if ($daemon) {
            $this->info('Starting scheduler daemon...');
            $this->info("Running every {$interval} seconds. Press Ctrl+C to stop.");

            while (true) {
                $this->runScheduler();
                sleep($interval);
            }
        } else {
            $this->runScheduler();
        }

        return 0;
    }

    /**
     * Run the scheduler once
     */
    protected function runScheduler()
    {
        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Running scheduled tasks...');

        $exitCode = $this->call('schedule:run');

        if ($exitCode === 0) {
            $this->info('[' . now()->format('Y-m-d H:i:s') . '] Scheduler completed successfully.');
        } else {
            $this->error('[' . now()->format('Y-m-d H:i:s') . '] Scheduler failed with exit code: ' . $exitCode);
        }
    }
}
