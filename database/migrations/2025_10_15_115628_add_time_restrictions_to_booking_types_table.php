<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_types', function (Blueprint $table) {
            $table->time('booking_start_time')->nullable()->after('duration_minutes')->comment('Earliest time of day this booking type can be booked (e.g., 08:00)');
            $table->time('booking_end_time')->nullable()->after('booking_start_time')->comment('Latest time of day this booking type can be booked (e.g., 17:00)');
        });
    }

    public function down(): void
    {
        Schema::table('booking_types', function (Blueprint $table) {
            $table->dropColumn(['booking_start_time', 'booking_end_time']);
        });
    }
};
