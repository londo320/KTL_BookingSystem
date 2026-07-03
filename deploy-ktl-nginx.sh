#!/bin/bash
set -e

echo "===== KTL Booking System - Nginx + PHP-FPM Setup ====="

# Configuration
PROJECT_DIR="/mnt/user/appdata/ktl-booking"
GIT_REPO="git@github.com:londo320/KTL_BookingSystem.git"
GIT_BRANCH="main"

# Custom Docker network name for Unraid
DOCKER_NETWORK="furynet"

# Target your External MariaDB Container Connection Settings
MARIADB_HOST="mysql"       # Must match the exact name of your Unraid MariaDB container
MARIADB_DATABASE="ktl_booking"
MARIADB_USER="londo"
MARIADB_PASSWORD="centuri1"

APP_CONTAINER="ktl-booking-app"
NGINX_CONTAINER="ktl-booking-nginx"
SCHEDULER_CONTAINER="ktl-booking-scheduler"

# Function to send notifications
send_notification() {
    local title="$1"
    local message="$2"
    /usr/local/emhttp/webGui/scripts/notify -e "KTL Booking" -s "$title" -d "$message" -l "normal" 2>/dev/null || true
}

# Automatically handle the Custom Docker network creation if it doesn't exist
if ! docker network inspect "$DOCKER_NETWORK" >/dev/null 2>&1; then
    echo "🌐 Creating custom docker network: $DOCKER_NETWORK..."
    docker network create "$DOCKER_NETWORK"
fi

echo "🧹 Cleaning up existing application setup..."
docker stop "$SCHEDULER_CONTAINER" 2>/dev/null || true
docker rm "$SCHEDULER_CONTAINER" 2>/dev/null || true
docker stop "$NGINX_CONTAINER" 2>/dev/null || true
docker rm "$NGINX_CONTAINER" 2>/dev/null || true
docker stop "$APP_CONTAINER" 2>/dev/null || true
docker rm "$APP_CONTAINER" 2>/dev/null || true

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

echo "⚙️ Setting up environment file..."

# Remove old .env file if it exists and backup
if [ -f ".env" ]; then
    echo "📋 Backing up existing .env..."
    cp .env .env.backup.$(date +%s)
    rm .env
fi

if [ -f ".env.example" ]; then
    echo "📝 Creating fresh .env from .env.example..."
    cp .env.example .env

    # Update configuration targeting external MariaDB
    sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env
    sed -i "s/^DB_HOST=.*/DB_HOST=$MARIADB_HOST/" .env
    sed -i 's/^DB_PORT=.*/DB_PORT=3306/' .env
    rm -f "$PROJECT_DIR/database/schema/mysql-schema.sql"
    sed -i "s/^DB_DATABASE=.*/DB_DATABASE=$MARIADB_DATABASE/" .env
    sed -i "s/^DB_USERNAME=.*/DB_USERNAME=$MARIADB_USER/" .env
    sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=$MARIADB_PASSWORD/" .env

    # Update app configuration
    sed -i 's/^APP_ENV=.*/APP_ENV=production/' .env
    sed -i 's/^APP_DEBUG=.*/APP_DEBUG=false/' .env
    sed -i 's|^APP_URL=.*|APP_URL=https://bookingsuat.fury.me.uk|' .env

    # Add scheduler settings on new lines at the end
    echo "" >> .env
    echo "# Docker Scheduler Configuration" >> .env
    echo "SCHEDULER_MODE=docker" >> .env
    echo "SCHEDULER_CONTAINER_NAME=$SCHEDULER_CONTAINER" >> .env

    echo "✅ Environment configured with external MariaDB credentials"
else
    echo "❌ .env.example not found"
    send_notification "Deployment Failed" ".env.example not found"
    exit 1
fi

echo "🐳 Creating PHP-FPM container on network $DOCKER_NETWORK..."
docker run -d \
    --name "$APP_CONTAINER" \
    --network "$DOCKER_NETWORK" \
    -v "$PROJECT_DIR":/var/www/html \
    -w /var/www/html \
    --restart unless-stopped \
    php:8.2-fpm

echo "📦 Installing system dependencies in PHP container..."
docker exec "$APP_CONTAINER" apt-get update -o Acquire::https::Verify-Peer=false
docker exec "$APP_CONTAINER" apt-get install -y -o Acquire::https::Verify-Peer=false ca-certificates
docker exec "$APP_CONTAINER" apt-get update

