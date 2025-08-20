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
        Schema::table('tipping_bays', function (Blueprint $table) {
            $table->decimal('map_x', 5, 2)->nullable()->comment('X coordinate on depot map (percentage)');
            $table->decimal('map_y', 5, 2)->nullable()->comment('Y coordinate on depot map (percentage)');
            $table->boolean('show_on_map')->default(true)->comment('Whether to display this bay on the depot map');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipping_bays', function (Blueprint $table) {
            $table->dropColumn(['map_x', 'map_y', 'show_on_map']);
        });
    }
};
