# How to Test Cron Jobs Are Working

## 🧪 Quick Tests (In the App)

### Test 1: Check Scheduler Log File ✅

**SSH into Unraid:**
```bash
ssh root@your-unraid-ip
```

**View scheduler log:**
```bash
docker exec ktl-booking-app tail -20 /var/www/html/storage/logs/scheduler.log
```

**What to look for:**
- Should see entries every minute
- Timestamps should be recent
- Should show "Running scheduled command: Closure" or similar

**Example Good Output:**
```
[2026-02-24 15:01:00] Running scheduled command: Closure
[2026-02-24 15:02:00] Running scheduled command: Closure
[2026-02-24 15:03:00] Running scheduled command: Closure
```

---

### Test 2: Check Slot locked_at Times ✅

**In app (via Tinker):**
```bash
docker exec ktl-booking-app php artisan tinker
```

```php
// Check if slots have locked_at set
$slotsWithLock = Slot::whereNotNull('locked_at')->count();
$totalSlots = Slot::where('start_at', '>=', now())->count();

echo "Slots with cut-off time: $slotsWithLock / $totalSlots\n";

// Show example
$slot = Slot::whereNotNull('locked_at')->first();
if ($slot) {
    echo "Example slot:\n";
    echo "  Start: " . $slot->start_at . "\n";
    echo "  Locked at: " . $slot->locked_at . "\n";
}

exit
```

**Expected:**
- Most/all future slots should have `locked_at` set
- If 0 slots have locked_at, cron hasn't run yet

---

### Test 3: Try Booking a Past Slot ✅

**In the app (Booking page):**

1. Try to create a booking for this morning (before current time)
2. **Expected:** Should be blocked with error message
3. **Error message:** "Cannot book a slot in the past" or "Booking cut-off time has passed"

**If it allows the booking:** Cron hasn't run to set cut-off times yet.

---

### Test 4: Check Crontab ✅

```bash
docker exec ktl-booking-app crontab -l
```

**Expected output:**
```
* * * * * cd /var/www/html && php artisan schedule:run >> /var/www/html/storage/logs/scheduler.log 2>&1
```

**If empty:** Cron job not configured.

---

### Test 5: Check Cron Service Status ✅

```bash
docker exec ktl-booking-app service cron status
```

**Expected output:**
```
* cron is running
```

**If not running:**
```bash
docker exec ktl-booking-app service cron start
```

---

### Test 6: View Scheduled Tasks List ✅

```bash
docker exec ktl-booking-app php artisan schedule:list
```

**Expected output:**
```
0 15 0 * * *  slots:generate --days=14  ................... Next Due: 10 hours from now
*/15 * * * *  app:auto-release-slots ...................... Next Due: 3 minutes from now
*/30 * * * *  bays:sync-occupancy ......................... Next Due: 12 minutes from now
*/15 * * * *  bookings:cleanup-incomplete ................. Next Due: 8 minutes from now
```

**If shows:** Scheduler is configured correctly.

---

### Test 7: Manually Trigger Scheduler ✅

**Force run all scheduled tasks:**
```bash
docker exec ktl-booking-app php artisan schedule:run
```

**Expected output:**
```
Running scheduled command: Closure
Running scheduled command: Closure
```

**This runs tasks that are due NOW.**

---

### Test 8: Check Individual Task Logs ✅

Each scheduled task writes to its own log:

```bash
# Slot release log
docker exec ktl-booking-app tail -20 /var/www/html/storage/logs/auto_release_slots.log

# Bay sync log
docker exec ktl-booking-app tail -20 /var/www/html/storage/logs/bay_sync.log

# Slot generation log
docker exec ktl-booking-app tail -20 /var/www/html/storage/logs/slots_generate.log

# Booking cleanup log
docker exec ktl-booking-app tail -20 /var/www/html/storage/logs/booking_cleanup.log
```

---

## 🎯 Quick Verification Checklist

After deployment, run these commands:

```bash
# 1. Check cron service
docker exec ktl-booking-app service cron status
# ✅ Should say "cron is running"

# 2. Check crontab
docker exec ktl-booking-app crontab -l
# ✅ Should show 1 line with schedule:run

# 3. Check scheduler log (wait 2-3 minutes after deployment)
docker exec ktl-booking-app tail -10 /var/www/html/storage/logs/scheduler.log
# ✅ Should show recent timestamps

# 4. Check slots have lock times
docker exec ktl-booking-app php artisan tinker --execute="echo Slot::whereNotNull('locked_at')->count() . ' slots with cut-off times'"
# ✅ Should show a number > 0

# 5. List scheduled tasks
docker exec ktl-booking-app php artisan schedule:list
# ✅ Should show 4 tasks
```

