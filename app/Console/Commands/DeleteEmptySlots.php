<?php

namespace App\Console\Commands;

use App\Models\Depot;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteEmptySlots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slots:delete-empty
                            {--depot= : Depot ID (leave empty for all depots)}
                            {--date= : Specific date (YYYY-MM-DD) to delete slots from}
                            {--from= : Start date (YYYY-MM-DD) for date range}
                            {--to= : End date (YYYY-MM-DD) for date range}
                            {--dry-run : Preview what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete empty slots (slots without bookings) by date or date range';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $depotId = $this->option('depot');
        $date = $this->option('date');
        $from = $this->option('from');
        $to = $this->option('to');
        $dryRun = $this->option('dry-run');

        // Validate input
        if ($date && ($from || $to)) {
            $this->error('Cannot use --date with --from/--to. Choose one approach.');
            return 1;
        }

        if (!$date && !$from && !$to) {
            $this->error('Please specify either --date or --from/--to date range.');
            return 1;
        }

        // Build query
        $query = Slot::whereDoesntHave('occupyingBookings');

        // Filter by depot
        if ($depotId) {
            $depot = Depot::find($depotId);
            if (!$depot) {
                $this->error("Depot ID {$depotId} not found.");
                return 1;
            }
            $query->where('depot_id', $depotId);
            $this->info("Filtering by depot: {$depot->name}");
        } else {
            $this->info("Processing all depots");
        }

        // Filter by date or date range
        if ($date) {
            try {
                $targetDate = Carbon::parse($date);
                $query->whereDate('start_at', $targetDate);
                $this->info("Filtering by date: {$targetDate->format('Y-m-d')}");
            } catch (\Exception $e) {
                $this->error("Invalid date format: {$date}");
                return 1;
            }
        } elseif ($from || $to) {
            if ($from) {
                try {
                    $fromDate = Carbon::parse($from);
                    $query->whereDate('start_at', '>=', $fromDate);
                    $this->info("From date: {$fromDate->format('Y-m-d')}");
                } catch (\Exception $e) {
                    $this->error("Invalid from date format: {$from}");
                    return 1;
                }
            }
            if ($to) {
                try {
                    $toDate = Carbon::parse($to);
                    $query->whereDate('start_at', '<=', $toDate);
                    $this->info("To date: {$toDate->format('Y-m-d')}");
                } catch (\Exception $e) {
                    $this->error("Invalid to date format: {$to}");
                    return 1;
                }
            }
        }

        // Count matching slots
        $count = $query->count();

        if ($count === 0) {
            $this->info('No empty slots found matching the criteria.');
            return 0;
        }

        $this->newLine();
        $this->line("Found {$count} empty slot(s) to delete.");

        if ($dryRun) {
            $this->warn('DRY RUN MODE - Nothing will be deleted');

            // Show breakdown by depot and date
            $slots = $query->with('depot')->get();
            $breakdown = $slots->groupBy(function ($slot) {
                return $slot->depot->name . ' - ' . Carbon::parse($slot->start_at)->format('Y-m-d');
            });

            $this->newLine();
            $this->line('Breakdown:');
            foreach ($breakdown as $key => $groupedSlots) {
                $this->line("  {$key}: {$groupedSlots->count()} slots");
            }

            $this->newLine();
            $this->info("Run without --dry-run to actually delete these slots.");
            return 0;
        }

        // Confirm deletion
        if (!$this->confirm("Are you sure you want to delete {$count} empty slot(s)?")) {
            $this->info('Deletion cancelled.');
            return 0;
        }

        // Delete in chunks to avoid memory issues
        $this->info('Deleting slots...');
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $deletedCount = 0;
        $query->chunk(100, function ($slots) use (&$deletedCount, $bar) {
            foreach ($slots as $slot) {
                $slot->delete();
                $deletedCount++;
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->newLine();
        $this->info("✅ Successfully deleted {$deletedCount} empty slot(s).");

        return 0;
    }
}
