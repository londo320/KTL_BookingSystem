#!/bin/bash

# Import production database from git backup
# Run this script locally after pulling the backup from git

echo "📥 Importing production database backup..."

# Get the directory where the script is located
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

BACKUP_FILE="${SCRIPT_DIR}/database/backups/production.sql"

if [ ! -f "$BACKUP_FILE" ]; then
    echo "❌ Backup file not found: $BACKUP_FILE"
    echo "💡 Run 'git pull' to get the latest backup from production"
    exit 1
fi

echo "🗄️  Importing to local database 'test'..."

mysql -u root test < "$BACKUP_FILE"

if [ $? -ne 0 ]; then
    echo "❌ Failed to import database"
    exit 1
fi

echo "✅ Database imported successfully!"
echo "🔄 Running migrations..."
php artisan migrate --force

echo "✨ Done! Your local database is now synced with production."
