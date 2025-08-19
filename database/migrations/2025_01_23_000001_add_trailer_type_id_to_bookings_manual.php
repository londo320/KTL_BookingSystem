<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('bookings', 'trailer_type_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->foreignId('trailer_type_id')->nullable()->constrained('trailer_types');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('bookings', 'trailer_type_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropForeign(['trailer_type_id']);
                $table->dropColumn('trailer_type_id');
            });
        }
    }
};
