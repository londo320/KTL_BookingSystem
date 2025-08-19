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
        Schema::table('bookings', function (Blueprint $table) {
            // Vehicle details for booking creation and arrival
            $table->string('vehicle_registration', 50)->nullable()->after('booking_reference');
            $table->string('container_number', 50)->nullable()->after('vehicle_registration');
            $table->string('carrier_company', 100)->nullable()->after('container_number');
            $table->string('gate_number', 20)->nullable()->after('carrier_company');
            $table->string('trailer_size', 50)->nullable()->after('gate_number');
            $table->timestamp('estimated_arrival')->nullable()->after('trailer_size');
            $table->text('special_instructions')->nullable()->after('estimated_arrival');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'vehicle_registration',
                'container_number', 
                'carrier_company',
                'gate_number',
                'trailer_size',
                'estimated_arrival',
                'special_instructions'
            ]);
        });
    }
};
