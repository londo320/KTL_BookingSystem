# Admin Guide: Bay Capacity & Duration Rules System

## Overview

This system provides granular control over:
1. **Which customers can use which bays** (Customer Bay Assignments)
2. **How long bookings take based on case count** (Duration Rules)
3. **Maximum concurrent bookings per booking type** (Bay Capacity Rules)
4. **Customer-specific time windows** (Time Window Rules)

---

## 1. Customer Bay Assignments

### Purpose
Control which customers can access specific tipping bays and set priority orders for automatic bay selection.

### How to Configure

**Navigate to:** Settings → Customers → [Select Customer] → Bay Assignments button

#### Steps:
1. Click "Bay Assignments" button next to any customer
2. For each bay, configure:
   - **Allowed**: ✅ Enable to allow customer access to this bay
   - **Priority**: Number (1-100) - Lower numbers = higher priority for automatic assignment
   - **Equipment**: Display only - shows what equipment this bay can handle

#### Example Configuration:
```
Customer: ABC Foods Ltd
Depot: Main Warehouse

Bay A1 - ✅ Allowed, Priority: 1, Equipment: [Forklift, Pallet Jack]
Bay A2 - ✅ Allowed, Priority: 2, Equipment: [Forklift, Reach Truck]
Bay B1 - ❌ Not Allowed
Bay B2 - ✅ Allowed, Priority: 3, Equipment: [Pallet Jack]
```

**Result:** When ABC Foods books a slot, the system will try to assign Bay A1 first, then A2, then B2. Bay B1 is completely blocked.

---

## 2. Duration Rules (Case-Based)

### Purpose
Automatically calculate booking duration based on the number of cases being delivered.

### How It Works
When a booking is created with a case count, the system:
1. Looks for the most specific rule (Customer + Depot → Depot → Global)
2. Finds the rule where case count falls within the min/max range
3. Applies that duration in minutes

### How to Configure

**Navigate to:** Settings → Duration Rules (Case-Based)

#### Create a Rule:
1. Click "Create New Duration Rule"
2. Configure:
   - **Booking Type**: Select the type (e.g., Container, Handball)
   - **Minimum Cases**: Start of case range (e.g., 0)
   - **Maximum Cases**: End of case range (e.g., 5000) - Leave blank for unlimited
   - **Duration (minutes)**: How long this booking takes (e.g., 180 = 3 hours)
   - **Depot** (optional): Apply only to specific depot
   - **Customer** (optional): Apply only to specific customer
   - **Priority**: Higher = checked first (use 100 for customer-specific, 50 for depot, 0 for global)

#### Example Configuration:

**Handball Rules (Global):**
```
Rule 1:
- Booking Type: Handball
- Min Cases: 0
- Max Cases: [blank = unlimited]
- Duration: 180 minutes (3 hours)
- Depot: [blank = all depots]
- Customer: [blank = all customers]
- Priority: 0
```

**Container Rules with Case Ranges:**
```
Rule 1:
- Booking Type: Container
- Min Cases: 0
- Max Cases: 5000
- Duration: 180 minutes (3 hours)
- Priority: 0

Rule 2:
- Booking Type: Container
- Min Cases: 5001
- Max Cases: [blank]
- Duration: 240 minutes (4 hours)
- Priority: 0
```

**Customer-Specific Override:**
```
Rule 1:
- Booking Type: Container
- Min Cases: 0
- Max Cases: 3000
- Duration: 120 minutes (2 hours)
- Customer: XYZ Fast Foods
- Priority: 100
```

### How Duration is Calculated in Bookings

**Example 1:** Customer books Handball with 3000 cases at 06:00
- System finds: Handball rule (0 to unlimited cases) = 180 minutes
- **Expected finish: 09:00** (06:00 + 3 hours)

**Example 2:** Customer books Container with 6000 cases at 14:00
- System finds: Container rule (5001+ cases) = 240 minutes
- **Expected finish: 18:00** (14:00 + 4 hours)

**Example 3:** XYZ Fast Foods books Container with 2500 cases at 08:00
- System finds customer-specific rule (priority 100) = 120 minutes
- **Expected finish: 10:00** (08:00 + 2 hours)

---

## 3. Bay Capacity Rules

### Purpose
Limit how many bookings of a specific type can run simultaneously at a depot, especially during specific time windows.

### Why This Matters
Example scenarios:
- "We can only handle 3 handball operations at once between 8am-5pm"
- "After 5pm, we can handle 4 handballs because we have more staff"
- "Depot A can only handle 3 containers max, but Depot B can handle 5"

### How to Configure

**Navigate to:** Settings → Bay Capacity Rules

