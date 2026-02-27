<?php

namespace App\Console\Commands;

use App\Models\Slot;
use App\Models\TippingBay;
use Illuminate\Console\Command;

class LockBaysWithoutSlots extends Command
{
    protected $signature = 'bays:lock-without-slots {--depot_id=} {--unlock : Unlock bays instead of locking}';

    protected $description = 'Lock or unlock all bays that do not have scheduled slots (prevents new bookings, allows existing bookings to arrive)';

    public function handle()
    {
        $depotId = $this->option('depot_id');
        $unlock = $this->option('unlock');

        $action = $unlock ? 'Unlocking' : 'Locking';
        $this->info("$action bays without scheduled slots...");

        // Get all bays (optionally filtered by depot)
        $baysQuery = TippingBay::where('is_active', true);

        if ($depotId) {
            $baysQuery->where('depot_id', $depotId);
        }

        $bays = $baysQuery->get();
        $this->info("Found {$bays->count()} active bays to check.");

        $affectedBays = 0;

        foreach ($bays as $bay) {
            // Check if this bay has any future slots
            $hasFutureSlots = Slot::where('tipping_bay_id', $bay->id)
                ->where('start_at', '>=', now())
                ->where('is_blocked', false)
                ->exists();

            if (!$hasFutureSlots && !$unlock) {
                // No future slots and we're locking - mark bay as inactive
                $bay->update(['is_active' => false]);
                $this->line("  ❌ Locked: {$bay->name} (Depot: {$bay->depot->name}) - No future slots");
                $affectedBays++;
            } elseif ($unlock) {
                // Unlocking - reactivate bay
                $bay->update(['is_active' => true]);
                $this->line("  ✅ Unlocked: {$bay->name} (Depot: {$bay->depot->name})");
                $affectedBays++;
            } else {
                $this->line("  ⏭️  Skipped: {$bay->name} - Has future slots");
            }
        }

        $this->info("\n✅ Done! Affected $affectedBays bay(s).");
        $this->line("\nNote: Existing bookings can still arrive and use these bays.");
        $this->line("New bookings will not be able to select inactive bays.");

        return 0;
    }
}
