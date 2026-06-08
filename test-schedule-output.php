<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test 1: Run schedule:list
echo "=== Test 1: Running schedule:list ===\n";
Illuminate\Support\Facades\Artisan::call('schedule:list');
$output = Illuminate\Support\Facades\Artisan::output();
echo "Output length: " . strlen($output) . "\n";
echo "Output:\n";
echo $output;
echo "\n";

// Test 2: Get schedule events directly
echo "\n=== Test 2: Getting Schedule events ===\n";
$schedule = $app->make(Illuminate\Console\Scheduling\Schedule::class);
$events = $schedule->events();
echo "Number of events: " . count($events) . "\n";

foreach ($events as $event) {
    echo "- " . ($event->description ?? $event->command ?? 'No description') . "\n";
    echo "  Expression: " . $event->expression . "\n";
}
