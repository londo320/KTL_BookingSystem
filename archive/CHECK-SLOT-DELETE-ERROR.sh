#!/bin/bash

echo "🔍 Checking for slot deletion errors..."
echo ""

echo "=== Recent Laravel Errors ==="
docker exec ktl-booking-app tail -50 /var/www/html/storage/logs/laravel.log | grep -A 5 -B 5 "ERROR\|Exception\|Error"
echo ""

echo "=== Check if jobs table exists ==="
docker exec ktl-booking-app php artisan tinker --execute="
try {
    \$count = DB::table('jobs')->count();
    echo \"✅ Jobs table exists. Current jobs in queue: \$count\n\";
} catch (\Exception \$e) {
    echo \"❌ Jobs table error: \" . \$e->getMessage() . \"\n\";
}
"
echo ""

echo "=== Check QUEUE_CONNECTION setting ==="
docker exec ktl-booking-app php artisan tinker --execute="
echo \"QUEUE_CONNECTION: \" . config('queue.default') . \"\n\";
"
echo ""

echo "=== Test DeleteEmptySlotsJob class exists ==="
docker exec ktl-booking-app php artisan tinker --execute="
try {
    \$job = new \App\Jobs\DeleteEmptySlotsJob(1, '2026-07-04');
    echo \"✅ DeleteEmptySlotsJob class loaded successfully\n\";
} catch (\Exception \$e) {
    echo \"❌ Error loading job: \" . \$e->getMessage() . \"\n\";
}
"
echo ""

echo "=== Recent nginx/PHP errors ==="
docker exec ktl-booking-app tail -20 /var/log/nginx/error.log 2>/dev/null || echo "No nginx error log accessible"
