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
        Schema::create('customer_booking_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('depot_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('sku_fields_enabled')->default(true)->comment('Whether SKU/product fields are shown in booking form');
            $table->boolean('require_po_data')->default(true)->comment('Whether PO numbers and lines are required');
            $table->timestamps();

            // Unique constraint: one config per customer-depot combination
            $table->unique(['customer_id', 'depot_id']);

            $table->index(['customer_id', 'depot_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_booking_configs');
    }
};
