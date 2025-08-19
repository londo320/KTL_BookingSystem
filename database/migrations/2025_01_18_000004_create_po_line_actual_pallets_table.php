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
        Schema::create('po_line_actual_pallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('po_line_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pallet_type_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['po_line_id', 'pallet_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_line_actual_pallets');
    }
};
