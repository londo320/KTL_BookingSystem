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
            $table->time('operational_start')->nullable()->after('is_active')->comment('Start of operational hours (e.g., 08:00)');
            $table->time('operational_end')->nullable()->after('operational_start')->comment('End of operational hours (e.g., 17:00)');
            $table->json('operational_days')->nullable()->after('operational_end')->comment('Days of week bay is operational - null means all days');
            $table->boolean('is_24_hour')->default(true)->after('operational_days')->comment('True if bay operates 24/7');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipping_bays', function (Blueprint $table) {
            $table->dropColumn(['operational_start', 'operational_end', 'operational_days', 'is_24_hour']);
        });
    }
};
