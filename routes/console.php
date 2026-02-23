<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Laravel 12 Scheduling Configuration
// Generate slots daily at 00:15 (12:15 AM) with 14 days ahead from templates
// Templates support time gaps (e.g., 06:00-13:00, then 16:00-20:00 with lunch break)
Schedule::command('slots:generate', ['--days' => 14])
    ->dailyAt('00:15')
    ->withoutOverlapping()
    ->timezone('Europe/London')
    ->appendOutputTo(storage_path('logs/slots_generate.log'))
    ->description('Auto-generate slots from templates for the next 14 days');

// Auto-release slots every 15 minutes based on rules
Schedule::command('app:auto-release-slots')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->timezone('Europe/London')
    ->appendOutputTo(storage_path('logs/auto_release_slots.log'))
    ->description('Auto-release slots based on SlotReleaseRules');

// Sync tipping bay occupancy status every 30 minutes
Schedule::command('bays:sync-occupancy')
    ->everyThirtyMinutes()
    ->withoutOverlapping()
    ->timezone('Europe/London')
    ->appendOutputTo(storage_path('logs/bay_sync.log'))
    ->description('Sync tipping bay occupancy based on active bookings');

// Cleanup incomplete bookings without PO details every 15 minutes
Schedule::command('bookings:cleanup-incomplete', ['--minutes' => 30])
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->timezone('Europe/London')
    ->appendOutputTo(storage_path('logs/booking_cleanup.log'))
    ->description('Delete bookings older than 30 minutes without PO details');
