#!/bin/bash

echo "🔍 Checking which slot view is actually being used..."
echo ""

echo "=== 1. Controller returns which view? ==="
docker exec ktl-booking-app grep -A 2 "return view" /var/www/html/app/Http/Controllers/Admin/SlotController.php | head -10
echo ""

echo "=== 2. Check admin/slots/index.blade.php exists ==="
docker exec ktl-booking-app ls -lh /var/www/html/resources/views/admin/slots/index.blade.php
echo ""

echo "=== 3. Check warehouse/slots/index.blade.php exists ==="
docker exec ktl-booking-app ls -lh /var/www/html/resources/views/warehouse/slots/index.blade.php 2>/dev/null || echo "File does not exist"
echo ""

echo "=== 4. Last 5 commits ==="
docker exec ktl-booking-app git log -5 --oneline
echo ""

echo "=== 5. Check if 'Select All Empty' button exists in admin view ==="
docker exec ktl-booking-app grep -n "Select All Empty" /var/www/html/resources/views/admin/slots/index.blade.php
echo ""

echo "=== 6. Check if 'Select All Empty' exists in warehouse view ==="
docker exec ktl-booking-app grep -n "Select All Empty" /var/www/html/resources/views/warehouse/slots/index.blade.php 2>/dev/null || echo "File does not exist or no match"
echo ""

echo "=== 7. Clear view cache and check compiled views ==="
docker exec ktl-booking-app php artisan view:clear
docker exec ktl-booking-app rm -rf /var/www/html/storage/framework/views/*
echo "View cache cleared"
echo ""

echo "=== 8. Add a marker to admin view to test ==="
docker exec ktl-booking-app sed -i '1i<!-- ADMIN VIEW LOADED -->' /var/www/html/resources/views/admin/slots/index.blade.php
echo "Added marker to admin view. Refresh browser and view page source - look for '<!-- ADMIN VIEW LOADED -->'"
