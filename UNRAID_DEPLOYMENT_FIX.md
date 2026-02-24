# 🚨 URGENT: Cron Jobs Not Running on Unraid

## Issue 1: Bookings Allowed for Past Times ❌

**Symptom:** You can create a booking for this morning at 08:00 even though cut-off has passed.

**Root Cause:** `slot->locked_at` is NULL because cron job `app:auto-release-slots` hasn't run to set it.

**Fix:** Set up cron jobs (see Issue 2 below)

---

## Issue 2: Cron Jobs Not Running ❌

**Laravel does NOT automatically run cron jobs!**

You need a system cron job that calls Laravel's scheduler every minute.

---

## 🔧 FIX: Run Setup Script on Unraid

### Step 1: SSH into Unraid

```bash
ssh root@your-unraid-ip
```

### Step 2: Navigate to Project Directory

```bash
cd /mnt/user/appdata/ktl-booking
```

### Step 3: Make Setup Script Executable

```bash
chmod +x setup-cron.sh
```

### Step 4: Run Setup Script

```bash
./setup-cron.sh
```

**Expected Output:**
```
╔════════════════════════════════════════════════════════╗
║   Laravel Scheduler (Cron) Setup Script               ║
╚════════════════════════════════════════════════════════╝

🐳 Docker environment detected
⏰ Setting up cron inside Docker container...
✅ Cron job added:
* * * * * cd /var/www/html && php artisan schedule:run >> /var/www/html/storage/logs/scheduler.log 2>&1

📋 Scheduled Tasks:
   • slots:generate        - Daily at 00:15
   • app:auto-release-slots - Every 15 minutes
   • bays:sync-occupancy   - Every 30 minutes
   • bookings:cleanup-incomplete - Every 15 minutes

🧪 Testing scheduler (running once)...
✅ Scheduler test completed

╔════════════════════════════════════════════════════════╗
║              CRON SETUP COMPLETE!                      ║
╚════════════════════════════════════════════════════════╝
```

---

## ✅ Verify Cron is Running

### Check Crontab

```bash
docker exec ktl-booking-app crontab -l
```

**Expected:**
```
* * * * * cd /var/www/html && php artisan schedule:run >> /var/www/html/storage/logs/scheduler.log 2>&1
```

### Check Cron Service Status

```bash
docker exec ktl-booking-app service cron status
```

**Expected:**
```
* cron is running
```

### Manually Run Scheduler Once

```bash
docker exec ktl-booking-app php artisan schedule:run
```

**Expected:**
```
Running scheduled command: Closure
Running scheduled command: Closure
Running scheduled command: Closure
Running scheduled command: Closure
```

### Check Logs

```bash
docker exec ktl-booking-app tail -20 /var/www/html/storage/logs/scheduler.log
```

---

## 🔍 What the Cron Jobs Do

### 1. `slots:generate` (Daily at 00:15)
- Generates slots for next 14 days
- Creates slots from templates
- Handles time gaps (lunch breaks)

### 2. `app:auto-release-slots` (Every 15 minutes) **← CRITICAL**
- Sets `locked_at` on slots (cut-off time)
- Sets `released_at` on slots (makes public)
- Removes customer restrictions when released
- **Without this, bookings have no cut-off enforcement!**

### 3. `bays:sync-occupancy` (Every 30 minutes)
- Updates bay occupancy status
- Syncs with active bookings

### 4. `bookings:cleanup-incomplete` (Every 15 minutes)
- Deletes incomplete bookings after 30 minutes
- Cleans up abandoned bookings

---

## 🧪 Test Cut-Off Time After Cron Setup

### Step 1: Wait 15 Minutes

After running `./setup-cron.sh`, wait 15 minutes for `app:auto-release-slots` to run.

### Step 2: Check Slot Lock Status

```bash
docker exec ktl-booking-app php artisan tinker
```

```php
$slot = Slot::where('start_at', '>=', now())->first();
echo 'Locked at: ' . ($slot->locked_at ? $slot->locked_at : 'NULL');
exit
```

