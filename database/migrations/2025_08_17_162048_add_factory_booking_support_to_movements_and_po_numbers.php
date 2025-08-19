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
        // Add factory_booking_id to movements table
        Schema::table('movements', function (Blueprint $table) {
            $table->foreignId('factory_booking_id')->nullable()->constrained()->after('booking_id');
            // Make booking_id nullable since factory bookings use factory_booking_id
            $table->foreignId('booking_id')->nullable()->change();
        });

        // Add factory_booking_id to booking_po_numbers table
        Schema::table('booking_po_numbers', function (Blueprint $table) {
            $table->foreignId('factory_booking_id')->nullable()->constrained()->after('booking_id');
            // Make booking_id nullable since factory bookings use factory_booking_id
            $table->foreignId('booking_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movements', function (Blueprint $table) {
            $table->dropForeign(['factory_booking_id']);
            $table->dropColumn('factory_booking_id');
        });

        Schema::table('booking_po_numbers', function (Blueprint $table) {
            $table->dropForeign(['factory_booking_id']);
            $table->dropColumn('factory_booking_id');
        });
    }
};