docker exec "$APP_CONTAINER" apt-get install -y \
    default-mysql-client \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    unzip \
    curl \
    git \
    vim \
    procps

echo "📦 Installing PHP extensions..."
docker exec "$APP_CONTAINER" docker-php-ext-configure gd --with-freetype --with-jpeg
docker exec "$APP_CONTAINER" docker-php-ext-install -j$(nproc) gd zip pdo_mysql mbstring xml opcache
docker exec "$APP_CONTAINER" docker-php-ext-enable pdo_mysql

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
docker exec -w /var/www/html "$APP_CONTAINER" mkdir -p \
    storage/framework/views \
    storage/framework/cache \
    storage/framework/sessions \
    storage/logs \
    bootstrap/cache \
    storage/app/public/depot-maps

docker exec -w /var/www/html "$APP_CONTAINER" chmod -R 777 storage bootstrap/cache
docker exec -w /var/www/html "$APP_CONTAINER" chown -R www-data:www-data storage bootstrap/cache public

echo "🚀 Setting up Laravel..."
docker exec -w /var/www/html "$APP_CONTAINER" php artisan key:generate --force

# Force clear cache configuration files purely from local storage to keep bootstrap completely clean
docker exec -w /var/www/html "$APP_CONTAINER" php artisan config:clear || true
docker exec -w /var/www/html "$APP_CONTAINER" php artisan route:clear || true
docker exec -w /var/www/html "$APP_CONTAINER" php artisan view:clear || true

# Force clear the application cache drivers completely using flush to bypass database structures
docker exec -w /var/www/html "$APP_CONTAINER" php artisan cache:clear || true

echo "🚀 Running database migrations..."
if docker exec -w /var/www/html "$APP_CONTAINER" php artisan migrate --force; then
    echo "✅ Migrations completed successfully"
else
    echo "❌ Migration failed"
    echo "📋 Checking error logs..."
    docker exec "$APP_CONTAINER" cat /var/www/html/storage/logs/laravel.log | tail -50
    send_notification "Deployment Failed" "Database migration failed"
    exit 1
fi

docker exec -w /var/www/html "$APP_CONTAINER" php artisan storage:link || echo "Storage link already exists"

echo "🚀 Optimizing Laravel..."
docker exec -w /var/www/html "$APP_CONTAINER" php artisan config:cache || true
docker exec -w /var/www/html "$APP_CONTAINER" php artisan route:cache || true
docker exec -w /var/www/html "$APP_CONTAINER" php artisan view:cache || true

echo "🔒 Final permission verification..."
docker exec -w /var/www/html "$APP_CONTAINER" chown -R www-data:www-data storage bootstrap/cache
docker exec -w /var/www/html "$APP_CONTAINER" chmod -R 775 storage bootstrap/cache

