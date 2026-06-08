# Unraid Deployment Guide 🚀

## ✅ Latest Push Complete

**Commit:** `c720bdb` - Fixed deployment script for Unraid

---

## What Was Fixed

### Issues Resolved
1. ✅ **Database connection** - `.env` file now properly configured
2. ✅ **Scheduler container** - Runs automatically as separate container
3. ✅ **Error handling** - Better migration failure detection
4. ✅ **HTTP testing** - Checks if app responds after deployment
5. ✅ **Dependencies** - Added `procps` for scheduler process management

### New Features in Deployment
- **Dedicated scheduler container** - Runs `php artisan scheduler:run --daemon`
- **Automatic .env configuration** - Database credentials set via `sed`
- **Migration validation** - Stops deployment if migrations fail
- **Status reporting** - Shows container table at end
- **Unraid notifications** - Sends success/failure notifications

---

## How to Deploy on Unraid

### 1. SSH into Unraid
```bash
ssh root@your-unraid-ip
```

### 2. Run Your Deployment Script
```bash
bash /path/to/your/ktl-deploy-script.sh
```

The script will:
1. Clean up old containers
2. Pull latest code from GitHub
3. Run the updated `deploy-ktl-nginx.sh`
4. Set up 4 containers:
   - `ktl-booking-mysql` (Database)
   - `ktl-booking-app` (PHP-FPM)
   - `ktl-booking-nginx` (Web Server)
   - `ktl-booking-scheduler` (Task Scheduler) **← NEW!**

---

## Expected Output

### Success Looks Like This:
```
===== KTL Booking System - Nginx + PHP-FPM Setup =====
✅ Port 3306 is available
🧹 Cleaning up any existing setup...
📁 Updating existing repository...
✅ Repository ready!
🐳 Creating MySQL container on port 3306...
⏳ Waiting for MySQL to initialize...
✅ MySQL is ready!
⚙️ Setting up environment file...
✅ Environment configured with correct database credentials
🐳 Creating PHP-FPM container...
📦 Installing system dependencies...
📦 Installing PHP extensions...
🚀 Installing Laravel dependencies...
🚀 Running database migrations...
✅ Migrations completed successfully
🚀 Optimizing Laravel...
🐳 Creating Nginx container...
⏰ Creating Scheduler container...
✅ Application is responding (HTTP 200)

=============================================
🎉 SETUP COMPLETE!
=============================================
🌐 Application URL: http://192.168.1.X:8088
🔧 Admin Panel: http://192.168.1.X:8088/app/dashboard
⏰ Scheduler Panel: http://192.168.1.X:8088/admin/scheduler
🗄️  MySQL: 192.168.1.X:3306
=============================================

🐳 Container Status:
NAMES                      STATUS              PORTS
ktl-booking-nginx          Up 5 seconds       0.0.0.0:8088->80/tcp
ktl-booking-scheduler      Up 5 seconds       9000/tcp
ktl-booking-app            Up 2 minutes       9000/tcp
ktl-booking-mysql          Up 3 minutes       0.0.0.0:3306->3306/tcp

✅ Setup completed successfully with scheduler and performance optimizations!
```

---

## Troubleshooting

### If Deployment Fails

#### 1. Check Container Logs
```bash
# MySQL
docker logs ktl-booking-mysql

# PHP-FPM
docker logs ktl-booking-app

# Nginx
docker logs ktl-booking-nginx

# Scheduler
docker logs ktl-booking-scheduler
```

#### 2. Check Laravel Logs
```bash
docker exec ktl-booking-app cat /var/www/html/storage/logs/laravel.log | tail -100
```

#### 3. Test Database Connection
```bash
docker exec ktl-booking-app php artisan tinker --execute="DB::connection()->getPdo();"
```

Should output something like: `=> PDO {#...}`

#### 4. Check Scheduler Status
```bash
docker exec ktl-booking-scheduler ps aux | grep scheduler
```

Should show: `php artisan scheduler:run --daemon --interval=60`

### Common Issues

#### "Migration failed"
**Cause:** Database connection issue or migration error

**Fix:**
```bash
# Check if MySQL is running
docker ps | grep mysql

# Check database credentials in .env
docker exec ktl-booking-app cat /var/www/html/.env | grep DB_

# Try migration manually
docker exec ktl-booking-app php artisan migrate --force
```

