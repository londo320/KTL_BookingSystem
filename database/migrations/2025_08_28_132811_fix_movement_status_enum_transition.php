<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check current ENUM values for movements table
        $result = DB::select("SHOW COLUMNS FROM movements LIKE 'current_status'");
        
        if (empty($result)) {
            return; // Column doesn't exist, nothing to fix
        }
        
        $enumValues = $result[0]->Type;
        
        // If the ENUM already contains the new values, skip
        if (str_contains($enumValues, 'in_parking') && str_contains($enumValues, 'back_to_parking')) {
            return; // Already fixed
        }
        
        // Step 1: Expand ENUM to include both old and new movement statuses
        DB::statement("
            ALTER TABLE movements MODIFY COLUMN current_status 
            ENUM(
                'scheduled', 
                'en_route', 
                'arrived', 
                'in_waiting',          -- Old: will be converted to in_parking
                'in_location',         -- Old: will be converted to in_parking  
                'in_parking',          -- New: replaces in_waiting, in_location, trailer_dropped
                'at_bay', 
                'unloading', 
                'empty', 
                'back_to_parking',     -- New: empty trailer moved back to parking
                'loading',             -- Old: keep for compatibility
                'loaded',              -- Old: keep for compatibility
                'ready_to_depart',     -- Old: keep for compatibility
                'departed', 
                'trailer_dropped',     -- Old: will be converted to in_parking
                'trailer_collected'
            ) DEFAULT 'scheduled'
        ");

        // Step 2: Update existing movement records to use simplified statuses
        DB::update("UPDATE movements SET current_status = 'in_parking' WHERE current_status IN ('in_waiting', 'in_location', 'trailer_dropped')");
        DB::update("UPDATE movements SET current_status = 'back_to_parking' WHERE current_status = 'empty' AND tipping_location_id IS NOT NULL");

        // Step 3: Simplify ENUM to final values
        DB::statement("
            ALTER TABLE movements MODIFY COLUMN current_status 
            ENUM(
                'scheduled', 
                'en_route', 
                'arrived', 
                'in_parking',          -- Replaces: in_waiting, in_location, trailer_dropped
                'at_bay', 
                'unloading', 
                'empty', 
                'back_to_parking',     -- New: empty trailer moved back to parking
                'departed', 
                'trailer_collected'
            ) DEFAULT 'scheduled'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to include old statuses for rollback compatibility
        DB::statement("
            ALTER TABLE movements MODIFY COLUMN current_status 
            ENUM(
                'scheduled', 
                'en_route', 
                'arrived', 
                'in_waiting', 
                'in_location', 
                'at_bay', 
                'unloading', 
                'empty', 
                'loading', 
                'loaded', 
                'ready_to_depart', 
                'departed', 
                'trailer_dropped', 
                'trailer_collected'
            ) DEFAULT 'scheduled'
        ");

        // Convert back (best effort)
        DB::update("UPDATE movements SET current_status = 'trailer_dropped' WHERE current_status = 'in_parking'");
        DB::update("UPDATE movements SET current_status = 'empty' WHERE current_status = 'back_to_parking'");
    }
};
