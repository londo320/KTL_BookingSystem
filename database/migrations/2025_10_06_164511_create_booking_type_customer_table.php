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
        Schema::create('booking_type_customer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('depot_id')->nullable()->constrained()->onDelete('cascade');
            $table->unsignedInteger('duration_minutes');
            $table->timestamps();

            // Ensure unique combination of booking type, customer, and depot
            $table->unique(['booking_type_id', 'customer_id', 'depot_id'], 'bt_customer_depot_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_type_customer');
    }
};
