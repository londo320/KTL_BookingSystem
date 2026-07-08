# Laravel Permission Fix for Deployment

## Issues Fixed
1. ✅ **Depot Map File Path Issue**: Fixed incorrect file paths in Blade templates from `storage/depot-maps/` to `images/depot-maps/`
2. ✅ **Laravel Permission Issues**: Created fix script for common Laravel permission problems

## Quick Fix for Current Deployment

When you deploy from Git, run this script to fix permissions:

```bash
# Make the script executable
chmod +x fix-permissions.sh

# Run the permission fix script
./fix-permissions.sh

# If you're on a server with www-data user, also run:
sudo chown -R www-data:www-data storage/ bootstrap/cache/
```

## Manual Permission Fix (if script fails)

If the automated script doesn't work, run these commands manually:

```bash
# Remove problematic view cache
rm -rf storage/framework/views/*
rm -rf storage/framework/cache/*
rm -rf bootstrap/cache/*

# Set proper permissions
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# Clear Laravel caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Set web server ownership (adjust user as needed)
sudo chown -R www-data:www-data storage/ bootstrap/cache/
```

## Files Modified

### 1. Fixed Depot Map File Paths
- `resources/views/warehouse/depot-map/select-map-file.blade.php`
- `resources/views/admin/depot-map/select-map-file.blade.php`

**Change**: Updated file paths from `public/storage/depot-maps/` to `public/images/depot-maps/` where the files actually exist.

### 2. Created Permission Fix Script
- `fix-permissions.sh` - Automated Laravel permission fix script

## Root Cause

The Laravel view compilation system needs write permissions to `storage/framework/views/` directory. The web server user (typically `www-data` or `nginx`) must have write access to:

- `storage/` directory (all subdirectories)
- `bootstrap/cache/` directory

## For Future Deployments

Add this to your deployment script:

```bash
# After git pull
php artisan config:clear
php artisan cache:clear
php artisan view:clear
chmod -R 775 storage/ bootstrap/cache/
```

This will prevent similar permission issues in the future.