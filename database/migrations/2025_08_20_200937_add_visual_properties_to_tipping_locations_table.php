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
        Schema::table('tipping_locations', function (Blueprint $table) {
            // Add styling fields only if they don't already exist
            if (!Schema::hasColumn('tipping_locations', 'map_width')) {
                $table->unsignedInteger('map_width')->nullable()->comment('Width in pixels for map display');
            }
            if (!Schema::hasColumn('tipping_locations', 'map_height')) {
                $table->unsignedInteger('map_height')->nullable()->comment('Height in pixels for map display');
            }
            if (!Schema::hasColumn('tipping_locations', 'map_rotation')) {
                $table->decimal('map_rotation', 5, 2)->nullable()->default(0)->comment('Rotation angle in degrees');
            }
            if (!Schema::hasColumn('tipping_locations', 'text_size')) {
                $table->enum('text_size', ['xs', 'sm', 'md', 'lg'])->nullable()->default('xs')->comment('Text size for location label');
            }
            if (!Schema::hasColumn('tipping_locations', 'text_color')) {
                $table->string('text_color', 7)->nullable()->default('#ffffff')->comment('Hex color code for text');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipping_locations', function (Blueprint $table) {
            // Drop columns only if they exist
            $columnsToDrop = [];
            if (Schema::hasColumn('tipping_locations', 'map_width')) $columnsToDrop[] = 'map_width';
            if (Schema::hasColumn('tipping_locations', 'map_height')) $columnsToDrop[] = 'map_height';
            if (Schema::hasColumn('tipping_locations', 'map_rotation')) $columnsToDrop[] = 'map_rotation';
            if (Schema::hasColumn('tipping_locations', 'text_size')) $columnsToDrop[] = 'text_size';
            if (Schema::hasColumn('tipping_locations', 'text_color')) $columnsToDrop[] = 'text_color';
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
