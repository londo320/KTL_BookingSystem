<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consignment_loads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consignment_id')->constrained('consignments')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('depot_id')->constrained('depots'); // Which depot this load is for

            // Load details with pallet type support
            $table->integer('expected_cases')->default(0);
            $table->integer('expected_pallets')->default(0);
            $table->foreignId('expected_pallet_type_id')->nullable()->constrained('pallet_types');

            $table->integer('actual_cases')->nullable();
            $table->integer('actual_pallets')->nullable();
            $table->foreignId('actual_pallet_type_id')->nullable()->constrained('pallet_types');

            $table->decimal('weight_kg', 10, 2)->nullable();

            // Customer reference tied to this specific load
            $table->string('customer_reference', 100)->nullable();
            $table->text('load_notes')->nullable();

            // Status tracking
            $table->enum('load_status', ['pending', 'loaded', 'delivered', 'cancelled'])->default('pending');
            $table->timestamp('loaded_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            $table->timestamps();

            $table->index(['consignment_id', 'customer_id']);
            $table->index(['depot_id', 'load_status']);
            $table->index('customer_reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consignment_loads');
    }
};
