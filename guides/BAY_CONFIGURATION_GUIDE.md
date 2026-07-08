# Bay Configuration Guide

## ✅ Slot Generation Method Changed

**OLD:** Template-based (`slots:generate`)
**NEW:** Bay-based (`slots:generate-by-bay`)

The system is now configured to use **bay-based slot generation** which supports:
- ✅ Customer bay assignments
- ✅ Equipment requirements (handball capability)
- ✅ Per-bay operating hours
- ✅ Multiple bookings at same time (different bays)

---

## 🔧 Bay Configuration Checklist

### 1. Enable Handball Capability on Bays

Mark which bays can handle handball bookings:

```sql
-- Example: Enable handball on Bay 1 and Bay 2 at Main Depot
UPDATE tipping_bays
SET can_handle_handball = 1
WHERE name IN ('Bay 1', 'Bay 2')
AND depot_id = (SELECT id FROM depots WHERE name = 'Main Depot');
```

**Check current configuration:**
```bash
php artisan tinker --execute="
\$bays = App\Models\TippingBay::where('can_handle_handball', 1)->get();
echo 'Handball-capable bays: ' . \$bays->count();
"
```

### 2. Set Bay Operating Hours (Optional)

If bays have different hours than depot defaults:

```sql
-- Example: Bay 1 operates 06:00-14:00
UPDATE tipping_bays
SET operating_start_time = '06:00:00',
    operating_end_time = '14:00:00'
WHERE name = 'Bay 1'
AND depot_id = 1;
```

**If NULL:** Uses depot's default operating hours

### 3. Set Bay Capacity (Default = 1)

How many bookings can use this bay simultaneously:

```sql
-- Usually keep at 1 (one booking per bay at a time)
UPDATE tipping_bays
SET capacity = 1
WHERE depot_id = 1;
```

### 4. Enable Palletised Capability

```sql
-- Enable palletised handling on all bays
UPDATE tipping_bays
SET can_handle_palletised = 1
WHERE is_active = 1;
```

---

## 🎯 Example: Configure 2 Handball Bays + 3 Palletised Bays

```sql
-- Main Depot: Bay 1 & Bay 2 = Handball + Palletised
UPDATE tipping_bays
SET can_handle_handball = 1,
    can_handle_palletised = 1,
    capacity = 1
WHERE name IN ('Bay 1', 'Bay 2')
AND depot_id = (SELECT id FROM depots WHERE name = 'Main Depot');

-- Main Depot: Bay 3, 4, 5 = Palletised only
UPDATE tipping_bays
SET can_handle_handball = 0,
    can_handle_palletised = 1,
    capacity = 1
WHERE name IN ('Bay 3', 'Bay 4', 'Bay 5')
AND depot_id = (SELECT id FROM depots WHERE name = 'Main Depot');
```

**Result:**
- **Handball at 07:00** → Can book on Bay 1 OR Bay 2 (2 concurrent bookings possible)
- **Palletised at 07:00** → Can book on any of 5 bays (5 concurrent bookings possible)

---

## 🚀 Generate Bay-Based Slots

### Manual Generation (Test)

```bash
# Generate next 30 days for all depots
php artisan slots:generate-by-bay --days=30

# Generate for specific depot
php artisan slots:generate-by-bay --depot=1 --days=30
```

### Automatic (Cron)

✅ **Already configured!** Runs daily at 00:15

```
Schedule::command('slots:generate-by-bay', ['--days' => 30])
    ->dailyAt('00:15');
```

---

## 📊 How Multi-Bay Bookings Work

### Scenario: 2 Handball Bays

**Bay Configuration:**
```
Bay 1: can_handle_handball = YES, capacity = 1
Bay 2: can_handle_handball = YES, capacity = 1
```

**Slot Generation Creates:**
```
07:00 - Bay 1 (capacity: 1)
07:00 - Bay 2 (capacity: 1)
07:15 - Bay 1 (capacity: 1)
07:15 - Bay 2 (capacity: 1)
... etc
```

**Booking Flow:**

1. **Customer A books Handball at 07:00:**
   - System finds Bay 1 slot at 07:00
   - Occupies Bay 1 from 07:00-10:00 (12 slots)
   - Bay 1 now FULL, Bay 2 still available

2. **Customer B books Handball at 07:00:**
   - System finds Bay 2 slot at 07:00
   - Occupies Bay 2 from 07:00-10:00 (12 slots)
   - Bay 2 now FULL

3. **Customer C tries to book Handball at 07:00:**
   - System checks both bays
   - Both FULL → Booking REJECTED ❌

4. **Customer C books Handball at 10:00:**
   - Bay 1 and Bay 2 both available again
   - Booking SUCCEEDS ✅

---

## 🔍 Verify Configuration

### Check Bay Settings

```bash
php artisan tinker --execute="
\$bays = App\Models\TippingBay::where('is_active', 1)->get();
foreach (\$bays as \$bay) {
    echo \$bay->depot->name . ' - ' . \$bay->name . ':' . PHP_EOL;
    echo '  Handball: ' . (\$bay->can_handle_handball ? 'YES' : 'NO') . PHP_EOL;
    echo '  Palletised: ' . (\$bay->can_handle_palletised ? 'YES' : 'NO') . PHP_EOL;
    echo '  Capacity: ' . \$bay->capacity . PHP_EOL;
    echo PHP_EOL;
}
"
```

### Check Generated Slots

```bash
php artisan tinker --execute="
\$slots = App\Models\Slot::with('tippingBay')
    ->where('start_at', '>=', now())
    ->whereTime('start_at', '07:00:00')
    ->whereDate('start_at', '2026-02-28')
    ->get();

echo 'Slots at 07:00 on Feb 28:' . PHP_EOL;
foreach (\$slots as \$slot) {
    echo '  Bay: ' . \$slot->tippingBay->name . ', Capacity: ' . \$slot->capacity . PHP_EOL;
}
"
```

---

## ⚠️ Important Notes

1. **One Generation Method:** Only use `slots:generate-by-bay`, NOT both template and bay-based
2. **Bay Configuration Required:** Bays need `can_handle_handball` or `can_handle_palletised` set
3. **Capacity = 1:** Each bay should have capacity of 1 (one booking at a time)
4. **Release Rules:** Slots still respect release rules (released_at, locked_at)
5. **Customer Bay Assignments:** Optional - restricts which bays customers can see

---

## 🎉 Summary

**Your System Configuration:**
- ✅ Bay-based slot generation (30 days ahead)
- ✅ Multi-slot duration support (Handball = 3 hours)
- ✅ Prevents double booking via capacity checks
- ✅ Multiple concurrent bookings (different bays)
- ✅ Bay assignment on arrival (not at booking)
- ✅ Equipment requirements per bay
- ✅ Customer bay restrictions (optional)

**Next Steps:**
1. Configure handball capability on your bays
2. Generate slots: `php artisan slots:generate-by-bay --days=30`
3. Configure slot release rules
4. Test booking system!
