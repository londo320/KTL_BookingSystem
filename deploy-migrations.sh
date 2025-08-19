#!/bin/bash

# Production Migration Deployment Script
# Ensures migrations run in correct dependency order

echo "🚀 Starting production migration deployment..."

# Core foundation tables (no dependencies)
echo "📋 Step 1: Core foundation tables"
php artisan migrate --path=database/migrations/2025_01_01_000000_create_depots_table.php --force
php artisan migrate --path=database/migrations/2025_01_01_000001_create_users_table.php --force
php artisan migrate --path=database/migrations/2025_06_30_214246_create_customers_table.php --force
php artisan migrate --path=database/migrations/2025_01_01_000002_create_booking_types_table.php --force

# System tables (cache, sessions)
echo "📋 Step 2: System tables"
php artisan migrate --path=database/migrations/2025_06_24_143904_create_sessions_table.php --force
php artisan migrate --path=database/migrations/2025_06_24_144236_create_cache_table.php --force
php artisan migrate --path=database/migrations/2025_07_06_120039_create_password_reset_tokens_table.php --force

# Permission/Role system
echo "📋 Step 3: Permission system"
# Note: This may fail if tables exist - that's OK
php artisan migrate --path=database/migrations/2025_06_29_172838_create_permission_tables.php --force 2>/dev/null || echo "   ⚠️  Permission tables already exist (OK)"

# Core business tables
echo "📋 Step 4: Core business tables"
php artisan migrate --path=database/migrations/2025_01_01_000003_create_slots_table.php --force
php artisan migrate --path=database/migrations/2025_01_01_000004_create_bookings_table.php --force

# Carrier system
echo "📋 Step 5: Carrier system"
# Note: This may fail if table exists - that's OK
php artisan migrate --path=database/migrations/2024_01_15_000001_create_carriers_system.php --force 2>/dev/null || echo "   ⚠️  Carriers table already exists (OK)"

# Relationship tables
echo "📋 Step 6: Relationship tables"
php artisan migrate --path=database/migrations/2025_07_04_182151_create_depot_user_table.php --force
php artisan migrate --path=database/migrations/2025_08_04_120000_create_customer_user_table.php --force

# Soft delete columns
echo "📋 Step 7: Soft delete columns"
php artisan migrate --path=database/migrations/2025_07_07_181732_add_deleted_at_to_depots_table.php --force
php artisan migrate --path=database/migrations/2025_07_07_181919_add_deleted_at_to_users_table.php --force
php artisan migrate --path=database/migrations/2025_07_07_183641_add_deleted_at_to_customers_table.php --force
php artisan migrate --path=database/migrations/2025_07_07_182421_add_deleted_at_to_booking_types_table.php --force
php artisan migrate --path=database/migrations/2025_07_06_001009_add_deleted_at_to_slots_table.php --force
php artisan migrate --path=database/migrations/2025_07_07_181735_add_deleted_at_to_bookings_table.php --force

# User/Customer relationships
echo "📋 Step 8: User relationships"
php artisan migrate --path=database/migrations/2025_06_30_214449_add_customer_id_to_users_table.php --force

# Products and depot configurations
echo "📋 Step 9: Products and configurations"
php artisan migrate --path=database/migrations/2025_07_01_170627_create_products_and_booking_product_tables.php --force
php artisan migrate --path=database/migrations/2025_07_01_171942_create_depot_product_table.php --force
php artisan migrate --path=database/migrations/2025_07_01_185252_create_customer_depot_product_table.php --force
php artisan migrate --path=database/migrations/2025_07_01_213934_create_depot_case_ranges_table.php --force

