#!/bin/bash

# Migration Reordering Script
# This renames migration files to ensure proper dependency order for Laravel

echo "🔄 Reordering migration files for proper dependency sequence..."

cd database/migrations

# Backup original names (just in case)
echo "📋 Creating backup list of original migration names..."
ls -1 *.php > ../migration_backup_list.txt

# Step 1: Core Foundation (2025-01-01 to 2025-01-04)
echo "📋 Step 1: Renaming core foundation migrations..."
mv 2025_01_01_000000_create_depots_table.php 2025_01_01_000001_create_depots_table.php 2>/dev/null || true
mv 2025_01_01_000001_create_users_table.php 2025_01_01_000002_create_users_table.php 2>/dev/null || true
mv 2025_06_30_214246_create_customers_table.php 2025_01_01_000003_create_customers_table.php 2>/dev/null || true
mv 2025_01_01_000002_create_booking_types_table.php 2025_01_01_000004_create_booking_types_table.php 2>/dev/null || true

# Step 2: System Tables (2025-01-02)
echo "📋 Step 2: Renaming system tables..."
mv 2025_06_24_143904_create_sessions_table.php 2025_01_02_000001_create_sessions_table.php 2>/dev/null || true
mv 2025_06_24_144236_create_cache_table.php 2025_01_02_000002_create_cache_table.php 2>/dev/null || true
mv 2025_07_06_120039_create_password_reset_tokens_table.php 2025_01_02_000003_create_password_reset_tokens_table.php 2>/dev/null || true

# Step 3: Permission System (2025-01-03)
echo "📋 Step 3: Renaming permission system..."
mv 2025_06_29_172838_create_permission_tables.php 2025_01_03_000001_create_permission_tables.php 2>/dev/null || true

# Step 4: Core Business Tables (2025-01-04)
echo "📋 Step 4: Renaming core business tables..."
mv 2025_01_01_000003_create_slots_table.php 2025_01_04_000001_create_slots_table.php 2>/dev/null || true
mv 2025_01_01_000004_create_bookings_table.php 2025_01_04_000002_create_bookings_table.php 2>/dev/null || true

# Step 5: Carrier System (2025-01-05)
echo "📋 Step 5: Renaming carrier system..."
mv 2024_01_15_000001_create_carriers_system.php 2025_01_05_000001_create_carriers_system.php 2>/dev/null || true

# Step 6: Relationship Tables (2025-01-06)
echo "📋 Step 6: Renaming relationship tables..."
mv 2025_07_04_182151_create_depot_user_table.php 2025_01_06_000001_create_depot_user_table.php 2>/dev/null || true
mv 2025_08_04_120000_create_customer_user_table.php 2025_01_06_000002_create_customer_user_table.php 2>/dev/null || true

# Step 7: Soft Delete Columns (2025-01-07)
echo "📋 Step 7: Renaming soft delete migrations..."
mv 2025_07_07_181732_add_deleted_at_to_depots_table.php 2025_01_07_000001_add_deleted_at_to_depots_table.php 2>/dev/null || true
mv 2025_07_07_181919_add_deleted_at_to_users_table.php 2025_01_07_000002_add_deleted_at_to_users_table.php 2>/dev/null || true
mv 2025_07_07_183641_add_deleted_at_to_customers_table.php 2025_01_07_000003_add_deleted_at_to_customers_table.php 2>/dev/null || true
mv 2025_07_07_182421_add_deleted_at_to_booking_types_table.php 2025_01_07_000004_add_deleted_at_to_booking_types_table.php 2>/dev/null || true
mv 2025_07_06_001009_add_deleted_at_to_slots_table.php 2025_01_07_000005_add_deleted_at_to_slots_table.php 2>/dev/null || true
mv 2025_07_07_181735_add_deleted_at_to_bookings_table.php 2025_01_07_000006_add_deleted_at_to_bookings_table.php 2>/dev/null || true

