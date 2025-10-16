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
        // Get existing data
        $existingRequirements = DB::table('booking_type_equipment_requirements')->get();

        // Drop and recreate the table with new structure
        Schema::dropIfExists('booking_type_equipment_requirements');

        Schema::create('booking_type_equipment_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_type_id')->constrained()->onDelete('cascade');
            $table->string('equipment_type')->comment('Equipment type key (e.g., "ramp", "forklift")');
            $table->boolean('is_required')->default(true)->comment('Whether this equipment is required');
            $table->integer('priority_boost')->default(10)->comment('Priority points to add when bay has this equipment');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Unique constraint: one row per booking type per equipment type
            $table->unique(['booking_type_id', 'equipment_type'], 'bt_equip_req_unique');
        });

        // Migrate old data (expand JSON arrays into individual rows)
        foreach ($existingRequirements as $oldRequirement) {
            $requiredEquipment = json_decode($oldRequirement->required_equipment, true);
            if (!empty($requiredEquipment) && is_array($requiredEquipment)) {
                foreach ($requiredEquipment as $equipment) {
                    DB::table('booking_type_equipment_requirements')->insert([
                        'booking_type_id' => $oldRequirement->booking_type_id,
                        'equipment_type' => $equipment,
                        'is_required' => true,
                        'priority_boost' => 10,
                        'is_active' => $oldRequirement->is_active,
                        'created_at' => $oldRequirement->created_at,
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get current data
        $currentRequirements = DB::table('booking_type_equipment_requirements')->get();

        // Group by booking_type_id
        $grouped = [];
        foreach ($currentRequirements as $req) {
            if (!isset($grouped[$req->booking_type_id])) {
                $grouped[$req->booking_type_id] = [
                    'booking_type_id' => $req->booking_type_id,
                    'equipment' => [],
                    'is_active' => $req->is_active,
                    'created_at' => $req->created_at,
                ];
            }
            if ($req->is_required) {
                $grouped[$req->booking_type_id]['equipment'][] = $req->equipment_type;
            }
        }

        // Drop and recreate with old structure
        Schema::dropIfExists('booking_type_equipment_requirements');

        Schema::create('booking_type_equipment_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_type_id')->constrained()->onDelete('cascade');
            $table->json('required_equipment');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Restore old data
        foreach ($grouped as $data) {
            if (!empty($data['equipment'])) {
                DB::table('booking_type_equipment_requirements')->insert([
                    'booking_type_id' => $data['booking_type_id'],
                    'required_equipment' => json_encode($data['equipment']),
                    'is_active' => $data['is_active'],
                    'created_at' => $data['created_at'],
                    'updated_at' => now(),
                ]);
            }
        }
    }
};
