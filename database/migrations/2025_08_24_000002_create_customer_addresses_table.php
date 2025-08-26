<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            
            // Address identification
            $table->string('address_name', 100)->nullable();
            $table->boolean('is_default')->default(false);
            
            // Contact details
            $table->string('contact_name', 100)->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->string('contact_email', 100)->nullable();
            
            // Structured address
            $table->string('company_name', 200)->nullable();
            $table->string('address_line_1', 200);
            $table->string('address_line_2', 200)->nullable();
            $table->string('city', 100);
            $table->string('county', 100)->nullable();
            $table->string('postcode', 20);
            $table->string('country', 5)->default('GB');
            
            // Geolocation (for routing)
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamp('geocoded_at')->nullable();
            
            // Delivery constraints
            $table->text('delivery_instructions')->nullable();
            $table->text('access_notes')->nullable();
            $table->json('delivery_hours')->nullable(); // {"mon": "08:00-17:00", "sat": "09:00-12:00"}
            
            // Delivery requirements
            $table->boolean('requires_appointment')->default(false);
            $table->boolean('requires_signature')->default(true);
            $table->boolean('requires_photo_proof')->default(false);
            $table->json('special_equipment')->nullable(); // ["tail_lift", "pallet_truck", "crane"]
            
            // Timing constraints
            $table->time('latest_delivery_time')->nullable();
            $table->integer('delivery_buffer_minutes')->default(15);
            $table->integer('unloading_duration_minutes')->default(30);
            $table->time('site_closure_time')->nullable();
            $table->time('lunch_break_start')->nullable();
            $table->time('lunch_break_end')->nullable();
            $table->json('no_delivery_periods')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            
            // Performance indexes
            $table->index(['customer_id', 'is_active']);
            $table->index('postcode');
            $table->index(['latitude', 'longitude']);
            $table->index(['customer_id', 'is_default']);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};