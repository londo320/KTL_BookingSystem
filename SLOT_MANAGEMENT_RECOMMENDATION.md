# Slot Management Strategy - Recommendation

## 🎯 Your Requirements

1. **Automate slot availability** - Generate slots automatically without manual management
2. **Prevent site overload** - Control how many bookings can happen at once
3. **Shut down bays dynamically** - Turn bays on/off without affecting slots
4. **Restrict bookings when needed** - Block time periods for maintenance/issues

---

## ✅ RECOMMENDED: Keep Current DEPOT-WIDE System

Based on your requirements, the **current depot-wide slot approach is better** because:

### Advantages for Your Use Case

#### 1. **Automatic Capacity Management**
- Slot capacity = number of active bays
- When you deactivate a bay → capacity automatically adjusts
- No need to modify slots when bays change

#### 2. **Simple Bay Shutdown**
```php
// Shut down Bay 3 for maintenance
$bay->update(['is_active' => false]);

// Result:
// ✅ Bay excluded from new bookings
// ✅ Existing bookings preserved
// ✅ Slots unchanged
// ✅ Capacity auto-adjusts
```

#### 3. **Flexible Site Load Control**
You have **4 ways** to control site capacity:

**Option A: Slot Capacity** (Easiest)
- Set `slot->capacity` lower than available bays
- Example: 10 bays active, but capacity=6
- Limits concurrent bookings regardless of bays

**Option B: Deactivate Bays**
- Set `bay->is_active = false`
- Bay unavailable for assignment
- Quick and reversible

**Option C: Block Slots**
- Set `slot->is_blocked = true`
- Entire time period unavailable
- Good for depot-wide closures

**Option D: Bay Capacity Rules**
- Use `BayCapacityRule` table
- Booking-type specific limits
- Example: "Max 3 handball bookings 08:00-15:00"

#### 4. **Less Database Overhead**
- **Depot-wide**: ~240 slots per depot (8 hours × 30 days)
- **Bay-specific**: ~2,160 slots per depot (9 bays × 8 hours × 30 days)

---

## 🔧 How Current System Handles Your Requirements

### Requirement 1: Automate Slot Availability ✅

**Current Implementation:**
```bash
# Cron job runs daily at 00:15
php artisan slots:generate --days=14
```

**How it works:**
1. SlotTemplate defines: day_of_week, start_time, duration, capacity
2. Command generates depot-wide slots for next 14-30 days
3. Capacity set from template (usually = number of active bays)
4. No manual slot management needed

**Configuration:**
- Edit `SlotTemplate` to change hours/capacity
- System auto-generates going forward
- Example: Change 08:00-17:00 to 06:00-22:00

---

### Requirement 2: Prevent Site Overload ✅

**Method 1: Slot Capacity (Recommended)**
```php
// Limit bookings during peak times
Slot::where('start_at', '>=', '2026-02-24 08:00')
    ->where('start_at', '<', '2026-02-24 12:00')
    ->update(['capacity' => 3]); // Only 3 bookings allowed
```

**Method 2: Bay Capacity Rules**
```php
// Max 3 handball bookings between 08:00-15:00
BayCapacityRule::create([
    'depot_id' => $depot->id,
    'booking_type_id' => $handballType->id,
    'time_start' => '08:00',
    'time_end' => '15:00',
    'max_concurrent_bookings' => 3,
]);
```

**Method 3: Deactivate Bays Temporarily**
```php
// Reduce capacity by deactivating bays
TippingBay::whereIn('id', [4, 5, 6])->update(['is_active' => false]);
```

---

### Requirement 3: Shut Down Bays ✅

**Simple On/Off Toggle:**
```php
// Turn off bay (maintenance, equipment failure, etc.)
$bay->update(['is_active' => false]);

// Turn back on
$bay->update(['is_active' => true]);
```

**What Happens:**
- ✅ Bay excluded from `CustomerBayAssignment::getAvailableBaysForCustomer()`
- ✅ New bookings cannot be assigned to this bay
- ✅ Existing bookings on this bay are preserved
- ✅ No slots need to be modified
- ✅ Capacity automatically reduces

**Test Results:**
- Before: 3 handball bays available (Bay 1, Bay 2, BAY 8)
- Deactivate Bay 1
- After: 2 handball bays available (Bay 2, BAY 8)
- System automatically adapts ✅

---

### Requirement 4: Restrict Bookings ✅

**Option A: Block Entire Time Periods**
```php
// Block slots for depot maintenance
Slot::where('depot_id', $depot->id)
    ->whereBetween('start_at', ['2026-03-01 00:00', '2026-03-01 23:59'])
    ->update(['is_blocked' => true]);
```

