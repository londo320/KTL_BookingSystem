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
        $this->info('Syncing tipping bay occupancy statuses...');
        
        $bays = TippingBay::all();
        $changedCount = 0;
        
        foreach ($bays as $bay) {
            $wasOccupied = $bay->is_occupied;
            $changed = $bay->syncOccupancyStatus();
            
            if ($changed) {
                $changedCount++;
                $status = $bay->is_occupied ? 'OCCUPIED' : 'AVAILABLE';
                $this->line("• {$bay->name} ({$bay->code}): Changed to {$status}");
            }
        }
        
        if ($changedCount > 0) {
            $this->info("✅ Updated {$changedCount} bay(s) out of {$bays->count()} total");
        } else {
            $this->info("✅ All {$bays->count()} bays already have correct occupancy status");
        }
        
        return Command::SUCCESS;
    }
}