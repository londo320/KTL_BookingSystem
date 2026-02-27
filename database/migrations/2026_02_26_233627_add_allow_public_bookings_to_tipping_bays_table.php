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
            $table->boolean('allow_public_bookings')->default(false)->after('is_active')
                ->comment('When true, customer restrictions are ignored once slots are publicly released');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipping_bays', function (Blueprint $table) {
            $table->dropColumn('allow_public_bookings');
        });
    }
};
