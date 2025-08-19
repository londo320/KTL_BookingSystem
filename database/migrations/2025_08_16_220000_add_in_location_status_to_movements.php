<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'in_location' to the current_status enum
        DB::statement("ALTER TABLE movements MODIFY COLUMN current_status ENUM('scheduled', 'en_route', 'arrived', 'in_waiting', 'in_location', 'at_bay', 'unloading', 'empty', 'loading', 'loaded', 'ready_to_depart', 'departed', 'trailer_dropped', 'trailer_collected') DEFAULT 'scheduled'");
    }

    public function down(): void
    {
        // Remove 'in_location' from the enum
        DB::statement("ALTER TABLE movements MODIFY COLUMN current_status ENUM('scheduled', 'en_route', 'arrived', 'in_waiting', 'at_bay', 'unloading', 'empty', 'loading', 'loaded', 'ready_to_depart', 'departed', 'trailer_dropped', 'trailer_collected') DEFAULT 'scheduled'");
    }
};