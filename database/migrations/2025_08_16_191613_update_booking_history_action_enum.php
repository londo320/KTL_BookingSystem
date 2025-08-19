<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('booking_history', function (Blueprint $table) {
            // Change action from enum to varchar to support more action types
            $table->string('action', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_history', function (Blueprint $table) {
            // Revert back to enum (note: this may lose data if new action types exist)
            $table->enum('action', ['created', 'cancelled', 'rebooked', 'modified', 'completed'])->change();
        });
    }
};