---

## 🚨 Troubleshooting

### Problem: No entries in scheduler.log

**Check if cron is running:**
```bash
docker exec ktl-booking-app service cron status
```

**Restart cron if needed:**
```bash
docker exec ktl-booking-app service cron restart
```

**Check crontab exists:**
```bash
docker exec ktl-booking-app crontab -l
```

---

### Problem: Slots don't have locked_at

**Manually run the release command:**
```bash
docker exec ktl-booking-app php artisan app:auto-release-slots
```

**Check output for errors.**

**Then verify:**
```bash
docker exec ktl-booking-app php artisan tinker --execute="echo 'Locked: ' . Slot::whereNotNull('locked_at')->count()"
```

---

### Problem: Can still book past times

**Check if validation is active:**
```bash
docker exec ktl-booking-app php artisan tinker
```

```php
$slot = Slot::where('start_at', '<', now())->first();
if ($slot) {
    echo "Slot start: " . $slot->start_at . "\n";
    echo "Is past: " . ($slot->start_at->isPast() ? 'YES' : 'NO') . "\n";
    echo "Locked at: " . ($slot->locked_at ?? 'NULL') . "\n";
}
exit
```

**If locked_at is NULL:** Run `php artisan app:auto-release-slots`

---

### Problem: Scheduler not running tasks

**Check schedule:list:**
```bash
docker exec ktl-booking-app php artisan schedule:list
```

**Manually run each task:**
```bash
docker exec ktl-booking-app php artisan slots:generate
docker exec ktl-booking-app php artisan app:auto-release-slots
docker exec ktl-booking-app php artisan bays:sync-occupancy
docker exec ktl-booking-app php artisan bookings:cleanup-incomplete
```

---

## ✅ Success Indicators

When cron is working correctly, you should see:

1. **Scheduler log updates every minute**
   - File: `storage/logs/scheduler.log`
   - Timestamps are recent

2. **Slots have cut-off times**
   - `Slot::whereNotNull('locked_at')->count()` > 0
   - `locked_at` is before slot start time

3. **Past bookings are blocked**
   - Try booking yesterday → Error
   - Try booking this morning → Error (if after cut-off)

4. **Priority slots are released**
   - `Slot::whereNotNull('released_at')->count()` > 0
   - Slots with `released_at` are public

5. **New slots generated daily**
   - Check slots exist for 14 days ahead
   - Run `Slot::where('start_at', '>=', now())->max('start_at')`
   - Should be ~14 days from now

---

## 📊 Monitoring Commands

**Watch scheduler log in real-time:**
```bash
docker exec ktl-booking-app tail -f /var/www/html/storage/logs/scheduler.log
```

**Count slots by status:**
```bash
docker exec ktl-booking-app php artisan tinker --execute="
echo 'Total future slots: ' . Slot::where('start_at', '>=', now())->count() . PHP_EOL;
echo 'With cut-off time: ' . Slot::whereNotNull('locked_at')->count() . PHP_EOL;
echo 'Released (public): ' . Slot::whereNotNull('released_at')->count() . PHP_EOL;
echo 'Unreleased (priority): ' . Slot::whereNull('released_at')->count() . PHP_EOL;
"
```

**Check next run times:**
```bash
docker exec ktl-booking-app php artisan schedule:list
```

---

## 🎯 Expected Cron Behavior

| Task | Frequency | What It Does |
|------|-----------|--------------|
| **slots:generate** | Daily at 00:15 | Creates slots for next 14 days from templates |
| **app:auto-release-slots** | Every 15 min | Sets `locked_at` and `released_at` on slots |
| **bays:sync-occupancy** | Every 30 min | Updates bay occupancy from active bookings |
| **bookings:cleanup-incomplete** | Every 15 min | Deletes incomplete bookings after 30 mins |

---

## 🔍 Simple Visual Test (In App)

**After 15 minutes of deployment:**

1. Go to booking page
2. Try to book a slot for yesterday
3. **Should see error:** "Cannot book a slot in the past"
4. Try to book a slot for this morning (before now)
5. **Should see error:** "Booking cut-off time has passed for this slot"

**If you can book past slots → Cron not working yet!**

---

## ⏰ Time to Wait After Deployment

- **Immediate:** Cron service should be running
- **1 minute:** First scheduler.log entry
- **15 minutes:** First auto-release-slots run (sets cut-off times)
- **30 minutes:** First bay sync
- **24 hours (00:15):** First slot generation

**Quick test:** Wait 2-3 minutes, check scheduler.log for entries.
