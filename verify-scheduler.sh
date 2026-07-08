#!/bin/bash
# Verify Laravel Scheduler is working correctly

APP_CONTAINER="ktl-booking-app"

echo "=== KTL Booking Scheduler Verification ==="
echo ""

# Check container is running
echo "1️⃣ Checking container status..."
if docker ps --format '{{.Names}}' | grep -q "^${APP_CONTAINER}$"; then
    echo "   ✅ Container is running"
else
    echo "   ❌ Container is not running"
    exit 1
fi

echo ""
echo "2️⃣ Checking cron service..."
if docker exec "$APP_CONTAINER" service cron status 2>/dev/null | grep -q "running"; then
    echo "   ✅ Cron service is running"
else
    echo "   ❌ Cron service is not running"
    echo "   Run: ./quick-deploy.sh   # self-heals the scheduler cron"
fi

echo ""
echo "3️⃣ Checking cron configuration..."
if docker exec "$APP_CONTAINER" test -f /etc/cron.d/laravel-scheduler; then
    echo "   ✅ Cron job file exists"
    echo "   📄 Contents:"
    docker exec "$APP_CONTAINER" cat /etc/cron.d/laravel-scheduler | sed 's/^/      /'
else
    echo "   ❌ Cron job file not found"
    echo "   Run: ./quick-deploy.sh   # self-heals the scheduler cron"
fi

echo ""
echo "4️⃣ Checking scheduled tasks..."
docker exec "$APP_CONTAINER" php artisan schedule:list

echo ""
echo "5️⃣ Checking recent scheduler logs..."
if docker exec "$APP_CONTAINER" test -f /var/www/html/storage/logs/scheduler-cron.log; then
    echo "   📊 Last 10 lines from scheduler-cron.log:"
    docker exec "$APP_CONTAINER" tail -10 /var/www/html/storage/logs/scheduler-cron.log | sed 's/^/      /'
else
    echo "   ⚠️ No scheduler-cron.log found yet (wait 1 minute for first run)"
fi

echo ""
echo "6️⃣ Checking task-specific logs..."
LOG_FILES=("slots_generate.log" "auto_release_slots.log" "bay_sync.log" "booking_cleanup.log")

for LOG_FILE in "${LOG_FILES[@]}"; do
    if docker exec "$APP_CONTAINER" test -f "/var/www/html/storage/logs/$LOG_FILE"; then
        LINES=$(docker exec "$APP_CONTAINER" wc -l < "/var/www/html/storage/logs/$LOG_FILE" 2>/dev/null || echo "0")
        LAST_MODIFIED=$(docker exec "$APP_CONTAINER" stat -c %y "/var/www/html/storage/logs/$LOG_FILE" 2>/dev/null | cut -d. -f1)
        echo "   📄 $LOG_FILE: $LINES lines, last modified: $LAST_MODIFIED"
    else
        echo "   ⚠️ $LOG_FILE: not found yet"
    fi
done

echo ""
echo "7️⃣ Testing manual scheduler run..."
echo "   Running 'php artisan schedule:run'..."
MANUAL_OUTPUT=$(docker exec "$APP_CONTAINER" php artisan schedule:run 2>&1)
echo "$MANUAL_OUTPUT" | sed 's/^/      /'

if echo "$MANUAL_OUTPUT" | grep -q "No scheduled commands are ready to run"; then
    echo "   ✅ Scheduler works (no tasks due right now)"
elif echo "$MANUAL_OUTPUT" | grep -q "Running scheduled command"; then
    echo "   ✅ Scheduler works and ran tasks!"
else
    echo "   ℹ️ Scheduler responded"
fi

echo ""
echo "============================================="
echo "✅ Verification Complete!"
echo "============================================="
echo ""
echo "📝 Next steps:"
echo "   • If cron is not running: ./quick-deploy.sh   # self-heals the scheduler cron"
echo "   • To watch live logs: docker exec $APP_CONTAINER tail -f /var/www/html/storage/logs/scheduler-cron.log"
echo "   • To restart cron: docker exec $APP_CONTAINER service cron restart"
echo "============================================="
