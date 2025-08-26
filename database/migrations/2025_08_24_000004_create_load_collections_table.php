<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('load_collections', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('outbound_load_id')->constrained()->cascadeOnDelete();
            $table->foreignId('depot_id')->constrained();
            
            // Collection scheduling
            $table->timestamp('planned_collection_time');
            $table->timestamp('actual_collection_time')->nullable();
            $table->integer('estimated_duration_minutes')->default(30);
            $table->integer('actual_duration_minutes')->nullable();
            
            // Collection details
            $table->integer('collection_sequence')->nullable(); // Order of depot visits
            $table->text('collection_notes')->nullable();
            
            // Collection totals for this depot
            $table->integer('depot_pallets')->default(0);
            $table->integer('depot_cases')->default(0);
            $table->integer('depot_units')->default(0);
            $table->decimal('depot_weight_kg', 10, 2)->nullable();
            
            // Collection status
            $table->enum('status', ['pending', 'ready', 'collecting', 'collected', 'failed'])->default('pending');
            
            // Performance indexes
            $table->index(['outbound_load_id', 'depot_id']);
            $table->index('planned_collection_time');
            $table->index(['outbound_load_id', 'collection_sequence']);
            $table->index('status');
            
            $table->timestamps();
            
            $table->unique(['outbound_load_id', 'depot_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('load_collections');
    }
};