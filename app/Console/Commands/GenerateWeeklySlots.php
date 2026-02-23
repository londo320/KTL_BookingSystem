<?php

namespace App\Console\Commands;

use App\Models\Slot;
use App\Models\SlotTemplate;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateWeeklySlots extends Command
{
    protected $signature = 'slots:generate {--days=14 : Number of days to generate slots for}';

    protected $description = 'Generate slots from templates for next 14-30 days';

    public function handle()
    {
        $daysToGenerate = (int) $this->option('days');
        $today = Carbon::today();
        $slotsCreated = 0;

        $this->info("Generating slots for next {$daysToGenerate} days...");

        // Get all slot templates
        $templates = SlotTemplate::with(['depot', 'bookingType'])->get();

        // For each day in the range
        for ($dayOffset = 0; $dayOffset < $daysToGenerate; $dayOffset++) {
            $targetDate = $today->copy()->addDays($dayOffset);
            $dayOfWeek = $targetDate->dayOfWeek; // 0 (Sunday) to 6 (Saturday)

            // Get templates for this day of week
            $todayTemplates = $templates->where('day_of_week', $dayOfWeek);

            foreach ($todayTemplates as $tpl) {
                $start = Carbon::parse("{$targetDate->toDateString()} {$tpl->start_time}");

                // Check if slot already exists
                if (! Slot::where('depot_id', $tpl->depot_id)
                    ->where('start_at', $start)
                    ->exists()) {

                    Slot::create([
                        'depot_id' => $tpl->depot_id,
                        'booking_type_id' => $tpl->booking_type_id,
                        'start_at' => $start,
                        'end_at' => $start->copy()->addMinutes($tpl->duration_minutes),
                        'capacity' => $tpl->capacity ?? 1,
                        'is_blocked' => false,
                    ]);

                    $slotsCreated++;
                }
            }
        }

        $this->info("✅ Created {$slotsCreated} new slots for next {$daysToGenerate} days");
        return 0;
    }
}
