# Booking System Enhancement Implementation Guide

## Overview
This document describes the implementation of two major features for the booking system:
1. **Optional SKU Configuration** - Per-customer/depot control over SKU field visibility
2. **Advanced Slot & Bay Logic** - Time windows, bay assignments, equipment matching, and duration-based availability

---

## Task 1: Optional SKU Configuration

### Database Schema

**Table: `customer_booking_configs`**
```sql
- id
- customer_id (FK to customers)
- depot_id (FK to depots, nullable for global settings)
- sku_fields_enabled (boolean, default true)
- require_po_data (boolean, default true)
- timestamps
- UNIQUE(customer_id, depot_id)
```

### Model: `CustomerBookingConfig`

**Location:** `app/Models/CustomerBookingConfig.php`

**Key Methods:**
- `getConfig($customerId, $depotId)` - Get configuration with fallback logic
- `skuFieldsEnabled($customerId, $depotId)` - Check if SKU fields should be shown
- `poDataRequired($customerId, $depotId)` - Check if PO data is required

**Configuration Hierarchy:**
1. Customer + Depot specific config (highest priority)
2. Customer global config (depot_id = null)
3. Default config (all fields enabled)

### Usage Examples

#### Check if SKU fields should be shown
```php
use App\Models\CustomerBookingConfig;

$customerId = 5;
$depotId = 2;

$showSkuFields = CustomerBookingConfig::skuFieldsEnabled($customerId, $depotId);
// Returns: true/false
```

#### Set configuration for a customer at a specific depot
```php
CustomerBookingConfig::create([
    'customer_id' => 5,
    'depot_id' => 2,
    'sku_fields_enabled' => false,  // Hide SKU fields
    'require_po_data' => false,     // Don't require PO data
]);
```

#### Set global configuration for a customer (all depots)
```php
CustomerBookingConfig::create([
    'customer_id' => 5,
    'depot_id' => null,             // NULL = applies to all depots
    'sku_fields_enabled' => false,
    'require_po_data' => false,
]);
```

### Integration Points

#### 1. Booking Form (Blade)
**File:** `resources/views/admin/bookings/_form.blade.php`

Add this at the top of the form to get config:
```php
@php
    $depotId = old('depot_id', $booking->slot->depot_id ?? null);
    $customerId = old('customer_id', $booking->customer_id ?? null);

    $config = \App\Models\CustomerBookingConfig::getConfig($customerId, $depotId);
    $showSkuFields = $config['sku_fields_enabled'];
    $requirePoData = $config['require_po_data'];
@endphp
```

Wrap SKU/Product sections with conditional:
```blade
@if($showSkuFields)
    {{-- PO Numbers, Product Selection, etc. --}}
@endif
```

#### 2. Booking Controller Validation
**Files:**
- `app/Http/Controllers/Admin/BookingController.php`
- `app/Http/Controllers/Customer/BookingController.php`

Update validation rules:
```php
public function store(Request $request)
{
    $customerId = $request->customer_id;
    $depotId = Slot::find($request->slot_id)->depot_id ?? null;

    $config = \App\Models\CustomerBookingConfig::getConfig($customerId, $depotId);

    $rules = [
        'customer_id' => 'required|exists:customers,id',
        'booking_type_id' => 'required|exists:booking_types,id',
        'slot_id' => 'required|exists:slots,id',
    ];

    // Only require PO data if config requires it
    if ($config['require_po_data']) {
        $rules['po_numbers'] = 'required|array|min:1';
        $rules['po_numbers.*.po_number'] = 'required|string';
    }

    $validated = $request->validate($rules);
    // ... create booking
}
```

---

## Task 2: Advanced Slot & Bay Logic

### Database Schema

#### Table: `customer_depot_time_windows`
```sql
- id
- customer_id (FK to customers)
- depot_id (FK to depots)
- allowed_start_time (time) - e.g., '08:00:00'
- allowed_end_time (time) - e.g., '17:00:00'
- days_of_week (JSON array) - e.g., [1,2,3,4,5] for Mon-Fri, null for all days
- is_active (boolean)
- timestamps
- UNIQUE(customer_id, depot_id)
```

