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
        // SQLite doesn't support MODIFY COLUMN with ENUM
        if (DB::connection()->getDriverName() !== 'sqlite') {
            // Add 'factory_delivery' to the movement_type ENUM
            DB::statement("ALTER TABLE movements MODIFY COLUMN movement_type ENUM('inbound_booked','inbound_unbooked','outbound','internal_transfer','factory_delivery')");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // SQLite doesn't support MODIFY COLUMN with ENUM
        if (DB::connection()->getDriverName() !== 'sqlite') {
            // Remove 'factory_delivery' from the movement_type ENUM
            DB::statement("ALTER TABLE movements MODIFY COLUMN movement_type ENUM('inbound_booked','inbound_unbooked','outbound','internal_transfer')");
        }
    }
};
