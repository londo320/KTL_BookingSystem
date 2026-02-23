<?php

namespace App\Console\Commands;

use App\Models\Depot;
use App\Models\Slot;
use App\Models\TippingBay;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateBaySpecificSlots extends Command
{
    protected $signature = 'slots:generate-by-bay {--days=14 : Number of days to generate slots for}';

    protected $description = 'Generate bay-specific slots based on each bay\'s operating hours';

    public function handle()
    {
        $daysToGenerate = (int) $this->option('days');
        $today = Carbon::today();
        $slotsCreated = 0;

        $this->info("Generating bay-specific slots for next {$daysToGenerate} days...");

        // Get all active depots
        $depots = Depot::all();

        foreach ($depots as $depot) {
            $this->line("Processing depot: {$depot->name}");

            // Get all active bays for this depot
            $bays = TippingBay::where('depot_id', $depot->id)
                ->where('is_active', true)
                ->get();

            if ($bays->isEmpty()) {
                $this->warn("  No active bays found. Skipping.");
                continue;
            }

            foreach ($bays as $bay) {
                $this->line("  Processing bay: {$bay->name}");

                // Skip if bay doesn't have operating hours configured
                if (!$bay->operational_start || !$bay->operational_end) {
                    $this->warn("    No operating hours configured. Skipping.");
                    continue;
                }

                // Parse operating hours
                $operationalStart = Carbon::parse($bay->operational_start);
                $operationalEnd = Carbon::parse($bay->operational_end);
                $operationalDays = $bay->operational_days ?? [1, 2, 3, 4, 5]; // Default Mon-Fri

                // For each day in the range
                for ($dayOffset = 0; $dayOffset < $daysToGenerate; $dayOffset++) {
                    $targetDate = $today->copy()->addDays($dayOffset);
                    $dayOfWeek = $targetDate->dayOfWeek; // 0 (Sunday) to 6 (Saturday)

                    // Skip if bay doesn't operate on this day
                    if (!in_array($dayOfWeek, $operationalDays)) {
                        continue;
                    }

                    // Generate hourly slots during operating hours
                    $currentTime = $targetDate->copy()->setTimeFromTimeString($operationalStart->format('H:i'));
                    $endTime = $targetDate->copy()->setTimeFromTimeString($operationalEnd->format('H:i'));

                    while ($currentTime->lessThan($endTime)) {
                        $slotStart = $currentTime->copy();
                        $slotEnd = $currentTime->copy()->addHour();

                        // Check if slot already exists
                        if (!Slot::where('depot_id', $depot->id)
                            ->where('tipping_bay_id', $bay->id)
                            ->where('start_at', $slotStart)
                            ->exists()) {

                            Slot::create([
                                'depot_id' => $depot->id,
                                'tipping_bay_id' => $bay->id, // BAY-SPECIFIC
                                'booking_type_id' => null, // Any booking type
                                'start_at' => $slotStart,
                                'end_at' => $slotEnd,
                                'capacity' => 1, // One booking per bay per slot
                                'is_blocked' => false,
                            ]);

                            $slotsCreated++;
                        }

                        $currentTime->addHour();
                    }
                }

                $this->info("    ✅ Bay {$bay->name} processed");
            }
        }

        $this->info("✅ Created {$slotsCreated} new bay-specific slots for next {$daysToGenerate} days");
        return 0;
    }
}
