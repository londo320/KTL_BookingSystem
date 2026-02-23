# Slot Generation: Templates vs Bay Hours

## Your Question

> Bay 1 operates Mon-Fri 06:00-13:00 and 16:00-20:00 (with lunch gap 13:00-16:00).
> Do I use site templates or bay operating hours?

---

## 🎯 ANSWER: Use SITE TEMPLATES

**Reason:** Bay model **cannot handle time gaps**. It only has ONE `operational_start` and ONE `operational_end`.

---

## ✅ RECOMMENDED APPROACH: Site-Wide Templates

### How It Works

**SlotTemplate defines site operating hours with gaps:**

```php
// Morning shift: 06:00-13:00
SlotTemplate::create(['depot_id' => 3, 'day_of_week' => 1, 'start_time' => '06:00', 'capacity' => 4]);
SlotTemplate::create(['depot_id' => 3, 'day_of_week' => 1, 'start_time' => '07:00', 'capacity' => 4]);
SlotTemplate::create(['depot_id' => 3, 'day_of_week' => 1, 'start_time' => '08:00', 'capacity' => 4]);
SlotTemplate::create(['depot_id' => 3, 'day_of_week' => 1, 'start_time' => '09:00', 'capacity' => 4]);
SlotTemplate::create(['depot_id' => 3, 'day_of_week' => 1, 'start_time' => '10:00', 'capacity' => 4]);
SlotTemplate::create(['depot_id' => 3, 'day_of_week' => 1, 'start_time' => '11:00', 'capacity' => 4]);
SlotTemplate::create(['depot_id' => 3, 'day_of_week' => 1, 'start_time' => '12:00', 'capacity' => 4]);
// LUNCH GAP: 13:00-15:00 (NO TEMPLATES)
// Evening shift: 16:00-20:00
SlotTemplate::create(['depot_id' => 3, 'day_of_week' => 1, 'start_time' => '16:00', 'capacity' => 4]);
SlotTemplate::create(['depot_id' => 3, 'day_of_week' => 1, 'start_time' => '17:00', 'capacity' => 4]);
SlotTemplate::create(['depot_id' => 3, 'day_of_week' => 1, 'start_time' => '18:00', 'capacity' => 4]);
SlotTemplate::create(['depot_id' => 3, 'day_of_week' => 1, 'start_time' => '19:00', 'capacity' => 4]);
```

**Result:**
- ✅ Slots generated: 06:00-12:00, 16:00-19:00
- ✅ Gap respected: No slots at 13:00-15:00
- ✅ All bays follow schedule
- ✅ Simple to manage

### Benefits

1. **Easy Time Gaps**
   - Just don't create templates for lunch hours
   - No slots = no bookings possible

2. **Site-Wide Control**
   - All bays follow the same schedule
   - One place to configure

3. **Simple Capacity**
   - Capacity = number of active bays
   - Example: 4 active bays = capacity 4

4. **Easy to Modify**
   - Want to extend evening? Add more templates
   - Want to close Fridays early? Delete/modify templates

---

## ⚠️ Alternative: Bay Operating Hours (Not Recommended for Gaps)

### The Problem

Bay model structure:
```php
$bay = [
    'operational_start' => '06:00',  // ONE start time
    'operational_end' => '20:00',    // ONE end time
];
```

**Cannot define:**
- 06:00-13:00 AND 16:00-20:00 (with gap)
- Would generate slots continuously 06:00-20:00 (including lunch!)

### To Support Gaps in Bay Hours, You Would Need:

**Option 1: Multiple Time Ranges (Complex)**
```php
$bay = [
    'operational_periods' => [
        ['start' => '06:00', 'end' => '13:00'],  // Morning
        ['start' => '16:00', 'end' => '20:00'],  // Evening
    ]
];
```
- Requires database migration
- More complex code
- Harder to manage

**Option 2: Two Bay Records (Hacky)**
```php
// Bay 1 - Morning
$bay1a = ['operational_start' => '06:00', 'operational_end' => '13:00'];
// Bay 1 - Evening
$bay1b = ['operational_start' => '16:00', 'operational_end' => '20:00'];
```
- Confusing (two records for one physical bay)
- Management nightmare
- Not recommended

---

## 🎯 RECOMMENDED SETUP

### Step 1: Keep Site Templates

Configure slots for your site hours:
```bash
php artisan tinker
```

```php
// Clear existing templates (if needed)
SlotTemplate::where('depot_id', 3)->delete();

// Define your schedule (Mon-Fri)
$days = [1, 2, 3, 4, 5]; // Monday to Friday
$morningHours = ['06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00'];
$eveningHours = ['16:00', '17:00', '18:00', '19:00'];
$capacity = 4; // Number of active bays

foreach ($days as $day) {
    // Morning shift
    foreach ($morningHours as $hour) {
        SlotTemplate::create([
            'depot_id' => 3,
            'day_of_week' => $day,
            'start_time' => $hour,
            'duration_minutes' => 60,
            'capacity' => $capacity,
        ]);
    }

    // Evening shift
    foreach ($eveningHours as $hour) {
        SlotTemplate::create([
            'depot_id' => 3,
            'day_of_week' => $day,
            'start_time' => $hour,
            'duration_minutes' => 60,
            'capacity' => $capacity,
        ]);
    }
}
```

### Step 2: Use Original Slot Generation Command

```bash
# Use the depot-wide command (handles templates correctly)
php artisan slots:generate --days=14
```

