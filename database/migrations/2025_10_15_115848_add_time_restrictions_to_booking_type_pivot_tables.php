<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add time restrictions to depot pivot
        Schema::table('booking_type_depot', function (Blueprint $table) {
            $table->time('booking_start_time')->nullable()->after('duration_minutes')->comment('Override start time for this depot');
            $table->time('booking_end_time')->nullable()->after('booking_start_time')->comment('Override end time for this depot');
        });

        // Add time restrictions to customer pivot
        Schema::table('booking_type_customer', function (Blueprint $table) {
            $table->time('booking_start_time')->nullable()->after('duration_minutes')->comment('Override start time for this customer');
            $table->time('booking_end_time')->nullable()->after('booking_start_time')->comment('Override end time for this customer');
        });
    }

    public function down(): void
    {
        Schema::table('booking_type_depot', function (Blueprint $table) {
            $table->dropColumn(['booking_start_time', 'booking_end_time']);
        });

        Schema::table('booking_type_customer', function (Blueprint $table) {
            $table->dropColumn(['booking_start_time', 'booking_end_time']);
        });
    }
};
