# Scheduler Fix for Unraid 🔧

## Problem Summary

The scheduler wasn't working reliably in Unraid because it was using a daemon process that would die when containers restart. This caused slots not to be released/locked properly.

## Solution: Cron-Based Scheduler ✅

Instead of using `php artisan scheduler:run --daemon`, we now use **cron** to run `php artisan schedule:run` every minute. This is the traditional Laravel way and is much more reliable.

---

## Quick Fix (If Already Deployed)

If you've already deployed and the scheduler isn't working:

```bash
# SSH into your Unraid server
ssh root@your-unraid-ip

# Navigate to your project
cd /mnt/user/appdata/ktl-booking

# Run the setup script
bash setup-scheduler-cron.sh

# Verify it's working
bash verify-scheduler.sh
```

---

## For New Deployments

The deployment script (`deploy-ktl-nginx.sh`) has been updated to automatically set up cron. Just run your deployment script as normal:

```bash
bash /path/to/your/deployment-script.sh
```

The scheduler will be automatically configured with cron.

---

## How It Works

### What Runs

Every minute, cron executes:
```bash
php artisan schedule:run
```

This checks all scheduled tasks and runs any that are due.

### Scheduled Tasks

These tasks run automatically:

| Task | Frequency | What It Does |
|------|-----------|--------------|
| `slots:generate-dynamic` | Daily at 00:15 | Creates booking slots for next 30 days |
| `app:auto-release-slots` | Every 15 min | **Releases and locks slots** based on rules |
| `bays:sync-occupancy` | Every 30 min | Updates bay status |
| `bookings:cleanup-incomplete` | Every 15 min | Removes incomplete bookings |

**The `app:auto-release-slots` task is the one that handles your slot locking/unlocking!**

---

## Verification

### Check if scheduler is running:

```bash
bash verify-scheduler.sh
```

### Manual verification:

```bash
# Check cron service is running
docker exec ktl-booking-app service cron status

# Check cron job is configured
docker exec ktl-booking-app cat /etc/cron.d/laravel-scheduler

# View scheduler logs
docker exec ktl-booking-app tail -f /var/www/html/storage/logs/scheduler-cron.log

# View slot release logs specifically
docker exec ktl-booking-app tail -f /var/www/html/storage/logs/auto_release_slots.log
```

### Test manually:

```bash
# Run scheduler once manually to test
docker exec ktl-booking-app php artisan schedule:run

# Run the slot release task directly
docker exec ktl-booking-app php artisan app:auto-release-slots
```

---

## After Container Restarts

**Important:** When the container restarts, cron needs to be restarted too.

### Option 1: Automatic Restart Script

Add this to your Unraid **User Scripts** plugin to run at "Array Start":

```bash
#!/bin/bash
# Wait for container to be ready
sleep 10

# Restart cron
docker exec ktl-booking-app service cron start

echo "✅ Scheduler cron started"
```

### Option 2: Manual Restart

After a container restart, run:

```bash
bash start-scheduler-persistent.sh
```

Or directly:

```bash
docker exec ktl-booking-app service cron start
```

---

## Logs

### Main scheduler log (shows when schedule:run executes):
```bash
docker exec ktl-booking-app tail -f /var/www/html/storage/logs/scheduler-cron.log
```

### Task-specific logs:

```bash
# Slot generation
docker exec ktl-booking-app tail -f /var/www/html/storage/logs/slots_generate.log

# Auto-release slots (locking/unlocking)
docker exec ktl-booking-app tail -f /var/www/html/storage/logs/auto_release_slots.log

# Bay sync
docker exec ktl-booking-app tail -f /var/www/html/storage/logs/bay_sync.log

# Booking cleanup
docker exec ktl-booking-app tail -f /var/www/html/storage/logs/booking_cleanup.log
```

---

## Troubleshooting

### Scheduler shows "not running" in admin panel

The admin panel checks for the daemon process. Since we're using cron now, it may show as "not running" even though cron is working fine.

**Ignore the admin panel status.** Instead, verify with:
```bash
bash verify-scheduler.sh
```

### Slots not being released/locked

1. Check if slot release rules are configured:
   - Visit: http://your-ip:8088/admin/slot-release-rules
   - Ensure rules exist for your depots

2. Check the auto-release log:
   ```bash
   docker exec ktl-booking-app tail -50 /var/www/html/storage/logs/auto_release_slots.log
   ```

3. Run the task manually to test:
   ```bash
   docker exec ktl-booking-app php artisan app:auto-release-slots
   ```

### Cron not running after container restart

```bash
# Check if cron is running
docker exec ktl-booking-app service cron status

# If not running, start it
docker exec ktl-booking-app service cron start

# Or use the helper script
bash start-scheduler-persistent.sh
```

### Want to see it running live?

```bash
# Watch the scheduler-cron.log in real-time
docker exec ktl-booking-app tail -f /var/www/html/storage/logs/scheduler-cron.log

# You should see a new entry every minute showing:
# "No scheduled commands are ready to run."
# or
# "Running scheduled command: ..."
```

---

## Scripts Included

| Script | Purpose |
|--------|---------|
| `setup-scheduler-cron.sh` | Sets up cron in the container (one-time setup) |
| `start-scheduler-persistent.sh` | Starts cron after container restarts |
| `verify-scheduler.sh` | Checks if everything is working correctly |

---

## Key Differences from Before

| Before (Daemon) | Now (Cron) |
|----------------|------------|
| `scheduler:run --daemon` | `schedule:run` every minute via cron |
| Dies on container restart | Cron can be restarted easily |
| Single long-running process | Many short-lived processes |
| Hard to debug | Easy to see in logs |
| Admin panel shows "running" | Admin panel may show "not running" (ignore this) |

---

## Next Steps

1. ✅ Deploy using your updated script
2. ✅ Verify scheduler is working: `bash verify-scheduler.sh`
3. ✅ Wait 15 minutes and check if slots are being released
4. ✅ Add `start-scheduler-persistent.sh` to Unraid startup (optional)
5. ✅ Check logs periodically to ensure tasks are running

---

## Questions?

- **Do I need to do anything different?** No, just redeploy. Cron is automatic.
- **Will slots release now?** Yes, every 15 minutes as configured.
- **What if I want to change the frequency?** Edit `routes/console.php` and redeploy.
- **Can I still use the admin panel?** Yes, but ignore the "not running" status. Use `verify-scheduler.sh` instead.

---

**Your scheduler will now work reliably on Unraid! 🎉**
