#!/bin/bash

# Stop the Laravel Scheduler Daemon

APP_DIR="/Users/londo/Herd/test"
PID_FILE="$APP_DIR/storage/scheduler.pid"

# Color output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}=== Stop Laravel Scheduler ===${NC}"

if [ ! -f "$PID_FILE" ]; then
    echo -e "${YELLOW}No PID file found. Scheduler may not be running.${NC}"
    exit 0
fi

PID=$(cat "$PID_FILE")

if ps -p "$PID" > /dev/null 2>&1; then
    echo -e "${YELLOW}Stopping scheduler (PID: $PID)...${NC}"
    kill "$PID"
    sleep 2

    # Check if still running
    if ps -p "$PID" > /dev/null 2>&1; then
        echo -e "${RED}Scheduler still running, forcing kill...${NC}"
        kill -9 "$PID"
    fi

    echo -e "${GREEN}Scheduler stopped successfully${NC}"
else
    echo -e "${YELLOW}Scheduler is not running (PID: $PID)${NC}"
fi

# Remove PID file
rm "$PID_FILE"
echo -e "${GREEN}PID file removed${NC}"
