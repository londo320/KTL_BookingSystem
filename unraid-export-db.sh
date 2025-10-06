#!/bin/bash

# Unraid script to export production database and push to git

# Path to your Laravel application on Unraid
APP_PATH="/mnt/user/appdata/ktl-booking"

# Navigate to app directory
cd "$APP_PATH" || exit 1

echo "📦 Exporting production database..."

# Create backups directory if it doesn't exist
mkdir -p database/backups

# Use Laravel's mysqldump to export database
# This reads from your .env file automatically
php artisan db:show

# Get database credentials from .env
DB_CONNECTION=$(grep DB_CONNECTION .env | cut -d '=' -f2)
DB_HOST=$(grep DB_HOST .env | cut -d '=' -f2)
DB_PORT=$(grep DB_PORT .env | cut -d '=' -f2)
DB_DATABASE=$(grep DB_DATABASE .env | cut -d '=' -f2)
DB_USERNAME=$(grep DB_USERNAME .env | cut -d '=' -f2)
DB_PASSWORD=$(grep DB_PASSWORD .env | cut -d '=' -f2)

# Export database
mysqldump -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" > database/backups/production.sql

if [ $? -ne 0 ]; then
    echo "❌ Failed to export database"
    exit 1
fi

echo "✅ Database exported to database/backups/production.sql"
echo "📊 File size: $(du -h database/backups/production.sql | cut -f1)"

echo "💾 Committing to git..."

# Configure git if needed
git config user.name "Unraid Server"
git config user.email "server@unraid.local"

# Add, commit and push
git add database/backups/production.sql
git commit -m "Backup production database $(date '+%Y-%m-%d %H:%M:%S')"
git push

if [ $? -ne 0 ]; then
    echo "❌ Failed to push to git"
    exit 1
fi

echo "✨ Done! Database backup pushed to git."
