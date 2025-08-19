<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: First expand enum to include 'drop'
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('tipping_type');
        });
        
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('tipping_type', ['live_tip', 'drop_and_go', 'drop_swap', 'drop'])
                  ->nullable()
                  ->after('priority_notes')
                  ->comment('Temporary expanded enum');
        });
        
        // Step 2: Update existing values to use 'drop'
        DB::statement("UPDATE bookings SET tipping_type = 'drop' WHERE tipping_type IN ('drop_and_go', 'drop_swap')");
        
        // Step 3: Simplify to final 2-option enum
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('tipping_type');
        });
        
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('tipping_type', ['live_tip', 'drop'])
                  ->nullable()
                  ->after('priority_notes')
                  ->comment('Simple tipping type: live_tip (unit stays) or drop (unit leaves)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the original 3-option enum
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('tipping_type');
        });
        
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('tipping_type', ['live_tip', 'drop_and_go', 'drop_swap'])
                  ->nullable()
                  ->after('priority_notes')
                  ->comment('How the tipping will be handled - set by operator at arrival');
        });
    }
};
