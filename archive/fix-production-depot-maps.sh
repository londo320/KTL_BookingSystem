#!/bin/bash

echo "🔧 Fixing production depot map issues..."

# 1. Ensure storage directories exist with correct permissions
echo "Creating storage directories..."
mkdir -p storage/app/public/depot-maps
chmod -R 755 storage/app/public/
chmod -R 755 storage/framework/
chmod -R 755 storage/logs/

# 2. Ensure storage symlink is correct
echo "Setting up storage symlink..."
php artisan storage:link

# 3. Copy any existing map files from old location to new location
if [ -d "public/images/depot-maps" ]; then
    echo "Copying map files from old location..."
    cp public/images/depot-maps/* storage/app/public/depot-maps/ 2>/dev/null || true
    echo "Map files copied successfully"
fi

# 4. Ensure proper file permissions for uploaded files
chmod -R 644 storage/app/public/depot-maps/* 2>/dev/null || true

# 5. Clear caches to ensure changes take effect
echo "Clearing Laravel caches..."
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo "✅ Production depot map fixes complete!"
echo "Available map files:"
ls -la storage/app/public/depot-maps/ 2>/dev/null || echo "No map files found"