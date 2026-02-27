# Quick Reference - Bay Locking & Testing

## 🚀 Start Testing Right Now

### 1. Access Admin Panel
```
URL: http://test.test/admin/slots
```

### 2. Block Bays Auto-Lock Feature
1. Click **Edit** on any slot
2. Check **"Is Blocked"**
3. Save
4. **Result**: ALL bays at that time are blocked

### 3. Lock Bays Without Slots
```bash
# Lock all bays that don't have future slots
php artisan bays:lock-without-slots

# Lock bays for specific depot only
php artisan bays:lock-without-slots --depot_id=1

# Unlock all bays
php artisan bays:lock-without-slots --unlock
```

## 🔐 What Happens When You Lock Bays

### Option 1: Block Slots (Auto-Lock Feature)
- **Effect**: Slots become unavailable for NEW bookings
- **Existing Bookings**: ✅ Still work, vehicles can still arrive
- **How**: Edit slot → Check "Is Blocked" → All bays at that time block

### Option 2: Lock Bays Without Slots (New Command)
- **Effect**: Bays marked as inactive (won't appear in booking form)
- **Existing Bookings**: ✅ Still work, vehicles can still arrive
- **How**: Run `php artisan bays:lock-without-slots`

## 📋 Key URLs

| What | URL |
|------|-----|
| **Admin Slots** | `/admin/slots` |
| **Admin Bookings** | `/admin/bookings` |
| **Admin Bays** | `/admin/tipping-bays` |
| **Customer Booking** | `/customer/bookings/create` |
| **Warehouse Dashboard** | `/warehouse` |

## ✅ Quick Tests

### Test 1: Block Future Bookings
```bash
# Step 1: Lock bays without slots
php artisan bays:lock-without-slots

# Step 2: Try to make a new booking (should fail or show no bays)
# Step 3: Check existing booking can still arrive (should work)
```

### Test 2: Bay Auto-Lock
```bash
# Step 1: Go to /admin/slots
# Step 2: Edit any slot, check "Is Blocked"
# Step 3: Check other bays at same time - all blocked
```

### Test 3: Customer Booking Flow
```bash
# Step 1: Login as customer
# Step 2: Go to "Book Delivery"
# Step 3: Select depot, date, time
# Step 4: Fill form and submit
# Step 5: Check "My Bookings" - should appear
```

## 🔍 Check What's Happening

### See Blocked Slots
```bash
php artisan db:table slots --where="is_blocked=1"
```

### See Inactive Bays
```bash
php artisan db:table tipping_bays --where="is_active=0"
```

### See Future Bookings
```bash
php artisan db:table bookings --where="status!=departed" --limit=10
```

### Check Cron Jobs
```bash
php artisan schedule:list
```

## 🎯 Your Question Answered

**"Lock bays without scheduled slots but allow existing bookings to arrive"**

### Solution:
```bash
# Run this command:
php artisan bays:lock-without-slots

# What it does:
# ✅ Marks bays as "inactive" if they have no future slots
# ✅ Prevents NEW bookings from selecting these bays
# ✅ Existing bookings still work - vehicles can arrive and use the bays
# ✅ System checks booking record, not bay availability for arrivals
```

### Why This Works:
- **New bookings**: System only shows active bays → Inactive bays hidden
- **Existing bookings**: System checks if booking exists → Bay activity doesn't matter
- **Result**: New bookings blocked, existing bookings allowed

## 📞 Still Confused?

The system is simpler than it looks:

1. **Slots** = Time windows (when you can book)
2. **Bays** = Physical locations (where you unload)
3. **Bookings** = Reservations (customer orders)

**To stop new bookings:**
- Block slots (prevents time booking)
- OR deactivate bays (prevents location selection)

**Existing bookings always work:**
- System looks for booking record
- Doesn't recheck slot/bay availability
- Vehicle can arrive and process normally

## 🛠️ Commands Summary

| Command | What It Does |
|---------|-------------|
| `php artisan slots:generate --days=14` | Create slots for next 14 days |
| `php artisan app:auto-release-slots` | Release slots based on rules |
| `php artisan bays:sync-occupancy` | Update bay occupied status |
| `php artisan bays:lock-without-slots` | **Lock bays without future slots** |
| `php artisan bays:lock-without-slots --unlock` | **Unlock all bays** |
| `php artisan schedule:run` | Run all scheduled tasks now |

---

**Need the full test plan?** See `SIMPLE_TEST_GUIDE.md`
**Need technical details?** See `BOOKING_TEST_PLAN.md`
