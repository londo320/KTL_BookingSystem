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
        Schema::table('depots', function (Blueprint $table) {
            $table->enum('slot_system_mode', ['depot_based', 'bay_based'])
                ->default('depot_based')
                ->after('name')
                ->comment('depot_based = traditional slot system, bay_based = slots tied to specific bays');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('depots', function (Blueprint $table) {
            $table->dropColumn('slot_system_mode');
        });
    }
};
