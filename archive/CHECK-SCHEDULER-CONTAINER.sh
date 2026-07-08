#!/bin/bash

echo "===== Checking Scheduler Container ====="
echo ""

echo "🐳 All KTL Containers:"
docker ps --filter "name=ktl-booking" --format "table {{.Names}}\t{{.Status}}\t{{.Command}}"
echo ""

echo "📋 Scheduler Container Details:"
if docker ps --format '{{.Names}}' | grep -q "ktl-booking-scheduler"; then
    echo "✅ Scheduler container is running"
    echo ""

    echo "🔍 Process inside scheduler container:"
    docker exec ktl-booking-scheduler ps aux | grep -E "php|scheduler" | grep -v grep
    echo ""

    echo "📋 Scheduler logs (last 30 lines):"
    docker logs ktl-booking-scheduler --tail 30
else
    echo "❌ Scheduler container NOT found"
    echo ""
    echo "To start scheduler container:"
    echo "docker run -d \\"
    echo "    --name ktl-booking-scheduler \\"
    echo "    --link ktl-booking-mysql:mysql \\"
    echo "    -v /mnt/user/appdata/ktl-booking:/var/www/html \\"
    echo "    -w /var/www/html \\"
    echo "    --restart unless-stopped \\"
    echo "    php:8.2-fpm \\"
    echo "    php artisan scheduler:run --daemon --interval=60"
fi
echo ""