#### "Application returned HTTP 500"
**Cause:** PHP error, missing dependencies, or permission issue

**Fix:**
```bash
# Check Laravel logs
docker logs ktl-booking-app --tail 100

# Check permissions
docker exec ktl-booking-app ls -la /var/www/html/storage

# Fix permissions if needed
docker exec ktl-booking-app chmod -R 775 /var/www/html/storage
docker exec ktl-booking-app chown -R www-data:www-data /var/www/html/storage
```

#### "Scheduler not running tasks"
**Cause:** Scheduler container might need restart or PHP dependencies missing

**Fix:**
```bash
# Check scheduler logs
docker logs ktl-booking-scheduler

# Restart scheduler
docker restart ktl-booking-scheduler

# Check if tasks are scheduled
docker exec ktl-booking-scheduler php artisan schedule:list
```

---

## After Successful Deployment

### 1. Access the Application
Visit: `http://your-unraid-ip:8088`

### 2. Check Scheduler
Visit: `http://your-unraid-ip:8088/admin/scheduler`

You should see:
- ✅ Green "Running" status
- 4 scheduled tasks listed
- Next run times for each task

### 3. Verify Containers
```bash
docker ps --filter "name=ktl-booking"
```

All 4 containers should be "Up":
- ktl-booking-mysql
- ktl-booking-app
- ktl-booking-nginx
- ktl-booking-scheduler

### 4. Test Bookings
1. Log in as a customer
2. Try to create a booking
3. Check that slots are available

---

## Container Details

### ktl-booking-mysql
- **Image:** mysql:8.0
- **Port:** 3306 (or 3307 if 3306 is in use)
- **Database:** ktl_booking
- **User:** ktl_user
- **Password:** ktl_password
- **Volume:** ktl-mysql-data

### ktl-booking-app
- **Image:** php:8.2-fpm
- **Purpose:** Runs PHP application
- **Extensions:** gd, zip, pdo_mysql, mbstring, xml, opcache
- **Volume:** /mnt/user/appdata/ktl-booking

### ktl-booking-nginx
- **Image:** nginx:alpine
- **Port:** 8088:80
- **Purpose:** Web server
- **Config:** Custom nginx.conf with performance tuning

### ktl-booking-scheduler **← NEW!**
- **Image:** php:8.2-fpm
- **Purpose:** Runs scheduled tasks
- **Command:** `php artisan scheduler:run --daemon --interval=60`
- **Tasks:** Runs every 60 seconds, executes due tasks
- **Volume:** Shares /var/www/html with app container

---

## Scheduled Tasks Running

Once deployed, these tasks run automatically:

| Task | Frequency | What It Does |
|------|-----------|--------------|
| Generate Slots | Daily 00:15 | Creates booking slots for next 30 days |
| Auto-Release Slots | Every 15 min | Releases slots based on rules |
| Bay Occupancy Sync | Every 30 min | Updates bay status |
| Cleanup Bookings | Every 15 min | Removes incomplete bookings |

---

## Updating After This Deployment

When you need to update in the future:

```bash
# Just run your deployment script again
bash /path/to/your/ktl-deploy-script.sh
```

It will:
1. Pull latest code
2. Rebuild containers
3. Run migrations
4. Restart everything

---

## Port Configuration

| Service | Default Port | Alternative |
|---------|--------------|-------------|
| Nginx | 8088 | Changeable in script |
| MySQL | 3306 | Auto-switches to 3307 if needed |

---

## Performance Features

✅ **OPcache** - Caches compiled PHP code
✅ **Gzip Compression** - Reduces transfer size
✅ **Static Asset Caching** - 1 year cache for images/CSS/JS
✅ **Connection Pooling** - Persistent database connections
✅ **Route Caching** - Faster route resolution
✅ **Config Caching** - Faster config access

---

## Security Notes

⚠️ **Change default passwords** in production:
- MySQL root password: `ktl123456`
- MySQL user password: `ktl_password`

Edit these in the deployment script before running in production.

---

## Next Steps

1. ✅ Deploy using your script
2. ✅ Verify all 4 containers are running
3. ✅ Access the application
4. ✅ Check scheduler at `/admin/scheduler`
5. ✅ Configure bay operating hours if needed
6. ✅ Test customer bookings

---

**Your Unraid deployment is ready! 🎉**

Run your deployment script now to get the latest fixes!
