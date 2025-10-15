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
        Schema::table('slots', function (Blueprint $table) {
            $table->foreignId('tipping_bay_id')->nullable()->after('depot_id')->constrained()->onDelete('cascade');
            $table->index(['tipping_bay_id', 'start_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slots', function (Blueprint $table) {
            $table->dropForeign(['tipping_bay_id']);
            $table->dropIndex(['tipping_bay_id', 'start_at']);
            $table->dropColumn('tipping_bay_id');
        });
    }
};
