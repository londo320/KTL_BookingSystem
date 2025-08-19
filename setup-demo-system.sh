#!/bin/bash

# Complete Demo System Setup Script
# This script sets up a fully functional booking system with sample data

echo "🚀 Setting up complete demo booking system..."

# Step 1: Reorder migrations (if not already done)
echo "📋 Step 1: Ensuring migrations are in correct order..."
if [ ! -f "database/migrations/2025_01_01_000001_create_depots_table.php" ]; then
    echo "Reordering migrations..."
    ./reorder-migrations.sh
else
    echo "Migrations already in correct order ✓"
fi

# Step 2: Wipe database and run migrations
echo "📋 Step 2: Setting up database..."
php artisan db:wipe --force
php artisan migrate --force

# Step 3: Run comprehensive seeders
echo "📋 Step 3: Seeding demo data..."
php artisan db:seed --force

echo "✅ Demo system setup complete!"
echo ""
echo "🎯 What's been created:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

echo "👥 USERS & CUSTOMERS:"
echo "   Admin Users:"
echo "   - admin@example.com / password (System Admin)"
echo "   - depotadmin@example.com / password (Depot Admin)"
echo "   - siteadmin@example.com / password (Site Admin)"
echo ""
echo "   Sample Customer Logins:"
echo "   - john@buildcorp.com / password (BuildCorp Construction)"
echo "   - mike@quickmart.co.uk / password (QuickMart Retail)"
echo "   - emma@freshfood.co.uk / password (FreshFood Distributors)"
echo "   - tom@autoparts.com / password (AutoParts Express)"
echo "   - lisa@chemsafe.co.uk / password (ChemSafe Solutions)"
echo "   - sophie@fashionforward.com / password (Fashion Forward)"

echo ""
echo "🏢 FACILITIES:"
echo "   Depots: 5 locations including Main Depot"
echo "   Tipping Locations: 5 specialized areas"
echo "   - Warehouse A (General) - 6 bays"
echo "   - Warehouse B (Bulk) - 4 bays" 
echo "   - Cold Storage - 3 bays"
echo "   - Outdoor Yard - 8 positions"
echo "   - Hazmat Storage - 2 bays"
echo "   Total: 23 loading/tipping bays"

echo ""
echo "📅 SCHEDULING:"
echo "   Slot Templates: 35+ templates covering Mon-Sat"
echo "   - Monday-Friday: 5 x 3-hour slots (6AM-9PM) + 1 priority slot"
echo "   - Saturday: 2 x 3-hour slots (8AM-2PM) + 1 priority slot"
echo "   - Generated slots for next 4 weeks"

echo ""
echo "📦 PRODUCTS & SERVICES:"
echo "   Products: 17 different product types"
echo "   - Construction materials (sand, gravel, cement, blocks)"
echo "   - General cargo (palletised goods, retail items)"
echo "   - Food products (ambient, chilled, frozen)"
echo "   - Automotive parts and tyres"
echo "   - Chemicals and hazmat"
echo "   - Textiles and clothing"
echo ""
echo "   Pallet Types: 10 different pallet specifications"
echo "   Trailer Types: 9 vehicle types"

echo ""
echo "⚙️ SYSTEM FEATURES:"
echo "   ✓ Configurable arrival time rules (0min tolerance default)"
echo "   ✓ Customer behavior analytics and risk scoring"
echo "   ✓ Automatic input normalization (carrier names, registrations)"
echo "   ✓ Comprehensive booking history and audit trail"
echo "   ✓ Tipping workflow with bay assignments"
echo "   ✓ Product and inventory management"
echo "   ✓ Multi-depot operations support"

echo ""
echo "🎮 READY TO TEST:"
echo "   1. Login as admin to configure system settings"
echo "   2. Login as customer to create test bookings"
echo "   3. Test the full booking workflow from creation to completion"
echo "   4. View customer behavior analytics and arrival tracking"
echo "   5. Configure depot-specific arrival time rules"

echo ""
echo "🌐 Access your system at: http://test.test"
echo "📚 View deployment docs: DEPLOYMENT.md"
echo ""
echo "✨ Happy testing! Your demo system is ready for action."