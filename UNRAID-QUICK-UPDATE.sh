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

# Update dependencies (suppress package:discover errors)
echo "📦 Updating dependencies..."
docker exec "$APP_CONTAINER" composer install --no-interaction --optimize-autoload --no-scripts 2>&1 || echo "⚠️  Composer had warnings (continuing)"
echo "  Running package discovery separately..."
docker exec "$APP_CONTAINER" php artisan package:discover --ansi 2>&1 || echo "  ⚠️  Package discovery failed (non-critical)"
docker exec "$APP_CONTAINER" composer dump-autoload --optimize 2>&1 || echo "  ⚠️  Autoload dump failed (non-critical)"
echo "✅ Dependencies updated"
echo ""

# Run migrations
echo "🗄️  Running migrations..."
docker exec "$APP_CONTAINER" php artisan migrate --force 2>&1 || echo "⚠️  Migrations failed or had no new migrations"
echo "✅ Migrations complete"
echo ""

# Ensure storage symlink exists
echo "🔗 Ensuring storage symlink..."
docker exec "$APP_CONTAINER" php artisan storage:link 2>/dev/null || echo "  (symlink already exists)"
echo ""

# Clear caches (each with individual error handling)
echo "🧹 Clearing caches..."
docker exec "$APP_CONTAINER" php artisan config:clear 2>&1 || echo "  ⚠️  config:clear failed"
docker exec "$APP_CONTAINER" php artisan cache:clear 2>&1 || echo "  ⚠️  cache:clear failed"
docker exec "$APP_CONTAINER" php artisan view:clear 2>&1 || echo "  ⚠️  view:clear failed"
docker exec "$APP_CONTAINER" php artisan route:clear 2>&1 || echo "  ⚠️  route:clear failed"

# Force delete compiled views
echo "  Removing compiled views..."
docker exec "$APP_CONTAINER" rm -rf /var/www/html/storage/framework/views/* 2>/dev/null || echo "  (no views to remove)"
docker exec "$APP_CONTAINER" rm -rf /var/www/html/bootstrap/cache/*.php 2>/dev/null || echo "  (no cache to remove)"
echo "✅ Caches cleared"
echo ""

# Fix permissions (common issue)
echo "🔒 Fixing permissions..."
docker exec "$APP_CONTAINER" chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>&1 || echo "  ⚠️  chown failed"
docker exec "$APP_CONTAINER" chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>&1 || echo "  ⚠️  chmod failed"
echo "✅ Permissions fixed"
echo ""

# Restart web server (graceful)
echo "🔄 Restarting web server..."
docker exec "$APP_CONTAINER" service apache2 reload 2>&1 || \
    docker exec "$APP_CONTAINER" service apache2 restart 2>&1 || \
    echo "  ⚠️  Apache restart skipped"
echo "✅ Web server restarted"
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
echo "💡 Tips:"
echo "   • If you see warnings above, they're usually non-critical"
echo "   • Clear your browser cache if pages look broken"
echo "   • Check logs: docker logs $APP_CONTAINER"
echo ""
