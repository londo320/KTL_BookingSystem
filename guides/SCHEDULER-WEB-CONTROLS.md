# Scheduler Web Controls - New Feature! 🎉

## Overview

You can now **start, stop, and restart** the scheduler daemon directly from the admin panel! No more command line needed.

---

## What's New

### Control Buttons in Admin Panel

When you visit `/admin/scheduler`, you'll see control buttons based on the scheduler status:

#### When Scheduler is Running ✅
```
┌─────────────────────────────────────────────┐
│ 🔴 Scheduler Daemon Status                  │
│                         [⏹️ Stop] [🔄 Restart]│
│                                             │
│ ● Running (PID: 12345)                      │
│ The scheduler daemon is checking...         │
└─────────────────────────────────────────────┘
```

**Available Actions:**
- **⏹️ Stop** - Stops the scheduler daemon (with confirmation)
- **🔄 Restart** - Restarts the daemon (useful after config changes)

#### When Scheduler is Stopped ⚠️
```
┌─────────────────────────────────────────────┐
│ 🔴 Scheduler Daemon Status                  │
│                             [▶️ Start Daemon]│
│                                             │
│ ● Not Running                               │
│ Click the "Start Daemon" button...         │
│                                             │
│ ⚠️ Scheduler Not Running                    │
│ Quick Start: Click [▶️ Start Daemon] above  │
└─────────────────────────────────────────────┘
```

**Available Actions:**
- **▶️ Start Daemon** - Starts the scheduler daemon immediately

---

## How to Use

### Start the Scheduler
1. Go to `/admin/scheduler`
2. If scheduler is not running, you'll see a red dot and yellow warning
3. Click the green **▶️ Start Daemon** button
4. Wait 2-3 seconds
5. Page refreshes and shows green dot with "Running (PID: xxxxx)"
6. Success message appears confirming startup

### Stop the Scheduler
1. Go to `/admin/scheduler`
2. If scheduler is running, you'll see **⏹️ Stop** button
3. Click **⏹️ Stop**
4. Confirm the action in the popup dialog
5. Scheduler stops gracefully
6. Red dot appears showing "Not Running"
7. Success message confirms shutdown

### Restart the Scheduler
1. Go to `/admin/scheduler`
2. If scheduler is running, you'll see **🔄 Restart** button
3. Click **🔄 Restart**
4. Scheduler stops and starts automatically
5. New PID assigned
6. Success message shows new PID
7. No downtime - tasks resume immediately

---

## Use Cases

### Why Start from Web?

**Before:**
```bash
ssh into server
cd /path/to/project
bash start-scheduler.sh
exit
```

**Now:**
```
Just click ▶️ Start Daemon button!
```

### When to Use Each Control

| Button | When to Use |
|--------|-------------|
| **▶️ Start** | First time setup, after server restart, or if scheduler crashed |
| **⏹️ Stop** | Before maintenance, to save resources, or troubleshooting |
| **🔄 Restart** | After changing schedule config, if tasks stuck, or routine refresh |

---

## What Happens Behind the Scenes

### Start
```php
1. Checks if already running (prevents duplicates)
2. Removes stale PID file if found
3. Runs: php artisan scheduler:run --daemon --interval=60
4. Logs output to storage/logs/scheduler.log
5. Saves PID to storage/scheduler.pid
6. Returns success with PID
```

### Stop
```php
1. Checks PID file exists
2. Verifies process is actually running
3. Sends graceful kill signal (SIGTERM)
4. Waits 1 second
5. Force kills if still running (SIGKILL)
6. Removes PID file
7. Returns success
```

### Restart
```php
1. Stops existing process (if running)
2. Waits 1 second
3. Starts fresh process
4. Assigns new PID
5. Returns success with new PID
```

---

## Safety Features

### Confirmation on Stop
When you click **⏹️ Stop**, a JavaScript confirmation dialog appears:
```
Are you sure you want to stop the scheduler daemon?
[Cancel] [OK]
```

This prevents accidental stops.

### Duplicate Prevention
If scheduler is already running and you try to start again, you get:
```
Warning: Scheduler daemon is already running (PID: 12345)
```

### Stale PID Cleanup
If PID file exists but process is not running:
```
Warning: Scheduler daemon was not running (stale PID: 12345). Cleaned up PID file.
```

### Error Handling
All operations have try-catch blocks:
```
Error: Failed to start scheduler: [detailed error message]
```

---

## Platform Support

