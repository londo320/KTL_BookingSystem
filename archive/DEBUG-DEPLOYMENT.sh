#!/bin/bash

echo "===== KTL Booking System - Debug Script ====="
echo ""

# Check containers
echo "🐳 Container Status:"
docker ps --filter "name=ktl-booking" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
echo ""

# Check if containers exist but stopped
echo "📦 All KTL Containers (including stopped):"
docker ps -a --filter "name=ktl-booking" --format "table {{.Names}}\t{{.Status}}"
echo ""

# Check app container logs
echo "📋 PHP-FPM Container Logs (last 50 lines):"
docker logs ktl-booking-app --tail 50 2>&1
echo ""

# Check nginx logs
echo "📋 Nginx Container Logs (last 30 lines):"
docker logs ktl-booking-nginx --tail 30 2>&1
echo ""

# Check Laravel logs
echo "📋 Laravel Application Logs (last 50 lines):"
docker exec ktl-booking-app cat /var/www/html/storage/logs/laravel.log 2>&1 | tail -50 || echo "Could not read Laravel logs"
echo ""

# Check .env file
echo "🔧 Environment Configuration:"
docker exec ktl-booking-app cat /var/www/html/.env 2>&1 | grep -E "APP_|DB_" || echo "Could not read .env"
echo ""

# Test database connection
echo "🗄️ Database Connection Test:"
docker exec ktl-booking-app php -r "try { \$pdo = new PDO('mysql:host=mysql;dbname=ktl_booking', 'ktl_user', 'ktl_password'); echo 'SUCCESS: Database connected\n'; } catch(Exception \$e) { echo 'FAILED: ' . \$e->getMessage() . '\n'; }" 2>&1
echo ""

# Check permissions
echo "🔒 Storage Permissions:"
docker exec ktl-booking-app ls -la /var/www/html/storage 2>&1 | head -20
echo ""

# Check if artisan works
echo "🚀 Artisan Test:"
docker exec ktl-booking-app php artisan --version 2>&1
echo ""

# Test HTTP response
echo "🌐 HTTP Response Test:"
RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8088 2>&1)
echo "HTTP Status: $RESPONSE"
if [ "$RESPONSE" != "200" ] && [ "$RESPONSE" != "302" ]; then
    echo "⚠️ Getting full response:"
    curl -v http://localhost:8088 2>&1 | head -30
fi
echo ""

echo "===== Debug Complete ====="
echo ""
echo "Please copy all output above and share it for diagnosis"
