<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Trailer drop/collection fields
            $table->timestamp('trailer_collection_scheduled')->nullable()->after('trailer_location_notes');
            $table->string('dropped_trailer_location')->nullable()->after('trailer_collection_scheduled');
            $table->enum('dropped_trailer_status', [
                'awaiting_collection',
                'empty_available',
                'being_tipped',
                'maintenance_required',
            ])->nullable()->after('dropped_trailer_location');

            // Trailer swap fields
            $table->string('collected_trailer_number')->nullable()->after('dropped_trailer_status');
            $table->string('collection_location')->nullable()->after('collected_trailer_number');
            $table->timestamp('trailer_swapped_at')->nullable()->after('collection_location');

            // Enhanced departure tracking
            $table->string('departure_scenario')->nullable()->after('trailer_swapped_at');
            $table->boolean('trailer_left_on_site')->default(false)->after('departure_scenario');
            $table->timestamp('trailer_collected_at')->nullable()->after('trailer_left_on_site');
            $table->string('collected_by_vehicle')->nullable()->after('trailer_collected_at'); // Registration of collecting vehicle

            // Indexes for trailer tracking
            $table->index(['dropped_trailer_status']);
            $table->index(['trailer_left_on_site']);
            $table->index(['dropped_trailer_location']);
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['dropped_trailer_status']);
            $table->dropIndex(['trailer_left_on_site']);
            $table->dropIndex(['dropped_trailer_location']);

            $table->dropColumn([
                'trailer_collection_scheduled',
                'dropped_trailer_location',
                'dropped_trailer_status',
                'collected_trailer_number',
                'collection_location',
                'trailer_swapped_at',
                'departure_scenario',
                'trailer_left_on_site',
                'trailer_collected_at',
                'collected_by_vehicle',
            ]);
        });
    }
};
