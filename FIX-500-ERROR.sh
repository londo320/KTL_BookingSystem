#!/bin/bash
set -e

echo "===== Fixing Laravel 500 Error ====="
echo ""

APP_CONTAINER="ktl-booking-app"
MYSQL_CONTAINER="ktl-booking-mysql"
MYSQL_DATABASE="ktl_booking"
MYSQL_USER="ktl_user"
MYSQL_PASSWORD="ktl_password"
PROJECT_DIR="/mnt/user/appdata/ktl-booking"

echo "🔍 Step 1: Checking current Laravel logs..."
docker exec "$APP_CONTAINER" cat /var/www/html/storage/logs/laravel.log 2>&1 | tail -30 || echo "No Laravel logs yet"
echo ""

echo "🔑 Step 2: Ensuring APP_KEY is set..."
docker exec "$APP_CONTAINER" php artisan key:generate --force
echo "✅ APP_KEY generated"
echo ""

echo "🧹 Step 3: Clearing all Laravel caches..."
docker exec "$APP_CONTAINER" php artisan config:clear
docker exec "$APP_CONTAINER" php artisan cache:clear
docker exec "$APP_CONTAINER" php artisan route:clear
docker exec "$APP_CONTAINER" php artisan view:clear
echo "✅ Caches cleared"
echo ""

echo "🔒 Step 4: Fixing permissions..."
docker exec "$APP_CONTAINER" chmod -R 777 /var/www/html/storage
docker exec "$APP_CONTAINER" chmod -R 777 /var/www/html/bootstrap/cache
docker exec "$APP_CONTAINER" chown -R www-data:www-data /var/www/html/storage
docker exec "$APP_CONTAINER" chown -R www-data:www-data /var/www/html/bootstrap/cache
echo "✅ Permissions fixed"
echo ""

echo "🗄️ Step 5: Testing database connection..."
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
    exit 1
fi
echo ""

echo "🔄 Step 6: Checking if migrations are needed..."
TABLES_COUNT=$(docker exec "$APP_CONTAINER" php -r "
    try {
        \$pdo = new PDO('mysql:host=mysql;dbname=$MYSQL_DATABASE', '$MYSQL_USER', '$MYSQL_PASSWORD');
        \$stmt = \$pdo->query('SHOW TABLES');
        echo count(\$stmt->fetchAll());
    } catch(Exception \$e) {
        echo '0';
    }
" 2>&1)

if [ "$TABLES_COUNT" = "0" ]; then
    echo "⚠️ No tables found - running migrations..."
    docker exec "$APP_CONTAINER" php artisan migrate --force
    echo "✅ Migrations completed"
else
    echo "✅ Database has $TABLES_COUNT tables"
fi
echo ""

echo "🔗 Step 7: Creating storage symlink..."
docker exec "$APP_CONTAINER" php artisan storage:link || echo "Storage link already exists"
echo ""

echo "⚡ Step 8: Optimizing Laravel for production..."
docker exec "$APP_CONTAINER" php artisan config:cache
docker exec "$APP_CONTAINER" php artisan route:cache
docker exec "$APP_CONTAINER" php artisan view:cache
echo "✅ Optimization complete"
echo ""

echo "🔄 Step 9: Restarting PHP-FPM..."
docker restart "$APP_CONTAINER"
echo "⏳ Waiting for container to restart..."
sleep 5
echo ""

echo "🔍 Step 10: Testing HTTP response..."
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8088 2>&1 || echo "000")
echo "HTTP Status: $HTTP_STATUS"

if [ "$HTTP_STATUS" = "200" ] || [ "$HTTP_STATUS" = "302" ] || [ "$HTTP_STATUS" = "301" ]; then
    echo "✅ Application is now responding!"
    echo ""
    echo "============================================="
    echo "🎉 500 ERROR FIXED!"
    echo "============================================="
    UNRAID_IP=$(hostname -I | awk '{print $1}')
    echo "🌐 Application: http://$UNRAID_IP:8088"
    echo "============================================="
else
    echo "❌ Still getting HTTP $HTTP_STATUS"
    echo ""
    echo "📋 Checking latest Laravel logs:"
    docker exec "$APP_CONTAINER" cat /var/www/html/storage/logs/laravel.log 2>&1 | tail -50 || echo "No logs"
    echo ""
    echo "📋 Checking PHP-FPM logs:"
    docker logs "$APP_CONTAINER" --tail 30
fi
echo ""
