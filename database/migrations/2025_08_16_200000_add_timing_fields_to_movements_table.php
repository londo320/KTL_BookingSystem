<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movements', function (Blueprint $table) {
            $table->timestamp('moved_to_bay_at')->nullable()->after('unloading_completed_at');
            $table->timestamp('moved_to_location_at')->nullable()->after('moved_to_bay_at');
        });
    }

    public function down(): void
    {
        Schema::table('movements', function (Blueprint $table) {
            $table->dropColumn(['moved_to_bay_at', 'moved_to_location_at']);
        });
    }
};