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
            $table->unsignedInteger('map_width')->nullable()->after('show_on_map')->comment('Width in pixels for map display');
            $table->unsignedInteger('map_height')->nullable()->after('map_width')->comment('Height in pixels for map display');
            $table->decimal('map_rotation', 5, 2)->nullable()->default(0)->after('map_height')->comment('Rotation angle in degrees');
            $table->enum('text_size', ['xs', 'sm', 'md', 'lg'])->nullable()->default('xs')->after('map_rotation')->comment('Text size for location label');
            $table->string('text_color', 7)->nullable()->default('#ffffff')->after('text_size')->comment('Hex color code for text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipping_locations', function (Blueprint $table) {
            $table->dropColumn(['map_width', 'map_height', 'map_rotation', 'text_size', 'text_color']);
        });
    }
};
