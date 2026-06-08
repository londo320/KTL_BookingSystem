#!/bin/bash

# Start the Laravel Scheduler Daemon
# This script ensures the scheduler runs continuously and restarts if it crashes

APP_DIR="/Users/londo/Herd/test"
LOG_DIR="$APP_DIR/storage/logs"
SCHEDULER_LOG="$LOG_DIR/scheduler.log"
PID_FILE="$APP_DIR/storage/scheduler.pid"

# Color output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}=== Laravel Scheduler Daemon ===${NC}"

# Check if already running
if [ -f "$PID_FILE" ]; then
    OLD_PID=$(cat "$PID_FILE")
    if ps -p "$OLD_PID" > /dev/null 2>&1; then
        echo -e "${YELLOW}Scheduler is already running (PID: $OLD_PID)${NC}"
        echo "Use 'bash stop-scheduler.sh' to stop it first"
        exit 1
    else
        echo -e "${YELLOW}Removing stale PID file${NC}"
        rm "$PID_FILE"
    fi
fi

# Create log directory if it doesn't exist
mkdir -p "$LOG_DIR"

# Start the scheduler
cd "$APP_DIR" || exit 1

echo -e "${GREEN}Starting scheduler daemon...${NC}"
nohup "$(which php)" artisan scheduler:run --daemon --interval=60 >> "$SCHEDULER_LOG" 2>&1 &
SCHEDULER_PID=$!

# Save PID
echo "$SCHEDULER_PID" > "$PID_FILE"

echo -e "${GREEN}Scheduler started with PID: $SCHEDULER_PID${NC}"
echo -e "${GREEN}Logs: $SCHEDULER_LOG${NC}"
echo ""
echo "Commands:"
echo "  - View logs: tail -f $SCHEDULER_LOG"
echo "  - Stop scheduler: bash stop-scheduler.sh"
echo "  - Check status: bash status-scheduler.sh"
