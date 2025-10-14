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
        Schema::create('booking_type_equipment_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_type_id')->constrained()->onDelete('cascade');
            $table->json('required_equipment')->comment('Array of equipment requirements like ["ramp", "forklift"]');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['booking_type_id', 'is_active'], 'bter_booking_type_active_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_type_equipment_requirements');
    }
};
