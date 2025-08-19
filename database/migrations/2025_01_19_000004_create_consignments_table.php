<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consignments', function (Blueprint $table) {
            $table->id();
            $table->string('consignment_number', 50)->unique();

            // Multi-depot support - consignment can span multiple depots
            $table->foreignId('origin_depot_id')->constrained('depots');
            $table->json('depot_route')->nullable(); // Array of depot IDs for multi-depot routes

            // Timing
            $table->timestamp('collection_time')->nullable();
            $table->timestamp('delivery_time')->nullable();
            $table->text('delivery_address')->nullable();

            // Load totals (calculated from consignment_loads)
            $table->integer('total_pallets')->default(0);
            $table->integer('total_cases')->default(0);
            $table->decimal('total_weight_kg', 10, 2)->nullable();

            // Status
            $table->enum('status', [
                'pending', 'loading', 'loaded', 'in_transit',
                'delivered', 'cancelled', 'partial_delivery',
            ])->default('pending');

            // Extensibility
            $table->json('additional_data')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('consignment_number');
            $table->index('status');
            $table->index('collection_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consignments');
    }
};
