<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movements', function (Blueprint $table) {
            $table->id();

            // Movement identification
            $table->enum('movement_type', ['inbound_booked', 'inbound_unbooked', 'outbound', 'internal_transfer'])->default('inbound_booked');
            $table->string('reference_number', 50); // booking_ref, consignment_ref, or auto-generated
            $table->foreignId('depot_id')->constrained('depots');

            // Vehicle & Trailer (can be swapped independently)
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles');
            $table->foreignId('trailer_id')->nullable()->constrained('trailers');

            // Carrier info (can override vehicle defaults)
            $table->string('carrier_company', 100)->nullable();
            $table->string('carrier_contact', 100)->nullable();
            $table->string('driver_name', 100)->nullable();
            $table->string('driver_phone', 20)->nullable();

            // Timing - flexible for multi-stage operations
            $table->timestamp('estimated_arrival')->nullable();
            $table->timestamp('actual_arrival')->nullable();
            $table->timestamp('estimated_departure')->nullable();
            $table->timestamp('actual_departure')->nullable();

            // Current status and location
            $table->enum('current_status', [
                'scheduled', 'en_route', 'arrived', 'in_waiting', 'at_bay',
                'unloading', 'empty', 'loading', 'loaded', 'ready_to_depart',
                'departed', 'trailer_dropped', 'trailer_collected',
            ])->default('scheduled');

            $table->string('gate_number', 10)->nullable();
            $table->foreignId('tipping_location_id')->nullable()->constrained('tipping_locations');
            $table->foreignId('tipping_bay_id')->nullable()->constrained('tipping_bays');
            $table->string('current_location_notes', 255)->nullable();

            // Load characteristics
            $table->string('load_type', 50)->nullable();
            $table->boolean('hazmat')->default(false);
            $table->string('temperature_requirements', 50)->nullable();
            $table->text('special_instructions')->nullable();

            // Operations tracking
            $table->timestamp('unloading_started_at')->nullable();
            $table->timestamp('unloading_completed_at')->nullable();
            $table->timestamp('loading_started_at')->nullable();
            $table->timestamp('loading_completed_at')->nullable();
            $table->text('operation_notes')->nullable();

            // Vehicle/Trailer swapping support
            $table->timestamp('trailer_dropped_at')->nullable();
            $table->timestamp('trailer_collected_at')->nullable();
            $table->foreignId('collecting_vehicle_id')->nullable()->constrained('vehicles');
            $table->text('swap_notes')->nullable();

            // Linked records
            $table->foreignId('booking_id')->nullable()->constrained('bookings'); // Only for booked movements
            $table->unsignedBigInteger('consignment_id')->nullable(); // Will add foreign key after consignments table is created

            // Extensibility
            $table->json('additional_data')->nullable(); // For future unknown fields
            $table->json('custom_fields')->nullable(); // For depot-specific or customer-specific data

            $table->timestamps();

            // Indexes for performance
            $table->index('reference_number');
            $table->index('movement_type');
            $table->index('current_status');
            $table->index('actual_arrival');
            $table->index('depot_id');
            $table->index(['vehicle_id', 'trailer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};
