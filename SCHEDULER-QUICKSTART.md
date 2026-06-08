# Quick Start - Scheduler Setup

## Fixed Issues

✅ **Booking API Error Fixed**: Resolved `Undefined variable $allowedBayIds` error in SlotAvailabilityController.php:190
✅ **Reliable Scheduler Created**: New daemon-based scheduler that works across all environments

## Start the Scheduler (Local Development)

```bash
# Start
bash start-scheduler.sh

# Check status
bash status-scheduler.sh

# View logs
tail -f storage/logs/scheduler.log

# Stop
bash stop-scheduler.sh
```

## For Railway/Docker Deployment

Add to your `railway.json` or `Procfile`:

```json
{
  "build": {
    "builder": "NIXPACKS"
  },
  "deploy": {
    "startCommand": "php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache",
    "restartPolicyType": "ON_FAILURE",
    "restartPolicyMaxRetries": 10
  }
}
```

Add separate scheduler service:
```
web: php artisan serve --host=0.0.0.0 --port=$PORT
scheduler: php artisan scheduler:run --daemon --interval=60
```

## Current Scheduled Tasks

| Task | Frequency | Description |
|------|-----------|-------------|
| Slot Generation | Daily at 00:15 | Generates slots for next 30 days |
| Auto-Release Slots | Every 15 min | Releases slots per rules |
| Bay Occupancy Sync | Every 30 min | Updates bay status |
| Cleanup Bookings | Every 15 min | Removes incomplete bookings |

## Test Commands

```bash
# Test scheduler manually
php artisan schedule:run

# View scheduled tasks
php artisan schedule:list

# Generate slots manually
php artisan slots:generate-by-bay --days=30
# or
php artisan slots:generate --days=30

# Test specific commands
php artisan app:auto-release-slots
php artisan bays:sync-occupancy
php artisan bookings:cleanup-incomplete --minutes=30
```

## Troubleshooting

**Problem**: Scheduler not firing
- Check if daemon is running: `bash status-scheduler.sh`
- View logs: `tail -f storage/logs/scheduler.log`
- Restart: `bash stop-scheduler.sh && bash start-scheduler.sh`

**Problem**: No slots being generated
- Check method setting: `php artisan tinker --execute="App\Models\Setting::getSlotGenerationMethod()"`
- Generate manually: `php artisan slots:generate-by-bay --days=30`
- Check logs: `tail -f storage/logs/slots_generate.log`

**Problem**: Bookings fail
- Issue was fixed in SlotAvailabilityController.php
- Test by logging in as customer and viewing availability page

## Next Steps

1. Start the scheduler: `bash start-scheduler.sh`
2. Test booking creation through the web interface
3. Monitor logs to ensure tasks are running
4. Set up health monitoring for production

For full documentation, see `SCHEDULER-SETUP.md`
