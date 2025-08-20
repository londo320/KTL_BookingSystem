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
            $table->string('map_file')->nullable()->comment('Filename of the depot map (stored in public/images/depot-maps/)');
            $table->text('map_notes')->nullable()->comment('Notes about the depot map layout');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('depots', function (Blueprint $table) {
            $table->dropColumn(['map_file', 'map_notes']);
        });
    }
};
