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

        $depots = Depot::all();

        foreach ($depots as $depot) {
            $bays = TippingBay::where('depot_id', $depot->id)
                ->where('is_active', true)
                ->get();

            if ($bays->isEmpty()) {
                continue;
            }

            foreach ($bays as $bay) {
                if (!$bay->operational_start || !$bay->operational_end) {
                    continue;
                }

                $operationalStart = Carbon::parse($bay->operational_start);
                $operationalEnd = Carbon::parse($bay->operational_end);
                $operationalDays = $bay->operational_days ?? [1, 2, 3, 4, 5];

                for ($dayOffset = 0; $dayOffset < $daysToGenerate; $dayOffset++) {
                    $targetDate = $today->copy()->addDays($dayOffset);
                    $dayOfWeek = $targetDate->dayOfWeek;

                    if (!in_array($dayOfWeek, $operationalDays)) {
                        continue;
                    }

                    $currentTime = $targetDate->copy()->setTimeFromTimeString($operationalStart->format('H:i'));
                    $endTime = $targetDate->copy()->setTimeFromTimeString($operationalEnd->format('H:i'));

                    while ($currentTime->lessThan($endTime)) {
                        $slotStart = $currentTime->copy();
                        $slotEnd = $currentTime->copy()->addHour();

                        if (!Slot::where('depot_id', $depot->id)
                            ->where('tipping_bay_id', $bay->id)
                            ->where('start_at', $slotStart)
                            ->exists()) {

                            Slot::create([
                                'depot_id' => $depot->id,
                                'tipping_bay_id' => $bay->id,
                                'booking_type_id' => null,
                                'start_at' => $slotStart,
                                'end_at' => $slotEnd,
                                'capacity' => 1,
                                'is_blocked' => false,
                            ]);

                            $slotsCreated++;
                        }

                        $currentTime->addHour();
                    }
                }
            }
        }

        if ($slotsCreated > 0) {
            $this->info("Created {$slotsCreated} new slots for next {$daysToGenerate} days");
        }

        return 0;
    }
}
