<?php

namespace App\Console\Commands;

use App\Models\Slot;
use App\Models\SlotReleaseRule;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoReleaseSlots extends Command
{
    protected $signature = 'app:auto-release-slots';

    protected $description = 'Release slots per rule or publicly if no rule exists';

    public function handle()
    {
        $now = now();
        $rules = SlotReleaseRule::with('customers', 'depot')->orderByDesc('priority')->get();
        $processedDepotIds = [];
        $depotsWithRules = $rules->pluck('depot_id')->unique();
        $totalReleased = 0;

        // === RULE-BASED RELEASE ===
        foreach ($rules as $rule) {
            $depotName = $rule->depot->name ?? 'Unknown Depot';

            $releaseDayOfWeek = is_numeric($rule->release_day)
                ? (int) $rule->release_day
                : Carbon::parse($rule->release_day)->dayOfWeek;

            $releaseTime = Carbon::parse($rule->release_time)->setTimezone(config('app.timezone'));
            $cutoffTime = Carbon::parse($rule->lock_cutoff_time);

            $daysUntilNext = (7 + $releaseDayOfWeek - $now->dayOfWeek) % 7;
            $daysUntilNext = $daysUntilNext === 0 ? 7 : $daysUntilNext;
            $releaseWindowEnd = $now->copy()->addDays($daysUntilNext)->startOfDay();

            $scheduledRelease = $now->copy()->startOfWeek()->addDays(($releaseDayOfWeek - 1))->setTimeFromTimeString($releaseTime->format('H:i'));
            if ($now->greaterThanOrEqualTo($scheduledRelease)) {
                $slots = Slot::where('depot_id', $rule->depot_id)
                    ->where('start_at', '<', $releaseWindowEnd)
                    ->whereNull('released_at')
                    ->get();

                if ($slots->isEmpty()) {
                    continue;
                }

                foreach ($slots as $slot) {
                    $slot->released_at = $now;
                    $slot->locked_at = $slot->start_at
                        ->copy()
                        ->subDays($rule->lock_cutoff_days)
                        ->setTimeFromTimeString($cutoffTime->format('H:i'));
                    $slot->save();

                    // At release time, slots become public — remove customer restrictions
                    $slot->allowed_customers()->detach();
                }

                $this->info("Released {$slots->count()} slots for {$depotName}");
                $totalReleased += $slots->count();
                $processedDepotIds[] = $rule->depot_id;
            }
        }

        // === FALLBACK RELEASE FOR DEPOTS WITHOUT RULES ===
        $fallbackDepotIds = Slot::select('depot_id')
            ->distinct()
            ->whereNotIn('depot_id', $depotsWithRules)
            ->pluck('depot_id');

        foreach ($fallbackDepotIds as $depotId) {
            $slots = Slot::where('depot_id', $depotId)
                ->whereNull('released_at')
                ->where('start_at', '>', now())
                ->get();

            if ($slots->isEmpty()) {
                continue;
            }

            foreach ($slots as $slot) {
                $slot->released_at = $now;
                $slot->locked_at = $slot->start_at->copy()->subDays(1)->setTime(16, 0);
                $slot->save();

                $slot->allowed_customers()->detach();
            }

            $this->info("Fallback released {$slots->count()} slots for depot ID {$depotId}");
            $totalReleased += $slots->count();
        }

        if ($totalReleased > 0) {
            $this->info("Total slots released: {$totalReleased}");
        }
    }
}
