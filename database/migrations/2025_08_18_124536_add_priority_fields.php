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
        Schema::table('customers', function (Blueprint $table) {
            $table->integer('priority_level')->default(0)->after('name')->comment('Priority level for tipping queue (0=normal, 1-10=high priority)');
            $table->text('priority_notes')->nullable()->after('priority_level')->comment('Notes about why customer has priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['priority_level', 'priority_notes']);
        });
    }
};