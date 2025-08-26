<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outbound_loads', function (Blueprint $table) {
            $table->id();
            
            // Load identification
            $table->string('load_reference', 50)->unique();
            $table->string('load_name', 100)->nullable();
            
            // Source tracking (integration with inbound)
            $table->enum('created_from', ['manual', 'booking_completion', 'factory_completion'])->default('manual');
            
            // Load scheduling & logistics
            $table->foreignId('planned_vehicle_id')->nullable()->constrained('vehicles');
            $table->foreignId('assigned_driver_id')->nullable()->constrained('users');
            
            // Load totals (calculated from orders)
            $table->integer('total_orders')->default(0);
            $table->integer('total_customers')->default(0);
            $table->integer('total_collection_points')->default(0);
            $table->integer('total_delivery_points')->default(0);
            $table->integer('total_pallets')->default(0);
            $table->integer('total_cases')->default(0);
            $table->integer('total_units')->default(0);
            $table->decimal('total_weight_kg', 10, 2)->nullable();
            
            // Load status
            $table->enum('status', [
                'planning', 'ready_for_collection', 'collecting', 
                'in_transit', 'delivering', 'completed', 'cancelled'
            ])->default('planning');
            
            // Route optimization
            $table->decimal('optimized_distance_km', 8, 2)->nullable();
            $table->integer('estimated_duration_minutes')->nullable();
            $table->decimal('optimization_score', 5, 2)->nullable();
            
            // Load metadata
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            
            // Performance indexes
            $table->index('load_reference');
            $table->index('status');
            $table->index(['planned_vehicle_id', 'assigned_driver_id']);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outbound_loads');
    }
};