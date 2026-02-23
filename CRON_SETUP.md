# Laravel Scheduler (Cron Jobs) Setup

## 🚀 Automatic Setup

The cron jobs are automatically configured during deployment. No manual intervention required!

### For New Deployments

Run the main deployment script:
```bash
./deploy-ktl-booking.sh
```

The script automatically:
- ✅ Installs cron inside the container
- ✅ Configures Laravel scheduler
- ✅ Sets up all scheduled tasks
- ✅ Starts the cron daemon

### For Existing Installations

Run the standalone cron setup script:
```bash
./setup-cron.sh
```

This script:
- 🔍 Auto-detects Docker or local environment
- ⚙️ Configures cron appropriately
- 📋 Shows all scheduled tasks
- ✅ Tests the scheduler

---

## 📊 Scheduled Tasks

All tasks are configured in `routes/console.php`:

| Task | Schedule | Description |
|------|----------|-------------|
| `slots:generate --days=14` | Daily at 00:15 | Generate slots for next 14 days from templates (configurable up to 30 days) |
| `app:auto-release-slots` | Every 15 minutes | Release slots to public based on rules |
| `bays:sync-occupancy` | Every 30 minutes | Sync bay occupancy status with active bookings |
| `bookings:cleanup-incomplete` | Every 15 minutes | Delete incomplete bookings after 30 minutes |

---

## 🧪 Testing

### Check Scheduled Tasks
```bash
# Docker
docker exec ktl-booking-app php artisan schedule:list

# Local
php artisan schedule:list
```

### Run Scheduler Manually
```bash
# Docker
docker exec ktl-booking-app php artisan schedule:run

# Local
php artisan schedule:run
```

### Test Individual Commands
```bash
# Auto-release slots
php artisan app:auto-release-slots

# Sync bay occupancy
php artisan bays:sync-occupancy

# Generate slots (default 14 days)
php artisan slots:generate

# Generate slots (custom days, e.g., 30 days)
php artisan slots:generate --days=30

# Cleanup incomplete bookings
php artisan bookings:cleanup-incomplete --minutes=30
```

---

## 📝 Logs

Scheduler logs are written to:
```
storage/logs/scheduler.log
```

Individual command logs:
```
storage/logs/auto_release_slots.log
storage/logs/bay_sync.log
storage/logs/slots_generate.log
storage/logs/booking_cleanup.log
```

---

## 🔧 Manual Cron Configuration

If you need to manually set up cron:

### Docker
```bash
# Install cron
docker exec ktl-booking-app apt-get install -y cron

# Add cron job
docker exec ktl-booking-app bash -c 'echo "* * * * * cd /var/www/html && php artisan schedule:run >> /var/www/html/storage/logs/scheduler.log 2>&1" | crontab -'

# Start cron
docker exec ktl-booking-app service cron start

# Verify
docker exec ktl-booking-app crontab -l
```

### Local/Server
```bash
# Edit crontab
crontab -e

# Add this line:
* * * * * cd /path/to/project && php artisan schedule:run >> /path/to/project/storage/logs/scheduler.log 2>&1
```

---

## ✅ Verification

After setup, verify cron is working:

1. **Check crontab:**
   ```bash
   # Docker
   docker exec ktl-booking-app crontab -l

   # Local
   crontab -l
   ```

2. **Monitor logs:**
   ```bash
   tail -f storage/logs/scheduler.log
   ```

3. **Check scheduled tasks:**
   ```bash
   php artisan schedule:list
   ```

4. **Verify cron service:**
   ```bash
   # Docker
   docker exec ktl-booking-app service cron status

   # Local
   service cron status  # or: systemctl status cron
   ```

---

## 🐛 Troubleshooting

### Scheduler Not Running

**Check cron service:**
```bash
docker exec ktl-booking-app service cron status
docker exec ktl-booking-app service cron start  # if stopped
```

**Verify crontab entry:**
```bash
docker exec ktl-booking-app crontab -l
```

### Tasks Not Executing

**Check logs:**
```bash
tail -50 storage/logs/scheduler.log
```

**Run manually to see errors:**
```bash
docker exec ktl-booking-app php artisan schedule:run -vvv
```

**Check task schedule:**
```bash
php artisan schedule:list
```

### Permission Issues

**Fix storage permissions:**
```bash
docker exec ktl-booking-app chown -R www-data:www-data storage/
docker exec ktl-booking-app chmod -R 775 storage/
```

---

## 🔄 Updating Scheduled Tasks

1. Edit `routes/console.php`
2. No need to update crontab - Laravel scheduler handles everything
3. Test: `php artisan schedule:list`

---

## 🎯 Production Checklist

- ✅ Cron daemon is installed and running
- ✅ Laravel scheduler crontab entry is configured
- ✅ Storage permissions allow writing logs
- ✅ Timezone is set correctly (`Europe/London`)
- ✅ Commands run without errors when tested manually
- ✅ Logs are being written to storage/logs/

---

## 📞 Support

If cron jobs are not running:
1. Check logs: `storage/logs/scheduler.log`
2. Run: `./setup-cron.sh` to reconfigure
3. Test manually: `php artisan schedule:run`
4. Verify: `php artisan schedule:list`
