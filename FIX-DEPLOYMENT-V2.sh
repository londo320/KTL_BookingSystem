#!/bin/bash
set -e

echo "===== KTL Booking System - Quick Fix V2 ====="

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

echo "🔍 Current status:"
docker ps -a --filter "name=ktl-booking" --format "table {{.Names}}\t{{.Status}}"
echo ""

echo "🛑 Stopping all containers..."
docker stop "$SCHEDULER_CONTAINER" 2>/dev/null || true
docker stop "$NGINX_CONTAINER" 2>/dev/null || true
docker stop "$APP_CONTAINER" 2>/dev/null || true
# Don't stop MySQL - keep the data

echo "🧹 Removing stopped containers..."
docker rm "$SCHEDULER_CONTAINER" 2>/dev/null || true
docker rm "$NGINX_CONTAINER" 2>/dev/null || true
docker rm "$APP_CONTAINER" 2>/dev/null || true

echo ""
echo "🔍 MySQL status:"
if docker ps --format '{{.Names}}' | grep -q "^${MYSQL_CONTAINER}$"; then
    echo "✅ MySQL is already running"

    # Test MySQL connection
    if docker exec "$MYSQL_CONTAINER" mysqladmin ping -u root -p"$MYSQL_ROOT_PASSWORD" --silent 2>/dev/null; then
        echo "✅ MySQL is responding"
    else
        echo "⚠️ MySQL not responding, restarting..."
        docker restart "$MYSQL_CONTAINER"
        sleep 10
    fi
else
    echo "❌ MySQL not running, starting it..."
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

    echo "⏳ Waiting for MySQL..."
    sleep 20
fi

echo ""
echo "🐳 Creating PHP-FPM container..."
docker run -d \
    --name "$APP_CONTAINER" \
    --link "$MYSQL_CONTAINER":mysql \
    -v "$PROJECT_DIR":/var/www/html \
    -w /var/www/html \
    --restart unless-stopped \
    php:8.2-fpm

echo "📦 Installing system dependencies (this may take a minute)..."
docker exec "$APP_CONTAINER" bash -c "
    apt-get update -qq && \
    apt-get install -y -qq \
        libpng-dev libjpeg-dev libfreetype6-dev libzip-dev \
        libonig-dev libxml2-dev unzip procps curl && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j\$(nproc) gd zip pdo_mysql mbstring xml opcache && \
    docker-php-ext-enable pdo_mysql
"

echo "🔒 Setting permissions..."
docker exec "$APP_CONTAINER" chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
docker exec "$APP_CONTAINER" chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public

echo ""
echo "🔍 Testing database connection..."
if docker exec "$APP_CONTAINER" php -r "
    try {
        \$pdo = new PDO('mysql:host=mysql;dbname=$MYSQL_DATABASE', '$MYSQL_USER', '$MYSQL_PASSWORD');
        echo 'SUCCESS';
    } catch(Exception \$e) {
        echo 'FAILED: ' . \$e->getMessage();
        exit(1);
    }
" 2>&1 | grep -q "SUCCESS"; then
    echo "✅ Database connection successful"
else
    echo "❌ Database connection failed!"
    echo "Checking MySQL logs..."
    docker logs "$MYSQL_CONTAINER" --tail 20
    exit 1
fi

echo ""
echo "🚀 Testing Laravel..."
if docker exec "$APP_CONTAINER" php artisan --version 2>&1; then
    echo "✅ Laravel is working"
else
    echo "❌ Laravel failed"
    exit 1
fi

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
# First install dependencies in scheduler container
SCHEDULER_ID=$(docker run -d \
    --name "$SCHEDULER_CONTAINER" \
    --link "$MYSQL_CONTAINER":mysql \
    -v "$PROJECT_DIR":/var/www/html \
    -w /var/www/html \
    --restart unless-stopped \
    --entrypoint /bin/sh \
    php:8.2-fpm \
    -c "sleep infinity")

echo "📦 Installing scheduler dependencies..."
docker exec "$SCHEDULER_CONTAINER" bash -c "
    apt-get update -qq && \
    apt-get install -y -qq procps libzip-dev libpng-dev && \
    docker-php-ext-install -j\$(nproc) zip pdo_mysql
"

# Restart with proper command
docker stop "$SCHEDULER_CONTAINER"
docker rm "$SCHEDULER_CONTAINER"

docker run -d \
    --name "$SCHEDULER_CONTAINER" \
    --link "$MYSQL_CONTAINER":mysql \
    -v "$PROJECT_DIR":/var/www/html \
    -w /var/www/html \
    --restart unless-stopped \
    php:8.2-fpm \
    php artisan scheduler:run --daemon --interval=60

echo ""
echo "⏳ Waiting for services to start..."
sleep 5

echo ""
echo "🔍 Testing HTTP response..."
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8088 2>&1 || echo "000")
echo "HTTP Status: $HTTP_STATUS"

if [ "$HTTP_STATUS" = "200" ] || [ "$HTTP_STATUS" = "302" ] || [ "$HTTP_STATUS" = "301" ]; then
    echo "✅ Application is responding!"
else
    echo "⚠️ Unexpected status code: $HTTP_STATUS"
    echo ""
    echo "=== PHP-FPM Logs ==="
    docker logs "$APP_CONTAINER" --tail 30
    echo ""
    echo "=== Nginx Error Log ==="
    docker exec "$NGINX_CONTAINER" cat /var/log/nginx/error.log 2>/dev/null | tail -20 || echo "No errors"
    echo ""
    echo "=== Laravel Log ==="
    docker exec "$APP_CONTAINER" cat /var/www/html/storage/logs/laravel.log 2>/dev/null | tail -30 || echo "No Laravel logs"
fi

echo ""
echo "============================================="
echo "🎉 FIX COMPLETE!"
echo "============================================="
UNRAID_IP=$(hostname -I | awk '{print $1}')
echo "🌐 Application: http://$UNRAID_IP:8088"
echo "⏰ Scheduler: http://$UNRAID_IP:8088/admin/scheduler"
echo "============================================="
echo ""

echo "📊 All containers:"
docker ps --filter "name=ktl-booking" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
echo ""

echo "✅ All containers should be 'Up' now!"
echo "   Visit the URL above to access your application"
