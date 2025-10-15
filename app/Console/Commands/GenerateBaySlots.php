<?php

namespace App\Console\Commands;

use App\Models\Depot;
use App\Models\Slot;
use App\Models\SlotReleaseRule;
use App\Models\TippingBay;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateBaySlots extends Command
{
    protected $signature = 'slots:generate-bay {--depot= : Depot ID to generate slots for} {--days=7 : How many days ahead to generate} {--hours=24 : How many hourly slots per day}';

    protected $description = 'Generate hourly slots per bay (new bay-based system)';

    public function handle()
    {
        $days = (int) $this->option('days');
        $hours = (int) $this->option('hours');
        $depotId = $this->option('depot');

        $this->info("🚪 Generating bay-based slots for next {$days} day(s) with {$hours} hourly slots per day...");

        // Get depots
        $depots = $depotId
            ? Depot::where('id', $depotId)->get()
            : Depot::all();

        if ($depots->isEmpty()) {
            $this->warn('No depots found.');
            return 0;
        }

        $created = 0;
        $skipped = 0;
        $today = Carbon::today();

        foreach ($depots as $depot) {
            $this->line("\n📦 Processing Depot: {$depot->name}");

            // Get all active bays for this depot
            $bays = TippingBay::where('depot_id', $depot->id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            if ($bays->isEmpty()) {
                $this->warn("  ⚠️  No active bays found for {$depot->name}");
                continue;
            }

            $this->line("  Found {$bays->count()} active bay(s)");

            // For each bay, create slots
            foreach ($bays as $bay) {
                $this->line("    🚪 Bay: {$bay->name}");

                // Generate slots for each day
                for ($day = 0; $day < $days; $day++) {
                    $date = $today->copy()->addDays($day);

                    // Generate hourly slots (e.g., 00:00, 01:00, 02:00... up to 23:00)
                    for ($hour = 0; $hour < $hours; $hour++) {
                        $startTime = $date->copy()->setTime($hour, 0, 0);
                        $endTime = $startTime->copy()->addHour();

                        // Check if slot already exists
                        $exists = Slot::where('depot_id', $depot->id)
                            ->where('tipping_bay_id', $bay->id)
                            ->where('start_at', $startTime)
                            ->exists();

                        if ($exists) {
                            $skipped++;
                            continue;
                        }

                        // Create the slot
                        $slot = Slot::create([
                            'depot_id' => $depot->id,
                            'tipping_bay_id' => $bay->id,
                            'start_at' => $startTime,
                            'end_at' => $endTime,
                            'capacity' => 1, // Default: 1 booking per bay per hour
                            'is_blocked' => false,
                        ]);

                        // Apply release rules
                        $rule = SlotReleaseRule::with('customers')
                            ->where('depot_id', $depot->id)
                            ->orderByDesc('priority')
                            ->first();

                        if ($rule) {
                            $customerIds = $rule->customers->pluck('id')->toArray();

                            if (!empty($customerIds)) {
                                $slot->allowed_customers()->syncWithoutDetaching($customerIds);
                            } else {
                                $slot->released_at = now();
                            }
                        } else {
                            $slot->released_at = now();
                        }

                        $slot->save();
                        $created++;
                    }
                }
            }
        }

        $this->info("\n✅ Done! Created {$created} slot(s), Skipped {$skipped} existing slot(s).");

        return 0;
    }
}
