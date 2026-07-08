# KTL Booking System - Simple End-to-End Test Guide

## 🎯 Quick Overview

This system has 3 main parts:
1. **Slots** = Available time windows (e.g., 10:00 AM - 10:30 AM)
2. **Bays** = Physical tipping bays where vehicles unload
3. **Bookings** = Customer reservations for a slot

## 🔐 Login Details

You need these to test:
- **Admin URL**: `http://localhost:8000/admin/login` or `http://test.test/admin/login`
- **Customer URL**: `http://localhost:8000/customer/login` or `http://test.test/customer/login`
- **Warehouse URL**: `http://localhost:8000/warehouse/login` or `http://test.test/warehouse/login`

---

## ✅ Test 1: Admin - View & Manage Slots

### Step 1: Login as Admin
1. Go to: `/admin/login`
2. Enter your admin credentials
3. You should see the dashboard

### Step 2: View Slots
1. Go to: **Admin → Inbound → Slots** or `/admin/slots`
2. You'll see a list of all time slots
3. Each slot shows:
   - Date & Time
   - Bay Name
   - Capacity (how many bookings allowed)
   - Bookings (how many currently booked)
   - Status (Blocked / Available)

### Step 3: Filter Slots
- **By Depot**: Select depot from dropdown
- **By Date**: Pick a date
- Click **Filter**

### Step 4: View Grouped Slots
- Click **"📊 Grouped View"** in top-right
- This shows all bays for each time slot together
- Easier to see total capacity per time

---

## ✅ Test 2: Block a Bay (Auto-Lock Feature)

### What This Does:
When you block ONE bay at a specific time, ALL OTHER bays at that same time are automatically blocked too.

### Steps:
1. In **Admin → Slots**, find a slot (e.g., Bay A at 10:00 AM)
2. Click **Edit** on that slot
3. Check the box **"Is Blocked"**
4. Click **Save**
5. Return to slot list
6. **Result**: ALL bays at 10:00 AM are now blocked (Bay A, Bay B, Bay C, etc.)

### To Unblock:
1. Edit ANY blocked slot at that time
2. Uncheck **"Is Blocked"**
3. Save
4. **Result**: ALL bays at that time are unblocked

---

## ✅ Test 3: Customer Makes a Booking

### Step 1: Login as Customer
1. Go to: `/customer/login`
2. Use customer credentials
3. See customer dashboard

### Step 2: Create Booking
1. Click **"Book Delivery"** or go to `/customer/bookings/create`
2. Fill in the form:
   - **Depot**: Select your depot
   - **Date**: Choose a future date
   - **Time Slot**: Pick an available slot (green)
   - **Vehicle Registration**: e.g., AB12 CDE
   - **PO Number**: e.g., PO-12345
   - **Load Type**: Select from dropdown
3. Click **Submit**

### Step 3: Verify Booking
1. You should see: **"Booking created successfully"**
2. Note your **Booking Reference** (e.g., BKG-20260216-0001)
3. Go to **"My Bookings"** - your booking appears there

---

## ✅ Test 4: Check Restricted vs Available Customers

### Setup (Admin):
1. Go to: **Admin → Inbound → Slot Release Rules**
2. Create a rule:
   - **Depot**: Depot 1
   - **Release Day**: Monday
   - **Release Time**: 09:00
   - **Restricted Customers**: Add "ABC Ltd"
3. Save

### Test - Restricted Customer (ABC Ltd):
1. Login as ABC Ltd customer
2. Try to book **next Monday at 10:00 AM**
3. **Before 09:00 on Monday**: You can see and book the slot
4. **After 09:00 on Monday**: Slot becomes public (everyone can book)

### Test - Other Customers:
1. Login as different customer (XYZ Transport)
2. Try to book same slot before Monday 09:00
3. **Result**: Slot NOT visible (restricted to ABC Ltd)
4. After Monday 09:00: Slot becomes visible

---

## ✅ Test 5: Vehicle Arrives On-Site

### Scenario: Customer booked slot, vehicle arrives

### Step 1: Check Booking Status (Admin/Warehouse)
1. Go to: **Admin → Bookings** or **Warehouse → Bookings**
2. Find the booking (search by vehicle reg or booking ref)
3. Current status should be: **"Pending"** or **"Confirmed"**

### Step 2: Mark Vehicle Arrived
1. Click on the booking
2. Click **"Mark as Arrived"** or update status to **"Arrived"**
3. System records arrival time

### Step 3: Assign to Bay
1. In booking details, click **"Assign to Bay"**
2. Select an available bay (e.g., Bay 1)
3. Click **Save**
4. Status changes to: **"At Bay"**

### Step 4: Start Tipping
1. Click **"Start Tipping"**
2. Status changes to: **"Tipping in Progress"**
3. Bay is marked as **Occupied**

### Step 5: Complete & Depart
1. When done, click **"Complete Tipping"**
2. Then click **"Mark Departed"**
3. Status changes to: **"Departed"**
4. Bay is marked as **Available** again

---

## ✅ Test 6: On-Site Booking (Walk-In Vehicle)