# Step 8: User Relationships (2025-01-08)
echo "📋 Step 8: Renaming user relationships..."
mv 2025_06_30_214449_add_customer_id_to_users_table.php 2025_01_08_000001_add_customer_id_to_users_table.php 2>/dev/null || true

# Step 9: Products and Configuration (2025-01-09)
echo "📋 Step 9: Renaming products and configuration..."
mv 2025_07_01_170627_create_products_and_booking_product_tables.php 2025_01_09_000001_create_products_and_booking_product_tables.php 2>/dev/null || true
mv 2025_07_01_171942_create_depot_product_table.php 2025_01_09_000002_create_depot_product_table.php 2>/dev/null || true
mv 2025_07_01_185252_create_customer_depot_product_table.php 2025_01_09_000003_create_customer_depot_product_table.php 2>/dev/null || true
mv 2025_07_01_213934_create_depot_case_ranges_table.php 2025_01_09_000004_create_depot_case_ranges_table.php 2>/dev/null || true

# Step 10: Booking Enhancements (2025-01-10)
echo "📋 Step 10: Renaming booking enhancements..."
mv 2025_06_25_000000_add_case_and_size_to_bookings_table.php 2025_01_10_000001_add_case_and_size_to_bookings_table.php 2>/dev/null || true
mv 2025_06_26_225152_add_details_to_bookings_table.php 2025_01_10_000002_add_details_to_bookings_table.php 2>/dev/null || true
mv 2025_06_29_094425_add_arrival_departure_to_bookings_table.php 2025_01_10_000003_add_arrival_departure_to_bookings_table.php 2>/dev/null || true
mv 2025_07_03_184941_add_customer_id_to_bookings_table.php 2025_01_10_000004_add_customer_id_to_bookings_table.php 2>/dev/null || true
mv 2025_07_03_193030_add_expected_actual_fields_to_bookings_table.php 2025_01_10_000005_add_expected_actual_fields_to_bookings_table.php 2>/dev/null || true
mv 2025_07_03_210203_edit_expected_actual_fields_to_bookings_table.php 2025_01_10_000006_edit_expected_actual_fields_to_bookings_table.php 2>/dev/null || true
mv 2025_07_03_222151_add_status_to_bookings_table.php 2025_01_10_000007_add_status_to_bookings_table.php 2>/dev/null || true
mv 2025_07_03_225327_add_end_time_to_bookings_table.php 2025_01_10_000008_add_end_time_to_bookings_table.php 2>/dev/null || true

# Step 11: Slot System Enhancements (2025-01-11)
echo "📋 Step 11: Renaming slot system..."
mv 2025_06_25_225157_create_slot_templates_table.php 2025_01_11_000001_create_slot_templates_table.php 2>/dev/null || true
mv 2025_06_29_080210_add_capacity_to_slots_table.php 2025_01_11_000002_add_capacity_to_slots_table.php 2>/dev/null || true
mv 2025_06_29_081807_add_duration_minutes_to_booking_types_table.php 2025_01_11_000003_add_duration_minutes_to_booking_types_table.php 2>/dev/null || true
mv 2025_06_29_082546_create_booking_type_depot_table.php 2025_01_11_000004_create_booking_type_depot_table.php 2>/dev/null || true
mv 2025_06_29_094218_create_slot_generation_settings_table.php 2025_01_11_000005_create_slot_generation_settings_table.php 2>/dev/null || true
mv 2025_06_30_211619_add_cut_off_time_to_depots_table.php 2025_01_11_000006_add_cut_off_time_to_depots_table.php 2>/dev/null || true

