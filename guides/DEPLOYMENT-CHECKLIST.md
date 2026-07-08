# Deployment Checklist 🚀

## ✅ Code Pushed Successfully

**Commit:** `d18b89f` - Add comprehensive scheduler management system with web controls
**Branch:** `main`
**Remote:** `github.com:londo320/KTL_BookingSystem.git`

---

## What Was Deployed

### 🆕 New Features
1. **Scheduler Admin Panel** - `/admin/scheduler`
   - Start/stop/restart daemon from browser
   - View all scheduled tasks
   - Manual task execution
   - Real-time log viewer
   - Auto-refreshing status

2. **Scheduler Management System**
   - Daemon-based continuous scheduler
   - 4 automated tasks running
   - Cross-platform support
   - Health monitoring

### 🐛 Bug Fixes
1. **Booking API Error** - Fixed undefined `$allowedBayIds` variable
2. **PHP Path with Spaces** - Fixed Herd compatibility
3. **Tasks Not Showing** - Fixed Console Kernel boot issue

### 📋 Scheduled Tasks (Running)
1. Generate slots - Daily at 00:15
2. Auto-release slots - Every 15 minutes
3. Bay occupancy sync - Every 30 minutes
4. Cleanup bookings - Every 15 minutes

---

## 🔧 Post-Deployment Steps

### On Your Production Server

#### 1. Pull the Latest Code
```bash
cd /path/to/your/app
git pull origin main
```

#### 2. Install Dependencies (if needed)
```bash
composer install --no-dev --optimize-autoloader
```

#### 3. Clear Caches
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

#### 4. Run Migrations (if any)
```bash
php artisan migrate --force
```

#### 5. Start the Scheduler

**Option A: Using systemd (Recommended for Production)**
```bash
# Copy service file
sudo cp scheduler.service /etc/systemd/system/laravel-scheduler.service

# Edit paths in the service file
sudo nano /etc/systemd/system/laravel-scheduler.service
# Update: User, Group, WorkingDirectory, ExecStart paths

# Enable and start
sudo systemctl daemon-reload
sudo systemctl enable laravel-scheduler
sudo systemctl start laravel-scheduler
sudo systemctl status laravel-scheduler
```

**Option B: Using Bash Script**
```bash
chmod +x start-scheduler.sh stop-scheduler.sh status-scheduler.sh
bash start-scheduler.sh
```

**Option C: Railway/Heroku**
Add to `Procfile`:
```
web: php artisan serve --host=0.0.0.0 --port=$PORT
scheduler: php artisan scheduler:run --daemon --interval=60
```

#### 6. Configure BAY Operating Hours

For any bays that need slots generated:
```sql
UPDATE tipping_bays
SET
    operational_start = '08:00:00',
    operational_end = '16:00:00',
    is_24_hour = 0,
    is_active = 1
WHERE operational_start IS NULL;
```

#### 7. Generate Initial Slots
```bash
php artisan slots:generate-by-bay --days=30
```

---

## ✅ Verification Steps

### 1. Check Scheduler is Running

**Via Admin Panel:**
- Go to: `/admin/scheduler`
- Should see green dot: "Running (PID: xxxxx)"
- All 4 tasks should be listed

**Via Command Line:**
```bash
bash status-scheduler.sh
# Should show: Status: RUNNING
```

### 2. Test Features

**Admin Panel:**
- [ ] Can access `/admin/scheduler`
- [ ] See 4 scheduled tasks
- [ ] See green "Running" status
- [ ] Can click "Run Now" on a task
- [ ] Can view logs
- [ ] Can stop/restart daemon

**Customer Bookings:**
- [ ] Test Customer can create bookings
- [ ] Zertus can create bookings (BAY 8)
- [ ] Slots appear in availability picker

### 3. Monitor Logs

```bash
# Scheduler log
tail -f storage/logs/scheduler.log

# Slot generation log
tail -f storage/logs/slots_generate.log

# Laravel log
tail -f storage/logs/laravel.log
```

### 4. Check Scheduled Tasks Execute

Wait 15 minutes and check:
```bash
tail -f storage/logs/auto_release_slots.log
tail -f storage/logs/booking_cleanup.log
```

---

## 🆘 Troubleshooting

### Scheduler Not Starting

**Check permissions:**
```bash
chmod -R 775 storage
chown -R www-data:www-data storage  # Or your web user
```

**Check PHP path:**
```bash
which php
# Update in start-scheduler.sh if needed
```

### Tasks Not Running

**Check schedule list:**
```bash
php artisan schedule:list
```

**Run manually:**
```bash
php artisan schedule:run
```

### Can't Access Admin Panel

**Clear caches:**
```bash
php artisan route:clear
php artisan config:clear
php artisan view:clear
```

**Check routes:**
```bash
php artisan route:list | grep scheduler
```

### Customers Can't Book

**Check bay configuration:**
```sql
SELECT id, name, operational_start, operational_end
FROM tipping_bays
WHERE is_active = 1;
```

**Generate slots:**
```bash
php artisan slots:generate-by-bay --days=30
```

---

## 📚 Documentation

All documentation is included in the repository:

| File | Purpose |
|------|---------|
| `README-SCHEDULER.md` | Quick overview |
| `SCHEDULER-COMPLETE-GUIDE.md` | Complete guide |
| `SCHEDULER-SETUP.md` | Setup for all environments |
| `HOW-TO-ACCESS-SCHEDULER.md` | Access instructions |
| `SCHEDULER-WEB-CONTROLS.md` | Web controls guide |
| `SCHEDULER-ADMIN-PANEL.md` | Admin panel features |
| `FIXES-APPLIED.md` | Bug fixes summary |
| `ZERTUS-BOOKING-FIX.md` | Bay configuration guide |

---

## 🎯 Key URLs

| Feature | URL |
|---------|-----|
| Scheduler Admin | `/admin/scheduler` |
| Tipping Bays | `/app/tipping-bays` |
| Bookings | `/app/bookings` |
| Settings | `/app/settings/dashboard` |

---

## 📞 Support

If you encounter issues:

1. **Check logs** in `storage/logs/`
2. **Check scheduler status** at `/admin/scheduler`
3. **Run health check**: `bash scheduler-health-check.sh`
4. **Review documentation** in the repo

---

## ✨ What's New for Users

### For Admins
- **New menu item**: Configuration → ⏰ Scheduler
- **Full control**: Start/stop scheduler from browser
- **Monitoring**: See all tasks and their status
- **Manual execution**: Run any task on-demand
- **Log viewing**: Check task outputs instantly

### For Customers
- **Better availability**: Slots auto-generate daily
- **Bay assignments**: Customers see only their assigned bay slots
- **More slots**: Bay-specific generation creates more booking options

---

## 🎉 Success Criteria

Deployment is successful when:

- ✅ Scheduler daemon is running (green status in admin panel)
- ✅ All 4 tasks are listed and showing next run times
- ✅ Test Customer can create bookings
- ✅ Zertus (or any assigned customer) can create bookings
- ✅ Admin panel loads at `/admin/scheduler`
- ✅ Manual task execution works ("Run Now" buttons)
- ✅ Logs are viewable in the admin panel

---

**You're ready to deploy! 🚀**

After deployment, just visit `/admin/scheduler` and click **▶️ Start Daemon** to get everything running!
