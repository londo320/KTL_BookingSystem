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
        Schema::table('products', function (Blueprint $table) {
            // Drop the old unique constraint on sku
            $table->dropUnique('products_sku_unique');

            // Add customer_id column
            $table->foreignId('customer_id')->after('id')->constrained()->cascadeOnDelete();

            // Add new unique constraint on (customer_id, sku)
            $table->unique(['customer_id', 'sku'], 'products_customer_id_sku_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('products_customer_id_sku_unique');

            // Drop customer_id foreign key and column
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');

            // Restore the original unique constraint on sku
            $table->unique('sku', 'products_sku_unique');
        });
    }
};
