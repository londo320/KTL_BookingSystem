<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tipping_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('depot_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., "Drop Zone A", "Trailer Park 1"
            $table->string('code')->nullable(); // Short code like "DZ-A"
            $table->text('description')->nullable();
            $table->integer('capacity')->default(5); // How many trailers can be dropped here
            $table->boolean('is_active')->default(true);
            $table->json('coordinates')->nullable(); // For mapping if needed
            $table->timestamps();

            $table->unique(['depot_id', 'code']);
            $table->index(['depot_id', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tipping_locations');
    }
};
