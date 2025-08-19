<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Waiting area workflow fields
            $table->string('current_location')->default('off_site')->after('tipping_status');
            $table->string('waiting_area_location')->nullable()->after('current_location');
            $table->timestamp('entered_waiting_area_at')->nullable()->after('waiting_area_location');
            $table->timestamp('assigned_bay_at')->nullable()->after('entered_waiting_area_at');

            // Track trailer status and location
            $table->enum('trailer_status', [
                'with_vehicle',     // Trailer attached to vehicle
                'dropped_waiting',  // Dropped in waiting area
                'dropped_bay',      // Dropped at tipping bay
                'being_tipped',     // Currently being tipped
                'awaiting_collection', // Empty trailer waiting for pickup
                'collected',         // Collected by another vehicle
            ])->default('with_vehicle')->after('assigned_bay_at');

            $table->string('trailer_location_notes')->nullable()->after('trailer_status');

            // Indexes for location tracking
            $table->index(['current_location']);
            $table->index(['trailer_status']);
            $table->index(['waiting_area_location']);
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['current_location']);
            $table->dropIndex(['trailer_status']);
            $table->dropIndex(['waiting_area_location']);

            $table->dropColumn([
                'current_location',
                'waiting_area_location',
                'entered_waiting_area_at',
                'assigned_bay_at',
                'trailer_status',
                'trailer_location_notes',
            ]);
        });
    }
};
