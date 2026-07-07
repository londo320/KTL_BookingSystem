#!/bin/bash
set -e

echo "===== KTL Booking System - Quick Update ====="
echo ""

# Configuration
PROJECT_DIR="/mnt/user/appdata/ktl-booking"
GIT_BRANCH="main"
APP_CONTAINER="ktl-booking-app"

cd "$PROJECT_DIR"

echo "📥 Pulling latest code from git..."
docker exec "$APP_CONTAINER" git fetch origin "$GIT_BRANCH"
docker exec "$APP_CONTAINER" git reset --hard "origin/$GIT_BRANCH"
echo "✅ Code updated to latest version"
echo ""

echo "📦 Checking for dependency changes..."
docker exec "$APP_CONTAINER" composer install --no-interaction --optimize-autoloader --no-dev
echo "✅ Dependencies up to date"
echo ""

echo "🗄️  Running database migrations..."
docker exec "$APP_CONTAINER" php artisan migrate --force
echo "✅ Migrations complete"
echo ""

echo "🔗 Ensuring storage symlink..."
docker exec "$APP_CONTAINER" php artisan storage:link
echo "✅ Storage linked"
echo ""

echo "🧹 Clearing all caches..."
docker exec "$APP_CONTAINER" php artisan cache:clear
docker exec "$APP_CONTAINER" php artisan view:clear
docker exec "$APP_CONTAINER" php artisan config:clear
docker exec "$APP_CONTAINER" php artisan route:clear
docker exec "$APP_CONTAINER" rm -rf /var/www/html/storage/framework/views/*
echo "✅ Caches cleared"
echo ""

echo "🔄 Restarting PHP-FPM..."
docker exec "$APP_CONTAINER" pkill -USR2 php-fpm || docker restart "$APP_CONTAINER"
echo "✅ PHP-FPM restarted"
echo ""

echo "⏰ Ensuring scheduler cron is running..."
if docker exec "$APP_CONTAINER" service cron status 2>/dev/null | grep -q "running"; then
    echo "✅ Cron already running"
else
    echo "⚠️  Cron not running, starting it..."
    docker exec "$APP_CONTAINER" service cron start 2>/dev/null || docker exec "$APP_CONTAINER" service cron restart 2>/dev/null || true
    if docker exec "$APP_CONTAINER" service cron status 2>/dev/null | grep -q "running"; then
        echo "✅ Cron started"
    else
        echo "❌ Failed to start cron - run manually: docker exec $APP_CONTAINER service cron start"
    fi
fi
echo ""

echo "📋 Current version:"
docker exec "$APP_CONTAINER" git log -1 --oneline
echo ""

echo "✅ Quick update complete!"
echo ""
echo "💡 If you added new environment variables or made major changes,"
echo "   run the full deployment script instead: ./deploy-ktl-nginx.sh"
