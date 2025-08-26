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
        // Add 'factory_delivery' to the movement_type ENUM
        DB::statement("ALTER TABLE movements MODIFY COLUMN movement_type ENUM('inbound_booked','inbound_unbooked','outbound','internal_transfer','factory_delivery')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'factory_delivery' from the movement_type ENUM
        DB::statement("ALTER TABLE movements MODIFY COLUMN movement_type ENUM('inbound_booked','inbound_unbooked','outbound','internal_transfer')");
    }
};
