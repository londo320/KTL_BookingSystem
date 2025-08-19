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
        Schema::create('booking_history', function (Blueprint $table) {
            $table->id();

            // Core booking reference
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('user_id')->constrained('users'); // Who made the change

            // Original slot details
            $table->foreignId('original_slot_id')->nullable()->constrained('slots');
            $table->datetime('original_start_time')->nullable();
            $table->datetime('original_end_time')->nullable();

            // New slot details (for rebooks)
            $table->foreignId('new_slot_id')->nullable()->constrained('slots');
            $table->datetime('new_start_time')->nullable();
            $table->datetime('new_end_time')->nullable();

            // Action tracking
            $table->enum('action', ['created', 'cancelled', 'rebooked', 'modified', 'completed']);
            $table->text('reason')->nullable();
            $table->json('changes')->nullable(); // Store field changes

            // Timing analysis
            $table->integer('hours_before_slot')->nullable(); // How many hours before slot was action taken
            $table->boolean('is_last_minute')->default(false); // Flag for last minute changes (<24hrs)

            // Behavioral tracking
            $table->integer('customer_rebook_count_30days')->default(0); // Rolling count
            $table->integer('customer_cancel_count_30days')->default(0); // Rolling count

            $table->timestamps();

            // Indexes for performance
            $table->index(['customer_id', 'action', 'created_at']);
            $table->index(['booking_id', 'action']);
            $table->index(['is_last_minute', 'action']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_history');
    }
};
