#!/bin/bash

# Run pending migrations on production
# This script will run the migrations needed for the booking form updates

echo "Running migrations on production..."

# The migrations that need to run:
# 1. 2025_10_10_204903_add_carrier_id_to_bookings_table.php
# 2. 2025_10_09_132620_add_supplier_haulier_contact_fields_to_bookings_table.php
# 3. 2025_10_10_211821_add_product_type_and_cases_per_pallet_to_products_table.php (optional, for products)

ssh londo@test.test "cd /path/to/production && php artisan migrate --force"

echo "Migrations completed!"