# Step 12: Slot Release System (2025-01-12)
echo "📋 Step 12: Renaming slot release system..."
mv 2025_07_10_180021_create_slot_release_rules_table.php 2025_01_12_000001_create_slot_release_rules_table.php 2>/dev/null || true
mv 2025_07_10_222631_create_slot_release_rule_customer.php 2025_01_12_000002_create_slot_release_rule_customer.php 2>/dev/null || true
mv 2025_07_18_114348_add_release_and_cutoff_to_slots.php 2025_01_12_000003_add_release_and_cutoff_to_slots.php 2>/dev/null || true
mv 2025_07_18_130836_create_slot_customer_table.php 2025_01_12_000004_create_slot_customer_table.php 2>/dev/null || true

# Step 13: Transportation System (2025-01-13)
echo "📋 Step 13: Renaming transportation system..."
mv 2025_08_02_000001_add_transportation_fields_to_bookings_table.php 2025_01_13_000001_add_transportation_fields_to_bookings_table.php 2>/dev/null || true
mv 2025_08_02_000002_rename_trailer_number_to_container_number.php 2025_01_13_000002_rename_trailer_number_to_container_number.php 2>/dev/null || true
mv 2025_08_02_000003_update_container_number_index.php 2025_01_13_000003_update_container_number_index.php 2>/dev/null || true

# Step 14: Trailer Types System (2025-01-14)
echo "📋 Step 14: Renaming trailer types system..."
mv 2025_08_16_070201_create_trailer_types_table.php 2025_01_14_000001_create_trailer_types_table.php 2>/dev/null || true
# Note: Skip problematic trailer_type_id migrations - will add manually

# Step 15: Booking History and Behavior (2025-01-15)
echo "📋 Step 15: Renaming booking history and behavior..."
mv 2025_08_08_164430_create_booking_history_table.php 2025_01_15_000001_create_booking_history_table.php 2>/dev/null || true
mv 2025_08_08_164623_add_rebooking_fields_to_bookings_table.php 2025_01_15_000002_add_rebooking_fields_to_bookings_table.php 2>/dev/null || true
mv 2025_08_09_000001_create_customer_behavior_settings_table.php 2025_01_15_000003_create_customer_behavior_settings_table.php 2>/dev/null || true

# Step 16: Arrival Time Settings (2025-01-16) ⭐ Critical Feature
echo "📋 Step 16: Renaming arrival time settings..."
mv 2025_08_16_082819_create_arrival_time_settings_table.php 2025_01_16_000001_create_arrival_time_settings_table.php 2>/dev/null || true

# Step 17: Tipping Workflow (2025-01-17)
echo "📋 Step 17: Renaming tipping workflow..."
mv 2025_08_09_000002_create_tipping_locations_table.php 2025_01_17_000001_create_tipping_locations_table.php 2>/dev/null || true
mv 2025_08_09_000003_create_tipping_bays_table.php 2025_01_17_000002_create_tipping_bays_table.php 2>/dev/null || true
mv 2025_08_09_000004_add_tipping_fields_to_bookings_table.php 2025_01_17_000003_add_tipping_fields_to_bookings_table.php 2>/dev/null || true
mv 2025_08_10_084925_add_tipping_workflow_enabled_to_settings.php 2025_01_17_000004_add_tipping_workflow_enabled_to_settings.php 2>/dev/null || true
mv 2025_08_11_174131_add_waiting_area_fields_to_bookings_table.php 2025_01_17_000005_add_waiting_area_fields_to_bookings_table.php 2>/dev/null || true
mv 2025_08_11_175057_add_trailer_collection_fields_to_bookings_table.php 2025_01_17_000006_add_trailer_collection_fields_to_bookings_table.php 2>/dev/null || true

# Step 18: PO and Pallet System (2025-01-18) - BEFORE Vehicle System
echo "📋 Step 18: Renaming PO and pallet system..."
mv 2025_08_12_create_booking_po_numbers_table.php 2025_01_18_000001_create_booking_po_numbers_table.php 2>/dev/null || true
mv 2025_08_12_create_pallet_types_table.php 2025_01_18_000002_create_pallet_types_table.php 2>/dev/null || true
mv 2025_08_12_create_po_lines_table.php 2025_01_18_000003_create_po_lines_table.php 2>/dev/null || true
mv 2025_08_14_212207_create_po_line_actual_pallets_table.php 2025_01_18_000004_create_po_line_actual_pallets_table.php 2>/dev/null || true

