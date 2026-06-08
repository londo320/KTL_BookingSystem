#!/bin/bash

echo "===== Starting Scheduler Inside PHP-FPM Container ====="
echo ""

APP_CONTAINER="ktl-booking-app"

echo "🔍 Checking if scheduler is already running..."
if docker exec "$APP_CONTAINER" ps aux | grep -E "scheduler:run|schedule:run" | grep -v grep > /dev/null; then
    echo "⚠️ Scheduler is already running!"
    echo ""
    echo "Current scheduler process:"
    docker exec "$APP_CONTAINER" ps aux | grep -E "scheduler:run|schedule:run" | grep -v grep
    echo ""
    echo "To restart, first run: docker exec $APP_CONTAINER pkill -f scheduler:run"
    exit 0
fi

echo "▶️ Starting scheduler daemon in background..."
docker exec -d "$APP_CONTAINER" php artisan scheduler:run --daemon --interval=60

sleep 2

echo ""
echo "🔍 Checking if scheduler started..."
if docker exec "$APP_CONTAINER" ps aux | grep -E "scheduler:run" | grep -v grep > /dev/null; then
    echo "✅ Scheduler is now running!"
    echo ""
    echo "Scheduler process:"
    docker exec "$APP_CONTAINER" ps aux | grep "scheduler:run" | grep -v grep
    echo ""
    echo "============================================="
    echo "🎉 SCHEDULER STARTED!"
    echo "============================================="
    echo "📋 View logs: docker logs $APP_CONTAINER"
    echo "🛑 Stop: docker exec $APP_CONTAINER pkill -f scheduler:run"
    echo "🔄 Restart: bash start-scheduler-docker.sh"
    echo "============================================="
else
    echo "❌ Scheduler failed to start"
    echo ""
    echo "Checking for errors:"
    docker exec "$APP_CONTAINER" php artisan scheduler:run --daemon --interval=60
fi
echo ""
