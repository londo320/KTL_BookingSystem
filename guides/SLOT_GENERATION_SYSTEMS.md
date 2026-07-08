# Slot Generation Systems - Explained

You have **TWO different systems** for generating slots. Here's what each does:

---

## System 1: Template-Based Slots (Depot-Wide) 📅

**What it does:**
- Creates slots at **depot level** (not tied to specific bays)
- Uses `Slot Templates` configured in admin panel
- Runs automatically via **cron job** every night

**How it works:**
1. Admin creates slot templates (Admin → Slot Templates)
2. Each template specifies:
   - Depot
   - Booking Type
   - Day of week
   - Start/end time
3. Cron job runs daily at **00:15** and creates slots for next 14 days
4. Command: `php artisan slots:generate --days=14`

**Example:**
```
Template: "Handball - Monday 08:00-17:00"
Result: Creates slots every Monday 08:00-17:00 for next 14 days
```

**Pros:**
- ✅ Automatic - runs every night
- ✅ Simple - one template per booking type/day
- ✅ Good for booking-type-based scheduling

**Cons:**
- ❌ Not bay-specific (depot-wide slots)
- ❌ Less granular control

---

## System 2: Bay-Based Slots (Hourly Per Bay) 🚪

**What it does:**
- Creates **hourly slots for each bay**
- Uses bay operational hours
- Triggered **manually** via web interface

**How it works:**
1. Admin configures bay operational hours (Admin → Tipping Bays → Edit)
2. Each bay can have:
   - 24-hour operation OR
   - Specific hours (e.g., 08:00-17:00)
   - Specific days (e.g., Monday-Friday only)
3. Admin manually triggers generation (Admin → Bay Slot Generation)
4. Creates hourly slots per bay (e.g., Bay 1: 08:00-09:00, 09:00-10:00, etc.)
5. Command: `php artisan slots:generate-bay --depot=X --days=14`

**Example:**
```
Bay 1: Mon-Fri 08:00-17:00
Result: Creates 9 hourly slots per day (08:00-09:00, 09:00-10:00... 16:00-17:00) for Mon-Fri
```

**Pros:**
- ✅ Bay-specific (each bay has own slots)
- ✅ Hourly granularity
- ✅ Respects bay operational hours

**Cons:**
- ❌ Manual trigger (not automated)
- ❌ Can create LOTS of slots (bays × hours × days)

---

## The Problem You Had 🐛

**Issue:** When you clicked "Generate Bay Slots" via web interface, it tried to create thousands of slots **synchronously** in a single HTTP request.

**Example:**
- 10 bays × 24 hours × 14 days = **3,360 database inserts**
- This takes 30+ seconds = browser timeout/hang/crash

**Solution:**
- Now runs as a **background job** ✅
- Web interface returns immediately
- Slots are created in the background (takes a few minutes)
- You'll see a message: "Bay slot generation started! Check back in a few minutes."

---

## Which System Should You Use? 🤔

### Use System 1 (Template-Based) if:
- You want **automatic daily slot generation**
- You have booking types with fixed schedules (e.g., "Handball Mon-Fri 08:00-17:00")
- You don't need bay-specific control
- **Recommended for most use cases**

### Use System 2 (Bay-Based) if:
- You need **bay-specific operational hours** (e.g., Bay 1 open 24/7, Bay 2 only Mon-Fri)
- You want **hourly granularity** per bay
- You're okay with **manual triggering**
- You have varying bay schedules

### Use BOTH if:
- Template-based for regular bookings (auto-generated nightly)
- Bay-based for special cases (manually triggered when needed)

---

## How to Check Which System You're Using

### Template-Based Slots:
```bash
# Check if templates exist
docker exec ktl-booking-app php artisan tinker --execute="echo 'Templates: ' . App\\Models\\SlotTemplate::count()"

# Check auto-generated slots (no tipping_bay_id)
docker exec ktl-booking-app php artisan tinker --execute="echo 'Depot-wide slots: ' . App\\Models\\Slot::whereNull('tipping_bay_id')->count()"
```

### Bay-Based Slots:
```bash
# Check bay-specific slots
docker exec ktl-booking-app php artisan tinker --execute="echo 'Bay-specific slots: ' . App\\Models\\Slot::whereNotNull('tipping_bay_id')->count()"
```

---

## Cron Jobs vs Manual Triggering

### Cron Jobs (Automated):
- `slots:generate --days=14` - Daily at 00:15 (Template-based)
- `app:auto-release-slots` - Every 15 mins (Sets cut-off times)
- `bays:sync-occupancy` - Every 30 mins (Updates bay status)
- `bookings:cleanup-incomplete` - Every 15 mins (Deletes abandoned bookings)

### Manual Triggering (Web Interface):
- Bay Slot Generation - Admin → Bay Slot Generation → Generate
- **Now runs in background!** Won't hang/crash anymore ✅

---

## Recommendation 💡

**For your use case (KTL Booking System):**

1. **Use Template-Based (System 1) for now**
   - Set up templates for each booking type
   - Let cron auto-generate slots nightly
   - Simple and automatic

2. **Only use Bay-Based (System 2) if needed**
   - If specific bays have different hours
   - Run manually when needed
   - Monitor logs for completion

3. **Check logs after generation:**
   ```bash
   # Template-based logs
   docker exec ktl-booking-app tail -50 /var/www/html/storage/logs/slots_generate.log

   # Bay-based logs (background job)
   docker exec ktl-booking-app tail -50 /var/www/html/storage/logs/laravel.log | grep "Bay slot generation"
   ```

---

## Quick Fix Summary

**What I fixed:**
- ✅ Bay slot generation now runs as **background job**
- ✅ Web interface returns immediately (no hanging)
- ✅ Job runs with **10-minute timeout** (plenty of time for large datasets)
- ✅ Logs success/failure to Laravel log

**How to use:**
1. Go to Admin → Bay Slot Generation
2. Configure bay hours
3. Click "Generate"
4. You'll see: "Bay slot generation started!"
5. Wait 2-5 minutes (depending on # of bays/days)
6. Refresh slot list to see new slots

**Monitor progress:**
```bash
docker exec ktl-booking-app tail -f /var/www/html/storage/logs/laravel.log | grep "Bay slot generation"
```

---

## Need Help?

**If slots aren't generating:**
1. Check templates exist: Admin → Slot Templates
2. Check cron is running: See `CRON_TESTING_GUIDE.md`
3. Check logs: `storage/logs/slots_generate.log`

**If bay-based generation fails:**
1. Check Laravel log: `storage/logs/laravel.log`
2. Look for "Bay slot generation failed"
3. Check bay operational hours are set correctly