#### Create a Rule:
1. Click "Create New Bay Capacity Rule"
2. Configure:
   - **Depot**: Select depot (REQUIRED)
   - **Booking Type**: Select type (leave blank = apply to all types)
   - **Start Time**: When this rule starts (e.g., 08:00)
   - **End Time**: When this rule ends (e.g., 17:00)
   - **Days of Week**: Select specific days (leave unchecked = all days)
   - **Max Concurrent Bookings**: Maximum allowed at once (e.g., 3)
   - **Capacity Weight**: How much capacity this type uses (1.0 = normal, 2.0 = double)
   - **Active**: Enable/disable this rule

#### Example Configurations:

**Scenario 1: Limit Handball Operations**
```
Rule 1:
- Depot: Main Warehouse
- Booking Type: Handball
- Start Time: 08:00
- End Time: 17:00
- Days of Week: [All days]
- Max Concurrent: 3
- Capacity Weight: 1.0
- Active: ✅
```
**Effect:** Only 3 handball bookings can run at the same time between 8am-5pm.

**Scenario 2: Higher Capacity After Hours**
```
Rule 1:
- Depot: Main Warehouse
- Booking Type: Handball
- Start Time: 08:00
- End Time: 17:00
- Max Concurrent: 3

Rule 2:
- Depot: Main Warehouse
- Booking Type: Handball
- Start Time: 17:00
- End Time: 23:00
- Max Concurrent: 5
```
**Effect:** 3 handballs during day, 5 after 5pm.

**Scenario 3: Handball Uses More Resources**
```
Rule 1:
- Depot: Main Warehouse
- Booking Type: All Types
- Start Time: 00:00
- End Time: 23:59
- Max Concurrent: 10
- Capacity Weight: 1.0

Rule 2:
- Depot: Main Warehouse
- Booking Type: Handball
- Capacity Weight: 2.0
```
**Effect:** Total capacity = 10 units. Normal bookings use 1.0, handballs use 2.0. So you could have 10 normal bookings OR 5 handballs OR 6 normal + 2 handballs (6×1.0 + 2×2.0 = 10).

**Scenario 4: Different Limits Per Depot**
```
Depot A Rule:
- Depot: Depot A
- Booking Type: Container
- Max Concurrent: 3

Depot B Rule:
- Depot: Depot B
- Booking Type: Container
- Max Concurrent: 5
```
**Effect:** Depot A can handle 3 containers max, Depot B can handle 5.

---

## 4. Customer Time Windows

### Purpose
Allow specific customers to book outside standard hours or restrict them to certain time windows.

### How to Configure

**Navigate to:** Settings → Customers → [Select Customer] → Time Windows button

#### Steps:
1. Click "Time Windows" button next to any customer
2. For each depot, add time windows:
   - **Depot**: Select depot
   - **Start Time**: Earliest allowed booking time (e.g., 06:00)
   - **End Time**: Latest allowed booking time (e.g., 22:00)
   - **Days**: Select which days this applies to

#### Example:
```
Customer: Premium Foods Ltd

Window 1:
- Depot: Main Warehouse
- Start: 06:00, End: 22:00
- Days: Monday-Friday

Window 2:
- Depot: Main Warehouse
- Start: 08:00, End: 18:00
- Days: Saturday, Sunday
```
**Effect:** Premium Foods can book 6am-10pm on weekdays, but only 8am-6pm on weekends.

---

## 5. Complete Workflow Example

### Scenario: Setting Up Handball Operations

**Business Requirements:**
- Handball takes 3 hours by default
- If over 5000 cases, extend to 4 hours
- Maximum 3 handballs can run at once during business hours (8am-5pm)
- After 5pm, we can handle 4 handballs
- Only depots with specialized equipment can handle handballs
- Specific customers get priority bay access

### Configuration Steps:

#### Step 1: Configure Duration Rules
```
Rule 1 (Base):
- Type: Handball, Cases: 0-5000, Duration: 180 min, Priority: 0

Rule 2 (High Volume):
- Type: Handball, Cases: 5001+, Duration: 240 min, Priority: 0
```

#### Step 2: Configure Bay Capacity Rules
```
Rule 1 (Day Shift):
- Depot: Main Warehouse
- Type: Handball
- Time: 08:00-17:00
- Max Concurrent: 3

Rule 2 (Evening Shift):
- Depot: Main Warehouse
- Type: Handball
- Time: 17:00-23:00
- Max Concurrent: 4
```

#### Step 3: Configure Bay Assignments
For each handball-capable customer:
```
Customer: ABC Foods
- Bay H1: ✅ Allowed, Priority: 1
- Bay H2: ✅ Allowed, Priority: 2
- Bay H3: ✅ Allowed, Priority: 3
- Other bays: ❌ Not Allowed
```

### Result When Booking:

**Booking 1:** ABC Foods books Handball, 3000 cases, 06:00
- ✅ Duration calculated: 180 minutes (3 hours)
- ✅ Expected finish: 09:00
- ✅ Bay assigned: H1 (highest priority)
- ✅ Capacity check: 1 of 3 slots used

