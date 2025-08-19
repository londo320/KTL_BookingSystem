<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movements', function (Blueprint $table) {
            // Unit departure timing (when the original unit leaves)
            $table->timestamp('unit_departed_at')->nullable()->after('actual_departure');
            
            // Collection unit timing (for dropped trailers)
            $table->timestamp('collection_unit_arrived_at')->nullable()->after('unit_departed_at');
            $table->timestamp('collection_unit_departed_at')->nullable()->after('collection_unit_arrived_at');
            
            // Collection unit details
            $table->string('collection_unit_registration', 50)->nullable()->after('collection_unit_departed_at');
            $table->string('collection_driver_name', 100)->nullable()->after('collection_unit_registration');
            $table->string('collection_driver_phone', 20)->nullable()->after('collection_driver_name');
            $table->text('collection_notes')->nullable()->after('collection_driver_phone');
        });
    }

    public function down(): void
    {
        Schema::table('movements', function (Blueprint $table) {
            $table->dropColumn([
                'unit_departed_at',
                'collection_unit_arrived_at', 
                'collection_unit_departed_at',
                'collection_unit_registration',
                'collection_driver_name',
                'collection_driver_phone',
                'collection_notes'
            ]);
        });
    }
};