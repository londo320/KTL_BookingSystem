<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Outbound\Services\OrderMatchingService;

class ProcessWmsOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'outbound:process-wms-orders {--dry-run : Show what would be processed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending WMS staging orders and match them to physical loads';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing WMS staging orders...');

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        $matchingService = new OrderMatchingService();
        
        if ($this->option('dry-run')) {
            // Show what would be processed
            $pending = \App\Modules\Outbound\Models\WmsStagingOrder::pending()->count();
            $this->info("Found {$pending} pending orders to process");
            return Command::SUCCESS;
        }

        $results = $matchingService->processPendingOrders();

        $this->info("Processing completed:");
        $this->line("- Processed: {$results['processed']} orders");
        $this->line("- Matched: {$results['matched']} orders");
        $this->line("- Failed: {$results['failed']} orders");

        if (!empty($results['errors'])) {
            $this->error("Errors encountered:");
            foreach ($results['errors'] as $error) {
                $this->line("  - {$error}");
            }
        }

        return Command::SUCCESS;
    }
}