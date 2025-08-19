<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove quantity columns from booking_po_numbers table
        Schema::table('booking_po_numbers', function (Blueprint $table) {
            $table->dropColumn([
                'expected_cases',
                'expected_pallets',
                'expected_pallet_type',
                'actual_cases',
                'actual_pallets',
                'actual_pallet_type',
                'sku',
                'description',
                'bbe',
                'qty',
                'batch',
                'scc_number',
            ]);
        });

        // Remove quantity columns from bookings table
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'expected_cases',
                'actual_cases',
                'case_variance',
                'expected_pallets',
                'actual_pallets',
                'pallet_variance',
            ]);
        });
    }

    public function down(): void
    {
        // Add back quantity columns to booking_po_numbers table
        Schema::table('booking_po_numbers', function (Blueprint $table) {
            $table->integer('expected_cases')->nullable();
            $table->integer('expected_pallets')->nullable();
            $table->string('expected_pallet_type')->nullable();
            $table->integer('actual_cases')->nullable();
            $table->integer('actual_pallets')->nullable();
            $table->string('actual_pallet_type')->nullable();
            $table->string('sku')->nullable();
            $table->text('description')->nullable();
            $table->date('bbe')->nullable();
            $table->integer('qty')->nullable();
            $table->string('batch')->nullable();
            $table->string('scc_number')->nullable();
        });

        // Add back quantity columns to bookings table
        Schema::table('bookings', function (Blueprint $table) {
            $table->integer('expected_cases')->nullable();
            $table->integer('actual_cases')->nullable();
            $table->integer('case_variance')->nullable();
            $table->integer('expected_pallets')->nullable();
            $table->integer('actual_pallets')->nullable();
            $table->integer('pallet_variance')->nullable();
        });
    }
};