**NOT:**
```bash
# Don't use bay-specific command (can't handle gaps)
php artisan slots:generate-by-bay --days=14
```

### Step 3: Update Cron Schedule

Edit `routes/console.php`:
```php
// CHANGE BACK TO:
Schedule::command('slots:generate', ['--days' => 14])
    ->dailyAt('00:15')
    ->withoutOverlapping()
    ->timezone('Europe/London')
    ->appendOutputTo(storage_path('logs/slots_generate.log'))
    ->description('Auto-generate slots from templates for the next 14 days');
```

---

## 🔀 When to Use Each Approach

### Use SITE TEMPLATES (Depot-Wide) When:

✅ All bays have same operating hours
✅ You need time gaps (lunch, breaks)
✅ Simple capacity management
✅ Site operates as one unit

**Example:** Manufacturing site where all bays open 06:00-13:00, break 13:00-16:00, then 16:00-20:00

### Use BAY HOURS (Bay-Specific) When:

✅ Different bays have DIFFERENT hours
✅ No time gaps needed
✅ More granular control required

**Example:**
- Bay 1: 08:00-17:00 (Mon-Fri)
- Bay 2: 06:00-22:00 (Mon-Sat)
- Bay 3: 24/7

---

## 📊 Comparison

| Feature | Site Templates | Bay Hours |
|---------|---------------|-----------|
| Time gaps (lunch) | ✅ Easy | ❌ Cannot handle |
| Different hours per bay | ❌ No | ✅ Yes |
| Database records | Few | Many |
| Management complexity | Simple | Complex |
| Capacity model | Depot-wide | Per bay |

---

## 🎯 MY RECOMMENDATION FOR YOU

**Use SITE TEMPLATES** because:

1. ✅ You have time gaps (06:00-13:00, 16:00-20:00)
2. ✅ All bays likely follow same schedule
3. ✅ Simpler to manage
4. ✅ Easier to adjust

**Configuration:**
- Morning: 06:00-13:00 (7 hours)
- Lunch Gap: 13:00-16:00 (3 hours, no slots)
- Evening: 16:00-20:00 (4 hours)
- Days: Mon-Fri
- Capacity: 4 (or however many active bays)

---

## 🛠️ Implementation

### Option A: Manual Template Creation

```php
php artisan tinker

$depot = Depot::where('name', 'Wimblington')->first();
$days = [1, 2, 3, 4, 5]; // Mon-Fri

// Clear existing
SlotTemplate::where('depot_id', $depot->id)->delete();

// Morning shift: 06:00-12:00
for ($hour = 6; $hour <= 12; $hour++) {
    foreach ($days as $day) {
        SlotTemplate::create([
            'depot_id' => $depot->id,
            'day_of_week' => $day,
            'start_time' => sprintf('%02d:00', $hour),
            'duration_minutes' => 60,
            'capacity' => 4,
        ]);
    }
}

// Evening shift: 16:00-19:00
for ($hour = 16; $hour <= 19; $hour++) {
    foreach ($days as $day) {
        SlotTemplate::create([
            'depot_id' => $depot->id,
            'day_of_week' => $day,
            'start_time' => sprintf('%02d:00', $hour),
            'duration_minutes' => 60,
            'capacity' => 4,
        ]);
    }
}
```

### Option B: Seeder (Recommended)

Create `database/seeders/WimblingtonSlotTemplateSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\Depot;
use App\Models\SlotTemplate;
use Illuminate\Database\Seeder;

class WimblingtonSlotTemplateSeeder extends Seeder
{
    public function run()
    {
        $depot = Depot::where('name', 'Wimblington')->first();
        $days = [1, 2, 3, 4, 5]; // Mon-Fri
        $capacity = 4;

        // Clear existing
        SlotTemplate::where('depot_id', $depot->id)->delete();

        // Morning shift: 06:00-12:00
        for ($hour = 6; $hour <= 12; $hour++) {
            foreach ($days as $day) {
                SlotTemplate::create([
                    'depot_id' => $depot->id,
                    'day_of_week' => $day,
                    'start_time' => sprintf('%02d:00', $hour),
                    'duration_minutes' => 60,
                    'capacity' => $capacity,
                ]);
            }
        }

        // LUNCH GAP: 13:00-15:00 (no templates)

        // Evening shift: 16:00-19:00
        for ($hour = 16; $hour <= 19; $hour++) {
            foreach ($days as $day) {
                SlotTemplate::create([
                    'depot_id' => $depot->id,
                    'day_of_week' => $day,
                    'start_time' => sprintf('%02d:00', $hour),
                    'duration_minutes' => 60,
                    'capacity' => $capacity,
                ]);
            }
        }

        $this->command->info("Created slot templates for {$depot->name}");
        $this->command->info("Morning: 06:00-12:00, Evening: 16:00-19:00, Mon-Fri");
    }
}
```

Run: `php artisan db:seed --class=WimblingtonSlotTemplateSeeder`

---

## ✅ FINAL ANSWER

**Use SITE TEMPLATES with time gaps.**

Your schedule:
- **06:00-13:00** (morning): 7 templates
- **13:00-16:00** (lunch): NO templates = no slots
- **16:00-20:00** (evening): 4 templates
- **Days:** Mon-Fri only
- **Capacity:** 4 (adjust based on active bays)

**Command to use:**
```bash
php artisan slots:generate --days=14
```

**NOT:**
```bash
php artisan slots:generate-by-bay --days=14
```

This is simpler, handles gaps perfectly, and matches your site's needs! 🎯
