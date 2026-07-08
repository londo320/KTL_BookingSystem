#!/bin/bash
set -e

echo "===== Rebuilding .env File ====="
echo ""

APP_CONTAINER="ktl-booking-app"
PROJECT_DIR="/mnt/user/appdata/ktl-booking"
MYSQL_DATABASE="ktl_booking"
MYSQL_USER="ktl_user"
MYSQL_PASSWORD="${MYSQL_PASSWORD:?Set MYSQL_PASSWORD in your environment before running this script}"

cd "$PROJECT_DIR" || exit 1

echo "📋 Step 1: Backing up corrupted .env..."
cp .env .env.corrupted.backup
echo "✅ Backup saved to .env.corrupted.backup"
echo ""

echo "📝 Step 2: Creating fresh .env from .env.example..."
if [ ! -f .env.example ]; then
    echo "❌ .env.example not found!"
    exit 1
fi

cp .env.example .env
echo "✅ Fresh .env created"
echo ""

echo "⚙️ Step 3: Configuring database credentials..."
sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env
sed -i 's/DB_HOST=.*/DB_HOST=mysql/' .env
sed -i 's/DB_PORT=.*/DB_PORT=3306/' .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$MYSQL_DATABASE/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$MYSQL_USER/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$MYSQL_PASSWORD/" .env
echo "✅ Database configured"
echo ""

echo "⚙️ Step 4: Configuring app settings..."
sed -i 's/APP_ENV=.*/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=.*/APP_DEBUG=false/' .env
echo "✅ App settings configured"
echo ""

echo "⚙️ Step 5: Adding Docker scheduler settings..."
echo "" >> .env
echo "# Docker Scheduler Configuration" >> .env
echo "SCHEDULER_MODE=docker" >> .env
echo "SCHEDULER_CONTAINER_NAME=ktl-booking-scheduler" >> .env
echo "✅ Scheduler settings added"
echo ""

echo "🔑 Step 6: Generating APP_KEY..."
docker exec "$APP_CONTAINER" php artisan key:generate --force
echo ""

echo "🧹 Step 7: Clearing caches..."
docker exec "$APP_CONTAINER" php artisan config:clear
docker exec "$APP_CONTAINER" php artisan cache:clear
docker exec "$APP_CONTAINER" php artisan view:clear
docker exec "$APP_CONTAINER" php artisan route:clear
echo "✅ Caches cleared"
echo ""

echo "⚡ Step 8: Optimizing Laravel..."
docker exec "$APP_CONTAINER" php artisan config:cache
docker exec "$APP_CONTAINER" php artisan route:cache
docker exec "$APP_CONTAINER" php artisan view:cache
echo "✅ Optimization complete"
echo ""

echo "🔄 Step 9: Restarting containers..."
docker restart "$APP_CONTAINER"
echo "⏳ Waiting for PHP-FPM..."
sleep 5
docker restart ktl-booking-nginx
echo "⏳ Waiting for Nginx..."
sleep 3
echo "✅ Containers restarted"
echo ""

echo "🔍 Step 10: Testing application..."
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8088 2>&1 || echo "000")
echo "HTTP Status: $HTTP_STATUS"

if [ "$HTTP_STATUS" = "200" ] || [ "$HTTP_STATUS" = "302" ] || [ "$HTTP_STATUS" = "301" ]; then
    echo ""
    echo "============================================="
    echo "🎉 .ENV REBUILT SUCCESSFULLY!"
    echo "============================================="
    UNRAID_IP=$(hostname -I | awk '{print $1}')
    echo "🌐 Application: http://$UNRAID_IP:8088"
    echo "📋 Old .env backup: .env.corrupted.backup"
    echo "============================================="
else
    echo ""
    echo "⚠️ Application returned HTTP $HTTP_STATUS"
    echo ""
    echo "📋 Checking Laravel logs:"
    docker exec "$APP_CONTAINER" cat /var/www/html/storage/logs/laravel.log 2>&1 | tail -30
fi
echo ""
