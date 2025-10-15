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
        Schema::create('bay_capacity_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('depot_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_type_id')->nullable()->constrained()->onDelete('cascade');
            $table->time('time_start')->comment('Start time of this capacity window (e.g., 08:00)');
            $table->time('time_end')->comment('End time of this capacity window (e.g., 17:00)');
            $table->json('days_of_week')->nullable()->comment('Array of day names (Monday, Tuesday, etc.) - null means all days');
            $table->integer('max_concurrent_bookings')->comment('Maximum concurrent bookings of this type in this time window');
            $table->json('applicable_bay_ids')->nullable()->comment('Which bay IDs this rule applies to - null means all bays');
            $table->decimal('capacity_weight', 5, 2)->default(1.00)->comment('How much capacity this booking type uses (e.g., 2.0 = uses double capacity)');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['depot_id', 'booking_type_id', 'is_active']);
            $table->index(['depot_id', 'time_start', 'time_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bay_capacity_rules');
    }
};
