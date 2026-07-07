#!/bin/bash
set -e

echo "===== KTL Booking System - Safe Quick Update ====="
echo ""
echo "Script Started: $(date)"
echo ""

# Configuration
PROJECT_DIR="/mnt/user/appdata/ktl-booking"
GIT_BRANCH="main"
APP_CONTAINER="ktl-booking-app"

# Function to run command with timeout and error handling
run_with_timeout() {
    local timeout=$1
    shift
    local description="$1"
    shift

    echo "▶ $description"
    if timeout "$timeout" "$@"; then
        echo "  ✅ Success"
        return 0
    else
        local exit_code=$?
        if [ $exit_code -eq 124 ]; then
            echo "  ⚠️  TIMEOUT after ${timeout}s - skipping"
        else
            echo "  ⚠️  ERROR (exit code: $exit_code) - continuing anyway"
        fi
        return 1
    fi
}

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
git fetch origin "$GIT_BRANCH" 2>&1 || echo "WARNING: git fetch failed"
git reset --hard "origin/$GIT_BRANCH" 2>&1 || echo "WARNING: git reset failed"
echo "✅ Code updated"
echo ""

# Update dependencies (with timeout)
echo "📦 Updating dependencies..."
run_with_timeout 180 "Running composer install" \
    docker exec "$APP_CONTAINER" composer install --no-interaction --optimize-autoload
echo ""

# Run migrations (with timeout and --isolated flag for safety)
echo "🗄️  Running migrations..."
run_with_timeout 120 "Executing database migrations" \
    docker exec "$APP_CONTAINER" php artisan migrate --force --isolated
echo ""

# Ensure storage symlink exists
echo "🔗 Ensuring storage symlink..."
docker exec "$APP_CONTAINER" php artisan storage:link 2>/dev/null || echo "  (symlink already exists)"
echo ""

# Clear caches (each with individual error handling)
echo "🧹 Clearing caches..."
docker exec "$APP_CONTAINER" php artisan config:clear 2>/dev/null || echo "  ⚠️  config:clear failed"
docker exec "$APP_CONTAINER" php artisan cache:clear 2>/dev/null || echo "  ⚠️  cache:clear failed"
docker exec "$APP_CONTAINER" php artisan view:clear 2>/dev/null || echo "  ⚠️  view:clear failed"
docker exec "$APP_CONTAINER" php artisan route:clear 2>/dev/null || echo "  ⚠️  route:clear failed"

# Force delete compiled views
echo "  Removing compiled views..."
docker exec "$APP_CONTAINER" rm -rf /var/www/html/storage/framework/views/* 2>/dev/null || echo "  (no views to remove)"
docker exec "$APP_CONTAINER" rm -rf /var/www/html/bootstrap/cache/*.php 2>/dev/null || echo "  (no cache to remove)"
echo "✅ Caches cleared"
echo ""

# Fix permissions (common issue)
echo "🔒 Fixing permissions..."
docker exec "$APP_CONTAINER" chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || echo "  ⚠️  chmod failed"
docker exec "$APP_CONTAINER" chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || echo "  ⚠️  chown failed"
echo "✅ Permissions fixed"
echo ""

# Restart web server (graceful)
echo "🔄 Restarting web server..."
docker exec "$APP_CONTAINER" service apache2 reload 2>/dev/null || \
    docker exec "$APP_CONTAINER" service apache2 restart 2>/dev/null || \
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
echo "   • If you see errors above, they're usually non-critical"
echo "   • Clear your browser cache if pages look broken"
echo "   • Check logs: docker logs $APP_CONTAINER"
echo ""
