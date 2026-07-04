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

        $this->info("🚪 Generating bay-specific slots for next {$daysToGenerate} days...");
        $this->newLine();

        $depots = Depot::all();

        foreach ($depots as $depot) {
            $bays = TippingBay::where('depot_id', $depot->id)
                ->where('is_active', true)
                ->get();

            if ($bays->isEmpty()) {
                $this->warn("Depot {$depot->name}: No active bays found");
                continue;
            }

            $this->line("📍 Depot: {$depot->name} ({$bays->count()} active bays)");

            foreach ($bays as $bay) {
                // Load per-day schedules
                $bay->load('schedules');

                // Check if bay has per-day schedules
                $hasSchedules = $bay->schedules->isNotEmpty();

                if ($hasSchedules) {
                    $this->line("  Bay {$bay->name}: Using per-day schedules");

                    for ($dayOffset = 0; $dayOffset < $daysToGenerate; $dayOffset++) {
                        $targetDate = $today->copy()->addDays($dayOffset);
                        $dayOfWeek = $targetDate->dayOfWeek; // 0=Sunday through 6=Saturday

                        // Get schedule for this specific day
                        $schedule = $bay->schedules->firstWhere('day_of_week', $dayOfWeek);

                        // If no schedule for this day or day is closed, skip
                        if (!$schedule || $schedule->is_closed) {
                            continue;
                        }

                        // Get operational times for this day
                        // If no times set, treat as 24/7 for this day
                        $dayStart = $schedule->operational_start
                            ? Carbon::parse($schedule->operational_start)
                            : Carbon::parse('00:00');

                        $dayEnd = $schedule->operational_end
                            ? Carbon::parse($schedule->operational_end)
                            : Carbon::parse('23:00');

                        $currentTime = $targetDate->copy()->setTimeFromTimeString($dayStart->format('H:i'));
                        $endTime = $targetDate->copy()->setTimeFromTimeString($dayEnd->format('H:i'));

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
                } else {
                    // FALLBACK: Use old bay-level operational hours for backwards compatibility
                    // Handle 24/7 operation (NULL times) - default to 00:00-23:00
                    $operationalStart = $bay->operational_start
                        ? Carbon::parse($bay->operational_start)
                        : Carbon::parse('00:00');

                    $operationalEnd = $bay->operational_end
                        ? Carbon::parse($bay->operational_end)
                        : Carbon::parse('23:00');

                    // Handle operational days
                    // - NULL or empty = all days (24/7)
                    // - Array with values = specific days only
                    $operationalDays = $bay->operational_days;

                    if (is_null($operationalDays) || (is_array($operationalDays) && empty($operationalDays))) {
                        // 24/7 or no days set = all days (0=Sunday through 6=Saturday)
                        $operationalDays = [0, 1, 2, 3, 4, 5, 6];
                        $this->line("  Bay {$bay->name}: 24/7 operation (all days)");
                    } else {
                        $this->line("  Bay {$bay->name}: Operating on specific days: " . implode(', ', $operationalDays));
                    }

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
        }

        $this->newLine();
        if ($slotsCreated > 0) {
            $this->info("✅ Created {$slotsCreated} new slots for next {$daysToGenerate} days");
        } else {
            $this->warn("⚠️  No new slots were created. Possible reasons:");
            $this->line("   • Slots already exist for these dates");
            $this->line("   • No active bays configured");
            $this->line("   • Bay operational hours/days not set");
        }

        return 0;
    }
}
