<?php

namespace Database\Seeders;

use App\Models\SlotTemplate;
use App\Models\Depot;
use App\Models\BookingType;
use Illuminate\Database\Seeder;

class SlotTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $mainDepot = Depot::first(); // Use first available depot
        
        if (!$mainDepot) {
            $this->command->error('Main Depot not found. Please run DepotSeeder first.');
            return;
        }

        $bookingTypes = BookingType::all();
        
        if ($bookingTypes->isEmpty()) {
            $this->command->error('No booking types found. Please run BookingTypeSeeder first.');
            return;
        }

        $templates = [];

        // Monday to Friday - Regular Business Hours
        $weekdays = [
            1 => 'Monday',
            2 => 'Tuesday', 
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday'
        ];

        foreach ($weekdays as $dayOfWeek => $dayName) {
            // Morning slots (6:00 AM - 12:00 PM) - 3 hour slots
            $templates[] = [
                'name' => "{$dayName} Morning Shift A",
                'depot_id' => $mainDepot->id,
                'day_of_week' => $dayOfWeek,
                'start_time' => '06:00:00',
                'end_time' => '09:00:00',
                'duration_minutes' => 180, // 3 hours
                'capacity' => 8,
                'is_active' => true,
                'description' => "Early morning slot - {$dayName} 6:00 AM - 9:00 AM",
                'slot_type' => 'regular',
                'auto_confirm' => false,
                'booking_window_days' => 14,
                'cut_off_hours' => 24,
            ];

            $templates[] = [
                'name' => "{$dayName} Morning Shift B", 
                'depot_id' => $mainDepot->id,
                'day_of_week' => $dayOfWeek,
                'start_time' => '09:00:00',
                'end_time' => '12:00:00',
                'duration_minutes' => 180, // 3 hours
                'capacity' => 10,
                'is_active' => true,
                'description' => "Mid morning slot - {$dayName} 9:00 AM - 12:00 PM",
                'slot_type' => 'regular',
                'auto_confirm' => false,
                'booking_window_days' => 14,
                'cut_off_hours' => 24,
            ];

            // Afternoon slots (12:00 PM - 6:00 PM) - 3 hour slots
            $templates[] = [
                'name' => "{$dayName} Afternoon Shift A",
                'depot_id' => $mainDepot->id,
                'day_of_week' => $dayOfWeek,
                'start_time' => '12:00:00',
                'end_time' => '15:00:00',
                'duration_minutes' => 180, // 3 hours
                'capacity' => 12,
                'is_active' => true,
                'description' => "Lunch time slot - {$dayName} 12:00 PM - 3:00 PM",
                'slot_type' => 'regular',
                'auto_confirm' => false,
                'booking_window_days' => 14,
                'cut_off_hours' => 24,
            ];

            $templates[] = [
                'name' => "{$dayName} Afternoon Shift B",
                'depot_id' => $mainDepot->id,
                'day_of_week' => $dayOfWeek,
                'start_time' => '15:00:00',
                'end_time' => '18:00:00',
                'duration_minutes' => 180, // 3 hours
                'capacity' => 10,
                'is_active' => true,
                'description' => "Late afternoon slot - {$dayName} 3:00 PM - 6:00 PM",
                'slot_type' => 'regular',
                'auto_confirm' => false,
                'booking_window_days' => 14,
                'cut_off_hours' => 24,
            ];

            // Evening slot (6:00 PM - 9:00 PM) - Reduced capacity
            $templates[] = [
                'name' => "{$dayName} Evening Shift",
                'depot_id' => $mainDepot->id,
                'day_of_week' => $dayOfWeek,
                'start_time' => '18:00:00',
                'end_time' => '21:00:00',
                'duration_minutes' => 180, // 3 hours
                'capacity' => 6,
                'is_active' => true,
                'description' => "Evening slot - {$dayName} 6:00 PM - 9:00 PM",
                'slot_type' => 'evening',
                'auto_confirm' => false,
                'booking_window_days' => 14,
                'cut_off_hours' => 48, // Longer cut-off for evening slots
            ];
        }

        // Saturday - Reduced hours (8:00 AM - 2:00 PM)
        $templates[] = [
            'name' => 'Saturday Morning',
            'depot_id' => $mainDepot->id,
            'day_of_week' => 6,
            'start_time' => '08:00:00',
            'end_time' => '11:00:00',
            'duration_minutes' => 180,
            'capacity' => 6,
            'is_active' => true,
            'description' => 'Saturday morning slot - 8:00 AM - 11:00 AM',
            'slot_type' => 'weekend',
            'auto_confirm' => false,
            'booking_window_days' => 14,
            'cut_off_hours' => 48,
        ];

        $templates[] = [
            'name' => 'Saturday Afternoon',
            'depot_id' => $mainDepot->id,
            'day_of_week' => 6,
            'start_time' => '11:00:00',
            'end_time' => '14:00:00',
            'duration_minutes' => 180,
            'capacity' => 6,
            'is_active' => true,
            'description' => 'Saturday afternoon slot - 11:00 AM - 2:00 PM',
            'slot_type' => 'weekend',
            'auto_confirm' => false,
            'booking_window_days' => 14,
            'cut_off_hours' => 48,
        ];

        // Special slots for urgent/priority bookings
        foreach ($weekdays as $dayOfWeek => $dayName) {
            $templates[] = [
                'name' => "{$dayName} Priority/Urgent",
                'depot_id' => $mainDepot->id,
                'day_of_week' => $dayOfWeek,
                'start_time' => '05:00:00',
                'end_time' => '06:00:00',
                'duration_minutes' => 60, // 1 hour
                'capacity' => 2,
                'is_active' => true,
                'description' => "Priority/urgent slot - {$dayName} 5:00 AM - 6:00 AM",
                'slot_type' => 'priority',
                'auto_confirm' => true, // Auto-confirm priority slots
                'booking_window_days' => 7,
                'cut_off_hours' => 4, // Short cut-off for urgent
            ];
        }

        foreach ($templates as $template) {
            SlotTemplate::firstOrCreate(
                [
                    'name' => $template['name'],
                    'depot_id' => $template['depot_id']
                ],
                $template
            );
        }

        $this->command->info('Slot templates seeded successfully (' . count($templates) . ' templates created).');
    }
}