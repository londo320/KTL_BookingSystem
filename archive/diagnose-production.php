<?php
/**
 * Production Diagnostics Script
 * Place this in your production public directory as diagnose.php
 * Access via: https://yoursite.com/diagnose.php
 */

// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/plain');

echo "=== PRODUCTION DIAGNOSTICS ===\n\n";

// 1. Check database columns
echo "1. BOOKINGS TABLE COLUMNS:\n";
try {
    $columns = DB::select("DESCRIBE bookings");
    foreach ($columns as $column) {
        echo "   - {$column->Field} ({$column->Type})\n";
    }
} catch (Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n";
}

echo "\n2. CHECK FOR NEW FIELDS:\n";
$requiredFields = ['carrier_id', 'supplier', 'haulier', 'contact_name', 'contact_phone'];
foreach ($requiredFields as $field) {
    try {
        $exists = DB::select("SHOW COLUMNS FROM bookings LIKE '{$field}'");
        echo "   - {$field}: " . (count($exists) > 0 ? "✓ EXISTS" : "✗ MISSING") . "\n";
    } catch (Exception $e) {
        echo "   - {$field}: ERROR - " . $e->getMessage() . "\n";
    }
}

echo "\n3. BOOKING MODEL FILLABLE:\n";
$booking = new App\Models\Booking();
$fillable = $booking->getFillable();
foreach ($requiredFields as $field) {
    echo "   - {$field}: " . (in_array($field, $fillable) ? "✓ IN FILLABLE" : "✗ NOT IN FILLABLE") . "\n";
}

echo "\n4. MIGRATION STATUS:\n";
try {
    $migrations = DB::table('migrations')
        ->where('migration', 'like', '%2025_10_%')
        ->orderBy('batch', 'desc')
        ->get();
    foreach ($migrations as $migration) {
        echo "   [{$migration->batch}] {$migration->migration}\n";
    }
} catch (Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n";
}

echo "\n5. LARAVEL VERSION:\n";
echo "   " . app()->version() . "\n";

echo "\n6. PHP VERSION:\n";
echo "   " . phpversion() . "\n";

echo "\n7. ENVIRONMENT:\n";
echo "   " . app()->environment() . "\n";

echo "\n8. TRY TO CREATE A BOOKING ARRAY:\n";
try {
    $testData = [
        'carrier_id' => null,
        'supplier' => 'Test Supplier',
        'haulier' => 'Test Haulier',
        'contact_name' => 'Test Contact',
        'contact_phone' => '1234567890'
    ];

    $booking = new App\Models\Booking();
    $booking->fill($testData);
    echo "   ✓ Booking model can accept all new fields\n";
    echo "   Filled attributes: " . implode(', ', array_keys($booking->getAttributes())) . "\n";
} catch (Exception $e) {
    echo "   ✗ ERROR: " . $e->getMessage() . "\n";
}

echo "\n9. CHECK LAST ERROR IN LOG:\n";
try {
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $lines = file($logFile);
        $lastLines = array_slice($lines, -50);
        echo "   Last 50 lines of log:\n";
        echo "   " . str_repeat("-", 80) . "\n";
        echo implode("", $lastLines);
    } else {
        echo "   Log file not found\n";
    }
} catch (Exception $e) {
    echo "   ERROR reading log: " . $e->getMessage() . "\n";
}

echo "\n=== END DIAGNOSTICS ===\n";
