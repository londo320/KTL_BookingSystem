# KTL Booking System - Comprehensive Test Plan

## System Overview
This document outlines test scenarios for the KTL booking system covering customer bookings, on-site/off-site vehicle management, restricted vs available customers, and bay auto-locking functionality.

---

## ✅ Fixed Issues
1. **Cron Jobs**: Now configured to run properly in `routes/console.php`
   - Slot generation: Daily at 00:15, creates slots for 14 days ahead
   - Auto-release: Every 15 minutes based on SlotReleaseRules
   - Bay sync: Every 30 minutes to update bay occupancy

2. **Bay Auto-Lock**: Implemented in `app/Http/Controllers/Admin/SlotController.php:177-194`
   - When blocking a slot, all other slots at the same depot/time are automatically blocked
   - When unblocking a slot, all other slots at the same depot/time are automatically unblocked

---

## Test Scenarios

### 1. Slot Generation & Release (Cron Jobs)

#### Test 1.1: Verify Slot Auto-Generation
**Steps:**
1. Check current slots: `php artisan db:table slots`
2. Run generation manually: `php artisan slots:generate --days=14`
3. Verify slots created for next 14 days
4. Check logs: `tail -f storage/logs/slots_generate.log`

**Expected:**
- Slots generated from templates for each day
- No duplicate slots created
- Slots respect depot and booking type settings

#### Test 1.2: Verify Auto-Release Logic
**Steps:**
1. Create a SlotReleaseRule (Admin → Inbound → Slot Release Rules)
2. Assign restricted customers to the rule
3. Run: `php artisan app:auto-release-slots`
4. Check slot `released_at` and `allowed_customers` pivot table

**Expected:**
- Restricted slots only visible to assigned customers
- Public slots visible to all after release
- Release happens on configured day/time

#### Test 1.3: Verify Bay Occupancy Sync
**Steps:**
1. Create a booking and move vehicle to bay
2. Run: `php artisan bays:sync-occupancy`
3. Check `tipping_bays.is_occupied` field

**Expected:**
- Bay marked as occupied when vehicle at bay
- Bay marked as available when vehicle departs
- Syncs every 30 minutes automatically

---

### 2. Bay Auto-Lock Feature

#### Test 2.1: Block One Bay, Lock All Others
**Location:** Admin → Inbound → Slots Management

**Steps:**
1. Navigate to slots for a specific date/time with multiple bays
2. Find a slot (e.g., Bay A at 10:00 AM)
3. Edit the slot and check "Is Blocked"
4. Save and return to slot list
5. Verify ALL bays at same depot and time (10:00 AM) are now blocked

**Expected:**
- All slots at same depot/time automatically blocked
- Message: "Slot updated successfully"
- Blocked slots show as unavailable in booking form

**Database Check:**
```sql
SELECT id, tipping_bay_id, start_at, is_blocked
FROM slots
WHERE depot_id = 1 AND start_at = '2026-02-24 10:00:00';
-- All should show is_blocked = 1
```

#### Test 2.2: Unblock One Bay, Unlock All Others
**Steps:**
1. Edit a blocked slot from Test 2.1
2. Uncheck "Is Blocked"
3. Save and return to slot list
4. Verify ALL bays at same depot and time are now unblocked

**Expected:**
- All slots at same depot/time automatically unblocked
- Slots become available for booking again

---

### 3. Customer Booking Scenarios

#### Test 3.1: Restricted Customer - Before Release
**Setup:**
- Customer: ABC Ltd (restricted)
- Depot: Depot 1
- SlotReleaseRule: Release Mondays at 09:00 to ABC Ltd only

**Steps (Customer Portal):**
1. Login as ABC Ltd customer
2. Navigate to "Book Slot"
3. Select Depot 1, choose a future date BEFORE release
4. Check available slots

**Expected:**
- Customer sees slots assigned to them via SlotReleaseRule
- Other customers do NOT see these slots yet
- Can book successfully if capacity available

#### Test 3.2: Restricted Customer - After Public Release
**Steps:**
1. Wait for or manually trigger release: `php artisan app:auto-release-slots`
2. Login as ABC Ltd
3. Check same depot/date

**Expected:**
- Customer still sees the slots (now public)
- All other customers can now also see and book these slots
- Slot shows remaining capacity

#### Test 3.3: Non-Restricted Customer - Standard Booking
**Steps:**
1. Login as standard customer (e.g., XYZ Transport)
2. Select depot and date
3. Choose available slot
4. Fill booking details (PO, vehicle reg, load type)
5. Submit booking

**Expected:**
- Customer only sees released/public slots
- Booking created with status "pending" or "confirmed"
- Slot capacity decremented
- Booking appears in dashboard

