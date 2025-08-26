<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outbound_orders', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('outbound_load_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('customer_address_id')->constrained();
            
            // Order identification
            $table->string('order_reference', 100);
            $table->string('internal_order_number', 50)->nullable();
            $table->string('po_number', 100)->nullable();
            
            // Collection source
            $table->foreignId('collection_depot_id')->constrained('depots');
            $table->string('collection_reference', 100)->nullable();
            
            // Delivery scheduling
            $table->date('planned_delivery_date')->nullable();
            $table->time('planned_delivery_time_start')->nullable();
            $table->time('planned_delivery_time_end')->nullable();
            $table->timestamp('estimated_delivery_time')->nullable();
            $table->timestamp('actual_delivery_time')->nullable();
            
            // Order quantities
            $table->integer('expected_pallets')->default(0);
            $table->integer('expected_cases')->default(0);
            $table->integer('expected_units')->default(0);
            $table->decimal('expected_weight_kg', 10, 2)->nullable();
            
            $table->integer('actual_pallets')->nullable();
            $table->integer('actual_cases')->nullable();
            $table->integer('actual_units')->nullable();
            $table->decimal('actual_weight_kg', 10, 2)->nullable();
            
            // Order characteristics
            $table->boolean('temperature_controlled')->default(false);
            $table->boolean('fragile')->default(false);
            $table->boolean('hazardous')->default(false);
            
            // Order status
            $table->enum('status', [
                'pending', 'ready_for_collection', 'collected', 'in_transit', 
                'out_for_delivery', 'delivered', 'failed', 'returned'
            ])->default('pending');
            
            // Timing calculations (for latest arrival feature)
            $table->timestamp('latest_vehicle_arrival_time')->nullable();
            $table->timestamp('delivery_window_end')->nullable();
            $table->integer('travel_time_to_site_minutes')->nullable();
            $table->integer('site_processing_time_minutes')->default(30);
            
            // Delivery priority
            $table->enum('delivery_priority', ['standard', 'priority', 'urgent'])->default('standard');
            $table->timestamp('must_deliver_by')->nullable();
            $table->time('preferred_delivery_window_start')->nullable();
            $table->time('preferred_delivery_window_end')->nullable();
            
            // Order notes
            $table->text('collection_notes')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->text('handling_instructions')->nullable();
            
            // Performance indexes
            $table->index(['outbound_load_id', 'customer_id']);
            $table->index('order_reference');
            $table->index('collection_depot_id');
            $table->index('estimated_delivery_time');
            $table->index('status');
            $table->index('delivery_priority');
            
            $table->timestamps();
            
            $table->unique(['outbound_load_id', 'order_reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outbound_orders');
    }
};