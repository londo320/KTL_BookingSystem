#!/bin/bash

echo "===== KTL Booking System - Safe Quick Update ====="
echo ""
echo "Script Started: $(date)"
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

# Check if container is running
if ! docker ps --format '{{.Names}}' | grep -q "^${APP_CONTAINER}$"; then
    echo "⚠️  Container ${APP_CONTAINER} is not running!"
    echo "   Starting container..."
    docker start "$APP_CONTAINER"
    sleep 5
fi

# Pull latest code (on host, not in container)
cd "$PROJECT_DIR"
echo "📥 Pulling latest code from GitHub..."
git fetch origin "$GIT_BRANCH" 2>&1 || echo "⚠️  WARNING: git fetch failed"
git reset --hard "origin/$GIT_BRANCH" 2>&1 || echo "⚠️  WARNING: git reset failed"
echo "✅ Code updated"
echo ""

# CRITICAL: Clear bootstrap cache first (fixes "Call to a member function make() on null")
echo "🔧 Clearing Laravel bootstrap cache..."
docker exec "$APP_CONTAINER" rm -rf /var/www/html/bootstrap/cache/*.php 2>/dev/null || true
docker exec "$APP_CONTAINER" rm -rf /var/www/html/storage/framework/cache/* 2>/dev/null || true
echo "✅ Bootstrap cache cleared"
echo ""

# Update dependencies
echo "📦 Updating dependencies..."
docker exec "$APP_CONTAINER" composer install --no-interaction --no-dev 2>&1 | grep -v "nothing to install" || true
echo "✅ Dependencies updated"
echo ""

# Ensure storage symlink exists
echo "🔗 Ensuring storage symlink..."
docker exec "$APP_CONTAINER" php artisan storage:link 2>&1 | grep -v "symlink already exists" || echo "  (symlink ready)"
echo ""

# Run migrations
echo "🗄️  Running migrations..."
docker exec "$APP_CONTAINER" php artisan migrate --force 2>&1 | grep -v "Nothing to migrate" || echo "  (no new migrations)"
echo "✅ Migrations complete"
echo ""

# Clear caches
echo "🧹 Clearing all caches..."
docker exec "$APP_CONTAINER" php artisan config:clear 2>&1 || echo "  ⚠️  config:clear failed"
docker exec "$APP_CONTAINER" php artisan cache:clear 2>&1 || echo "  ⚠️  cache:clear failed"
docker exec "$APP_CONTAINER" php artisan view:clear 2>&1 || echo "  ⚠️  view:clear failed"
docker exec "$APP_CONTAINER" php artisan route:clear 2>&1 || echo "  ⚠️  route:clear failed"

# Force delete compiled views
echo "  Removing compiled views..."
docker exec "$APP_CONTAINER" rm -rf /var/www/html/storage/framework/views/* 2>/dev/null || true
echo "✅ Caches cleared"
echo ""

# Fix permissions (common issue)
echo "🔒 Fixing permissions..."
docker exec "$APP_CONTAINER" chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>&1 || true
docker exec "$APP_CONTAINER" chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>&1 || true
echo "✅ Permissions fixed"
echo ""

# Restart PHP process (since Apache isn't available)
echo "🔄 Restarting PHP..."
docker exec "$APP_CONTAINER" pkill -9 php 2>/dev/null || true
sleep 2
echo "✅ PHP restarted"
echo ""

# Show current version
echo "📋 Current version:"
cd "$PROJECT_DIR"
git log -1 --oneline
echo ""

echo "============================================="
echo "✅ Quick update complete!"
echo "============================================="
echo ""
echo "Script Finished: $(date)"
echo ""
echo "💡 If Laravel errors persist:"
echo "   • Restart the container: docker restart $APP_CONTAINER"
echo "   • Check logs: docker logs $APP_CONTAINER"
echo ""
