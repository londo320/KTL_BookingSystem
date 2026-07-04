#!/bin/bash
set -e

echo "===== KTL Booking System - Quick Deploy ====="

# Configuration
PROJECT_DIR="/mnt/user/appdata/ktl-booking"
GIT_REPO="git@github.com:londo320/KTL_BookingSystem.git"
GIT_BRANCH="main"
APP_CONTAINER="ktl-booking-app"

# Check if container exists
if ! docker ps -a --format '{{.Names}}' | grep -q "^${APP_CONTAINER}$"; then
    echo "❌ Container ${APP_CONTAINER} does not exist!"
    echo "   Run the full deployment first: ./deploy-ktl.sh"
    exit 1
fi

# Get latest code on HOST (outside container)
if [ ! -d "$PROJECT_DIR" ]; then
    echo "📁 Cloning repository..."
    mkdir -p "$PROJECT_DIR"
    cd "$PROJECT_DIR"
    GIT_SSH_COMMAND="ssh -o StrictHostKeyChecking=no" git clone "$GIT_REPO" .
    git checkout "$GIT_BRANCH"
else
    echo "📥 Updating repository..."
    cd "$PROJECT_DIR"
    git fetch origin "$GIT_BRANCH" 2>&1 || echo "WARNING: git fetch failed"
    git reset --hard "origin/$GIT_BRANCH" 2>&1 || echo "WARNING: git reset failed"
fi

# Remove schema file (prevents mysql CLI dependency)
SCHEMA_FILE="$PROJECT_DIR/database/schema/mysql-schema.sql"
if [ -f "$SCHEMA_FILE" ]; then
    rm -f "$SCHEMA_FILE"
fi

# Run quick update script
QUICK_UPDATE_SCRIPT="$PROJECT_DIR/quick-update.sh"

if [ ! -f "$QUICK_UPDATE_SCRIPT" ]; then
    echo "❌ ERROR: quick-update.sh not found at $QUICK_UPDATE_SCRIPT"
    echo "   Pull the latest code or run full deployment: ./deploy-ktl.sh"
    exit 1
fi

chmod +x "$QUICK_UPDATE_SCRIPT"
echo ""
echo "🚀 Running quick update..."
echo ""
"$QUICK_UPDATE_SCRIPT"

echo ""
echo "✅ Quick deployment complete!"
