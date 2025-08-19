<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movements', function (Blueprint $table) {
            $table->foreign('consignment_id')->references('id')->on('consignments');
        });

        Schema::table('movement_loads', function (Blueprint $table) {
            // Add this constraint only if booking_po_lines table exists
            if (Schema::hasTable('booking_po_lines')) {
                $table->foreign('booking_po_line_id')->references('id')->on('booking_po_lines');
            }
        });
    }

    public function down(): void
    {
        Schema::table('movements', function (Blueprint $table) {
            $table->dropForeign(['consignment_id']);
        });

        Schema::table('movement_loads', function (Blueprint $table) {
            if (Schema::hasColumn('movement_loads', 'booking_po_line_id')) {
                $table->dropForeign(['booking_po_line_id']);
            }
        });
    }
};
