<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't support MODIFY COLUMN with ENUM, so we skip this migration for SQLite
        // The movements table already has current_status as a string field, not enum in SQLite
        if (DB::connection()->getDriverName() !== 'sqlite') {
            // Add 'in_location' to the current_status enum
            DB::statement("ALTER TABLE movements MODIFY COLUMN current_status ENUM('scheduled', 'en_route', 'arrived', 'in_waiting', 'in_location', 'at_bay', 'unloading', 'empty', 'loading', 'loaded', 'ready_to_depart', 'departed', 'trailer_dropped', 'trailer_collected') DEFAULT 'scheduled'");
        }
    }

    public function down(): void
    {
        // SQLite doesn't support MODIFY COLUMN with ENUM
        if (DB::connection()->getDriverName() !== 'sqlite') {
            // Remove 'in_location' from the enum
            DB::statement("ALTER TABLE movements MODIFY COLUMN current_status ENUM('scheduled', 'en_route', 'arrived', 'in_waiting', 'at_bay', 'unloading', 'empty', 'loading', 'loaded', 'ready_to_depart', 'departed', 'trailer_dropped', 'trailer_collected') DEFAULT 'scheduled'");
        }
    }
};