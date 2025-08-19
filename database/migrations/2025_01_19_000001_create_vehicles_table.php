<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('registration', 50)->unique();
            $table->enum('vehicle_type', ['tractor', 'rigid', 'van', 'other'])->default('tractor');
            $table->string('carrier_company', 100)->nullable();
            $table->string('default_driver_name', 100)->nullable();
            $table->string('default_driver_phone', 20)->nullable();
            $table->json('additional_data')->nullable(); // For future unknown fields
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('registration');
            $table->index('carrier_company');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
