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
            // First drop the foreign key
            $table->dropForeign('bookings_slot_id_foreign');
            // Then drop the unique constraint
            $table->dropUnique('bookings_slot_id_user_id_unique');
            // Re-add the foreign key without the unique constraint
            $table->foreign('slot_id')->references('id')->on('slots')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drop the foreign key
            $table->dropForeign(['slot_id']);
            // Re-add the unique constraint
            $table->unique(['slot_id', 'user_id'], 'bookings_slot_id_user_id_unique');
            // Re-add the foreign key
            $table->foreign('slot_id', 'bookings_slot_id_foreign')->references('id')->on('slots')->onDelete('cascade');
        });
    }
};
