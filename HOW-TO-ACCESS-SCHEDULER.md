# How to Access the Scheduler Admin Panel

## Quick Access

**URL:** `http://test.test/admin/scheduler` (or your domain + `/admin/scheduler`)

---

## Step-by-Step Guide

### Step 1: Log In
Log in to your application as an admin user (or any user with the `settings.manage` function)

### Step 2: Find the Menu
Look at the top navigation bar. You'll see several menu items:
- 📊 Dashboard
- 📋 Bookings
- 🚛 Operations
- 🏭 Outbound (if enabled)
- **⚙️ Configuration** ← Click this one!
- 👥 People
- 📊 Reports

### Step 3: Open Configuration Menu
Click on **⚙️ Configuration** in the navigation

A dropdown menu appears with sections:
- **Locations** (Depots, Products, etc.)
- **Booking Configuration** (Slot Templates, Booking Types, etc.)
- **Equipment Types** (Pallet Types, Trailer Types)
- **System** ← This section!

### Step 4: Click Scheduler
Under the **System** section, you'll see:
- **⏰ Scheduler** ← Click this!
  - "Manage scheduled tasks"
- 🛠️ System Settings
- 🏭 Factory Tipping Targets

### Step 5: You're There!
You should now see the Scheduler Management page with:

```
┌─────────────────────────────────────────────────────────┐
│ ⏰ Scheduler Management                                  │
│                              🔄 Refresh   ▶️ Run All     │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│ 🔴 Scheduler Daemon Status                              │
│                                                          │
│ ● Running (PID: 12345)                                  │
│ The scheduler daemon is checking for tasks every 60s    │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│ 📅 Scheduled Tasks (4 tasks)                            │
│                                                          │
│ ┌────────────────────────────────────────────────────┐ │
│ │ Auto-generate slots using configured method        │ │
│ │ php artisan slots:generate...        ▶️ Run Now   │ │
│ │ Schedule: 15 0 * * *  Next: 2024-... Timezone: EU  │ │
│ │ Overlapping: ❌ Prevented              📄 View Log │ │
│ └────────────────────────────────────────────────────┘ │
│                                                          │
│ [3 more tasks shown similarly...]                       │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│ 📖 Cron Expression Reference                            │
│ * * * * * = Every minute                                │
│ */15 * * * * = Every 15 minutes                         │
│ [more examples...]                                      │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│ 📋 Recent Log Activity                                  │
│                                                          │
│ scheduler.log         Updated: 2024-01-15 10:30:45     │
│ [log contents...]                            View Full  │
│                                                          │
│ [more log files...]                                     │
└─────────────────────────────────────────────────────────┘
```

---

## What You Can Do

### ✅ Monitor Status
- See if the scheduler daemon is running
- Green dot = running ✅
- Red dot = stopped ⚠️

### ✅ View All Tasks
- See what tasks are scheduled
- When they run next
- What command they execute

### ✅ Run Tasks Manually
- Click **▶️ Run Now** on any task
- Click **▶️ Run All Tasks Now** in header
- See execution output immediately

### ✅ View Logs
- Click **📄 View Log** under any task
- See last 500 lines
- Check for errors or success messages

### ✅ Understand Schedules
- Use the cron expression reference
- See timezone for each task
- Know exactly when tasks will run

---

## Troubleshooting Access

### Can't See Configuration Menu?
**Cause:** Your user doesn't have the right permissions

**Fix:** You need the `settings.manage` function. Ask your admin to grant you this permission.

### Can't See Scheduler Option?
**Cause:** Routes not loaded or cache issue

**Fix:**
```bash
php artisan route:clear
php artisan config:clear
php artisan view:clear
```

### Getting 404 Error?
**Cause:** Routes not registered

**Check:**
```bash
php artisan route:list | grep scheduler
```

You should see 5 routes. If not, check that `routes/web.php` has the scheduler routes.

### Page Loads But Shows Error?
**Cause:** Missing dependencies

**Fix:**
```bash
composer dump-autoload
```

---

## Mobile Access

The interface is responsive and works on mobile devices:
- Scroll down to see all sections
- Tap buttons to run tasks
- View logs in modal popup
- All features available

---

## Direct Links

If you know your domain, you can bookmark these:

- **Main Scheduler:** `http://your-domain/admin/scheduler`
- **Run All Tasks:** POST to `http://your-domain/admin/scheduler/run`
- **Check Status:** `http://your-domain/admin/scheduler/status`

Replace `your-domain` with:
- Local: `test.test` or `localhost`
- Production: `your-actual-domain.com`

---

## First Time Setup

### 1. Start the Scheduler Daemon
```bash
cd /Users/londo/Herd/test
bash start-scheduler.sh
```

### 2. Open Admin Panel
Navigate to: `http://test.test/admin/scheduler`

### 3. Verify It's Running
- Check for green dot next to "Running"
- See PID number
- All tasks should show next run times

### 4. Test It
Click **▶️ Run Now** on "Auto-Release Slots" to test

---

## Need Help?

**Documentation:**
- `SCHEDULER-COMPLETE-GUIDE.md` - Everything about the scheduler
- `SCHEDULER-ADMIN-PANEL.md` - Admin panel features
- `SCHEDULER-SETUP.md` - Full setup instructions

**Command Line:**
```bash
bash status-scheduler.sh        # Check status
bash start-scheduler.sh         # Start daemon
tail -f storage/logs/scheduler.log  # View logs
```

**Admin Panel:**
- Go to `/admin/scheduler`
- Click "View Log" on any task
- Click "Run Now" to test

---

## Quick Reference Card

| Want to... | Do this... |
|------------|------------|
| Access scheduler | Configuration → ⏰ Scheduler |
| Run a task | Click ▶️ Run Now |
| Run all tasks | Click ▶️ Run All Tasks Now |
| View logs | Click 📄 View Log |
| Check status | Look at top section (green/red dot) |
| Refresh | Click 🔄 Refresh Status |
| Understand cron | Check "Cron Expression Reference" |
| Start daemon | `bash start-scheduler.sh` |
| Check health | `bash scheduler-health-check.sh` |

---

**You're all set! Enjoy your new scheduler management system! 🎉**
