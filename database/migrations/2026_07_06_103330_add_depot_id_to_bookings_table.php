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
        Schema::table('bookings', function (Blueprint $table) {
            // Add depot_id column as nullable first
            $table->foreignId('depot_id')->nullable()->after('id')->constrained('depots');
        });

        // Populate depot_id from slot relationships for existing bookings
        DB::statement('
            UPDATE bookings
            INNER JOIN slots ON bookings.slot_id = slots.id
            SET bookings.depot_id = slots.depot_id
            WHERE bookings.depot_id IS NULL
        ');

        // Now make it required (not nullable)
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('depot_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['depot_id']);
            $table->dropColumn('depot_id');
        });
    }
};
