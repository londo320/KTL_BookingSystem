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
            $table->enum('tipping_type', ['live_tip', 'drop_and_go', 'drop_swap'])
                  ->nullable()
                  ->after('priority_notes')
                  ->comment('How the tipping will be handled - set by operator at arrival');
            
            $table->foreignId('swap_trailer_id')
                  ->nullable()
                  ->after('tipping_type')
                  ->constrained('trailers')
                  ->nullOnDelete()
                  ->comment('Empty trailer selected for swap (drop_swap only)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['swap_trailer_id']);
            $table->dropColumn(['tipping_type', 'swap_trailer_id']);
        });
    }
};
