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
        Schema::create('customer_depot_time_windows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('depot_id')->constrained()->onDelete('cascade');
            $table->time('allowed_start_time')->comment('Earliest time customer can book slots');
            $table->time('allowed_end_time')->comment('Latest time customer can book slots');
            $table->json('days_of_week')->nullable()->comment('Array of allowed days (0=Sun, 6=Sat). Null = all days allowed');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Unique constraint: one time window per customer-depot combination
            $table->unique(['customer_id', 'depot_id']);

            $table->index(['customer_id', 'depot_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_depot_time_windows');
    }
};
