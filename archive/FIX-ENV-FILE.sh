#!/bin/bash
set -e

echo "===== Fixing Corrupted .env File ====="
echo ""

APP_CONTAINER="ktl-booking-app"
PROJECT_DIR="/mnt/user/appdata/ktl-booking"

cd "$PROJECT_DIR" || exit 1

echo "🔍 Step 1: Backing up current .env file..."
cp .env .env.backup.$(date +%s)
echo "✅ Backup created"
echo ""

echo "🔍 Step 2: Checking for corruption..."
if grep -q '"SCHEDULER_MODE=' .env; then
    echo "⚠️ Found corruption - SCHEDULER_MODE appended to another line"

    echo "🔧 Fixing .env file..."

    # Remove the corrupted SCHEDULER_MODE and SCHEDULER_CONTAINER_NAME from wherever they are
    sed -i 's/SCHEDULER_MODE=docker//g' .env
    sed -i 's/SCHEDULER_CONTAINER_NAME=ktl-booking-scheduler//g' .env

    # Remove any lines that are just quotes or whitespace
    sed -i '/^[[:space:]]*$/d' .env

    # Add SCHEDULER_MODE and SCHEDULER_CONTAINER_NAME at the end properly
    if ! grep -q "^SCHEDULER_MODE=" .env; then
        echo "" >> .env
        echo "SCHEDULER_MODE=docker" >> .env
    fi

    if ! grep -q "^SCHEDULER_CONTAINER_NAME=" .env; then
        echo "SCHEDULER_CONTAINER_NAME=ktl-booking-scheduler" >> .env
    fi

    echo "✅ .env file fixed"
else
    echo "✅ No corruption found in standard locations"

    # Still add the variables if missing
    if ! grep -q "^SCHEDULER_MODE=" .env; then
        echo "" >> .env
        echo "SCHEDULER_MODE=docker" >> .env
        echo "✅ Added SCHEDULER_MODE=docker"
    fi

    if ! grep -q "^SCHEDULER_CONTAINER_NAME=" .env; then
        echo "SCHEDULER_CONTAINER_NAME=ktl-booking-scheduler" >> .env
        echo "✅ Added SCHEDULER_CONTAINER_NAME"
    fi
fi
echo ""

echo "🔍 Step 3: Validating .env file with Laravel..."
if docker exec "$APP_CONTAINER" php artisan config:clear 2>&1 | grep -q "invalid"; then
    echo "❌ .env file is still invalid"
    ENV_VALID=false
else
    echo "✅ .env file is valid"
    ENV_VALID=true
fi

if [ "$ENV_VALID" = "true" ]; then
    echo ""
    echo "🔑 Step 4: Generating new APP_KEY..."
    docker exec "$APP_CONTAINER" php artisan key:generate --force

    echo ""
    echo "🧹 Step 5: Clearing caches..."
    docker exec "$APP_CONTAINER" php artisan config:clear
    docker exec "$APP_CONTAINER" php artisan cache:clear

    echo ""
    echo "🔄 Step 6: Restarting containers..."
    docker restart "$APP_CONTAINER"
    sleep 3
    docker restart ktl-booking-nginx
    sleep 2

    echo ""
    echo "🔍 Step 7: Testing application..."
    HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8088 2>&1 || echo "000")

    if [ "$HTTP_STATUS" = "200" ] || [ "$HTTP_STATUS" = "302" ]; then
        echo "✅ Application is responding (HTTP $HTTP_STATUS)"
        echo ""
        echo "============================================="
        echo "🎉 .ENV FILE FIXED!"
        echo "============================================="
        UNRAID_IP=$(hostname -I | awk '{print $1}')
        echo "🌐 Application: http://$UNRAID_IP:8088"
        echo "============================================="
    else
        echo "⚠️ Still getting HTTP $HTTP_STATUS"
        echo ""
        echo "📋 Check Laravel logs:"
        docker exec "$APP_CONTAINER" cat /var/www/html/storage/logs/laravel.log 2>&1 | tail -30
    fi
else
    echo ""
    echo "❌ .env file is still invalid. Manual fix required."
    echo "Creating fresh .env from .env.example..."

    if [ -f .env.example ]; then
        cp .env.example .env

        # Configure it properly
        sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env
        sed -i 's/DB_HOST=.*/DB_HOST=mysql/' .env
        sed -i 's/DB_PORT=.*/DB_PORT=3306/' .env
        sed -i 's/DB_DATABASE=.*/DB_DATABASE=ktl_booking/' .env
        sed -i 's/DB_USERNAME=.*/DB_USERNAME=ktl_user/' .env
        sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=ktl_password/' .env
        sed -i 's/APP_ENV=.*/APP_ENV=production/' .env
        sed -i 's/APP_DEBUG=.*/APP_DEBUG=false/' .env

        echo "" >> .env
        echo "SCHEDULER_MODE=docker" >> .env
        echo "SCHEDULER_CONTAINER_NAME=ktl-booking-scheduler" >> .env

        echo "✅ Fresh .env file created from .env.example"
        echo "🔄 Run this script again to continue"
    fi
fi
echo ""