#### Table: `customer_bay_assignments`
```sql
- id
- customer_id (FK to customers)
- tipping_bay_id (FK to tipping_bays)
- priority (integer) - Higher = preferred, 0 = allowed
- is_active (boolean)
- timestamps
- UNIQUE(customer_id, tipping_bay_id)
```

#### Table: `booking_type_equipment_requirements`
```sql
- id
- booking_type_id (FK to booking_types)
- required_equipment (JSON) - e.g., ["ramp", "forklift"]
- is_active (boolean)
- timestamps
```

### Models

#### `CustomerDepotTimeWindow`
**Key Methods:**
- `isTimeAllowed($customerId, $depotId, $slotTime)` - Check if time is within allowed window
- `getTimeWindow($customerId, $depotId)` - Get time window configuration

#### `CustomerBayAssignment`
**Key Methods:**
- `getAllowedBayIds($customerId, $depotId)` - Get list of allowed bay IDs (null = no restrictions)
- `getAvailableBaysForCustomer($customerId, $depotId, $requiredEquipment)` - Get available bays sorted by priority
- `hasRestrictions($customerId, $depotId)` - Check if customer has bay restrictions

#### `BookingTypeEquipmentRequirement`
**Key Methods:**
- `getRequiredEquipment($bookingTypeId)` - Get array of required equipment
- `hasRequirements($bookingTypeId)` - Check if booking type has equipment requirements

### Service: `SlotAvailabilityService`

**Location:** `app/Services/SlotAvailabilityService.php`

**Key Methods:**

1. **`isSlotAvailable($slot, $customerId, $bookingTypeId, $excludeBookingId = null)`**
   - Comprehensive availability check
   - Returns: `['available' => bool, 'errors' => array, 'duration_minutes' => int, 'extended_slots' => array]`

2. **`getAvailableSlots($depotId, $customerId, $bookingTypeId, $startDate = null, $endDate = null)`**
   - Get filtered list of available slots for customer

3. **`reserveExtendedSlots($booking, $primarySlot, $durationMinutes)`**
   - Reserve all slots occupied by a multi-slot booking

4. **`getBookingDuration($bookingTypeId, $depotId, $customerId)`**
   - Get booking duration considering customer-specific overrides

### Usage Examples

#### 1. Check if a slot is available
```php
use App\Services\SlotAvailabilityService;

$service = new SlotAvailabilityService();
$slot = Slot::find(10);

$check = $service->isSlotAvailable($slot, $customerId = 5, $bookingTypeId = 2);

if ($check['available']) {
    // Slot is available
    $duration = $check['duration_minutes'];
    $extendedSlots = $check['extended_slots']; // IDs of additional slots if booking spans multiple
} else {
    // Show errors
    foreach ($check['errors'] as $error) {
        echo $error;
    }
}
```

#### 2. Get all available slots for a customer
```php
$service = new SlotAvailabilityService();

$availableSlots = $service->getAvailableSlots(
    depotId: 2,
    customerId: 5,
    bookingTypeId: 2,
    startDate: now(),
    endDate: now()->addWeeks(2)
);

foreach ($availableSlots as $slot) {
    echo $slot->start_at->format('Y-m-d H:i');
}
```

#### 3. Create booking with extended slot reservation
```php
$service = new SlotAvailabilityService();
$slot = Slot::find(10);
$customer = Customer::find(5);
$bookingType = BookingType::find(2);

// Create booking
$booking = Booking::create([
    'slot_id' => $slot->id,
    'customer_id' => $customer->id,
    'booking_type_id' => $bookingType->id,
    // ... other fields
]);

// Reserve extended slots if needed
$duration = $bookingType->getDurationForCustomer($slot->depot_id, $customer->id);
$service->reserveExtendedSlots($booking, $slot, $duration);
```

#### 4. Configure time windows for a customer
```php
use App\Models\CustomerDepotTimeWindow;

// Customer can only book 8am-5pm Monday-Friday at this depot
CustomerDepotTimeWindow::create([
    'customer_id' => 5,
    'depot_id' => 2,
    'allowed_start_time' => '08:00:00',
    'allowed_end_time' => '17:00:00',
    'days_of_week' => [1, 2, 3, 4, 5], // Monday=1, Friday=5
    'is_active' => true,
]);

// Check if a time is allowed
$slotTime = Carbon::parse('2025-10-15 14:30:00'); // Wednesday 2:30pm
$isAllowed = CustomerDepotTimeWindow::isTimeAllowed(5, 2, $slotTime);
// Returns: true
```

