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
        Schema::create('customer_bay_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('tipping_bay_id')->constrained()->onDelete('cascade');
            $table->integer('priority')->default(0)->comment('Higher = preferred. 0 = allowed but not preferred');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Unique constraint: one assignment per customer-bay combination
            $table->unique(['customer_id', 'tipping_bay_id']);

            $table->index(['customer_id', 'is_active']);
            $table->index(['tipping_bay_id', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_bay_assignments');
    }
};