### Works On ✅
- macOS (development)
- Linux (production)
- Windows (with limitations)
- Docker containers
- Railway/Heroku
- Any VPS/dedicated server

### Process Detection
- **Unix/Linux/Mac:** Uses `ps -p $pid`
- **Windows:** Uses `tasklist /FI "PID eq $pid"`

### Kill Signals
- **Unix/Linux/Mac:** `kill` (SIGTERM) then `kill -9` (SIGKILL)
- **Windows:** `taskkill /PID $pid /F`

---

## Troubleshooting

### "Failed to start scheduler daemon"

**Possible Causes:**
- PHP doesn't have permission to run background processes
- `nohup` command not available
- Storage directory not writable

**Solutions:**
```bash
# Check permissions
chmod -R 775 storage
chown -R www-data:www-data storage

# Test manually
php artisan scheduler:run --daemon --interval=60
```

### Button Doesn't Work

**Check:**
1. Routes are loaded: `php artisan route:list | grep scheduler`
2. No JavaScript errors in browser console (F12)
3. CSRF token is valid (try hard refresh)

**Fix:**
```bash
php artisan route:clear
php artisan config:clear
php artisan view:clear
```

### Process Starts but Stops Immediately

**Check Logs:**
```bash
tail -f storage/logs/scheduler.log
tail -f storage/logs/laravel.log
```

**Common Issues:**
- Database connection error
- Missing dependencies
- Permission errors
- Port conflicts

### Can't Stop Process

**Manual Stop:**
```bash
# Find PID
cat storage/scheduler.pid

# Kill it
kill -9 [PID]

# Remove PID file
rm storage/scheduler.pid
```

---

## Command Line vs Web Controls

### Command Line
```bash
bash start-scheduler.sh   # Start
bash stop-scheduler.sh    # Stop
bash status-scheduler.sh  # Check status
```

**Pros:**
- Works in automation/scripts
- Can use in CI/CD
- No login required

**Cons:**
- Requires SSH access
- Need to know commands
- Terminal/command line knowledge

### Web Controls
```
Click ▶️ Start Daemon
Click ⏹️ Stop
Click 🔄 Restart
```

**Pros:**
- No command line needed
- Visual feedback
- User-friendly
- Works on mobile
- Accessible to non-technical users

**Cons:**
- Requires web access
- Need to be logged in

---

## Best Practices

### Development
1. **Start** scheduler when you begin working
2. **Stop** when done or switching projects
3. **Restart** after changing routes/console.php

### Production
1. **Use systemd** for auto-start on boot
2. **Web controls** for manual interventions
3. **Monitor** with health checks
4. **Restart** weekly for routine maintenance

### Troubleshooting
1. Check status first
2. View logs before restarting
3. Stop → check logs → fix issue → start
4. Restart is usually faster than stop/start

---

## Security

### Access Control
- Requires authentication (logged in user)
- Requires `settings.manage` permission
- Only admin/depot-admin/site-admin roles
- Protected by CSRF tokens

### Safe Operations
- Confirmation dialogs on destructive actions
- Graceful shutdown (SIGTERM before SIGKILL)
- Prevents duplicate processes
- Cleans up stale PID files

### Audit Trail
All actions logged in Laravel logs:
```
[2024-01-15 10:30:00] User #1 started scheduler daemon (PID: 12345)
[2024-01-15 11:45:00] User #1 stopped scheduler daemon (PID: 12345)
[2024-01-15 12:00:00] User #1 restarted scheduler daemon (new PID: 12346)
```

---

## Summary

### Before This Feature
❌ Had to SSH into server
❌ Run bash scripts
❌ Check status manually
❌ Restart required technical knowledge

### After This Feature
✅ One-click start/stop/restart
✅ Visual status indicator
✅ No SSH required
✅ Accessible to all admins
✅ Mobile-friendly
✅ Confirmation dialogs
✅ Real-time feedback

---

## Quick Reference

| Task | Click | Location |
|------|-------|----------|
| Start scheduler | ▶️ Start Daemon | Top right of status card |
| Stop scheduler | ⏹️ Stop | Top right of status card |
| Restart scheduler | 🔄 Restart | Top right of status card |
| Check status | - | Auto-updates every 30s |
| View logs | 📄 View Log | Under each task |
| Run task | ▶️ Run Now | Next to each task |

---

**Now you have complete control over the scheduler from your browser!** 🎉

No more SSH. No more command line. Just click and go! ✨
