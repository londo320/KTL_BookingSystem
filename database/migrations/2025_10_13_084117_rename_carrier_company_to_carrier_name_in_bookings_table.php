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
            // Only rename if carrier_company exists and carrier_name doesn't
            if (Schema::hasColumn('bookings', 'carrier_company') && !Schema::hasColumn('bookings', 'carrier_name')) {
                $table->renameColumn('carrier_company', 'carrier_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->renameColumn('carrier_name', 'carrier_company');
        });
    }
};
