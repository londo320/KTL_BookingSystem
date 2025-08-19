<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Add missing tipping fields
            if (!Schema::hasColumn('bookings', 'tipping_location_id')) {
                $table->foreignId('tipping_location_id')->nullable()->constrained()->nullOnDelete();
            }

            if (!Schema::hasColumn('bookings', 'tipping_bay_id')) {
                $table->foreignId('tipping_bay_id')->nullable()->constrained()->nullOnDelete();
            }
            
            if (!Schema::hasColumn('bookings', 'tipping_status')) {
                $table->enum('tipping_status', [
                    'not_started',     // Initial state
                    'trailer_dropped', // Trailer dropped at location
                    'moved_to_bay',    // Moved to tipping bay
                    'tipping_in_progress', // Currently being tipped
                    'tipping_completed',   // Tipping finished
                    'trailer_departed',     // Trailer left site
                ])->default('not_started');
            }

            // Timestamps for tipping workflow
            if (!Schema::hasColumn('bookings', 'trailer_dropped_at')) {
                $table->timestamp('trailer_dropped_at')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'moved_to_bay_at')) {
                $table->timestamp('moved_to_bay_at')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'tipping_started_at')) {
                $table->timestamp('tipping_started_at')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'tipping_completed_at')) {
                $table->timestamp('tipping_completed_at')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'trailer_departed_at')) {
                $table->timestamp('trailer_departed_at')->nullable();
            }

            // Tipping details
            if (!Schema::hasColumn('bookings', 'tipping_notes')) {
                $table->text('tipping_notes')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'actual_tipping_duration')) {
                $table->integer('actual_tipping_duration')->nullable(); // in minutes
            }
            if (!Schema::hasColumn('bookings', 'tipping_issues')) {
                $table->json('tipping_issues')->nullable(); // Any problems encountered
            }

            // Staff tracking
            if (!Schema::hasColumn('bookings', 'tipping_operator_id')) {
                $table->foreignId('tipping_operator_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('bookings', 'bay_assigned_by')) {
                $table->foreignId('bay_assigned_by')->nullable()->constrained('users')->nullOnDelete();
            }
        });

        // Add indexes after all columns are added
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'tipping_status') && Schema::hasColumn('bookings', 'tipping_bay_id')) {
                $table->index(['tipping_status']);
                $table->index(['tipping_location_id', 'tipping_status']);
                $table->index(['tipping_bay_id', 'tipping_status']);
            }
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drop indexes first
            if (Schema::hasColumn('bookings', 'tipping_status')) {
                $table->dropIndex(['tipping_status']);
                $table->dropIndex(['tipping_location_id', 'tipping_status']);
                $table->dropIndex(['tipping_bay_id', 'tipping_status']);
            }

            // Drop foreign keys
            if (Schema::hasColumn('bookings', 'tipping_location_id')) {
                $table->dropForeign(['tipping_location_id']);
            }
            if (Schema::hasColumn('bookings', 'tipping_bay_id')) {
                $table->dropForeign(['tipping_bay_id']);
            }
            if (Schema::hasColumn('bookings', 'tipping_operator_id')) {
                $table->dropForeign(['tipping_operator_id']);
            }
            if (Schema::hasColumn('bookings', 'bay_assigned_by')) {
                $table->dropForeign(['bay_assigned_by']);
            }

            // Drop columns
            $columns = [
                'tipping_location_id',
                'tipping_bay_id',
                'tipping_status',
                'trailer_dropped_at',
                'moved_to_bay_at',
                'tipping_started_at',
                'tipping_completed_at',
                'trailer_departed_at',
                'tipping_notes',
                'actual_tipping_duration',
                'tipping_issues',
                'tipping_operator_id',
                'bay_assigned_by',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};