<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customer_behavior_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('setting_key'); // e.g., 'max_rebooks_per_booking', 'max_last_minute_rebooks_30days', etc.
            $table->string('setting_value'); // Store as string, cast as needed
            $table->string('setting_type')->default('integer'); // integer, boolean, string, float
            $table->text('description')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->unique(['customer_id', 'setting_key']);
            $table->index(['customer_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_behavior_settings');
    }
};
