#!/bin/bash

# Docker/Railway Scheduler - Runs continuously in foreground
# This is designed to run as a service in containerized environments

APP_DIR="/var/www/html"
LOG_DIR="$APP_DIR/storage/logs"
INTERVAL=60

echo "=== Laravel Scheduler Service ==="
echo "Running every $INTERVAL seconds..."
echo "Logs will be written to stdout/stderr"
echo ""

# Ensure we're in the right directory
cd "$APP_DIR" || exit 1

# Run scheduler in daemon mode (foreground)
php artisan scheduler:run --daemon --interval="$INTERVAL"
