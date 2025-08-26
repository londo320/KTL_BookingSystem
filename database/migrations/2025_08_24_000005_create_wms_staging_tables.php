<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Staging table for WMS uploads
        Schema::create('wms_staging_orders', function (Blueprint $table) {
            $table->id();
            
            // Source tracking
            $table->enum('source_system', ['wms_1', 'wms_2', 'edi', 'manual'])->default('wms_1');
            $table->string('source_file_name')->nullable();
            $table->timestamp('uploaded_at');
            
            // Load identification  
            $table->string('load_reference', 50); // From driver paperwork
            $table->string('external_load_id', 100)->nullable(); // WMS load ID
            
            // Order details
            $table->string('order_reference', 100);
            $table->string('po_number', 100)->nullable();
            $table->string('customer_code', 50);
            $table->string('customer_name', 200);
            
            // Collection details
            $table->string('collection_depot_code', 50);
            $table->string('collection_reference', 100)->nullable();
            
            // Delivery details
            $table->text('delivery_address_raw'); // Full address as received
            $table->string('delivery_postcode', 20);
            $table->date('planned_delivery_date')->nullable();
            $table->time('delivery_time_start')->nullable();
            $table->time('delivery_time_end')->nullable();
            
            // Quantities
            $table->integer('pallets')->default(0);
            $table->integer('cases')->default(0);
            $table->integer('units')->default(0);
            $table->decimal('weight_kg', 10, 2)->nullable();
            
            // Special requirements
            $table->boolean('temperature_controlled')->default(false);
            $table->boolean('fragile')->default(false);
            $table->boolean('hazardous')->default(false);
            $table->text('special_instructions')->nullable();
            
            // Processing status
            $table->enum('processing_status', [
                'pending', 'matched', 'failed', 'ignored'
            ])->default('pending');
            $table->text('processing_notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            
            // Links to live system
            $table->foreignId('outbound_load_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('outbound_order_id')->nullable()->constrained()->nullOnDelete();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('load_reference');
            $table->index('order_reference');
            $table->index('processing_status');
            $table->index('uploaded_at');
            $table->index(['source_system', 'uploaded_at']);
        });
        
        // Physical load registration (when driver arrives)
        Schema::create('physical_load_registrations', function (Blueprint $table) {
            $table->id();
            
            // Load identification
            $table->string('load_reference', 50)->unique();
            $table->string('driver_paperwork_ref', 100)->nullable();
            
            // Vehicle details
            $table->string('vehicle_registration', 20);
            $table->string('trailer_registration', 20)->nullable();
            $table->string('carrier_company', 200);
            
            // Driver details
            $table->string('driver_name', 100);
            $table->string('driver_phone', 20)->nullable();
            $table->string('driver_license', 50)->nullable();
            
            // Arrival details
            $table->timestamp('arrival_time');
            $table->foreignId('arrival_depot_id')->constrained('depots');
            $table->text('arrival_notes')->nullable();
            
            // Status tracking
            $table->enum('status', [
                'arrived', 'orders_matched', 'ready_for_collection', 
                'collecting', 'departed', 'cancelled'
            ])->default('arrived');
            
            // Links to outbound system
            $table->foreignId('outbound_load_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('expected_orders')->default(0);
            $table->integer('matched_orders')->default(0);
            
            // Staff tracking
            $table->foreignId('registered_by')->constrained('users');
            
            $table->timestamps();
            
            // Indexes
            $table->index('load_reference');
            $table->index('vehicle_registration');
            $table->index('status');
            $table->index('arrival_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('physical_load_registrations');
        Schema::dropIfExists('wms_staging_orders');
    }
};