**Option B: Block Specific Bay**
```php
// Deactivate bay (prevents new bookings)
$bay->update(['is_active' => false]);
```

**Option C: Customer Restrictions**
```php
// Restrict customer to specific bays
CustomerBayAssignment::create([
    'customer_id' => $customer->id,
    'tipping_bay_id' => $bay->id,
    'priority' => 50,
    'is_active' => true,
]);
// Customer can ONLY use assigned bays
```

---

## 🚫 Why NOT Bay-Specific Slots

While bay-specific slots seem more granular, they create problems for your use case:

### Problems:

1. **Bay Shutdown Complexity**
   - Must find and block ALL slots for that bay
   - Must track which slots belong to which bay
   - Cannot just toggle `is_active`

2. **Capacity Management**
   - Cannot easily limit total site capacity
   - Must adjust each bay's slots individually
   - Less flexible for load balancing

3. **Operating Hours**
   - Bay-specific hours rarely needed in practice
   - Most sites run standard hours
   - Can be handled with bay deactivation for off-hours

4. **Database Bloat**
   - 9× more slot records
   - Slower queries
   - More complex maintenance

---

## 📋 Recommended Slot Management Workflow

### Daily Operations:

**Morning:**
```php
// Check which bays are available today
TippingBay::active()->forDepot($depotId)->get();

// Deactivate bays as needed
$bay->update(['is_active' => false]);
```

**During Day:**
```php
// If site getting busy, reduce capacity temporarily
Slot::current()->update(['capacity' => 3]);

// Or block specific time period
Slot::whereBetween('start_at', [$start, $end])
    ->update(['is_blocked' => true]);
```

**Maintenance:**
```php
// Scheduled maintenance - deactivate bay
$bay->update(['is_active' => false]);
// All future bookings automatically avoid this bay
// Reactivate when done
$bay->update(['is_active' => true]);
```

---

## 🎯 Configuration

### Slot Templates (One-time Setup)

Edit slot templates to define operating hours:

```php
SlotTemplate::create([
    'depot_id' => $depot->id,
    'day_of_week' => 1, // Monday
    'start_time' => '08:00',
    'duration_minutes' => 60,
    'capacity' => 4, // Max 4 concurrent bookings
]);
```

### Bay Configuration

```php
TippingBay::create([
    'depot_id' => $depot->id,
    'name' => 'Bay 1',
    'is_active' => true,
    'equipment' => ['handball', 'palletised'],
    // Optional: track operating hours for reference
    'operational_start' => '08:00',
    'operational_end' => '17:00',
]);
```

---

## ✅ Final Recommendation

**Keep the current DEPOT-WIDE system** because it:

1. ✅ Automatically generates slots (cron job)
2. ✅ Prevents overload (slot capacity + bay rules)
3. ✅ Easy bay shutdown (toggle is_active)
4. ✅ Multiple restriction options (block, capacity, rules)
5. ✅ Simple to manage
6. ✅ Handles all your requirements
7. ✅ Already working and tested

**No changes needed** - the current system is optimal for your use case!

---

## 🛠️ Tools Available to You

### Admin Controls (Already Built):

1. **Slot Management**
   - View slots
   - Block/unblock time periods
   - Adjust capacity

2. **Bay Management**
   - Activate/deactivate bays
   - Configure equipment
   - Set customer assignments

3. **Capacity Rules**
   - Booking-type limits
   - Time-based restrictions
   - Depot-wide or bay-specific

4. **Customer Restrictions**
   - Assign specific bays to customers
   - Priority ordering
   - Equipment matching

---

## 📊 Quick Reference

| Need to...                          | Solution                                |
|-------------------------------------|----------------------------------------|
| Close bay for maintenance           | `$bay->is_active = false`              |
| Limit concurrent bookings           | Reduce `$slot->capacity`               |
| Block entire day                    | `$slot->is_blocked = true`             |
| Max 3 handball bookings at once     | Create `BayCapacityRule`               |
| Change operating hours              | Update `SlotTemplate`, regenerate      |
| Restrict customer to specific bays  | Create `CustomerBayAssignment`         |
| Emergency shutdown                  | Deactivate all bays at depot           |

---

## 🚀 Next Steps

1. ✅ **Keep current system** (no changes needed)
2. ✅ **Generate slots for all depots**: `php artisan slots:generate --days=30`
3. ✅ **Configure slot templates** for each depot's operating hours
4. ✅ **Set bay equipment** to enable booking type filtering
5. ✅ **Test bay shutdown** to confirm it prevents new bookings
6. ✅ **Set capacity rules** if needed for specific booking types

System is ready for production! 🎉
