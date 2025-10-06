#!/bin/bash

# Export production database to git
# Run this script on the production server

echo "📦 Exporting production database..."

# Get the directory where the script is located
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Export database
php artisan db:dump --path="${SCRIPT_DIR}/database/backups/production.sql"

if [ $? -ne 0 ]; then
    echo "❌ Failed to export database"
    exit 1
fi

echo "✅ Database exported to database/backups/production.sql"
echo "💾 Committing to git..."

git add database/backups/production.sql
git commit -m "Backup production database for local sync"
git push

echo "✨ Done! Database backup pushed to git."
