#!/bin/bash
# Fix scheduler cron job to use full PHP path

APP_CONTAINER="ktl-booking-app"

echo "=== Fixing Scheduler Cron Job Path ==="

# Get the full PHP path
PHP_PATH=$(docker exec "$APP_CONTAINER" which php)
echo "PHP binary location: $PHP_PATH"

# Update cron job with full PHP path
echo "📝 Updating cron job with full PHP path..."
docker exec "$APP_CONTAINER" bash -c "cat > /etc/cron.d/laravel-scheduler <<EOF
* * * * * www-data cd /var/www/html && $PHP_PATH artisan schedule:run >> /var/www/html/storage/logs/scheduler-cron.log 2>&1
EOF"

# Set permissions
docker exec "$APP_CONTAINER" chmod 0644 /etc/cron.d/laravel-scheduler

# Restart cron
echo "🔄 Restarting cron service..."
docker exec "$APP_CONTAINER" service cron restart

echo ""
echo "✅ Cron job fixed!"
echo ""
echo "📄 Updated cron job:"
docker exec "$APP_CONTAINER" cat /etc/cron.d/laravel-scheduler

echo ""
echo "⏳ Wait 1 minute, then check logs:"
echo "   docker exec $APP_CONTAINER tail -20 /var/www/html/storage/logs/scheduler-cron.log"
