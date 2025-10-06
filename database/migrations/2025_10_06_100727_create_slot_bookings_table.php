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
        Schema::create('slot_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_primary')->default(false); // true for the slot customer actually booked
            $table->timestamps();

            $table->unique(['slot_id', 'booking_id']);
            $table->index('booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slot_bookings');
    }
};
