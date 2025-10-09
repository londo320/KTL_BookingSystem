<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't support MODIFY COLUMN with ENUM
        if (DB::connection()->getDriverName() !== 'sqlite') {
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
        }

        // Step 2: Update existing movement records to use simplified statuses
        DB::update("UPDATE movements SET current_status = 'in_parking' WHERE current_status IN ('in_waiting', 'in_location', 'trailer_dropped')");
        DB::update("UPDATE movements SET current_status = 'back_to_parking' WHERE current_status = 'empty' AND tipping_location_id IS NOT NULL");

        if (DB::connection()->getDriverName() !== 'sqlite') {
            // Step 3: Remove old ENUM values that are no longer needed
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

            // Step 1: Expand ENUM to include both old and new values
            DB::statement("
                ALTER TABLE tipping_locations MODIFY COLUMN location_type
                ENUM('drop_zone', 'collection_zone', 'general', 'parking') DEFAULT 'general'
            ");
        }

        // Step 2: Convert all existing records to parking
        DB::update("UPDATE tipping_locations SET location_type = 'parking'");

        if (DB::connection()->getDriverName() !== 'sqlite') {
            // Step 3: Simplify ENUM to only parking
            DB::statement("
                ALTER TABLE tipping_locations MODIFY COLUMN location_type
                ENUM('parking') DEFAULT 'parking'
            ");
        }

        // Update location names for clarity
        DB::update("UPDATE tipping_locations SET name = CONCAT(name, ' (Parking Area)') WHERE location_type = 'parking' AND name NOT LIKE '%(Parking Area)%'");
    }

    public function down(): void
    {
        // SQLite doesn't support MODIFY COLUMN with ENUM
        if (DB::connection()->getDriverName() !== 'sqlite') {
            // Revert movement statuses
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
        }

        // Revert movement records (best effort)
        DB::update("UPDATE movements SET current_status = 'trailer_dropped' WHERE current_status = 'in_parking'");
        DB::update("UPDATE movements SET current_status = 'empty' WHERE current_status = 'back_to_parking'");

        if (DB::connection()->getDriverName() !== 'sqlite') {
            // Revert tipping location types
            DB::statement("
                ALTER TABLE tipping_locations MODIFY COLUMN location_type
                ENUM('drop_zone', 'collection_zone', 'general') DEFAULT 'general'
            ");
        }

        // Convert back to general (best effort)
        DB::update("UPDATE tipping_locations SET location_type = 'general' WHERE location_type = 'parking'");

        // Remove (Parking Area) suffix
        DB::update("UPDATE tipping_locations SET name = REPLACE(name, ' (Parking Area)', '') WHERE name LIKE '%(Parking Area)%'");
    }
};