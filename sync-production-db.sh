#!/bin/bash

# Sync production database to local development environment
# This script pulls the database from production and imports it locally

echo "🔄 Syncing production database to local..."

# Configuration - Update these with your production details
PROD_HOST="your-production-host.com"
PROD_USER="your-ssh-user"
PROD_DB_NAME="your_production_database"
PROD_DB_USER="your_production_db_user"
PROD_DB_PASS="your_production_db_password"

LOCAL_DB_NAME="test"
LOCAL_DB_USER="root"
LOCAL_DB_PASS=""

# Create temporary filename
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
DUMP_FILE="production_db_${TIMESTAMP}.sql"

echo "📥 Dumping production database..."
ssh ${PROD_USER}@${PROD_HOST} "mysqldump -u${PROD_DB_USER} -p${PROD_DB_PASS} ${PROD_DB_NAME}" > ${DUMP_FILE}

if [ $? -ne 0 ]; then
    echo "❌ Failed to dump production database"
    exit 1
fi

echo "📦 Production database dumped to ${DUMP_FILE}"
echo "💾 Importing to local database..."

# Drop and recreate local database
mysql -u${LOCAL_DB_USER} -e "DROP DATABASE IF EXISTS ${LOCAL_DB_NAME};"
mysql -u${LOCAL_DB_USER} -e "CREATE DATABASE ${LOCAL_DB_NAME};"

# Import the dump
mysql -u${LOCAL_DB_USER} ${LOCAL_DB_NAME} < ${DUMP_FILE}

if [ $? -ne 0 ]; then
    echo "❌ Failed to import database"
    exit 1
fi

echo "✅ Database synced successfully!"
echo "🗑️  Removing dump file..."
rm ${DUMP_FILE}

echo "🔄 Running migrations (if any)..."
php artisan migrate --force

echo "✨ Done! Your local database is now synced with production."
