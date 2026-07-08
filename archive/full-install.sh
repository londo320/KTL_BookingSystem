#!/bin/bash
set -e

echo "===== KTL Booking System - Full Install ====="
echo "(App containers only — connects to your existing external MariaDB, does not create/reset a database)"

# Configuration
PROJECT_DIR="/mnt/user/appdata/ktl-booking"
GIT_REPO="git@github.com:londo320/KTL_BookingSystem.git"
GIT_BRANCH="main"

# MARIADB_PASSWORD is required and must be exported by the caller — never hardcode it here.
# e.g. on Unraid: source ~/.ktl-deploy-env   (a file that lives outside this repo)
: "${MARIADB_PASSWORD:?Set MARIADB_PASSWORD in your environment before running this script, e.g. MARIADB_PASSWORD='...' ./full-install.sh}"

# Get latest code on HOST (outside container)
if [ ! -d "$PROJECT_DIR/.git" ]; then
    echo "📁 Cloning repository..."
    mkdir -p "$PROJECT_DIR"
    cd "$PROJECT_DIR"
    GIT_SSH_COMMAND="ssh -o StrictHostKeyChecking=no" git clone "$GIT_REPO" .
    git checkout "$GIT_BRANCH"
else
    echo "📥 Updating repository..."
    cd "$PROJECT_DIR"
    git fetch origin "$GIT_BRANCH"
    git reset --hard "origin/$GIT_BRANCH"
fi

chmod +x deploy-ktl-nginx.sh

echo ""
echo "🚀 Running full install (deploy-ktl-nginx.sh)..."
echo "   ⚠️  This stops/recreates the app, nginx, and scheduler containers, and issues"
echo "      a brand-new APP_KEY, which will log out any active sessions."
echo ""

# MARIADB_PASSWORD is already exported in this shell, so deploy-ktl-nginx.sh picks it up.
./deploy-ktl-nginx.sh

echo ""
echo "✅ Full install complete!"