#### 5. Configure bay assignments for a customer
```php
use App\Models\CustomerBayAssignment;

// Customer can use bays 1, 2, 3 with priority
CustomerBayAssignment::create([
    'customer_id' => 5,
    'tipping_bay_id' => 1,
    'priority' => 10,  // High priority - preferred bay
    'is_active' => true,
]);

CustomerBayAssignment::create([
    'customer_id' => 5,
    'tipping_bay_id' => 2,
    'priority' => 5,   // Medium priority
    'is_active' => true,
]);

CustomerBayAssignment::create([
    'customer_id' => 5,
    'tipping_bay_id' => 3,
    'priority' => 0,   // Allowed but not preferred
    'is_active' => true,
]);

// Get available bays sorted by priority
$bays = CustomerBayAssignment::getAvailableBaysForCustomer(
    customerId: 5,
    depotId: 2,
    requiredEquipment: ['ramp']
);
// Returns bays in priority order: Bay 1, Bay 2, Bay 3
```

#### 6. Configure equipment requirements for booking type
```php
use App\Models\BookingTypeEquipmentRequirement;

// This booking type requires a ramp
BookingTypeEquipmentRequirement::create([
    'booking_type_id' => 2,
    'required_equipment' => ['ramp'],
    'is_active' => true,
]);

// Check what equipment is required
$equipment = BookingTypeEquipmentRequirement::getRequiredEquipment(2);
// Returns: ['ramp']
```

#### 7. Set equipment on a bay
```php
use App\Models\TippingBay;

$bay = TippingBay::find(1);
$bay->equipment = ['ramp', 'forklift'];
$bay->save();

// The equipment field is already JSON in the tipping_bays table
// No migration needed - just use it!
```

---

## Integration with Booking Controllers

### Admin Booking Controller

**File:** `app/Http/Controllers/Admin/BookingController.php`

```php
use App\Services\SlotAvailabilityService;
use App\Models\CustomerBookingConfig;
use App\Models\CustomerDepotTimeWindow;

class BookingController extends Controller
{
    protected SlotAvailabilityService $slotService;

    public function __construct(SlotAvailabilityService $slotService)
    {
        $this->slotService = $slotService;
    }

    public function create()
    {
        $slots = Slot::with('depot')
            ->where('start_at', '>', now())
            ->where('is_blocked', false)
            ->orderBy('start_at')
            ->get();

        $customers = Customer::all();
        $types = BookingType::all();

        return view('admin.bookings.create', compact('slots', 'customers', 'types'));
    }

    public function store(Request $request)
    {
        $slot = Slot::findOrFail($request->slot_id);

        // Get SKU configuration
        $config = CustomerBookingConfig::getConfig($request->customer_id, $slot->depot_id);

        // Build validation rules
        $rules = [
            'customer_id' => 'required|exists:customers,id',
            'booking_type_id' => 'required|exists:booking_types,id',
            'slot_id' => 'required|exists:slots,id',
        ];

        if ($config['require_po_data']) {
            $rules['po_numbers'] = 'required|array|min:1';
            $rules['po_numbers.*.po_number'] = 'required|string';
        }

        $validated = $request->validate($rules);

        // Check slot availability with all rules
        $availability = $this->slotService->isSlotAvailable(
            $slot,
            $request->customer_id,
            $request->booking_type_id
        );

        if (!$availability['available']) {
            return back()->withErrors([
                'slot_id' => 'Slot not available: ' . implode(', ', $availability['errors'])
            ])->withInput();
        }

        // Create booking
        $booking = Booking::create([
            'slot_id' => $slot->id,
            'customer_id' => $request->customer_id,
            'booking_type_id' => $request->booking_type_id,
            // ... other fields
        ]);

        // Reserve extended slots
        $duration = $this->slotService->getBookingDuration(
            $request->booking_type_id,
            $slot->depot_id,
            $request->customer_id
        );

        $this->slotService->reserveExtendedSlots($booking, $slot, $duration);

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Booking created successfully');
    }
}
```

