#!/bin/bash
set -e

echo "🚀 Starting KTL Booking System..."

# Wait for database if DATABASE_URL is set
if [ -n "$DATABASE_URL" ] || [ -n "$DB_HOST" ]; then
    echo "⏳ Waiting for database..."
    sleep 5
fi

# Create .env file from environment variables if it doesn't exist
if [ ! -f /var/www/html/.env ]; then
    echo "📝 Creating .env file from environment variables..."
    cp /var/www/html/.env.example /var/www/html/.env

    # Set environment variables in .env
    sed -i "s/APP_NAME=.*/APP_NAME=\"${APP_NAME:-Laravel}\"/" /var/www/html/.env
    sed -i "s/APP_ENV=.*/APP_ENV=${APP_ENV:-production}/" /var/www/html/.env
    sed -i "s/APP_DEBUG=.*/APP_DEBUG=${APP_DEBUG:-false}/" /var/www/html/.env
    sed -i "s|APP_URL=.*|APP_URL=${APP_URL:-http://localhost}|" /var/www/html/.env

    sed -i "s/DB_CONNECTION=.*/DB_CONNECTION=${DB_CONNECTION:-mysql}/" /var/www/html/.env
    sed -i "s/DB_HOST=.*/DB_HOST=${DB_HOST:-127.0.0.1}/" /var/www/html/.env
    sed -i "s/DB_PORT=.*/DB_PORT=${DB_PORT:-3306}/" /var/www/html/.env
    sed -i "s/DB_DATABASE=.*/DB_DATABASE=${DB_DATABASE:-laravel}/" /var/www/html/.env
    sed -i "s/DB_USERNAME=.*/DB_USERNAME=${DB_USERNAME:-root}/" /var/www/html/.env
    sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=${DB_PASSWORD:-}/" /var/www/html/.env
fi

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:CHANGEME" ]; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --force --no-interaction
else
    # Set APP_KEY in .env
    sed -i "s|APP_KEY=.*|APP_KEY=${APP_KEY}|" /var/www/html/.env
fi

# Run migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force --no-interaction || echo "⚠️ Migrations failed (this is okay on first run)"

# Clear and cache config
echo "⚡ Optimizing Laravel..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link
echo "🔗 Creating storage symlink..."
php artisan storage:link || echo "Storage link already exists"

# Fix permissions
echo "🔒 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "✅ Initialization complete!"
echo ""

# Execute CMD
exec "$@"
