#!/bin/bash

echo "🔧 Fixing max_input_vars PHP setting..."

APP_CONTAINER="ktl-booking-app"

echo "📝 Updating PHP configuration..."
docker exec "$APP_CONTAINER" bash -c 'cat > /usr/local/etc/php/conf.d/custom.ini <<EOF
upload_max_filesize=20M
post_max_size=20M
max_execution_time=300
memory_limit=512M
max_input_vars=5000
EOF'

echo "🔄 Restarting PHP-FPM..."
docker exec "$APP_CONTAINER" pkill -USR2 php-fpm || docker restart "$APP_CONTAINER"

echo ""
echo "✅ PHP max_input_vars increased to 5000"
echo "✅ You can now delete large numbers of slots via web UI"
echo ""
echo "Verify with:"
echo "  docker exec $APP_CONTAINER php -i | grep max_input_vars"
