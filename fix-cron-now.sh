#!/bin/bash
set -e

echo "=== Quick Cron Fix for Existing Container ==="
echo ""

APP_CONTAINER="ktl-booking-app"

echo "📦 Installing cron..."
docker exec "$APP_CONTAINER" bash -c 'apt-get update -qq && apt-get install -y cron'

echo ""
echo "⚙️ Configuring crontab..."
docker exec "$APP_CONTAINER" bash -c 'echo "* * * * * cd /var/www/html && php artisan schedule:run >> /var/www/html/storage/logs/scheduler.log 2>&1" | crontab -'

echo ""
echo "🚀 Starting cron service..."
docker exec "$APP_CONTAINER" bash -c 'service cron start'

echo ""
echo "✅ Verifying crontab:"
docker exec "$APP_CONTAINER" crontab -l

echo ""
echo "✅ Checking cron service:"
docker exec "$APP_CONTAINER" service cron status

echo ""
echo "🚀 Running initial slot release..."
docker exec -w /var/www/html "$APP_CONTAINER" php artisan app:auto-release-slots

echo ""
echo "📋 Scheduled tasks:"
docker exec -w /var/www/html "$APP_CONTAINER" php artisan schedule:list

echo ""
echo "✅ Cron setup complete!"
echo ""
echo "Monitor logs with:"
echo "  docker exec $APP_CONTAINER tail -f /var/www/html/storage/logs/scheduler.log"
