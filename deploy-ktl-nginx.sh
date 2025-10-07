#!/bin/bash
set -e

echo "===== KTL Booking System - Nginx + PHP-FPM Setup ====="

# Configuration
PROJECT_DIR="/mnt/user/appdata/ktl-booking"
GIT_REPO="git@github.com:londo320/KTL_BookingSystem.git"
GIT_BRANCH="main"
MYSQL_CONTAINER="ktl-booking-mysql"
MYSQL_ROOT_PASSWORD="ktl123456"
APP_CONTAINER="ktl-booking-app"
NGINX_CONTAINER="ktl-booking-nginx"
MYSQL_PORT="3307"

# Function to send notifications
send_notification() {
    local title="$1"
    local message="$2"
    /usr/local/emhttp/webGui/scripts/notify -e "KTL Booking" -s "$title" -d "$message" -l "normal" 2>/dev/null || true
}

echo "🔍 Checking for port conflicts..."
if netstat -ln | grep -q ":3306 "; then
    echo "⚠️  Port 3306 is in use, using port $MYSQL_PORT for MySQL instead"
else
    MYSQL_PORT="3306"
    echo "✅ Port 3306 is available"
fi

echo "🧹 Cleaning up any existing setup..."
docker stop "$NGINX_CONTAINER" 2>/dev/null || true
docker rm "$NGINX_CONTAINER" 2>/dev/null || true
docker stop "$APP_CONTAINER" 2>/dev/null || true
docker rm "$APP_CONTAINER" 2>/dev/null || true
docker stop "$MYSQL_CONTAINER" 2>/dev/null || true
docker rm "$MYSQL_CONTAINER" 2>/dev/null || true

# Repository check
if [ ! -d "$PROJECT_DIR" ] || [ ! -f "$PROJECT_DIR/composer.json" ]; then
    echo "📁 Setting up project directory..."
    mkdir -p "$PROJECT_DIR"
    cd "$PROJECT_DIR"
    echo "📥 Cloning repository via SSH..."
    GIT_SSH_COMMAND="ssh -o StrictHostKeyChecking=no" git clone "$GIT_REPO" .
    git checkout "$GIT_BRANCH"
else
    echo "📁 Using existing repository..."
    cd "$PROJECT_DIR"
    echo "📥 Updating existing repository..."
    git fetch origin "$GIT_BRANCH"
    git reset --hard "origin/$GIT_BRANCH"
fi

echo "✅ Repository ready!"

echo "🐳 Creating MySQL container on port $MYSQL_PORT..."
docker run -d \
    --name "$MYSQL_CONTAINER" \
    -e MYSQL_ROOT_PASSWORD="$MYSQL_ROOT_PASSWORD" \
    -e MYSQL_DATABASE=ktl_booking \
    -e MYSQL_USER=ktl_user \
    -e MYSQL_PASSWORD=ktl_password \
    -p "$MYSQL_PORT":3306 \
    -v ktl-mysql-data:/var/lib/mysql \
    --restart unless-stopped \
    mysql:8.0

echo "⏳ Waiting for MySQL to initialize..."
sleep 30

echo "🔍 Checking MySQL readiness..."
mysql_ready=0
for i in {1..20}; do
    if docker exec "$MYSQL_CONTAINER" mysqladmin ping -u root -p"$MYSQL_ROOT_PASSWORD" --silent 2>/dev/null; then
        mysql_ready=1
        echo "✅ MySQL is ready!"
        break
    fi
    echo "⏳ Attempt $i/20 - waiting for MySQL..."
    sleep 3
done

if [ $mysql_ready -eq 0 ]; then
    echo "❌ MySQL failed to start"
    exit 1
fi

echo "⚙️ Setting up environment file..."
if [ -f ".env.example" ]; then
    cp .env.example .env
    echo "✅ Copied .env.example to .env with complete production configuration"
else
    echo "❌ .env.example not found - this should not happen"
    exit 1
fi

echo "✅ Environment configured"

echo "🐳 Creating PHP-FPM container..."
docker run -d \
    --name "$APP_CONTAINER" \
    --link "$MYSQL_CONTAINER":mysql \
    -v "$PROJECT_DIR":/var/www/html \
    -w /var/www/html \
    --restart unless-stopped \
    php:8.2-fpm

echo "📦 Installing system dependencies in PHP container..."
docker exec "$APP_CONTAINER" apt-get update -qq
docker exec "$APP_CONTAINER" apt-get install -y -qq libpng-dev libjpeg-dev libfreetype6-dev libzip-dev libonig-dev libxml2-dev unzip curl git vim

echo "📦 Installing PHP extensions..."
docker exec "$APP_CONTAINER" docker-php-ext-configure gd --with-freetype --with-jpeg
docker exec "$APP_CONTAINER" docker-php-ext-install -j$(nproc) gd zip pdo pdo_mysql mbstring xml opcache

