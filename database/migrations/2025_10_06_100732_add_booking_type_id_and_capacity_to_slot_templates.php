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
        Schema::table('slot_templates', function (Blueprint $table) {
            $table->foreignId('booking_type_id')->nullable()->after('depot_id')->constrained()->nullOnDelete();
            $table->unsignedInteger('capacity')->default(1)->after('duration_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slot_templates', function (Blueprint $table) {
            $table->dropForeign(['booking_type_id']);
            $table->dropColumn(['booking_type_id', 'capacity']);
        });
    }
};