---

### 4. On-Site Vehicle Booking

#### Test 4.1: Book Vehicle Already On-Site (Drop & Swap)
**Location:** Admin/Warehouse → On-Site Bookings

**Scenario:** Vehicle arrives, drops trailer, wants to pick up empty

**Steps:**
1. Go to On-Site Bookings page
2. Select vehicle registration (should be in yard)
3. Choose "Drop & Swap" or similar booking type
4. Select available slot
5. Submit booking

**Expected:**
- System detects vehicle already on-site
- No need for arrival time (already there)
- Bay assigned based on availability
- Booking status: "on_site"

#### Test 4.2: On-Site Booking - Equipment Requirements
**Steps:**
1. Create booking type requiring specific equipment (e.g., "Handball" requires forklift)
2. Book on-site vehicle for this type
3. System should only show bays with required equipment

**Expected:**
- Only compatible bays shown
- Error if no bays have required equipment
- Booking successful if equipment available

---

### 5. Off-Site Vehicle Booking

#### Test 5.1: Standard External Booking (Web Form)
**Location:** Customer Portal → Book Delivery

**Steps:**
1. Login as customer
2. Click "Book Delivery"
3. Fill form:
   - Vehicle registration: AB12 CDE
   - Depot: Depot 1
   - Date: Tomorrow
   - Time slot: 10:00 AM
   - Load type: Waste
   - PO number: PO-12345
4. Submit

**Expected:**
- Booking created with status "pending"
- Email confirmation sent
- Slot reserved
- Appears in admin dashboard

#### Test 5.2: Off-Site Booking - Time Window Restriction
**Setup:**
- Customer ABC Ltd restricted to 08:00-12:00 on Mondays

**Steps:**
1. Login as ABC Ltd
2. Try to book slot at 14:00 on Monday

**Expected:**
- Slot not shown or error: "Booking time outside allowed window"
- Customer can only book within 08:00-12:00 window

#### Test 5.3: Off-Site Booking - Duration Calculation
**Setup:**
- Booking type: Handball (90 minutes)
- Slot duration: 30 minutes

**Steps:**
1. Book handball slot at 10:00 AM
2. Check database for occupied slots

**Expected:**
- Primary slot: 10:00-10:30 (occupied)
- Extended slots: 10:30-11:00, 11:00-11:30 (also occupied)
- Total: 90 minutes reserved
- Other bookings cannot use extended slots

**Database Check:**
```sql
SELECT * FROM slot_bookings WHERE booking_id = [booking_id];
-- Should show 3 slots: 1 primary + 2 extended
```

---

### 6. Web Booking Flow (Customer Portal)

#### Test 6.1: Complete Customer Journey
**URL:** `/customer/bookings/create`

**Steps:**
1. **Login:** Customer logs in
2. **Select Depot:** Choose depot from dropdown
3. **Select Date:** Pick future date (within 14 days)
4. **View Slots:** Available time slots appear
5. **Select Slot:** Click on available slot (green)
6. **Fill Details:**
   - Vehicle reg
   - Driver name
   - PO number
   - Load type
   - Special instructions
7. **Confirm:** Review and submit
8. **Confirmation:** Receive booking reference

**Expected:**
- Each step validates properly
- Real-time slot availability
- Blocked slots shown as unavailable (red)
- Full slots shown as unavailable
- Success message with reference number

#### Test 6.2: Web Booking - Capacity Limits
**Steps:**
1. Book a slot with capacity = 2
2. Make 2 bookings for same slot
3. Try to make 3rd booking

**Expected:**
- First 2 bookings succeed
- 3rd booking fails: "Slot at full capacity"
- Slot shows as full on booking form

---

### 7. In-App Booking Flow (Admin/Warehouse)

#### Test 7.1: Admin Creates Booking for Customer
**Location:** Admin → Bookings → Create Booking

**Steps:**
1. Login as admin
2. Go to Create Booking
3. Select customer: ABC Ltd
4. Select depot and date
5. Choose available slot
6. Fill booking details
7. Submit

**Expected:**
- Admin can see all slots (including restricted)
- Can override restrictions if needed
- Booking created on behalf of customer
- Customer receives notification

#### Test 7.2: Warehouse Books Walk-In Vehicle
**Location:** Warehouse → Quick Booking

**Steps:**
1. Login as warehouse user
2. Vehicle arrives without booking
3. Click "Quick Booking"
4. Enter vehicle reg
5. Select immediate or next available slot
6. Assign bay
7. Submit

**Expected:**
- Booking created immediately
- Bay assigned automatically
- Status: "on_site" or "at_bay"
- Vehicle can proceed to tipping

---

### 8. Booking Status Workflow

