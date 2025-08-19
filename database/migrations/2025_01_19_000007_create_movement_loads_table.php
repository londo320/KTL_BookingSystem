<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // This table tracks what's actually loaded/unloaded during each movement
        // Supports: unloading a trailer, reloading it, multiple customers per movement
        Schema::create('movement_loads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movement_id')->constrained('movements')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers');

            // Load operation type
            $table->enum('operation_type', ['inbound', 'outbound', 'transfer']);
            $table->integer('sequence')->default(1); // For multiple operations on same movement

            // Load details with pallet types
            $table->integer('expected_cases')->default(0);
            $table->integer('expected_pallets')->default(0);
            $table->foreignId('expected_pallet_type_id')->nullable()->constrained('pallet_types');

            $table->integer('actual_cases')->nullable();
            $table->integer('actual_pallets')->nullable();
            $table->foreignId('actual_pallet_type_id')->nullable()->constrained('pallet_types');

            // References
            $table->string('customer_reference', 100)->nullable();
            $table->string('po_number', 50)->nullable();

            // Operation tracking
            $table->timestamp('operation_started_at')->nullable();
            $table->timestamp('operation_completed_at')->nullable();
            $table->text('operation_notes')->nullable();

            // Link to PO system if inbound
            $table->unsignedBigInteger('booking_po_line_id')->nullable(); // Will add foreign key constraint later

            $table->timestamps();

            $table->index(['movement_id', 'operation_type']);
            $table->index(['customer_id', 'operation_type']);
            $table->index('po_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movement_loads');
    }
};
