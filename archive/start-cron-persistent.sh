#!/bin/bash
# Ensures cron starts when container starts/restarts

echo "===== Ensuring Cron Runs on Container Start ====="

APP_CONTAINER="ktl-booking-app"

echo "📦 Installing cron if needed..."
docker exec "$APP_CONTAINER" bash -c 'command -v cron >/dev/null || (apt-get update -qq && apt-get install -y cron)'

echo "⚙️ Setting up crontab..."
docker exec "$APP_CONTAINER" bash -c 'echo "* * * * * cd /var/www/html && php artisan schedule:run >> /var/www/html/storage/logs/scheduler.log 2>&1" | crontab -'

echo "🚀 Starting cron service..."
docker exec "$APP_CONTAINER" service cron start

echo "✅ Verifying cron is running..."
docker exec "$APP_CONTAINER" service cron status

echo ""
echo "✅ Cron is now running!"
echo ""
echo "To run this after container restart:"
echo "  ./start-cron-persistent.sh"
