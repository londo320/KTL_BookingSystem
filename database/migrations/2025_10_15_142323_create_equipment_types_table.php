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
        Schema::create('equipment_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Display name like "Ramp", "Dock Leveler"');
            $table->string('key')->unique()->comment('Machine-readable key like "ramp", "dock_leveler"');
            $table->text('description')->nullable()->comment('Description of what this equipment is used for');
            $table->boolean('is_active')->default(true)->comment('Whether this equipment type is active');
            $table->integer('sort_order')->default(0)->comment('Display order in dropdowns');
            $table->timestamps();
        });

        // Seed with existing equipment types from the hardcoded list
        DB::table('equipment_types')->insert([
            ['name' => 'Ramp', 'key' => 'ramp', 'description' => 'Loading/unloading ramp', 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dock Leveler', 'key' => 'dock_leveler', 'description' => 'Adjustable dock leveler', 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Forklift', 'key' => 'forklift', 'description' => 'Forklift access required', 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pallet Jack', 'key' => 'pallet_jack', 'description' => 'Pallet jack available', 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cold Storage', 'key' => 'cold_storage', 'description' => 'Refrigerated storage area', 'is_active' => true, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Freezer', 'key' => 'freezer', 'description' => 'Frozen storage area', 'is_active' => true, 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Scale', 'key' => 'scale', 'description' => 'Weighing scale', 'is_active' => true, 'sort_order' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Loading Bay', 'key' => 'loading_bay', 'description' => 'Designated loading bay', 'is_active' => true, 'sort_order' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Unloading Bay', 'key' => 'unloading_bay', 'description' => 'Designated unloading bay', 'is_active' => true, 'sort_order' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Inspection Area', 'key' => 'inspection_area', 'description' => 'Quality inspection area', 'is_active' => true, 'sort_order' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Secure Storage', 'key' => 'secure_storage', 'description' => 'Secure/locked storage', 'is_active' => true, 'sort_order' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hazmat Certified', 'key' => 'hazmat_certified', 'description' => 'Hazardous materials certified', 'is_active' => true, 'sort_order' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Handball', 'key' => 'handball', 'description' => 'Manual handball unloading', 'is_active' => true, 'sort_order' => 13, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_types');
    }
};