echo "📦 Configuring PHP OPcache for performance..."
docker exec "$APP_CONTAINER" bash -c 'cat > /usr/local/etc/php/conf.d/opcache.ini <<EOF
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.save_comments=1
opcache.fast_shutdown=1
EOF'

echo "📦 Optimizing PHP settings..."
docker exec "$APP_CONTAINER" bash -c 'cat > /usr/local/etc/php/conf.d/custom.ini <<EOF
upload_max_filesize=20M
post_max_size=20M
max_execution_time=300
memory_limit=512M
EOF'

echo "🎼 Installing Composer..."
docker exec "$APP_CONTAINER" curl -sS https://getcomposer.org/installer -o composer-setup.php
docker exec "$APP_CONTAINER" php composer-setup.php --install-dir=/usr/local/bin --filename=composer
docker exec "$APP_CONTAINER" rm composer-setup.php

echo "🚀 Installing Laravel dependencies..."
docker exec -w /var/www/html "$APP_CONTAINER" composer install --no-interaction --optimize-autoloader --no-dev

echo "🔧 Applying Laravel permission fixes..."
docker exec -w /var/www/html "$APP_CONTAINER" rm -rf storage/framework/views storage/framework/cache bootstrap/cache || true
docker exec -w /var/www/html "$APP_CONTAINER" mkdir -p storage/framework/views storage/framework/cache storage/framework/sessions storage/logs bootstrap/cache storage/app/public/depot-maps

docker exec -w /var/www/html "$APP_CONTAINER" chmod -R 777 storage bootstrap/cache
docker exec -w /var/www/html "$APP_CONTAINER" chown -R www-data:www-data storage bootstrap/cache public

docker exec -w /var/www/html "$APP_CONTAINER" rm -f storage/framework/cache/* || true

echo "🚀 Setting up Laravel..."
docker exec -w /var/www/html "$APP_CONTAINER" php artisan key:generate --force

docker exec -w /var/www/html "$APP_CONTAINER" php artisan config:clear
docker exec -w /var/www/html "$APP_CONTAINER" php artisan cache:clear
docker exec -w /var/www/html "$APP_CONTAINER" php artisan view:clear
docker exec -w /var/www/html "$APP_CONTAINER" php artisan route:clear

docker exec -w /var/www/html "$APP_CONTAINER" php artisan storage:link || echo "Storage link already exists"

echo "🚀 Running database migrations..."
docker exec -w /var/www/html "$APP_CONTAINER" php artisan migrate --force

echo "🚀 Optimizing Laravel..."
docker exec -w /var/www/html "$APP_CONTAINER" php artisan config:cache
docker exec -w /var/www/html "$APP_CONTAINER" php artisan route:cache
docker exec -w /var/www/html "$APP_CONTAINER" php artisan view:cache

echo "🔒 Final permission verification..."
docker exec -w /var/www/html "$APP_CONTAINER" chown -R www-data:www-data storage bootstrap/cache
docker exec -w /var/www/html "$APP_CONTAINER" chmod -R 775 storage bootstrap/cache

echo "🌐 Creating Nginx configuration..."
cat > "$PROJECT_DIR/nginx.conf" <<'EOF'
server {
    listen 80;
    server_name _;
    root /var/www/html/public;

    index index.php index.html;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/json;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Client body size
    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass ktl-booking-app:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;

        # PHP-FPM timeouts
        fastcgi_read_timeout 300;
        fastcgi_send_timeout 300;

        # Buffer settings for better performance
        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Deny access to sensitive files
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

echo "🐳 Creating Nginx container..."
docker run -d \
    --name "$NGINX_CONTAINER" \
    --link "$APP_CONTAINER":php-fpm \
    -p 8088:80 \
    -v "$PROJECT_DIR":/var/www/html:ro \
    -v "$PROJECT_DIR/nginx.conf":/etc/nginx/conf.d/default.conf:ro \
    --restart unless-stopped \
    nginx:alpine

echo "🔍 Testing application..."
sleep 5

UNRAID_IP=$(hostname -I | awk '{print $1}')

echo ""
echo "============================================="
echo "🎉 SETUP COMPLETE!"
echo "============================================="
echo "🌐 Application URL: http://$UNRAID_IP:8088"
echo "🔧 Admin Panel: http://$UNRAID_IP:8088/app"
echo "🗄️  MySQL: $UNRAID_IP:$MYSQL_PORT"
echo "============================================="
echo "⚡ Using Nginx + PHP-FPM for maximum performance!"
echo "✅ OPcache enabled for faster PHP execution"
echo "✅ Gzip compression enabled"
echo "✅ Static asset caching enabled"
echo "============================================="

echo ""
echo "🐳 Container Status:"
docker ps | grep ktl-booking

send_notification "Setup Complete" "KTL Booking System is running at http://$UNRAID_IP:8088 (Nginx + PHP-FPM)"

echo ""
echo "✅ Setup completed successfully with performance optimizations!"
