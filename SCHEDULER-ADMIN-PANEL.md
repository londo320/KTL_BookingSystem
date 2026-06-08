# Scheduler Admin Panel

## Overview

A comprehensive web interface for managing and monitoring Laravel scheduled tasks directly from your admin panel.

## Access

**URL:** `/admin/scheduler`

**Navigation:** Configuration menu → ⏰ Scheduler

**Required Permission:** `settings.manage` function

## Features

### 1. **Daemon Status Monitor**
- Real-time status of the scheduler daemon
- Shows PID (Process ID) if running
- Visual indicator (green = running, red = stopped)
- Instructions for starting if not running
- Auto-refreshes every 30 seconds

### 2. **Scheduled Tasks Overview**
Displays all scheduled tasks with:
- **Task Description** - Human-readable name
- **Command** - Actual artisan command
- **Schedule** - Cron expression (e.g., `*/15 * * * *`)
- **Next Run** - When the task will execute next
- **Timezone** - Task timezone (usually Europe/London)
- **Overlapping** - Whether the task prevents concurrent runs
- **Run Now** button - Execute any task immediately

### 3. **Current Scheduled Tasks**
| Task | Schedule | Description |
|------|----------|-------------|
| **Generate Slots** | `15 0 * * *` | Daily at 00:15 - Generates slots for next 30 days |
| **Auto-Release Slots** | `*/15 * * * *` | Every 15 min - Releases slots per rules |
| **Bay Occupancy Sync** | `*/30 * * * *` | Every 30 min - Updates bay status |
| **Cleanup Bookings** | `*/15 * * * *` | Every 15 min - Removes incomplete bookings |

### 4. **Manual Task Execution**
- **Run All Tasks Now** - Executes the entire scheduler (all due tasks)
- **Run Individual Task** - Click "Run Now" on any specific task
- View command output directly in the browser
- Success/error messages displayed

### 5. **Log Viewer**
- View recent log entries for each task
- Click "View Log" to see full log file in modal
- Shows last 500 lines of each log file
- Logs available:
  - `scheduler.log` - Main scheduler activity
  - `slots_generate.log` - Slot generation output
  - `auto_release_slots.log` - Slot release activity
  - `bay_sync.log` - Bay occupancy sync
  - `booking_cleanup.log` - Booking cleanup activity

### 6. **Cron Expression Reference**
Built-in reference guide for understanding cron expressions:
- `* * * * *` = Every minute
- `*/15 * * * *` = Every 15 minutes
- `*/30 * * * *` = Every 30 minutes
- `0 * * * *` = Every hour
- `0 0 * * *` = Daily at midnight
- `15 0 * * *` = Daily at 00:15

## Usage Examples

### View Scheduler Status
1. Log in as admin
2. Click **Configuration** in the navigation
3. Click **⏰ Scheduler**
4. Check the daemon status at the top

### Run a Task Manually
1. Navigate to the Scheduler page
2. Find the task you want to run
3. Click the green **▶️ Run Now** button
4. View the output in the success message

### View Task Logs
1. Navigate to the Scheduler page
2. Scroll to the task you want to check
3. Click **📄 View Log** under the task
4. Modal opens with the full log file
5. Click X to close

### Run All Tasks Immediately
1. Navigate to the Scheduler page
2. Click **▶️ Run All Tasks Now** in the header
3. All scheduled tasks that are due will execute
4. View the output

## Technical Details

### Routes
```php
GET  /admin/scheduler              - Main interface
POST /admin/scheduler/run          - Run all scheduled tasks
POST /admin/scheduler/run-command  - Run specific command
GET  /admin/scheduler/logs         - Get log file contents (AJAX)
GET  /admin/scheduler/status       - Check daemon status (AJAX)
```

### Controller
`App\Http\Controllers\Admin\SchedulerController`

### View
`resources/views/admin/scheduler/index.blade.php`

### Auto-Refresh
- Page checks daemon status every 30 seconds automatically
- Manual refresh available via "🔄 Refresh Status" button

## Troubleshooting

### "Scheduler Not Running" Warning
**Cause:** The scheduler daemon is not active

**Fix:**
- Local: Run `bash start-scheduler.sh`
- Production: Enable systemd service
- Docker: Ensure scheduler service is in Procfile
- See `SCHEDULER-SETUP.md` for full instructions

### Can't See Tasks
**Cause:** Tasks are defined in `routes/console.php`

**Fix:**
- Check `routes/console.php` has scheduled tasks
- Run `php artisan schedule:list` to verify

### "Run Now" Doesn't Work
**Cause:** Permissions or command not found

**Check:**
- Laravel logs in `storage/logs/laravel.log`
- Command name is correct
- User has permission to run artisan commands

### Logs Not Showing
**Cause:** Log files don't exist yet

**Fix:**
- Run the tasks at least once
- Check `storage/logs/` directory permissions
- Logs are created when tasks run

## Benefits

✅ **No Command Line Required** - Manage everything from the browser
✅ **Real-Time Monitoring** - See status and logs instantly
✅ **Manual Override** - Run any task on-demand
✅ **Better Visibility** - All scheduled tasks in one place
✅ **Troubleshooting** - View logs without SSH access
✅ **User-Friendly** - Clear descriptions and instructions
✅ **Multi-Environment** - Works in dev, staging, production

## Security

- Protected by authentication middleware
- Requires `settings.manage` function permission
- Only admin, depot-admin, site-admin, warehouse roles have access
- Log file access is sanitized to prevent path traversal
- Command execution is controlled and validated

## Future Enhancements (Optional)

- [ ] Enable/disable individual tasks
- [ ] Edit task schedules via UI
- [ ] Email notifications for failed tasks
- [ ] Task execution history with timestamps
- [ ] Download log files
- [ ] Real-time log streaming
- [ ] Task dependencies visualization

## Related Documentation

- `SCHEDULER-SETUP.md` - Full scheduler setup instructions
- `SCHEDULER-QUICKSTART.md` - Quick reference guide
- `FIXES-APPLIED.md` - Recent fixes and changes
- `routes/console.php` - Where tasks are defined