# Booking enhancements
echo "📋 Step 10: Booking enhancements"
php artisan migrate --path=database/migrations/2025_06_25_000000_add_case_and_size_to_bookings_table.php --force
php artisan migrate --path=database/migrations/2025_06_26_225152_add_details_to_bookings_table.php --force
php artisan migrate --path=database/migrations/2025_06_29_094425_add_arrival_departure_to_bookings_table.php --force
php artisan migrate --path=database/migrations/2025_07_03_184941_add_customer_id_to_bookings_table.php --force
php artisan migrate --path=database/migrations/2025_07_03_193030_add_expected_actual_fields_to_bookings_table.php --force
php artisan migrate --path=database/migrations/2025_07_03_210203_edit_expected_actual_fields_to_bookings_table.php --force
php artisan migrate --path=database/migrations/2025_07_03_222151_add_status_to_bookings_table.php --force
php artisan migrate --path=database/migrations/2025_07_03_225327_add_end_time_to_bookings_table.php --force

# Slot enhancements
echo "📋 Step 11: Slot system enhancements"
php artisan migrate --path=database/migrations/2025_06_25_225157_create_slot_templates_table.php --force
php artisan migrate --path=database/migrations/2025_06_29_080210_add_capacity_to_slots_table.php --force
php artisan migrate --path=database/migrations/2025_06_29_081807_add_duration_minutes_to_booking_types_table.php --force
php artisan migrate --path=database/migrations/2025_06_29_082546_create_booking_type_depot_table.php --force
php artisan migrate --path=database/migrations/2025_06_29_094218_create_slot_generation_settings_table.php --force
php artisan migrate --path=database/migrations/2025_06_30_211619_add_cut_off_time_to_depots_table.php --force

# Slot release system
echo "📋 Step 12: Slot release system"
php artisan migrate --path=database/migrations/2025_07_10_180021_create_slot_release_rules_table.php --force
php artisan migrate --path=database/migrations/2025_07_10_222631_create_slot_release_rule_customer.php --force
php artisan migrate --path=database/migrations/2025_07_18_114348_add_release_and_cutoff_to_slots.php --force
php artisan migrate --path=database/migrations/2025_07_18_130836_create_slot_customer_table.php --force

# Transportation system
echo "📋 Step 13: Transportation system"
php artisan migrate --path=database/migrations/2025_08_02_000001_add_transportation_fields_to_bookings_table.php --force
php artisan migrate --path=database/migrations/2025_08_02_000002_rename_trailer_number_to_container_number.php --force
php artisan migrate --path=database/migrations/2025_08_02_000003_update_container_number_index.php --force

# Trailer types system (new)
echo "📋 Step 14: Trailer types system"
php artisan migrate --path=database/migrations/2025_08_16_070201_create_trailer_types_table.php --force
# Skip trailer_type_id migration for now - will add manually
# Skip trailer_size migration - doesn't exist in fresh install  
# Skip remove trailer_size - doesn't exist in fresh install

# Booking history and behavior tracking
echo "📋 Step 15: Booking history and behavior tracking"
php artisan migrate --path=database/migrations/2025_08_08_164430_create_booking_history_table.php --force
php artisan migrate --path=database/migrations/2025_08_08_164623_add_rebooking_fields_to_bookings_table.php --force
php artisan migrate --path=database/migrations/2025_08_09_000001_create_customer_behavior_settings_table.php --force

# Arrival time settings (new critical feature)
echo "📋 Step 16: Arrival time settings system"
php artisan migrate --path=database/migrations/2025_08_16_082819_create_arrival_time_settings_table.php --force

# Tipping workflow system
echo "📋 Step 17: Tipping workflow system"
php artisan migrate --path=database/migrations/2025_08_09_000002_create_tipping_locations_table.php --force
php artisan migrate --path=database/migrations/2025_08_09_000003_create_tipping_bays_table.php --force
php artisan migrate --path=database/migrations/2025_08_09_000004_add_tipping_fields_to_bookings_table.php --force
php artisan migrate --path=database/migrations/2025_08_10_084925_add_tipping_workflow_enabled_to_settings.php --force
php artisan migrate --path=database/migrations/2025_08_11_174131_add_waiting_area_fields_to_bookings_table.php --force
php artisan migrate --path=database/migrations/2025_08_11_175057_add_trailer_collection_fields_to_bookings_table.php --force