**Should show:** `Locked at: 2026-02-23 16:00:00` (or similar)

### Step 3: Try to Book Past Cut-Off

Try booking a slot that's past its cut-off time.

**Expected:** Booking should be blocked with error message.

---

## 🚨 Alternative: Manual Run (Temporary Fix)

If you can't wait 15 minutes, manually run the release command:

```bash
docker exec ktl-booking-app php artisan app:auto-release-slots
```

This will:
- Set `locked_at` on all slots
- Set `released_at` on slots past release time
- Make slots public

---

## 📊 Verify Slots Updated

After running auto-release (manual or via cron):

```bash
docker exec ktl-booking-app php artisan tinker
```

```php
// Check released slots
$released = Slot::whereNotNull('released_at')->count();
$unreleased = Slot::whereNull('released_at')->count();
$locked = Slot::whereNotNull('locked_at')->count();

echo "Released (public): $released\n";
echo "Unreleased (priority): $unreleased\n";
echo "With lock time: $locked\n";
exit
```

---

## 🔧 Troubleshooting

### Problem: Cron not running

**Check cron service:**
```bash
docker exec ktl-booking-app service cron status
```

**Start cron if stopped:**
```bash
docker exec ktl-booking-app service cron start
```

### Problem: Scheduler not logging

**Check permissions:**
```bash
docker exec ktl-booking-app ls -la /var/www/html/storage/logs/
docker exec ktl-booking-app chown -R www-data:www-data /var/www/html/storage/logs/
```

### Problem: Crontab empty

**Re-add cron job:**
```bash
docker exec ktl-booking-app bash -c 'echo "* * * * * cd /var/www/html && php artisan schedule:run >> /var/www/html/storage/logs/scheduler.log 2>&1" | crontab -'
```

### Problem: Commands not running

**Check schedule list:**
```bash
docker exec ktl-booking-app php artisan schedule:list
```

**Manually test each command:**
```bash
docker exec ktl-booking-app php artisan app:auto-release-slots
docker exec ktl-booking-app php artisan bays:sync-occupancy
docker exec ktl-booking-app php artisan slots:generate
docker exec ktl-booking-app php artisan bookings:cleanup-incomplete
```

---

## ✅ After Setup Checklist

- [ ] Cron service running: `docker exec ktl-booking-app service cron status`
- [ ] Crontab configured: `docker exec ktl-booking-app crontab -l`
- [ ] Scheduler runs: `docker exec ktl-booking-app php artisan schedule:run`
- [ ] Logs being written: `docker exec ktl-booking-app tail -f /var/www/html/storage/logs/scheduler.log`
- [ ] Slots have `locked_at`: `Slot::whereNotNull('locked_at')->count()`
- [ ] Slots have `released_at`: `Slot::whereNotNull('released_at')->count()`
- [ ] Past bookings blocked: Try booking yesterday - should fail

---

## 📝 Summary

**Two Issues Found:**

1. **Bookings allowed for past times**
   - Cause: `locked_at` is NULL on slots
   - Fix: Run cron jobs to set lock times

2. **Cron jobs not running**
   - Cause: Laravel doesn't auto-run cron
   - Fix: Run `./setup-cron.sh` in Unraid

**Quick Fix:**
```bash
# SSH to Unraid
cd /mnt/user/appdata/ktl-booking
chmod +x setup-cron.sh
./setup-cron.sh

# Manually trigger first run
docker exec ktl-booking-app php artisan app:auto-release-slots

# Verify
docker exec ktl-booking-app php artisan schedule:list
```

After this, the system will:
- ✅ Set cut-off times on slots (bookings blocked after cut-off)
- ✅ Release priority slots at configured time
- ✅ Generate new slots daily
- ✅ Clean up incomplete bookings
- ✅ Sync bay occupancy

**Cron runs every minute inside the Docker container, checking if scheduled tasks are due.**
