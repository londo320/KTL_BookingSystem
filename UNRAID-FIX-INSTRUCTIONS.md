# 🚨 Quick Fix for Unraid Deployment

## Current Issue
- `ktl-booking-app` container not running
- `ktl-booking-nginx` exited with error
- MySQL is running but app can't connect

## Fix Steps

### 1. SSH into Unraid
```bash
ssh root@your-unraid-ip
```

### 2. Navigate to Project Directory
```bash
cd /mnt/user/appdata/ktl-booking
```

### 3. Pull Latest Fixes
```bash
git pull origin main
```

### 4. Run the Fix Script
```bash
bash FIX-DEPLOYMENT-V2.sh
```

## What This Script Does

1. ✅ **Preserves MySQL** - Keeps your database running, doesn't touch it
2. ✅ **Removes failed containers** - Cleans up app, nginx, scheduler
3. ✅ **Installs all dependencies** - PHP extensions, system packages
4. ✅ **Tests database connection** - Verifies before proceeding
5. ✅ **Recreates all containers** - Fresh PHP-FPM, Nginx, Scheduler
6. ✅ **Tests HTTP response** - Confirms app is working

## Expected Success Output

```
✅ MySQL is already running
✅ MySQL is responding
✅ Database connection successful
✅ Laravel is working
✅ Application is responding!

🎉 FIX COMPLETE!
🌐 Application: http://192.168.1.X:8088
```

## After Running

Check container status:
```bash
docker ps --filter "name=ktl-booking"
```

All 4 containers should show "Up":
- ktl-booking-mysql
- ktl-booking-app
- ktl-booking-nginx
- ktl-booking-scheduler

## If Getting HTTP 500 Error

If containers are running but you get a 500 error, run:
```bash
bash FIX-500-ERROR.sh
```

This will:
- Generate APP_KEY
- Clear all caches
- Fix permissions
- Run migrations if needed
- Optimize Laravel
- Restart PHP-FPM

## If Still Having Issues

Run the debug script:
```bash
bash CHECK-LARAVEL-LOGS.sh
```

This will show detailed Laravel error logs and configuration.

## Quick Verification

Test the application:
```bash
curl -I http://localhost:8088
```

Should return: `HTTP/1.1 200 OK` or `HTTP/1.1 302 Found`

---

**Ready to fix!** Run the steps above and the deployment should work. ✅
