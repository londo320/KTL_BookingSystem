<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_po_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('po_number');

            // Expected quantities
            $table->integer('expected_cases')->nullable();
            $table->integer('expected_pallets')->nullable();
            $table->string('expected_pallet_type')->nullable();

            // Actual quantities
            $table->integer('actual_cases')->nullable();
            $table->integer('actual_pallets')->nullable();
            $table->string('actual_pallet_type')->nullable();

            // Future fields (disabled state)
            $table->string('sku')->nullable();
            $table->text('description')->nullable();
            $table->date('bbe')->nullable(); // Best Before End
            $table->integer('qty')->nullable();
            $table->string('batch')->nullable();
            $table->string('scc_number')->nullable();

            $table->timestamps();

            // Ensure unique PO numbers per booking
            $table->unique(['booking_id', 'po_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_po_numbers');
    }
};