### Scenario: Vehicle arrives WITHOUT a booking

### Step 1: Create On-Site Booking (Warehouse)
1. Go to: **Warehouse → Quick Booking** or **Create Booking**
2. Enter vehicle registration
3. System checks if vehicle is on-site
4. Select **"On-Site"** booking type
5. Choose immediate or next available slot
6. Assign to available bay
7. Submit

### Step 2: Process as Normal
- Vehicle goes to bay
- Starts tipping
- Completes and departs

---

## ✅ Test 7: Off-Site Booking (Future Delivery)

### This is the standard customer booking flow from Test 3
- Customer books in advance
- Vehicle arrives on scheduled day
- Follow Test 5 steps

---

## ✅ Test 8: Check Cron Jobs Are Running

### Manual Test:
```bash
# Generate slots for next 14 days
php artisan slots:generate --days=14

# Release slots based on rules
php artisan app:auto-release-slots

# Sync bay occupancy
php artisan bays:sync-occupancy

# See scheduled tasks
php artisan schedule:list
```

### Verify Logs:
```bash
# Check slot generation log
tail -f storage/logs/slots_generate.log

# Check auto-release log
tail -f storage/logs/auto_release_slots.log

# Check bay sync log
tail -f storage/logs/bay_sync.log
```

---

## ✅ Test 9: Block All Future Bookings (Keep Existing)

### What You Want:
- Stop NEW bookings from being made
- Allow vehicles with EXISTING bookings to still arrive

### How to Do This:

#### Option 1: Block All Future Slots
1. Go to: **Admin → Slots**
2. Filter by **future dates**
3. For each day you want to block:
   - Edit one slot for that day
   - Check **"Is Blocked"**
   - Save
   - **All slots for that time are now blocked**
4. Repeat for each time slot you want to block

#### Option 2: Deactivate Bays
1. Go to: **Admin → Tipping Bays**
2. Edit a bay
3. Uncheck **"Is Active"**
4. Save
5. **Result**: Bay won't accept NEW bookings

#### Existing Bookings Still Work:
- Vehicles with bookings can still arrive
- System checks booking status, not slot availability
- They can still be assigned to bays and process normally

---

## 🔍 How to Check If It's Working

### 1. Check Slot Blocking:
```sql
-- See blocked slots
SELECT s.id, s.start_at, tb.name as bay_name, s.is_blocked
FROM slots s
JOIN tipping_bays tb ON s.tipping_bay_id = tb.id
WHERE s.is_blocked = 1
ORDER BY s.start_at;
```

### 2. Check Existing Bookings:
```sql
-- See future bookings
SELECT id, booking_reference, vehicle_registration, status,
       slot_id, created_at
FROM bookings
WHERE status NOT IN ('departed', 'cancelled')
ORDER BY created_at DESC;
```

### 3. Check Bay Status:
```sql
-- See bay availability
SELECT name, is_active, is_occupied
FROM tipping_bays
WHERE depot_id = 1;
```

---

## 🎯 Simple Test Checklist

- [ ] **Can login** as admin, customer, warehouse
- [ ] **Can see slots** in admin panel
- [ ] **Block one bay** → all bays at that time block
- [ ] **Unblock one bay** → all bays at that time unblock
- [ ] **Customer can book** available slot
- [ ] **Customer cannot book** blocked slot
- [ ] **Restricted customer** sees early access slots
- [ ] **Vehicle arrives** and status updates
- [ ] **Bay assignment** works
- [ ] **Tipping workflow** (arrived → at bay → tipping → departed)
- [ ] **On-site booking** for walk-in vehicle
- [ ] **Cron jobs run** (check logs)
- [ ] **Existing bookings work** even if new bookings blocked

---

## 🐛 Troubleshooting

### Can't See Admin Panel
- Check user has admin role: `SELECT * FROM model_has_roles WHERE model_id = YOUR_USER_ID;`
- Check permissions: `SELECT * FROM permissions;`

### Slots Not Generating
- Run manually: `php artisan slots:generate --days=14`
- Check templates exist: `SELECT * FROM slot_templates;`

### Bays Not Auto-Locking
- Feature added to: `app/Http/Controllers/Admin/SlotController.php`
- Make sure you're editing via admin panel (not direct database)

### Existing Bookings Can't Arrive
- Check booking status is not "cancelled"
- Check bay is active: `is_active = 1`
- Arrival system doesn't check slot availability, only booking existence

---

## 📞 Need Help?

The system has these business rules:
1. **Slots** control WHEN bookings can be made
2. **Bays** control WHERE vehicles unload
3. **Bookings** are the actual reservations
4. **Blocking a slot** prevents NEW bookings but doesn't affect existing ones
5. **Deactivating a bay** prevents it from being used but doesn't cancel existing bookings

**Your request**: "Lock bays without scheduled slots + allow existing bookings"
- **Solution**: Block future slots (they won't be able to make new bookings)
- **Existing bookings**: Still work because arrival system checks booking record, not slot availability

---

**Last Updated:** 2026-02-16