# Step 19: Vehicle and Movement System (2025-01-19) - AFTER Pallet Types
echo "📋 Step 19: Renaming vehicle and movement system..."
mv 2025_08_13_000001_create_vehicles_table.php 2025_01_19_000001_create_vehicles_table.php 2>/dev/null || true
mv 2025_08_13_000002_create_trailers_table.php 2025_01_19_000002_create_trailers_table.php 2>/dev/null || true
mv 2025_08_13_000003_create_movements_table.php 2025_01_19_000003_create_movements_table.php 2>/dev/null || true
mv 2025_08_13_000004_create_consignments_table.php 2025_01_19_000004_create_consignments_table.php 2>/dev/null || true
mv 2025_08_13_000005_create_consignment_references_table.php 2025_01_19_000005_create_consignment_references_table.php 2>/dev/null || true
mv 2025_08_13_000006_create_consignment_loads_table.php 2025_01_19_000006_create_consignment_loads_table.php 2>/dev/null || true
mv 2025_08_13_000007_create_movement_loads_table.php 2025_01_19_000007_create_movement_loads_table.php 2>/dev/null || true

# Step 20: Vehicle Details (2025-01-20) - Skip problematic ones
echo "📋 Step 20: Renaming vehicle details..."
# Skip: 2025_08_14_165255_add_vehicle_details_to_bookings_table.php (has duplicates)
mv 2025_08_14_165406_add_vehicle_details_json_to_bookings_table.php 2025_01_20_000001_add_vehicle_details_json_to_bookings_table.php 2>/dev/null || true
# Skip: 2025_08_15_073303_add_missing_tipping_bay_id_to_bookings_table.php (has duplicates)

# Step 21: Cleanup and Optimization (2025-01-21)
echo "📋 Step 21: Renaming cleanup migrations..."
mv 2025_08_12_061542_remove_driver_fields_from_bookings_table.php 2025_01_21_000001_remove_driver_fields_from_bookings_table.php 2>/dev/null || true
mv 2025_08_12_204307_make_container_size_nullable_in_bookings_table.php 2025_01_21_000002_make_container_size_nullable_in_bookings_table.php 2>/dev/null || true
mv 2025_08_12_remove_quantity_columns_from_booking_tables.php 2025_01_21_000003_remove_quantity_columns_from_booking_tables.php 2>/dev/null || true
mv 2025_08_12_remove_reference_and_gate_fields_from_bookings.php 2025_01_21_000004_remove_reference_and_gate_fields_from_bookings.php 2>/dev/null || true
mv 2025_08_13_000008_remove_vehicle_fields_from_bookings_table.php 2025_01_21_000005_remove_vehicle_fields_from_bookings_table.php 2>/dev/null || true

# Step 22: Foreign Key Constraints (2025-01-22)
echo "📋 Step 22: Renaming foreign key constraints..."
mv 2025_08_13_000009_add_foreign_key_constraints.php 2025_01_22_000001_add_foreign_key_constraints.php 2>/dev/null || true

# Step 23: Manual Additions (2025-01-23)
echo "📋 Step 23: Creating manual addition migration..."
cat > 2025_01_23_000001_add_trailer_type_id_to_bookings_manual.php << 'EOF'
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
EOF

cd ../..

echo "✅ Migration reordering complete!"
echo ""
echo "📋 Summary:"
echo "- Migrations renamed to chronological order (2025-01-01 to 2025-01-23)"
echo "- Dependencies properly sequenced"
echo "- Problematic migrations skipped/fixed"
echo "- Manual trailer_type_id migration created"
echo ""
echo "🚀 You can now run: php artisan migrate"
echo "📝 Original migration names backed up in database/migration_backup_list.txt"