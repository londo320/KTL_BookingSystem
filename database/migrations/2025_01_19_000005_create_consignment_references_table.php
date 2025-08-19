<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consignment_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consignment_id')->constrained('consignments')->onDelete('cascade');
            $table->enum('reference_type', [
                'customer_ref', 'delivery_note', 'invoice', 'po_number',
                'collection_note', 'manifest', 'other',
            ]);
            $table->string('reference_value', 100);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['consignment_id', 'reference_type']);
            $table->index('reference_value');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consignment_references');
    }
};
