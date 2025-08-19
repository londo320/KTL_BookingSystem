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
        Schema::table('bookings', function (Blueprint $table) {
            // Remove driver fields due to language barriers in operations
            $table->dropColumn([
                'driver_name',
                'driver_phone',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Restore driver fields if migration needs to be rolled back
            $table->string('driver_name', 100)->nullable()->after('trailer_number');
            $table->string('driver_phone', 20)->nullable()->after('driver_name');
        });
    }
};
