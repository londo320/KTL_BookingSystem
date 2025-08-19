<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('po_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_po_number_id')->constrained()->cascadeOnDelete();
            $table->integer('line_number')->default(1); // Line number within the PO

            // Expected quantities
            $table->integer('expected_cases')->nullable();
            $table->integer('expected_pallets')->nullable();
            $table->foreignId('expected_pallet_type_id')->nullable()->constrained('pallet_types');

            // Actual quantities
            $table->integer('actual_cases')->nullable();
            $table->integer('actual_pallets')->nullable();
            $table->foreignId('actual_pallet_type_id')->nullable()->constrained('pallet_types');

            // Future fields (will be added later - keeping structure ready)
            $table->string('sku')->nullable();
            $table->text('description')->nullable();
            $table->date('bbe')->nullable(); // Best Before End
            $table->integer('qty')->nullable();
            $table->string('batch')->nullable();
            $table->string('scc_number')->nullable();

            $table->timestamps();

            // Ensure line numbers are unique per PO
            $table->unique(['booking_po_number_id', 'line_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('po_lines');
    }
};
