<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pallet_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code', 10)->unique(); // Short code like EUR, UK, CP, etc.
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default pallet types
        DB::table('pallet_types')->insert([
            ['name' => 'Euro Pallet', 'code' => 'EUR', 'description' => 'Standard European pallet (1200x800mm)', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'UK Pallet', 'code' => 'UK', 'description' => 'Standard UK pallet (1200x1000mm)', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'CP Pallet', 'code' => 'CP', 'description' => 'Chep pallet', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'LPR Pallet', 'code' => 'LPR', 'description' => 'LPR pallet', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Block Pallet', 'code' => 'BLOCK', 'description' => 'Block pallet', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Red Pallet', 'code' => 'RED', 'description' => 'Red colored pallet', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Blue Pallet', 'code' => 'BLUE', 'description' => 'Blue colored pallet', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'GKN Pallet', 'code' => 'GKN', 'description' => 'GKN pallet', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pallet_types');
    }
};
