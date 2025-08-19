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
            // Rebooking tracking
            $table->foreignId('original_booking_id')->nullable()->constrained('bookings')->after('booking_reference');
            $table->string('rebook_reason')->nullable()->after('original_booking_id');
            $table->timestamp('cancelled_at')->nullable()->after('rebook_reason');
            $table->string('cancellation_reason')->nullable()->after('cancelled_at');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->after('cancellation_reason');

            // Behavioral flags
            $table->boolean('is_rebooked')->default(false)->after('cancelled_by');
            $table->integer('rebook_count')->default(0)->after('is_rebooked'); // How many times this booking chain has been rebooked
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by']);
            $table->dropForeign(['original_booking_id']);
            $table->dropColumn([
                'original_booking_id',
                'rebook_reason',
                'cancelled_at',
                'cancellation_reason',
                'cancelled_by',
                'is_rebooked',
                'rebook_count',
            ]);
        });
    }
};