#### Test 8.1: Track Booking Through Full Lifecycle
**Steps:**
1. Create booking (status: "pending")
2. Vehicle arrives → Update to "arrived"
3. Assigned to bay → Update to "at_bay"
4. Start tipping → Update to "tipping_in_progress"
5. Complete tipping → Update to "tipping_completed"
6. Vehicle departs → Update to "departed"

**Expected:**
- Status changes tracked in booking_history table
- Timestamps recorded for each status
- Bay occupancy synced automatically

---

### 9. Edge Cases & Error Handling

#### Test 9.1: Double Booking Prevention
**Steps:**
1. Book slot at 10:00 AM (capacity = 1)
2. Try to book same slot simultaneously from 2 browsers

**Expected:**
- First booking succeeds
- Second booking fails: "Slot no longer available"
- No double booking created

#### Test 9.2: Expired Slot Booking
**Steps:**
1. Try to book slot in the past
2. Try to book slot that starts in < 2 hours

**Expected:**
- Past slot: Error "Cannot book past slots"
- Near-term slot: Error "Slot locked - too close to start time"

#### Test 9.3: Equipment Mismatch
**Steps:**
1. Book "Handball" type (requires forklift)
2. System tries to assign bay without forklift

**Expected:**
- Error: "No bays available with required equipment: forklift"
- Booking not created

---

## Database Verification Queries

### Check Slot Generation
```sql
SELECT DATE(start_at) as date, COUNT(*) as slot_count
FROM slots
WHERE start_at >= CURDATE()
GROUP BY DATE(start_at)
ORDER BY date;
```

### Check Blocked Slots
```sql
SELECT s.id, s.start_at, tb.name as bay_name, s.is_blocked
FROM slots s
JOIN tipping_bays tb ON s.tipping_bay_id = tb.id
WHERE s.is_blocked = 1;
```

### Check Slot Release Rules
```sql
SELECT sr.*, d.name as depot_name,
       GROUP_CONCAT(c.name) as restricted_customers
FROM slot_release_rules sr
JOIN depots d ON sr.depot_id = d.id
LEFT JOIN slot_release_rule_customers srrc ON sr.id = srrc.slot_release_rule_id
LEFT JOIN customers c ON srrc.customer_id = c.id
GROUP BY sr.id;
```

### Check Bay Occupancy
```sql
SELECT tb.name, tb.is_occupied,
       b.vehicle_registration, b.status
FROM tipping_bays tb
LEFT JOIN bookings b ON b.tipping_bay_id = tb.id AND b.departed_at IS NULL
WHERE tb.depot_id = 1;
```

### Check Booking Capacity
```sql
SELECT s.id, s.start_at, s.capacity,
       COUNT(sb.booking_id) as occupied
FROM slots s
LEFT JOIN slot_bookings sb ON s.id = sb.slot_id
WHERE s.start_at >= NOW()
GROUP BY s.id
HAVING occupied >= s.capacity;
```

---

## Automated Testing Commands

### Run Cron Jobs Manually
```bash
# Generate slots for next 14 days
php artisan slots:generate --days=14

# Auto-release slots based on rules
php artisan app:auto-release-slots

# Sync bay occupancy
php artisan bays:sync-occupancy

# Cleanup incomplete bookings
php artisan bookings:cleanup-incomplete --minutes=30

# Test scheduler
php artisan schedule:run
```

### Check Scheduled Tasks
```bash
php artisan schedule:list
```

---

## Test Checklist

- [ ] Slot generation creates 14 days ahead
- [ ] Auto-release runs every 15 minutes
- [ ] Bay sync updates occupancy every 30 minutes
- [ ] Blocking one bay locks all bays at same time
- [ ] Unblocking one bay unlocks all bays at same time
- [ ] Restricted customers see early access slots
- [ ] Public customers see only released slots
- [ ] On-site bookings work for vehicles in yard
- [ ] Off-site bookings work from web form
- [ ] Equipment requirements enforced
- [ ] Time windows respected
- [ ] Capacity limits enforced
- [ ] Duration calculation spans multiple slots
- [ ] Double booking prevented
- [ ] Past/locked slots cannot be booked
- [ ] Booking status workflow tracked
- [ ] Bay occupancy synced correctly

---

## Contact for Issues
- Bay auto-lock not working → Check `app/Http/Controllers/Admin/SlotController.php:177-194`
- Cron jobs not running → Check `routes/console.php` and verify scheduler is active
- Slots not releasing → Check `app/Console/Commands/AutoReleaseSlots.php`
- Bays not syncing → Check `app/Console/Commands/SyncBayOccupancy.php` (if exists)

---

**Last Updated:** 2026-02-16
**System Version:** Laravel 12 with PHP 8.4
