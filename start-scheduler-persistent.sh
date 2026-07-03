#!/bin/bash
# Persistent scheduler starter for KTL Booking System
# This ensures cron stays running even after container restarts
# Can be added to Unraid's "Go" file or run as a User Script

APP_CONTAINER="ktl-booking-app"

# Function to start cron in container
start_cron() {
    echo "🚀 Starting cron service in $APP_CONTAINER..."
    docker exec "$APP_CONTAINER" service cron start 2>/dev/null

    # Verify it started
    if docker exec "$APP_CONTAINER" service cron status 2>/dev/null | grep -q "running"; then
        echo "✅ Cron is running"
        return 0
    else
        echo "⚠️ Cron may not have started, retrying..."
        docker exec "$APP_CONTAINER" service cron restart 2>/dev/null
        return 1
    fi
}

# Wait for container to be running
echo "⏳ Waiting for container $APP_CONTAINER to be ready..."
for i in {1..30}; do
    if docker ps --format '{{.Names}}' | grep -q "^${APP_CONTAINER}$"; then
        echo "✅ Container is running"
        sleep 2  # Give it a moment to fully initialize
        break
    fi

    if [ $i -eq 30 ]; then
        echo "❌ Container not found after 30 seconds"
        exit 1
    fi

    sleep 1
done

# Start cron
start_cron

echo ""
echo "============================================="
echo "✅ Scheduler persistent startup complete!"
echo "============================================="
echo "📊 To check scheduler logs:"
echo "   docker exec $APP_CONTAINER tail -f /var/www/html/storage/logs/scheduler-cron.log"
echo ""
echo "🔍 To verify cron is running:"
echo "   docker exec $APP_CONTAINER service cron status"
echo "============================================="
