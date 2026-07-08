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
            $table->enum('requested_account_type', ['knowles', 'customer'])->nullable()->after('email');
            $table->json('requested_depot_ids')->nullable()->after('requested_account_type');
            $table->json('requested_customer_ids')->nullable()->after('requested_depot_ids');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['requested_account_type', 'requested_depot_ids', 'requested_customer_ids']);
        });
    }
};
