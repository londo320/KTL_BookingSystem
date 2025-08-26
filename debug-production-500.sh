#!/bin/bash

echo "🔍 Debugging Production 500 Error..."
echo "===================================="

# 1. Check Laravel logs for recent errors
echo "📋 Recent Laravel Error Logs:"
echo "------------------------------"
tail -20 storage/logs/laravel.log 2>/dev/null || echo "❌ No Laravel log file found"

echo ""
echo "📋 Today's Laravel Errors:"
echo "-------------------------"
grep "$(date '+%Y-%m-%d')" storage/logs/laravel.log | tail -10 2>/dev/null || echo "No errors today"

echo ""
echo "📋 PHP Error Log (if available):"
echo "--------------------------------"
tail -10 /var/log/php_errors.log 2>/dev/null || echo "PHP error log not found at /var/log/php_errors.log"

echo ""
echo "🔗 Storage Symlink Test:"
echo "------------------------"
if [ -L "public/storage" ]; then
    echo "✅ Storage symlink exists"
    ls -la public/storage
    echo "Target: $(readlink public/storage)"
else
    echo "❌ Storage symlink missing"
fi

echo ""
echo "📁 Storage Directory Check:"
echo "--------------------------"
echo "storage/ permissions:"
ls -ld storage/ 2>/dev/null || echo "storage directory missing"

echo "storage/app/public/depot-maps/ check:"
ls -la storage/app/public/depot-maps/ 2>/dev/null || echo "depot-maps directory missing"

echo ""
echo "🎯 Depot Map Files:"
echo "------------------"
find storage/app/public/depot-maps/ -name "*.svg" 2>/dev/null || echo "No SVG files found"

echo ""
echo "🔍 Database Connection Test:"
echo "---------------------------"
php artisan tinker --execute "
try {
    echo 'Users count: ' . App\Models\User::count() . PHP_EOL;
    echo 'Depots count: ' . App\Models\Depot::count() . PHP_EOL;
    \$depot = App\Models\Depot::first();
    if (\$depot) {
        echo 'First depot: ' . \$depot->name . PHP_EOL;
        echo 'Map file: ' . (\$depot->map_file ?? 'null') . PHP_EOL;
    }
    echo '✅ Database connection OK' . PHP_EOL;
} catch (Exception \$e) {
    echo '❌ Database error: ' . \$e->getMessage() . PHP_EOL;
}
" 2>/dev/null || echo "❌ Artisan/Database connection failed"

echo ""
echo "🌐 Web Server Error Logs:"
echo "-------------------------"
tail -5 /var/log/nginx/error.log 2>/dev/null || echo "Nginx error log not found"
tail -5 /var/log/apache2/error.log 2>/dev/null || echo "Apache error log not found"

echo ""
echo "🔧 Laravel Configuration:"
echo "------------------------"
php artisan config:show app.debug 2>/dev/null || echo "Cannot check debug status"
echo "Environment: $(php artisan env 2>/dev/null || echo 'unknown')"

echo ""
echo "===================================="
echo "✅ Debug information collected!"