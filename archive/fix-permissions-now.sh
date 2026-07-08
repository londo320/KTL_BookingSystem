#!/bin/bash

echo "🚨 EMERGENCY Laravel Permission Fix"
echo "=================================="

# Check if running in Docker container
if [ -f /.dockerenv ]; then
    echo "📦 Running inside Docker container"
    WEB_USER="www-data"
else
    echo "🖥️  Running on host system"
    WEB_USER="www-data"
fi

echo "🧹 Force removing problematic view cache files..."

# Method 1: Try to change ownership first
chown -R $WEB_USER:$WEB_USER storage/ 2>/dev/null || echo "⚠️  Could not change ownership (may need sudo)"

# Method 2: Force delete with different methods
rm -rf storage/framework/views/* 2>/dev/null || echo "⚠️  Method 1 failed, trying alternative..."

# Method 3: Use find with delete
find storage/framework/views/ -name "*.php" -delete 2>/dev/null || echo "⚠️  Method 2 failed, trying alternative..."

# Method 4: Nuclear option - recreate the directory
if [ "$(ls -A storage/framework/views/ 2>/dev/null)" ]; then
    echo "🚨 Nuclear option: recreating views directory..."
    rm -rf storage/framework/views/
    mkdir -p storage/framework/views/
fi

# Method 5: Use sudo if available
if command -v sudo >/dev/null 2>&1; then
    echo "🔑 Trying with sudo..."
    sudo rm -rf storage/framework/views/*
    sudo rm -rf storage/framework/cache/*
    sudo rm -rf bootstrap/cache/*.php
fi

echo "🔧 Setting proper permissions..."
chmod -R 777 storage/ bootstrap/cache/
chown -R $WEB_USER:$WEB_USER storage/ bootstrap/cache/ 2>/dev/null || echo "⚠️  Ownership change may need sudo"

echo "🧹 Clearing Laravel caches..."
php artisan config:clear || echo "⚠️  Config clear failed"
php artisan cache:clear || echo "⚠️  Cache clear failed"
php artisan view:clear || echo "⚠️  View clear failed"
php artisan route:clear || echo "⚠️  Route clear failed"

echo "📁 Creating fresh directories..."
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/logs
mkdir -p bootstrap/cache

echo "🔒 Final permission set..."
chmod -R 775 storage/ bootstrap/cache/
chown -R $WEB_USER:$WEB_USER storage/ bootstrap/cache/ 2>/dev/null || echo "⚠️  Final ownership change may need sudo"

echo "✅ Permission fix completed!"
echo ""
echo "🔍 Current permissions:"
ls -la storage/framework/views/ | head -5
echo ""
echo "📊 Directory status:"
ls -ld storage/ bootstrap/cache/