#!/bin/bash

echo "===== Checking Laravel Application Logs ====="
echo ""

echo "📋 Laravel Error Log (last 100 lines):"
docker exec ktl-booking-app cat /var/www/html/storage/logs/laravel.log 2>&1 | tail -100

echo ""
echo "================================"
echo ""

echo "🔍 Checking .env configuration:"
docker exec ktl-booking-app cat /var/www/html/.env 2>&1 | grep -E "APP_|DB_"

echo ""
echo "================================"
echo ""

echo "🗄️ Testing database connection:"
docker exec ktl-booking-app php -r "
try {
    \$pdo = new PDO('mysql:host=mysql;dbname=ktl_booking', 'ktl_user', 'ktl_password');
    echo '✅ Database connection successful\n';

    // Check if migrations have run
    \$stmt = \$pdo->query('SHOW TABLES');
    \$tables = \$stmt->fetchAll(PDO::FETCH_COLUMN);
    echo 'Tables found: ' . count(\$tables) . '\n';

    if (count(\$tables) == 0) {
        echo '⚠️ No tables found - migrations may not have run!\n';
    } else {
        echo '✅ Database has tables\n';
    }
} catch(Exception \$e) {
    echo '❌ Database connection failed: ' . \$e->getMessage() . '\n';
}
" 2>&1

echo ""
echo "================================"
echo ""

echo "🔑 Checking if APP_KEY is set:"
docker exec ktl-booking-app php artisan tinker --execute="echo config('app.key') ? '✅ APP_KEY is set' : '❌ APP_KEY is missing';" 2>&1

echo ""
echo "================================"
echo ""

echo "📁 Checking storage permissions:"
docker exec ktl-booking-app ls -la /var/www/html/storage 2>&1 | head -15

echo ""
echo "================================"
