<?php

namespace App\Console\Commands;

use App\Models\TippingBay;
use Illuminate\Console\Command;

class SyncTippingBayOccupancy extends Command
{
    protected $signature = 'bays:sync-occupancy';
    protected $description = 'Sync tipping bay occupancy status based on active bookings';

    public function handle()
    {
        $bays = TippingBay::all();
        $changedCount = 0;

        foreach ($bays as $bay) {
            $changed = $bay->syncOccupancyStatus();

            if ($changed) {
                $changedCount++;
            }
        }

        if ($changedCount > 0) {
            $this->info("Updated {$changedCount} bay(s) out of {$bays->count()} total");
        }

        return Command::SUCCESS;
    }
}