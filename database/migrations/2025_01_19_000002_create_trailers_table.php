<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trailers', function (Blueprint $table) {
            $table->id();
            $table->string('trailer_number', 50)->unique();
            $table->enum('trailer_type', ['container', 'curtain_sider', 'flatbed', 'box', 'tank', 'other'])->default('container');
            $table->string('size', 20)->nullable(); // 20ft, 40ft, 45ft, etc.
            $table->integer('capacity_pallets')->nullable();
            $table->integer('capacity_weight_kg')->nullable();
            $table->boolean('temperature_controlled')->default(false);
            $table->string('owner', 100)->nullable(); // Trailer ownership
            $table->json('additional_data')->nullable(); // For future unknown fields
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('trailer_number');
            $table->index('trailer_type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trailers');
    }
};
