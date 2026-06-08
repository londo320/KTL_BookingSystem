#!/bin/bash

# Health check script for monitoring the scheduler
# Can be used with monitoring tools or cron

APP_DIR="/Users/londo/Herd/test"
PID_FILE="$APP_DIR/storage/scheduler.pid"
MAX_AGE=300  # Maximum age of last run in seconds (5 minutes)

# Check if scheduler is running
if [ ! -f "$PID_FILE" ]; then
    echo "CRITICAL: Scheduler PID file not found"
    exit 2
fi

PID=$(cat "$PID_FILE")

if ! ps -p "$PID" > /dev/null 2>&1; then
    echo "CRITICAL: Scheduler process not running (PID: $PID)"
    exit 2
fi

# Check if scheduler has run recently by checking log file age
LOG_FILE="$APP_DIR/storage/logs/scheduler.log"

if [ ! -f "$LOG_FILE" ]; then
    echo "WARNING: Scheduler log file not found"
    exit 1
fi

# Get last modification time
if [[ "$OSTYPE" == "darwin"* ]]; then
    # macOS
    LOG_AGE=$(($(date +%s) - $(stat -f %m "$LOG_FILE")))
else
    # Linux
    LOG_AGE=$(($(date +%s) - $(stat -c %Y "$LOG_FILE")))
fi

if [ "$LOG_AGE" -gt "$MAX_AGE" ]; then
    echo "WARNING: Scheduler log hasn't been updated in $LOG_AGE seconds"
    exit 1
fi

echo "OK: Scheduler is running and active (PID: $PID, last run: ${LOG_AGE}s ago)"
exit 0
