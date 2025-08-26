#!/bin/bash

# Setup script for depot map files
echo "Setting up depot map storage..."

# Create storage directories
mkdir -p storage/app/public/depot-maps

# Copy default map files if they exist in public/images
if [ -d "public/images/depot-maps" ]; then
    echo "Copying depot map files from public/images/depot-maps to storage..."
    cp public/images/depot-maps/* storage/app/public/depot-maps/ 2>/dev/null || true
fi

# Ensure storage link exists
php artisan storage:link

# Set proper permissions
chmod -R 755 storage/app/public/depot-maps

echo "Depot map setup complete!"
echo "Map files available in storage/app/public/depot-maps/"
ls -la storage/app/public/depot-maps/ 2>/dev/null || echo "No map files found"