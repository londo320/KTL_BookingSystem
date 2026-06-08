# Laravel Scheduler Setup Guide

This application includes a comprehensive scheduler management system that works across different environments (local development, Docker, Railway, production servers).

## What the Scheduler Does

The scheduler automatically runs these tasks:

1. **Slot Generation** (Daily at 00:15)
   - Auto-generates slots for the next 30 days
   - Uses bay-based or template-based method (configurable in settings)

2. **Auto-Release Slots** (Every 15 minutes)
   - Releases slots based on configured rules
   - Manages customer access to booking slots

3. **Bay Occupancy Sync** (Every 30 minutes)
   - Syncs tipping bay occupancy status
   - Updates bay availability based on active bookings

4. **Cleanup Incomplete Bookings** (Every 15 minutes)
   - Removes bookings without PO details after 30 minutes
   - Keeps the system clean

## Setup Instructions

### Option 1: Local Development (macOS/Linux)

Start the scheduler daemon:
```bash
chmod +x start-scheduler.sh stop-scheduler.sh status-scheduler.sh
bash start-scheduler.sh
```

Check status:
```bash
bash status-scheduler.sh
```

Stop the scheduler:
```bash
bash stop-scheduler.sh
```

View logs:
```bash
tail -f storage/logs/scheduler.log
```

### Option 2: Production Server (Ubuntu/Debian with systemd)

1. Copy the service file:
```bash
sudo cp scheduler.service /etc/systemd/system/laravel-scheduler.service
```

2. Edit the service file to match your paths:
```bash
sudo nano /etc/systemd/system/laravel-scheduler.service
```

Update these lines:
- `User=www-data` (your web server user)
- `Group=www-data` (your web server group)
- `WorkingDirectory=/var/www/html` (your app path)
- `ExecStart=/usr/bin/php /var/www/html/artisan ...` (your php and app paths)

3. Enable and start the service:
```bash
sudo systemctl daemon-reload
sudo systemctl enable laravel-scheduler
sudo systemctl start laravel-scheduler
```

4. Check status:
```bash
sudo systemctl status laravel-scheduler
```

5. View logs:
```bash
sudo journalctl -u laravel-scheduler -f
```

### Option 3: Docker/Railway

Add this to your `Procfile` or `docker-entrypoint.sh`:

**For Railway (Procfile):**
```
web: php artisan serve --host=0.0.0.0 --port=$PORT
scheduler: php artisan scheduler:run --daemon --interval=60
```

**For Docker (docker-entrypoint.sh):**
```bash
#!/bin/bash

# Start scheduler in background
php artisan scheduler:run --daemon --interval=60 &

# Start main application
php artisan serve --host=0.0.0.0 --port=8000
```

Or use the included script:
```dockerfile
# In your Dockerfile
COPY docker-scheduler.sh /usr/local/bin/scheduler
RUN chmod +x /usr/local/bin/scheduler

# Then run it as a separate service
CMD ["/usr/local/bin/scheduler"]
```

### Option 4: Traditional Cron (Fallback)

If you prefer the traditional Laravel scheduler approach with cron:

```bash
crontab -e
```

Add this line:
```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

**Note:** This requires cron to be available and running, which may not work in all containerized environments.

## Health Monitoring

Use the health check script to monitor the scheduler:

```bash
chmod +x scheduler-health-check.sh
bash scheduler-health-check.sh
```

Exit codes:
- `0`: Everything is working
- `1`: Warning (scheduler running but may have issues)
- `2`: Critical (scheduler not running)

You can add this to your monitoring system or run it via cron:
```bash
# Check every 5 minutes and alert if failed
*/5 * * * * /path/to/scheduler-health-check.sh || echo "Scheduler health check failed!" | mail -s "Alert: Scheduler Down" admin@example.com
```

## Troubleshooting

### Scheduler not running tasks

1. Check if the daemon is running:
```bash
bash status-scheduler.sh
# or
ps aux | grep "scheduler:run"
```

2. Check the logs:
```bash
tail -f storage/logs/scheduler.log
tail -f storage/logs/slots_generate.log
tail -f storage/logs/auto_release_slots.log
```

3. Test the schedule manually:
```bash
php artisan schedule:list  # View all scheduled tasks
php artisan schedule:run    # Run the scheduler once
```

### Slots not generating

1. Check slot generation settings:
```bash
php artisan tinker
>>> \App\Models\Setting::where('key', 'slot_generation_method')->first();
```

2. Manually generate slots:
```bash
# Bay-based method
php artisan slots:generate-by-bay --days=30

# Template-based method
php artisan slots:generate --days=30
```

3. Check the slot generation log:
```bash
tail -f storage/logs/slots_generate.log
```

### Permission issues

Ensure storage directory is writable:
```bash
chmod -R 775 storage
chown -R www-data:www-data storage
```

## Customization

### Change scheduler interval

Edit the `--interval` parameter (in seconds):

```bash
# Check every 30 seconds instead of 60
php artisan scheduler:run --daemon --interval=30
```

Update this in:
- `start-scheduler.sh` (line with `--interval=60`)
- `scheduler.service` (ExecStart line)
- Railway Procfile
- Docker scripts

### Add new scheduled tasks

Edit `routes/console.php`:

```php
Schedule::command('your:command')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->timezone('Europe/London')
    ->appendOutputTo(storage_path('logs/your_command.log'));
```

## Production Best Practices

1. **Always use the daemon approach** (`scheduler:run --daemon`) instead of cron in containerized environments
2. **Set up health monitoring** using the health check script
3. **Monitor logs** regularly for errors
4. **Use `withoutOverlapping()`** on all scheduled tasks to prevent concurrent runs
5. **Set appropriate timezones** in your scheduled tasks
6. **Keep logs** but rotate them regularly to save disk space

## Multiple Environment Support

This scheduler system is designed to work across:
- ✅ Local development (macOS, Linux, Windows with WSL)
- ✅ Docker containers
- ✅ Railway.app
- ✅ Heroku
- ✅ Traditional VPS/dedicated servers
- ✅ Kubernetes (as a separate deployment)
- ✅ Shared hosting (using cron fallback)

Choose the appropriate setup method for your environment from the options above.
