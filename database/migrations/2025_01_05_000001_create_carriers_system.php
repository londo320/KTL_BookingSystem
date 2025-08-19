<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Create carriers table
        Schema::create('carriers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_approval')->default(false);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['name', 'is_active']);
        });

        // Create depot-carrier configuration table
        Schema::create('depot_carrier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('depot_id')->constrained();
            $table->foreignId('carrier_id')->constrained();
            $table->boolean('is_enabled')->default(true);
            $table->boolean('auto_disable_unused')->default(true);
            $table->integer('auto_disable_months')->default(6);
            $table->json('allowed_customer_ids')->nullable();
            $table->timestamps();
            
            $table->unique(['depot_id', 'carrier_id']);
        });

        // Create carrier merge audit log
        Schema::create('carrier_merges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_carrier_id')->constrained('carriers');
            $table->string('source_carrier_name');
            $table->foreignId('target_carrier_id')->constrained('carriers');
            $table->string('target_carrier_name');
            $table->integer('bookings_moved');
            $table->json('depot_relationships_merged');
            $table->foreignId('merged_by')->constrained('users');
            $table->boolean('source_deleted');
            $table->timestamps();
        });

        // Add carrier_id to bookings table if it doesn't exist
        if (Schema::hasTable('bookings') && !Schema::hasColumn('bookings', 'carrier_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->foreignId('carrier_id')->nullable()->constrained('carriers');
                // Keep carrier_company as backup during transition
                $table->index('carrier_id');
            });
        }

        // Seed common carriers
        DB::table('carriers')->insert([
            ['name' => 'DHL', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'FedEx', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'UPS', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Royal Mail', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'TNT', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hermes', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Yodel', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('carrier_merges');
        Schema::dropIfExists('depot_carrier');
        
        if (Schema::hasTable('bookings') && Schema::hasColumn('bookings', 'carrier_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropForeign(['carrier_id']);
                $table->dropColumn('carrier_id');
            });
        }
        
        Schema::dropIfExists('carriers');
    }
};