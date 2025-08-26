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
        Schema::table('factory_bookings', function (Blueprint $table) {
            $table->enum('tipping_type', ['live_tip', 'drop'])->nullable()->after('trailer_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('factory_bookings', function (Blueprint $table) {
            $table->dropColumn('tipping_type');
        });
    }
};
