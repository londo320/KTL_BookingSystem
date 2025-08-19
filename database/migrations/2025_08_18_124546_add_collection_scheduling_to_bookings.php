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
            $table->datetime('collection_scheduled_at')->nullable()->after('tipping_completed_at')->comment('When trailer collection is scheduled');
            $table->integer('manual_priority_boost')->default(0)->after('collection_scheduled_at')->comment('Manual priority boost points (can be negative)');
            $table->text('priority_notes')->nullable()->after('manual_priority_boost')->comment('Notes about manual priority adjustments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['collection_scheduled_at', 'manual_priority_boost', 'priority_notes']);
        });
    }
};