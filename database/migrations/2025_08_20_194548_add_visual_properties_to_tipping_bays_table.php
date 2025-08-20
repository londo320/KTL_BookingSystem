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
            // Add styling fields only if they don't already exist
            if (!Schema::hasColumn('tipping_bays', 'map_rotation')) {
                $table->decimal('map_rotation', 5, 2)->nullable()->default(0)->comment('Rotation angle in degrees (0-360)');
            }
            if (!Schema::hasColumn('tipping_bays', 'map_width')) {
                $table->integer('map_width')->nullable()->default(60)->comment('Width in pixels');
            }
            if (!Schema::hasColumn('tipping_bays', 'map_height')) {
                $table->integer('map_height')->nullable()->default(40)->comment('Height in pixels');
            }
            if (!Schema::hasColumn('tipping_bays', 'text_size')) {
                $table->enum('text_size', ['xs', 'sm', 'md', 'lg'])->default('xs')->comment('Text size for bay label');
            }
            if (!Schema::hasColumn('tipping_bays', 'text_color')) {
                $table->string('text_color', 7)->default('#ffffff')->comment('Hex color for text (e.g., #ffffff)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipping_bays', function (Blueprint $table) {
            // Drop columns only if they exist
            $columnsToDrop = [];
            if (Schema::hasColumn('tipping_bays', 'map_rotation')) $columnsToDrop[] = 'map_rotation';
            if (Schema::hasColumn('tipping_bays', 'map_width')) $columnsToDrop[] = 'map_width';
            if (Schema::hasColumn('tipping_bays', 'map_height')) $columnsToDrop[] = 'map_height';
            if (Schema::hasColumn('tipping_bays', 'text_size')) $columnsToDrop[] = 'text_size';
            if (Schema::hasColumn('tipping_bays', 'text_color')) $columnsToDrop[] = 'text_color';
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