**Booking 2:** ABC Foods books Handball, 6500 cases, 14:00
- ✅ Duration calculated: 240 minutes (4 hours)
- ✅ Expected finish: 18:00
- ✅ Bay assigned: H2 (H1 occupied)
- ✅ Capacity check: 2 of 3 slots used (14:00-17:00), then 2 of 4 slots (17:00-18:00)

**Booking 3:** ABC Foods tries 4th Handball at 10:00
- ❌ REJECTED: Maximum 3 handballs already running during 10:00-13:00

---

## 6. Slot Generation for Bay-Based System

### Command
```bash
php artisan slots:generate-bay --depot=1 --days=7 --hours=24
```

### What It Does
- Generates **hourly slots per bay** (not per depot)
- Each bay gets 24 slots per day (00:00, 01:00, 02:00... 23:00)
- Slots are automatically marked with depot-specific release rules

### Example Output
```
Bay A1:
- 2025-10-15 00:00 → 01:00
- 2025-10-15 01:00 → 02:00
- 2025-10-15 02:00 → 03:00
... (24 slots per day)

Bay A2:
- 2025-10-15 00:00 → 01:00
... (24 slots per day)
```

### Parameters
- `--depot=1`: Generate for specific depot only (optional)
- `--days=7`: How many days ahead to generate (default: 7)
- `--hours=24`: How many hourly slots per day (default: 24)

---

## 7. Checking Rules Are Working

### Test Scenario Checklist

#### ✅ Duration Rules Test:
1. Create a booking with Handball, 2000 cases at 06:00
2. Expected result: Finish time = 09:00 (3 hours)
3. Create a booking with Container, 6000 cases at 14:00
4. Expected result: Finish time = 18:00 (4 hours due to high case count)

#### ✅ Bay Capacity Test:
1. Create 3 handball bookings overlapping at 10:00-13:00
2. Expected result: All succeed
3. Try to create 4th handball booking at 11:00
4. Expected result: REJECTED with message "Maximum 3 concurrent Handball bookings allowed between 08:00-17:00"

#### ✅ Bay Assignment Test:
1. As Customer A, create booking
2. Expected result: Assigned to highest priority allowed bay
3. Create 2nd booking while 1st still running
4. Expected result: Assigned to next priority bay

#### ✅ Time Window Test:
1. As restricted customer, try booking outside allowed window
2. Expected result: REJECTED with message about time restrictions

---

## 8. Priority System Explained

### Duration Rules Priority
- **100**: Customer + Depot specific (most specific)
- **50**: Depot specific
- **0**: Global (applies to all)

**Example:**
If Customer ABC has a rule with priority 100 for 120 minutes, and there's a global rule with priority 0 for 180 minutes, Customer ABC will always get 120 minutes.

### Bay Assignment Priority
- **1-10**: High priority bays (assigned first)
- **50**: Medium priority
- **90-100**: Low priority (assigned last, used as overflow)

---

## 9. Common Questions

### Q: What happens if no duration rule matches?
**A:** System falls back to the booking type's default duration (usually 60 minutes).

### Q: What happens if no bay capacity rule exists?
**A:** No restrictions applied - unlimited concurrent bookings allowed.

### Q: Can I have different rules for weekdays vs weekends?
**A:** Yes! Use the "Days of Week" checkbox in Bay Capacity Rules.

### Q: How do I block a bay temporarily?
**A:** Edit the bay directly (Settings → Tipping Bays) and set "Is Active" to disabled.

### Q: Can I see which rules are being applied to a booking?
**A:** Currently rules are applied automatically. Check the booking's expected finish time and assigned bay to verify correct rules are in effect.

---

## 10. Troubleshooting

### Problem: Booking duration is wrong
**Check:**
1. Settings → Duration Rules - Verify rule exists for that booking type and case count
2. Check priority values - Higher priority rules override lower ones
3. Verify depot and customer IDs match

### Problem: "Maximum concurrent bookings exceeded" error
**Check:**
1. Settings → Bay Capacity Rules - Verify max concurrent value
2. Check time window for the rule (start/end times)
3. Look at existing bookings that overlap with the requested time
4. Check capacity weights - Handballs might be using 2.0x capacity

### Problem: Customer can't access certain bays
**Check:**
1. Settings → Customers → Bay Assignments
2. Verify bay is marked as "Allowed" for that customer
3. Check bay equipment requirements match booking type

### Problem: No slots available
**Check:**
1. Run slot generation command: `php artisan slots:generate-bay --days=14`
2. Verify bays are marked as "Is Active" in Settings → Tipping Bays
3. Check slot release rules haven't restricted access

---

## Need Help?

Contact your system administrator or refer to:
- **Depot Management**: Settings → Depots
- **Bay Management**: Settings → Tipping Bays
- **Customer Settings**: Settings → Customers
- **Slot Generation**: Settings → Generate Slots

---

*Last Updated: 2025-10-15*
