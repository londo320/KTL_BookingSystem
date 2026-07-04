#!/bin/bash
set -e

echo "===== KTL Booking System - Quick Update ====="
echo ""

# Configuration
PROJECT_DIR="/mnt/user/appdata/ktl-booking"
GIT_BRANCH="main"
APP_CONTAINER="ktl-booking-app"

# Check if container exists
if ! docker ps -a --format '{{.Names}}' | grep -q "^${APP_CONTAINER}$"; then
    echo "❌ Container ${APP_CONTAINER} does not exist!"
    echo "   Run the full deployment first"
    exit 1
fi

# Pull latest code (on host)
cd "$PROJECT_DIR"
echo "📥 Pulling latest code from git..."
git fetch origin "$GIT_BRANCH" 2>&1 || echo "WARNING: git fetch failed"
git reset --hard "origin/$GIT_BRANCH" 2>&1 || echo "WARNING: git reset failed"
echo "✅ Code updated"
echo ""

# Update dependencies
echo "📦 Updating dependencies..."
docker exec "$APP_CONTAINER" composer install --no-interaction --optimize-autoloader --no-dev
echo "✅ Dependencies updated"
echo ""

# Run migrations
echo "🗄️  Running migrations..."
docker exec "$APP_CONTAINER" php artisan migrate --force
echo "✅ Migrations complete"
echo ""

# Clear caches
echo "🧹 Clearing caches..."
docker exec "$APP_CONTAINER" php artisan cache:clear
docker exec "$APP_CONTAINER" php artisan view:clear
docker exec "$APP_CONTAINER" php artisan config:clear
docker exec "$APP_CONTAINER" php artisan route:clear
docker exec "$APP_CONTAINER" rm -rf /var/www/html/storage/framework/views/*
echo "✅ Caches cleared"
echo ""

# Restart PHP-FPM
echo "🔄 Restarting PHP-FPM..."
docker exec "$APP_CONTAINER" pkill -USR2 php-fpm || docker restart "$APP_CONTAINER"
echo "✅ PHP-FPM restarted"
echo ""

# Show current version
echo "📋 Current version:"
docker exec "$APP_CONTAINER" git log -1 --oneline
echo ""

echo "✅ Quick update complete!"
echo ""
echo "Application is now updated and ready to use."
