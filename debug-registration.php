<?php

require_once __DIR__ . '/bootstrap/app.php';

use App\Models\User;
use App\Services\AdminNotificationService;

try {
    echo "=== Registration Debug Test ===\n";
    
    // Test if a soft-deleted user exists
    $softDeletedUser = User::withTrashed()->where('email', 'test@example.com')->first();
    if ($softDeletedUser) {
        echo "Soft-deleted user found: " . $softDeletedUser->name . " (deleted: " . ($softDeletedUser->trashed() ? 'yes' : 'no') . ")\n";
    } else {
        echo "No soft-deleted user found\n";
    }
    
    // Test email validation
    echo "\nTesting email validation...\n";
    $validator = Validator::make(
        ['email' => 'test@example.com'],
        ['email' => 'unique:users,email,NULL,id,deleted_at,NULL']
    );
    
    if ($validator->fails()) {
        echo "Validation failed: " . implode(', ', $validator->errors()->all()) . "\n";
    } else {
        echo "Validation passed - email is available\n";
    }
    
    // Test admin notification service
    echo "\nTesting admin notification service...\n";
    $testUser = new User(['name' => 'Test User', 'email' => 'test@example.com']);
    
    try {
        AdminNotificationService::sendNewUserRegistration($testUser);
        echo "Admin notification service works\n";
    } catch (Exception $e) {
        echo "Admin notification failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== Debug Complete ===\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}