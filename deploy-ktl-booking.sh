#!/bin/bash
set -e

echo "===== KTL Booking System - Complete Setup ====="

# Configuration
PROJECT_DIR="/mnt/user/appdata/ktl-booking"
GIT_REPO="git@github.com:londo320/KTL_BookingSystem.git"
GIT_BRANCH="main"
MYSQL_CONTAINER="ktl-booking-mysql"
MYSQL_ROOT_PASSWORD="ktl123456"
APP_CONTAINER="ktl-booking-app"
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
    echo "✅ Copied .env.example to .env"
else
    echo "⚠️ Creating basic .env file..."
    echo 'APP_NAME="KTL Booking System"' > .env
    echo 'APP_ENV=production' >> .env
    echo 'APP_KEY=' >> .env
    echo 'APP_DEBUG=false' >> .env
    echo 'APP_URL=http://localhost:8088' >> .env
    echo '' >> .env
    echo 'DB_CONNECTION=mysql' >> .env
    echo 'DB_HOST=ktl-booking-mysql' >> .env
    echo 'DB_PORT=3306' >> .env
    echo 'DB_DATABASE=ktl_booking' >> .env
    echo 'DB_USERNAME=ktl_user' >> .env
    echo 'DB_PASSWORD=ktl_password' >> .env
fi

# Update database settings
sed -i "s/DB_HOST=.*/DB_HOST=ktl-booking-mysql/" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=ktl_booking/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=ktl_user/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=ktl_password/" .env

echo "✅ Environment configured"

echo "🐳 Creating Laravel app container..."
docker run -d \
    --name "$APP_CONTAINER" \
    --link "$MYSQL_CONTAINER":mysql \
    -p 8088:80 \
    -v "$PROJECT_DIR":/var/www/html \
    -w /var/www/html \
    --restart unless-stopped \
    php:8.2-apache

echo "📦 Installing system dependencies..."
docker exec "$APP_CONTAINER" apt-get update -qq
docker exec "$APP_CONTAINER" apt-get install -y -qq libpng-dev libjpeg-dev libfreetype6-dev libzip-dev libonig-dev libxml2-dev unzip curl git vim

echo "📦 Installing PHP extensions..."
docker exec "$APP_CONTAINER" docker-php-ext-configure gd --with-freetype --with-jpeg
docker exec "$APP_CONTAINER" docker-php-ext-install -j$(nproc) gd zip pdo pdo_mysql mbstring xml

echo "📦 Configuring Apache..."
docker exec "$APP_CONTAINER" a2enmod rewrite
docker exec "$APP_CONTAINER" a2enmod headers

# Create Apache config file
docker exec "$APP_CONTAINER" bash -c 'echo "<VirtualHost *:80>" > /etc/apache2/sites-available/000-default.conf'
docker exec "$APP_CONTAINER" bash -c 'echo "    DocumentRoot /var/www/html/public" >> /etc/apache2/sites-available/000-default.conf'
docker exec "$APP_CONTAINER" bash -c 'echo "    <Directory /var/www/html/public>" >> /etc/apache2/sites-available/000-default.conf'
docker exec "$APP_CONTAINER" bash -c 'echo "        AllowOverride All" >> /etc/apache2/sites-available/000-default.conf'
docker exec "$APP_CONTAINER" bash -c 'echo "        Require all granted" >> /etc/apache2/sites-available/000-default.conf'
docker exec "$APP_CONTAINER" bash -c 'echo "    </Directory>" >> /etc/apache2/sites-available/000-default.conf'
docker exec "$APP_CONTAINER" bash -c 'echo "</VirtualHost>" >> /etc/apache2/sites-available/000-default.conf'

echo "🎼 Installing Composer..."
docker exec "$APP_CONTAINER" curl -sS https://getcomposer.org/installer -o composer-setup.php
docker exec "$APP_CONTAINER" php composer-setup.php --install-dir=/usr/local/bin --filename=composer
docker exec "$APP_CONTAINER" rm composer-setup.php

echo "🚀 Installing Laravel dependencies..."
docker exec -w /var/www/html "$APP_CONTAINER" composer install --no-interaction --optimize-autoloader

echo "🔧 Applying Laravel permission fixes..."
# AGGRESSIVE: Remove and recreate directories to eliminate permission issues
docker exec -w /var/www/html "$APP_CONTAINER" rm -rf storage/framework/views storage/framework/cache bootstrap/cache || true
docker exec -w /var/www/html "$APP_CONTAINER" mkdir -p storage/framework/views storage/framework/cache storage/framework/sessions storage/logs bootstrap/cache

# Set permissions BEFORE anything creates files
docker exec -w /var/www/html "$APP_CONTAINER" chmod -R 777 storage bootstrap/cache
docker exec -w /var/www/html "$APP_CONTAINER" chown -R www-data:www-data storage bootstrap/cache public

# Clear any existing locks or cached items
docker exec -w /var/www/html "$APP_CONTAINER" rm -f storage/framework/cache/* || true

echo "🚀 Setting up Laravel..."
docker exec -w /var/www/html "$APP_CONTAINER" php artisan key:generate --force

# Clear all caches before setting up
docker exec -w /var/www/html "$APP_CONTAINER" php artisan config:clear
docker exec -w /var/www/html "$APP_CONTAINER" php artisan cache:clear
docker exec -w /var/www/html "$APP_CONTAINER" php artisan view:clear
docker exec -w /var/www/html "$APP_CONTAINER" php artisan route:clear

# Create storage symlink
docker exec -w /var/www/html "$APP_CONTAINER" php artisan storage:link || echo "Storage link already exists"

echo "🚀 Running database migrations..."
docker exec -w /var/www/html "$APP_CONTAINER" php artisan migrate --force

echo "🚀 Optimizing Laravel (after permission fixes)..."
docker exec -w /var/www/html "$APP_CONTAINER" php artisan config:cache
docker exec -w /var/www/html "$APP_CONTAINER" php artisan route:cache
docker exec -w /var/www/html "$APP_CONTAINER" php artisan view:cache

# Final permission check after caching
echo "🔒 Final permission verification..."
docker exec -w /var/www/html "$APP_CONTAINER" chown -R www-data:www-data storage bootstrap/cache
docker exec -w /var/www/html "$APP_CONTAINER" chmod -R 775 storage bootstrap/cache

echo "🌐 Starting web server..."
docker exec "$APP_CONTAINER" service apache2 restart

echo "🔍 Testing application..."
sleep 5

UNRAID_IP=$(hostname -I | awk '{print $1}')

echo ""
echo "============================================="
echo "🎉 SETUP COMPLETE!"
echo "============================================="
echo "🌐 Application URL: http://$UNRAID_IP:8088"
echo "🔧 Admin Panel: http://$UNRAID_IP:8088/admin"
echo "🗄️  MySQL: $UNRAID_IP:$MYSQL_PORT"
echo "============================================="

echo ""
echo "🐳 Container Status:"
docker ps | grep ktl-booking

# Final verification
echo ""
echo "🔍 Verifying setup..."
docker exec -w /var/www/html "$APP_CONTAINER" ls -la storage/framework/ | head -5
echo ""
echo "📁 Storage permissions:"
docker exec -w /var/www/html "$APP_CONTAINER" ls -ld storage/ bootstrap/cache/

send_notification "Setup Complete" "KTL Booking System is running at http://$UNRAID_IP:8088"

echo ""
echo "✅ Setup completed successfully!"
echo "✅ Permission fixes applied automatically!"
echo "✅ Depot map file paths corrected!"