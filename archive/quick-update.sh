#!/bin/bash
set -e

echo "===== KTL Booking System - Quick Update ====="
echo ""

# Configuration
PROJECT_DIR="/mnt/user/appdata/ktl-booking"
GIT_BRANCH="main"
APP_CONTAINER="ktl-booking-app"

# Make sure the container actually exists and is running before we do anything else
if ! docker ps -a --format '{{.Names}}' | grep -q "^${APP_CONTAINER}$"; then
    echo "❌ Container ${APP_CONTAINER} does not exist!"
    echo "   Run the full install first: ./full-install.sh"
    exit 1
fi

if ! docker ps --format '{{.Names}}' | grep -q "^${APP_CONTAINER}$"; then
    echo "⚠️  Container ${APP_CONTAINER} is not running — starting it..."
    docker start "$APP_CONTAINER"
    sleep 5
fi

echo "📥 Pulling latest code from git..."
cd "$PROJECT_DIR"
git fetch origin "$GIT_BRANCH"
git reset --hard "origin/$GIT_BRANCH"
echo "✅ Code updated to latest version"
echo ""

# CRITICAL: Clear bootstrap cache first (fixes "Call to a member function make() on null")
echo "🔧 Clearing Laravel bootstrap cache..."
docker exec "$APP_CONTAINER" rm -rf /var/www/html/bootstrap/cache/*.php 2>/dev/null || true
docker exec "$APP_CONTAINER" rm -rf /var/www/html/storage/framework/cache/* 2>/dev/null || true
echo "✅ Bootstrap cache cleared"
echo ""

echo "📦 Checking for dependency changes..."
docker exec "$APP_CONTAINER" composer install --no-interaction --optimize-autoloader --no-dev
echo "✅ Dependencies up to date"
echo ""

echo "🔗 Ensuring storage symlink..."
docker exec "$APP_CONTAINER" php artisan storage:link 2>&1 | grep -v "symlink already exists" || echo "  (symlink ready)"
echo ""

echo "🗄️  Running database migrations..."
docker exec "$APP_CONTAINER" php artisan migrate --force
echo "✅ Migrations complete"
echo ""

echo "🧹 Clearing all caches..."
docker exec "$APP_CONTAINER" php artisan cache:clear
docker exec "$APP_CONTAINER" php artisan view:clear
docker exec "$APP_CONTAINER" php artisan config:clear
docker exec "$APP_CONTAINER" php artisan route:clear
docker exec "$APP_CONTAINER" rm -rf /var/www/html/storage/framework/views/*
echo "✅ Caches cleared"
echo ""

echo "🔒 Fixing permissions..."
docker exec "$APP_CONTAINER" chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>&1 || true
docker exec "$APP_CONTAINER" chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>&1 || true
echo "✅ Permissions fixed"
echo ""

echo "🔄 Restarting PHP-FPM..."
docker exec "$APP_CONTAINER" pkill -USR2 php-fpm 2>/dev/null || docker restart "$APP_CONTAINER"
echo "✅ PHP-FPM restarted"
echo ""

# Ensure scheduler cron is running (quick updates don't recreate the container,
# so cron can be left stopped from a previous restart/crash and this script
# would otherwise never notice)
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
git log -1 --oneline
echo ""

echo "============================================="
echo "✅ Quick update complete!"
echo "============================================="
echo ""
echo "💡 If you added new environment variables or made major changes,"
echo "   run the full install script instead: ./full-install.sh"
