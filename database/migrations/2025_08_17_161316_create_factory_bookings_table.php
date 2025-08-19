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
        Schema::create('factory_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // FAC-2024-001
            $table->foreignId('depot_id')->constrained();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('carrier_id')->nullable()->constrained();
            $table->foreignId('trailer_type_id')->nullable()->constrained();
            
            // Arrival details
            $table->timestamp('arrived_at');
            $table->string('vehicle_registration', 50);
            $table->string('trailer_registration', 50)->nullable();
            $table->string('driver_name', 100)->nullable();
            $table->string('driver_phone', 20)->nullable();
            
            // Delivery details  
            $table->text('delivery_notes')->nullable();
            $table->json('vehicle_details')->nullable(); // Size, type, etc
            $table->integer('priority')->default(50); // 0-100 priority system
            
            // Status tracking
            $table->enum('status', ['arrived', 'processing', 'completed', 'departed'])->default('arrived');
            $table->timestamp('processing_started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('departed_at')->nullable();
            
            // Gate staff
            $table->foreignId('registered_by')->constrained('users');
            $table->text('gate_notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['depot_id', 'status']);
            $table->index(['arrived_at']);
            $table->index(['priority', 'arrived_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factory_bookings');
    }
};