---

## API Endpoints (Optional)

### Get Available Slots for Customer
```php
// Route: GET /api/slots/available
public function getAvailableSlots(Request $request, SlotAvailabilityService $service)
{
    $validated = $request->validate([
        'depot_id' => 'required|exists:depots,id',
        'customer_id' => 'required|exists:customers,id',
        'booking_type_id' => 'required|exists:booking_types,id',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date',
    ]);

    $slots = $service->getAvailableSlots(
        $validated['depot_id'],
        $validated['customer_id'],
        $validated['booking_type_id'],
        $validated['start_date'] ?? null,
        $validated['end_date'] ?? null
    );

    return response()->json([
        'slots' => $slots->map(fn($slot) => [
            'id' => $slot->id,
            'start_at' => $slot->start_at->toISOString(),
            'end_at' => $slot->end_at->toISOString(),
            'capacity' => $slot->capacity,
            'remaining_capacity' => $slot->remainingCapacity(),
        ])
    ]);
}
```

### Check Slot Availability
```php
// Route: POST /api/slots/{slot}/check-availability
public function checkAvailability(Slot $slot, Request $request, SlotAvailabilityService $service)
{
    $validated = $request->validate([
        'customer_id' => 'required|exists:customers,id',
        'booking_type_id' => 'required|exists:booking_types,id',
    ]);

    $check = $service->isSlotAvailable(
        $slot,
        $validated['customer_id'],
        $validated['booking_type_id']
    );

    return response()->json($check);
}
```

---

## Testing Examples

### Test SKU Configuration
```php
// tests/Feature/CustomerBookingConfigTest.php
public function test_sku_fields_disabled_for_customer_at_depot()
{
    $customer = Customer::factory()->create();
    $depot = Depot::factory()->create();

    // Disable SKU fields for this customer at this depot
    CustomerBookingConfig::create([
        'customer_id' => $customer->id,
        'depot_id' => $depot->id,
        'sku_fields_enabled' => false,
        'require_po_data' => false,
    ]);

    $this->assertFalse(
        CustomerBookingConfig::skuFieldsEnabled($customer->id, $depot->id)
    );
}

public function test_global_customer_config_applies_to_all_depots()
{
    $customer = Customer::factory()->create();
    $depot1 = Depot::factory()->create();
    $depot2 = Depot::factory()->create();

    // Global config
    CustomerBookingConfig::create([
        'customer_id' => $customer->id,
        'depot_id' => null,
        'sku_fields_enabled' => false,
    ]);

    $this->assertFalse(CustomerBookingConfig::skuFieldsEnabled($customer->id, $depot1->id));
    $this->assertFalse(CustomerBookingConfig::skuFieldsEnabled($customer->id, $depot2->id));
}
```

### Test Time Window Restrictions
```php
public function test_customer_cannot_book_outside_time_window()
{
    $customer = Customer::factory()->create();
    $depot = Depot::factory()->create();

    CustomerDepotTimeWindow::create([
        'customer_id' => $customer->id,
        'depot_id' => $depot->id,
        'allowed_start_time' => '08:00:00',
        'allowed_end_time' => '17:00:00',
        'is_active' => true,
    ]);

    $earlySlot = Carbon::parse('2025-10-15 06:00:00');
    $validSlot = Carbon::parse('2025-10-15 14:00:00');
    $lateSlot = Carbon::parse('2025-10-15 20:00:00');

    $this->assertFalse(CustomerDepotTimeWindow::isTimeAllowed($customer->id, $depot->id, $earlySlot));
    $this->assertTrue(CustomerDepotTimeWindow::isTimeAllowed($customer->id, $depot->id, $validSlot));
    $this->assertFalse(CustomerDepotTimeWindow::isTimeAllowed($customer->id, $depot->id, $lateSlot));
}
```

