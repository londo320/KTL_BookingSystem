#!/bin/bash
set -e

echo "===== KTL Booking System - Quick Fix ====="

PROJECT_DIR="/mnt/user/appdata/ktl-booking"
MYSQL_CONTAINER="ktl-booking-mysql"
MYSQL_ROOT_PASSWORD="ktl123456"
MYSQL_DATABASE="ktl_booking"
MYSQL_USER="ktl_user"
MYSQL_PASSWORD="ktl_password"
APP_CONTAINER="ktl-booking-app"
NGINX_CONTAINER="ktl-booking-nginx"
SCHEDULER_CONTAINER="ktl-booking-scheduler"

cd "$PROJECT_DIR" || exit 1

echo "🔍 Checking what went wrong..."
echo ""
echo "Current container status:"
docker ps -a --filter "name=ktl-booking" --format "table {{.Names}}\t{{.Status}}"
echo ""

# Check if app container exists but stopped
if docker ps -a --format '{{.Names}}' | grep -q "^${APP_CONTAINER}$"; then
    echo "📋 Checking why app container stopped..."
    docker logs "$APP_CONTAINER" --tail 50
    echo ""
    echo "🧹 Removing stopped app container..."
    docker rm "$APP_CONTAINER" 2>/dev/null || true
fi

# Check if MySQL container exists but stopped
if docker ps -a --format '{{.Names}}' | grep -q "^${MYSQL_CONTAINER}$"; then
    echo "📋 Checking why MySQL container stopped..."
    docker logs "$MYSQL_CONTAINER" --tail 50
    echo ""
    echo "🧹 Removing stopped MySQL container..."
    docker rm "$MYSQL_CONTAINER" 2>/dev/null || true
fi

# Stop nginx and scheduler since they depend on app
echo "🛑 Stopping nginx and scheduler..."
docker stop "$NGINX_CONTAINER" "$SCHEDULER_CONTAINER" 2>/dev/null || true
docker rm "$NGINX_CONTAINER" "$SCHEDULER_CONTAINER" 2>/dev/null || true

echo ""
echo "🐳 Starting MySQL container..."
docker run -d \
    --name "$MYSQL_CONTAINER" \
    -e MYSQL_ROOT_PASSWORD="$MYSQL_ROOT_PASSWORD" \
    -e MYSQL_DATABASE="$MYSQL_DATABASE" \
    -e MYSQL_USER="$MYSQL_USER" \
    -e MYSQL_PASSWORD="$MYSQL_PASSWORD" \
    -p 3306:3306 \
    -v ktl-mysql-data:/var/lib/mysql \
    --restart unless-stopped \
    mysql:8.0

echo "⏳ Waiting for MySQL to be ready..."
sleep 20

for i in {1..10}; do
    if docker exec "$MYSQL_CONTAINER" mysqladmin ping -u root -p"$MYSQL_ROOT_PASSWORD" --silent 2>/dev/null; then
        echo "✅ MySQL is ready!"
        break
    fi
    echo "⏳ Waiting... ($i/10)"
    sleep 3
done

echo ""
echo "🐳 Starting PHP-FPM container..."
docker run -d \
    --name "$APP_CONTAINER" \
    --link "$MYSQL_CONTAINER":mysql \
    -v "$PROJECT_DIR":/var/www/html \
    -w /var/www/html \
    --restart unless-stopped \
    php:8.2-fpm

echo "📦 Installing required packages..."
docker exec "$APP_CONTAINER" apt-get update -qq
docker exec "$APP_CONTAINER" apt-get install -y -qq \
    libpng-dev libjpeg-dev libfreetype6-dev libzip-dev \
    libonig-dev libxml2-dev unzip procps

echo "📦 Installing PHP extensions..."
docker exec "$APP_CONTAINER" docker-php-ext-configure gd --with-freetype --with-jpeg
docker exec "$APP_CONTAINER" docker-php-ext-install -j$(nproc) gd zip pdo_mysql mbstring xml opcache
docker exec "$APP_CONTAINER" docker-php-ext-enable pdo_mysql

echo "🔒 Fixing permissions..."
docker exec "$APP_CONTAINER" chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
docker exec "$APP_CONTAINER" chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

echo ""
echo "🔍 Testing database connection..."
docker exec "$APP_CONTAINER" php -r "
try {
    \$pdo = new PDO('mysql:host=mysql;dbname=ktl_booking', 'ktl_user', 'ktl_password');
    echo '✅ Database connection successful\n';
} catch(Exception \$e) {
    echo '❌ Database connection failed: ' . \$e->getMessage() . '\n';
    exit(1);
}
"

echo ""
echo "🚀 Testing artisan..."
docker exec "$APP_CONTAINER" php artisan --version

echo ""
echo "🌐 Starting Nginx..."
docker run -d \
    --name "$NGINX_CONTAINER" \
    --link "$APP_CONTAINER":php-fpm \
    -p 8088:80 \
    -v "$PROJECT_DIR":/var/www/html:ro \
    -v "$PROJECT_DIR/nginx.conf":/etc/nginx/conf.d/default.conf:ro \
    --restart unless-stopped \
    nginx:alpine

echo ""
echo "⏰ Starting Scheduler..."
docker run -d \
    --name "$SCHEDULER_CONTAINER" \
    --link "$MYSQL_CONTAINER":mysql \
    -v "$PROJECT_DIR":/var/www/html \
    -w /var/www/html \
    --restart unless-stopped \
    php:8.2-fpm \
    php artisan scheduler:run --daemon --interval=60

echo ""
echo "⏳ Waiting for services to stabilize..."
sleep 5

echo ""
echo "🔍 Testing HTTP response..."
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8088 || echo "000")
if [ "$HTTP_STATUS" = "200" ] || [ "$HTTP_STATUS" = "302" ]; then
    echo "✅ Application is responding (HTTP $HTTP_STATUS)"
else
    echo "⚠️ Application returned HTTP $HTTP_STATUS"
    echo "Checking logs..."
    echo ""
    echo "=== PHP-FPM Logs ==="
    docker logs "$APP_CONTAINER" --tail 20
    echo ""
    echo "=== Nginx Logs ==="
    docker logs "$NGINX_CONTAINER" --tail 20
fi

echo ""
echo "============================================="
echo "🎉 FIX COMPLETE!"
echo "============================================="
UNRAID_IP=$(hostname -I | awk '{print $1}')
echo "🌐 Try accessing: http://$UNRAID_IP:8088"
echo "============================================="
echo ""

echo "📊 Current container status:"
docker ps --filter "name=ktl-booking" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
echo ""
