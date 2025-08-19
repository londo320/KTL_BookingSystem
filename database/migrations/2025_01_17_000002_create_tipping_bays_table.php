<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tipping_bays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('depot_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., "Bay 1", "Tipping Bay A"
            $table->string('code')->nullable(); // Short code like "BAY-1"
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_occupied')->default(false);
            $table->json('equipment')->nullable(); // What equipment is available
            $table->timestamps();

            $table->unique(['depot_id', 'code']);
            $table->index(['depot_id', 'is_active', 'is_occupied']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tipping_bays');
    }
};