### Test Bay Assignment Priority
```php
public function test_bays_returned_in_priority_order()
{
    $customer = Customer::factory()->create();
    $depot = Depot::factory()->create();
    $bay1 = TippingBay::factory()->create(['depot_id' => $depot->id, 'is_occupied' => false]);
    $bay2 = TippingBay::factory()->create(['depot_id' => $depot->id, 'is_occupied' => false]);

    // Bay 2 has higher priority
    CustomerBayAssignment::create(['customer_id' => $customer->id, 'tipping_bay_id' => $bay1->id, 'priority' => 5]);
    CustomerBayAssignment::create(['customer_id' => $customer->id, 'tipping_bay_id' => $bay2->id, 'priority' => 10]);

    $bays = CustomerBayAssignment::getAvailableBaysForCustomer($customer->id, $depot->id);

    $this->assertEquals($bay2->id, $bays->first()->id);
}
```

---

## Admin UI Suggestions

### Customer Configuration Page

Create a page to manage customer configurations:

**Route:** `/admin/customers/{customer}/config`

**Features:**
- Toggle SKU fields on/off per depot
- Set time windows per depot
- Assign bays with priority sliders
- Preview booking form as customer would see it

### Booking Type Configuration

Add equipment requirements section:

**Route:** `/admin/booking-types/{bookingType}/edit`

**Features:**
- Multi-select for required equipment (ramp, forklift, etc.)
- Show which bays have this equipment
- Warn if configuration makes bookings impossible

### Bay Management

Enhance bay editor:

**Route:** `/admin/tipping-bays/{bay}/edit`

**Features:**
- Equipment checklist
- List of customers assigned to this bay
- Availability calendar

---

## Migration Plan

### Phase 1: Foundation (Completed ✅)
- ✅ Database migrations created
- ✅ Models created with relationships
- ✅ Service class implemented
- ✅ Migrations run successfully

### Phase 2: Controller Integration (Next Steps)
- Update booking controllers with new validation logic
- Integrate `SlotAvailabilityService` into booking creation
- Update booking forms with conditional SKU fields

### Phase 3: Admin UI (Future)
- Create customer configuration pages
- Add time window management UI
- Add bay assignment management UI
- Add equipment requirements UI

### Phase 4: Testing & Rollout
- Write comprehensive tests
- Test with pilot customers
- Monitor for edge cases
- Gradual rollout to all customers

---

## FAQ

### Q: What happens if no configuration exists for a customer?
A: Default behavior applies - all fields enabled, no restrictions.

### Q: Can I disable SKU fields globally?
A: Yes, create a config with `customer_id = null` and `depot_id = null` (though this requires a schema change - currently requires customer_id).

### Q: How do I know which bays have which equipment?
A: Check the `equipment` JSON field on `tipping_bays` table. Use `CustomerBayAssignment::getAvailableBaysForCustomer()` with required equipment.

### Q: What if a booking spans multiple slots?
A: The `SlotAvailabilityService` automatically handles this. It checks all extended slots and reserves them via `reserveExtendedSlots()`.

### Q: Can a customer have different rules at different depots?
A: Yes! All configurations support per-depot settings. The system checks depot-specific first, then falls back to customer-global, then defaults.

### Q: What if a customer's time window conflicts with depot hours?
A: Customer time windows are enforced regardless of depot hours. Configure customer windows within depot operating hours.

---

## Summary

This implementation provides:

✅ **Flexible SKU Configuration**
- Per-customer, per-depot control
- Graceful fallbacks
- Easy to disable for specific customers

✅ **Advanced Slot Logic**
- Time window restrictions
- Duration-based availability checking
- Extended slot reservation for long bookings

✅ **Smart Bay Assignment**
- Customer-specific bay restrictions
- Priority-based bay selection
- Equipment requirement matching

✅ **Backward Compatible**
- No breaking changes
- Defaults maintain current behavior
- Opt-in feature adoption

---

## Next Steps

1. **Update Controllers:** Integrate `SlotAvailabilityService` and `CustomerBookingConfig` into booking controllers
2. **Update Views:** Add conditional rendering for SKU fields in booking forms
3. **Create Admin UI:** Build configuration pages for managing customer settings
4. **Write Tests:** Comprehensive test coverage for all new features
5. **Documentation:** Add inline code documentation and API docs
6. **Gradual Rollout:** Start with pilot customers, monitor, and expand

**Files to Update Next:**
- `app/Http/Controllers/Admin/BookingController.php`
- `app/Http/Controllers/Customer/BookingController.php`
- `resources/views/admin/bookings/_form.blade.php`
- `resources/views/customer/bookings/create.blade.php`
