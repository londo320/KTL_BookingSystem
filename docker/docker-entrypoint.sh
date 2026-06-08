#!/bin/bash
set -e

echo "🚀 Starting KTL Booking System..."

# Wait for database if DATABASE_URL is set
if [ -n "$DATABASE_URL" ] || [ -n "$DB_HOST" ]; then
    echo "⏳ Waiting for database..."
    sleep 5
fi

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:CHANGEME" ]; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --force --no-interaction
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
