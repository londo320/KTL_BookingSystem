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
            $table->decimal('map_rotation', 5, 2)->nullable()->default(0)->comment('Rotation angle in degrees (0-360)');
            $table->integer('map_width')->nullable()->default(60)->comment('Width in pixels');
            $table->integer('map_height')->nullable()->default(40)->comment('Height in pixels');
            $table->enum('text_size', ['xs', 'sm', 'md', 'lg'])->default('xs')->comment('Text size for bay label');
            $table->string('text_color', 7)->default('#ffffff')->comment('Hex color for text (e.g., #ffffff)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipping_bays', function (Blueprint $table) {
            $table->dropColumn(['map_rotation', 'map_width', 'map_height', 'text_size', 'text_color']);
        });
    }
};
