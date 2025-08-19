<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // First drop foreign key constraints that might exist
            $table->dropForeign(['tipping_location_id']);
            $table->dropForeign(['tipping_bay_id']);
            $table->dropForeign(['tipping_operator_id']);
            $table->dropForeign(['bay_assigned_by']);

            // Drop indexes before dropping the columns
            $table->dropIndex(['vehicle_registration', 'container_number']);
            $table->dropIndex(['tipping_location_id', 'tipping_status']);
            $table->dropIndex(['tipping_bay_id', 'tipping_status']);
            $table->dropIndex(['tipping_status']);
            $table->dropIndex(['current_location']);
            $table->dropIndex(['trailer_status']);
            $table->dropIndex(['waiting_area_location']);
            $table->dropIndex(['dropped_trailer_status']);
            $table->dropIndex(['trailer_left_on_site']);
            $table->dropIndex(['dropped_trailer_location']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            // Remove all vehicle/trailer related fields - they now belong in movements table
            $table->dropColumn([
                // Basic vehicle info
                'vehicle_registration',
                'container_number',

                // Carrier info
                'carrier_company',
                'carrier_contact',

                // Location assignments - gate_number was already removed in a previous migration
                'bay_number',

                // Load/manifest info
                'manifest_number',
                'load_type',
                'hazmat',
                'temperature_requirements',
                'estimated_arrival',
                'special_instructions',

                // Tipping/movement tracking - moved to movements
                'tipping_location_id',
                'tipping_bay_id',
                'tipping_status',
                'current_location',
                'waiting_area_location',
                'entered_waiting_area_at',
                'assigned_bay_at',

                // Trailer management - moved to movements
                'trailer_status',
                'trailer_location_notes',
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
                'trailer_dropped_at',

                // Timing fields - moved to movements
                'moved_to_bay_at',
                'tipping_started_at',
                'tipping_completed_at',
                'trailer_departed_at',
                'tipping_notes',
                'actual_tipping_duration',
                'tipping_issues',
                'tipping_operator_id',
                'bay_assigned_by',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Basic vehicle info
            $table->string('vehicle_registration', 50)->nullable();
            $table->string('container_number', 50)->nullable();

            // Carrier info
            $table->string('carrier_company', 100)->nullable();
            $table->string('carrier_contact', 100)->nullable();

            // Location assignments
            $table->string('gate_number', 10)->nullable();
            $table->string('bay_number', 10)->nullable();

            // Load/manifest info
            $table->string('manifest_number', 50)->nullable();
            $table->string('load_type', 50)->nullable();
            $table->boolean('hazmat')->default(false);
            $table->string('temperature_requirements', 50)->nullable();
            $table->timestamp('estimated_arrival')->nullable();
            $table->text('special_instructions')->nullable();

            // Tipping/movement tracking
            $table->foreignId('tipping_location_id')->nullable();
            $table->foreignId('tipping_bay_id')->nullable();
            $table->enum('tipping_status', ['not_started', 'in_progress', 'completed'])->nullable();
            $table->string('current_location', 100)->nullable();
            $table->string('waiting_area_location', 100)->nullable();
            $table->timestamp('entered_waiting_area_at')->nullable();
            $table->timestamp('assigned_bay_at')->nullable();

            // Trailer management
            $table->enum('trailer_status', ['attached', 'dropped', 'collected'])->nullable();
            $table->text('trailer_location_notes')->nullable();
            $table->timestamp('trailer_collection_scheduled')->nullable();
            $table->string('dropped_trailer_location', 100)->nullable();
            $table->string('dropped_trailer_status', 50)->nullable();
            $table->string('collected_trailer_number', 50)->nullable();
            $table->string('collection_location', 100)->nullable();
            $table->timestamp('trailer_swapped_at')->nullable();
            $table->enum('departure_scenario', ['normal', 'emergency_departure', 'trailer_swap'])->nullable();
            $table->boolean('trailer_left_on_site')->default(false);
            $table->timestamp('trailer_collected_at')->nullable();
            $table->string('collected_by_vehicle', 50)->nullable();
            $table->timestamp('trailer_dropped_at')->nullable();

            // Timing fields
            $table->timestamp('moved_to_bay_at')->nullable();
            $table->timestamp('tipping_started_at')->nullable();
            $table->timestamp('tipping_completed_at')->nullable();
            $table->timestamp('trailer_departed_at')->nullable();
            $table->text('tipping_notes')->nullable();
            $table->integer('actual_tipping_duration')->nullable();
            $table->text('tipping_issues')->nullable();
            $table->foreignId('tipping_operator_id')->nullable();
            $table->foreignId('bay_assigned_by')->nullable();

            // Recreate the indexes
            $table->index(['vehicle_registration', 'container_number']);
            $table->index(['tipping_location_id', 'tipping_status']);
            $table->index(['tipping_bay_id', 'tipping_status']);
            $table->index(['tipping_status']);
            $table->index(['current_location']);
            $table->index(['trailer_status']);
            $table->index(['waiting_area_location']);
            $table->index(['dropped_trailer_status']);
            $table->index(['trailer_left_on_site']);
            $table->index(['dropped_trailer_location']);
        });
    }
};