# Vehicle and movement system  
echo "📋 Step 18: Vehicle and movement system"
php artisan migrate --path=database/migrations/2025_08_13_000001_create_vehicles_table.php --force
php artisan migrate --path=database/migrations/2025_08_13_000002_create_trailers_table.php --force
php artisan migrate --path=database/migrations/2025_08_13_000003_create_movements_table.php --force
php artisan migrate --path=database/migrations/2025_08_13_000004_create_consignments_table.php --force
php artisan migrate --path=database/migrations/2025_08_13_000005_create_consignment_references_table.php --force
# Skip consignment_loads - needs pallet_types first
# Skip movement_loads - needs pallet_types first

# PO and pallet system
echo "📋 Step 19: PO and pallet system"
php artisan migrate --path=database/migrations/2025_08_12_create_booking_po_numbers_table.php --force
php artisan migrate --path=database/migrations/2025_08_12_create_pallet_types_table.php --force
php artisan migrate --path=database/migrations/2025_08_12_create_po_lines_table.php --force
php artisan migrate --path=database/migrations/2025_08_14_212207_create_po_line_actual_pallets_table.php --force

# Now run the migrations that needed pallet_types
echo "📋 Step 19b: Consignment/Movement loads (after pallet_types)"
php artisan migrate --path=database/migrations/2025_08_13_000006_create_consignment_loads_table.php --force
php artisan migrate --path=database/migrations/2025_08_13_000007_create_movement_loads_table.php --force

# Vehicle details enhancements
echo "📋 Step 20: Vehicle details enhancements"
php artisan migrate --path=database/migrations/2025_08_14_165255_add_vehicle_details_to_bookings_table.php --force
php artisan migrate --path=database/migrations/2025_08_14_165406_add_vehicle_details_json_to_bookings_table.php --force
php artisan migrate --path=database/migrations/2025_08_15_073303_add_missing_tipping_bay_id_to_bookings_table.php --force

# Cleanup migrations
echo "📋 Step 21: Cleanup and optimization"
php artisan migrate --path=database/migrations/2025_08_12_061542_remove_driver_fields_from_bookings_table.php --force
php artisan migrate --path=database/migrations/2025_08_12_204307_make_container_size_nullable_in_bookings_table.php --force
php artisan migrate --path=database/migrations/2025_08_12_remove_quantity_columns_from_booking_tables.php --force
php artisan migrate --path=database/migrations/2025_08_12_remove_reference_and_gate_fields_from_bookings.php --force
php artisan migrate --path=database/migrations/2025_08_13_000008_remove_vehicle_fields_from_bookings_table.php --force

# Foreign key constraints
echo "📋 Step 22: Foreign key constraints"
php artisan migrate --path=database/migrations/2025_08_13_000009_add_foreign_key_constraints.php --force

echo "✅ All migrations completed successfully!"
echo ""
echo "🔧 Adding missing trailer_type_id column..."
php artisan tinker --execute="
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
if (!Schema::hasColumn('bookings', 'trailer_type_id')) {
    Schema::table('bookings', function (Blueprint \$table) {
        \$table->foreignId('trailer_type_id')->nullable()->constrained('trailer_types');
    });
    echo 'trailer_type_id added' . PHP_EOL;
} else {
    echo 'trailer_type_id already exists' . PHP_EOL;
}
"

echo ""
echo "🌱 Running essential seeders..."
php artisan db:seed --class=RoleSeeder --force
php artisan db:seed --class=DepotSeeder --force
php artisan db:seed --class=BookingTypeSeeder --force
php artisan db:seed --class=TrailerTypeSeeder --force
php artisan db:seed --class=ArrivalTimeSettingsSeeder --force
# Skip UserSeeder if users already exist
php artisan tinker --execute="
if (\App\Models\User::where('email', 'admin@example.com')->doesntExist()) {
    \Artisan::call('db:seed', ['--class' => 'UserSeeder', '--force' => true]);
    echo 'Users seeded' . PHP_EOL;
} else {
    echo 'Admin user already exists - skipping UserSeeder' . PHP_EOL;
}
"

echo "✅ Production deployment complete!"
echo ""
echo "🔑 Default admin login:"
echo "   Email: admin@example.com"
echo "   Password: password"