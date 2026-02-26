# Cron Persistence in Docker Containers

## The Problem 🐛

**Cron does NOT auto-start when Docker containers restart!**

### What Happens:

```bash
# Initial deployment
./ktl-deploy.sh
# ✅ Cron installed and running

# Container restart (server reboot, Docker restart, etc.)
docker restart ktl-booking-app
# ❌ Cron service NOT running
# ❌ Scheduled tasks stop working
```

---

## Why This Happens

The `php:8.2-apache` Docker image:
- ✅ Auto-starts Apache on boot (built-in)
- ❌ Does NOT auto-start cron (not included)

When container restarts:
- Apache starts automatically
- Cron stays stopped
- Your scheduled tasks (slot generation, auto-release, etc.) don't run

---

## How to Check if Cron is Running

```bash
docker exec ktl-booking-app service cron status
```

**Expected:**
```
* cron is running
```

**If stopped:**
```
* cron is not running
```

---

## Solutions

### Option 1: Manual Restart (Quick Fix)

**After each container restart, run:**

```bash
docker exec ktl-booking-app service cron start
```

**Or use the helper script:**

```bash
cd /mnt/user/appdata/ktl-booking
./start-cron-persistent.sh
```

---

### Option 2: Automated Startup Script (Recommended)

**On Unraid, create a user script that runs on array start:**

1. Go to **Settings → User Scripts**
2. Click **Add New Script**
3. Name it: `Start KTL Booking Cron`
4. Set schedule: **At Startup of Array**
5. Add this script:

```bash
#!/bin/bash
# Wait for Docker to be ready
sleep 30

# Start cron in KTL Booking container
docker exec ktl-booking-app service cron start

# Log result
if docker exec ktl-booking-app service cron status | grep -q "running"; then
    echo "✅ KTL Booking cron started successfully"
else
    echo "❌ KTL Booking cron failed to start"
    exit 1
fi
```

6. Click **Save** and **Run in Background**

**Now cron will auto-start every time Unraid boots!**

---

### Option 3: Custom Dockerfile (Advanced)

Create a proper Docker image with cron pre-installed and auto-starting.

This is overkill for now, but here's the concept:

```dockerfile
FROM php:8.2-apache

# Install cron
RUN apt-get update && apt-get install -y cron

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
```

---

## Testing Cron After Restart

### 1. Check Cron Status
```bash
docker exec ktl-booking-app service cron status
```

### 2. Check Scheduler Log
```bash
docker exec ktl-booking-app tail -20 /var/www/html/storage/logs/scheduler.log
```

**Should show entries every minute with recent timestamps.**

### 3. Check Scheduled Tasks
```bash
docker exec ktl-booking-app php artisan schedule:list
```

### 4. Manually Trigger Scheduler
```bash
docker exec ktl-booking-app php artisan schedule:run
```

---

## Symptoms of Cron Not Running

❌ **Slots not generating nightly** (00:15 generation missing)
❌ **Cut-off times not set** (`locked_at` is NULL on slots)
❌ **Bays not syncing** (occupancy status outdated)
❌ **Incomplete bookings not cleaned up** (old bookings remain)
❌ **Empty scheduler.log** or no recent timestamps

---

## Quick Diagnostic Commands

```bash
# 1. Is cron running?
docker exec ktl-booking-app service cron status

# 2. Is crontab configured?
docker exec ktl-booking-app crontab -l

# 3. Last scheduler run?
docker exec ktl-booking-app tail -1 /var/www/html/storage/logs/scheduler.log

# 4. How many slots have cut-off times set?
docker exec ktl-booking-app php artisan tinker --execute="echo Slot::whereNotNull('locked_at')->count()"
```

---

## Recommended Setup for Production

**For Unraid deployment:**

1. ✅ Deploy with `./ktl-deploy.sh` (installs cron initially)
2. ✅ Create Unraid User Script (auto-starts cron on boot)
3. ✅ Test by restarting container: `docker restart ktl-booking-app`
4. ✅ Verify cron started: `./start-cron-persistent.sh` or check logs

---

## Helper Script: `start-cron-persistent.sh`

**What it does:**
- Checks if cron is installed (installs if missing)
- Configures crontab
- Starts cron service
- Verifies it's running

**When to use:**
- After container restart
- After server reboot
- After Docker service restart
- When scheduled tasks stop working

**How to use:**
```bash
cd /mnt/user/appdata/ktl-booking
./start-cron-persistent.sh
```

---

## Summary

**The Issue:**
- Cron doesn't auto-start when Docker container restarts
- Causes scheduled tasks to stop running

**The Fix:**
1. **Immediate:** Run `./start-cron-persistent.sh` after each restart
2. **Permanent:** Create Unraid User Script to auto-start cron on boot

**Verification:**
```bash
# Check cron is running
docker exec ktl-booking-app service cron status

# Check scheduler is working (wait 2 minutes, then check)
docker exec ktl-booking-app tail -10 /var/www/html/storage/logs/scheduler.log
```

---

## Next Steps

1. **Right now:** Run `./start-cron-persistent.sh` to ensure cron is running
2. **For future:** Create Unraid User Script (see Option 2 above)
3. **Test it:** Restart container and verify cron auto-starts

---

## Need Help?

**If cron won't stay running:**
- Check Docker logs: `docker logs ktl-booking-app`
- Check if container is restarting: `docker ps -a | grep ktl-booking-app`
- Verify cron installed: `docker exec ktl-booking-app which cron`

**If scheduled tasks aren't running even with cron active:**
- See `CRON_TESTING_GUIDE.md` for detailed troubleshooting
