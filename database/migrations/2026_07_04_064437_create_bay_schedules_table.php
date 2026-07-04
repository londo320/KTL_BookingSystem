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
        Schema::create('bay_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipping_bay_id')
                ->constrained('tipping_bays')
                ->onDelete('cascade');
            $table->tinyInteger('day_of_week')
                ->comment('0=Sunday, 1=Monday, 2=Tuesday, 3=Wednesday, 4=Thursday, 5=Friday, 6=Saturday');
            $table->time('operational_start')->nullable();
            $table->time('operational_end')->nullable();
            $table->boolean('is_closed')->default(false)
                ->comment('If true, bay is closed this day regardless of times');
            $table->timestamps();

            // Ensure one schedule per bay per day
            $table->unique(['tipping_bay_id', 'day_of_week'], 'unique_bay_day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bay_schedules');
    }
};
