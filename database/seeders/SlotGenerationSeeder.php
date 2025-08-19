<?php

namespace Database\Seeders;

use App\Models\Slot;
use App\Models\SlotTemplate;
use App\Models\Depot;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SlotGenerationSeeder extends Seeder
{
    public function run(): void
    {
        $mainDepot = Depot::first(); // Use first available depot
        
        if (!$mainDepot) {
            $this->command->error('Main Depot not found. Please run DepotSeeder first.');
            return;
        }

        $templates = SlotTemplate::where('depot_id', $mainDepot->id)->get();
        
        if ($templates->isEmpty()) {
            $this->command->error('No slot templates found. Please run SlotTemplateSeeder first.');
            return;
        }

        // Generate slots for the next 4 weeks
        $startDate = Carbon::now()->startOfWeek(); // This Monday
        $endDate = $startDate->copy()->addWeeks(4);
        
        $slotsCreated = 0;

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dayOfWeek = $date->dayOfWeek;
            $dayOfWeek = $dayOfWeek === 0 ? 7 : $dayOfWeek; // Convert Sunday from 0 to 7

            // Get templates for this day of week
            $dayTemplates = $templates->where('day_of_week', $dayOfWeek)->where('is_active', true);

            foreach ($dayTemplates as $template) {
                // Create slot start and end times
                $startTime = $date->copy()->setTimeFromTimeString($template->start_time);
                $endTime = $date->copy()->setTimeFromTimeString($template->end_time);

                // Skip if the slot is in the past
                if ($startTime->isPast()) {
                    continue;
                }

                // Check if slot already exists
                $existingSlot = Slot::where('depot_id', $mainDepot->id)
                    ->where('start_at', $startTime)
                    ->where('end_at', $endTime)
                    ->first();

                if (!$existingSlot) {
                    $slot = Slot::create([
                        'depot_id' => $mainDepot->id,
                        'start_at' => $startTime,
                        'end_at' => $endTime,
                        'capacity' => $template->capacity,
                        'available_capacity' => $template->capacity,
                        'slot_type' => $template->slot_type ?? 'regular',
                        'is_available' => true,
                        'auto_confirm' => $template->auto_confirm ?? false,
                        'booking_window_start' => $startTime->copy()->subDays($template->booking_window_days ?? 14),
                        'booking_window_end' => $startTime->copy()->subHours($template->cut_off_hours ?? 24),
                        'notes' => $template->description,
                        'created_from_template' => true,
                        'template_id' => $template->id,
                    ]);

                    $slotsCreated++;
                }
            }
        }

        $this->command->info("Slots generated successfully ({$slotsCreated} slots created for next 4 weeks).");
    }
}