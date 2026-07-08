#!/bin/bash

# Check the status of the Laravel Scheduler Daemon

APP_DIR="/Users/londo/Herd/test"
PID_FILE="$APP_DIR/storage/scheduler.pid"
LOG_DIR="$APP_DIR/storage/logs"

# Color output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}=== Laravel Scheduler Status ===${NC}"
echo ""

# Check if PID file exists
if [ ! -f "$PID_FILE" ]; then
    echo -e "${RED}Status: NOT RUNNING${NC}"
    echo "No PID file found at $PID_FILE"
    exit 1
fi

PID=$(cat "$PID_FILE")

# Check if process is running
if ps -p "$PID" > /dev/null 2>&1; then
    echo -e "${GREEN}Status: RUNNING${NC}"
    echo "PID: $PID"
    echo ""

    # Show process details
    echo "Process details:"
    ps -fp "$PID"
    echo ""

    # Show recent log entries
    echo "Recent log entries (last 10 lines):"
    tail -n 10 "$LOG_DIR/scheduler.log" 2>/dev/null || echo "No logs found"
else
    echo -e "${RED}Status: NOT RUNNING${NC}"
    echo "PID file exists but process is not running (stale PID: $PID)"
    echo "Run './quick-deploy.sh' to redeploy — it self-heals the scheduler cron"
fi
