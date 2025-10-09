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
            $table->string('supplier', 255)->nullable()->after('carrier_id');
            $table->string('haulier', 255)->nullable()->after('supplier');
            $table->string('contact_name', 255)->nullable()->after('haulier');
            $table->string('contact_phone', 50)->nullable()->after('contact_name');

            // Add index for contact lookups (to speed up autocomplete)
            $table->index(['supplier', 'contact_name']);
            $table->index(['haulier', 'contact_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['supplier', 'contact_name']);
            $table->dropIndex(['haulier', 'contact_name']);
            $table->dropColumn(['supplier', 'haulier', 'contact_name', 'contact_phone']);
        });
    }
};
