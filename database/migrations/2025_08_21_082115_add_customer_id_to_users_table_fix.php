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
        Schema::table('users', function (Blueprint $table) {
            // Only add the column if it doesn't already exist
            if (!Schema::hasColumn('users', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete()->after('depot_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Only drop if the column exists
            if (Schema::hasColumn('users', 'customer_id')) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            }
        });
    }
};
