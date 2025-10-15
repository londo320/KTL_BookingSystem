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
        Schema::create('booking_type_duration_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('depot_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('min_cases')->default(0)->comment('Minimum case count for this rule');
            $table->integer('max_cases')->nullable()->comment('Maximum case count (null = no limit)');
            $table->integer('duration_minutes')->comment('Duration in minutes for this case range');
            $table->integer('priority')->default(0)->comment('Higher priority rules are checked first');
            $table->timestamps();

            // Indexes for efficient lookups (with custom short names)
            $table->index(['booking_type_id', 'depot_id', 'customer_id', 'priority'], 'btdr_type_depot_cust_priority_idx');
            $table->index(['booking_type_id', 'min_cases', 'max_cases'], 'btdr_type_cases_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_type_duration_rules');
    }
};
