<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'trailer_collected' status is already in the original enum, but let's ensure it's there
        DB::statement("ALTER TABLE movements MODIFY COLUMN current_status ENUM('scheduled', 'en_route', 'arrived', 'in_waiting', 'in_location', 'at_bay', 'unloading', 'empty', 'loading', 'loaded', 'ready_to_depart', 'departed', 'trailer_dropped', 'trailer_collected') DEFAULT 'scheduled'");
    }

    public function down(): void
    {
        // Keep the current enum as is
    }
};