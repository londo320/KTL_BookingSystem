#!/bin/bash

echo "Moving depot map files to correct location..."

# Create the correct directory
mkdir -p /var/www/html/storage/app/public/depot-maps

# Move files from old location to new location
if [ -d "/var/www/html/public/images/depot-maps" ]; then
    echo "Found files in old location, moving them..."
    cp -v /var/www/html/public/images/depot-maps/* /var/www/html/storage/app/public/depot-maps/ 2>/dev/null || true
fi

# Set correct permissions
chown -R www-data:www-data /var/www/html/storage/app/public/depot-maps
chmod -R 755 /var/www/html/storage/app/public/depot-maps

echo "Listing files in correct location:"
ls -lh /var/www/html/storage/app/public/depot-maps/

echo "Done! Map files should now be accessible."
