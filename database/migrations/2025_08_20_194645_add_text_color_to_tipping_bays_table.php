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
        // This migration is redundant - text_color field is already added by 
        // 2025_08_20_194548_add_visual_properties_to_tipping_bays_table migration
        // Keeping this migration file for backward compatibility but doing nothing
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration doesn't add anything, so there's nothing to rollback
        // The text_color field is managed by the main visual properties migration
    }
};
