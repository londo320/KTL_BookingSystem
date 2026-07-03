#!/bin/bash
# Setup cron-based scheduler for KTL Booking System in Unraid
# This replaces the daemon approach with a traditional cron job

APP_CONTAINER="ktl-booking-app"

echo "=== Setting up Laravel Scheduler with Cron ==="

# Check if container exists
if ! docker ps -a --format '{{.Names}}' | grep -q "^${APP_CONTAINER}$"; then
    echo "❌ Container $APP_CONTAINER not found. Run deploy script first."
    exit 1
fi

# Install cron in the container if not already installed
echo "📦 Installing cron in container..."
docker exec "$APP_CONTAINER" apt-get update -qq
docker exec "$APP_CONTAINER" apt-get install -y -qq cron

# Create the cron job
echo "⏰ Setting up cron job to run every minute..."
docker exec "$APP_CONTAINER" bash -c 'cat > /etc/cron.d/laravel-scheduler <<EOF
* * * * * www-data cd /var/www/html && /usr/local/bin/php artisan schedule:run >> /var/www/html/storage/logs/scheduler-cron.log 2>&1
EOF'

# Set proper permissions on cron file
docker exec "$APP_CONTAINER" chmod 0644 /etc/cron.d/laravel-scheduler

# Start cron service
echo "🚀 Starting cron service..."
docker exec "$APP_CONTAINER" service cron start

# Verify cron is running
if docker exec "$APP_CONTAINER" service cron status | grep -q "running"; then
    echo "✅ Cron service is running"
else
    echo "⚠️ Cron service may not be running, trying to start again..."
    docker exec "$APP_CONTAINER" service cron restart
fi

# Verify the cron job is installed
echo ""
echo "📋 Installed cron jobs:"
docker exec "$APP_CONTAINER" crontab -l -u www-data 2>/dev/null || echo "(using /etc/cron.d/laravel-scheduler)"
docker exec "$APP_CONTAINER" cat /etc/cron.d/laravel-scheduler

echo ""
echo "============================================="
echo "✅ Scheduler setup complete!"
echo "============================================="
echo "📝 The scheduler now runs every minute via cron"
echo "📊 Check logs at: storage/logs/scheduler-cron.log"
echo ""
echo "🔍 To verify it's working, wait 1 minute then run:"
echo "   docker exec $APP_CONTAINER tail -20 /var/www/html/storage/logs/scheduler-cron.log"
echo ""
echo "⚠️ IMPORTANT: After container restarts, cron needs to be restarted"
echo "   Add this to your startup: docker exec $APP_CONTAINER service cron start"
echo "============================================="
