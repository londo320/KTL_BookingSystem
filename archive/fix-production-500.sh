#!/bin/bash

# Quick fix script for production 500 errors
# Run this on the production server

APP_CONTAINER="ktl-booking-app"

echo "🔍 Checking production logs..."
docker exec "$APP_CONTAINER" tail -50 /var/www/html/storage/logs/laravel.log

echo ""
echo "🔧 Clearing all caches..."
docker exec -w /var/www/html "$APP_CONTAINER" php artisan config:clear
docker exec -w /var/www/html "$APP_CONTAINER" php artisan cache:clear
docker exec -w /var/www/html "$APP_CONTAINER" php artisan view:clear
docker exec -w /var/www/html "$APP_CONTAINER" php artisan route:clear

echo ""
echo "🔒 Fixing permissions..."
docker exec -w /var/www/html "$APP_CONTAINER" chmod -R 777 storage bootstrap/cache
docker exec -w /var/www/html "$APP_CONTAINER" chown -R www-data:www-data storage bootstrap/cache public

echo ""
echo "♻️  Restarting PHP-FPM..."
docker restart "$APP_CONTAINER"

echo ""
echo "✅ Done! Check https://bookingsuat.fury.me.uk/login now"
echo ""
echo "If still 500 error, check logs with:"
echo "docker exec $APP_CONTAINER tail -100 /var/www/html/storage/logs/laravel.log"