# 🌐 Creating Nginx configuration...
cat > "$PROJECT_DIR/nginx.conf" <<'EOF'
server {
    listen 80;
    server_name _;
    root /var/www/html/public;

    index index.php index.html;

    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/json;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

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

        fastcgi_read_timeout 300;
        fastcgi_send_timeout 300;

        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

echo "🐳 Creating Nginx container on network $DOCKER_NETWORK..."
docker run -d \
    --name "$NGINX_CONTAINER" \
    --network "$DOCKER_NETWORK" \
    -p 8088:80 \
    -v "$PROJECT_DIR":/var/www/html:ro \
    -v "$PROJECT_DIR/nginx.conf":/etc/nginx/conf.d/default.conf:ro \
    --restart unless-stopped \
    nginx:alpine

echo "⏰ Setting up Scheduler with Cron..."
# Install cron
docker exec "$APP_CONTAINER" apt-get install -y -qq cron

# Create cron job to run Laravel scheduler every minute (use full PHP path for cron)
docker exec "$APP_CONTAINER" bash -c 'cat > /etc/cron.d/laravel-scheduler <<EOF
* * * * * www-data cd /var/www/html && /usr/local/bin/php artisan schedule:run >> /var/www/html/storage/logs/scheduler-cron.log 2>&1
EOF'

# Set permissions
docker exec "$APP_CONTAINER" chmod 0644 /etc/cron.d/laravel-scheduler

# Start cron service
docker exec "$APP_CONTAINER" service cron start

# Verify cron is running
if docker exec "$APP_CONTAINER" service cron status | grep -q "running"; then
    echo "✅ Cron-based scheduler started successfully"
else
    echo "⚠️ Cron may not have started - retrying..."
    docker exec "$APP_CONTAINER" service cron restart
fi

echo "🔍 Testing application..."
sleep 5

UNRAID_IP=$(hostname -I | awk '{print $1}')
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8088" || echo "000")

if [ "$HTTP_STATUS" = "200" ] || [ "$HTTP_STATUS" = "302" ]; then
    echo "✅ Application is responding (HTTP $HTTP_STATUS)"
else
    echo "⚠️  Application returned HTTP $HTTP_STATUS"
    echo "📋 Checking PHP-FPM logs..."
    docker logs "$APP_CONTAINER" --tail 50
    echo "📋 Checking Nginx logs..."
    docker logs "$NGINX_CONTAINER" --tail 50
fi

echo ""
echo "============================================="
echo "🎉 SETUP COMPLETE!"
echo "============================================="
echo "🌐 Application URL: http://$UNRAID_IP:8088"
echo "🔧 Admin Panel: http://$UNRAID_IP:8088/app/dashboard"
echo "⏰ Scheduler Panel: http://$UNRAID_IP:8088/admin/scheduler"
echo "🗄️  Using External MariaDB Host: $MARIADB_HOST"
echo "============================================="
echo "⚡ Using Nginx + PHP-FPM for maximum performance!"
echo "⏰ Scheduler running via cron (every minute) inside PHP-FPM container!"
echo "✅ OPcache enabled for faster PHP execution"
echo "✅ Gzip compression enabled"
echo "✅ Static asset caching enabled"
echo "============================================="

echo ""
echo "🐳 Container Status:"
docker ps --filter "name=ktl-booking" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"

echo ""
echo "📊 Scheduler Status:"
if docker exec "$APP_CONTAINER" service cron status 2>/dev/null | grep -q "running"; then
    echo "✅ Cron service is running"
    if docker exec "$APP_CONTAINER" test -f /etc/cron.d/laravel-scheduler; then
        echo "✅ Laravel scheduler cron job is configured"
        echo "📄 Cron job: Runs every minute"
    fi
else
    echo "⚠️ Cron service not detected"
fi

echo ""
echo "🔧 Final verification and fixes..."

docker exec "$APP_CONTAINER" php artisan key:generate --force --ansi 2>/dev/null || echo "APP_KEY already set"
docker exec "$APP_CONTAINER" chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
docker exec "$APP_CONTAINER" chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
docker exec "$APP_CONTAINER" php artisan storage:link 2>/dev/null || echo "Storage link already exists"

echo "🔄 Restarting PHP-FPM to ensure clean state..."
docker restart "$APP_CONTAINER"
sleep 5

echo "⏰ Restarting cron service after container restart..."
docker exec "$APP_CONTAINER" service cron start

docker restart "$NGINX_CONTAINER"
sleep 3

echo ""
echo "🔍 Final HTTP test..."
HTTP_STATUS_FINAL=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8088" || echo "000")

if [ "$HTTP_STATUS_FINAL" = "200" ] || [ "$HTTP_STATUS_FINAL" = "302" ] || [ "$HTTP_STATUS_FINAL" = "301" ]; then
    echo "✅ Application is responding perfectly (HTTP $HTTP_STATUS_FINAL)"
    send_notification "Setup Complete" "KTL Booking System is running at http://$UNRAID_IP:8088"
else
    echo "⚠️ Application returned HTTP $HTTP_STATUS_FINAL"
    echo ""
    echo "Running automatic fix..."
    docker exec "$APP_CONTAINER" php artisan config:clear || true
    docker exec "$APP_CONTAINER" php artisan cache:clear || true
    docker exec "$APP_CONTAINER" chmod -R 777 /var/www/html/storage || true
    docker restart "$APP_CONTAINER"
    sleep 5
    docker restart "$NGINX_CONTAINER"
    sleep 3
fi

echo ""
echo "📝 Next Steps:"
echo "   1. Visit http://$UNRAID_IP:8088 to access the application"
echo "   2. Check scheduler at http://$UNRAID_IP:8088/admin/scheduler"
echo "   3. All 4 scheduled tasks run automatically every minute via cron"
echo "   4. Verify scheduler: bash verify-scheduler.sh"
echo "   5. View scheduler logs: docker exec $APP_CONTAINER tail -f /var/www/html/storage/logs/scheduler-cron.log"
echo ""
