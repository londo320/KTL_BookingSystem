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
        Schema::create('arrival_time_settings', function (Blueprint $table) {
            $table->id();
            $table->string('level'); // 'global', 'depot', 'customer'
            $table->unsignedBigInteger('depot_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->integer('early_threshold_minutes')->default(0); // More than X minutes early = early
            $table->integer('late_threshold_minutes')->default(0);  // More than X minutes late = late
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('depot_id')->references('id')->on('depots')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            
            // Indexes for performance
            $table->index(['level', 'is_active']);
            $table->index(['depot_id', 'is_active']);
            $table->index(['customer_id', 'is_active']);
            
            // Unique constraint to prevent duplicate active settings at same level
            $table->unique(['level', 'depot_id', 'customer_id'], 'unique_active_setting');
        });
        
        // Insert default global settings
        DB::table('arrival_time_settings')->insert([
            'level' => 'global',
            'depot_id' => null,
            'customer_id' => null,
            'early_threshold_minutes' => 0,
            'late_threshold_minutes' => 0,
            'description' => 'Default global arrival time rules: exact time only (no tolerance)',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arrival_time_settings');
    }
};